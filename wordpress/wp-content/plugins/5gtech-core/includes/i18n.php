<?php
/**
 * Front-end multilingual layer for Lithuanian, English and German.
 *
 * Lithuanian remains the canonical source language. English and German use
 * prefixed, localised URLs while resolving to the same WordPress content
 * objects. Public copy is translated through curated catalogues so shared
 * modules stay consistent on every page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supported public languages.
 *
 * @return array<string,array<string,string>>
 */
/**
 * Ar kalbas valdo Polylang.
 *
 * Kai Polylang sukonfigūruotas, senasis maršrutų / nukreipimų / lokalės
 * sluoksnis užleidžia jam vietą, o vertimų katalogas lieka tik dinaminių
 * tekstų (nustatymų, katalogų įrašų) vertimui išvesties buferyje.
 */
function g5tech_polylang_ready() {
	static $ready = null;

	if ( null === $ready ) {
		$options = get_option( 'polylang' );
		$ready   = is_array( $options ) && ! empty( $options['version'] );
	}

	return $ready;
}

function g5tech_languages() {
	return array(
		'lt' => array(
			'label'  => 'LT',
			'name'   => 'Lietuvių',
			'locale' => 'lt_LT',
			'prefix' => '',
		),
		'en' => array(
			'label'  => 'EN',
			'name'   => 'English',
			'locale' => 'en_US',
			'prefix' => 'en',
		),
		'de' => array(
			'label'  => 'DE',
			'name'   => 'Deutsch',
			'locale' => 'de_DE',
			'prefix' => 'de',
		),
	);
}

/**
 * Normalise a site path for route comparisons.
 */
function g5tech_normalize_i18n_path( $path ) {
	$path = '/' . trim( (string) $path, '/' );

	return '/' === $path ? '/' : trailingslashit( $path );
}

/**
 * Public route catalogue. Keys are canonical Lithuanian paths.
 *
 * Dynamic entries which keep their post slug (team profiles and any future
 * posts) are handled by the first-segment fallback below.
 *
 * @return array<string,array<string,string>>
 */
