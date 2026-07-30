<?php
/**
 * Turinio modulių vertimų regresijos testas.
 *
 * Paleidimas:
 * php tools/test-wordpress-module-translations.php
 */

require dirname( __DIR__ ) . '/wordpress/wp-load.php';

$checks = 0;

function g5tech_i18n_test_assert( $condition, $message ) {
	global $checks;
	++$checks;

	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

g5tech_i18n_test_assert( 'English' === g5tech_languages()['en']['name'], 'EN language name is available to the editor.' );
g5tech_i18n_test_assert( 'Deutsch' === g5tech_languages()['de']['name'], 'DE language name is available to the editor.' );
g5tech_i18n_test_assert( 'Program' === g5tech_module_translation_default( 'Programa', 'en' ), 'Existing EN catalogue prefills module fields.' );
g5tech_i18n_test_assert( 'Programm' === g5tech_module_translation_default( 'Programa', 'de' ), 'Existing DE catalogue prefills module fields.' );

$module_id = wp_insert_post(
	array(
		'post_type'   => 'g5_module',
		'post_status' => 'publish',
		'post_title'  => 'Vertimų sistemos testas',
	)
);

g5tech_i18n_test_assert( ! is_wp_error( $module_id ) && $module_id > 0, 'Temporary module was created.' );

update_post_meta( $module_id, 'g5_module_type', 'list' );
update_post_meta( $module_id, 'g5_module_eyebrow', 'Testo žyma' );
update_post_meta( $module_id, 'g5_module_heading', 'Testo antraštė' );
update_post_meta( $module_id, 'g5_module_lead', 'Testo įžanga' );
update_post_meta( $module_id, 'g5_module_content', "Pirmas punktas\nAntras punktas" );
update_post_meta(
	$module_id,
	'_g5tech_module_translations',
	array(
		'en' => array(
			'fields' => array(
				'eyebrow' => 'Test label',
				'heading' => 'Test heading',
				'lead'    => 'Test introduction',
				'content' => "First item\nSecond item",
			),
			'pairs' => array(),
		),
		'de' => array(
			'fields' => array(
				'eyebrow' => 'Testbezeichnung',
				'heading' => 'Testüberschrift',
				'lead'    => 'Testeinleitung',
				'content' => "Erster Punkt\nZweiter Punkt",
			),
			'pairs' => array(),
		),
	)
);

$en_pairs = g5tech_compile_module_translation_pairs( $module_id, 'en' );
$de_pairs = g5tech_compile_module_translation_pairs( $module_id, 'de' );

g5tech_i18n_test_assert( 'Test heading' === ( $en_pairs['Testo antraštė'] ?? '' ), 'Static EN heading is compiled.' );
g5tech_i18n_test_assert( 'Second item' === ( $en_pairs['Antras punktas'] ?? '' ), 'Multiline EN content is compiled line by line.' );
g5tech_i18n_test_assert( 'Testüberschrift' === ( $de_pairs['Testo antraštė'] ?? '' ), 'Static DE heading is compiled.' );
g5tech_i18n_test_assert( 'Zweiter Punkt' === ( $de_pairs['Antras punktas'] ?? '' ), 'Multiline DE content is compiled line by line.' );

$overrides = g5tech_rebuild_content_translation_overrides();
g5tech_i18n_test_assert( 'Test heading' === ( $overrides['en']['Testo antraštė'] ?? '' ), 'Compiled EN catalogue includes module override.' );
g5tech_i18n_test_assert( 'Testüberschrift' === ( $overrides['de']['Testo antraštė'] ?? '' ), 'Compiled DE catalogue includes module override.' );

$dynamic = get_posts(
	array(
		'post_type'      => 'g5_module',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => 'g5_module_type',
		'meta_value'     => 'dynamic',
	)
);

g5tech_i18n_test_assert( ! empty( $dynamic ), 'A dynamic module exists for extraction testing.' );
g5tech_i18n_test_assert( count( g5tech_module_visible_strings( $dynamic[0] ) ) > 0, 'Visible strings are extracted from a dynamic module.' );

wp_delete_post( $module_id, true );
g5tech_rebuild_content_translation_overrides();

echo "PASS: {$checks} module translation checks\n";
