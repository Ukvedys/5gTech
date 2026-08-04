<?php
/**
 * Bendros Polylang diegimo funkcijos (naudoja setup-polylang.php ir deploy/sync-content.php).
 */

function g5pll_ensure_languages() {
	$model = PLL()->model;
	$langs = array(
		array( 'name' => 'Lietuvių', 'slug' => 'lt', 'locale' => 'lt_LT', 'rtl' => 0, 'term_group' => 0, 'flag' => 'lt' ),
		array( 'name' => 'English',  'slug' => 'en', 'locale' => 'en_US', 'rtl' => 0, 'term_group' => 1, 'flag' => 'us' ),
		array( 'name' => 'Deutsch',  'slug' => 'de', 'locale' => 'de_DE', 'rtl' => 0, 'term_group' => 2, 'flag' => 'de' ),
	);
	foreach ( $langs as $l ) {
		if ( ! $model->get_language( $l['slug'] ) ) {
			$model->add_language( $l );
			echo 'kalba ' . $l['slug'] . " sukurta\n";
		}
	}
	if ( method_exists( $model, 'update_default_lang' ) ) $model->update_default_lang( 'lt' );
}

function g5pll_apply_options() {
	$o = PLL()->options;
	$o['force_lang']    = 1;
	$o['hide_default']  = 1;
	$o['redirect_lang'] = 1; // kalbos šaknis (/en/) yra pradinis puslapis
	$o['browser']       = 1;
	$o['rewrite']       = 1;
	$o['media_support'] = 0;
	$o['post_types']    = array( 'g5_service', 'g5_project', 'g5_job', 'g5_team' );
	if ( method_exists( $o, 'save_all' ) ) $o->save_all();
	delete_transient( 'pll_languages_list' );
	echo "Polylang nustatymai įrašyti\n";
}

function g5pll_page_slugs() {
	return array(
		'pagrindinis'        => array( 'home', 'startseite' ),
		'paslaugos'          => array( 'services', 'leistungen' ),
		'projektai'          => array( 'projects', 'projekte' ),
		'patirtis'           => array( 'experience', 'erfahrung' ),
		'apie-mus'           => array( 'about-us', 'ueber-uns' ),
		'karjera'            => array( 'careers', 'karriere' ),
		'akademija'          => array( 'academy', 'akademie' ),
		'mokymai'            => array( 'training', 'schulungen' ),
		'duk'                => array( 'candidate-faq', 'faq-fuer-bewerber' ),
		'vadovams'           => array( 'for-executives', 'fuer-entscheider' ),
		'projektu-vadovams'  => array( 'for-project-managers', 'fuer-projektleiter' ),
		'kontaktai'          => array( 'contact', 'kontakt' ),
		'naujienos'          => array( 'news', 'aktuelles' ),
		'kandidatuoti'       => array( 'apply', 'bewerben' ),
		'privatumo-politika' => array( 'privacy-policy', 'datenschutz' ),
		'slapukai'           => array( 'cookies', 'cookies' ),
	);
}

function g5pll_single_slugs() {
	return array(
		'mobiliojo-rysio-tinklai'                            => array( 'mobile-networks', 'mobilfunknetze' ),
		'vidinio-rysio-tinklai'                              => array( 'in-building-networks', 'indoor-mobilfunk' ),
		'fiksuoto-rysio-tinklai'                             => array( 'fixed-networks', 'festnetze' ),
		'elektros-darbai'                                    => array( 'electrical-installations', 'elektroinstallationen' ),
		'apsaugos-ir-stebejimo-sistemos'                     => array( 'security-and-surveillance-systems', 'sicherheits-und-ueberwachungssysteme' ),
		'saules-elektrines'                                  => array( 'solar-power-systems', 'photovoltaikanlagen' ),
		'baziniu-stociu-modernizavimas-vokietijoje'          => array( 'mobile-network-modernisation-germany', 'mobilfunk-modernisierung-deutschland' ),
		'telekomunikaciju-specialistas'                      => array( 'telecommunications-technician', 'telekommunikationstechniker' ),
		'baziniu-stociu-modernizavimo-projektas-vokietijoje' => array( 'mobile-network-modernisation-project-germany', 'mobilfunk-modernisierungsprojekt-deutschland' ),
		'praktiniai-darbuotoju-mokymai'                      => array( 'practical-employee-training', 'praxisnahe-mitarbeiterschulungen' ),
		'projektai-sesiose-europos-salyse'                   => array( 'projects-in-six-european-countries', 'projekte-in-sechs-europaeischen-laendern' ),
	);
}

function g5pll_t( $text, $lang ) {
	$text = (string) $text;
	if ( '' === trim( $text ) ) return $text;
	$out = g5tech_t( $text, $lang );
	if ( $out !== $text ) return $out;
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	if ( count( $lines ) > 1 ) {
		return implode( "\n", array_map( static fn( $l ) => g5tech_t( $l, $lang ), $lines ) );
	}
	return $text;
}

function g5pll_html( $html, $lang ) {
	$prev = $GLOBALS['g5tech_language'] ?? 'lt';
	$GLOBALS['g5tech_language'] = $lang;
	$out = g5tech_translate_frontend_html( (string) $html );
	$GLOBALS['g5tech_language'] = $prev;
	return $out;
}