function g5tech_i18n_routes() {
	$routes = array(
		'/' => array(
			'en' => '/',
			'de' => '/',
		),
		'/paslaugos/' => array(
			'en' => '/services/',
			'de' => '/leistungen/',
		),
		'/paslaugos/mobiliojo-rysio-tinklai/' => array(
			'en' => '/services/mobile-networks/',
			'de' => '/leistungen/mobilfunknetze/',
		),
		'/paslaugos/vidinio-rysio-tinklai/' => array(
			'en' => '/services/in-building-networks/',
			'de' => '/leistungen/indoor-mobilfunk/',
		),
		'/paslaugos/fiksuoto-rysio-tinklai/' => array(
			'en' => '/services/fixed-networks/',
			'de' => '/leistungen/festnetze/',
		),
		'/paslaugos/elektros-darbai/' => array(
			'en' => '/services/electrical-installations/',
			'de' => '/leistungen/elektroinstallationen/',
		),
		'/paslaugos/apsaugos-ir-stebejimo-sistemos/' => array(
			'en' => '/services/security-and-surveillance-systems/',
			'de' => '/leistungen/sicherheits-und-ueberwachungssysteme/',
		),
		'/paslaugos/saules-elektrines/' => array(
			'en' => '/services/solar-power-systems/',
			'de' => '/leistungen/photovoltaikanlagen/',
		),
		'/patirtis/' => array(
			'en' => '/experience/',
			'de' => '/erfahrung/',
		),
		'/projektai/' => array(
			'en' => '/projects/',
			'de' => '/projekte/',
		),
		'/projektai/baziniu-stociu-modernizavimas-vokietijoje/' => array(
			'en' => '/projects/mobile-network-modernisation-germany/',
			'de' => '/projekte/mobilfunk-modernisierung-deutschland/',
		),
		'/apie-mus/' => array(
			'en' => '/about-us/',
			'de' => '/ueber-uns/',
		),
		'/komanda/aleksandras-iljinas/' => array(
			'en' => '/team/aleksandras-iljinas/',
			'de' => '/team/aleksandras-iljinas/',
		),
		'/karjera/' => array(
			'en' => '/careers/',
			'de' => '/karriere/',
		),
		'/karjera/telekomunikaciju-specialistas/' => array(
			'en' => '/careers/telecommunications-technician/',
			'de' => '/karriere/telekommunikationstechniker/',
		),
		'/kandidatuoti/' => array(
			'en' => '/apply/',
			'de' => '/bewerben/',
		),
		'/akademija/' => array(
			'en' => '/academy/',
			'de' => '/akademie/',
		),
		'/mokymai/' => array(
			'en' => '/training/',
			'de' => '/schulungen/',
		),
		'/duk/' => array(
			'en' => '/candidate-faq/',
			'de' => '/faq-fuer-bewerber/',
		),
		'/vadovams/' => array(
			'en' => '/for-executives/',
			'de' => '/fuer-entscheider/',
		),
		'/projektu-vadovams/' => array(
			'en' => '/for-project-managers/',
			'de' => '/fuer-projektleiter/',
		),
		'/naujienos/' => array(
			'en' => '/news/',
			'de' => '/aktuelles/',
		),
		'/baziniu-stociu-modernizavimo-projektas-vokietijoje/' => array(
			'en' => '/mobile-network-modernisation-project-germany/',
			'de' => '/mobilfunk-modernisierungsprojekt-deutschland/',
		),
		'/praktiniai-darbuotoju-mokymai/' => array(
			'en' => '/practical-employee-training/',
			'de' => '/praxisnahe-mitarbeiterschulungen/',
		),
		'/projektai-sesiose-europos-salyse/' => array(
			'en' => '/projects-in-six-european-countries/',
			'de' => '/projekte-in-sechs-europaeischen-laendern/',
		),
		'/kontaktai/' => array(
			'en' => '/contact/',
			'de' => '/kontakt/',
		),
		'/privatumo-politika/' => array(
			'en' => '/privacy-policy/',
			'de' => '/datenschutz/',
		),
		'/slapukai/' => array(
			'en' => '/cookies/',
			'de' => '/cookies/',
		),
	);

	return apply_filters( 'g5tech_i18n_routes', $routes );
}

/**
 * First path segments used as a fallback for future dynamic entries.
 *
 * @return array<string,array<string,string>>
 */
function g5tech_i18n_route_segments() {
	return array(
		'paslaugos' => array( 'en' => 'services', 'de' => 'leistungen' ),
		'projektai' => array( 'en' => 'projects', 'de' => 'projekte' ),
		'komanda'   => array( 'en' => 'team', 'de' => 'team' ),
		'karjera'   => array( 'en' => 'careers', 'de' => 'karriere' ),
	);
}

/**
 * Resolve a localised path back to its canonical Lithuanian path.
 */
function g5tech_i18n_canonical_path( $path, $language ) {
	$path     = g5tech_normalize_i18n_path( $path );
	$language = sanitize_key( $language );

	if ( 'lt' === $language ) {
		return $path;
	}

	foreach ( g5tech_i18n_routes() as $canonical => $translations ) {
		if ( isset( $translations[ $language ] ) && g5tech_normalize_i18n_path( $translations[ $language ] ) === $path ) {
			return g5tech_normalize_i18n_path( $canonical );
		}
	}

	$segments = array_values( array_filter( explode( '/', trim( $path, '/' ) ) ) );
	if ( ! $segments ) {
		return '/';
	}

	foreach ( g5tech_i18n_route_segments() as $canonical_segment => $translations ) {
		if ( isset( $translations[ $language ] ) && $translations[ $language ] === $segments[0] ) {
			$segments[0] = $canonical_segment;
			return g5tech_normalize_i18n_path( implode( '/', $segments ) );
		}
	}

	return $path;
}

/**
 * Convert a canonical Lithuanian path to a requested language.
 */
