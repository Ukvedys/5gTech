<?php
/**
 * Turinio sinchronizavimas iš repozitorijos momentinio įrašo.
 *
 * Paleidžia diegimas (deploy.yml) per: wp eval-file wp-content/g5-deploy/sync-content.php
 * Įrašas idempotentiškas: kiek kartų paleisi — rezultatas tas pats.
 *
 * SVARBU: kol veikia šis sinchronizavimas, turinio TIESA yra repozitorija
 * (lokali svetainė). Serveryje darytus turinio pakeitimus kitas diegimas
 * perrašys. Paleidus svetainę gyvai — ištrinti deploy/content/SYNC-ON.
 */

// Skriptas savarankiškas: WP užkraunamas su Polylang administravimo
// konstantomis (kitaip CLI kontekste Polylang API nepasiekiama).
// Diegime kviečiamas: wp eval-file ... --skip-wordpress
if ( ! defined( 'ABSPATH' ) ) {
	define( 'PLL_SETTINGS', true );
	define( 'PLL_ADMIN', true );
	$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
	require dirname( __DIR__, 2 ) . '/wp-load.php';
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$g5_admins = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
	if ( $g5_admins ) wp_set_current_user( $g5_admins[0]->ID );
}

// Polylang: aktyvavimas ir kalbos (jei papildinys įdiegtas faile).
if ( file_exists( WP_PLUGIN_DIR . '/polylang/polylang.php' ) ) {
	if ( ! is_plugin_active( 'polylang/polylang.php' ) ) {
		activate_plugin( 'polylang/polylang.php' );

		if ( ! function_exists( 'PLL' ) ) {
			// Hostinger draudžia PHP procesų paleidimo funkcijas. Specialus kodas
			// liepia deploy.yml scenarijų paleisti dar kartą naujame PHP procese.
			echo "Polylang aktyvuotas, reikalingas antras sinchronizavimo paleidimas.\n";
			exit( 75 );
		}
	}

	if ( function_exists( 'PLL' ) && PLL() ) {
		require __DIR__ . '/polylang-shared.php';
		g5pll_ensure_languages();
		g5pll_apply_options();
	}
}

$g5_sync_dir  = defined( 'G5_SYNC_DIR' ) ? G5_SYNC_DIR : __DIR__;
$g5_flag      = $g5_sync_dir . '/content/SYNC-ON';
$g5_snapshot  = $g5_sync_dir . '/content/snapshot.json';

if ( ! file_exists( $g5_flag ) ) {
	echo "Sinchronizavimas isjungtas (nera content/SYNC-ON) - praleista.\n";
	return;
}

if ( ! file_exists( $g5_snapshot ) ) {
	echo "KLAIDA: nerastas content/snapshot.json\n";
	exit( 1 );
}

$data = json_decode( (string) file_get_contents( $g5_snapshot ), true );

if ( ! is_array( $data ) || empty( $data['posts'] ) ) {
	echo "KLAIDA: snapshot.json tuscias arba nenuskaitomas\n";
	exit( 1 );
}

wp_set_current_user( 0 );
kses_remove_filters(); // Turinys jau patikrintas lokaliai; kses gadintų blokų atributus.

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$uploads = wp_get_upload_dir();

// 1. Nuotraukos: kiekvienam failui užtikrinamas attachment įrašas; senas ID → naujas ID.
$id_map = array();

foreach ( (array) ( $data['attachments'] ?? array() ) as $old_id => $att ) {
	$rel  = ltrim( (string) $att['file'], '/' );
	$path = trailingslashit( $uploads['basedir'] ) . $rel;
	$url  = trailingslashit( $uploads['baseurl'] ) . $rel;

	$existing = attachment_url_to_postid( $url );

	if ( ! $existing && file_exists( $path ) ) {
		$existing = wp_insert_attachment(
			array(
				'post_title'     => (string) $att['title'],
				'post_mime_type' => (string) $att['mime'],
				'post_status'    => 'inherit',
			),
			$path
		);

		if ( $existing && ! is_wp_error( $existing ) ) {
			update_post_meta( $existing, '_wp_attached_file', $rel );
			$meta = wp_generate_attachment_metadata( $existing, $path );

			if ( $meta ) {
				wp_update_attachment_metadata( $existing, $meta );
			}
		}
	}

	if ( $existing && ! is_wp_error( $existing ) ) {
		if ( ! empty( $att['alt'] ) ) {
			update_post_meta( $existing, '_wp_attachment_image_alt', (string) $att['alt'] );
		}

		$id_map[ (int) $old_id ] = (int) $existing;
	} else {
		echo 'DEMESIO: nuotrauka nerasta serveryje: ' . $rel . "\n";
	}
}

