<?php
/**
 * Vienkartinės turinio migracijos.
 *
 * Šis failas laikinas: kiekviena migracija perkelia turinį iš kodo arba iš
 * opcijų į tikrus WordPress įrašus. Pabaigus perėjimą failą galima pašalinti
 * kartu su jo `require_once` eilute.
 *
 * @package 5gtech-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Privatumo politikos turinys blokų pavidalu.
 *
 * Rekvizitai ir el. paštas lieka dinaminiais blokais, kad nebūtų dubliuojami:
 * juos ir toliau valdo „5G TECH nustatymai".
 */
function g5tech_privacy_page_block_content() {
	return <<<'BLOCKS'
<!-- wp:g5tech/legal-hero {"title":"Privatumo politika","lead":"Kaip renkame, naudojame ir saugome jūsų asmens duomenis.","lock":{"move":true,"remove":true}} /-->

<!-- wp:group {"tagName":"article","className":"g5-section g5-grid-lines","templateLock":"contentOnly","layout":{"type":"default"}} -->
<article class="wp-block-group g5-section g5-grid-lines"><!-- wp:group {"className":"g5-container article","layout":{"type":"default"}} -->
<div class="wp-block-group g5-container article"><!-- wp:heading -->
<h2 class="wp-block-heading">Kas tvarko jūsų duomenis</h2>
<!-- /wp:heading -->

<!-- wp:g5tech/company-line /-->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kokius duomenis renkame</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Kontaktų formose pateiktus duomenis, užklausų turinį, kandidatavimo informaciją ir gyvenimo aprašymą.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kodėl juos naudojame</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Atsakyti į užklausas, vykdyti darbuotojų atranką ir administruoti svetainės veikimą.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kiek laiko saugome</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Užklausų duomenis saugome tiek, kiek būtina atsakymui ir bendradarbiavimui. Kandidatų duomenų saugojimo terminą nustatome pagal atrankos tikslą ir taikomus teisės aktus.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Jūsų teisės</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Turite teisę susipažinti su duomenimis, juos ištaisyti, ištrinti, apriboti jų tvarkymą ar pateikti skundą.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kontaktas</h2>
<!-- /wp:heading -->

<!-- wp:g5tech/contact-email {"intro":"Dėl privatumo klausimų rašykite"} /--></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
BLOCKS;
}

/**
 * Slapukų politikos turinys blokų pavidalu.
 */
function g5tech_cookies_page_block_content() {
	return <<<'BLOCKS'
<!-- wp:g5tech/legal-hero {"title":"Slapukų politika","lead":"Kaip svetainėje naudojami slapukai ir kaip galite valdyti savo pasirinkimus.","lock":{"move":true,"remove":true}} /-->

<!-- wp:group {"tagName":"article","className":"g5-section g5-grid-lines","templateLock":"contentOnly","layout":{"type":"default"}} -->
<article class="wp-block-group g5-section g5-grid-lines"><!-- wp:group {"className":"g5-container article","layout":{"type":"default"}} -->
<div class="wp-block-group g5-container article"><!-- wp:heading -->
<h2 class="wp-block-heading">Kas yra slapukai</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Slapukai yra nedideli failai, išsaugomi jūsų įrenginyje lankantis svetainėje.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Būtinieji slapukai</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Jie reikalingi pagrindiniam svetainės veikimui, saugumui ir jūsų pasirinkimų išsaugojimui.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Analitiniai slapukai</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Juos naudojame tik gavę jūsų sutikimą. Šie slapukai padeda suprasti, kaip lankytojai naudojasi svetaine.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kaip pakeisti pasirinkimą</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Slapukų nustatymus galite pakeisti sutikimų valdymo lange arba savo naršyklės nustatymuose.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Kontaktas</h2>
<!-- /wp:heading -->

<!-- wp:g5tech/contact-email {"intro":"Dėl slapukų naudojimo rašykite"} /--></div>
<!-- /wp:group --></article>
<!-- /wp:group -->
BLOCKS;
}

/**
 * Perkelia teisinius puslapius iš kietai užkoduoto PHP į redaguojamus blokus.
 *
 * Migracija idempotentinė: dirba tik tada, kai puslapyje dar yra senasis
 * vientisas blokas. `wp_update_post` sukuria revziją, todėl grįžti galima
 * įprastu WordPress būdu.
 */
