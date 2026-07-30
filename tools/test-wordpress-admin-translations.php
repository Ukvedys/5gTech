<?php
/**
 * Puslapių formų LT / EN / DE vertimų regresijos testas.
 *
 * Paleidimas:
 * php tools/test-wordpress-admin-translations.php
 */

require dirname( __DIR__ ) . '/wordpress/wp-load.php';

$checks                    = 0;
$original_translations     = get_option( 'g5tech_admin_content_translations', null );
$original_overrides        = get_option( 'g5tech_admin_translation_overrides', null );
$translations_option_exists = false !== get_option( 'g5tech_admin_content_translations', false );
$overrides_option_exists    = false !== get_option( 'g5tech_admin_translation_overrides', false );
$original_post             = $_POST;

function g5tech_admin_i18n_test_assert( $condition, $message ) {
	global $checks;
	++$checks;

	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

try {
	g5tech_admin_i18n_test_assert(
		array( 'en', 'de' ) === array_keys( g5tech_admin_translation_languages() ),
		'Administravimo kalbų sąrašas neatitinka EN ir DE.'
	);

	$sources = g5tech_admin_content_source_strings(
		array(
			'title' => 'Testo antraštė',
			'items' => array(
				array( 'text' => "Pirma eilutė\nAntra eilutė" ),
			),
			'url' => 'https://example.test/',
			'id'  => 15,
		)
	);

	g5tech_admin_i18n_test_assert( in_array( 'Testo antraštė', $sources, true ), 'Paprastas tekstas nesurinktas.' );
	g5tech_admin_i18n_test_assert( in_array( "Pirma eilutė\nAntra eilutė", $sources, true ), 'Kelių eilučių tekstas nesurinktas.' );
	g5tech_admin_i18n_test_assert( ! in_array( 'https://example.test/', $sources, true ), 'Techninė URL reikšmė neturi būti verčiama.' );

	$_POST = array(
		'g5tech_admin_i18n_context' => 'test_context',
		'g5tech_admin_i18n_nonce'   => wp_create_nonce( 'g5tech_save_admin_content_translations_test_context' ),
		'g5tech_admin_i18n_payload' => wp_slash(
			wp_json_encode(
			array(
				'en' => array(
					array(
						'source'      => "Pirma eilutė\nAntra eilutė",
						'translation' => "First line\nSecond line",
					),
				),
				'de' => array(
					array(
						'source'      => 'Testo antraštė',
						'translation' => 'Testüberschrift',
					),
				),
			)
			)
		),
	);

	g5tech_admin_i18n_test_assert(
		g5tech_save_admin_content_translations( 'test_context' ),
		'Vertimų forma neišsaugota.'
	);

	$saved = g5tech_get_admin_content_translations( 'test_context' );
	g5tech_admin_i18n_test_assert(
		"First line\nSecond line" === ( $saved['en'][ "Pirma eilutė\nAntra eilutė" ] ?? '' ),
		'EN vertimų pora neišsaugota.'
	);
	g5tech_admin_i18n_test_assert(
		'Testüberschrift' === ( $saved['de']['Testo antraštė'] ?? '' ),
		'DE vertimų pora neišsaugota.'
	);

	$overrides = g5tech_rebuild_admin_content_translation_overrides();
	g5tech_admin_i18n_test_assert(
		'Second line' === ( $overrides['en']['Antra eilutė'] ?? '' ),
		'Kelių eilučių EN tekstas nesuskaidytas viešam katalogui.'
	);
	g5tech_admin_i18n_test_assert(
		'Testüberschrift' === ( $overrides['de']['Testo antraštė'] ?? '' ),
		'DE tekstas nepateko į viešą katalogą.'
	);

	foreach (
		array(
			'about'                      => 'includes/team.php',
			'career'                     => 'includes/jobs.php',
			'training'                   => 'includes/admin.php',
			'structured_'                => 'includes/structured-content.php',
			'settings'                   => 'includes/settings.php',
			'service_'                    => 'includes/services.php',
		) as $context => $file
	) {
		$source = file_get_contents( G5TECH_CORE_DIR . $file );
		g5tech_admin_i18n_test_assert(
			1 === preg_match(
				"/g5tech_render_admin_content_translation_context\\(\\s*'" . preg_quote( $context, '/' ) . '/',
				$source
			),
			"Kalbų valdiklis neprijungtas prie konteksto: {$context}."
		);
		g5tech_admin_i18n_test_assert(
			1 === preg_match(
				"/g5tech_save_admin_content_translations\\(\\s*'" . preg_quote( $context, '/' ) . '/',
				$source
			),
			"Vertimų išsaugojimas neprijungtas prie konteksto: {$context}."
		);
	}

	$service_order = g5tech_service_meta_box_order(
		array(
			'side'     => 'submitdiv,g5tech-service-details,postimagediv',
			'normal'   => 'slugdiv',
			'advanced' => '',
		)
	);
	g5tech_admin_i18n_test_assert(
		'g5tech-service-details,slugdiv' === $service_order['normal'],
		'Paslaugos turinio blokas negrąžintas į pagrindinį stulpelį.'
	);
	g5tech_admin_i18n_test_assert(
		false === strpos( $service_order['side'], 'g5tech-service-details' ),
		'Paslaugos turinio blokas liko šoniniame stulpelyje.'
	);
} finally {
	$_POST = $original_post;

	if ( $translations_option_exists ) {
		update_option( 'g5tech_admin_content_translations', $original_translations, false );
	} else {
		delete_option( 'g5tech_admin_content_translations' );
	}

	if ( $overrides_option_exists ) {
		update_option( 'g5tech_admin_translation_overrides', $original_overrides, false );
	} else {
		delete_option( 'g5tech_admin_translation_overrides' );
	}
}

echo "PASS: {$checks} admin translation checks\n";