function g5tech_i18n_localized_path( $path, $language ) {
	$path     = g5tech_normalize_i18n_path( $path );
	$language = sanitize_key( $language );

	if ( 'lt' === $language ) {
		return $path;
	}

	$routes = g5tech_i18n_routes();
	if ( isset( $routes[ $path ][ $language ] ) ) {
		$translated_path = g5tech_normalize_i18n_path( $routes[ $path ][ $language ] );
	} else {
		$translated_path = $path;
		$segments        = array_values( array_filter( explode( '/', trim( $path, '/' ) ) ) );
		$segment_map     = g5tech_i18n_route_segments();

		if ( $segments && isset( $segment_map[ $segments[0] ][ $language ] ) ) {
			$segments[0]    = $segment_map[ $segments[0] ][ $language ];
			$translated_path = g5tech_normalize_i18n_path( implode( '/', $segments ) );
		}
	}

	$prefix = g5tech_languages()[ $language ]['prefix'];

	return g5tech_normalize_i18n_path( $prefix . '/' . ltrim( $translated_path, '/' ) );
}

/**
 * Detect and remove a language prefix before WordPress parses the request.
 */
function g5tech_i18n_bootstrap_request() {
	if ( PHP_SAPI === 'cli' || empty( $_SERVER['REQUEST_URI'] ) ) {
		$GLOBALS['g5tech_language']      = 'lt';
		$GLOBALS['g5tech_i18n_base_path'] = '/';
		return;
	}

	$request_uri = (string) wp_unslash( $_SERVER['REQUEST_URI'] );
	$parts       = wp_parse_url( $request_uri );
	$path        = isset( $parts['path'] ) ? g5tech_normalize_i18n_path( rawurldecode( $parts['path'] ) ) : '/';
	$query       = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
	$language    = 'lt';
	$had_prefix  = false;

	foreach ( array( 'en', 'de' ) as $candidate ) {
		$prefix = '/' . $candidate;
		if ( $path === $prefix . '/' || str_starts_with( $path, $prefix . '/' ) ) {
			$language   = $candidate;
			$had_prefix = true;
			$path       = substr( $path, strlen( $prefix ) );
			$path       = g5tech_normalize_i18n_path( $path );
			break;
		}
	}

	$canonical_path = g5tech_i18n_canonical_path( $path, $language );

	$GLOBALS['g5tech_language']       = $language;
	$GLOBALS['g5tech_i18n_base_path'] = $canonical_path;
	$GLOBALS['g5tech_i18n_prefixed']  = $had_prefix;
	$GLOBALS['g5tech_i18n_original']  = $request_uri;

	if ( $had_prefix ) {
		$_SERVER['REQUEST_URI'] = $canonical_path . $query;
	}
}

if ( g5tech_polylang_ready() ) {
	// Maršrutus valdo Polylang; senasis prefikso išėmimas nebevykdomas.
	$GLOBALS['g5tech_language']       = 'lt';
	$GLOBALS['g5tech_i18n_base_path'] = '/';
} else {
	g5tech_i18n_bootstrap_request();
}

/**
 * Current public language.
 */
function g5tech_current_language() {
	if ( g5tech_polylang_ready() && ! is_admin() && function_exists( 'pll_current_language' ) ) {
		$pll = pll_current_language();

		if ( $pll && isset( g5tech_languages()[ $pll ] ) ) {
			return $pll;
		}
	}

	$language = isset( $GLOBALS['g5tech_language'] ) ? sanitize_key( $GLOBALS['g5tech_language'] ) : 'lt';

	return isset( g5tech_languages()[ $language ] ) ? $language : 'lt';
}

/**
 * Current canonical path before language localisation.
 */
function g5tech_current_canonical_path() {
	return isset( $GLOBALS['g5tech_i18n_base_path'] )
		? g5tech_normalize_i18n_path( $GLOBALS['g5tech_i18n_base_path'] )
		: '/';
}

/**
 * Build a clean language URL for a canonical path.
 */
function g5tech_language_url( $language, $canonical_path = null, $force_choice = false ) {
	$language      = isset( g5tech_languages()[ $language ] ) ? $language : 'lt';
	$canonical_path = null === $canonical_path ? g5tech_current_canonical_path() : $canonical_path;
	$localized_path = g5tech_i18n_localized_path( $canonical_path, $language );
	$url            = untrailingslashit( get_option( 'home' ) ) . $localized_path;

	if ( $force_choice ) {
		$url = add_query_arg( 'g5_lang', $language, $url );
	}

	return $url;
}

