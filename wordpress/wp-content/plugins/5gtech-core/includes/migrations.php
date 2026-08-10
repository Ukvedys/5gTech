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
	$decision_cards = array(
		array(
			'title' => 'Viena atsakinga komanda',
			'text'  => 'Suderiname techninę užduotį, suplanuojame darbus, vykdome montavimą, testuojame ir parengiame perdavimo dokumentaciją.',
		),
		array(
			'title' => 'Patikrinta darbų tvarka',
			'text'  => 'Kokybės, aplinkosaugos ir darbų saugos procesus pagrindžia ISO 9001, ISO 14001, ISO 45001 ir SSVA kvalifikacija.',
		),
		array(
			'title' => 'Patirtis sudėtinguose objektuose',
			'text'  => 'Komandos patirtis apima daugiau kaip 6000 bazinių stočių, 2G–5G technologijas ir projektus šešiose Europos šalyse.',
		),
		array(
			'title' => 'Aiški komunikacija',
			'text'  => 'Užsakovas žino, kas atsako už projektą, kokia darbų eiga, kokie neatitikimai nustatyti ir kokių sprendimų reikia.',
		),
	);
	$decision_links = array(
		g5tech_block_markup(
			'g5tech/link-card',
			array(
				'label'    => 'Paslaugos',
				'title'    => 'Peržiūrėkite darbų kryptis ir apimtį.',
				'linkText' => 'Paslaugų sąrašas →',
				'url'      => '/paslaugos/',
			)
		),
		g5tech_block_markup(
			'g5tech/link-card',
			array(
				'label'    => 'Patirtis',
				'title'    => 'Patikrinkite geografiją, technologijas ir kvalifikacijas.',
				'linkText' => 'Patirties faktai →',
				'url'      => '/patirtis/',
			)
		),
		g5tech_block_markup(
			'g5tech/link-card',
			array(
				'label'    => 'Techninė apimtis',
				'title'    => 'Perduokite techninį vertinimą projekto komandai.',
				'linkText' => 'Projektų vadovams →',
				'url'      => '/projektu-vadovams/',
			)
		),
	);
	$brief_items = array(
		array( 'text' => 'Techninė užduotis arba preliminari darbų apimtis' ),
		array( 'text' => 'Objekto vieta, esama būklė ir prieigos sąlygos' ),
		array( 'text' => 'Pageidaujamas darbų terminas arba projekto grafikas' ),
		array( 'text' => 'Užsakovo dokumentacijos ir darbų saugos reikalavimai' ),
	);

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'     => 'Partnerystė generaliniams rangovams ir operatoriams',
					'title'       => 'Vienas techninis partneris nuo užduoties iki dokumentuoto perdavimo.',
					'lead'        => 'Mobiliojo ir fiksuoto ryšio, elektros, apsaugos bei saulės energetikos darbus vykdome Lietuvoje ir kitose Europos šalyse.',
					'buttonLabel' => 'Aptarti projektą',
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
					'eyebrow'  => 'Užsakovo kontrolė',
					'title'    => 'Mažiau rangovų sąsajų, aiškesnė atsakomybė.',
					'lead'     => 'Sutartą darbų apimtį koordinuoja viena komanda, o sprendimai, patikros ir perdavimas lieka atsekami.',
					'theme'    => 'paper',
					'anchorId' => 'reasons-title',
				),
				g5tech_cards_block_markup( $decision_cards )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Įrodymai prieš pažadus',
					'title'    => 'Patirtis, kurią galima patikrinti.',
					'theme'    => 'dark',
					'anchorId' => 'proof-title',
				),
				implode(
					"\n",
					array(
						g5tech_block_markup( 'g5tech/stats-band' ),
						g5tech_block_markup(
							'g5tech/media-frame',
							array(
								'alt'           => 'Mobiliojo ryšio bokštas kalnuotoje vietovėje',
								'ratio'         => '16 / 6',
								'themeFallback' => 'assets/images/from-live-site/services-header.jpg',
							)
						),
					)
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Informacija sprendimui',
					'title'    => 'Patikrinkite tai, kas svarbu prieš pasirenkant partnerį.',
					'theme'    => 'light',
					'anchorId' => 'decision-links-title',
				),
				g5tech_block_markup( 'g5tech/card-grid', array(), implode( "\n", $decision_links ) )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Pirmasis įvertinimas',
					'title'    => 'Ko reikia pokalbio pradžiai.',
					'lead'     => 'Jei dalies informacijos dar neturite, pradėkime nuo turimos projekto medžiagos.',
					'theme'    => 'paper',
					'anchorId' => 'brief-title',
				),
				g5tech_list_block_markup( $brief_items, 'g5tech/check-list', 'g5tech/check-item', array( 'text' => 'text' ) )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Tiesioginis kontaktas',
					'title'       => 'Aptarkime projekto apimtį ir atsakomybes.',
					'body'        => 'Aleksandras Iljinas · generalinis direktorius',
					'buttonLabel' => 'Aptarti projektą',
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

	$scope_cards = array(
		array(
			'title' => 'Objekto įvertinimas',
			'text'  => 'Atliekame „Site Survey“, įvertiname esamą infrastruktūrą, prieigą, darbų saugos sąlygas ir techninius apribojimus.',
		),
		array(
			'title' => 'Darbų planas',
			'text'  => 'Suderiname darbų seką, terminus, medžiagas, komandos poreikį ir atsakomybių ribas.',
		),
		array(
			'title' => 'Montavimas ir patikra',
			'text'  => 'Montuojame ir konfigūruojame įrangą, atliekame matavimus bei valdome neatitikimus iki patvirtinto rezultato.',
		),
		array(
			'title' => 'Dokumentuotas perdavimas',
			'text'  => 'Parengiame sutarto formato dokumentaciją, užfiksuojame bandymų rezultatus ir perduodame užbaigtą darbų apimtį.',
		),
	);
	$brief_items = array(
		array( 'text' => 'Techninė užduotis, brėžiniai arba darbų apimties žiniaraštis' ),
		array( 'text' => 'Objekto adresas, prieigos ir darbų aukštyje sąlygos' ),
		array( 'text' => 'Naudojama įranga ir užsakovo techniniai standartai' ),
		array( 'text' => 'Reikalingi matavimai, ataskaitos ir perdavimo dokumentai' ),
		array( 'text' => 'Pageidaujamas darbų grafikas ir svarbiausi terminai' ),
	);

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'     => 'Projektų vadovams ir techninei komandai',
					'title'       => 'Aiški techninė eiga nuo „Site Survey“ iki dokumentacijos.',
					'lead'        => 'Suplanuojame darbus, montuojame, konfigūruojame ir testuojame įrangą, valdome neatitikimus bei ruošiame dokumentaciją pagal užsakovo standartą.',
					'buttonLabel' => 'Atsiųsti techninę užduotį',
					'buttonUrl'   => '/kontaktai/',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Ką perimame',
					'title'    => 'Keturi aiškūs atsakomybės blokai.',
					'lead'     => 'Galime perimti visą sutartą ciklą arba prisijungti prie konkretaus projekto etapo.',
					'theme'    => 'light',
					'anchorId' => 'scope-title',
				),
				g5tech_cards_block_markup( $scope_cards )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Techninė bazė',
					'title'    => 'Įrangą ir darbo veiksmus tikriname dar prieš objektą.',
					'lead'     => '5G TECH mokymų salėje komanda dirba su realia ryšio įranga, konstrukcijomis ir darbų aukštyje saugos priemonėmis.',
					'theme'    => 'dark',
					'anchorId' => 'technical-base-title',
				),
				g5tech_block_markup(
					'g5tech/media-frame',
					array(
						'alt'           => '5G TECH praktinių mokymų salė su mobiliojo ryšio įranga',
						'ratio'         => '16 / 8',
						'themeFallback' => 'assets/images/from-live-site/training-room-wide.jpg',
					)
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['scope_eyebrow'],
					'title'    => (string) $content['scope_title'],
					'theme'    => 'paper',
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
				'g5tech/section',
				array(
					'eyebrow'  => 'Pirmasis įvertinimas',
					'title'    => 'Ką atsiųsti, kad galėtume įvertinti darbus.',
					'theme'    => 'light',
					'anchorId' => 'brief-title',
				),
				g5tech_list_block_markup( $brief_items, 'g5tech/check-list', 'g5tech/check-item', array( 'text' => 'text' ) )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Techninė užduotis',
					'title'       => 'Atsiųskite turimą dokumentaciją arba darbų apimtį.',
					'body'        => 'Peržiūrėsime medžiagą ir susisieksime dėl trūkstamų duomenų bei tolesnių veiksmų.',
					'buttonLabel' => 'Aptarti techninę užduotį',
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
					'themeFallback' => 'assets/images/from-live-site/training-room-wide.jpg',
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


/**
 * Naujienų puslapio turinys blokais.
 */
function g5tech_news_page_block_content() {
	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow' => 'Naujienos',
					'title'   => 'Projektai, komanda ir techninės įžvalgos.',
					'compact' => true,
					'lock'    => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Naujausia',
					'title'    => 'Naujausi įrašai.',
					'theme'    => 'paper',
					'anchorId' => 'news-title',
				),
				g5tech_block_markup( 'g5tech/news-grid' )
			),
		)
	);
}

