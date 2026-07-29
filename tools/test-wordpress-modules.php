<?php
/**
 * 5G TECH modulių ir puslapių integracijos testas.
 *
 * Paleidimas:
 * php tools/test-wordpress-modules.php
 */

require dirname( __DIR__ ) . '/wordpress/wp-load.php';

$checks             = 0;
$created_post_ids   = array();
$created_user_ids   = array();
$original_user_id   = get_current_user_id();
$original_placements = get_option( 'g5tech_module_placements', array() );
$original_content_options = array();
foreach ( array( 'g5tech_settings', 'g5tech_about_content', 'g5tech_career_page_content', 'g5tech_structured_content' ) as $option_name ) {
	$sentinel = new stdClass();
	$value    = get_option( $option_name, $sentinel );
	$original_content_options[ $option_name ] = array(
		'exists' => $value !== $sentinel,
		'value'  => $value !== $sentinel ? $value : null,
	);
}
$failure            = null;

function g5tech_test_assert( $condition, $message ) {
	global $checks;
	$checks++;

	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

function g5tech_test_module( $title ) {
	global $created_post_ids;

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'g5_module',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		throw new RuntimeException( $post_id->get_error_message() );
	}

	$created_post_ids[] = $post_id;
	update_post_meta( $post_id, 'g5_module_type', 'text' );
	update_post_meta( $post_id, 'g5_module_eyebrow', 'Testas' );
	update_post_meta( $post_id, 'g5_module_heading', $title );
	update_post_meta( $post_id, 'g5_module_content', 'Laikinas automatinio testo turinys.' );
	update_post_meta( $post_id, 'g5_module_theme', 'light' );

	return $post_id;
}

function g5tech_test_user( $role ) {
	global $created_user_ids;

	$suffix  = strtolower( wp_generate_password( 10, false, false ) );
	$user_id = wp_insert_user(
		array(
			'user_login' => 'g5tech_test_' . $suffix,
			'user_pass'  => wp_generate_password( 24, true, true ),
			'user_email' => 'g5tech-test-' . $suffix . '@example.test',
			'role'       => $role,
		)
	);

	if ( is_wp_error( $user_id ) ) {
		throw new RuntimeException( $user_id->get_error_message() );
	}

	$created_user_ids[] = $user_id;

	return $user_id;
}

$page_renderers = array(
	'home'             => 'g5tech_render_homepage',
	'services'         => 'g5tech_render_services_grid',
	'projects'         => 'g5tech_render_projects_archive',
	'experience'       => 'g5tech_render_experience_page',
	'about'            => 'g5tech_render_about_page',
	'career'           => 'g5tech_render_career_page',
	'academy'          => 'g5tech_render_academy_page',
	'training'         => 'g5tech_render_training_page',
	'candidate_faq'    => 'g5tech_render_candidate_faq_page',
	'leaders'          => 'g5tech_render_leaders_page',
	'project_managers' => 'g5tech_render_project_managers_page',
	'contact'          => 'g5tech_render_contact_page',
	'news'             => 'g5tech_render_news_page',
);

