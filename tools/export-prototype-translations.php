<?php
/**
 * Export the curated WordPress public-copy catalogues for static prototypes.
 */

$root = dirname( __DIR__ );
$language_dir = $root . '/wordpress/wp-content/plugins/5gtech-core/languages';
$target = $root . '/dizainas/maketai/5gtech-vidiniai-v1/prototype-i18n.json';

$catalogues = array();

foreach ( array( 'en', 'de' ) as $language ) {
	$base = require $language_dir . '/' . $language . '.php';
	$overrides = require $language_dir . '/' . $language . '-overrides.php';
	$catalogues[ $language ] = array_replace( $base, $overrides );
}

$json = json_encode(
	$catalogues,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
);

if ( false === $json || false === file_put_contents( $target, $json . "\n" ) ) {
	fwrite( STDERR, "Failed to export prototype translations.\n" );
	exit( 1 );
}

printf(
	"Exported %d EN and %d DE translations to %s\n",
	count( $catalogues['en'] ),
	count( $catalogues['de'] ),
	$target
);