// 2. Turinys: adresai ir nuotraukų ID pritaikomi šiai aplinkai.
$source_home = untrailingslashit( (string) ( $data['source_home'] ?? '' ) );
$target_home = untrailingslashit( home_url() );

$g5_adapt_content = static function ( $content ) use ( $source_home, $target_home, $id_map ) {
	if ( $source_home && $source_home !== $target_home ) {
		$content = str_replace( $source_home, $target_home, $content );
	}

	return preg_replace_callback(
		'/"(image\d*Id)":(\d+)/',
		static function ( $m ) use ( $id_map ) {
			$old = (int) $m[2];

			return '"' . $m[1] . '":' . ( $id_map[ $old ] ?? 0 );
		},
		$content
	);
};

// 3. Įrašai pagal (tipas, slug) — atnaujinami arba sukuriami.
$find_post = static function ( $type, $slug ) {
	$found = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page' => 1,
		)
	);

	return $found ? $found[0] : null;
};

$created = 0;
$updated = 0;
$deleted = 0;
$expected_posts = array();

// Tėviniai priskiriami antru praėjimu, kai visi įrašai jau egzistuoja.
$parents = array();

foreach ( $data['posts'] as $item ) {
	$slug = (string) $item['slug'];
	$type = (string) $item['type'];

	if ( '' === $slug || ! post_type_exists( $type ) ) {
		continue;
	}

	$expected_posts[ $type ][ $slug ] = true;

	$payload = array(
		'post_type'    => $type,
		'post_name'    => $slug,
		'post_title'   => (string) $item['title'],
		'post_status'  => (string) $item['status'],
		'post_date'    => (string) ( $item['date'] ?? '' ),
		'post_date_gmt' => (string) ( $item['date_gmt'] ?? '' ),
		'post_content' => $g5_adapt_content( (string) $item['content'] ),
		'post_excerpt' => (string) $item['excerpt'],
		'menu_order'   => (int) $item['menu_order'],
	);

	$existing = $find_post( $type, $slug );

	if ( $existing ) {
		$payload['ID'] = $existing->ID;
		$post_id       = wp_update_post( wp_slash( $payload ), true );
		$updated++;
	} else {
		$post_id = wp_insert_post( wp_slash( $payload ), true );
		$created++;
	}

	if ( is_wp_error( $post_id ) ) {
		echo 'KLAIDA (' . $type . '/' . $slug . '): ' . $post_id->get_error_message() . "\n";
		continue;
	}

	foreach ( (array) $item['meta'] as $key => $values ) {
		delete_post_meta( $post_id, $key );

		foreach ( (array) $values as $value ) {
			add_post_meta( $post_id, $key, is_string( $value ) ? wp_slash( $value ) : $value );
		}
	}

	foreach ( get_object_taxonomies( $type ) as $taxonomy ) {
		$is_content_taxonomy = in_array( $taxonomy, array( 'category', 'post_tag' ), true )
			|| 0 === strpos( $taxonomy, 'g5_' );

		if ( ! $is_content_taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$term_slugs = array();

		foreach ( (array) ( $item['terms'][ $taxonomy ] ?? array() ) as $term_data ) {
			$term_slug = sanitize_title( (string) ( $term_data['slug'] ?? '' ) );

			if ( '' === $term_slug ) {
				continue;
			}

			if ( ! term_exists( $term_slug, $taxonomy ) ) {
				wp_insert_term(
					(string) ( $term_data['name'] ?? $term_slug ),
					$taxonomy,
					array( 'slug' => $term_slug )
				);
			}

			$term_slugs[] = $term_slug;
		}

		wp_set_object_terms( $post_id, $term_slugs, $taxonomy, false );
	}

	if ( ! empty( $item['thumbnail'] ) ) {
		$thumb_id = attachment_url_to_postid( trailingslashit( $uploads['baseurl'] ) . ltrim( (string) $item['thumbnail'], '/' ) );

		if ( $thumb_id ) {
			set_post_thumbnail( $post_id, $thumb_id );
		}
	} else {
		delete_post_thumbnail( $post_id );
	}

	if ( ! empty( $item['parent'] ) ) {
		$parents[ $post_id ] = array( $type, (string) $item['parent'] );
	}
}

foreach ( $parents as $post_id => $ref ) {
	$parent = $find_post( $ref[0], $ref[1] );

	if ( $parent ) {
		wp_update_post( array( 'ID' => $post_id, 'post_parent' => $parent->ID ) );
	}
}

// 3b. Kalbos ir vertimų ryšiai (Polylang).
if ( function_exists( 'pll_set_post_language' ) && function_exists( 'PLL' ) && PLL() ) {
	$by_key = array();

	foreach ( $data['posts'] as $item ) {
		$p = $find_post( (string) $item['type'], (string) $item['slug'] );
		if ( $p ) $by_key[ $item['type'] . '|' . $item['slug'] ] = $p->ID;
	}

	foreach ( $data['posts'] as $item ) {
		$id = $by_key[ $item['type'] . '|' . $item['slug'] ] ?? 0;
		if ( ! $id || empty( $item['lang'] ) ) continue;
		pll_set_post_language( $id, (string) $item['lang'] );
	}

	foreach ( $data['posts'] as $item ) {
		if ( ( $item['lang'] ?? '' ) !== 'lt' || empty( $item['translations'] ) ) continue;
		$id = $by_key[ $item['type'] . '|' . $item['slug'] ] ?? 0;
		if ( ! $id ) continue;
		$group = array( 'lt' => $id );

		foreach ( (array) $item['translations'] as $tr_lang => $tr_slug ) {
			$tr_id = $by_key[ $item['type'] . '|' . $tr_slug ] ?? 0;
			if ( $tr_id ) $group[ $tr_lang ] = $tr_id;
		}

		pll_save_post_translations( $group );
	}

	echo "Kalbos priskirtos\n";
}

// 3c. Vietinė kopija yra turinio šaltinis: pašalinami serveryje likę
// bandomieji ar pasenę valdomų tipų įrašai, kurių momentiniame įraše nėra.
foreach ( array_keys( $expected_posts ) as $type ) {
	$server_posts = get_posts(
		array(
			'post_type'        => $type,
			'post_status'      => array( 'publish', 'draft', 'private', 'pending', 'future' ),
			'posts_per_page'   => -1,
			'suppress_filters' => true,
			'lang'             => '',
		)
	);

	foreach ( $server_posts as $server_post ) {
		if ( isset( $expected_posts[ $type ][ $server_post->post_name ] ) ) {
			continue;
		}

		if ( wp_delete_post( $server_post->ID, true ) ) {
			$deleted++;
		}
	}
}

// 4. Nustatymai.
foreach ( (array) ( $data['options'] ?? array() ) as $name => $value ) {
	if ( 'g5tech_about_content' === $name && is_array( $value ) ) {
		foreach ( array( 'story_image_1_id', 'story_image_2_id' ) as $key ) {
			$old_id = (int) ( $value[ $key ] ?? 0 );
			$value[ $key ] = $old_id ? ( $id_map[ $old_id ] ?? 0 ) : 0;
		}
	}

	if ( 'g5tech_training_page_content' === $name && is_array( $value ) ) {
		$old_id = (int) ( $value['image_id'] ?? 0 );
		$value['image_id'] = $old_id ? ( $id_map[ $old_id ] ?? 0 ) : 0;
		$value['equipment_ids'] = array_values(
			array_filter(
				array_map(
					static fn( $attachment_id ) => $id_map[ (int) $attachment_id ] ?? 0,
					(array) ( $value['equipment_ids'] ?? array() )
				)
			)
		);
	}

	update_option( $name, $value );
}

// Pašalinami tik seni 5G TECH nustatymai. WordPress, vartotojų ir kitų
// papildinių nustatymai neliečiami.
global $wpdb;
$expected_options = array_fill_keys( array_keys( (array) ( $data['options'] ?? array() ) ), true );
$server_options = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( 'g5tech_' ) . '%'
	)
);

foreach ( $server_options as $option_name ) {
	if ( 'g5tech_roles_version' !== $option_name && ! isset( $expected_options[ $option_name ] ) ) {
		delete_option( $option_name );
	}
}

// 5. Pradinis puslapis pagal slug (ID skiriasi tarp aplinkų).
$front = $find_post( 'page', 'pagrindinis' );

if ( $front ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front->ID );
}

// Kitas viešas WordPress užklausimas sugeneruos taisykles jau su aktyviu
// Polylang. Taip nereikia Hostinger išjungtų PHP procesų paleidimo funkcijų.
delete_transient( 'pll_languages_list' );
delete_option( 'rewrite_rules' );
echo "Nuorodu taisykles pazymetos atnaujinti.\n";

echo 'Baigta: atnaujinta ' . $updated . ', sukurta ' . $created . ', pasalinta ' . $deleted . ', nuotrauku susieta ' . count( $id_map ) . ".\n";
