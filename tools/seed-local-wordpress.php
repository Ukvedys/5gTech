<?php
/**
 * Sukuria pirmo vietinio 5G TECH bandymo turinį.
 *
 * Paleidimas:
 * php /tmp/5gtech-wp-cli.phar eval-file ../tools/seed-local-wordpress.php
 */

if ( 'local' !== wp_get_environment_type() ) {
	WP_CLI::error( 'Šį failą galima paleisti tik vietinėje aplinkoje.' );
}

function g5tech_local_page( $title, $slug, $content = '' ) {
	$existing = get_page_by_path( $slug, OBJECT, 'page' );

	if ( $existing ) {
		return $existing->ID;
	}

	return wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => 'publish',
		)
	);
}

function g5tech_local_attachment( $source, $title ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'title'          => $title,
			'posts_per_page' => 1,
			'post_status'    => 'inherit',
		)
	);

	if ( $existing ) {
		return $existing[0]->ID;
	}

	$filename = basename( $source );
	$upload   = wp_upload_bits( $filename, null, file_get_contents( $source ) );

	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::error( $upload['error'] );
	}

	$filetype = wp_check_filetype( $upload['file'] );
	$id       = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'],
			'post_title'     => $title,
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata(
		$id,
		wp_generate_attachment_metadata( $id, $upload['file'] )
	);

	return $id;
}

function g5tech_local_partner( $title, $type, $order ) {
	$matches = get_posts(
		array(
			'post_type'      => 'g5_partner',
			'title'          => $title,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		)
	);
	$existing = $matches ? $matches[0] : null;

	if ( $existing ) {
		return $existing->ID;
	}

	$data     = array(
		'post_type'   => 'g5_partner',
		'post_title'  => $title,
		'post_status' => 'publish',
		'menu_order'  => $order,
	);

	$id = wp_insert_post( $data );

	update_post_meta( $id, 'g5_partner_type', $type );

	return $id;
}

function g5tech_local_service( $service, $catalog_ids, $image_id, $order ) {
	$existing = get_page_by_path( $service['slug'], OBJECT, 'g5_service' );

	if ( $existing ) {
		return $existing->ID;
	}

	$data     = array(
		'post_type'    => 'g5_service',
		'post_title'   => $service['title'],
		'post_name'    => $service['slug'],
		'post_content' => g5tech_default_service_content(),
		'post_status'  => 'publish',
		'menu_order'   => $order,
	);

	$id = wp_insert_post( $data );

	$selected_ids = array();
	foreach ( $service['equipment'] as $name ) {
		if ( isset( $catalog_ids[ $name ] ) ) {
			$selected_ids[] = $catalog_ids[ $name ];
		}
	}

	update_post_meta( $id, 'g5_service_category', $service['category'] );
	update_post_meta( $id, 'g5_service_summary', $service['summary'] );
	update_post_meta( $id, 'g5_service_card_title', $service['card_title'] ?? $service['title'] );
	update_post_meta( $id, 'g5_service_card_summary', $service['card_summary'] ?? $service['summary'] );
	update_post_meta( $id, 'g5_service_work', implode( "\n", $service['work'] ) );
	update_post_meta( $id, 'g5_service_faq', implode( "\n", $service['faq'] ?? array() ) );
	update_post_meta( $id, 'g5_service_partners', $selected_ids );
	delete_post_meta( $id, 'g5_service_equipment' );
	delete_post_meta( $id, 'g5_service_cta_title' );
	delete_post_meta( $id, 'g5_service_cta_text' );
	set_post_thumbnail( $id, $image_id );

	return $id;
}