try {
	$choices = g5tech_module_page_choices();
	g5tech_test_assert( array_keys( $choices ) === array_keys( $page_renderers ), 'Dokumentuotų puslapių ir integruotų puslapių sąrašai nesutampa.' );
	g5tech_test_assert( function_exists( 'g5tech_render_repeater' ), 'Nerastas bendras kartotinių elementų valdiklis.' );
	g5tech_test_assert( 0 < count( g5tech_stats() ), 'Nėra bendrų rodiklių.' );
	g5tech_test_assert( 0 < count( g5tech_process_steps() ), 'Nėra projekto etapų.' );

	$about_content = g5tech_about_content();
	g5tech_test_assert( is_array( $about_content['story_facts'] ), 'Neperkelti „Apie mus“ faktai.' );
	g5tech_test_assert( is_array( $about_content['values'] ), 'Neperkeltos „Apie mus“ vertybės.' );
	g5tech_test_assert( is_array( $about_content['strategies'] ), 'Neperkeltos strateginės kryptys.' );
	g5tech_test_assert( is_array( $about_content['competences'] ), 'Neperkelti kompetencijos punktai.' );

	$career_content = g5tech_career_page_content();
	g5tech_test_assert( is_array( $career_content['benefits'] ), 'Neperkelti Karjeros privalumai.' );
	g5tech_test_assert( is_array( $career_content['selection_steps'] ), 'Neperkelti atrankos etapai.' );
	g5tech_test_assert( is_array( $career_content['growth_cards'] ), 'Neperkeltos augimo kortelės.' );

	$academy_content = g5tech_structured_section( 'academy' );
	$leaders_content = g5tech_structured_section( 'leaders' );
	$manager_content = g5tech_structured_section( 'project_managers' );
	$audience_content = g5tech_structured_section( 'home_audiences' );
	g5tech_test_assert( is_array( $academy_content['program'] ), 'Neperkelti Academy programos etapai.' );
	g5tech_test_assert( is_array( $academy_content['included'] ), 'Neperkeltas Academy suteikiamų dalykų sąrašas.' );
	g5tech_test_assert( is_array( $leaders_content['reasons'] ), 'Neperkelti vadovų puslapio argumentai.' );
	g5tech_test_assert( is_array( $manager_content['tasks'] ), 'Neperkelta projektų vadovų techninė apimtis.' );
	g5tech_test_assert( is_array( $audience_content['cards'] ), 'Neperkeltos titulinio auditorijų kortelės.' );

	$sanitized_repeater = g5tech_sanitize_repeater_items(
		array(
			array( 'title' => '<b>Bandymas</b>', 'text' => "Pirma\nAntra" ),
			array( 'title' => '', 'text' => '' ),
		),
		array(
			'title' => array( 'type' => 'text' ),
			'text'  => array( 'type' => 'textarea' ),
		)
	);
	g5tech_test_assert( 1 === count( $sanitized_repeater ), 'Tuščias kartotinio elementas nepašalintas.' );
	g5tech_test_assert( 'Bandymas' === $sanitized_repeater[0]['title'], 'Kartotinio elemento antraštė neišvalyta.' );

	$about_content['values'][] = array( '_id' => 'test-value', 'title' => 'Testinė vertybė', 'text' => 'Laikinas testas.' );
	update_option( 'g5tech_about_content', $about_content, false );
	g5tech_test_assert( false !== strpos( g5tech_render_about_page(), 'Testinė vertybė' ), 'Nauja „Apie mus“ vertybė neatvaizduota.' );

	$career_content['benefits'][] = array( '_id' => 'test-benefit', 'title' => 'Testinis privalumas', 'text' => 'Laikinas testas.' );
	update_option( 'g5tech_career_page_content', $career_content, false );
	g5tech_test_assert( false !== strpos( g5tech_render_career_page(), 'Testinis privalumas' ), 'Naujas Karjeros privalumas neatvaizduotas.' );

	$settings_test = g5tech_settings();
	$settings_test['stats'][] = array( '_id' => 'test-stat', 'value' => '999', 'label' => 'testinis rodiklis' );
	update_option( 'g5tech_settings', $settings_test, false );
	g5tech_test_assert( false !== strpos( g5tech_render_experience_page(), 'testinis rodiklis' ), 'Naujas bendras rodiklis neatvaizduotas.' );

	$structured_test = g5tech_structured_content();
	$structured_test['home_audiences']['cards'][] = array(
		'_id'   => 'test-audience',
		'label' => 'Testas',
		'title' => 'Testinė auditorija',
		'text'  => 'Laikinas testas.',
		'url'   => '/testas/',
	);
	update_option( 'g5tech_structured_content', $structured_test, false );
	g5tech_test_assert( false !== strpos( g5tech_render_homepage(), 'Testinė auditorija' ), 'Nauja titulinio auditorija neatvaizduota.' );

	$definitions      = g5tech_builtin_module_definitions();
	$expected_by_page = array_fill_keys( array_keys( $choices ), array() );

	g5tech_test_assert( 38 === count( $definitions ), 'Nesutampa numatytų dinaminių modulių skaičius.' );
	foreach ( array( 'home_audiences', 'academy_program', 'academy_included', 'leaders_reasons', 'project_managers_scope', 'project_managers_equipment' ) as $dynamic_key ) {
		g5tech_test_assert(
			false !== strpos( $definitions[ $dynamic_key ]['source_url'], 'g5tech-structured-content' ),
			"Dinaminio modulio redagavimo nuoroda neatidaro tikro sąrašo valdymo: {$dynamic_key}."
		);
	}

	foreach ( $definitions as $dynamic_key => $definition ) {
		$module = g5tech_get_builtin_module( $dynamic_key );

		g5tech_test_assert( $module instanceof WP_Post, "Nerastas dinaminis modulis: {$dynamic_key}." );
		g5tech_test_assert( 'publish' === $module->post_status, "Dinaminis modulis nepaskelbtas: {$dynamic_key}." );
		g5tech_test_assert( 'dynamic' === get_post_meta( $module->ID, 'g5_module_type', true ), "Neteisingas dinaminio modulio tipas: {$dynamic_key}." );
		g5tech_test_assert( '' !== g5tech_render_content_module( $module ), "Dinaminis modulis neatvaizduojamas: {$dynamic_key}." );
		g5tech_test_assert( g5tech_module_is_on_page( $module->ID, $definition['page'] ), "Dinaminis modulis neprijungtas prie numatyto puslapio: {$dynamic_key}." );

		$expected_by_page[ $definition['page'] ][] = (int) $module->ID;
	}

	foreach ( $choices as $page_key => $page_label ) {
		g5tech_test_assert( (bool) g5tech_module_page_public_url( $page_key ), "Nenurodytas puslapio adresas: {$page_label}." );
		g5tech_test_assert( function_exists( $page_renderers[ $page_key ] ), "Nerastas puslapio atvaizdavimo metodas: {$page_label}." );

		$output = call_user_func( $page_renderers[ $page_key ] );

		foreach ( $expected_by_page[ $page_key ] as $module_id ) {
			g5tech_test_assert(
				false !== strpos( $output, 'data-content-module="' . $module_id . '"' ),
				"Numatytas modulis neatvaizduotas puslapyje: {$page_label}, modulio ID {$module_id}."
			);
		}
	}

	$first_id  = g5tech_test_module( 'Automatinis modulių testas A' );
	$second_id = g5tech_test_module( 'Automatinis modulių testas B' );

	foreach ( array_keys( $choices ) as $page_key ) {
		g5tech_set_module_page_active( $first_id, $page_key, true );
		$output = call_user_func( $page_renderers[ $page_key ] );
		g5tech_test_assert(
			false !== strpos( $output, 'data-content-module="' . $first_id . '"' ),
			"Modulis neatvaizduotas puslapyje: {$choices[ $page_key ]}."
		);
		g5tech_set_module_page_active( $first_id, $page_key, false );
	}

	g5tech_set_module_pages( $first_id, array( 'home', 'about' ) );
	g5tech_test_assert( g5tech_module_is_on_page( $first_id, 'home' ), 'Susietas modulis nepridėtas į Titulinį.' );
	g5tech_test_assert( g5tech_module_is_on_page( $first_id, 'about' ), 'Susietas modulis nepridėtas į „Apie mus“.' );

	g5tech_set_module_page_active( $second_id, 'home', true );
	$home_before = array_map( 'intval', wp_list_pluck( g5tech_get_page_modules_for_admin( 'home' ), 'ID' ) );
	$home_order  = array_merge( array( $second_id, $first_id ), array_values( array_diff( $home_before, array( $second_id, $first_id ) ) ) );
	g5tech_set_page_module_order( 'home', $home_order );
	$home_ids = wp_list_pluck( g5tech_get_page_modules_for_admin( 'home' ), 'ID' );
	g5tech_test_assert( $home_order === array_map( 'intval', $home_ids ), 'Modulių tvarka neišsaugota.' );

	$home_output = g5tech_render_homepage();
	g5tech_test_assert(
		strpos( $home_output, 'data-content-module="' . $second_id . '"' ) < strpos( $home_output, 'data-content-module="' . $first_id . '"' ),
		'Pakeista modulių tvarka neatsispindi viešame puslapyje.'
	);

	$copy_id = g5tech_create_content_module_copy( $first_id );
	g5tech_test_assert( ! is_wp_error( $copy_id ), 'Nepavyko sukurti nepriklausomos kopijos.' );
	$created_post_ids[] = $copy_id;
	g5tech_test_assert( 'draft' === get_post_status( $copy_id ), 'Nepriklausoma kopija nesukurta kaip juodraštis.' );
	g5tech_test_assert( (int) get_post_meta( $copy_id, 'g5_module_source_id', true ) === $first_id, 'Kopija nesusieta su originalo šaltiniu.' );

	$dynamic_module = g5tech_get_builtin_module( 'home_news' );
	$dynamic_copy   = g5tech_create_content_module_copy( $dynamic_module->ID );
	g5tech_test_assert( is_wp_error( $dynamic_copy ), 'Dinaminė sekcija neturi būti kopijuojama kaip nepriklausomas modulis.' );

	g5tech_set_module_page_active( $dynamic_module->ID, 'about', true );
	g5tech_test_assert(
		false !== strpos( g5tech_render_about_page(), 'data-content-module="' . $dynamic_module->ID . '"' ),
		'Bendras dinaminis modulis neatvaizduotas kitame puslapyje.'
	);
	g5tech_set_module_page_active( $dynamic_module->ID, 'about', false );

	$experience_numbers = g5tech_get_builtin_module( 'experience_numbers' );
	g5tech_set_module_page_active( $experience_numbers->ID, 'experience', false );
	g5tech_test_assert(
		false === strpos( g5tech_render_experience_page(), 'data-content-module="' . $experience_numbers->ID . '"' ),
		'Iš puslapio pašalintas modulis vis dar atvaizduojamas.'
	);
	g5tech_set_module_page_active( $experience_numbers->ID, 'experience', true );

	g5tech_remove_module_from_all_pages( $first_id );
	foreach ( array_keys( $choices ) as $page_key ) {
		g5tech_test_assert( ! g5tech_module_is_on_page( $first_id, $page_key ), "Modulis liko puslapyje po visuotinio pašalinimo: {$choices[ $page_key ]}." );
	}

	wp_trash_post( $second_id );
	g5tech_test_assert( 'trash' === get_post_status( $second_id ), 'Modulis neperkeltas į šiukšlinę.' );
	foreach ( array_keys( $choices ) as $page_key ) {
		g5tech_test_assert( ! g5tech_module_is_on_page( $second_id, $page_key ), "Ištrintas modulis liko puslapyje: {$choices[ $page_key ]}." );
	}

	wp_untrash_post( $second_id );
	g5tech_test_assert( 'draft' === get_post_status( $second_id ), 'Atkurtas modulis negrąžintas kaip saugus juodraštis.' );
	foreach ( array_keys( $choices ) as $page_key ) {
		g5tech_test_assert( ! g5tech_module_is_on_page( $second_id, $page_key ), "Atkurtas modulis automatiškai grįžo į puslapį: {$choices[ $page_key ]}." );
	}

	$hr_user_id = g5tech_test_user( 'g5_hr_editor' );
	wp_set_current_user( $hr_user_id );
	g5tech_test_assert( g5tech_user_can_manage_module_page( 'about' ), 'Personalo redaktorius negali valdyti „Apie mus“ modulių.' );
	g5tech_test_assert( g5tech_can_manage_structured_content( 'academy' ), 'Personalo redaktorius negali valdyti Academy sąrašų.' );
	g5tech_test_assert( ! g5tech_can_manage_structured_content( 'leaders' ), 'Personalo redaktoriui neturi būti leidžiama valdyti Vadovų puslapio sąrašų.' );
	g5tech_test_assert( ! g5tech_user_can_manage_module_page( 'services' ), 'Personalo redaktoriui neturi būti leidžiama valdyti Paslaugų modulių.' );
	g5tech_test_assert( current_user_can( 'delete_post', $first_id ), 'Personalo redaktorius negali trinti jam prieinamų modulių.' );

	$content_user_id = g5tech_test_user( 'g5_content_editor' );
	wp_set_current_user( $content_user_id );
	g5tech_test_assert( g5tech_user_can_manage_module_page( 'services' ), 'Turinio redaktorius negali valdyti Paslaugų modulių.' );
	g5tech_test_assert( g5tech_user_can_manage_module_page( 'training' ), 'Turinio redaktorius negali valdyti Mokymų modulių.' );
	g5tech_test_assert( current_user_can( 'delete_post', $first_id ), 'Turinio redaktorius negali trinti modulių.' );

	echo "Atlikta patikrų: {$checks}\n";
	echo "Modulių biblioteka ir visi dokumentuoti puslapiai veikia.\n";
} catch ( Throwable $error ) {
	$failure = $error;
} finally {
	wp_set_current_user( $original_user_id );
	update_option( 'g5tech_module_placements', $original_placements, false );
	foreach ( $original_content_options as $option_name => $original ) {
		if ( $original['exists'] ) {
			update_option( $option_name, $original['value'], false );
		} else {
			delete_option( $option_name );
		}
	}

	foreach ( array_reverse( $created_post_ids ) as $post_id ) {
		if ( get_post( $post_id ) ) {
			wp_delete_post( $post_id, true );
		}
	}

	if ( $created_user_ids ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		foreach ( array_reverse( $created_user_ids ) as $user_id ) {
			if ( get_userdata( $user_id ) ) {
				wp_delete_user( $user_id );
			}
		}
	}
}

if ( $failure ) {
	fwrite( STDERR, 'KLAIDA: ' . $failure->getMessage() . "\n" );
	exit( 1 );
}
