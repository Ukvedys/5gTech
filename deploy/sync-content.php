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

if ( ! defined( 'ABSPATH' ) ) {
	require dirname( __DIR__, 2 ) . '/wp-load.php';
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

// Tėviniai priskiriami antru praėjimu, kai visi įrašai jau egzistuoja.
$parents = array();

foreach ( $data['posts'] as $item ) {
	$slug = (string) $item['slug'];
	$type = (string) $item['type'];

	if ( '' === $slug || ! post_type_exists( $type ) ) {
		continue;
	}

	$payload = array(
		'post_type'    => $type,
		'post_name'    => $slug,
		'post_title'   => (string) $item['title'],
		'post_status'  => (string) $item['status'],
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

// 4. Nustatymai.
foreach ( (array) ( $data['options'] ?? array() ) as $name => $value ) {
	update_option( $name, $value );
}

// 5. Pradinis puslapis pagal slug (ID skiriasi tarp aplinkų).
$front = $find_post( 'page', 'pagrindinis' );

if ( $front ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front->ID );
}

flush_rewrite_rules();

echo 'Baigta: atnaujinta ' . $updated . ', sukurta ' . $created . ', nuotrauku susieta ' . count( $id_map ) . ".\n";