/**
 * Skip localisation for technical WordPress and asset paths.
 */
function g5tech_i18n_is_technical_path( $path ) {
	$path = '/' . ltrim( (string) $path, '/' );

	return str_starts_with( $path, '/wp-' )
		|| str_starts_with( $path, '/feed' )
		|| str_starts_with( $path, '/comments' )
		|| str_starts_with( $path, '/xmlrpc.php' )
		|| str_starts_with( $path, '/favicon' )
		|| (bool) preg_match( '/\.(?:css|js|json|xml|jpg|jpeg|png|gif|webp|svg|ico|woff2?|ttf|pdf|zip|docx?)$/i', $path );
}

/**
 * Localise internal URLs generated by WordPress and custom modules.
 */
function g5tech_i18n_localize_url( $url, $language = null ) {
	if ( ! is_string( $url ) || '' === $url || str_starts_with( $url, '#' ) || str_starts_with( $url, 'mailto:' ) || str_starts_with( $url, 'tel:' ) ) {
		return $url;
	}

	$language = $language ?: g5tech_current_language();
	$parts    = wp_parse_url( $url );

	if ( false === $parts ) {
		return $url;
	}

	$home_parts = wp_parse_url( get_option( 'home' ) );
	if ( isset( $parts['host'] ) && isset( $home_parts['host'] ) && strtolower( $parts['host'] ) !== strtolower( $home_parts['host'] ) ) {
		return $url;
	}

	$path = isset( $parts['path'] ) ? $parts['path'] : '/';
	if ( g5tech_i18n_is_technical_path( $path ) ) {
		return $url;
	}

	$detected_language = 'lt';
	foreach ( array( 'en', 'de' ) as $candidate ) {
		if ( $path === '/' . $candidate . '/' || str_starts_with( $path, '/' . $candidate . '/' ) ) {
			$detected_language = $candidate;
			$path              = substr( $path, 3 );
			break;
		}
	}

	$canonical_path = g5tech_i18n_canonical_path( $path, $detected_language );
	$new_path       = g5tech_i18n_localized_path( $canonical_path, $language );
	$query          = isset( $parts['query'] ) ? '?' . $parts['query'] : '';
	$fragment       = isset( $parts['fragment'] ) ? '#' . $parts['fragment'] : '';

	if ( isset( $parts['host'] ) ) {
		$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : ( is_ssl() ? 'https' : 'http' );
		$port   = isset( $parts['port'] ) ? ':' . $parts['port'] : '';

		return $scheme . '://' . $parts['host'] . $port . $new_path . $query . $fragment;
	}

	return $new_path . $query . $fragment;
}

/**
 * Prefix all generated front-end home URLs with the current language.
 */
function g5tech_i18n_filter_home_url( $url, $path, $orig_scheme, $blog_id ) {
	if ( g5tech_polylang_ready() ) {
		return $url;
	}

	unset( $path, $orig_scheme, $blog_id );

	if ( is_admin() || 'lt' === g5tech_current_language() ) {
		return $url;
	}

	return g5tech_i18n_localize_url( $url );
}
add_filter( 'home_url', 'g5tech_i18n_filter_home_url', 99, 4 );

/**
 * WordPress compares the internally stripped URI with the public prefixed URI
 * and would otherwise redirect a language URL back to itself indefinitely.
 */
function g5tech_i18n_disable_prefixed_canonical_redirect( $redirect_url, $requested_url ) {
	unset( $requested_url );

	return ! empty( $GLOBALS['g5tech_i18n_prefixed'] ) ? false : $redirect_url;
}
add_filter( 'redirect_canonical', 'g5tech_i18n_disable_prefixed_canonical_redirect', 100, 2 );

/**
 * Parse supported browser languages by preference.
 */
