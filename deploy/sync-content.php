<?php
/**
 * Explicit, CLI-only content import. Ordinary code deployments do not run it.
 * php sync-content.php --wordpress=/absolute/wordpress --import-content
 * Back up the target database before importing. Unlisted client content is preserved.
 */
if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit;
}
$args = getopt( '', array( 'wordpress:', 'import-content' ) );
if ( ! array_key_exists( 'import-content', $args ) ) {
	echo "Content import disabled; no WordPress settings changed.\n";
	return;
}
$sync_dir = defined( 'G5_SYNC_DIR' ) ? G5_SYNC_DIR : __DIR__;
$data = json_decode( (string) file_get_contents( $sync_dir . '/content/snapshot.json' ), true );
if ( ! is_array( $data ) || ( $data['schema_version'] ?? 0 ) !== 2 || empty( $data['posts'] ) ) {
	fwrite( STDERR, "Invalid snapshot: export schema version 2 is required.\n" );
	exit( 1 );
}
$wp_root = $args['wordpress'] ?? '';
if ( ! defined( 'ABSPATH' ) ) {
	if ( ! $wp_root || ! is_file( $wp_root . '/wp-load.php' ) ) {
		fwrite( STDERR, "Specify --wordpress=/absolute/wordpress.\n" );
		exit( 1 );
	}
	define( 'PLL_SETTINGS', true );
	define( 'PLL_ADMIN', true );
	$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
	require rtrim( $wp_root, '/' ) . '/wp-load.php';
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/sync-helpers.php';
try {
	if ( ! is_plugin_active( '5gtech-core/5gtech-core.php' ) || ! is_plugin_active( 'polylang/polylang.php' ) || get_stylesheet() !== '5gtech' ) {
		throw new RuntimeException( 'Activate 5gtech-core, Polylang and the 5gtech theme before import.' );
	}
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'PLL' ) || ! PLL() ) {
		throw new RuntimeException( 'Polylang API is unavailable.' );
	}
	$uploads = wp_get_upload_dir();
	$source_ids = array();
	$keys = array();
	$allowed_types = array( 'page', 'post', 'g5_team', 'g5_service', 'g5_project', 'g5_job', 'g5_faq', 'g5_partner', 'g5_module' );
	foreach ( $data['posts'] as $item ) {
		$key = $item['type'] . '|' . $item['slug'];
		if ( empty( $item['source_id'] ) || empty( $item['slug'] ) || ! in_array( $item['type'], $allowed_types, true ) || ! post_type_exists( $item['type'] ) || isset( $keys[$key] ) || isset( $source_ids[$item['source_id']] ) ) {
			throw new RuntimeException( 'Invalid or duplicate post: ' . $key );
		}
		$keys[$key] = true;
		$source_ids[(int) $item['source_id']] = (int) $item['source_id'];
	}
	foreach ( $data['attachments'] as $id => $att ) {
		if ( (int) $id <= 0 || isset( $source_ids[(int) $id] ) ) throw new RuntimeException( 'Invalid or duplicate attachment ID: ' . $id );
		$rel = $att['file'];
		if ( str_starts_with( $rel, '/' ) || str_contains( $rel, '..' ) || ! is_file( $uploads['basedir'] . '/' . $rel ) ) {
			throw new RuntimeException( 'Missing or unsafe upload: ' . $rel );
		}
		$source_ids[(int) $id] = (int) $id;
	}
	// Validate all references before any content is written.
	foreach ( $data['posts'] as $item ) {
		g5sync_content( $item['content'], $source_ids );
		g5sync_meta( $item['meta'], $source_ids );
		if ( ! empty( $item['parent'] ) && ! isset( $keys[$item['type'] . '|' . $item['parent']] ) ) {
			throw new RuntimeException( 'Missing parent for ' . $item['slug'] );
		}
		foreach ( (array) ( $item['translations'] ?? array() ) as $slug ) {
			if ( ! isset( $keys[$item['type'] . '|' . $slug] ) ) throw new RuntimeException( 'Missing translation: ' . $slug );
		}
		if ( ! empty( $item['thumbnail'] ) && ! in_array( $item['thumbnail'], array_column( $data['attachments'], 'file' ), true ) ) {
			throw new RuntimeException( 'Thumbnail missing from manifest: ' . $item['slug'] );
		}
	}
	g5sync_options( $data['options'], $source_ids );
	require_once __DIR__ . '/polylang-shared.php';
	g5pll_ensure_languages();
	g5pll_apply_options();
	kses_remove_filters();
	$id_map = array();
	$thumbnail_map = array();
	foreach ( $data['attachments'] as $old_id => $att ) {
		$url = trailingslashit( $uploads['baseurl'] ) . $att['file'];
		$id = attachment_url_to_postid( $url );
		if ( ! $id ) {
			$path = trailingslashit( $uploads['basedir'] ) . $att['file'];
			$id = wp_insert_attachment( array( 'post_title' => $att['title'], 'post_mime_type' => $att['mime'], 'post_status' => 'inherit' ), $path, 0, true );
			g5sync_check( $id, 'Attachment: ' . $att['file'] );
			update_post_meta( $id, '_wp_attached_file', $att['file'] );
			$metadata = wp_generate_attachment_metadata( $id, $path );
			g5sync_check( $metadata, 'Attachment metadata: ' . $att['file'], false );
			wp_update_attachment_metadata( $id, $metadata );
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $att['alt'] ?? '' );
		$id_map[(int) $old_id] = (int) $id;
		$thumbnail_map[$att['file']] = (int) $id;
	}
	$by_key = array();
	// First pass establishes destination IDs, including forward references.
	foreach ( $data['posts'] as $item ) {
		$key = $item['type'] . '|' . $item['slug'];
		$found = get_posts( array( 'post_type' => $item['type'], 'name' => $item['slug'], 'post_status' => array( 'publish','draft','private','pending','future' ), 'posts_per_page' => 2, 'suppress_filters' => true, 'lang' => '' ) );
		if ( count( $found ) > 1 ) throw new RuntimeException( 'Ambiguous destination: ' . $key );
		$id = $found ? $found[0]->ID : wp_insert_post( array( 'post_type' => $item['type'], 'post_name' => $item['slug'], 'post_title' => $item['title'], 'post_status' => 'draft' ), true );
		g5sync_check( $id, 'Create: ' . $key );
		if ( get_post_field( 'post_name', $id ) !== $item['slug'] ) throw new RuntimeException( 'Slug collision: ' . $key );
		$by_key[$key] = (int) $id;
		$id_map[(int) $item['source_id']] = (int) $id;
	}
	$source_home = untrailingslashit( $data['source_home'] );
	$target_home = untrailingslashit( home_url() );
	foreach ( $data['posts'] as $item ) {
		$id = $by_key[$item['type'] . '|' . $item['slug']];
		$content = g5sync_content( $item['content'], $id_map );
		$payload = array( 'ID' => $id, 'post_title' => $item['title'], 'post_status' => $item['status'],
			'post_content' => g5sync_urls( $content, $source_home, $target_home ), 'post_excerpt' => g5sync_urls( $item['excerpt'], $source_home, $target_home ),
			'menu_order' => $item['menu_order'], 'post_parent' => empty( $item['parent'] ) ? 0 : $by_key[$item['type'] . '|' . $item['parent']],
			'post_date' => $item['date'], 'post_date_gmt' => $item['date_gmt'] );
		g5sync_check( wp_update_post( wp_slash( $payload ), true ), 'Update: ' . $item['slug'] );
		foreach ( g5sync_meta( $item['meta'], $id_map ) as $key => $values ) {
			delete_post_meta( $id, $key );
			foreach ( $values as $value ) {
				$value = g5sync_urls( $value, $source_home, $target_home );
				g5sync_check( add_post_meta( $id, $key, is_string( $value ) ? wp_slash( $value ) : $value ), 'Meta: ' . $key );
			}
		}
		if ( ! empty( $item['thumbnail'] ) ) {
			$thumb = $thumbnail_map[$item['thumbnail']];
			set_post_thumbnail( $id, $thumb );
			if ( (int) get_post_thumbnail_id( $id ) !== $thumb ) throw new RuntimeException( 'Thumbnail could not be assigned.' );
		} else {
			delete_post_thumbnail( $id );
		}
		foreach ( (array) $item['terms'] as $taxonomy => $terms ) {
			if ( ! taxonomy_exists( $taxonomy ) || ( ! in_array( $taxonomy, array( 'category', 'post_tag' ), true ) && ! str_starts_with( $taxonomy, 'g5_' ) ) ) throw new RuntimeException( 'Unknown taxonomy: ' . $taxonomy );
			$term_ids = array();
			foreach ( $terms as $term ) {
				$existing = term_exists( $term['slug'], $taxonomy );
				if ( ! $existing ) $existing = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
				g5sync_check( $existing, 'Term: ' . $term['slug'] );
				$term_ids[] = (int) ( is_array( $existing ) ? $existing['term_id'] : $existing );
			}
			g5sync_check( wp_set_object_terms( $id, $term_ids, $taxonomy, false ), 'Terms: ' . $taxonomy, false );
		}
		if ( ! empty( $item['lang'] ) ) {
			pll_set_post_language( $id, $item['lang'] );
			if ( pll_get_post_language( $id ) !== $item['lang'] ) throw new RuntimeException( 'Language assignment failed.' );
		}
	}
	foreach ( $data['posts'] as $item ) {
		if ( $item['lang'] !== 'lt' || empty( $item['translations'] ) ) continue;
		$group = array( 'lt' => $by_key[$item['type'] . '|' . $item['slug']] );
		foreach ( $item['translations'] as $lang => $slug ) $group[$lang] = $by_key[$item['type'] . '|' . $slug];
		pll_save_post_translations( $group );
		foreach ( $group as $lang => $id ) if ( pll_get_post( $group['lt'], $lang ) !== $id ) throw new RuntimeException( 'Translation relationship failed.' );
	}
	foreach ( g5sync_options( $data['options'], $id_map ) as $name => $value ) {
		if ( ! str_starts_with( $name, 'g5tech_' ) && ! in_array( $name, array( 'blogname','blogdescription','timezone_string','date_format','time_format','start_of_week','posts_per_page','permalink_structure' ), true ) ) throw new RuntimeException( 'Disallowed option: ' . $name );
		$value = g5sync_urls( $value, $source_home, $target_home );
		update_option( $name, $value );
		if ( get_option( $name ) != $value ) throw new RuntimeException( 'Option verification failed: ' . $name );
	}
	if ( isset( $by_key['page|pagrindinis'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $by_key['page|pagrindinis'] );
	}
	delete_transient( 'pll_languages_list' );
	delete_option( 'rewrite_rules' );
	echo 'Imported and verified ' . count( $by_key ) . ' posts. Unlisted client posts/options preserved.' . "\n";
} catch ( Throwable $error ) {
	fwrite( STDERR, 'IMPORT FAILED: ' . $error->getMessage() . "\nRestore the pre-import backup before retrying a partially completed import.\n" );
	exit( 1 );
}
