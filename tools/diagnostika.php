<?php
/**
 * Lokalios svetainės diagnostika: kalbos ir komandos nuotraukos.
 * Paleidimas: php tools/diagnostika.php
 */
define( 'PLL_SETTINGS', true );
define( 'PLL_ADMIN', true );
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? '5gtech.test';
require dirname( __DIR__ ) . '/wordpress/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

echo "== Polylang ==\n";
echo 'Aktyvus: ', is_plugin_active( 'polylang/polylang.php' ) ? 'TAIP' : 'NE', "\n";
echo 'PLL(): ', ( function_exists( 'PLL' ) && PLL() ) ? 'veikia' : 'NEVEIKIA', "\n";

if ( function_exists( 'pll_languages_list' ) ) {
	echo 'Kalbos: ', implode( ', ', (array) pll_languages_list() ), "\n";
}

$opt = get_option( 'polylang' );
if ( is_array( $opt ) ) {
	foreach ( array( 'force_lang', 'hide_default', 'redirect_lang', 'browser' ) as $k ) {
		echo "$k = ", var_export( $opt[ $k ] ?? null, true ), "\n";
	}
	echo 'post_types = ', implode( ',', (array) ( $opt['post_types'] ?? array() ) ), "\n";
}

echo "\n== Marsrutai ==\n";
foreach ( array( '/en/', '/de/', '/en/about-us/', '/de/leistungen/' ) as $path ) {
	$url = home_url( $path );
	$response = wp_remote_get( $url, array( 'timeout' => 10, 'redirection' => 0, 'sslverify' => false ) );
	echo $path, ' => ', is_wp_error( $response ) ? $response->get_error_message() : wp_remote_retrieve_response_code( $response ), "\n";
}

echo "\n== Komandos nuotraukos ==\n";
$members = get_posts( array( 'post_type' => 'g5_team', 'posts_per_page' => -1, 'post_status' => 'any', 'lang' => '' ) );
foreach ( $members as $m ) {
	$lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $m->ID ) : '?';
	$tid  = get_post_thumbnail_id( $m->ID );
	$file = $tid ? get_post_meta( $tid, '_wp_attached_file', true ) : '-';
	$ok   = $tid && file_exists( wp_get_upload_dir()['basedir'] . '/' . $file ) ? 'yra' : 'NERA';
	echo str_pad( $m->post_name, 30 ), " [$lang] thumb=", $tid ?: 0, " $file ($ok)\n";
}

echo "\n== Priedu failai ==\n";
$up = wp_get_upload_dir();
foreach ( glob( $up['basedir'] . '/2026/08/komanda-*.jpg' ) as $f ) {
	echo basename( $f ), ' ', filesize( $f ), " B\n";
}
echo "Baigta.\n";