function g5tech_i18n_browser_language() {
	$header = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) ) : '';
	if ( '' === $header ) {
		return 'lt';
	}

	$candidates = array();
	foreach ( explode( ',', $header ) as $index => $part ) {
		$bits = array_map( 'trim', explode( ';', $part ) );
		$tag  = str_replace( '_', '-', $bits[0] );
		$code = strtolower( strtok( $tag, '-' ) ?: '' );
		$q    = 1 - ( $index / 1000 );
		foreach ( array_slice( $bits, 1 ) as $parameter ) {
			if ( str_starts_with( $parameter, 'q=' ) ) {
				$q = max( 0, min( 1, (float) substr( $parameter, 2 ) ) );
				break;
			}
		}
		if ( $q > 0 && isset( g5tech_languages()[ $code ] ) ) {
			$candidates[ $code ] = max( $candidates[ $code ] ?? 0, $q );
		}
	}

	if ( ! $candidates ) {
		return 'lt';
	}

	arsort( $candidates );
	return (string) array_key_first( $candidates );
}

/**
 * Store an explicit or prefixed language choice.
 */
function g5tech_i18n_set_cookie( $language ) {
	if ( headers_sent() || ! isset( g5tech_languages()[ $language ] ) ) {
		return;
	}

	setcookie(
		'g5tech_language',
		$language,
		array(
			'expires'  => time() + YEAR_IN_SECONDS,
			'path'     => COOKIEPATH ?: '/',
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => false,
			'samesite' => 'Lax',
		)
	);
}

/**
 * Handle explicit choices and first-visit browser-language detection.
 */
