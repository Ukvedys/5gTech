<?php
/**
 * Komandos katalogas, „Apie mus“ puslapis ir individualus profilis.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_team_type() {
	register_post_type(
		'g5_team',
		array(
			'labels' => array(
				'name'               => 'Komanda',
				'singular_name'      => 'Komandos narys',
				'add_new'            => 'Pridėti žmogų',
				'add_new_item'       => 'Pridėti komandos narį',
				'edit_item'          => 'Redaguoti komandos narį',
				'new_item'           => 'Naujas komandos narys',
				'view_item'          => 'Peržiūrėti profilį',
				'search_items'       => 'Ieškoti komandoje',
				'not_found'          => 'Komandos narių nerasta',
				'not_found_in_trash' => 'Šiukšlinėje komandos narių nėra',
				'menu_name'          => 'Komanda',
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'komanda',
				'with_front' => false,
			),
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-groups',
			'menu_position' => 23,
			'supports'      => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'g5tech_register_team_type' );

function g5tech_team_fields() {
	return array(
		'g5_team_role' => array(
			'label' => 'Pareigos',
			'type'  => 'text',
		),
		'g5_team_summary' => array(
			'label'       => 'Profesinis pristatymas',
			'type'        => 'textarea',
			'description' => '2–3 sakiniai profilio pradžiai.',
		),
		'g5_team_email' => array(
			'label' => 'El. paštas',
			'type'  => 'email',
		),
		'g5_team_phone' => array(
			'label' => 'Telefonas',
			'type'  => 'text',
		),
		'g5_team_experience_since' => array(
			'label'       => 'Profesinė patirtis nuo',
			'type'        => 'text',
			'description' => 'Pvz. 2012',
		),
		'g5_team_company_since' => array(
			'label'       => '5G TECH komandoje nuo',
			'type'        => 'text',
			'description' => 'Pvz. 2020',
		),
		'g5_team_primary_area' => array(
			'label' => 'Pagrindinė kryptis',
			'type'  => 'text',
		),
		'g5_team_responsibility' => array(
			'label' => 'Pagrindinė atsakomybė',
			'type'  => 'text',
		),
		'g5_team_experience' => array(
			'label'       => 'Patirties aprašymas',
			'type'        => 'textarea',
			'description' => 'Trumpai aprašykite projektus ir atsakomybes.',
		),
		'g5_team_countries' => array(
			'label'       => 'Šalys',
			'type'        => 'textarea',
			'description' => 'Viena šalis vienoje eilutėje.',
		),
		'g5_team_competencies' => array(
			'label'       => 'Kompetencijos',
			'type'        => 'textarea',
			'description' => 'Viena kompetencija vienoje eilutėje.',
		),
	);
}

function g5tech_add_team_meta_box() {
	add_meta_box(
		'g5tech-team-details',
		'Profesinė informacija',
		'g5tech_render_team_meta_box',
		'g5_team',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_team_meta_box' );

function g5tech_render_team_meta_box( $post ) {
	$operator_ids = array_map( 'absint', get_post_meta( $post->ID, 'g5_team_operators', false ) );
	$operators    = function_exists( 'g5tech_get_partners' )
		? g5tech_get_partners( 'operator' )
		: array();
	$show_contact = metadata_exists( 'post', $post->ID, 'g5_team_show_contact' )
		? (bool) get_post_meta( $post->ID, 'g5_team_show_contact', true )
		: true;
	$show_profile = (bool) get_post_meta( $post->ID, 'g5_team_show_profile', true );

	wp_nonce_field( 'g5tech_save_team', 'g5tech_team_nonce' );

	echo '<p class="description">Vardas ir pavardė įrašomi puslapio viršuje, nuotrauka įkeliama skiltyje „Specialusis paveikslėlis“.</p>';

	foreach ( g5tech_team_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<div style="margin:0 0 20px;">';
		printf(
			'<label for="%1$s" style="display:block;font-weight:600;margin-bottom:6px;">%2$s</label>',
			esc_attr( $key ),
			esc_html( $field['label'] )
		);

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea class="large-text" id="%1$s" name="%1$s" rows="%2$d">%3$s</textarea>',
				esc_attr( $key ),
				in_array( $key, array( 'g5_team_countries', 'g5_team_competencies' ), true ) ? 5 : 4,
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input class="regular-text" type="%1$s" id="%2$s" name="%2$s" value="%3$s">',
				esc_attr( $field['type'] ),
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		if ( ! empty( $field['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $field['description'] ) );
		}

		echo '</div>';
	}

	if ( $operators ) {
		echo '<fieldset style="margin:24px 0;">';
		echo '<legend><strong>Operatorių projektų patirtis</strong></legend>';

		foreach ( $operators as $operator ) {
			printf(
				'<label style="display:inline-block;margin:10px 18px 0 0;"><input type="checkbox" name="g5_team_operators[]" value="%1$d" %2$s> %3$s</label>',
				(int) $operator->ID,
				checked( in_array( (int) $operator->ID, $operator_ids, true ), true, false ),
				esc_html( get_the_title( $operator ) )
			);
		}

		echo '</fieldset>';
	}

	?>
	<p>
		<label>
			<input type="checkbox" name="g5_team_show_contact" value="1" <?php checked( $show_contact ); ?>>
			<strong>Viešai rodyti el. paštą ir telefoną</strong>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" name="g5_team_show_profile" value="1" <?php checked( $show_profile ); ?>>
			<strong>Rodyti nuorodą į išsamų profilį</strong>
		</label>
	</p>
	<?php
}

function g5tech_save_team_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_team_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_team_nonce'] ) ),
			'g5tech_save_team'
		)
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( g5tech_team_fields() as $key => $field ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';

		if ( 'email' === $field['type'] ) {
			$value = sanitize_email( $value );
		} elseif ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}

		update_post_meta( $post_id, $key, $value );
	}

	update_post_meta( $post_id, 'g5_team_show_contact', isset( $_POST['g5_team_show_contact'] ) ? '1' : '0' );
	update_post_meta( $post_id, 'g5_team_show_profile', isset( $_POST['g5_team_show_profile'] ) ? '1' : '0' );
	delete_post_meta( $post_id, 'g5_team_operators' );

	$operator_ids = isset( $_POST['g5_team_operators'] )
		? array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['g5_team_operators'] ) ) ) )
		: array();

	foreach ( $operator_ids as $operator_id ) {
		add_post_meta( $post_id, 'g5_team_operators', $operator_id );
	}
}
add_action( 'save_post_g5_team', 'g5tech_save_team_meta' );

function g5tech_team_lines( $post_id, $key ) {
	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $post_id, $key, true ) )
			)
		)
	);
}

function g5tech_get_team_members() {
	return get_posts(
		array(
			'post_type'      => 'g5_team',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
}

function g5tech_about_sections() {
	return array(
		'story'      => 'Kas esame',
		'purpose'    => 'Misija ir vizija',
		'values'     => 'Mūsų vertybės',
		'team'       => 'Pagrindinė komanda',
		'strategy'   => 'Kokybiškas ir tvarus augimas',
		'competence' => 'Kompetencijos',
	);
}

function g5tech_about_section_order() {
	$defaults = array_combine(
		array_keys( g5tech_about_sections() ),
		range( 1, count( g5tech_about_sections() ) )
	);
	$saved    = get_option( 'g5tech_about_section_order', array() );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	return wp_parse_args( $saved, $defaults );
}

function g5tech_about_content_defaults() {
	return array(
		'hero_eyebrow'             => 'Apie 5G TECH',
		'hero_title'               => '5G TECH komanda ir veiklos kryptys.',
		'hero_lead'                => 'Nuo 2020 metų įgyvendiname telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektus Lietuvoje bei kitose Europos šalyse.',
		'story_eyebrow'            => 'Kas esame',
		'story_title'              => 'Nuo planavimo iki dokumentacijos.',
		'story_lead'               => 'Veiklą pradėjome nuo telekomunikacijų infrastruktūros projektų. Šiandien sukauptą techninę patirtį taikome ir energetikos bei kitose inžinerinės infrastruktūros srityse.',
		'story_body_1'             => 'Dirbame su skirtingos apimties užduotimis: nuo konkretaus montavimo ar modernizavimo etapo iki darbų, kuriuose reikia suvaldyti planavimą, techninius sprendimus, rizikas, testavimą ir dokumentaciją.',
		'story_body_2'             => 'Prieš pradėdami susitariame dėl darbų eigos, atsakomybių ir rezultato vertinimo. Atliktus darbus testuojame bei dokumentuojame, o saugą, kokybę ir terminus valdome viso projekto metu.',
		'story_body_3'             => 'Procesai grindžiami ISO 9001, ISO 14001 ir ISO 45001 standartais. Įmonė taip pat turi SSVA ypatingojo statinio rangovo kvalifikacijos atestatą.',
		'story_fact_1_label'       => '01 / Pradžia',
		'story_fact_1_title'       => 'Įsigiliname prieš pradėdami.',
		'story_fact_1_text'        => 'Techninė užduotis, objekto sąlygos, terminai ir atsakomybės.',
		'story_fact_2_label'       => '02 / Vykdymas',
		'story_fact_2_title'       => 'Dirbame pagal aiškią eigą.',
		'story_fact_2_text'        => 'Komanda, įranga, saugos reikalavimai ir tiesioginis ryšys.',
		'story_fact_3_label'       => '03 / Rezultatas',
		'story_fact_3_title'       => 'Patikriname ir dokumentuojame.',
		'story_fact_3_text'        => 'Testavimo duomenys, neatitikimų sprendimas ir aiškus perdavimas.',
		'story_image_1_id'         => '0',
		'story_image_1_caption'    => '5G TECH komanda',
		'story_image_2_id'         => '0',
		'story_image_2_caption'    => 'Atsakomybė turi vardą',
		'purpose_eyebrow'          => 'Kryptis',
		'purpose_title'            => 'Misija ir vizija.',
		'mission_label'            => 'Misija',
		'mission_title'            => 'Patikimai įgyvendinti infrastruktūros projektus.',
		'mission_text'             => 'Aiški darbų eiga, saugus vykdymas ir patikrintas rezultatas.',
		'vision_label'             => 'Vizija',
		'vision_title'             => 'Būti pirmu pasirinkimu telekomunikacijų ir energetikos projektams.',
		'vision_text'              => 'Augti Europos rinkose stiprinant komandą ir technines kompetencijas.',
		'values_eyebrow'           => 'Mūsų vertybės',
		'values_title'             => 'Principai, kuriais vadovaujamės kasdieniame darbe.',
		'value_1_number'           => '01',
		'value_1_title'            => 'Atsakomybė',
		'value_1_text'             => 'Prisiimame atsakomybę už sutartą darbų apimtį, terminus ir rezultatą.',
		'value_2_number'           => '02',
		'value_2_title'            => 'Profesionalumas',
		'value_2_text'             => 'Laikomės susitarimų, dirbame tiksliai ir sprendžiame problemas nelaukdami.',
		'value_3_number'           => '03',
		'value_3_title'            => 'Pagarba',
		'value_3_text'             => 'Atvirai bendraujame su klientais, partneriais ir kolegomis.',
		'value_4_number'           => '04',
		'value_4_title'            => 'Prasmė',
		'value_4_text'             => 'Kuriame infrastruktūrą, kuria kasdien naudojasi žmonės ir organizacijos.',
		'culture_label'            => 'Darbo kultūra',
		'culture_title'            => 'Elgesio kodeksas.',
		'culture_text'             => 'Jis apibrėžia profesinio elgesio, atsakomybės, saugos, darbo drausmės ir bendradarbiavimo principus.',
		'culture_button_label'     => 'Peržiūrėti kodeksą ↗',
		'culture_url'              => 'https://5gtech.lt/wp-content/uploads/2025/04/5GTech_Kodeksas_Elgesys_Etika_2025_V1.1.pdf',
		'strategy_eyebrow'         => 'Strategija 2025–2028',
		'strategy_title'           => 'Kokybiškas ir tvarus augimas.',
		'strategy_lead'            => 'Siekiame stiprinti telekomunikacijų ir atsinaujinančios energetikos kompetencijas, plėsti veiklą Europoje ir nuosekliai gerinti darbo procesus.',
		'strategy_1_label'         => '01 / KOKYBĖ',
		'strategy_1_title'         => 'Vienodi procesai visuose projektuose.',
		'strategy_1_text'          => 'Tobuliname darbų planavimo, kokybės kontrolės ir dokumentavimo metodiką.',
		'strategy_2_label'         => '02 / EUROPA',
		'strategy_2_title'         => 'Daugiau projektų Europos rinkose.',
		'strategy_2_text'          => 'Plečiame komandos patirtį dirbdami pagal skirtingų šalių operatorių reikalavimus.',
		'strategy_3_label'         => '03 / KOMANDA',
		'strategy_3_title'         => 'Mokymai, sauga ir profesinis augimas.',
		'strategy_3_text'          => 'Investuojame į darbui reikalingas kvalifikacijas ir praktinį komandos pasirengimą.',
		'strategy_4_label'         => '04 / SANTYKIS',
		'strategy_4_title'         => 'Aiškūs įsipareigojimai ir tiesioginis ryšys.',
		'strategy_4_text'          => 'Klientas viso projekto metu žino darbų būklę, atsakomybes ir kitus veiksmus.',
		'team_eyebrow'             => 'Komanda',
		'team_title'               => 'Pagrindinė komanda.',
		'competence_eyebrow'       => 'Kompetencija',
		'competence_title'         => 'Patirtis, kvalifikacijos ir tiesioginiai kontaktai.',
		'competence_1_number'      => '01',
		'competence_1_title'       => 'Srities patirtis',
		'competence_1_text'        => 'Darbo metai, atsakomybės ir projektų tipai.',
		'competence_2_number'      => '02',
		'competence_2_title'       => 'Geografija ir operatoriai',
		'competence_2_text'        => 'Šalys ir operatorių projektai, kuriuose žmogus dalyvavo.',
		'competence_3_number'      => '03',
		'competence_3_title'       => 'Kvalifikacijos',
		'competence_3_text'        => 'Galiojantys sertifikatai, atestatai ir darbų vadovo teisės.',
		'competence_4_number'      => '04',
		'competence_4_title'       => 'Kontaktas',
		'competence_4_text'        => 'Tiesioginis ryšys su už projektą atsakingu žmogumi.',
		'cta_eyebrow'              => 'Aptarkime projektą',
		'cta_title'                => 'Aptarkime, kokios komandos reikia jūsų projektui.',
		'cta_text'                 => 'Papasakokite apie projekto apimtį, techninius reikalavimus ir numatytus terminus.',
		'cta_button_label'         => 'Aptarkime jūsų projektą',
	);
}

function g5tech_about_content() {
	$saved = get_option( 'g5tech_about_content', array() );

	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	$content = wp_parse_args( $saved, g5tech_about_content_defaults() );

	$repeaters = array(
		'story_facts' => array(
			'prefix' => 'story_fact_',
			'count'  => 3,
			'fields' => array( 'label' => 'label', 'title' => 'title', 'text' => 'text' ),
		),
		'values' => array(
			'prefix' => 'value_',
			'count'  => 4,
			'fields' => array( 'title' => 'title', 'text' => 'text' ),
		),
		'strategies' => array(
			'prefix' => 'strategy_',
			'count'  => 4,
			'fields' => array( 'label' => 'label', 'title' => 'title', 'text' => 'text' ),
		),
		'competences' => array(
			'prefix' => 'competence_',
			'count'  => 4,
			'fields' => array( 'title' => 'title', 'text' => 'text' ),
		),
	);

	foreach ( $repeaters as $key => $config ) {
		if ( ! array_key_exists( $key, $saved ) || ! is_array( $saved[ $key ] ) ) {
			$content[ $key ] = g5tech_legacy_repeater_items(
				$content,
				$config['prefix'],
				$config['count'],
				$config['fields']
			);
		}
	}

	return $content;
}

function g5tech_about_repeater_configs() {
	return array(
		'story' => array(
			'key'         => 'story_facts',
			'add_label'   => 'Pridėti faktą',
			'empty_label' => 'Faktų dar nėra.',
			'schema'      => array(
				'label' => array( 'label' => 'Trumpa žyma', 'type' => 'text', 'description' => 'Numeris pridedamas automatiškai.' ),
				'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
				'text'  => array( 'label' => 'Tekstas', 'type' => 'textarea' ),
			),
		),
		'values' => array(
			'key'         => 'values',
			'add_label'   => 'Pridėti vertybę',
			'empty_label' => 'Vertybių dar nėra.',
			'schema'      => array(
				'title' => array( 'label' => 'Vertybė', 'type' => 'text' ),
				'text'  => array( 'label' => 'Trumpas paaiškinimas', 'type' => 'textarea' ),
			),
		),
		'strategy' => array(
			'key'         => 'strategies',
			'add_label'   => 'Pridėti kryptį',
			'empty_label' => 'Strateginių krypčių dar nėra.',
			'schema'      => array(
				'label' => array( 'label' => 'Trumpa žyma', 'type' => 'text', 'description' => 'Numeris pridedamas automatiškai.' ),
				'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
				'text'  => array( 'label' => 'Tekstas', 'type' => 'textarea' ),
			),
		),
		'competence' => array(
			'key'         => 'competences',
			'add_label'   => 'Pridėti kompetencijos punktą',
			'empty_label' => 'Kompetencijos punktų dar nėra.',
			'schema'      => array(
				'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
				'text'  => array( 'label' => 'Tekstas', 'type' => 'textarea' ),
			),
		),
	);
}

function g5tech_about_content_value( $key ) {
	$content = g5tech_about_content();

	return isset( $content[ $key ] ) ? $content[ $key ] : '';
}

function g5tech_about_content_fields() {
	return array(
		'Puslapio pradžia' => array(
			'hero_eyebrow' => array( 'label' => 'Trumpa antraštė', 'type' => 'text' ),
			'hero_title'    => array( 'label' => 'Pagrindinė antraštė', 'type' => 'text' ),
			'hero_lead'     => array( 'label' => 'Įžanginis tekstas', 'type' => 'textarea' ),
		),
		'Kas esame' => array(
			'story_eyebrow'         => array( 'label' => 'Sekcijos žyma', 'type' => 'text' ),
			'story_title'           => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'story_lead'            => array( 'label' => 'Įžanga', 'type' => 'textarea' ),
			'story_body_1'          => array( 'label' => 'Pirma pastraipa', 'type' => 'textarea' ),
			'story_body_2'          => array( 'label' => 'Antra pastraipa', 'type' => 'textarea' ),
			'story_body_3'          => array( 'label' => 'Trečia pastraipa', 'type' => 'textarea' ),
			'story_image_1_caption' => array( 'label' => 'Pirmos nuotraukos užrašas', 'type' => 'text' ),
			'story_image_2_caption' => array( 'label' => 'Antros nuotraukos užrašas', 'type' => 'text' ),
		),
		'Misija ir vizija' => array(
			'purpose_eyebrow' => array( 'label' => 'Sekcijos žyma', 'type' => 'text' ),
			'purpose_title'    => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'mission_label'    => array( 'label' => 'Misijos žyma', 'type' => 'text' ),
			'mission_title'    => array( 'label' => 'Misijos antraštė', 'type' => 'text' ),
			'mission_text'     => array( 'label' => 'Misijos tekstas', 'type' => 'textarea' ),
			'vision_label'     => array( 'label' => 'Vizijos žyma', 'type' => 'text' ),
			'vision_title'     => array( 'label' => 'Vizijos antraštė', 'type' => 'text' ),
			'vision_text'      => array( 'label' => 'Vizijos tekstas', 'type' => 'textarea' ),
		),
		'Mūsų vertybės' => array(
			'values_eyebrow'       => array( 'label' => 'Sekcijos žyma', 'type' => 'text' ),
			'values_title'         => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'culture_label'        => array( 'label' => 'Kodekso žyma', 'type' => 'text' ),
			'culture_title'        => array( 'label' => 'Kodekso antraštė', 'type' => 'text' ),
			'culture_text'         => array( 'label' => 'Kodekso tekstas', 'type' => 'textarea' ),
			'culture_button_label' => array( 'label' => 'Kodekso mygtukas', 'type' => 'text' ),
			'culture_url'          => array( 'label' => 'Kodekso nuoroda', 'type' => 'url' ),
		),
		'Kokybiškas ir tvarus augimas' => array(
			'strategy_eyebrow' => array( 'label' => 'Sekcijos žyma', 'type' => 'text' ),
			'strategy_title'    => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'strategy_lead'     => array( 'label' => 'Įžanga', 'type' => 'textarea' ),
		),
		'Komanda ir kompetencijos' => array(
			'team_eyebrow'        => array( 'label' => 'Komandos sekcijos žyma', 'type' => 'text' ),
			'team_title'          => array( 'label' => 'Komandos antraštė', 'type' => 'text' ),
			'competence_eyebrow'  => array( 'label' => 'Kompetencijų sekcijos žyma', 'type' => 'text' ),
			'competence_title'    => array( 'label' => 'Kompetencijų antraštė', 'type' => 'text' ),
		),
		'Baigiamasis kvietimas' => array(
			'cta_eyebrow'      => array( 'label' => 'Sekcijos žyma', 'type' => 'text' ),
			'cta_title'        => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'cta_text'         => array( 'label' => 'Tekstas', 'type' => 'textarea' ),
			'cta_button_label' => array( 'label' => 'Mygtuko tekstas', 'type' => 'text' ),
		),
	);
}

function g5tech_add_about_order_page() {
	add_submenu_page(
		'edit.php?post_type=g5_team',
		'Apie mus puslapis',
		'Apie mus turinys',
		'edit_g5_team_members',
		'g5tech-about-order',
		'g5tech_render_about_order_page'
	);
}
add_action( 'admin_menu', 'g5tech_add_about_order_page' );

function g5tech_about_admin_url( $view = 'structure', $section = '' ) {
	$url = add_query_arg(
		array(
			'post_type' => 'g5_team',
			'page'      => 'g5tech-about-order',
		),
		admin_url( 'edit.php' )
	);

	if ( $section ) {
		$section = sanitize_key( $section );
		$url     = add_query_arg( 'section', $section, $url ) . '#' . sanitize_html_class( $section );
	}

	return $url;
}

function g5tech_about_content_group_key( $group_label ) {
	$keys = array(
		'Puslapio pradžia'             => 'hero',
		'Kas esame'                    => 'story',
		'Misija ir vizija'             => 'purpose',
		'Mūsų vertybės'                => 'values',
		'Kokybiškas ir tvarus augimas' => 'strategy',
		'Komanda ir kompetencijos'     => 'competence',
		'Baigiamasis kvietimas'        => 'cta',
	);

	return $keys[ $group_label ] ?? sanitize_title( $group_label );
}

function g5tech_render_about_admin_field( $content, $key, $field ) {
	$value = isset( $content[ $key ] ) ? $content[ $key ] : '';
	?>
	<tr>
		<th scope="row"><label for="g5tech-about-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
		<td>
			<?php if ( 'textarea' === $field['type'] ) : ?>
				<textarea
					class="large-text"
					id="g5tech-about-<?php echo esc_attr( $key ); ?>"
					name="g5tech_about_content[<?php echo esc_attr( $key ); ?>]"
					rows="3"
				><?php echo esc_textarea( $value ); ?></textarea>
			<?php else : ?>
				<input
					class="large-text"
					id="g5tech-about-<?php echo esc_attr( $key ); ?>"
					name="g5tech_about_content[<?php echo esc_attr( $key ); ?>]"
					type="<?php echo esc_attr( $field['type'] ); ?>"
					value="<?php echo esc_attr( $value ); ?>"
				>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function g5tech_render_about_media_field( $content, $key, $label, $fallback_url ) {
	$attachment_id = isset( $content[ $key ] ) ? absint( $content[ $key ] ) : 0;
	$preview_url   = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : $fallback_url;
	?>
	<div class="g5tech-about-media-field">
		<strong><?php echo esc_html( $label ); ?></strong>
		<div class="g5tech-about-media-preview">
			<img src="<?php echo esc_url( $preview_url ); ?>" alt="">
		</div>
		<input type="hidden" id="g5tech-about-<?php echo esc_attr( $key ); ?>" name="g5tech_about_content[<?php echo esc_attr( $key ); ?>]" value="<?php echo (int) $attachment_id; ?>">
		<p>
			<button class="button g5tech-about-media-select" type="button" data-target="g5tech-about-<?php echo esc_attr( $key ); ?>">Pasirinkti nuotrauką</button>
			<button class="button-link-delete g5tech-about-media-remove" type="button" data-target="g5tech-about-<?php echo esc_attr( $key ); ?>" data-fallback="<?php echo esc_url( $fallback_url ); ?>">Naudoti numatytąją</button>
		</p>
	</div>
	<?php
}

function g5tech_render_about_order_page() {
	if ( ! current_user_can( 'edit_g5_team_members' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio puslapio.', '5gtech-core' ) );
	}

	wp_enqueue_media();

	$open_section   = sanitize_key( wp_unslash( $_GET['section'] ?? '' ) );
	$content        = g5tech_about_content();
	$content_fields = g5tech_about_content_fields();
	$repeaters      = g5tech_about_repeater_configs();
	$team_image_1   = get_theme_file_uri( 'assets/images/team/team-work-01.jpg' );
	$team_image_2   = get_theme_file_uri( 'assets/images/team/team-work-02.jpg' );
	?>
	<div class="wrap g5tech-admin-page g5tech-about-admin">
		<?php
		g5tech_render_unified_admin_header(
			array(
				'title'       => 'Apie mus puslapis',
				'description' => 'Viršuje keiskite sekcijų tvarką, žemiau redaguokite jų turinį ir iš karto matykite viešo puslapio peržiūrą.',
				'page_url'    => home_url( '/apie-mus/' ),
				'actions'     => array(
					array(
						'label' => 'Komandos nariai',
						'url'   => admin_url( 'edit.php?post_type=g5_team' ),
					),
				),
			)
		);
		?>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>„Apie mus“ puslapio pakeitimai išsaugoti.</p></div>
		<?php endif; ?>

		<?php g5tech_render_page_module_manager( 'about' ); ?>

		<div class="g5tech-admin-layout">
			<div class="g5tech-admin-editor">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="g5tech_save_about_order">
				<?php wp_nonce_field( 'g5tech_save_about_order' ); ?>
				<?php g5tech_render_admin_content_translation_context( 'about', $content ); ?>

				<div class="g5tech-about-content-groups">
					<?php foreach ( $content_fields as $group_label => $fields ) : ?>
						<?php $group_key = g5tech_about_content_group_key( $group_label ); ?>
						<?php $is_fixed = in_array( $group_key, array( 'hero', 'cta' ), true ); ?>
						<details class="g5tech-admin-group g5tech-about-content-group" id="<?php echo esc_attr( $group_key ); ?>" <?php echo $is_fixed || $group_key === $open_section ? 'open' : ''; ?>>
							<summary>
								<span><?php echo esc_html( $group_label ); ?></span>
								<?php if ( $is_fixed ) : ?><span class="g5tech-admin-group__meta">Fiksuota vieta</span><?php endif; ?>
							</summary>
							<div class="g5tech-admin-group__content">
							<?php if ( 'story' === $group_key ) : ?>
								<div class="g5tech-about-content-group__media">
									<h3>Nuotraukos</h3>
									<div class="g5tech-about-media-grid">
										<?php g5tech_render_about_media_field( $content, 'story_image_1_id', 'Pirma nuotrauka', $team_image_1 ); ?>
										<?php g5tech_render_about_media_field( $content, 'story_image_2_id', 'Antra nuotrauka', $team_image_2 ); ?>
									</div>
								</div>
							<?php endif; ?>
							<table class="form-table" role="presentation">
								<tbody>
									<?php foreach ( $fields as $key => $field ) : ?>
										<?php g5tech_render_about_admin_field( $content, $key, $field ); ?>
									<?php endforeach; ?>
								</tbody>
							</table>
							<?php if ( isset( $repeaters[ $group_key ] ) ) : ?>
								<div class="g5tech-about-content-group__repeater">
									<h3>Elementai</h3>
									<?php
									$config = $repeaters[ $group_key ];
									g5tech_render_repeater(
										array(
											'name'        => 'g5tech_about_content[' . $config['key'] . ']',
											'items'       => $content[ $config['key'] ],
											'schema'      => $config['schema'],
											'add_label'   => $config['add_label'],
											'empty_label' => $config['empty_label'],
											'title_field' => 'title',
										)
									);
									?>
								</div>
							<?php endif; ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<div class="g5tech-admin-actions">
					<?php submit_button( 'Išsaugoti pakeitimus', 'primary', 'submit', false ); ?>
					<a class="button" href="<?php echo esc_url( home_url( '/apie-mus/' ) ); ?>" target="_blank" rel="noopener">Atidaryti puslapį ↗</a>
				</div>
			</form>
			</div>
			<?php g5tech_render_unified_admin_preview( home_url( '/apie-mus/' ), 'Apie mus puslapio' ); ?>
		</div>
	</div>
	<style>
		.g5tech-about-media-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;max-width:820px}
		.g5tech-about-media-field{padding:18px;border:1px solid #dcdcde;background:#fff}
		.g5tech-about-media-preview{height:180px;margin-top:12px;background:#f0f0f1}
		.g5tech-about-media-preview img{width:100%;height:100%;display:block;object-fit:cover}
		.g5tech-about-media-remove{margin-left:12px}
		.g5tech-about-content-group__media{margin-bottom:18px}
		.g5tech-about-content-group__repeater{padding-top:6px}
		.g5tech-about-content-group__media h3{margin:0 0 12px}
		.g5tech-about-content-group .form-table{margin:0}
		.g5tech-about-content-group .form-table th{padding-left:0}
		@media(max-width:782px){.g5tech-about-media-grid{grid-template-columns:1fr}}
	</style>
	<script>
		jQuery(function($) {
			$('.g5tech-about-media-select').on('click', function() {
				const button = $(this);
				const input = $('#' + button.data('target'));
				const preview = button.closest('.g5tech-about-media-field').find('img');
				const frame = wp.media({
					title: 'Pasirinkite nuotrauką',
					button: { text: 'Naudoti nuotrauką' },
					multiple: false
				});

				frame.on('select', function() {
					const attachment = frame.state().get('selection').first().toJSON();
					input.val(attachment.id);
					preview.attr('src', attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url);
				});

				frame.open();
			});

			$('.g5tech-about-media-remove').on('click', function() {
				const button = $(this);
				$('#' + button.data('target')).val('0');
				button.closest('.g5tech-about-media-field').find('img').attr('src', button.data('fallback'));
			});
		});
	</script>
	<?php
}

function g5tech_save_about_order() {
	if ( ! current_user_can( 'edit_g5_team_members' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio puslapio.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_save_about_order' );
	g5tech_save_admin_content_translations( 'about' );

	$content_fields    = g5tech_about_content_fields();
	$submitted_content = isset( $_POST['g5tech_about_content'] )
		? (array) wp_unslash( $_POST['g5tech_about_content'] )
		: array();
	$sanitized_content = array(
		'story_image_1_id' => isset( $submitted_content['story_image_1_id'] ) ? absint( $submitted_content['story_image_1_id'] ) : 0,
		'story_image_2_id' => isset( $submitted_content['story_image_2_id'] ) ? absint( $submitted_content['story_image_2_id'] ) : 0,
	);

	foreach ( $content_fields as $fields ) {
		foreach ( $fields as $key => $field ) {
			$value = isset( $submitted_content[ $key ] ) ? $submitted_content[ $key ] : '';

			if ( 'url' === $field['type'] ) {
				$sanitized_content[ $key ] = esc_url_raw( $value );
			} elseif ( 'textarea' === $field['type'] ) {
				$sanitized_content[ $key ] = sanitize_textarea_field( $value );
			} else {
				$sanitized_content[ $key ] = sanitize_text_field( $value );
			}
		}
	}

	foreach ( g5tech_about_repeater_configs() as $config ) {
		$sanitized_content[ $config['key'] ] = g5tech_sanitize_repeater_items(
			$submitted_content[ $config['key'] ] ?? array(),
			$config['schema']
		);
	}

	update_option( 'g5tech_about_content', $sanitized_content, false );

	wp_safe_redirect(
		add_query_arg(
			'updated',
			'1',
			g5tech_about_admin_url()
		)
	);
	exit;
}
add_action( 'admin_post_g5tech_save_about_order', 'g5tech_save_about_order' );

function g5tech_about_page_edit_redirect() {
	if (
		! isset( $_GET['post'], $_GET['action'] )
		|| 'edit' !== sanitize_key( wp_unslash( $_GET['action'] ) )
		|| ! current_user_can( 'edit_g5_team_members' )
	) {
		return;
	}

	$post_id = absint( $_GET['post'] );

	if ( 'page' !== get_post_type( $post_id ) || 'apie-mus' !== get_post_field( 'post_name', $post_id ) ) {
		return;
	}

	wp_safe_redirect( admin_url( 'edit.php?post_type=g5_team&page=g5tech-about-order' ) );
	exit;
}
add_action( 'load-post.php', 'g5tech_about_page_edit_redirect' );

function g5tech_about_page_row_actions( $actions, $post ) {
	if (
		'page' !== $post->post_type
		|| 'apie-mus' !== $post->post_name
		|| ! current_user_can( 'edit_g5_team_members' )
	) {
		return $actions;
	}

	$actions['g5_about_order'] = sprintf(
		'<a href="%s">Turinys ir tvarka</a>',
		esc_url( admin_url( 'edit.php?post_type=g5_team&page=g5tech-about-order' ) )
	);
	$actions['g5_team'] = sprintf(
		'<a href="%s">Komandos nariai</a>',
		esc_url( admin_url( 'edit.php?post_type=g5_team' ) )
	);

	return $actions;
}
add_filter( 'page_row_actions', 'g5tech_about_page_row_actions', 10, 2 );

function g5tech_register_team_blocks() {
	register_block_type(
		'g5tech/about-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH apie mus',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_about_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);

	register_block_type(
		'g5tech/team-profile',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH komandos profilis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_team_profile',
			'uses_context'    => array( 'postId', 'postType' ),
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_team_blocks' );

function g5tech_team_portrait( $post_id, $class, $label = '' ) {
	$image = get_the_post_thumbnail(
		$post_id,
		'large',
		array(
			'class' => $class . '__image',
			'alt'   => get_the_title( $post_id ),
		)
	);

	if ( $image ) {
		printf( '<div class="%1$s %1$s--image">%2$s</div>', esc_attr( $class ), $image );
		return;
	}

	printf(
		'<div class="%1$s" aria-hidden="true">%2$s</div>',
		esc_attr( $class ),
		$label ? '<span>' . esc_html( $label ) . '</span>' : ''
	);
}

function g5tech_render_team_cards() {
	$members = g5tech_get_team_members();

	if ( ! $members ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-team-grid">
		<?php foreach ( $members as $member ) : ?>
			<?php
			$role         = get_post_meta( $member->ID, 'g5_team_role', true );
			$email        = get_post_meta( $member->ID, 'g5_team_email', true );
			$phone        = get_post_meta( $member->ID, 'g5_team_phone', true );
			$show_contact = (bool) get_post_meta( $member->ID, 'g5_team_show_contact', true );
			$show_profile = (bool) get_post_meta( $member->ID, 'g5_team_show_profile', true );
			?>
			<article class="g5-team-card">
				<?php g5tech_team_portrait( $member->ID, 'g5-team-card__portrait' ); ?>
				<div class="g5-team-card__body">
					<?php if ( $role ) : ?>
						<span class="g5-team-card__role"><?php echo esc_html( $role ); ?></span>
					<?php endif; ?>
					<h3 class="g5-heading-md"><?php echo esc_html( get_the_title( $member ) ); ?></h3>
					<?php if ( $show_contact && ( $email || $phone ) ) : ?>
						<div class="team-contact">
							<?php if ( $email ) : ?>
								<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
							<?php endif; ?>
							<?php if ( $phone ) : ?>
								<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( $show_profile ) : ?>
					<div class="team-card-footer">
						<a class="g5-text-link" href="<?php echo esc_url( get_permalink( $member ) ); ?>">Peržiūrėti profilį →</a>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_about_page_legacy() {
	$about           = g5tech_about_content();
	$team_image_1_id = absint( $about['story_image_1_id'] );
	$team_image_2_id = absint( $about['story_image_2_id'] );
	$team_image_1    = $team_image_1_id ? wp_get_attachment_image_url( $team_image_1_id, 'full' ) : '';
	$team_image_2    = $team_image_2_id ? wp_get_attachment_image_url( $team_image_2_id, 'full' ) : '';
	$team_image_1    = $team_image_1 ? $team_image_1 : get_theme_file_uri( 'assets/images/team/team-work-01.jpg' );
	$team_image_2    = $team_image_2 ? $team_image_2 : get_theme_file_uri( 'assets/images/team/team-work-02.jpg' );
	$team_image_1_alt = $team_image_1_id ? get_post_meta( $team_image_1_id, '_wp_attachment_image_alt', true ) : '';
	$team_image_2_alt = $team_image_2_id ? get_post_meta( $team_image_2_id, '_wp_attachment_image_alt', true ) : '';
	$team_image_1_alt = $team_image_1_alt ? $team_image_1_alt : $about['story_image_1_caption'];
	$team_image_2_alt = $team_image_2_alt ? $team_image_2_alt : $about['story_image_2_caption'];
	$contact_url     = home_url( g5tech_setting( 'contact_page_url', '/kontaktai/' ) );
	$about_stat_1    = g5tech_stat( 1, '6000+', 'įgyvendintų bazinių stočių' );
	$about_stat_3    = g5tech_stat( 3, '6', 'Europos šalys' );
	$about_stat_4    = g5tech_stat( 4, '2020', 'veiklos pradžia' );

	ob_start();
	?>
	<section class="team-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="team-page-title">
		<div class="g5-container g5-grid">
			<div class="team-hero__copy">
				<nav class="g5-breadcrumbs" aria-label="Puslapio kelias">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a><span>/</span><span>Apie mus</span>
				</nav>
				<div class="g5-eyebrow"><?php echo esc_html( $about['hero_eyebrow'] ); ?></div>
				<h1 class="g5-display-xl" id="team-page-title"><?php echo esc_html( $about['hero_title'] ); ?></h1>
				<p class="g5-body"><?php echo esc_html( $about['hero_lead'] ); ?></p>
			</div>
			<div class="team-hero__proof" aria-label="Pagrindiniai 5G TECH faktai">
				<div class="team-hero__metric"><strong><?php echo esc_html( $about_stat_4['value'] ); ?></strong><span><?php echo esc_html( $about_stat_4['label'] ); ?></span></div>
				<div class="team-hero__metric"><strong><?php echo esc_html( $about_stat_1['value'] ); ?></strong><span><?php echo esc_html( $about_stat_1['label'] ); ?></span></div>
				<div class="team-hero__metric"><strong><?php echo esc_html( $about_stat_3['value'] ); ?></strong><span><?php echo esc_html( $about_stat_3['label'] ); ?></span></div>
				<div class="team-hero__metric"><strong>ISO / SSVA</strong><span>standartai ir rangovo kvalifikacija</span></div>
			</div>
		</div>
	</section>

	<?php
	$about_sections = array();
	ob_start();
	?>
	<section class="g5-section about-story g5-grid-lines" aria-labelledby="story-title" data-g5-core-module="about_story">
		<div class="g5-container">
			<div class="editorial-head">
				<div class="g5-eyebrow"><?php echo esc_html( $about['story_eyebrow'] ); ?></div>
				<div class="editorial-head__copy">
					<h2 class="g5-display-lg" id="story-title"><?php echo esc_html( $about['story_title'] ); ?></h2>
					<p class="g5-body"><?php echo esc_html( $about['story_lead'] ); ?></p>
				</div>
			</div>
			<div class="story-layout">
				<div class="story-copy">
					<p class="g5-body"><?php echo esc_html( $about['story_body_1'] ); ?></p>
					<p class="g5-body"><?php echo esc_html( $about['story_body_2'] ); ?></p>
					<p class="g5-body"><?php echo esc_html( $about['story_body_3'] ); ?></p>
				</div>
				<div class="story-facts">
					<?php foreach ( $about['story_facts'] as $fact_index => $fact ) : ?>
						<div class="story-fact"><small><?php echo esc_html( str_pad( (string) ( $fact_index + 1 ), 2, '0', STR_PAD_LEFT ) . ' / ' . $fact['label'] ); ?></small><strong><?php echo esc_html( $fact['title'] ); ?></strong><span><?php echo esc_html( $fact['text'] ); ?></span></div>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="about-media">
				<figure><img src="<?php echo esc_url( $team_image_1 ); ?>" alt="<?php echo esc_attr( $team_image_1_alt ); ?>"><figcaption><?php echo esc_html( $about['story_image_1_caption'] ); ?></figcaption></figure>
				<figure><img src="<?php echo esc_url( $team_image_2 ); ?>" alt="<?php echo esc_attr( $team_image_2_alt ); ?>"><figcaption><?php echo esc_html( $about['story_image_2_caption'] ); ?></figcaption></figure>
			</div>
		</div>
	</section>

	<?php
	$about_sections['story'] = (string) ob_get_clean();
	ob_start();
	?>
	<section class="g5-section purpose-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="purpose-title" data-g5-core-module="about_purpose">
		<div class="g5-container">
			<div class="editorial-head">
				<div class="g5-eyebrow"><?php echo esc_html( $about['purpose_eyebrow'] ); ?></div>
				<div class="editorial-head__copy"><h2 class="g5-display-lg" id="purpose-title"><?php echo esc_html( $about['purpose_title'] ); ?></h2></div>
			</div>
			<div class="purpose-grid">
				<article class="purpose-card"><span class="purpose-card__label"><?php echo esc_html( $about['mission_label'] ); ?></span><h3 class="g5-heading-lg"><?php echo esc_html( $about['mission_title'] ); ?></h3><p class="g5-body"><?php echo esc_html( $about['mission_text'] ); ?></p></article>
				<article class="purpose-card"><span class="purpose-card__label"><?php echo esc_html( $about['vision_label'] ); ?></span><h3 class="g5-heading-lg"><?php echo esc_html( $about['vision_title'] ); ?></h3><p class="g5-body"><?php echo esc_html( $about['vision_text'] ); ?></p></article>
			</div>
		</div>
	</section>

	<?php
	$about_sections['purpose'] = (string) ob_get_clean();
	ob_start();
	?>
	<section class="g5-section values-section g5-grid-lines" aria-labelledby="values-title" data-g5-core-module="about_values">
		<div class="g5-container">
			<div class="editorial-head">
				<div class="g5-eyebrow"><?php echo esc_html( $about['values_eyebrow'] ); ?></div>
				<div class="editorial-head__copy"><h2 class="g5-display-lg" id="values-title"><?php echo esc_html( $about['values_title'] ); ?></h2></div>
			</div>
			<div class="value-grid">
				<?php foreach ( $about['values'] as $value_index => $value ) : ?>
					<article class="value-card"><span class="value-card__number"><?php echo esc_html( str_pad( (string) ( $value_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( $value['title'] ); ?></h3><p><?php echo esc_html( $value['text'] ); ?></p></article>
				<?php endforeach; ?>
			</div>
			<div class="culture-callout">
				<div class="culture-callout__copy">
					<div><span class="purpose-card__label"><?php echo esc_html( $about['culture_label'] ); ?></span><h3 class="g5-heading-lg"><?php echo esc_html( $about['culture_title'] ); ?></h3></div>
					<p class="g5-body"><?php echo esc_html( $about['culture_text'] ); ?></p>
				</div>
				<div class="culture-callout__action"><a class="g5-button g5-button--primary" href="<?php echo esc_url( $about['culture_url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $about['culture_button_label'] ); ?></a></div>
			</div>
		</div>
	</section>

	<?php
	$about_sections['values'] = (string) ob_get_clean();
	ob_start();
	?>
	<section class="g5-section strategy-section g5-grid-lines" aria-labelledby="strategy-title" data-g5-core-module="about_strategy">
		<div class="g5-container">
			<div class="strategy-intro"><div class="strategy-intro__copy"><div class="g5-eyebrow"><?php echo esc_html( $about['strategy_eyebrow'] ); ?></div><h2 class="g5-display-lg" id="strategy-title"><?php echo esc_html( $about['strategy_title'] ); ?></h2><p class="g5-body"><?php echo esc_html( $about['strategy_lead'] ); ?></p></div></div>
			<div class="strategy-grid">
				<?php foreach ( $about['strategies'] as $strategy_index => $strategy ) : ?>
					<article class="strategy-item"><span class="strategy-item__number"><?php echo esc_html( str_pad( (string) ( $strategy_index + 1 ), 2, '0', STR_PAD_LEFT ) . ' / ' . $strategy['label'] ); ?></span><h3 class="g5-heading-md"><?php echo esc_html( $strategy['title'] ); ?></h3><p><?php echo esc_html( $strategy['text'] ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	$about_sections['strategy'] = (string) ob_get_clean();
	ob_start();
	?>
	<section class="g5-section team-listing g5-grid-lines" id="komanda" aria-labelledby="people-title" data-g5-core-module="about_team">
		<div class="g5-container">
			<div class="editorial-head"><div class="g5-eyebrow"><?php echo esc_html( $about['team_eyebrow'] ); ?></div><div class="editorial-head__copy"><h2 class="g5-display-lg" id="people-title"><?php echo esc_html( $about['team_title'] ); ?></h2></div></div>
			<?php echo g5tech_render_team_cards(); ?>
		</div>
	</section>

	<?php
	$about_sections['team'] = (string) ob_get_clean();
	ob_start();
	?>
	<section class="g5-section competence-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="competence-title" data-g5-core-module="about_competence">
		<div class="g5-container competence-grid">
			<div class="competence-grid__intro"><div class="g5-eyebrow"><?php echo esc_html( $about['competence_eyebrow'] ); ?></div><h2 class="g5-heading-lg" id="competence-title"><?php echo esc_html( $about['competence_title'] ); ?></h2></div>
			<div class="competence-list">
				<?php foreach ( $about['competences'] as $competence_index => $competence ) : ?>
					<article class="competence-item"><span class="competence-item__number"><?php echo esc_html( str_pad( (string) ( $competence_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( $competence['title'] ); ?></h3><p><?php echo esc_html( $competence['text'] ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php
	$about_sections['competence'] = (string) ob_get_clean();
	$about_order                  = g5tech_about_section_order();

	uksort(
		$about_sections,
		static function ( $first, $second ) use ( $about_order ) {
			return (int) $about_order[ $first ] <=> (int) $about_order[ $second ];
		}
	);

	echo implode( '', $about_sections );

	echo g5tech_render_page_modules( 'about' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

	<section class="g5-section page-cta g5-grid-lines g5-grid-lines--dark" aria-labelledby="team-cta-title" data-g5-page-anchor="cta">
		<div class="g5-container page-cta__grid">
			<div class="page-cta__copy"><div class="g5-eyebrow"><?php echo esc_html( $about['cta_eyebrow'] ); ?></div><h2 class="g5-display-lg" id="team-cta-title"><?php echo esc_html( $about['cta_title'] ); ?></h2><p class="g5-body"><?php echo esc_html( $about['cta_text'] ); ?></p></div>
			<div class="page-cta__action"><a class="g5-button g5-button--primary" href="<?php echo esc_url( $contact_url ); ?>"><?php echo esc_html( $about['cta_button_label'] ); ?> <span class="g5-button__icon">→</span></a></div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_about_page() {
	return g5tech_compose_modular_page( 'about', g5tech_get_legacy_page_html( 'about' ) );
}

function g5tech_team_profile_post_id( $block ) {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}

	return get_the_ID();
}

function g5tech_render_team_profile( $attributes, $content, $block ) {
	$post_id       = g5tech_team_profile_post_id( $block );
	$role          = get_post_meta( $post_id, 'g5_team_role', true );
	$summary       = get_post_meta( $post_id, 'g5_team_summary', true );
	$email         = get_post_meta( $post_id, 'g5_team_email', true );
	$phone         = get_post_meta( $post_id, 'g5_team_phone', true );
	$show_contact  = (bool) get_post_meta( $post_id, 'g5_team_show_contact', true );
	$since         = get_post_meta( $post_id, 'g5_team_experience_since', true );
	$company_since = get_post_meta( $post_id, 'g5_team_company_since', true );
	$primary_area  = get_post_meta( $post_id, 'g5_team_primary_area', true );
	$responsibility = get_post_meta( $post_id, 'g5_team_responsibility', true );
	$experience    = get_post_meta( $post_id, 'g5_team_experience', true );
	$countries     = g5tech_team_lines( $post_id, 'g5_team_countries' );
	$competencies  = g5tech_team_lines( $post_id, 'g5_team_competencies' );
	$operator_ids  = array_map( 'absint', get_post_meta( $post_id, 'g5_team_operators', false ) );
	$operators     = function_exists( 'g5tech_get_partners' )
		? g5tech_get_partners( '', $operator_ids )
		: array();
	$experience_label = $since && ctype_digit( (string) $since )
		? max( 1, (int) wp_date( 'Y' ) - (int) $since ) . '+ m.'
		: $since;

	ob_start();
	?>
	<section class="profile-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="profile-title">
		<div class="g5-container g5-grid">
			<?php g5tech_team_portrait( $post_id, 'profile-hero__portrait', '5G TECH komanda' ); ?>
			<div class="profile-hero__copy">
				<nav class="g5-breadcrumbs" aria-label="Puslapio kelias">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a><span>/</span>
					<a href="<?php echo esc_url( home_url( '/apie-mus/#komanda' ) ); ?>">Komanda</a><span>/</span>
					<span><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
				</nav>
				<?php if ( $role ) : ?><div class="g5-eyebrow"><?php echo esc_html( $role ); ?></div><?php endif; ?>
				<h1 class="g5-display-xl" id="profile-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
				<?php if ( $summary ) : ?><p class="g5-body"><?php echo esc_html( $summary ); ?></p><?php endif; ?>
				<div class="profile-hero__actions">
					<?php if ( $show_contact && $email ) : ?><a class="g5-button g5-button--primary" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">Susisiekti el. paštu <span class="g5-button__icon">→</span></a><?php endif; ?>
					<?php if ( $show_contact && $phone ) : ?><a class="g5-button g5-button--outline-light" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
					<a class="g5-button g5-button--outline-light" href="<?php echo esc_url( home_url( '/apie-mus/#komanda' ) ); ?>">Visa komanda →</a>
				</div>
			</div>
			<div class="profile-metrics" aria-label="Profesinės patirties rodikliai">
				<?php if ( $experience_label ) : ?><div class="profile-metric"><strong><?php echo esc_html( $experience_label ); ?></strong><span>profesinėje srityje</span></div><?php endif; ?>
				<?php if ( $countries ) : ?><div class="profile-metric"><strong><?php echo count( $countries ); ?></strong><span>Europos šalys</span></div><?php endif; ?>
				<?php if ( $operators ) : ?><div class="profile-metric"><strong><?php echo count( $operators ); ?></strong><span>operatorių</span></div><?php endif; ?>
				<?php if ( $company_since ) : ?><div class="profile-metric"><strong><?php echo esc_html( $company_since ); ?></strong><span>5G TECH komandoje nuo</span></div><?php endif; ?>
			</div>
		</div>
	</section>

	<section class="g5-section profile-section g5-grid-lines" aria-labelledby="experience-title">
		<div class="g5-container profile-section__grid">
			<div class="profile-section__label">01 / Patirtis</div>
			<div class="profile-section__content">
				<h2 class="g5-display-md" id="experience-title">Patirtis ryšio infrastruktūros projektuose.</h2>
				<?php if ( $experience ) : ?><p class="g5-body"><?php echo esc_html( $experience ); ?></p><?php endif; ?>
				<div class="profile-facts">
					<?php if ( $since ) : ?><div class="profile-fact"><small>Srityje nuo</small><strong><?php echo esc_html( $since ); ?> m.</strong></div><?php endif; ?>
					<?php if ( $company_since ) : ?><div class="profile-fact"><small>5G TECH nuo</small><strong><?php echo esc_html( $company_since ); ?> m.</strong></div><?php endif; ?>
					<?php if ( $primary_area ) : ?><div class="profile-fact"><small>Pagrindinė kryptis</small><strong><?php echo esc_html( $primary_area ); ?></strong></div><?php endif; ?>
					<?php if ( $responsibility ) : ?><div class="profile-fact"><small>Atsakomybė</small><strong><?php echo esc_html( $responsibility ); ?></strong></div><?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $countries || $operators ) : ?>
		<section class="g5-section g5-section--paper profile-section g5-grid-lines" aria-labelledby="geography-title">
			<div class="g5-container profile-section__grid">
				<div class="profile-section__label">02 / Projektų geografija</div>
				<div class="profile-section__content">
					<h2 class="g5-display-md" id="geography-title">Patirtis skirtingose Europos rinkose.</h2>
					<?php if ( $countries ) : ?>
						<h3 class="g5-heading-sm">Šalys, kuriose dirbta</h3>
						<div class="tag-list" aria-label="Šalys, kuriose dirbta"><?php foreach ( $countries as $country ) : ?><span class="tag"><?php echo esc_html( $country ); ?></span><?php endforeach; ?></div>
					<?php endif; ?>
					<?php if ( $operators ) : ?>
						<h3 class="g5-heading-sm">Operatorių projektų patirtis</h3>
						<div class="tag-list" aria-label="Operatorių projektų patirtis"><?php foreach ( $operators as $operator ) : ?><span class="tag"><?php echo esc_html( get_the_title( $operator ) ); ?></span><?php endforeach; ?></div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $competencies ) : ?>
		<section class="g5-section profile-section g5-grid-lines" aria-labelledby="skills-title">
			<div class="g5-container profile-section__grid">
				<div class="profile-section__label">03 / Kompetencijos</div>
				<div class="profile-section__content">
					<h2 class="g5-display-md" id="skills-title">Pagrindinės kompetencijos.</h2>
					<div class="tag-list" aria-label="Profesinės kompetencijos"><?php foreach ( $competencies as $competency ) : ?><span class="tag"><?php echo esc_html( $competency ); ?></span><?php endforeach; ?></div>
				</div>
			</div>
		</section>
	<?php endif; ?>
	<?php
	return (string) ob_get_clean();
}

function g5tech_redirect_hidden_team_profile() {
	if ( ! is_singular( 'g5_team' ) ) {
		return;
	}

	if ( ! (bool) get_post_meta( get_queried_object_id(), 'g5_team_show_profile', true ) ) {
		wp_safe_redirect( home_url( '/apie-mus/#komanda' ), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'g5tech_redirect_hidden_team_profile' );
