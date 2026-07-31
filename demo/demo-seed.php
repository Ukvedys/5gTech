<?php
/**
 * Plugin Name: 5G TECH demo duomenys
 * Description: Uzpildo 5G TECH nustatymus ir moduliu priskyrimus WordPress Playground demonstracijai.
 * Version: 1.0
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

function g5tech_demo_options() {
	return array (
  'g5tech_about_content' => 
  array (
    'story_image_1_id' => 0,
    'story_image_2_id' => 0,
    'hero_eyebrow' => 'Apie 5G TECH',
    'hero_title' => '5G TECH komanda ir veiklos kryptys.',
    'hero_lead' => 'Nuo 2020 metų įgyvendiname telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektus Lietuvoje bei kitose Europos šalyse.',
    'story_eyebrow' => 'Kas esame',
    'story_title' => 'Nuo planavimo iki dokumentacijos.',
    'story_lead' => 'Veiklą pradėjome nuo telekomunikacijų infrastruktūros projektų. Šiandien sukauptą techninę patirtį taikome ir energetikos bei kitose inžinerinės infrastruktūros srityse.',
    'story_body_1' => 'Dirbame su skirtingos apimties užduotimis: nuo konkretaus montavimo ar modernizavimo etapo iki darbų, kuriuose reikia suvaldyti planavimą, techninius sprendimus, rizikas, testavimą ir dokumentaciją.',
    'story_body_2' => 'Prieš pradėdami susitariame dėl darbų eigos, atsakomybių ir rezultato vertinimo. Atliktus darbus testuojame bei dokumentuojame, o saugą, kokybę ir terminus valdome viso projekto metu.',
    'story_body_3' => 'Procesai grindžiami ISO 9001, ISO 14001 ir ISO 45001 standartais. Įmonė taip pat turi SSVA ypatingojo statinio rangovo kvalifikacijos atestatą.',
    'story_fact_1_label' => '01 / Pradžia',
    'story_fact_1_title' => 'Įsigiliname prieš pradėdami.',
    'story_fact_1_text' => 'Techninė užduotis, objekto sąlygos, terminai ir atsakomybės.',
    'story_fact_2_label' => '02 / Vykdymas',
    'story_fact_2_title' => 'Dirbame pagal aiškią eigą.',
    'story_fact_2_text' => 'Komanda, įranga, saugos reikalavimai ir tiesioginis ryšys.',
    'story_fact_3_label' => '03 / Rezultatas',
    'story_fact_3_title' => 'Patikriname ir dokumentuojame.',
    'story_fact_3_text' => 'Testavimo duomenys, neatitikimų sprendimas ir aiškus perdavimas.',
    'story_image_1_caption' => '5G TECH komanda',
    'story_image_2_caption' => 'Atsakomybė turi vardą',
    'purpose_eyebrow' => 'Kryptis',
    'purpose_title' => 'Misija ir vizija.',
    'mission_label' => 'Misija',
    'mission_title' => 'Patikimai įgyvendinti infrastruktūros projektus.',
    'mission_text' => 'Aiški darbų eiga, saugus vykdymas ir patikrintas rezultatas.',
    'vision_label' => 'Vizija',
    'vision_title' => 'Būti pirmu pasirinkimu telekomunikacijų ir energetikos projektams.',
    'vision_text' => 'Augti Europos rinkose stiprinant komandą ir technines kompetencijas.',
    'values_eyebrow' => 'Mūsų vertybės',
    'values_title' => 'Principai, kuriais vadovaujamės kasdieniame darbe.',
    'value_1_number' => '01',
    'value_1_title' => 'Atsakomybė',
    'value_1_text' => 'Prisiimame atsakomybę už sutartą darbų apimtį, terminus ir rezultatą.',
    'value_2_number' => '02',
    'value_2_title' => 'Profesionalumas',
    'value_2_text' => 'Laikomės susitarimų, dirbame tiksliai ir sprendžiame problemas nelaukdami.',
    'value_3_number' => '03',
    'value_3_title' => 'Pagarba',
    'value_3_text' => 'Atvirai bendraujame su klientais, partneriais ir kolegomis.',
    'value_4_number' => '04',
    'value_4_title' => 'Prasmė',
    'value_4_text' => 'Kuriame infrastruktūrą, kuria kasdien naudojasi žmonės ir organizacijos.',
    'culture_label' => 'Darbo kultūra',
    'culture_title' => 'Elgesio kodeksas.',
    'culture_text' => 'Jis apibrėžia profesinio elgesio, atsakomybės, saugos, darbo drausmės ir bendradarbiavimo principus.',
    'culture_button_label' => 'Peržiūrėti kodeksą ↗',
    'culture_url' => 'https://5gtech.lt/wp-content/uploads/2025/04/5GTech_Kodeksas_Elgesys_Etika_2025_V1.1.pdf',
    'strategy_eyebrow' => 'Strategija 2025–2028',
    'strategy_title' => 'Kokybiškas ir tvarus augimas.',
    'strategy_lead' => 'Siekiame stiprinti telekomunikacijų ir atsinaujinančios energetikos kompetencijas, plėsti veiklą Europoje ir nuosekliai gerinti darbo procesus.',
    'strategy_1_label' => '01 / KOKYBĖ',
    'strategy_1_title' => 'Vienodi procesai visuose projektuose.',
    'strategy_1_text' => 'Tobuliname darbų planavimo, kokybės kontrolės ir dokumentavimo metodiką.',
    'strategy_2_label' => '02 / EUROPA',
    'strategy_2_title' => 'Daugiau projektų Europos rinkose.',
    'strategy_2_text' => 'Plečiame komandos patirtį dirbdami pagal skirtingų šalių operatorių reikalavimus.',
    'strategy_3_label' => '03 / KOMANDA',
    'strategy_3_title' => 'Mokymai, sauga ir profesinis augimas.',
    'strategy_3_text' => 'Investuojame į darbui reikalingas kvalifikacijas ir praktinį komandos pasirengimą.',
    'strategy_4_label' => '04 / SANTYKIS',
    'strategy_4_title' => 'Aiškūs įsipareigojimai ir tiesioginis ryšys.',
    'strategy_4_text' => 'Klientas viso projekto metu žino darbų būklę, atsakomybes ir kitus veiksmus.',
    'team_eyebrow' => 'Komanda',
    'team_title' => 'Pagrindinė komanda.',
    'competence_eyebrow' => 'Kompetencija',
    'competence_title' => 'Patirtis, kvalifikacijos ir tiesioginiai kontaktai.',
    'competence_1_number' => '01',
    'competence_1_title' => 'Srities patirtis',
    'competence_1_text' => 'Darbo metai, atsakomybės ir projektų tipai.',
    'competence_2_number' => '02',
    'competence_2_title' => 'Geografija ir operatoriai',
    'competence_2_text' => 'Šalys ir operatorių projektai, kuriuose žmogus dalyvavo.',
    'competence_3_number' => '03',
    'competence_3_title' => 'Kvalifikacijos',
    'competence_3_text' => 'Galiojantys sertifikatai, atestatai ir darbų vadovo teisės.',
    'competence_4_number' => '04',
    'competence_4_title' => 'Kontaktas',
    'competence_4_text' => 'Tiesioginis ryšys su už projektą atsakingu žmogumi.',
    'cta_eyebrow' => 'Aptarkime projektą',
    'cta_title' => 'Aptarkime, kokios komandos reikia jūsų projektui.',
    'cta_text' => 'Papasakokite apie projekto apimtį, techninius reikalavimus ir numatytus terminus.',
    'cta_button_label' => 'Aptarkime jūsų projektą',
  ),
  'g5tech_about_section_order' => 
  array (
    'story' => 1,
    'purpose' => 2,
    'values' => 3,
    'team' => 4,
    'strategy' => 5,
    'competence' => 6,
  ),
  'g5tech_admin_translation_overrides' => 
  array (
    'en' => 
    array (
    ),
    'de' => 
    array (
    ),
  ),
  'g5tech_builtin_modules_migrated_1' => '1',
  'g5tech_content_modules_migrated_1' => '1',
  'g5tech_content_pages_blocks_migrated' => '1',
  'g5tech_content_translation_overrides' => 
  array (
    'en' => 
    array (
      'Programa' => 'Program',
      'Mokymų temos.' => 'Training topics.',
      'Asmeninės apsaugos priemonės ir jų naudojimas' => 'Personal protective equipment and their use',
      'Darbų aukštyje specifika ir saugos taisyklės' => 'Specifics and safety rules for work at height',
      '2G, 3G, 4G-LTE ir 5G technologijų pagrindai' => 'Basics of 2G, 3G, 4G-LTE and 5G technologies',
      'Antenų ir radijo modulių montavimas' => 'Installation of antennas and radio modules',
      'Kabelių instaliacija ir prijungimas' => 'Cable installation and connection',
      'Elektros instaliacijos principai' => 'Principles of electrical installation',
      'Techninės dokumentacijos rengimas' => 'Preparation of technical documentation',
      'Bazinės stoties konfigūravimo pagrindai' => 'Base station configuration basics',
      'Techninė eiga' => 'Technical progress',
      'Planavimas, darbų kontrolė ir dokumentacija.' => 'Planning, work control and documentation.',
      '„Site Survey“ ir esamos situacijos įvertinimas' => 'Site Survey and Assessment of Existing Situation',
      'Darbų, medžiagų ir komandos planavimas' => 'Work, material and team planning',
      'Ericsson, Nokia, Huawei ir kitos įrangos montavimas' => 'Installation of Ericsson, Nokia, Huawei and other equipment',
      'MW linijų konfigūravimas ir testavimas' => 'Configuration and testing of MW lines',
      '„SiteMaster“ ir kiti kokybės matavimai' => 'SiteMaster and other quality metrics',
      'Dokumentacija pagal užsakovo standartus' => 'Documentation according to customer standards',
      'Tiesioginis ryšys su atsakingu projektų vadovu' => 'Direct communication with the responsible project manager',
      'Neatitikimų valdymas iki patvirtinto rezultato' => 'Non-conformity management through to verified completion',
    ),
    'de' => 
    array (
      'Programa' => 'Programm',
      'Mokymų temos.' => 'Schulungsthemen.',
      'Asmeninės apsaugos priemonės ir jų naudojimas' => 'Persönliche Schutzausrüstung und ihre Verwendung',
      'Darbų aukštyje specifika ir saugos taisyklės' => 'Besonderheiten und Sicherheitsregeln für Arbeiten in der Höhe',
      '2G, 3G, 4G-LTE ir 5G technologijų pagrindai' => 'Grundlagen der 2G-, 3G-, 4G-LTE- und 5G-Technologien',
      'Antenų ir radijo modulių montavimas' => 'Installation von Antennen und Funkmodulen',
      'Kabelių instaliacija ir prijungimas' => 'Kabelinstallation und Anschluss',
      'Elektros instaliacijos principai' => 'Grundlagen der Elektroinstallation',
      'Techninės dokumentacijos rengimas' => 'Erstellung technischer Dokumentation',
      'Bazinės stoties konfigūravimo pagrindai' => 'Grundlagen der Basisstationskonfiguration',
      'Techninė eiga' => 'Technischer Fortschritt',
      'Planavimas, darbų kontrolė ir dokumentacija.' => 'Planung, Arbeitssteuerung und Dokumentation.',
      '„Site Survey“ ir esamos situacijos įvertinimas' => 'Standortbesichtigung und Beurteilung der bestehenden Situation',
      'Darbų, medžiagų ir komandos planavimas' => 'Arbeits-, Material- und Teamplanung',
      'Ericsson, Nokia, Huawei ir kitos įrangos montavimas' => 'Installation von Ericsson, Nokia, Huawei und anderen Geräten',
      'MW linijų konfigūravimas ir testavimas' => 'Konfiguration und Test von MW-Linien',
      '„SiteMaster“ ir kiti kokybės matavimai' => 'SiteMaster und andere Qualitätsmetriken',
      'Dokumentacija pagal užsakovo standartus' => 'Dokumentation nach Kundenstandards',
      'Tiesioginis ryšys su atsakingu projektų vadovu' => 'Direkte Kommunikation mit dem verantwortlichen Projektleiter',
      'Neatitikimų valdymas iki patvirtinto rezultato' => 'Abweichungsmanagement bis zum geprüften Ergebnis',
    ),
  ),
  'g5tech_legal_pages_blocks_migrated' => '1',
  'g5tech_roles_version' => '3',
  'g5tech_settings' => 
  array (
    'stat_1_value' => '6000+',
    'stat_1_label' => 'įgyvendintų bazinių stočių',
    'stat_2_value' => '2G–5G',
    'stat_2_label' => 'technologijų patirtis',
    'stat_3_value' => '6',
    'stat_3_label' => 'Europos šalys',
    'stat_4_value' => '2020',
    'stat_4_label' => 'veikiame nuo',
    'process_1_title' => 'Įsigiliname',
    'process_1_text' => 'Suprantame techninę užduotį, objekto sąlygas ir atsakomybes.',
    'process_2_title' => 'Suplanuojame',
    'process_2_text' => 'Suderiname sprendimus, terminus, komandą ir darbų saugą.',
    'process_3_title' => 'Įgyvendiname',
    'process_3_text' => 'Atliekame numatytus montavimo ir integravimo darbus.',
    'process_4_title' => 'Patikriname',
    'process_4_text' => 'Testuojame įrangą ir ištaisome nustatytus neatitikimus.',
    'process_5_title' => 'Perduodame',
    'process_5_text' => 'Pateikiame dokumentaciją ir perduodame užbaigtą sprendimą.',
    'cta_title' => 'Aptarkime jūsų techninę užduotį.',
    'cta_text' => 'Atsiųskite turimą informaciją – įvertinsime darbų apimtį ir pasiūlysime tolesnius veiksmus.',
    'cta_button_label' => 'Susisiekti',
    'hero_button_label' => 'Aptarkime projektą',
    'home_hero_eyebrow' => 'Telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektai',
    'home_hero_title' => 'Kai svarbus ne tik atliktas darbas, bet ir ramybė dėl rezultato',
    'home_hero_lead' => 'Techninis partneris nuo pasirengimo ir montavimo iki testavimo, dokumentacijos bei priežiūros.',
    'home_show_intro' => '1',
    'home_order_intro' => '1',
    'home_show_services' => '1',
    'home_order_services' => '2',
    'home_show_standards' => '1',
    'home_order_standards' => '3',
    'home_show_process' => '1',
    'home_order_process' => '4',
    'home_show_experience' => '1',
    'home_order_experience' => '5',
    'home_show_equipment' => '1',
    'home_order_equipment' => '6',
    'home_show_team' => '1',
    'home_order_team' => '7',
    'home_show_audiences' => '1',
    'home_order_audiences' => '8',
    'home_show_news' => '1',
    'home_order_news' => '9',
    'contact_page_url' => '/kontaktai/',
    'phone' => '+370 687 77155',
    'email' => 'info@5gtech.lt',
    'career_email' => 'Kristina@5gtech.lt',
    'address' => 'Meistrų g. 8A
LT-02189 Vilnius',
    'company_code' => '305547599',
    'vat_code' => 'LT100013136617',
    'countries' => 'Lietuva
Vokietija
Švedija
Norvegija
Danija
Suomija',
    'certifications' => 'ISO 9001 | Kokybės vadyba
ISO 14001 | Aplinkos apsaugos vadyba
ISO 45001 | Darbuotojų sveikata ir sauga
SSVA | Ypatingojo statinio rangovo kvalifikacija',
  ),
  'g5tech_training_page_content' => 
  array (
    'hero_eyebrow' => 'Mokymai',
    'hero_title' => 'Praktiniai techniniai mokymai.',
    'hero_lead' => 'Mokymų salė skirta naujų darbuotojų parengimui ir patyrusių specialistų kompetencijų tobulinimui.',
    'topics_eyebrow' => 'Programa',
    'topics_title' => 'Mokymų temos.',
    'topics' => 'Asmeninės apsaugos priemonės ir jų naudojimas
Darbų aukštyje specifika ir saugos taisyklės
2G, 3G, 4G-LTE ir 5G technologijų pagrindai
Antenų ir radijo modulių montavimas
Kabelių instaliacija ir prijungimas
Elektros instaliacijos principai
Techninės dokumentacijos rengimas
Bazinės stoties konfigūravimo pagrindai',
    'equipment_eyebrow' => 'Įranga',
    'equipment_title' => 'Mokomės su realiuose projektuose naudojama įranga.',
    'equipment_ids' => 
    array (
      0 => 22,
      1 => 23,
      2 => 24,
      3 => 44,
      4 => 45,
      5 => 144,
    ),
    'image_eyebrow' => 'Aplinka',
    'image_title' => 'Mokymų salė.',
    'image_id' => 0,
    'image_alt' => '5G TECH praktinių mokymų aplinka',
    'cta_eyebrow' => 'Karjera',
    'cta_title' => 'Norite prisijungti prie komandos?',
    'cta_button_label' => 'Peržiūrėti pozicijas',
    'cta_button_url' => 'http://5gtech.test/karjera/',
    'section_order' => 
    array (
      0 => 'topics',
      1 => 'equipment',
      2 => 'image',
    ),
  ),
);
}

function g5tech_demo_placement_keys() {
	return array (
  'home' => 
  array (
    0 => 'dyn:home_intro',
    1 => 'dyn:home_services',
    2 => 'dyn:home_standards',
    3 => 'dyn:home_process',
    4 => 'dyn:home_experience',
    5 => 'dyn:home_equipment',
    6 => 'dyn:home_team',
    7 => 'dyn:home_audiences',
    8 => 'dyn:home_news',
  ),
  'services' => 
  array (
    0 => 'dyn:services_list',
    1 => 'dyn:services_standard',
  ),
  'projects' => 
  array (
    0 => 'dyn:projects_list',
  ),
  'experience' => 
  array (
    0 => 'dyn:experience_numbers',
    1 => 'dyn:experience_geography',
    2 => 'dyn:experience_projects',
    3 => 'dyn:experience_partners',
    4 => 'dyn:experience_certifications',
  ),
  'about' => 
  array (
    0 => 'dyn:about_story',
    1 => 'dyn:about_purpose',
    2 => 'dyn:about_values',
    3 => 'dyn:about_team',
    4 => 'dyn:about_strategy',
    5 => 'dyn:about_competence',
  ),
  'career' => 
  array (
    0 => 'dyn:career_benefits',
    1 => 'dyn:career_positions',
    2 => 'dyn:career_selection',
    3 => 'dyn:career_growth',
  ),
  'academy' => 
  array (
  ),
  'training' => 
  array (
  ),
  'candidate_faq' => 
  array (
    0 => 'dyn:candidate_faq_groups',
  ),
  'leaders' => 
  array (
  ),
  'project_managers' => 
  array (
  ),
  'contact' => 
  array (
    0 => 'dyn:contact_form',
    1 => 'dyn:contact_people',
  ),
  'news' => 
  array (
    0 => 'dyn:news_list',
  ),
);
}

add_action( 'init', function () {
	if ( get_option( 'g5tech_demo_seeded' ) ) {
		return;
	}

	foreach ( g5tech_demo_options() as $name => $value ) {
		update_option( $name, $value, false );
	}

	// Moduliu priskyrimai atkuriami pagal raktus, nes po importo ID kiti.
	$lookup = array();

	foreach ( get_posts( array( 'post_type' => 'g5_module', 'numberposts' => -1, 'post_status' => 'any' ) ) as $module ) {
		$dyn = get_post_meta( $module->ID, 'g5_module_dynamic_key', true );
		$src = get_post_meta( $module->ID, 'g5_module_source_key', true );

		if ( $dyn ) { $lookup[ 'dyn:' . $dyn ] = $module->ID; }
		if ( $src ) { $lookup[ 'src:' . $src ] = $module->ID; }
		$lookup[ 'title:' . $module->post_title ] = $module->ID;
	}

	$placements = array();

	foreach ( g5tech_demo_placement_keys() as $page_key => $keys ) {
		$ids = array();

		foreach ( $keys as $key ) {
			if ( isset( $lookup[ $key ] ) ) {
				$ids[] = (int) $lookup[ $key ];
			}
		}

		$placements[ $page_key ] = $ids;
	}

	update_option( 'g5tech_module_placements', $placements, false );

	$front = get_page_by_path( 'pagrindinis' );

	if ( $front ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $front->ID );
	}

	update_option( 'permalink_structure', '/%postname%/' );
	update_option( 'blogname', '5G TECH' );
	update_option( 'blogdescription', 'Telekomunikaciju, energetikos ir inzinerines infrastrukturos projektai' );
	flush_rewrite_rules();

	update_option( 'g5tech_demo_seeded', 1 );
}, 5 );

/**
 * Nustato svetainės logotipą.
 *
 * Tema jį ima per `wp:site-logo` bloką, kuris skaito atskirą `site_logo`
 * opciją. Nei turinio importas, nei nustatymų perkėlimas jos nenustato,
 * todėl logotipas surandamas medijos bibliotekoje pagal failo vardą.
 *
 * Tikrinama atskirai nuo bendro seed'o, kad suveiktų ir tada, kai
 * nustatymai jau perkelti anksčiau.
 */
add_action( 'init', function () {
	if ( get_option( 'site_logo' ) ) {
		return;
	}

	$found = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'meta_query'     => array(
				array(
					'key'     => '_wp_attached_file',
					'value'   => '5gtech-logo-white',
					'compare' => 'LIKE',
				),
			),
		)
	);

	if ( $found ) {
		update_option( 'site_logo', (int) $found[0]->ID );
	}
}, 6 );