function g5tech_i18n_handle_language_request() {
	if ( g5tech_polylang_ready() ) {
		return;
	}

	if ( is_admin() || wp_doing_ajax() || is_feed() || is_robots() ) {
		return;
	}

	if ( empty( $GLOBALS['g5tech_i18n_prefixed'] ) ) {
		header( 'Vary: Accept-Language, Cookie', false );
	}

	$requested = isset( $_GET['g5_lang'] ) ? sanitize_key( wp_unslash( $_GET['g5_lang'] ) ) : '';
	if ( isset( g5tech_languages()[ $requested ] ) ) {
		g5tech_i18n_set_cookie( $requested );
		$query = $_GET;
		unset( $query['g5_lang'] );
		$url = g5tech_language_url( $requested, g5tech_current_canonical_path() );
		if ( $query ) {
			$url = add_query_arg( array_map( 'sanitize_text_field', wp_unslash( $query ) ), $url );
		}
		wp_safe_redirect( $url, 302 );
		exit;
	}

	if ( ! empty( $GLOBALS['g5tech_i18n_prefixed'] ) ) {
		g5tech_i18n_set_cookie( g5tech_current_language() );
		return;
	}

	if ( is_404() ) {
		return;
	}

	$preferred_language = isset( $_COOKIE['g5tech_language'] )
		? sanitize_key( wp_unslash( $_COOKIE['g5tech_language'] ) )
		: g5tech_i18n_browser_language();

	if ( ! isset( g5tech_languages()[ $preferred_language ] ) ) {
		$preferred_language = 'lt';
	}

	g5tech_i18n_set_cookie( $preferred_language );

	if ( 'lt' !== $preferred_language ) {
		wp_safe_redirect( g5tech_language_url( $preferred_language, g5tech_current_canonical_path() ), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'g5tech_i18n_handle_language_request', -100 );

/**
 * Translation catalogue for the current language.
 *
 * @return array<string,string>
 */
function g5tech_translation_catalog( $language = null ) {
	static $catalogues = array();

	$language = $language ?: g5tech_current_language();
	if ( 'lt' === $language ) {
		return array();
	}

	if ( isset( $catalogues[ $language ] ) ) {
		return $catalogues[ $language ];
	}

	$file          = G5TECH_CORE_DIR . 'languages/' . $language . '.php';
	$overrides_file = G5TECH_CORE_DIR . 'languages/' . $language . '-overrides.php';
	$base          = file_exists( $file ) ? (array) require $file : array();
	$overrides     = file_exists( $overrides_file ) ? (array) require $overrides_file : array();
	$catalogues[ $language ] = array_replace( $base, $overrides );

	$catalogues[ $language ] = apply_filters( 'g5tech_translation_catalog', $catalogues[ $language ], $language );

	return $catalogues[ $language ];
}

/**
 * Translate one complete public text value.
 */
function g5tech_t( $text, $language = null ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return $text;
	}

	$language = $language ?: g5tech_current_language();
	if ( 'lt' === $language ) {
		return $text;
	}

	$catalogue = g5tech_translation_catalog( $language );

	return isset( $catalogue[ $text ] ) ? $catalogue[ $text ] : $text;
}

/**
 * Translate visible text and public attributes in rendered HTML.
 */
function g5tech_translate_frontend_html( $html ) {
	if ( 'lt' === g5tech_current_language() || ! is_string( $html ) || '' === $html ) {
		return $html;
	}

	$html = preg_replace_callback(
		'/>([^<>]+)</u',
		static function ( $matches ) {
			$value   = $matches[1];
			$decoded = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			$trimmed = preg_replace( '/^\s+|\s+$/u', '', $decoded );

			if ( '' === $trimmed ) {
				return $matches[0];
			}

			$translated = g5tech_t( $trimmed );
			if ( $translated === $trimmed ) {
				return $matches[0];
			}

			preg_match( '/^\s*/u', $value, $leading );
			preg_match( '/\s*$/u', $value, $trailing );

			return '>' . ( $leading[0] ?? '' ) . esc_html( $translated ) . ( $trailing[0] ?? '' ) . '<';
		},
		$html
	);

	$html = preg_replace_callback(
		'/\b(alt|title|placeholder|aria-label|value|content)=(["\'])(.*?)\2/isu',
		static function ( $matches ) {
			$value = html_entity_decode( $matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8' );

			if ( preg_match( '#^https?://#i', $value ) ) {
				$translated = g5tech_i18n_localize_url( $value );
			} else {
				$translated = g5tech_t( $value );
			}

			return $matches[1] . '=' . $matches[2] . esc_attr( $translated ) . $matches[2];
		},
		$html
	);

	$html = preg_replace_callback(
		'/<[^>]+\b(?:href|action)=(["\']).*?\1[^>]*>/isu',
		static function ( $tag_match ) {
			$tag = $tag_match[0];

			if ( false !== stripos( $tag, 'hreflang=' ) ) {
				return $tag;
			}

			return preg_replace_callback(
				'/\b(href|action)=(["\'])(.*?)\2/isu',
				static function ( $matches ) {
					$value = html_entity_decode( $matches[3], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
					if ( false !== strpos( $value, 'g5_lang=' ) ) {
						return $matches[0];
					}

					return $matches[1] . '=' . $matches[2] . esc_url( g5tech_i18n_localize_url( $value ) ) . $matches[2];
				},
				$tag
			);
		},
		$html
	);

	$locale = g5tech_languages()[ g5tech_current_language() ]['locale'];
	$html   = preg_replace( '/(<html\b[^>]*\blang=)(["\']).*?\2/i', '$1$2' . str_replace( '_', '-', $locale ) . '$2', $html, 1 );
	$html   = str_replace(
		array( '"inLanguage":"lt-LT"', '"inLanguage": "lt-LT"', 'content="lt_LT"' ),
		array( '"inLanguage":"' . str_replace( '_', '-', $locale ) . '"', '"inLanguage": "' . str_replace( '_', '-', $locale ) . '"', 'content="' . $locale . '"' ),
		$html
	);

	return $html;
}

/**
 * Start front-end translation buffering after redirects have been handled.
 */
function g5tech_i18n_start_buffer() {
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_robots() || 'lt' === g5tech_current_language() ) {
		return;
	}

	ob_start( 'g5tech_translate_frontend_html' );
}
add_action( 'template_redirect', 'g5tech_i18n_start_buffer', 0 );

/**
 * Use the public locale for WordPress-provided front-end strings and dates.
 */
function g5tech_i18n_locale( $locale ) {
	if ( g5tech_polylang_ready() ) {
		return $locale;
	}

	if ( is_admin() || wp_doing_ajax() ) {
		return $locale;
	}

	return g5tech_languages()[ g5tech_current_language() ]['locale'];
}
add_filter( 'locale', 'g5tech_i18n_locale', 20 );

/**
 * Render an accessible language switcher.
 */
function g5tech_render_language_switcher( $attributes = array() ) {
	$location = isset( $attributes['location'] ) ? sanitize_html_class( $attributes['location'] ) : 'header';
	$current  = g5tech_current_language();
	$links    = array();

	foreach ( g5tech_languages() as $code => $language ) {
		if ( $current === $code ) {
			continue;
		}

		$switch_url = g5tech_language_url( $code, null, true );

		if ( g5tech_polylang_ready() && function_exists( 'pll_home_url' ) ) {
			$switch_url = pll_home_url( $code );

			if ( is_singular() && function_exists( 'pll_get_post' ) ) {
				$translation_id = pll_get_post( get_queried_object_id(), $code );

				if ( $translation_id && 'publish' === get_post_status( $translation_id ) ) {
					$switch_url = get_permalink( $translation_id );
				}
			}
		}

		$links[] = sprintf(
			'<a class="g5-language-switcher__link" href="%s" lang="%s" hreflang="%s">%s</a>',
			esc_url( $switch_url ),
			esc_attr( $code ),
			esc_attr( str_replace( '_', '-', g5tech_languages()[ $code ]['locale'] ) ),
			esc_html( $language['label'] )
		);
	}

	return sprintf(
		'<nav class="g5-language-switcher g5-language-switcher--%1$s" aria-label="%2$s"><details class="g5-language-switcher__details"><summary class="g5-language-switcher__current" lang="%3$s" aria-label="%2$s: %4$s"><span>%4$s</span><span class="g5-language-switcher__chevron" aria-hidden="true">⌄</span></summary><div class="g5-language-switcher__menu">%5$s</div></details></nav>',
		esc_attr( $location ),
		esc_attr( g5tech_t( 'Pasirinkite kalbą' ) ),
		esc_attr( $current ),
		esc_html( g5tech_languages()[ $current ]['label'] ),
		implode( '', $links )
	);
}

/**
 * Register the language-switcher block used by header and footer templates.
 */
function g5tech_register_language_switcher_block() {
	register_block_type(
		'g5tech/language-switcher',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH kalbų perjungiklis',
			'category'        => 'theme',
			'attributes'      => array(
				'location' => array(
					'type'    => 'string',
					'default' => 'header',
				),
			),
			'render_callback' => 'g5tech_render_language_switcher',
			'supports'        => array(
				'html'     => false,
				'inserter' => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_language_switcher_block', 5 );

/**
 * Load the switcher styles on every public page.
 */
function g5tech_i18n_assets() {
	$path = G5TECH_CORE_DIR . 'assets/language-switcher.css';
	$script_path = G5TECH_CORE_DIR . 'assets/language-switcher.js';
	wp_enqueue_style(
		'g5tech-language-switcher',
		G5TECH_CORE_URL . 'assets/language-switcher.css',
		array(),
		file_exists( $path ) ? (string) filemtime( $path ) : G5TECH_CORE_VERSION
	);
	wp_enqueue_script(
		'g5tech-language-switcher',
		G5TECH_CORE_URL . 'assets/language-switcher.js',
		array(),
		file_exists( $script_path ) ? (string) filemtime( $script_path ) : G5TECH_CORE_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'g5tech_i18n_assets', 30 );

/**
 * Add complete alternate-language signals for search engines.
 */
function g5tech_i18n_hreflang() {
	if ( g5tech_polylang_ready() ) {
		return;
	}

	if ( is_admin() || is_404() || is_search() ) {
		return;
	}

	foreach ( g5tech_languages() as $code => $language ) {
		printf(
			"<link rel=\"alternate\" hreflang=\"%s\" href=\"%s\">\n",
			esc_attr( str_replace( '_', '-', $language['locale'] ) ),
			esc_url( g5tech_language_url( $code ) )
		);
	}
	printf(
		"<link rel=\"alternate\" hreflang=\"x-default\" href=\"%s\">\n",
		esc_url( g5tech_language_url( 'lt' ) )
	);
}
add_action( 'wp_head', 'g5tech_i18n_hreflang', 4 );