/**
 * Kandidatų DUK puslapio turinys blokais.
 */
function g5tech_candidate_faq_page_block_content() {
	$sections = array(
		g5tech_block_markup(
			'g5tech/page-hero',
			array(
				'eyebrow' => 'DUK kandidatams',
				'title'   => 'Dažniausi klausimai apie darbą 5G TECH.',
				'compact' => true,
				'lock'    => array( 'move' => true, 'remove' => true ),
			)
		),
	);

	$groups = array(
		'start'  => array( 'Darbo pradžia', 'Kandidatavimas ir pasirengimas.' ),
		'travel' => array( 'Komandiruotės', 'Darbas Europos projektuose.' ),
		'safety' => array( 'Sauga ir priemonės', 'Darbas aukštyje ir sauga.' ),
		'daily'  => array( 'Kasdienis darbas', 'Kasdienė darbo eiga.' ),
	);

	$index = 0;

	foreach ( $groups as $group => $labels ) {
		$sections[] = g5tech_block_markup(
			'g5tech/section',
			array(
				'eyebrow'  => $labels[0],
				'title'    => $labels[1],
				'theme'    => 1 === $index % 2 ? 'paper' : 'light',
				'anchorId' => $group . '-title',
			),
			g5tech_block_markup( 'g5tech/faq-group', array( 'group' => $group ) )
		);
		$index++;
	}

	// CTA tekstas imamas iš nustatymų (karjeros el. paštas), todėl čia
	// nurodomas tik nustatymo raktas, o ne pati reikšmė.
	$sections[] = g5tech_block_markup(
		'g5tech/page-cta',
		array(
			'eyebrow'     => 'Neradote atsakymo?',
			'title'       => 'Susisiekite su personalo komanda.',
			'bodySetting' => 'career_email',
			'buttonLabel' => 'Kandidatuoti',
			'buttonUrl'   => '/kandidatuoti/',
			'lock'        => array( 'move' => true, 'remove' => true ),
		)
	);

	return implode( "\n\n", $sections );
}

