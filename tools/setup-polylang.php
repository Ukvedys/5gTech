<?php
/**
 * Polylang diegimas ir LT/EN/DE kopijų generavimas.
 *
 * Paleidimas iš projekto šaknies:  php tools/setup-polylang.php
 * Idempotentiškas: pakartotinai paleidus atnaujina esamas kopijas.
 * Vienam šaltinio puslapiui: php tools/setup-polylang.php paslaugos
 */

$root = dirname( __DIR__ );

if ( isset( $argv[1] ) && 'admin-steps' === $argv[1] ) {
	$only_source_slugs = isset( $argv[2] ) ? array_filter( explode( ',', $argv[2] ) ) : array();
	define( 'PLL_SETTINGS', true );
	define( 'PLL_ADMIN', true );
	$_SERVER['HTTP_HOST'] = 'localhost';
	require $root . '/wordpress/wp-load.php';
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
	wp_set_current_user( $admins[0]->ID );
	kses_remove_filters();

	if ( ! is_plugin_active( 'polylang/polylang.php' ) ) {
		$r = activate_plugin( 'polylang/polylang.php' );
		if ( is_wp_error( $r ) ) { echo 'AKTYVAVIMO KLAIDA: ' . $r->get_error_message() . "\n"; exit( 1 ); }
		echo "Polylang aktyvuotas. Paleiskite skriptą DAR KARTĄ, kad būtų sukurtos kalbos.\n";
		exit( 0 );
	}

	if ( ! function_exists( 'PLL' ) || ! PLL() ) { echo "PLL nepasikrovė\n"; exit( 1 ); }

	require dirname( __DIR__ ) . '/deploy/polylang-shared.php';
	g5pll_ensure_languages();
	g5pll_apply_options();
	g5pll_generate_copies( $only_source_slugs );
	echo "admin-steps baigta\n";
	exit( 0 );
}

// 1. Administravimo žingsniai atskirame procese (Polylang CLI ypatybė).
$only_source_slugs = isset( $argv[1] ) ? (string) $argv[1] : '';
$command = escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' admin-steps';
if ( '' !== $only_source_slugs ) $command .= ' ' . escapeshellarg( $only_source_slugs );
passthru( $command, $code );
if ( 0 !== $code ) exit( $code );

// 2. Nuorodų taisyklės atnaujinamos viešame kontekste (kitaip CPT be kalbos prefikso).
$_SERVER['HTTP_HOST'] = 'localhost';
require $root . '/wordpress/wp-load.php';
delete_transient( 'pll_languages_list' );
flush_rewrite_rules();
echo "Nuorodų taisyklės atnaujintos. Polylang paruoštas.\n";