function g5pll_attrs( $attrs, $lang ) {
	foreach ( $attrs as $k => $v ) {
		if ( is_array( $v ) ) {
			$attrs[ $k ] = g5pll_attrs( $v, $lang );
		} elseif ( is_string( $v ) ) {
			if ( preg_match( '/Url$|^url$/', (string) $k ) && '' !== $v && '/' === $v[0] ) {
				$attrs[ $k ] = g5tech_i18n_localized_path( g5tech_normalize_i18n_path( $v ), $lang );
			} else {
				$attrs[ $k ] = g5pll_t( $v, $lang );
			}
		}
	}
	return $attrs;
}

function g5pll_blocks( $blocks, $lang ) {
	foreach ( $blocks as &$b ) {
		if ( ! empty( $b['attrs'] ) ) $b['attrs'] = g5pll_attrs( $b['attrs'], $lang );
		if ( ! empty( $b['innerBlocks'] ) ) $b['innerBlocks'] = g5pll_blocks( $b['innerBlocks'], $lang );
		if ( ! empty( $b['innerHTML'] ) ) $b['innerHTML'] = g5pll_html( $b['innerHTML'], $lang );
		if ( ! empty( $b['innerContent'] ) ) {
			foreach ( $b['innerContent'] as $i => $chunk ) {
				if ( is_string( $chunk ) && '' !== trim( $chunk ) ) $b['innerContent'][ $i ] = g5pll_html( $chunk, $lang );
			}
		}
	}
	return $blocks;
}

function g5pll_content( $content, $lang ) {
	if ( '' === trim( (string) $content ) ) return (string) $content;
	return serialize_blocks( g5pll_blocks( parse_blocks( (string) $content ), $lang ) );
}

function g5pll_generate_copies( $only_source_slugs = array() ) {
	$page_slugs   = g5pll_page_slugs();
	$single_slugs = g5pll_single_slugs();
	$only_source_slugs = array_fill_keys( array_map( 'sanitize_title', (array) $only_source_slugs ), true );
	$types = array( 'page', 'post', 'g5_service', 'g5_project', 'g5_job', 'g5_team' );
	$made  = 0;

	foreach ( $types as $type ) {
		$items = get_posts( array( 'post_type' => $type, 'post_status' => array( 'publish', 'draft' ), 'posts_per_page' => -1, 'orderby' => 'ID', 'order' => 'ASC', 'lang' => '' ) );

		foreach ( $items as $item ) {
			$item_lang = pll_get_post_language( $item->ID );
			if ( $item_lang && 'lt' !== $item_lang ) continue;
			if ( $only_source_slugs && ! isset( $only_source_slugs[ $item->post_name ] ) ) continue;
			pll_set_post_language( $item->ID, 'lt' );

			$translations = array( 'lt' => $item->ID );

			foreach ( array( 'en', 'de' ) as $lang ) {
				$li   = 'en' === $lang ? 0 : 1;
				$slug = $item->post_name;
				if ( 'page' === $type && isset( $page_slugs[ $slug ] ) ) $slug = $page_slugs[ $slug ][ $li ];
				elseif ( isset( $single_slugs[ $slug ] ) ) $slug = $single_slugs[ $slug ][ $li ];
				elseif ( 'g5_team' !== $type ) $slug = $item->post_name . '-' . $lang;

				$payload = array(
					'post_type'    => $type,
					'post_status'  => $item->post_status,
					'post_name'    => $slug,
					'post_title'   => 'g5_team' === $type ? $item->post_title : g5pll_t( $item->post_title, $lang ),
					'post_content' => g5pll_content( $item->post_content, $lang ),
					'post_excerpt' => g5pll_t( $item->post_excerpt, $lang ),
					'menu_order'   => $item->menu_order,
				);

				$existing = pll_get_post( $item->ID, $lang );
				if ( $existing ) { $payload['ID'] = $existing; $copy_id = wp_update_post( wp_slash( $payload ), true ); }
				else { $copy_id = wp_insert_post( wp_slash( $payload ), true ); }
				if ( is_wp_error( $copy_id ) ) { echo "KLAIDA $type/$slug: " . $copy_id->get_error_message() . "\n"; continue; }

				foreach ( get_post_meta( $item->ID ) as $key => $values ) {
					if ( ! preg_match( '/^(g5_|_g5tech_|_wp_page_template$)/', $key ) ) continue;
					delete_post_meta( $copy_id, $key );
					foreach ( $values as $value ) {
						$value = maybe_unserialize( $value );
						if ( is_string( $value ) ) $value = g5pll_t( $value, $lang );
						add_post_meta( $copy_id, $key, is_string( $value ) ? wp_slash( $value ) : $value );
					}
				}

				if ( 'page' === $type && 'pagrindinis' !== $item->post_name ) {
					$tpl = 'page-' . $item->post_name;
					if ( file_exists( get_theme_file_path( 'templates/' . $tpl . '.html' ) ) ) {
						update_post_meta( $copy_id, '_wp_page_template', $tpl );
					}
				}

				$thumb = get_post_thumbnail_id( $item->ID );
				if ( $thumb ) set_post_thumbnail( $copy_id, $thumb );

				pll_set_post_language( $copy_id, $lang );
				$translations[ $lang ] = $copy_id;
				$made++;
			}

			pll_save_post_translations( $translations );
		}
	}

	echo "Kopijų sukurta/atnaujinta: $made\n";
}
