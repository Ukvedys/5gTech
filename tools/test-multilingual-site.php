<?php
/**
 * End-to-end checks for LT, EN and DE public pages.
 *
 * Usage: php tools/test-multilingual-site.php [http://5gtech.test]
 */

$base_url = rtrim( $argv[1] ?? 'http://5gtech.test', '/' );

require dirname( __DIR__ ) . '/wordpress/wp-load.php';

$failures = array();
$checks   = 0;

/**
 * Request one page without following redirects.
 *
 * @return array{status:int,headers:string,body:string}
 */
function g5_test_request( $url, array $request_headers = array() ) {
	$handle = curl_init( $url );
	curl_setopt_array(
		$handle,
		array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => true,
			CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_TIMEOUT        => 20,
			CURLOPT_HTTPHEADER     => $request_headers,
		)
	);
	$response    = (string) curl_exec( $handle );
	$header_size = (int) curl_getinfo( $handle, CURLINFO_HEADER_SIZE );
	$status      = (int) curl_getinfo( $handle, CURLINFO_RESPONSE_CODE );
	curl_close( $handle );

	return array(
		'status'  => $status,
		'headers' => substr( $response, 0, $header_size ),
		'body'    => substr( $response, $header_size ),
	);
}

function g5_test_assert( $condition, $message ) {
	global $failures, $checks;
	++$checks;
	if ( ! $condition ) {
		$failures[] = $message;
	}
}

$routes = g5tech_i18n_routes();