/**
 * Kontaktų puslapio turinys blokais.
 */
function g5tech_contact_page_block_content() {
	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow' => 'Kontaktai',
					'title'   => 'Aptarkime jūsų projektą.',
					'lead'    => 'Aprašykite užduotį arba susisiekite tiesiogiai – atsakysime ir nukreipsime pas tinkamą specialistą.',
					'compact' => true,
					'lock'    => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Parašykite',
					'title'    => 'Projekto užklausa.',
					'anchorId' => 'contact-title',
				),
				g5tech_block_markup( 'g5tech/contact-form-split' )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Komanda',
					'title'    => 'Tiesioginiai kontaktai.',
					'theme'    => 'paper',
					'anchorId' => 'people-title',
				),
				g5tech_block_markup( 'g5tech/contact-people' )
			),
		)
	);
}

/**
 * Kandidatavimo puslapio turinys blokais.
 */
function g5tech_application_page_block_content() {
	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow' => 'Kandidatavimas',
					'title'   => 'Pateikite kandidatūrą.',
					'lead'    => 'Įkelkite CV ir nurodykite dominančią poziciją. Jei tinkamos pozicijos šiuo metu nėra, galėsime susisiekti vėliau.',
					'compact' => true,
					'lock'    => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Kandidatavimas',
					'title'    => 'Kontaktai ir darbo patirtis.',
					'lead'     => 'Žvaigždute pažymėti laukai yra privalomi.',
					'anchorId' => 'form-title',
				),
				g5tech_block_markup( 'g5tech/application-form' )
			),
		)
	);
}

