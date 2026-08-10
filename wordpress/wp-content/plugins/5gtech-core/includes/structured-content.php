<?php
/**
 * Paprastų vidinių puslapių struktūriniai sąrašai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_structured_content_defaults() {
	return array(
		'academy' => array(
			'program_eyebrow'  => 'Programa',
			'program_title'    => 'Penki etapai nuo pokalbio iki darbo pasiūlymo.',
			'program'          => array(
				array( '_id' => 'academy-1', 'title' => 'Kandidatavimas', 'text' => 'Forma ir pirminis susipažinimo pokalbis.' ),
				array( '_id' => 'academy-2', 'title' => 'Įvadas', 'text' => '1 diena: darbų sauga, AAP ir ryšio įranga.' ),
				array( '_id' => 'academy-3', 'title' => 'Praktika salėje', 'text' => '2 dienos: antenos, kabeliai ir bazinės stoties komponentai.' ),
				array( '_id' => 'academy-4', 'title' => 'Realus objektas', 'text' => '5 dienos su patyrusia projekto komanda.' ),
				array( '_id' => 'academy-5', 'title' => 'Pasiūlymas', 'text' => 'Sėkmingai programą baigusiems kandidatams galime pasiūlyti darbą.' ),
			),
			'included_eyebrow' => 'Suteikiame',
			'included_title'   => 'Pasirengimas saugiai darbo pradžiai.',
			'included'         => array(
				array( '_id' => 'included-1', 'text' => 'Terminuotą darbo sutartį programos metu' ),
				array( '_id' => 'included-2', 'text' => 'Apgyvendinimą kandidatams ne iš Vilniaus' ),
				array( '_id' => 'included-3', 'text' => 'Darbo aprangą ir asmenines apsaugos priemones' ),
				array( '_id' => 'included-4', 'text' => 'Darbui reikalingus įrankius' ),
				array( '_id' => 'included-5', 'text' => 'Patyrusios komandos priežiūrą' ),
				array( '_id' => 'included-6', 'text' => 'Galimybę gauti nuolatinio darbo pasiūlymą' ),
			),
		),
		'leaders' => array(
			'reasons_eyebrow' => 'Kodėl 5G TECH',
			'reasons_title'   => 'Kodėl mumis pasitiki užsakovai.',
			'reasons'         => array(
				array( '_id' => 'leader-1', 'title' => 'Valdoma rizika', 'text' => 'ISO 9001, ISO 14001, ISO 45001 ir SSVA kvalifikacija.' ),
				array( '_id' => 'leader-2', 'title' => 'Įrodyta patirtis', 'text' => '6000+ stočių, 2G–5G technologijos ir šešios Europos šalys.' ),
				array( '_id' => 'leader-3', 'title' => 'Kompetencijos vienoje komandoje', 'text' => 'Telekomunikacijos, energetika ir inžinerinės sistemos.' ),
				array( '_id' => 'leader-4', 'title' => 'Aiški atsakomybė', 'text' => 'Planavimas, darbų kontrolė, testavimas ir dokumentuotas perdavimas.' ),
			),
		),
		'project_managers' => array(
			'scope_eyebrow'     => 'Techninė eiga',
			'scope_title'       => 'Planavimas, darbų kontrolė ir dokumentacija.',
			'tasks'             => array(
				array( '_id' => 'task-1', 'text' => '„Site Survey“ ir esamos situacijos įvertinimas' ),
				array( '_id' => 'task-2', 'text' => 'Darbų, medžiagų ir komandos planavimas' ),
				array( '_id' => 'task-3', 'text' => 'Ericsson, Nokia, Huawei ir kitos įrangos montavimas' ),
				array( '_id' => 'task-4', 'text' => 'MW linijų konfigūravimas ir testavimas' ),
				array( '_id' => 'task-5', 'text' => '„SiteMaster“ ir kiti kokybės matavimai' ),
				array( '_id' => 'task-6', 'text' => 'Dokumentacija pagal užsakovo standartus' ),
				array( '_id' => 'task-7', 'text' => 'Tiesioginis ryšys su atsakingu projektų vadovu' ),
				array( '_id' => 'task-8', 'text' => 'Neatitikimų valdymas iki patvirtinto rezultato' ),
			),
			'equipment_eyebrow' => 'Įrangos patirtis',
			'equipment_title'   => 'Įranga, su kuria dirbame.',
			'equipment_ids'     => array(),
		),
		'home_audiences' => array(
			'title' => 'Informacija, kurios reikia prieš priimant sprendimą.',
			'lead'  => 'Patirties įrodymai, techninė apimtis ir darbo sąlygos – atskirai, be bendrų pažadų.',
			'cards' => array(
				array( '_id' => 'audience-1', 'label' => 'Vadovams', 'title' => 'Rizika, atsakomybė ir partnerio pajėgumas.', 'text' => 'Patirties skaičiai, sertifikatai, darbų valdymas ir tiesioginis kontaktas su vadovu.', 'url' => '/vadovams/' ),
				array( '_id' => 'audience-2', 'label' => 'Projektų vadovams', 'title' => 'Darbų apimtis, įranga ir dokumentacija.', 'text' => 'Nuo „Site Survey“ ir planavimo iki matavimų, neatitikimų valdymo ir perdavimo.', 'url' => '/projektu-vadovams/' ),
				array( '_id' => 'audience-3', 'label' => 'Specialistams', 'title' => 'Darbo sąlygos, mokymai ir augimo kelias.', 'text' => 'Atviros pozicijos, projektų geografija, kvalifikacijos ir 5GTECH Academy.', 'url' => '/karjera/' ),
			),
		),
	);
}

function g5tech_structured_content() {
	$saved    = get_option( 'g5tech_structured_content', array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$defaults = g5tech_structured_content_defaults();
	$content  = array();

	foreach ( $defaults as $section => $values ) {
		$content[ $section ] = wp_parse_args(
			isset( $saved[ $section ] ) && is_array( $saved[ $section ] ) ? $saved[ $section ] : array(),
			$values
		);
	}

	if (
		( ! isset( $saved['project_managers'] ) || ! array_key_exists( 'equipment_ids', $saved['project_managers'] ) )
		&& function_exists( 'g5tech_get_partners' )
	) {
		$preferred = array( 'Ericsson', 'Nokia', 'Huawei', 'Delta', 'Eltek' );
		foreach ( g5tech_get_partners( 'manufacturer' ) as $partner ) {
			if ( in_array( get_the_title( $partner ), $preferred, true ) ) {
				$content['project_managers']['equipment_ids'][] = (int) $partner->ID;
			}
		}
	}

	return $content;
}

function g5tech_structured_section( $section ) {
	$content = g5tech_structured_content();

	return $content[ $section ] ?? array();
}

function g5tech_structured_content_admin_url( $section = 'academy' ) {
	return add_query_arg(
		array(
			'post_type' => 'g5_module',
			'page'      => 'g5tech-structured-content',
			'section'   => sanitize_key( $section ),
		),
		admin_url( 'edit.php' )
	);
}

function g5tech_can_manage_structured_content( $section = '' ) {
	if ( current_user_can( 'manage_g5tech_settings' ) ) {
		return true;
	}

	return 'academy' === $section
		&& function_exists( 'g5tech_user_has_role' )
		&& g5tech_user_has_role( 'g5_hr_editor' )
		&& current_user_can( 'edit_g5_modules' );
}

function g5tech_structured_repeater_configs() {
	return array(
		'academy' => array(
			'program' => array(
				'label'       => 'Programos etapai',
				'add_label'   => 'Pridėti programos etapą',
				'empty_label' => 'Programos etapų dar nėra.',
				'schema'      => array(
					'title' => array( 'label' => 'Etapo pavadinimas', 'type' => 'text' ),
					'text'  => array( 'label' => 'Trumpas paaiškinimas', 'type' => 'textarea' ),
				),
			),
			'included' => array(
				'label'       => 'Ką suteikiame',
				'add_label'   => 'Pridėti punktą',
				'empty_label' => 'Punktų dar nėra.',
				'title_field' => 'text',
				'schema'      => array(
					'text' => array( 'label' => 'Punktas', 'type' => 'text' ),
				),
			),
		),
		'leaders' => array(
			'reasons' => array(
				'label'       => 'Pasitikėjimo argumentai',
				'add_label'   => 'Pridėti argumentą',
				'empty_label' => 'Argumentų dar nėra.',
				'schema'      => array(
					'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
					'text'  => array( 'label' => 'Trumpas paaiškinimas', 'type' => 'textarea' ),
				),
			),
		),
		'project_managers' => array(
			'tasks' => array(
				'label'       => 'Techninės apimties punktai',
				'add_label'   => 'Pridėti punktą',
				'empty_label' => 'Techninės apimties punktų dar nėra.',
				'title_field' => 'text',
				'schema'      => array(
					'text' => array( 'label' => 'Punktas', 'type' => 'text' ),
				),
			),
		),
		'home_audiences' => array(
			'cards' => array(
				'label'       => 'Auditorijų kortelės',
				'add_label'   => 'Pridėti auditoriją',
				'empty_label' => 'Auditorijų dar nėra.',
				'schema'      => array(
					'label' => array( 'label' => 'Kam skirta', 'type' => 'text' ),
					'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
					'text'  => array( 'label' => 'Trumpas paaiškinimas', 'type' => 'textarea' ),
					'url'   => array( 'label' => 'Nuoroda', 'type' => 'url' ),
				),
			),
		),
	);
}

function g5tech_add_structured_content_page() {
	add_submenu_page(
		'edit.php?post_type=g5_module',
		'Puslapių sąrašai',
		'Puslapių sąrašai',
		'edit_g5_modules',
		'g5tech-structured-content',
		'g5tech_render_structured_content_page'
	);
}
// Ekranas išjungtas: šie puslapiai redaguojami Puslapiai → blokų redaktoriuje.

function g5tech_render_structured_text_field( $section, $key, $label, $value, $textarea = false ) {
	?>
	<p>
		<label for="g5tech-structured-<?php echo esc_attr( $section . '-' . $key ); ?>"><strong><?php echo esc_html( $label ); ?></strong></label>
		<?php if ( $textarea ) : ?>
			<textarea class="large-text" rows="3" id="g5tech-structured-<?php echo esc_attr( $section . '-' . $key ); ?>" name="g5tech_structured[<?php echo esc_attr( $key ); ?>]"><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input class="large-text" type="text" id="g5tech-structured-<?php echo esc_attr( $section . '-' . $key ); ?>" name="g5tech_structured[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $value ); ?>">
		<?php endif; ?>
	</p>
	<?php
}

function g5tech_render_structured_content_page() {
	$labels = array(
		'academy'          => 'Academy',
		'leaders'          => 'Vadovams',
		'project_managers' => 'Projektų vadovams',
		'home_audiences'   => 'Titulinio auditorijos',
	);
	$section = sanitize_key( wp_unslash( $_GET['section'] ?? 'academy' ) );
	$section = isset( $labels[ $section ] ) ? $section : 'academy';

	if ( ! current_user_can( 'manage_g5tech_settings' ) ) {
		$labels  = array( 'academy' => $labels['academy'] );
		$section = 'academy';
	}

	if ( ! g5tech_can_manage_structured_content( $section ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio turinio.', '5gtech-core' ) );
	}

	$content = g5tech_structured_section( $section );
	$configs = g5tech_structured_repeater_configs();
	$page_key = 'home_audiences' === $section ? 'home' : $section;
	$page_url = g5tech_module_page_public_url( $page_key );
	?>
	<div class="wrap g5tech-admin-page g5tech-structured-admin">
		<?php
		g5tech_render_unified_admin_header(
			array(
				'title'       => $labels[ $section ] . ' turinys',
				'description' => 'Viršuje keiskite puslapio sekcijų tvarką, žemiau redaguokite korteles, etapus ir sąrašus.',
				'page_url'    => $page_url,
			)
		);
		?>
		<nav class="nav-tab-wrapper">
			<?php foreach ( $labels as $key => $label ) : ?>
				<a class="nav-tab <?php echo $key === $section ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( g5tech_structured_content_admin_url( $key ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php if ( isset( $_GET['updated'] ) ) : ?><div class="notice notice-success is-dismissible"><p>Pakeitimai išsaugoti.</p></div><?php endif; ?>

		<?php g5tech_render_page_module_manager( $page_key ); ?>

		<div class="g5tech-admin-layout">
			<div class="g5tech-admin-editor">
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="g5tech_save_structured_content">
					<input type="hidden" name="section" value="<?php echo esc_attr( $section ); ?>">
					<?php wp_nonce_field( 'g5tech_save_structured_content_' . $section ); ?>
					<?php g5tech_render_admin_content_translation_context( 'structured_' . $section, $content ); ?>

					<details class="g5tech-admin-group" open>
						<summary><span>Sekcijos turinys</span></summary>
						<div class="g5tech-admin-group__content g5tech-structured-admin__body">
				<?php if ( 'academy' === $section ) : ?>
					<?php g5tech_render_structured_text_field( $section, 'program_eyebrow', 'Programos žyma', $content['program_eyebrow'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'program_title', 'Programos antraštė', $content['program_title'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'included_eyebrow', 'Suteikiame žyma', $content['included_eyebrow'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'included_title', 'Suteikiame antraštė', $content['included_title'] ); ?>
				<?php elseif ( 'leaders' === $section ) : ?>
					<?php g5tech_render_structured_text_field( $section, 'reasons_eyebrow', 'Sekcijos žyma', $content['reasons_eyebrow'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'reasons_title', 'Sekcijos antraštė', $content['reasons_title'] ); ?>
				<?php elseif ( 'project_managers' === $section ) : ?>
					<?php g5tech_render_structured_text_field( $section, 'scope_eyebrow', 'Techninės apimties žyma', $content['scope_eyebrow'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'scope_title', 'Techninės apimties antraštė', $content['scope_title'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'equipment_eyebrow', 'Įrangos sekcijos žyma', $content['equipment_eyebrow'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'equipment_title', 'Įrangos sekcijos antraštė', $content['equipment_title'] ); ?>
				<?php else : ?>
					<?php g5tech_render_structured_text_field( $section, 'title', 'Sekcijos antraštė', $content['title'] ); ?>
					<?php g5tech_render_structured_text_field( $section, 'lead', 'Trumpas paaiškinimas', $content['lead'], true ); ?>
				<?php endif; ?>

				<?php foreach ( $configs[ $section ] ?? array() as $key => $config ) : ?>
					<h2><?php echo esc_html( $config['label'] ); ?></h2>
					<?php
					g5tech_render_repeater(
						array(
							'name'        => 'g5tech_structured[' . $key . ']',
							'items'       => $content[ $key ],
							'schema'      => $config['schema'],
							'add_label'   => $config['add_label'],
							'empty_label' => $config['empty_label'],
							'title_field' => $config['title_field'] ?? 'title',
						)
					);
					?>
				<?php endforeach; ?>

				<?php if ( 'project_managers' === $section ) : ?>
					<h2>Rodoma įranga</h2>
					<p class="description">Įrangos gamintojai valdomi bendroje „Partneriai ir įranga“ bibliotekoje.</p>
					<div class="g5tech-structured-equipment">
						<?php foreach ( g5tech_get_partners( 'manufacturer' ) as $partner ) : ?>
							<label><input type="checkbox" name="g5tech_structured[equipment_ids][]" value="<?php echo (int) $partner->ID; ?>" <?php checked( in_array( (int) $partner->ID, array_map( 'absint', $content['equipment_ids'] ), true ) ); ?>> <?php echo esc_html( get_the_title( $partner ) ); ?></label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
						</div>
					</details>
					<div class="g5tech-admin-actions">
						<?php submit_button( 'Išsaugoti pakeitimus', 'primary', 'submit', false ); ?>
						<a class="button" href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener">Atidaryti puslapį ↗</a>
					</div>
				</form>
			</div>
			<?php g5tech_render_unified_admin_preview( $page_url, $labels[ $section ] . ' puslapio' ); ?>
		</div>
	</div>
	<style>
		.g5tech-structured-admin__body{padding:18px}
		.g5tech-structured-admin__body>p>label{display:block;margin-bottom:6px}
		.g5tech-structured-equipment{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:15px}
		.g5tech-structured-equipment label{border:1px solid #dcdcde;padding:12px}
	</style>
	<?php
}

function g5tech_save_structured_content() {
	$section = sanitize_key( wp_unslash( $_POST['section'] ?? '' ) );
	$allowed = array( 'academy', 'leaders', 'project_managers', 'home_audiences' );

	if ( ! in_array( $section, $allowed, true ) ) {
		wp_die( esc_html__( 'Neteisinga turinio grupė.', '5gtech-core' ) );
	}

	if ( ! g5tech_can_manage_structured_content( $section ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio turinio.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_save_structured_content_' . $section );
	g5tech_save_admin_content_translations( 'structured_' . $section );

	$submitted = isset( $_POST['g5tech_structured'] ) && is_array( $_POST['g5tech_structured'] )
		? wp_unslash( $_POST['g5tech_structured'] )
		: array();
	$all       = g5tech_structured_content();
	$defaults  = g5tech_structured_content_defaults();
	$configs   = g5tech_structured_repeater_configs();
	$clean     = array();

	foreach ( $defaults[ $section ] as $key => $default ) {
		if ( is_array( $default ) ) {
			continue;
		}
		$clean[ $key ] = sanitize_textarea_field( $submitted[ $key ] ?? $default );
	}

	foreach ( $configs[ $section ] ?? array() as $key => $config ) {
		$clean[ $key ] = g5tech_sanitize_repeater_items(
			$submitted[ $key ] ?? array(),
			$config['schema']
		);
	}

	if ( 'project_managers' === $section ) {
		$clean['equipment_ids'] = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', (array) ( $submitted['equipment_ids'] ?? array() ) )
				)
			)
		);
	}

	$all[ $section ] = wp_parse_args( $clean, $defaults[ $section ] );
	update_option( 'g5tech_structured_content', $all, false );

	wp_safe_redirect( add_query_arg( 'updated', '1', g5tech_structured_content_admin_url( $section ) ) );
	exit;
}
add_action( 'admin_post_g5tech_save_structured_content', 'g5tech_save_structured_content' );