foreach ( array( 'en', 'de' ) as $language ) {
	$catalogue = array_replace(
		require dirname( __DIR__ ) . '/wordpress/wp-content/plugins/5gtech-core/languages/' . $language . '.php',
		require dirname( __DIR__ ) . '/wordpress/wp-content/plugins/5gtech-core/languages/' . $language . '-overrides.php'
	);
	$expected_lang = 'en' === $language ? 'en-US' : 'de-DE';

	foreach ( $routes as $canonical_path => $localised_paths ) {
		if ( ! isset( $localised_paths[ $language ] ) ) {
			continue;
		}

		$path     = '/' . $language . g5tech_normalize_i18n_path( $localised_paths[ $language ] );
		$url      = $base_url . $path;
		$response = g5_test_request( $url );
		$label    = strtoupper( $language ) . ' ' . $path;

		g5_test_assert( 200 === $response['status'], $label . ' returned HTTP ' . $response['status'] );
		if ( 200 !== $response['status'] ) {
			continue;
		}

		g5_test_assert(
			(bool) preg_match( '/<html[^>]+lang="' . preg_quote( $expected_lang, '/' ) . '"/i', $response['body'] ),
			$label . ' has an incorrect HTML language.'
		);
		g5_test_assert(
			4 === preg_match_all( '/<link[^>]+rel="alternate"[^>]+hreflang=/i', $response['body'] ),
			$label . ' does not contain all hreflang links.'
		);
		g5_test_assert(
			(bool) preg_match( '/g5-language-switcher__current[^>]+lang="' . $language . '"/i', $response['body'] ),
			$label . ' language switcher does not identify the active language.'
		);
		g5_test_assert(
			2 === preg_match_all( '/g5-language-switcher__link[^>]+g5_lang=/i', $response['body'] ) / 2,
			$label . ' language switcher does not expose the other two languages in both locations.'
		);

		$document = new DOMDocument();
		@$document->loadHTML( $response['body'] );
		$xpath = new DOMXPath( $document );

		$untranslated = array();
		foreach ( $xpath->query( '//body//text()[not(ancestor::script) and not(ancestor::style) and not(ancestor::svg)]' ) as $node ) {
			$text = preg_replace( '/\s+/u', ' ', trim( $node->nodeValue ) );
			if ( '' !== $text && isset( $catalogue[ $text ] ) && $catalogue[ $text ] !== $text ) {
				$untranslated[ $text ] = true;
			}
		}
		g5_test_assert(
			! $untranslated,
			$label . ' contains untranslated copy: ' . implode( ' | ', array_keys( $untranslated ) )
		);

		foreach ( $xpath->query( '//body//a[@href]' ) as $link ) {
			$href = html_entity_decode( $link->getAttribute( 'href' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			if (
				'' === $href
				|| str_starts_with( $href, '#' )
				|| str_starts_with( $href, 'mailto:' )
				|| str_starts_with( $href, 'tel:' )
				|| false !== strpos( $href, 'g5_lang=' )
			) {
				continue;
			}

			$parts = wp_parse_url( $href );
			if ( false === $parts ) {
				continue;
			}
			$base_parts = wp_parse_url( $base_url );
			if ( isset( $parts['host'] ) && strtolower( $parts['host'] ) !== strtolower( $base_parts['host'] ) ) {
				continue;
			}

			$link_path = $parts['path'] ?? '/';
			if ( g5tech_i18n_is_technical_path( $link_path ) ) {
				continue;
			}

			g5_test_assert(
				$link_path === '/' . $language . '/' || str_starts_with( $link_path, '/' . $language . '/' ),
				$label . ' contains a public link outside its language: ' . $href
			);
		}
	}
}

$detection_cases = array(
	array( '/', 'de-DE,de;q=0.9,en;q=0.8', 302, '/de/' ),
	array( '/', 'de-AT,de;q=0.9,en;q=0.8', 302, '/de/' ),
	array( '/', 'en-GB,en;q=0.9', 302, '/en/' ),
	array( '/', 'de;q=0,en;q=0.8', 302, '/en/' ),
	array( '/', 'fr-FR,fr;q=0.9', 200, '' ),
	array( '/paslaugos/', 'de-DE,de;q=0.9', 302, '/de/leistungen/' ),
);

foreach ( $detection_cases as [ $path, $accept_language, $status, $location_path ] ) {
	$response = g5_test_request( $base_url . $path, array( 'Accept-Language: ' . $accept_language ) );
	g5_test_assert( $status === $response['status'], 'Language detection returned an unexpected status for ' . $path . ' and ' . $accept_language );
	g5_test_assert(
		(bool) preg_match( '/^Vary:.*Accept-Language.*Cookie/im', $response['headers'] ),
		'Language detection response is missing its cache variation header for ' . $path . ' and ' . $accept_language
	);
	if ( $location_path ) {
		g5_test_assert(
			(bool) preg_match( '/^Location:\s*' . preg_quote( $base_url . $location_path, '/' ) . '\s*$/mi', $response['headers'] ),
			'Language detection returned an incorrect location for ' . $path . ' and ' . $accept_language
		);
	}
}

$switch_response = g5_test_request( $base_url . '/en/services/?g5_lang=lt' );
g5_test_assert( 302 === $switch_response['status'], 'Explicit LT language switch did not redirect.' );
g5_test_assert(
	(bool) preg_match( '/^Location:\s*' . preg_quote( $base_url . '/paslaugos/', '/' ) . '\s*$/mi', $switch_response['headers'] ),
	'Explicit LT language switch did not preserve the current page.'
);
g5_test_assert(
	(bool) preg_match( '/^Set-Cookie:\s*g5tech_language=lt;/mi', $switch_response['headers'] ),
	'Explicit language switch did not save the selected language.'
);

$cookie_response = g5_test_request(
	$base_url . '/paslaugos/',
	array( 'Cookie: g5tech_language=de' )
);
g5_test_assert( 302 === $cookie_response['status'], 'Saved language preference did not redirect.' );
g5_test_assert(
	(bool) preg_match( '/^Location:\s*' . preg_quote( $base_url . '/de/leistungen/', '/' ) . '\s*$/mi', $cookie_response['headers'] ),
	'Saved language preference did not preserve the requested page.'
);

$query_pages = array(
	'/en/contact/?forma=success',
	'/en/contact/?forma=required',
	'/en/contact/?forma=email',
	'/en/contact/?forma=consent',
	'/en/contact/?forma=mail',
	'/en/contact/?forma=security',
	'/de/kontakt/?forma=success',
	'/de/kontakt/?forma=required',
	'/de/kontakt/?forma=email',
	'/de/kontakt/?forma=consent',
	'/de/kontakt/?forma=mail',
	'/de/kontakt/?forma=security',
	'/en/apply/?kandidatas=success',
	'/en/apply/?kandidatas=file',
	'/de/bewerben/?kandidatas=success',
	'/de/bewerben/?kandidatas=file',
	'/en/?s=network',
	'/de/?s=Mobilfunk',
);

foreach ( $query_pages as $path ) {
	$response = g5_test_request( $base_url . $path );
	g5_test_assert( 200 === $response['status'], $path . ' returned HTTP ' . $response['status'] );
	$language = str_starts_with( $path, '/de/' ) ? 'de' : 'en';
	g5_test_assert(
		(bool) preg_match( '/<html[^>]+lang="' . ( 'de' === $language ? 'de-DE' : 'en-US' ) . '"/i', $response['body'] ),
		$path . ' lost its language while preserving query parameters.'
	);
}

foreach ( array( 'en', 'de' ) as $language ) {
	$response = g5_test_request( $base_url . '/' . $language . '/this-page-does-not-exist/' );
	g5_test_assert( 404 === $response['status'], strtoupper( $language ) . ' unknown route does not return HTTP 404.' );
}

$homepage_assets = array(
	'hero-indoor-networks.jpg',
	'hero-fixed-networks.jpg',
	'hero-electrical.jpg',
	'hero-security.jpg',
	'hero-solar.jpg',
);
$homepage_response = g5_test_request( $base_url . '/', array( 'Cookie: g5tech_language=lt' ) );
foreach ( $homepage_assets as $asset ) {
	g5_test_assert(
		false !== strpos( $homepage_response['body'], '/assets/images/home/' . $asset ),
		'Homepage is missing its original hero asset: ' . $asset
	);
}
g5_test_assert(
	false === strpos( $homepage_response['body'], '/assets/images/generated/service-' ),
	'Generated service visuals must not be used in the homepage hero.'
);

$visual_asset_pages = array(
	'/paslaugos/mobiliojo-rysio-tinklai/'            => 'from-live-site/service-mobile-networks.png',
	'/paslaugos/vidinio-rysio-tinklai/'              => 'from-live-site/service-indoor-networks.png',
	'/paslaugos/fiksuoto-rysio-tinklai/'             => 'generated/service-fixed-networks-v1.jpg',
	'/paslaugos/elektros-darbai/'                    => 'generated/service-electrical-v1.jpg',
	'/paslaugos/apsaugos-ir-stebejimo-sistemos/'     => 'generated/service-security-v1.jpg',
	'/paslaugos/saules-elektrines/'                  => 'from-live-site/service-solar.png',
	'/mokymai/'                                      => 'from-live-site/training-room-wide.jpg',
	'/projektai/baziniu-stociu-modernizavimas-vokietijoje/' => 'generated/project-telecom-site-v1.jpg',
);

foreach ( $visual_asset_pages as $path => $asset ) {
	$response = g5_test_request( $base_url . $path, array( 'Cookie: g5tech_language=lt' ) );
	g5_test_assert( 200 === $response['status'], $path . ' returned HTTP ' . $response['status'] . ' during asset verification.' );
	g5_test_assert(
		false !== strpos( $response['body'], '/assets/images/' . $asset ),
		$path . ' is missing its assigned visual: ' . $asset
	);
}

if ( $failures ) {
	fwrite( STDERR, sprintf( "FAILED: %d of %d checks\n", count( $failures ), $checks ) );
	foreach ( $failures as $failure ) {
		fwrite( STDERR, ' - ' . $failure . "\n" );
	}
	exit( 1 );
}

printf( "PASS: %d multilingual checks\n", $checks );