/**
 * Karjeros puslapio turinys blokais. Tekstai imami iš esamos turinio opcijos.
 */
function g5tech_career_page_block_content() {
	if ( ! function_exists( 'g5tech_career_page_content' ) ) {
		return '';
	}

	$content = g5tech_career_page_content();

	$growth_cards = array();

	foreach ( (array) $content['growth_cards'] as $card ) {
		$growth_cards[] = g5tech_block_markup(
			'g5tech/link-card',
			array(
				'label'    => (string) ( $card['label'] ?? '' ),
				'title'    => (string) ( $card['title'] ?? '' ),
				'linkText' => (string) ( $card['link'] ?? '' ),
				'url'      => (string) ( $card['url'] ?? '' ),
			)
		);
	}

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'      => (string) $content['hero_eyebrow'],
					'title'        => (string) $content['hero_title'],
					'lead'         => (string) $content['hero_lead'],
					'buttonLabel'  => 'Peržiūrėti pozicijas',
					'buttonUrl'    => '#positions',
					'buttonIcon'   => '↓',
					'button2Label' => 'Palikti CV',
					'button2Url'   => '/kandidatuoti/',
					'lock'         => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['benefits_eyebrow'],
					'title'    => (string) $content['benefits_title'],
					'theme'    => 'paper',
					'anchorId' => 'benefits-title',
				),
				g5tech_cards_block_markup( $content['benefits'] )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'   => (string) $content['positions_eyebrow'],
					'title'     => (string) $content['positions_title'],
					'anchorId'  => 'positions-title',
					'sectionId' => 'positions',
				),
				g5tech_block_markup(
					'g5tech/job-groups',
					array( 'emptyText' => (string) $content['positions_empty'] )
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['selection_eyebrow'],
					'title'    => (string) $content['selection_title'],
					'theme'    => 'dark',
					'anchorId' => 'selection-title',
				),
				g5tech_list_block_markup(
					$content['selection_steps'],
					'g5tech/steps',
					'g5tech/step',
					array( 'title' => 'title', 'text' => 'text' )
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => (string) $content['growth_eyebrow'],
					'title'    => (string) $content['growth_title'],
					'anchorId' => 'growth-title',
				),
				g5tech_block_markup( 'g5tech/card-grid', array(), implode( "\n", $growth_cards ) )
			),
		)
	);
}

/**
 * Patirties puslapio turinys blokais (svetainės g5-* dialektas).
 */
function g5tech_experience_page_block_content() {
	$contact_url = function_exists( 'g5tech_setting' )
		? (string) g5tech_setting( 'contact_page_url', '/kontaktai/' )
		: '/kontaktai/';

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'  => 'Patirtis',
					'title'    => 'Patirtis Europos infrastruktūros projektuose.',
					'lead'     => 'Dirbame šešiose Europos šalyse, pagal skirtingų operatorių standartus ir su įvairių gamintojų įranga.',
					'dialect'  => 'site',
					'anchorId' => 'g5-experience-title',
					'lock'     => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Skaičiai',
					'title'    => 'Pagrindiniai skaičiai.',
					'theme'    => 'dark',
					'dialect'  => 'site',
					'anchorId' => 'g5-experience-numbers-title',
				),
				g5tech_block_markup( 'g5tech/stats-band' )
			),
			g5tech_block_markup(
				'g5tech/geo-section',
				array(
					'eyebrow'  => 'Geografija',
					'title'    => 'Šešios Europos šalys.',
					'anchorId' => 'g5-countries-title',
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Projektai',
					'title'    => 'Atrinkti projektai.',
					'theme'    => 'dark',
					'dialect'  => 'site',
					'anchorId' => 'g5-projects-preview-title',
				),
				g5tech_block_markup( 'g5tech/projects-preview' )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Patirtis',
					'title'    => 'Partneriai.',
					'theme'    => 'paper',
					'dialect'  => 'site',
					'anchorId' => 'g5-operators-title',
				),
				g5tech_block_markup( 'g5tech/partner-tag-split' )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Darbo standartas',
					'title'    => 'ISO standartai ir SSVA kvalifikacija.',
					'theme'    => 'dark',
					'dialect'  => 'site',
					'anchorId' => 'g5-certifications-title',
				),
				g5tech_block_markup( 'g5tech/certification-grid' )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Naujas projektas',
					'title'       => 'Aptarkime jūsų projektą.',
					'body'        => 'Nurodykite rinką, operatorių ar įrangą – pateiksime aktualią patirtį.',
					'buttonLabel' => 'Susisiekti',
					'buttonUrl'   => $contact_url,
					'dialect'     => 'site',
					'anchorId'    => 'g5-experience-cta-title',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}