function g5tech_migrate_legal_pages_to_blocks() {
	if ( get_option( 'g5tech_legal_pages_blocks_migrated' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$pages = array(
		'privatumo-politika' => array(
			'legacy'  => 'g5tech/privacy-page',
			'content' => g5tech_privacy_page_block_content(),
		),
		'slapukai'           => array(
			'legacy'  => 'g5tech/cookies-page',
			'content' => g5tech_cookies_page_block_content(),
		),
	);

	foreach ( $pages as $slug => $definition ) {
		$page = get_page_by_path( $slug );

		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		if ( ! has_block( $definition['legacy'], $page->post_content ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $definition['content'],
			)
		);
	}

	update_option( 'g5tech_legal_pages_blocks_migrated', 1, false );
}
add_action( 'admin_init', 'g5tech_migrate_legal_pages_to_blocks', 40 );

/* -------------------------------------------------------------------------
 * Puslapių sekcijų perkėlimas į post_content blokus.
 * ---------------------------------------------------------------------- */

/**
 * Saugiai suformuoja bloko komentarą su atributais.
 */
function g5tech_block_markup( $name, $attributes = array(), $inner = null ) {
	$attributes = array_filter(
		$attributes,
		static function ( $value ) {
			return '' !== $value && null !== $value && false !== $value;
		}
	);

	$json = $attributes
		? ' ' . wp_json_encode( $attributes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
		: '';

	if ( null === $inner ) {
		return '<!-- wp:' . $name . $json . ' /-->';
	}

	return '<!-- wp:' . $name . $json . ' -->' . "\n" . $inner . "\n" . '<!-- /wp:' . $name . ' -->';
}

/**
 * Kortelių tinklelis iš kartotinių elementų eilučių.
 */
function g5tech_cards_block_markup( $rows, $title_key = 'title', $text_key = 'text' ) {
	$cards = array();

	foreach ( (array) $rows as $row ) {
		$cards[] = g5tech_block_markup(
			'g5tech/card',
			array(
				'title' => (string) ( $row[ $title_key ] ?? '' ),
				'text'  => (string) ( $row[ $text_key ] ?? '' ),
			)
		);
	}

	return g5tech_block_markup( 'g5tech/card-grid', array(), implode( "\n", $cards ) );
}

/**
 * Vadovų puslapio turinys blokais.
 */
function g5tech_leaders_page_block_content() {
	$content = g5tech_structured_section( 'leaders' );

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'     => 'Informacija įmonių vadovams',
					'title'       => 'Projektų vykdymas su aiškiomis atsakomybėmis.',
					'lead'        => 'Telekomunikacijų, energetikos ir inžinerinių sistemų projektai Lietuvoje bei kitose Europos šalyse.',
					'buttonLabel' => 'Susisiekti su vadovu',
					'buttonUrl'   => '/kontaktai/',
					'lock'        => array(
						'move'   => true,
						'remove' => true,
					),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['reasons_eyebrow'],
					'title'    => (string) $content['reasons_title'],
					'theme'    => 'paper',
					'anchorId' => 'reasons-title',
				),
				g5tech_cards_block_markup( $content['reasons'] )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Tiesioginis kontaktas',
					'title'       => 'Aptarkime projekto apimtį, rizikas ir atsakomybes.',
					'body'        => 'Aleksandras Iljinas · generalinis direktorius',
					'buttonLabel' => 'Susisiekti',
					'buttonUrl'   => '/kontaktai/',
					'lock'        => array(
						'move'   => true,
						'remove' => true,
					),
				)
			),
		)
	);
}

/**
 * Perkelia puslapį į blokus ir atriša jį nuo modulių bibliotekos.
 */
function g5tech_migrate_page_to_blocks( $slug, $content, $page_key = '' ) {
	$page = get_page_by_path( $slug );

	if ( ! $page instanceof WP_Post || '' === trim( $content ) ) {
		return false;
	}

	wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => $content,
		)
	);

	if ( $page_key && function_exists( 'g5tech_module_placements' ) ) {
		$placements = g5tech_module_placements();

		if ( ! empty( $placements[ $page_key ] ) ) {
			$placements[ $page_key ] = array();
			update_option( 'g5tech_module_placements', $placements, false );
		}
	}

	return true;
}


/**
 * Bendras sąrašo blokų generatorius.
 */
