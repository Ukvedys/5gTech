<?php
/**
 * Turinio momentinio įrašo eksportas į deploy/content/snapshot.json.
 *
 * Paleidimas iš projekto šaknies:  php tools/export-content-snapshot.php
 * Rezultatas: deploy/content/snapshot.json + nuotraukų kopijos deploy/uploads/.
 * Įrašą įkėlus į git, diegimas (deploy.yml) jį pritaiko serveryje.
 */

$root = dirname( __DIR__ );

if ( ! defined( 'G5_EXPORT_ROOT' ) ) {
	define( 'G5_EXPORT_ROOT', $root );
}

$_SERVER['HTTP_HOST']   = $_SERVER['HTTP_HOST'] ?? '5gtech.test';
$_SERVER['REQUEST_URI'] = '/';

require $root . '/wordpress/wp-load.php';

$post_types = array( 'page', 'post', 'g5_team', 'g5_service', 'g5_project', 'g5_job', 'g5_faq', 'g5_partner', 'g5_module' );
$statuses   = array( 'publish', 'draft', 'private' );

$snapshot = array(
	'generated'   => gmdate( 'c' ),
	'source_home' => home_url(),
	'options'     => array(),
	'posts'       => array(),
	'attachments' => array(),
);

// 1. Svetainės nustatymai (be rolių versijos — ji priklauso nuo aplinkos kodo).
global $wpdb;
foreach ( $wpdb->get_results( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'g5tech\\_%'" ) as $row ) {
	if ( 'g5tech_roles_version' === $row->option_name ) {
		continue;
	}
	$snapshot['options'][ $row->option_name ] = get_option( $row->option_name );
}

foreach ( array( 'blogname', 'blogdescription', 'timezone_string', 'date_format', 'time_format', 'start_of_week', 'posts_per_page', 'permalink_structure' ) as $option_name ) {
	$snapshot['options'][ $option_name ] = get_option( $option_name );
}

$uploads     = wp_get_upload_dir();
$att_paths   = array();
$register_attachment = static function ( $attachment_id ) use ( &$snapshot, &$att_paths, $uploads ) {
	$attachment_id = absint( $attachment_id );

	if ( ! $attachment_id || isset( $snapshot['attachments'][ $attachment_id ] ) ) {
		return;
	}

	$file = get_post_meta( $attachment_id, '_wp_attached_file', true );

	if ( ! $file ) {
		return;
	}

	$snapshot['attachments'][ $attachment_id ] = array(
		'file'  => $file,
		'title' => get_the_title( $attachment_id ),
		'alt'   => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		'mime'  => get_post_mime_type( $attachment_id ),
	);
	$att_paths[] = $file;
};

// Nuotraukos, kurios saugomos ne blokuose, o svetainės nustatymuose.
$about_media = (array) ( $snapshot['options']['g5tech_about_content'] ?? array() );
foreach ( array( 'story_image_1_id', 'story_image_2_id' ) as $key ) {
	$register_attachment( $about_media[ $key ] ?? 0 );
}

$training_media = (array) ( $snapshot['options']['g5tech_training_page_content'] ?? array() );
$register_attachment( $training_media['image_id'] ?? 0 );
foreach ( (array) ( $training_media['equipment_ids'] ?? array() ) as $attachment_id ) {
	$register_attachment( $attachment_id );
}

// 2. Įrašai su meta.
foreach ( $post_types as $post_type ) {
	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => $statuses,
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'lang'           => '', // visos kalbos, ne tik dabartinė
		)
	);

	foreach ( $posts as $p ) {
		$meta_out = array();
		$terms_out = array();

		foreach ( get_post_meta( $p->ID ) as $key => $values ) {
			// Sinchronizuojami tik svetainės turinio laukai, ne WP vidiniai.
			if ( ! preg_match( '/^(g5_|_g5tech_|_wp_page_template$)/', $key ) ) {
				continue;
			}

			$meta_out[ $key ] = array_map( 'maybe_unserialize', $values );
		}

		$thumbnail_id = get_post_thumbnail_id( $p->ID );

		if ( $thumbnail_id ) {
			$register_attachment( $thumbnail_id );
		}

		// Nuotraukų ID blokų atributuose — registruojami permapavimui.
		if ( preg_match_all( '/"image\d*Id":(\d+)/', $p->post_content, $matches ) ) {
			foreach ( $matches[1] as $image_id ) {
				$register_attachment( $image_id );
			}
		}
		if ( preg_match_all( '/"imageId":(\d+)/', $p->post_content, $matches ) ) {
			foreach ( $matches[1] as $image_id ) {
				$register_attachment( $image_id );
			}
		}

		foreach ( get_object_taxonomies( $p->post_type ) as $taxonomy ) {
			if ( 0 !== strpos( $taxonomy, 'g5_' ) ) {
				continue;
			}

			$terms = wp_get_object_terms( $p->ID, $taxonomy );

			if ( is_wp_error( $terms ) || ! $terms ) {
				continue;
			}

			$terms_out[ $taxonomy ] = array_map(
				static fn( $term ) => array(
					'slug' => $term->slug,
					'name' => $term->name,
				),
				$terms
			);
		}

		$entry_lang         = function_exists( 'pll_get_post_language' ) ? ( pll_get_post_language( $p->ID ) ?: '' ) : '';
		$entry_translations = array();

		if ( 'lt' === $entry_lang && function_exists( 'pll_get_post_translations' ) ) {
			foreach ( pll_get_post_translations( $p->ID ) as $tr_lang => $tr_id ) {
				if ( 'lt' !== $tr_lang && $tr_id ) {
					$entry_translations[ $tr_lang ] = get_post_field( 'post_name', $tr_id );
				}
			}
		}

		$snapshot['posts'][] = array(
			'lang'         => $entry_lang,
			'translations' => $entry_translations,
			'type'       => $p->post_type,
			'slug'       => $p->post_name,
			'title'      => $p->post_title,
			'status'     => $p->post_status,
			'date'       => $p->post_date,
			'date_gmt'   => $p->post_date_gmt,
			'content'    => $p->post_content,
			'excerpt'    => $p->post_excerpt,
			'menu_order' => (int) $p->menu_order,
			'parent'     => $p->post_parent ? get_post_field( 'post_name', $p->post_parent ) : '',
			'thumbnail'  => $thumbnail_id ? (string) get_post_meta( $thumbnail_id, '_wp_attached_file', true ) : '',
			'meta'       => $meta_out,
			'terms'      => $terms_out,
		);
	}
}

// 3. Nuotraukų failų kopijos deploy/uploads/.
$copied  = 0;
$missing = array();

foreach ( array_unique( $att_paths ) as $rel ) {
	$src = trailingslashit( $uploads['basedir'] ) . $rel;
	$dst = G5_EXPORT_ROOT . '/deploy/uploads/' . $rel;

	if ( ! file_exists( $src ) ) {
		$missing[] = $rel;
		continue;
	}

	if ( ! is_dir( dirname( $dst ) ) ) {
		mkdir( dirname( $dst ), 0755, true );
	}

	copy( $src, $dst );
	$copied++;
}

$out = G5_EXPORT_ROOT . '/deploy/content/snapshot.json';

if ( ! is_dir( dirname( $out ) ) ) {
	mkdir( dirname( $out ), 0755, true );
}

file_put_contents( $out, wp_json_encode( $snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );

echo 'Irasu: ' . count( $snapshot['posts'] ) . ', nustatymu: ' . count( $snapshot['options'] ) . ', nuotrauku: ' . $copied . "\n";

if ( $missing ) {
	echo 'DEMESIO, nerasta failu: ' . implode( ', ', $missing ) . "\n";
}