/**
 * Pagalbinė: žymos/antraštės/teksto įrašų blokai.
 */
function g5tech_labeled_items_markup( $rows ) {
	$out = array();

	foreach ( (array) $rows as $row ) {
		$out[] = g5tech_block_markup(
			'g5tech/labeled-item',
			array(
				'label' => (string) ( $row['label'] ?? '' ),
				'title' => (string) ( $row['title'] ?? '' ),
				'text'  => (string) ( $row['text'] ?? '' ),
			)
		);
	}

	return implode( "\n", $out );
}

/**
 * „Apie mus“ puslapio turinys blokais. Tekstai imami iš esamos turinio opcijos.
 */
function g5tech_about_page_block_content() {
	if ( ! function_exists( 'g5tech_about_content' ) ) {
		return '';
	}

	$about       = g5tech_about_content();
	$contact_url = function_exists( 'g5tech_setting' )
		? (string) g5tech_setting( 'contact_page_url', '/kontaktai/' )
		: '/kontaktai/';

	// Faktų žymos saugomos be numerio — numeruoja atvaizdavimas.
	$strip_number = static function ( $rows ) {
		return array_map(
			static function ( $row ) {
				$row['label'] = preg_replace( '/^\d+\s*\/\s*/', '', (string) ( $row['label'] ?? '' ) );
				return $row;
			},
			(array) $rows
		);
	};

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/about-hero',
				array(
					'eyebrow' => (string) $about['hero_eyebrow'],
					'title'   => (string) $about['hero_title'],
					'lead'    => (string) $about['hero_lead'],
					'lock'    => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/about-story',
				array(
					'eyebrow'  => (string) $about['story_eyebrow'],
					'title'    => (string) $about['story_title'],
					'lead'     => (string) $about['story_lead'],
					'body1'    => (string) $about['story_body_1'],
					'body2'    => (string) $about['story_body_2'],
					'body3'    => (string) $about['story_body_3'],
					'image1Id' => absint( $about['story_image_1_id'] ),
					'image2Id' => absint( $about['story_image_2_id'] ),
					'caption1' => (string) $about['story_image_1_caption'],
					'caption2' => (string) $about['story_image_2_caption'],
				),
				g5tech_labeled_items_markup( $strip_number( $about['story_facts'] ) )
			),
			g5tech_block_markup(
				'g5tech/about-purpose',
				array(
					'eyebrow'      => (string) $about['purpose_eyebrow'],
					'title'        => (string) $about['purpose_title'],
					'missionLabel' => (string) $about['mission_label'],
					'missionTitle' => (string) $about['mission_title'],
					'missionText'  => (string) $about['mission_text'],
					'visionLabel'  => (string) $about['vision_label'],
					'visionTitle'  => (string) $about['vision_title'],
					'visionText'   => (string) $about['vision_text'],
				)
			),
			g5tech_block_markup(
				'g5tech/about-values',
				array(
					'eyebrow'            => (string) $about['values_eyebrow'],
					'title'              => (string) $about['values_title'],
					'cultureLabel'       => (string) $about['culture_label'],
					'cultureTitle'       => (string) $about['culture_title'],
					'cultureText'        => (string) $about['culture_text'],
					'cultureButtonLabel' => (string) $about['culture_button_label'],
					'cultureUrl'         => (string) $about['culture_url'],
				),
				g5tech_labeled_items_markup( $about['values'] )
			),
			g5tech_block_markup(
				'g5tech/about-team',
				array(
					'eyebrow' => (string) $about['team_eyebrow'],
					'title'   => (string) $about['team_title'],
				)
			),
			g5tech_block_markup(
				'g5tech/about-strategy',
				array(
					'eyebrow' => (string) $about['strategy_eyebrow'],
					'title'   => (string) $about['strategy_title'],
					'lead'    => (string) $about['strategy_lead'],
				),
				g5tech_labeled_items_markup( $strip_number( $about['strategies'] ) )
			),
			g5tech_block_markup(
				'g5tech/about-competence',
				array(
					'eyebrow' => (string) $about['competence_eyebrow'],
					'title'   => (string) $about['competence_title'],
				),
				g5tech_labeled_items_markup( $about['competences'] )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => (string) $about['cta_eyebrow'],
					'title'       => (string) $about['cta_title'],
					'body'        => (string) $about['cta_text'],
					'buttonLabel' => (string) $about['cta_button_label'],
					'buttonUrl'   => $contact_url,
					'dialect'     => 'team',
					'anchorId'    => 'team-cta-title',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}

/**
 * Titulinio puslapio turinys blokais. Tekstai imami iš nustatymų ir
 * struktūrinio turinio, skaidrės — iš temos failų arba pasirinktos nuotraukos.
 */
function g5tech_homepage_block_content() {
	if ( ! function_exists( 'g5tech_setting' ) || ! function_exists( 'g5tech_structured_section' ) ) {
		return '';
	}

	$audiences = g5tech_structured_section( 'home_audiences' );
	$front_id  = (int) get_option( 'page_on_front' );
	$hero_id   = (int) get_post_thumbnail_id( $front_id );

	$slides = array(
		array( 'imageId' => $hero_id, 'themeFile' => 'assets/images/home/hero-sky-worker.png', 'title' => 'Mobiliojo ryšio tinklai', 'alt' => '5G TECH specialistas dirba mobiliojo ryšio bokšte kalnų fone' ),
		array( 'imageId' => 0, 'themeFile' => 'assets/images/home/hero-indoor-networks.jpg', 'title' => 'Vidinio ryšio tinklai', 'alt' => 'Specialistas montuoja vidinio mobiliojo ryšio anteną prie modernaus pastato' ),
		array( 'imageId' => 0, 'themeFile' => 'assets/images/home/hero-fixed-networks.jpg', 'title' => 'Fiksuoto ryšio tinklai', 'alt' => 'Specialistas tvarko šviesolaidinio ryšio įrangą lauko skirstomojoje spintoje' ),
		array( 'imageId' => 0, 'themeFile' => 'assets/images/home/hero-electrical.jpg', 'title' => 'Elektros darbai', 'alt' => 'Elektrikas tikrina pramoninę elektros skirstomąją spintą' ),
		array( 'imageId' => 0, 'themeFile' => 'assets/images/home/hero-security.jpg', 'title' => 'Apsaugos sistemos', 'alt' => 'Specialistas montuoja lauko vaizdo stebėjimo kamerą' ),
		array( 'imageId' => 0, 'themeFile' => 'assets/images/home/hero-solar.jpg', 'title' => 'Saulės elektrinės', 'alt' => 'Specialistas montuoja saulės modulius ant komercinio objekto stogo' ),
	);

	$slide_blocks = array();

	foreach ( $slides as $slide ) {
		$slide_blocks[] = g5tech_block_markup( 'g5tech/hero-slide', $slide );
	}

	$audience_blocks = array();

	foreach ( (array) $audiences['cards'] as $card ) {
		$audience_blocks[] = g5tech_block_markup(
			'g5tech/audience-item',
			array(
				'label' => (string) ( $card['label'] ?? '' ),
				'title' => (string) ( $card['title'] ?? '' ),
				'text'  => (string) ( $card['text'] ?? '' ),
				'url'   => (string) ( $card['url'] ?? '' ),
			)
		);
	}

	// Sekcijos dedamos nustatymuose įrašyta tvarka; paslėptos sekcijos
	// tiesiog neįtraukiamos — nuo šiol tvarką ir matomumą valdo blokai.
	$sections = array(
		'intro'     => array( 1, g5tech_block_markup( 'g5tech/home-intro', array(
			'eyebrow'  => 'Nuo poreikio iki rezultato',
			'title'    => 'Kiekvienas projektas prasideda nuo konkretaus poreikio.',
			'body'     => 'Skiriasi objektai, šalys ir technologijos, tačiau užsakovui visada svarbu, kas prisiims atsakomybę už techninius sprendimus, terminus ir kokybę.',
			'imageAlt' => 'Telekomunikacijų, apsaugos, elektros ir saulės energetikos infrastruktūros linijinė iliustracija',
		) ) ),
		'services'  => array( 2, g5tech_block_markup( 'g5tech/home-services', array( 'title' => 'Paslaugos' ) ) ),
		'standards' => array( 3, g5tech_block_markup( 'g5tech/home-standards', array(
			'eyebrow' => 'Darbo standartas',
			'title'   => 'ISO standartai ir SSVA kvalifikacija.',
		) ) ),
		'process'   => array( 4, g5tech_block_markup( 'g5tech/home-process', array(
			'eyebrow' => 'Kaip dirbame',
			'title'   => 'Patikimas rezultatas kuriamas kiekviename projekto etape.',
		) ) ),
		'experience' => array( 5, g5tech_block_markup( 'g5tech/home-experience', array(
			'eyebrow' => 'Patirties geografija',
			'title'   => 'Patirtis šešiose Europos šalyse.',
		) ) ),
		'equipment' => array( 6, g5tech_block_markup( 'g5tech/home-equipment', array(
			'eyebrow' => 'Partneriai',
			'title'   => 'Gamintojai, su kurių įranga dirbame.',
		) ) ),
		'team'      => array( 7, g5tech_block_markup( 'g5tech/home-team', array(
			'eyebrow'   => 'Žmonės, kurie atsako už rezultatą',
			'title'     => 'Patirtis turi vardą, kompetenciją ir atsakomybę.',
			'copy'      => 'Susipažinkite su žmonėmis, kurie planuoja, įgyvendina ir tikrina projektus.',
			'linkLabel' => 'Susipažinti su visa komanda',
		) ) ),
		'audiences' => array( 8, g5tech_block_markup( 'g5tech/home-audiences', array(
			'title' => (string) $audiences['title'],
			'lead'  => (string) $audiences['lead'],
		), implode( "\n", $audience_blocks ) ) ),
		'news'      => array( 9, g5tech_block_markup( 'g5tech/home-news', array(
			'title' => 'Projektai, komanda ir techninės įžvalgos.',
		) ) ),
	);

	$visible = array();

	foreach ( $sections as $key => $definition ) {
		if ( ! g5tech_home_section_is_visible( $key ) ) {
			continue;
		}

		$visible[ $key ] = array( g5tech_home_section_order( $key, $definition[0] ), $definition[1] );
	}

	uasort(
		$visible,
		static function ( $first, $second ) {
			return $first[0] <=> $second[0];
		}
	);

	$section_markup = implode( "\n\n", array_column( $visible, 1 ) );

	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/home-hero',
				array(
					'eyebrow' => (string) g5tech_setting( 'home_hero_eyebrow' ),
					'title'   => (string) g5tech_setting( 'home_hero_title' ),
					'lead'    => (string) g5tech_setting( 'home_hero_lead' ),
					'lock'    => array( 'move' => true, 'remove' => true ),
				),
				implode( "\n", $slide_blocks )
			),
			g5tech_block_markup( 'g5tech/home-sections', array(), $section_markup ),
			g5tech_block_markup(
				'g5tech/home-cta',
				array(
					'eyebrow' => 'Pradėkime nuo pokalbio',
					'title'   => 'Aptarkime jūsų projektą.',
					'body'    => 'Trumpai aprašykite projektą arba techninę užduotį. Įvertinsime darbų apimtį ir pasiūlysime tolesnius veiksmus.',
					'lock'    => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}

/**
 * „Paslaugos" puslapio turinys blokais — buvusio archyvo atitikmuo.
 */
function g5tech_services_page_block_content() {
	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'  => 'Paslaugos',
					'title'    => 'Telekomunikacijų, energetikos ir inžinerinių sistemų paslaugos.',
					'dialect'  => 'site',
					'anchorId' => 'g5-services-page-title',
					'lock'     => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => '6 kryptys',
					'title'    => 'Paslaugų sritys.',
					'theme'    => 'paper',
					'dialect'  => 'site',
					'anchorId' => 'g5-services-list-title',
				),
				g5tech_block_markup( 'g5tech/service-cards' )
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Darbo standartas',
					'title'    => 'ISO standartai ir SSVA kvalifikacija.',
					'theme'    => 'dark',
					'dialect'  => 'site',
					'anchorId' => 'g5-services-standard-title',
				),
				g5tech_block_markup( 'g5tech/certification-grid' )
			),
			g5tech_block_markup(
				'g5tech/settings-cta',
				array(
					'eyebrow'  => 'Projektas',
					'anchorId' => 'g5-services-cta-title',
					'lock'     => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}

/**
 * „Projektai" puslapio turinys blokais — buvusio archyvo atitikmuo.
 */
function g5tech_projects_page_block_content() {
	return implode(
		"\n\n",
		array(
			g5tech_block_markup(
				'g5tech/page-hero',
				array(
					'eyebrow'  => 'Projektai',
					'title'    => 'Infrastruktūros projektai Europoje.',
					'lead'     => 'Telekomunikacijų, energetikos ir inžinerinės infrastruktūros darbai Lietuvoje bei kitose Europos šalyse.',
					'dialect'  => 'site',
					'anchorId' => 'g5-projects-title',
					'lock'     => array( 'move' => true, 'remove' => true ),
				)
			),
			g5tech_block_markup(
				'g5tech/section',
				array(
					'eyebrow'  => 'Patirtis',
					'title'    => 'Atrinkti projektai.',
					'theme'    => 'paper',
					'dialect'  => 'site',
					'anchorId' => 'g5-projects-list-title',
				),
				g5tech_block_markup( 'g5tech/project-cards' )
			),
			g5tech_block_markup(
				'g5tech/page-cta',
				array(
					'eyebrow'     => 'Naujas projektas',
					'title'       => 'Aptarkime jūsų projektą.',
					'body'        => 'Atsiūskite turimą informaciją – įvertinsime darbų apimtį ir tolesnius veiksmus.',
					'buttonLabel' => 'Susisiekti',
					'buttonUrl'   => '/kontaktai/',
					'dialect'     => 'site',
					'anchorId'    => 'g5-projects-cta-title',
					'lock'        => array( 'move' => true, 'remove' => true ),
				)
			),
		)
	);
}

/**
 * Sukuria katalogo puslapius „Paslaugos" ir „Projektai".
 *
 * Anksčiau šie adresai buvo įrašų tipų archyvai, valdomi tik per šablonus.
 * Dabar tai įprasti puslapiai sąraše „Puslapiai" — kaip visi kiti.
 */
function g5tech_create_catalog_pages() {
	if ( ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	$pages = array(
		'paslaugos' => array( 'Paslaugos', 'g5tech_services_page_block_content' ),
		'projektai' => array( 'Projektai', 'g5tech_projects_page_block_content' ),
	);

	$created = false;

	foreach ( $pages as $slug => $definition ) {
		if ( get_page_by_path( $slug ) instanceof WP_Post ) {
			continue;
		}

		wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'post_title'   => $definition[0],
				'post_content' => call_user_func( $definition[1] ),
			)
		);

		$created = true;
	}

	// Archyvai išjungti, todėl nuorodų taisyklės perrašomos, kad šiuos
	// adresus perimtų puslapiai.
	if ( $created ) {
		flush_rewrite_rules();
	}
}
add_action( 'admin_init', 'g5tech_create_catalog_pages', 42 );

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
		'naujienos'         => array( 'legacy' => 'g5tech/news-page',             'page_key' => 'news',             'builder' => 'g5tech_news_page_block_content' ),
		'duk'               => array( 'legacy' => 'g5tech/candidate-faq-page',    'page_key' => 'candidate_faq',    'builder' => 'g5tech_candidate_faq_page_block_content' ),
		'kontaktai'         => array( 'legacy' => 'g5tech/contact-page',          'page_key' => 'contact',          'builder' => 'g5tech_contact_page_block_content' ),
		'kandidatuoti'      => array( 'legacy' => 'g5tech/application-page',      'page_key' => '',                 'builder' => 'g5tech_application_page_block_content' ),
		'karjera'           => array( 'legacy' => 'g5tech/career-page',           'page_key' => 'career',           'builder' => 'g5tech_career_page_block_content' ),
		'patirtis'          => array( 'legacy' => 'g5tech/experience-page',       'page_key' => 'experience',       'builder' => 'g5tech_experience_page_block_content' ),
		'apie-mus'          => array( 'legacy' => 'g5tech/about-page',            'page_key' => 'about',            'builder' => 'g5tech_about_page_block_content' ),
		'pagrindinis'       => array( 'legacy' => 'g5tech/homepage',              'page_key' => 'home',             'builder' => 'g5tech_homepage_block_content' ),
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