function g5tech_list_block_markup( $rows, $wrapper, $item, $keys ) {
	$out = array();

	foreach ( (array) $rows as $row ) {
		$attributes = array();

		foreach ( $keys as $attr => $key ) {
			$attributes[ $attr ] = (string) ( $row[ $key ] ?? '' );
		}

		$out[] = g5tech_block_markup( $item, $attributes );
	}

	return g5tech_block_markup( $wrapper, array(), implode( "\n", $out ) );
}

/**
 * Academy puslapio turinys blokais.
 */
function g5tech_academy_page_block_content() {
	$content = g5tech_structured_section( 'academy' );

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'     => '5GTECH Academy',
					'title'       => 'Praktinis pasirengimas darbui telekomunikacijų projektuose.',
					'lead'        => 'Programa norintiems įgyti techninių pagrindų ir išbandyti darbą mobiliojo ryšio infrastruktūros projekte.',
					'buttonLabel' => 'Kandidatuoti',
					'buttonUrl'   => '/kandidatuoti/',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['program_eyebrow'],
					'title'    => (string) $content['program_title'],
					'theme'    => 'dark',
					'anchorId' => 'program-title',
				),
				g5tech_list_block_markup( $content['program'], 'g5tech/steps', 'g5tech/step', array( 'title' => 'title', 'text' => 'text' ) )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['included_eyebrow'],
					'title'    => (string) $content['included_title'],
					'theme'    => 'light',
					'anchorId' => 'included-title',
				),
				g5tech_list_block_markup( $content['included'], 'g5tech/check-list', 'g5tech/check-item', array( 'text' => 'text' ) )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Pradėkite',
					'title'       => 'Norite pradėti karjerą telekomunikacijose?',
					'body'        => 'Techninė patirtis – privalumas. Svarbiausia atsakingas požiūris ir noras mokytis.',
					'buttonLabel' => 'Pildyti formą',
					'buttonUrl'   => '/kandidatuoti/',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}


/**
 * Projektų vadovų puslapio turinys blokais.
 */
function g5tech_project_managers_page_block_content() {
	$content = g5tech_structured_section( 'project_managers' );
	$ids     = array_values( array_filter( array_map( 'absint', (array) ( $content['equipment_ids'] ?? array() ) ) ) );
	$names   = array();

	foreach ( g5tech_get_partners( '', $ids ) as $partner ) {
		$names[] = get_the_title( $partner );
	}

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'     => 'Projektų vadovams ir techniniam personalui',
					'title'       => 'Techninė projekto eiga ir dokumentacija.',
					'lead'        => 'Dirbame pagal užsakovo techninius standartus ir tiesiogiai informuojame apie darbų eigą, neatitikimus bei dokumentaciją.',
					'buttonLabel' => 'Atsiųsti techninę užduotį',
					'buttonUrl'   => '/kontaktai/',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['scope_eyebrow'],
					'title'    => (string) $content['scope_title'],
					'theme'    => 'light',
					'anchorId' => 'technical-title',
				),
				g5tech_list_block_markup( $content['tasks'], 'g5tech/check-list', 'g5tech/check-item', array( 'text' => 'text' ) )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['equipment_eyebrow'],
					'title'    => (string) $content['equipment_title'],
					'theme'    => 'dark',
					'anchorId' => 'equipment-title',
				),
				g5tech_block_markup( 'g5tech/partner-stats', array( 'partnerIds' => $ids, 'names' => $names ) )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Techninė užduotis',
					'title'       => 'Atsiųskite turimą dokumentaciją arba darbų apimtį.',
					'buttonLabel' => 'Susisiekti',
					'buttonUrl'   => '/kontaktai/',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}


/**
 * Mokymų puslapio turinys blokais.
 */