function g5tech_local_project( $project, $service_ids, $image_id, $order ) {
	$existing   = get_page_by_path( $project['slug'], OBJECT, 'g5_project' );
	$project_id = $existing
		? $existing->ID
		: wp_insert_post(
			array(
				'post_type'   => 'g5_project',
				'post_title'  => $project['title'],
				'post_name'   => $project['slug'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);

	wp_update_post(
		array(
			'ID'           => $project_id,
			'post_title'   => $project['title'],
			'post_status'  => 'publish',
			'menu_order'   => $order,
		)
	);

	update_post_meta( $project_id, 'g5_project_summary', $project['summary'] ?? '' );
	update_post_meta( $project_id, 'g5_project_year', $project['year'] ?? '' );
	update_post_meta( $project_id, 'g5_project_location', $project['location'] ?? '' );
	update_post_meta( $project_id, 'g5_project_scope', implode( "\n", $project['scope'] ?? array() ) );
	update_post_meta( $project_id, 'g5_project_result', $project['result'] ?? '' );
	update_post_meta( $project_id, 'g5_project_visible', '1' );
	update_post_meta(
		$project_id,
		'g5_project_service',
		$service_ids[ $project['service'] ] ?? 0
	);

	wp_set_object_terms( $project_id, $project['country'] ?? array(), 'g5_project_country', false );
	wp_set_object_terms( $project_id, $project['technology'] ?? array(), 'g5_project_technology', false );

	if ( $image_id ) {
		set_post_thumbnail( $project_id, $image_id );
	}

	return $project_id;
}

function g5tech_local_faq( $question, $answer, $service_ids, $order, $topic = 'client', $group = '' ) {
	$matches = get_posts(
		array(
			'post_type'      => 'g5_faq',
			'title'          => $question,
			'post_status'    => 'any',
			'posts_per_page' => 1,
		)
	);
	$faq_id  = $matches
		? $matches[0]->ID
		: wp_insert_post(
			array(
				'post_type'   => 'g5_faq',
				'post_title'  => $question,
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);

	update_post_meta( $faq_id, 'g5_faq_answer', $answer );
	update_post_meta( $faq_id, 'g5_faq_topic', $topic );
	update_post_meta( $faq_id, 'g5_faq_group', $group );
	delete_post_meta( $faq_id, 'g5_faq_service' );

	foreach ( array_unique( array_filter( array_map( 'absint', $service_ids ) ) ) as $service_id ) {
		add_post_meta( $faq_id, 'g5_faq_service', $service_id );
	}

	return $faq_id;
}

function g5tech_local_team_member( $person, $operator_ids, $order ) {
	$existing = get_page_by_path( $person['slug'], OBJECT, 'g5_team' );
	$member_id = $existing
		? $existing->ID
		: wp_insert_post(
			array(
				'post_type'   => 'g5_team',
				'post_title'  => $person['name'],
				'post_name'   => $person['slug'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);

	$fields = array(
		'g5_team_role'             => $person['role'] ?? '',
		'g5_team_summary'          => $person['summary'] ?? '',
		'g5_team_email'            => $person['email'] ?? '',
		'g5_team_phone'            => $person['phone'] ?? '',
		'g5_team_experience_since' => $person['experience_since'] ?? '',
		'g5_team_company_since'    => $person['company_since'] ?? '',
		'g5_team_primary_area'     => $person['primary_area'] ?? '',
		'g5_team_responsibility'   => $person['responsibility'] ?? '',
		'g5_team_experience'       => $person['experience'] ?? '',
		'g5_team_countries'        => implode( "\n", $person['countries'] ?? array() ),
		'g5_team_competencies'     => implode( "\n", $person['competencies'] ?? array() ),
	);

	foreach ( $fields as $key => $value ) {
		update_post_meta( $member_id, $key, $value );
	}

	update_post_meta( $member_id, 'g5_team_show_contact', '1' );
	update_post_meta( $member_id, 'g5_team_show_profile', ! empty( $person['show_profile'] ) ? '1' : '0' );
	delete_post_meta( $member_id, 'g5_team_operators' );

	foreach ( $person['operators'] ?? array() as $operator_name ) {
		if ( ! empty( $operator_ids[ $operator_name ] ) ) {
			add_post_meta( $member_id, 'g5_team_operators', $operator_ids[ $operator_name ] );
		}
	}

	return $member_id;
}

function g5tech_local_job( $job, $order ) {
	$existing = get_page_by_path( $job['slug'], OBJECT, 'g5_job' );
	$job_id   = $existing
		? $existing->ID
		: wp_insert_post(
			array(
				'post_type'   => 'g5_job',
				'post_title'  => $job['title'],
				'post_name'   => $job['slug'],
				'post_status' => 'publish',
				'menu_order'  => $order,
			)
		);

	$fields = array(
		'g5_job_group'            => $job['group'] ?? 'lithuania',
		'g5_job_level'            => $job['level'] ?? '',
		'g5_job_location'         => $job['location'] ?? '',
		'g5_job_salary'           => $job['salary'] ?? '',
		'g5_job_rotation'         => $job['rotation'] ?? '',
		'g5_job_driving'          => $job['driving'] ?? '',
		'g5_job_summary'          => $job['summary'] ?? '',
		'g5_job_responsibilities' => implode( "\n", $job['responsibilities'] ?? array() ),
		'g5_job_requirements'     => implode( "\n", $job['requirements'] ?? array() ),
		'g5_job_offer'            => implode( "\n", $job['offer'] ?? array() ),
		'g5_job_expires'          => $job['expires'] ?? '',
	);

	foreach ( $fields as $key => $value ) {
		update_post_meta( $job_id, $key, $value );
	}

	update_post_meta( $job_id, 'g5_job_active', '1' );

	return $job_id;
}

function g5tech_local_news_post( $news, $order ) {
	$existing = get_page_by_path( $news['slug'], OBJECT, 'post' );
	$category = term_exists( $news['category'], 'category' );

	if ( ! $category ) {
		$category = wp_insert_term( $news['category'], 'category' );
	}

	$category_id = is_array( $category ) ? (int) $category['term_id'] : (int) $category;
	$post_id     = $existing
		? $existing->ID
		: wp_insert_post(
			array(
				'post_type'     => 'post',
				'post_title'    => $news['title'],
				'post_name'     => $news['slug'],
				'post_content'  => $news['content'],
				'post_excerpt'  => $news['excerpt'],
				'post_status'   => 'publish',
				'post_category' => array( $category_id ),
				'menu_order'    => $order,
			)
		);

	wp_set_post_categories( $post_id, array( $category_id ) );

	return $post_id;
}

$home_content = '<!-- wp:g5tech/homepage /-->';

$home_id    = g5tech_local_page( 'Pagrindinis', 'pagrindinis', $home_content );
$contact_id = g5tech_local_page(
	'Kontaktai',
	'kontaktai',
	'<!-- wp:g5tech/contact-page /-->'
);
$privacy_id = g5tech_local_page(
	'Privatumo politika',
	'privatumo-politika',
	'<!-- wp:g5tech/privacy-page /-->'
);
$cookies_id = g5tech_local_page(
	'Slapukų politika',
	'slapukai',
	'<!-- wp:g5tech/cookies-page /-->'
);
$application_id = g5tech_local_page(
	'Kandidatuoti',
	'kandidatuoti',
	'<!-- wp:g5tech/application-page /-->'
);
$academy_id = g5tech_local_page( '5GTECH Academy', 'akademija', '<!-- wp:g5tech/academy-page /-->' );
$training_id = g5tech_local_page( 'Mokymai', 'mokymai', '<!-- wp:g5tech/training-page /-->' );
$candidate_faq_id = g5tech_local_page( 'DUK kandidatams', 'duk', '<!-- wp:g5tech/candidate-faq-page /-->' );
$leaders_id = g5tech_local_page( 'Vadovams', 'vadovams', '<!-- wp:g5tech/leaders-page /-->' );
$project_managers_id = g5tech_local_page( 'Projektų vadovams', 'projektu-vadovams', '<!-- wp:g5tech/project-managers-page /-->' );
$experience_id = g5tech_local_page(
	'Patirtis',
	'patirtis',
	'<!-- wp:g5tech/experience-page /-->'
);
$about_id = g5tech_local_page(
	'Apie mus',
	'apie-mus',
	'<!-- wp:g5tech/about-page /-->'
);
$career_id = g5tech_local_page(
	'Karjera',
	'karjera',
	'<!-- wp:g5tech/career-page /-->'
);
$news_id = g5tech_local_page(
	'Naujienos',
	'naujienos',
	'<!-- wp:g5tech/news-page /-->'
);

wp_update_post(
	array(
		'ID'           => $home_id,
		'post_content' => $home_content,
	)
);
wp_update_post( array( 'ID' => $contact_id, 'post_content' => '<!-- wp:g5tech/contact-page /-->' ) );
wp_update_post( array( 'ID' => $privacy_id, 'post_content' => '<!-- wp:g5tech/privacy-page /-->' ) );
wp_update_post( array( 'ID' => $cookies_id, 'post_content' => '<!-- wp:g5tech/cookies-page /-->' ) );
wp_update_post( array( 'ID' => $application_id, 'post_content' => '<!-- wp:g5tech/application-page /-->' ) );
wp_update_post( array( 'ID' => $academy_id, 'post_content' => '<!-- wp:g5tech/academy-page /-->' ) );
wp_update_post( array( 'ID' => $training_id, 'post_content' => '<!-- wp:g5tech/training-page /-->' ) );
wp_update_post( array( 'ID' => $candidate_faq_id, 'post_content' => '<!-- wp:g5tech/candidate-faq-page /-->' ) );
wp_update_post( array( 'ID' => $leaders_id, 'post_content' => '<!-- wp:g5tech/leaders-page /-->' ) );
wp_update_post( array( 'ID' => $project_managers_id, 'post_content' => '<!-- wp:g5tech/project-managers-page /-->' ) );

update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home_id );

$manufacturer_names = array(
	'Ericsson',
	'Nokia',
	'Huawei',
	'ZTE',
	'CommScope',
	'Sonel',
	'FIAMM',
	'Enersys',
	'Eltek',
	'Delta',
	'Dantherm',
);
$manufacturer_ids   = array();

foreach ( $manufacturer_names as $index => $manufacturer_name ) {
	$manufacturer_ids[ $manufacturer_name ] = g5tech_local_partner(
		$manufacturer_name,
		'manufacturer',
		$index
	);
	update_post_meta(
		$manufacturer_ids[ $manufacturer_name ],
		'g5_partner_show_home',
		in_array(
			$manufacturer_name,
			array( 'Ericsson', 'Nokia', 'Huawei', 'ZTE', 'FIAMM', 'Enersys', 'Eltek', 'Delta', 'Dantherm' ),
			true
		) ? '1' : '0'
	);
}

$equipment_names = array(
	'SiteMaster',
	'3Z-RFVision',
	'Optiniai tinklai',
	'ODF',
	'OTDR',
	'Šviesolaidis',
	'Variniai kabeliai',
	'Testavimo įranga',
	'Matavimo įranga',
	'IP CCTV',
	'Patekimo kontrolė',
	'Gaisro signalizacija',
	'Perimetro apsauga',
	'LPR',
	'Vaizdo archyvai',
	'Saulės moduliai',
	'Inverteriai',
	'Konstrukcijos',
	'Apsaugos įranga',
	'Stebėsena',
);
$catalog_ids    = $manufacturer_ids;

foreach ( $equipment_names as $index => $equipment_name ) {
	$catalog_ids[ $equipment_name ] = g5tech_local_partner(
		$equipment_name,
		'equipment',
		$index
	);
}

$operator_names = array(
	'Telia',
	'Tele2',
	'Bitė',
	'Deutsche Telekom',
	'Vodafone',
	'Telefónica / O2',
	'Telenor',
	'Elisa',
	'TDC',
	'1&1',
	'450connect',
);
$operator_ids = array();

foreach ( $operator_names as $index => $operator_name ) {
	$operator_ids[ $operator_name ] = g5tech_local_partner( $operator_name, 'operator', $index );
}

$theme_dir = get_theme_file_path( 'assets/images' );
$logo_id   = g5tech_local_attachment( $theme_dir . '/5gtech-logo-white.png', '5G TECH logotipas' );
$tower_id  = g5tech_local_attachment( $theme_dir . '/service-demo.png', '5G bokšto darbai' );
$team_id   = g5tech_local_attachment( $theme_dir . '/service-team.jpg', '5G TECH komandos darbai' );
$home_hero_id = g5tech_local_attachment( $theme_dir . '/home/hero-sky-worker.png', '5G TECH titulinio puslapio nuotrauka' );

set_theme_mod( 'custom_logo', $logo_id );
set_post_thumbnail( $home_id, $home_hero_id );

$local_settings                  = wp_parse_args( get_option( 'g5tech_settings', array() ), g5tech_default_settings() );
$local_settings['phone']         = $local_settings['phone'] ?: '+370 687 77155';
$local_settings['email']         = $local_settings['email'] ?: 'info@5gtech.lt';
$local_settings['career_email']  = $local_settings['career_email'] ?: 'Kristina@5gtech.lt';
$local_settings['address']       = $local_settings['address'] ?: "Meistrų g. 8A\nLT-02189 Vilnius";
$local_settings['company_code']  = $local_settings['company_code'] ?: '305547599';
$local_settings['vat_code']      = $local_settings['vat_code'] ?: 'LT100013136617';
update_option( 'g5tech_settings', $local_settings );

foreach ( array( 'Lietuva', 'Vokietija', 'Švedija', 'Norvegija', 'Danija', 'Suomija' ) as $country_name ) {
	if ( ! term_exists( $country_name, 'g5_project_country' ) ) {
		wp_insert_term( $country_name, 'g5_project_country' );
	}
}

foreach ( array( '2G', '3G', '4G-LTE', '5G' ) as $technology_name ) {
	if ( ! term_exists( $technology_name, 'g5_project_technology' ) ) {
		wp_insert_term( $technology_name, 'g5_project_technology' );
	}
}

$services = array(
	array(
		'slug'      => 'mobiliojo-rysio-tinklai',
		'category'  => 'Telekomunikacijos',
		'title'     => 'Mobiliojo ryšio tinklai',
		'summary'   => 'Įrengiame, modernizuojame ir prižiūrime mobiliojo ryšio infrastruktūrą pagal operatorių techninius bei darbų saugos standartus.',
		'card_summary' => 'Bazinių stočių diegimas, modernizavimas, integravimas ir priežiūra.',
		'faq'       => array(
			'Kokiuose objektuose atliekate darbus? | Dirbame mobiliųjų operatorių bokštuose, ant pastatų, stiebų, televizijos ir vandens bokštų bei kituose ryšio infrastruktūros objektuose.',
			'Ar parengiate projekto dokumentaciją? | Taip. Dokumentacijos apimtį ir formatą suderiname pagal konkretaus užsakovo bei projekto reikalavimus.',
			'Ar galite atlikti tik dalį projekto darbų? | Taip. Galime prisijungti prie atskiro projekto etapo arba įgyvendinti visą sutartą darbų apimtį.',
		),
		'image'     => $tower_id,
		'work'      => array(
			'2G, 3G, 4G-LTE ir 5G ryšio įrangos montavimas bei modernizavimas',
			'Antenų, radijo modulių ir metalinių konstrukcijų montavimas',
			'Bangolaidžių, fiderių ir kabelių instaliavimas',
			'„SiteMaster“ matavimai ir bangolaidžių kokybės testavimas',
			'Radiorelinių linijų įrengimas, konfigūravimas ir testavimas',
			'Duomenų perdavimo sistemų diegimas',
			'Objekto ir įrangos techninis įvertinimas („Site Survey“)',
			'Dokumentacijos parengimas ir perdavimas užsakovui',
		),
		'equipment' => array( 'Ericsson', 'Nokia', 'Huawei', 'ZTE', 'Eltek', 'Delta' ),
	),
	array(
		'slug'      => 'vidinio-rysio-tinklai',
		'category'  => 'Telekomunikacijos',
		'title'     => 'Vidinio ryšio tinklai',
		'summary'   => 'Projektuojame ir įrengiame mobiliojo bei belaidžio ryšio sprendimus dideliuose ir techniškai sudėtinguose pastatuose.',
		'card_summary' => 'Ryšio sprendimai dideliuose ir techniškai sudėtinguose pastatuose.',
		'image'     => $team_id,
		'work'      => array(
			'Vidaus mobiliojo ryšio tinklų projektavimas',
			'Vidinių antenų ir signalo stiprinimo sistemų montavimas',
			'Wi-Fi infrastruktūros diegimas pastatuose',
			'Ryšio sprendimai prekybos centruose, ligoninėse ir viešbučiuose',
			'Sprendimai tuneliuose, požeminėse aikštelėse ir kituose objektuose',
			'Sistemos matavimai, optimizavimas ir testavimas',
			'Techninės dokumentacijos rengimas',
			'Įrengto sprendimo perdavimas užsakovui',
		),
		'equipment' => array( 'Ericsson', 'Nokia', 'Huawei', 'CommScope', 'ZTE', 'SiteMaster' ),
	),
	array(
		'slug'      => 'fiksuoto-rysio-tinklai',
		'category'  => 'Telekomunikacijos',
		'title'     => 'Fiksuoto ryšio tinklai',
		'summary'   => 'Įrengiame šviesolaidinio ir varinio ryšio infrastruktūrą nuo magistralinių linijų iki galutinio vartotojo prijungimo.',
		'card_summary' => 'Šviesolaidinio ir varinio ryšio infrastruktūros įrengimas.',
		'image'     => $tower_id,
		'work'      => array(
			'Magistralinių ir skirstomųjų ryšio linijų projektavimas',
			'Šviesolaidinių kabelių montavimas',
			'Varinių kabelių montavimas ir priežiūra',
			'Optinių kabelių virinimas ir movų montavimas',
			'Ryšio linijų testavimas ir gedimų diagnostika',
			'Vartotojų paslaugų diegimas ir prijungimas',
			'ODF spintų įrengimas centralėse',
			'Dokumentacijos parengimas ir tinklo perdavimas užsakovui',
		),
		'equipment' => array( 'Optiniai tinklai', 'ODF', 'OTDR', 'Šviesolaidis', 'Variniai kabeliai', 'Testavimo įranga' ),
	),
	array(
		'slug'      => 'elektros-darbai',
		'category'  => 'Energetika',
		'title'     => 'Elektros darbai',
		'summary'   => 'Projektuojame, įrengiame ir prižiūrime elektros sistemas naujuose bei rekonstruojamuose objektuose.',
		'card_summary' => 'Elektros sistemų projektavimas, montavimas, bandymai ir priežiūra.',
		'image'     => $team_id,
		'work'      => array(
			'Vidaus elektros instaliacijos montavimas',
			'Vidaus ir lauko apšvietimo sistemų įrengimas',
			'Elektros tinklų ir įvadų projektavimas',
			'Elektros įrangos montavimas ir prijungimas',
			'Elektrinio šildymo sistemų įrengimas',
			'Profilaktinė elektros sistemų priežiūra',
			'Varžų matavimai ir bandymai',
			'Dokumentacijos parengimas tinklams perduoti ESO',
		),
		'equipment' => array( 'Sonel', 'Eltek', 'Delta', 'FIAMM', 'Enersys', 'Matavimo įranga' ),
	),
	array(
		'slug'      => 'apsaugos-ir-stebejimo-sistemos',
		'category'  => 'Inžinerinės sistemos',
		'title'     => 'Apsaugos ir stebėjimo sistemos',
		'summary'   => 'Projektuojame, montuojame ir integruojame vaizdo stebėjimo, signalizacijos bei patekimo kontrolės sprendimus.',
		'card_title' => 'Apsaugos sistemos',
		'card_summary' => 'Stebėjimo, signalizacijos ir patekimo kontrolės sprendimai.',
		'image'     => $tower_id,
		'work'      => array(
			'IP, HD-SDI ir analoginių kamerų sistemų diegimas',
			'Apsaugos ir gaisro signalizacijos montavimas',
			'Perimetro apsaugos sprendimai',
			'Patekimo kontrolės sistemos',
			'Telefonspynių ir vaizdo telefonspynių įrengimas',
			'Automobilių numerių atpažinimo sistemos',
			'Stebėjimo postų ir vaizdo archyvavimo sprendimai',
			'Dokumentacijos parengimas eksploatacijai',
		),
		'equipment' => array( 'IP CCTV', 'Patekimo kontrolė', 'Gaisro signalizacija', 'Perimetro apsauga', 'LPR', 'Vaizdo archyvai' ),
	),
	array(
		'slug'      => 'saules-elektrines',
		'category'  => 'Atsinaujinanti energetika',
		'title'     => 'Saulės elektrinės',
		'summary'   => 'Projektuojame, montuojame ir prižiūrime saulės elektrines verslo, infrastruktūros ir gyvenamuosiuose objektuose.',
		'card_summary' => 'Projektavimas, konstrukcijos, montavimas, paleidimas ir priežiūra.',
		'image'     => $team_id,
		'work'      => array(
			'Techninis sprendimo įvertinimas ir projektavimas',
			'Saulės modulių montavimas',
			'Inverterių montavimas ir prijungimas',
			'Konstrukcijų įrengimas ant stogų',
			'Antžeminių konstrukcijų įrengimas',
			'Elektrinės testavimas ir paleidimas',
			'Techninė priežiūra ir gedimų diagnostika',
			'Dokumentacijos parengimas atsakingoms institucijoms',
		),
		'equipment' => array( 'Saulės moduliai', 'Inverteriai', 'Konstrukcijos', 'Apsaugos įranga', 'Matavimo įranga', 'Stebėsena' ),
	),
);

$service_ids = array();
foreach ( $services as $index => $service ) {
	$service_id = g5tech_local_service(
		$service,
		$catalog_ids,
		$service['image'],
		$index
	);
	$service_ids[ $service['slug'] ] = $service_id;

	foreach ( $service['faq'] ?? array() as $faq_order => $faq_row ) {
		$faq_parts = array_map( 'trim', explode( '|', $faq_row, 2 ) );

		if ( 2 === count( $faq_parts ) && $faq_parts[0] && $faq_parts[1] ) {
			g5tech_local_faq(
				$faq_parts[0],
				$faq_parts[1],
				array( $service_id ),
				$faq_order
			);
		}
	}
}

$projects = array(
	array(
		'title'   => 'Bazinių stočių modernizavimo projektas Vokietijoje',
		'slug'    => 'baziniu-stociu-modernizavimas-vokietijoje',
		'country' => array( 'Vokietija' ),
		'service' => 'mobiliojo-rysio-tinklai',
		'summary' => 'Darbai apima ryšio įrangos montavimą, testavimą ir dokumentaciją.',
		'scope'   => array(
			'Ryšio įrangos montavimas',
			'Sumontuotos įrangos testavimas',
			'Atliktų darbų dokumentacija',
		),
		'result'  => 'Sumontuota įranga testuojama, o rezultatai dokumentuojami ir perduodami užsakovui.',
	),
);

foreach ( $projects as $index => $project ) {
	g5tech_local_project( $project, $service_ids, $tower_id, $index );
}

$candidate_faq_groups = array(
	'start' => array(
		array( 'Ar būtina ankstesnė patirtis telekomunikacijose?', 'Patirtis yra privalumas, tačiau pradinio lygio pozicijose svarbiausia atsakingas požiūris, motyvacija ir noras mokytis.' ),
		array( 'Ar reikalingi sertifikatai?', 'Turimi sertifikatai yra privalumas. Darbui reikalingus aukštalipio, elektrosaugos, pirmosios pagalbos ir kitus mokymus gali apmokėti įmonė.' ),
		array( 'Kiek trunka mokymai pradėjus dirbti?', 'Nuo kelių dienų iki kelių savaičių – priklauso nuo patirties ir konkretaus projekto.' ),
		array( 'Ar svarbi anglų kalba?', 'Ji yra privalumas, nes dalis techninės informacijos pateikiama anglų kalba. Komandoje bendraujama lietuviškai.' ),
		array( 'Ar būtinas vairuotojo pažymėjimas?', 'B kategorija dažnai reikalinga darbui projektuose, tačiau kiekviena situacija vertinama individualiai.' ),
	),
	'travel' => array(
		array( 'Kiek trunka komandiruotės?', 'Įprastai 6–8 savaites. Trukmė ir grįžimo laikotarpiai derinami pagal projekto sąlygas.' ),
		array( 'Kuriose šalyse vyksta projektai?', 'Lietuvoje, Vokietijoje, Švedijoje, Norvegijoje, Danijoje, Suomijoje ir kitose Europos rinkose.' ),
		array( 'Kas organizuoja apgyvendinimą?', 'Apgyvendinimą organizuoja ir apmoka įmonė.' ),
		array( 'Ar apmokamos kelionės ir transportas?', 'Taip. Su projektu susijusias keliones, transportą, degalus ir kelių mokesčius apmoka įmonė.' ),
		array( 'Kokią darbo sutartį pasirašau?', 'Pasirašoma Lietuvoje galiojanti darbo sutartis su Lietuvos socialinėmis garantijomis.' ),
	),
	'safety' => array(
		array( 'Kaip užtikrinamas saugumas dirbant aukštyje?', 'Naudojama sertifikuota ir reguliariai tikrinama įranga, vykdomi mokymai, o saugos reikalavimų pažeidimams taikoma nulinė tolerancija.' ),
		array( 'Ar suteikiami darbo rūbai ir apsaugos priemonės?', 'Taip. Suteikiama sezoninė darbo apranga, specializuota avalynė ir visos darbui reikalingos apsaugos priemonės.' ),
		array( 'Kaip įranga pakeliama į aukštį?', 'Naudojamos kėlimo gervės, virvės, o prireikus – kranai.' ),
		array( 'Kur vyksta darbai aukštyje?', 'Ryšio bokštuose, ant pastatų stogų, pramoninių konstrukcijų ir kituose infrastruktūros objektuose.' ),
	),
	'daily' => array(
		array( 'Ar dirbama individualiai, ar komandoje?', 'Priklausomai nuo projekto, darbai atliekami individualiai arba 2–3 specialistų komandoje.' ),
		array( 'Kaip atrodo tipinė darbo diena?', 'Komanda paruošia medžiagas, objekte atlieka saugos įvertinimą, numatytus darbus ir dokumentuoja rezultatus.' ),
		array( 'Ką daryti kilus techniniam klausimui?', 'Projektų vykdytojai ir patyrę komandos nariai padeda spręsti techninius klausimus projekto metu.' ),
		array( 'Ar galima keisti projektą ar komandą?', 'Dėl projekto ar komandos keitimo tariamasi su tiesioginiu arba projekto vadovu.' ),
	),
);
$candidate_faq_order  = 0;

foreach ( $candidate_faq_groups as $group => $faqs ) {
	foreach ( $faqs as $faq ) {
		g5tech_local_faq( $faq[0], $faq[1], array(), $candidate_faq_order, 'candidate', $group );
		$candidate_faq_order++;
	}
}

$team_members = array(
	array(
		'name'             => 'Aleksandras Iljinas',
		'slug'             => 'aleksandras-iljinas',
		'role'             => 'Generalinis direktorius',
		'email'            => 'Aleksandras@5gtech.lt',
		'phone'            => '+370 687 77155',
		'experience_since' => '2012',
		'company_since'    => '2020',
		'primary_area'     => 'Mobiliojo ryšio infrastruktūra',
		'responsibility'   => 'Projektų ir komandos koordinavimas',
		'summary'          => 'Telekomunikacijų srityje nuo 2012 m. Vadovauja 5G TECH komandai ir koordinuoja ryšio infrastruktūros projektus Lietuvoje bei kitose Europos šalyse.',
		'experience'       => 'Aleksandras dirbo ir vadovavo mobiliojo ryšio infrastruktūros diegimo, modernizavimo bei priežiūros projektams. Jo patirtis apima darbų planavimą, komandų koordinavimą, techninių sprendimų derinimą, kokybės kontrolę ir projektų perdavimą.',
		'countries'        => array( 'Lietuva', 'Vokietija', 'Švedija', 'Norvegija', 'Danija', 'Suomija' ),
		'operators'        => array( 'Telia', 'Deutsche Telekom', 'Vodafone', 'Telefónica / O2', 'Telenor', 'Tele2', 'Bitė', 'Elisa', 'TDC', '1&1', '450connect' ),
		'competencies'     => array(
			'Mobiliojo ryšio infrastruktūra',
			'2G–5G modernizavimas',
			'Projektų planavimas',
			'Komandų koordinavimas',
			'Techninių sprendimų derinimas',
			'Kokybės kontrolė',
			'Dokumentacija ir perdavimas',
		),
		'show_profile'     => true,
	),
	array(
		'name'  => 'Nerijus Bazinas',
		'slug'  => 'nerijus-bazinas',
		'role'  => 'Projektų vadovas',
		'email' => 'Nerijus@5gtech.lt',
		'phone' => '+370 605 47198',
	),
	array(
		'name'  => 'Eimantas Žemaitis',
		'slug'  => 'eimantas-zemaitis',
		'role'  => 'Projektų vykdytojas',
		'email' => 'Eimantas@5gtech.lt',
		'phone' => '+370 662 93845',
	),
	array(
		'name'  => 'Tadas Grabauskas',
		'slug'  => 'tadas-grabauskas',
		'role'  => 'Projektų vykdytojas',
		'email' => 'Tadas@5gtech.lt',
		'phone' => '+370 660 89077',
	),
	array(
		'name'  => 'Kristina Naginevičienė',
		'slug'  => 'kristina-nagineviciene',
		'role'  => 'Administracijos ir personalo koordinatorė',
		'email' => 'Kristina@5gtech.lt',
		'phone' => '+370 665 51934',
	),
);

foreach ( $team_members as $index => $person ) {
	g5tech_local_team_member( $person, $operator_ids, $index );
}

$jobs = array(
	array(
		'title'            => 'Telekomunikacijų specialistas (-ė)',
		'slug'             => 'telekomunikaciju-specialistas',
		'group'            => 'europe',
		'level'            => 'Pradinis lygis',
		'location'         => 'Europa',
		'salary'           => '2 500–2 750+ €',
		'rotation'         => '6–8 sav.',
		'driving'          => 'B',
		'summary'          => 'Darbas mobiliojo ryšio infrastruktūros montavimo projektuose Vokietijoje, Švedijoje, Norvegijoje ir kitose Europos šalyse.',
		'responsibilities' => array(
			'Montuosite antenas ir radijo modulius',
			'Instaliuosite kabelius ir kitą infrastruktūrą',
			'Padėsite atlikti testavimo darbus',
			'Laikysitės techninių ir saugos reikalavimų',
			'Pildysite atliktų darbų informaciją',
			'Dirbsite kartu su patyrusiu komandos nariu',
		),
		'requirements'     => array(
			'Atsakingo požiūrio į darbų saugą',
			'Noro mokytis techninio darbo',
			'Galimybės dirbti komandiruočių principu',
			'B kategorijos vairuotojo pažymėjimo',
			'Anglų kalbos pagrindų – privalumas',
		),
		'offer'            => array(
			'Darbo sutartį Lietuvoje ir socialines garantijas',
			'Apmokamą apgyvendinimą ir keliones',
			'Darbo aprangą, įrankius ir saugos priemones',
			'Darbui reikalingus mokymus ir kvalifikacijas',
			'Mentoriaus palaikymą projekto pradžioje',
			'Galimybę augti pagal Grow2Go modelį',
		),
	),
);

foreach ( $jobs as $index => $job ) {
	g5tech_local_job( $job, $index );
}

$news_posts = array(
	array(
		'title'    => 'Bazinių stočių modernizavimo projektas Vokietijoje',
		'slug'     => 'baziniu-stociu-modernizavimo-projektas-vokietijoje',
		'category' => 'Projektai',
		'excerpt'  => 'Darbai apima įrangos montavimą, testavimą ir dokumentaciją.',
		'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Projekto kontekstas</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Darbai vykdomi pagal operatoriaus techninius reikalavimus. Komanda atsakinga už įrangos montavimą, testavimą ir atliktų darbų dokumentaciją.</p><!-- /wp:paragraph --><!-- wp:heading --><h2 class="wp-block-heading">Darbų eiga</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Prieš darbų pradžią suderinama techninė užduotis ir pasirengimas objektui. Sumontuota įranga testuojama, o rezultatai dokumentuojami ir perduodami užsakovui.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'    => 'Praktiniai darbuotojų mokymai',
		'slug'     => 'praktiniai-darbuotoju-mokymai',
		'category' => 'Komanda',
		'excerpt'  => 'Mokymuose daugiausia dėmesio skiriame darbų saugai, įrangai ir techninių užduočių vykdymui.',
		'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Praktinis pasirengimas</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Komandos mokymuose deriname darbų saugos reikalavimus, techninių užduočių analizę ir darbą su projektuose naudojama įranga.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'    => 'Projektai šešiose Europos šalyse',
		'slug'     => 'projektai-sesiose-europos-salyse',
		'category' => 'Įmonė',
		'excerpt'  => 'Lietuva, Vokietija, Švedija, Norvegija, Danija ir Suomija.',
		'content'  => '<!-- wp:heading --><h2 class="wp-block-heading">Tarptautinė projektų patirtis</h2><!-- /wp:heading --><!-- wp:paragraph --><p>5G TECH komanda yra vykdžiusi telekomunikacijų ir infrastruktūros projektus Lietuvoje, Vokietijoje, Švedijoje, Norvegijoje, Danijoje ir Suomijoje.</p><!-- /wp:paragraph -->',
	),
);

foreach ( $news_posts as $index => $news_post ) {
	g5tech_local_news_post( $news_post, $index );
}

flush_rewrite_rules();

WP_CLI::success( 'Bandomoji svetainė ir visos paslaugos paruoštos.' );
WP_CLI::log( 'Paslaugos: ' . get_post_type_archive_link( 'g5_service' ) );
WP_CLI::log( 'Administravimas: ' . admin_url( 'edit.php?post_type=g5_service' ) );