function g5tech_training_page_block_content() {
	$content = g5tech_training_page_content();
	$topics  = g5tech_training_page_lines( 'topics' );
	$ids     = g5tech_sanitize_partner_ids( $content['equipment_ids'] ?? array() );
	$names   = array();

	foreach ( g5tech_get_partners( '', $ids ) as $partner ) {
		$names[] = get_the_title( $partner );
	}

	$topic_rows = array();

	foreach ( $topics as $topic ) {
		$topic_rows[] = array( 'text' => $topic );
	}

	$image_id  = absint( $content['image_id'] ?? 0 );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	$sections = array(
		'topics'    => g5tech_block_markup(
			'g5tech/section',
			array(
				'eyebrow'  => (string) $content['topics_eyebrow'],
				'title'    => (string) $content['topics_title'],
				'theme'    => 'light',
				'anchorId' => 'topics-title',
			),
			g5tech_list_block_markup( $topic_rows, 'g5tech/check-list', 'g5tech/check-item', array( 'text' => 'text' ) )
		),
		'equipment' => g5tech_block_markup(
			'g5tech/section',
			array(
				'eyebrow'  => (string) $content['equipment_eyebrow'],
				'title'    => (string) $content['equipment_title'],
				'theme'    => 'paper',
				'anchorId' => 'equipment-title',
			),
			g5tech_block_markup( 'g5tech/equipment-logos', array( 'partnerIds' => $ids, 'names' => $names ) )
		),
		'image'     => g5tech_block_markup(
			'g5tech/section',
			array(
				'eyebrow'  => (string) $content['image_eyebrow'],
				'title'    => (string) $content['image_title'],
				'theme'    => 'dark',
				'anchorId' => 'training-room-title',
			),
			g5tech_block_markup(
				'g5tech/media-frame',
				array(
					'imageId'       => $image_id,
					'imageUrl'      => $image_url,
					'alt'           => (string) ( $content['image_alt'] ?? '' ),
					'ratio'         => '16 / 8',
					'themeFallback' => 'assets/images/generated/training-technical-lab-v1.jpg',
				)
			)
		),
	);

	$order  = g5tech_sanitize_training_section_order( $content['section_order'] ?? array() );
	$blocks = array(
		g5tech_block_markup(
			'g5tech/page-hero',
			array(
				'eyebrow' => (string) $content['hero_eyebrow'],
				'title'   => (string) $content['hero_title'],
				'lead'    => (string) $content['hero_lead'],
				'lock'    => array( 'move' => true, 'remove' => true ),
			)
		),
	);

	foreach ( $order as $key ) {
		if ( isset( $sections[ $key ] ) ) {
			$blocks[] = $sections[ $key ];
		}
	}

	$cta_url = (string) ( $content['cta_button_url'] ?? '' );
	$home    = home_url();

	if ( $home && 0 === strpos( $cta_url, $home ) ) {
		$cta_url = substr( $cta_url, strlen( $home ) );
	}

	$blocks[] = g5tech_block_markup(
		'g5tech/page-cta',
		array(
			'eyebrow'     => (string) $content['cta_eyebrow'],
			'title'       => (string) $content['cta_title'],
			'buttonLabel' => (string) $content['cta_button_label'],
			'buttonUrl'   => $cta_url ?: '/kontaktai/',
			'lock'        => array( 'move' => true, 'remove' => true ),
		)
	);

	return implode( "\n\n", $blocks );
}

function g5tech_migrate_content_pages_to_blocks() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	// Kiekvienas puslapis tikrinamas atskirai pagal tai, ar jame dar yra
	// senasis vientisas blokas. Bendra vėliava netinka: pridėjus naują
	// puslapį ji jau būtų pažymėta ir naujas puslapis liktų neperkeltas.
	$pages = array(
		'vadovams'          => array( 'legacy' => 'g5tech/leaders-page',          'page_key' => 'leaders',          'builder' => 'g5tech_leaders_page_block_content' ),
		'akademija'         => array( 'legacy' => 'g5tech/academy-page',          'page_key' => 'academy',          'builder' => 'g5tech_academy_page_block_content' ),
		'projektu-vadovams' => array( 'legacy' => 'g5tech/project-managers-page', 'page_key' => 'project_managers', 'builder' => 'g5tech_project_managers_page_block_content' ),
		'mokymai'           => array( 'legacy' => 'g5tech/training-page',         'page_key' => 'training',         'builder' => 'g5tech_training_page_block_content' ),
	);

	foreach ( $pages as $slug => $definition ) {
		$page = get_page_by_path( $slug );

		if ( ! $page instanceof WP_Post ) {
			continue;
		}

		// Jau perkeltas – nieko nedarome, kad nebūtų perrašytas redaktoriaus darbas.
		if ( ! has_block( $definition['legacy'], $page->post_content ) ) {
			continue;
		}

		if ( ! function_exists( $definition['builder'] ) ) {
			continue;
		}

		g5tech_migrate_page_to_blocks( $slug, call_user_func( $definition['builder'] ), $definition['page_key'] );
	}
}
add_action( 'admin_init', 'g5tech_migrate_content_pages_to_blocks', 41 );
