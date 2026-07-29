<?php
/**
 * Darbo pozicijos ir Karjeros puslapis.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_job_type() {
	register_post_type(
		'g5_job',
		array(
			'labels' => array(
				'name'               => 'Darbo pozicijos',
				'singular_name'      => 'Darbo pozicija',
				'add_new'            => 'Kurti naują',
				'add_new_item'       => 'Kurti naują darbo skelbimą',
				'edit_item'          => 'Redaguoti darbo poziciją',
				'new_item'           => 'Nauja darbo pozicija',
				'view_item'          => 'Peržiūrėti poziciją',
				'search_items'       => 'Ieškoti pozicijų',
				'not_found'          => 'Pozicijų nerasta',
				'not_found_in_trash' => 'Šiukšlinėje pozicijų nėra',
				'all_items'          => 'Visi darbo skelbimai',
				'menu_name'          => 'Karjera · skelbimai',
				'name_admin_bar'     => 'Darbo skelbimas',
			),
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'karjera',
				'with_front' => false,
			),
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-id',
			'menu_position' => 24,
			'supports'      => array( 'title', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'g5tech_register_job_type' );

function g5tech_job_fields() {
	return array(
		'g5_job_group' => array(
			'label'   => 'Pozicijų grupė',
			'type'    => 'select',
			'options' => array(
				'lithuania' => 'Lietuvos projektai',
				'europe'    => 'Europos projektai',
				'office'    => 'Biuro pozicijos',
			),
		),
		'g5_job_level' => array(
			'label' => 'Lygis arba sritis',
			'type'  => 'text',
		),
		'g5_job_location' => array(
			'label' => 'Darbo arba projekto vieta',
			'type'  => 'text',
		),
		'g5_job_salary' => array(
			'label'       => 'Atlygis',
			'type'        => 'text',
			'description' => 'Pvz. 2 500–2 750+ €',
		),
		'g5_job_rotation' => array(
			'label'       => 'Komandiruotės trukmė',
			'type'        => 'text',
			'description' => 'Pvz. 6–8 sav.',
		),
		'g5_job_driving' => array(
			'label'       => 'Vairuotojo kategorija',
			'type'        => 'text',
			'description' => 'Pvz. B',
		),
		'g5_job_summary' => array(
			'label'       => 'Trumpas pozicijos aprašymas',
			'type'        => 'textarea',
			'description' => '2–3 sakiniai puslapio pradžiai.',
		),
		'g5_job_responsibilities' => array(
			'label'       => 'Pagrindinės atsakomybės',
			'type'        => 'textarea',
			'description' => 'Viena atsakomybė vienoje eilutėje.',
		),
		'g5_job_requirements' => array(
			'label'       => 'Ko tikimės',
			'type'        => 'textarea',
			'description' => 'Vienas reikalavimas vienoje eilutėje.',
		),
		'g5_job_offer' => array(
			'label'       => 'Ką suteikiame',
			'type'        => 'textarea',
			'description' => 'Vienas punktas vienoje eilutėje.',
		),
		'g5_job_expires' => array(
			'label'       => 'Skelbimas galioja iki',
			'type'        => 'date',
			'description' => 'Palikus tuščią, galiojimo data neribojama.',
		),
	);
}

function g5tech_add_job_meta_box() {
	add_meta_box(
		'g5tech-job-details',
		'Pozicijos informacija',
		'g5tech_render_job_meta_box',
		'g5_job',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_job_meta_box' );

function g5tech_add_career_jobs_meta_box( $post ) {
	if (
		! $post instanceof WP_Post
		|| 'karjera' !== $post->post_name
		|| ! current_user_can( 'edit_g5_jobs' )
	) {
		return;
	}

	add_meta_box(
		'g5tech-career-jobs',
		'Darbo skelbimų valdymas',
		'g5tech_render_career_jobs_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_page', 'g5tech_add_career_jobs_meta_box' );

function g5tech_render_career_jobs_meta_box() {
	$all_jobs_url = admin_url( 'edit.php?post_type=g5_job' );
	$new_job_url  = admin_url( 'post-new.php?post_type=g5_job' );
	?>
	<p>Šiame puslapyje automatiškai rodomi aktyvūs darbo skelbimai. Juos redaguokite atskiroje skiltyje.</p>
	<p>
		<a class="button button-primary" href="<?php echo esc_url( $all_jobs_url ); ?>">Visi darbo skelbimai</a>
		<a class="button" href="<?php echo esc_url( $new_job_url ); ?>">Kurti naują skelbimą</a>
	</p>
	<?php
}

function g5tech_career_page_edit_redirect() {
	if (
		! isset( $_GET['post'], $_GET['action'] )
		|| 'edit' !== sanitize_key( wp_unslash( $_GET['action'] ) )
		|| ! current_user_can( 'edit_g5_jobs' )
	) {
		return;
	}

	$post_id = absint( $_GET['post'] );

	if ( 'page' !== get_post_type( $post_id ) || 'karjera' !== get_post_field( 'post_name', $post_id ) ) {
		return;
	}

	wp_safe_redirect( admin_url( 'edit.php?post_type=g5_job&g5_from=career' ) );
	exit;
}
add_action( 'load-post.php', 'g5tech_career_page_edit_redirect' );

function g5tech_career_page_row_actions( $actions, $post ) {
	if (
		'page' !== $post->post_type
		|| 'karjera' !== $post->post_name
		|| ! current_user_can( 'edit_g5_jobs' )
	) {
		return $actions;
	}

	$actions['g5_jobs'] = sprintf(
		'<a href="%s">Darbo skelbimai</a>',
		esc_url( admin_url( 'edit.php?post_type=g5_job' ) )
	);
	$actions['g5_new_job'] = sprintf(
		'<a href="%s">Kurti naują skelbimą</a>',
		esc_url( admin_url( 'post-new.php?post_type=g5_job' ) )
	);

	return $actions;
}
add_filter( 'page_row_actions', 'g5tech_career_page_row_actions', 10, 2 );

function g5tech_career_jobs_admin_notice() {
	$post_type = isset( $_GET['post_type'] )
		? sanitize_key( wp_unslash( $_GET['post_type'] ) )
		: '';
	$source = isset( $_GET['g5_from'] )
		? sanitize_key( wp_unslash( $_GET['g5_from'] ) )
		: '';

	if (
		'g5_job' !== $post_type
		|| 'career' !== $source
	) {
		return;
	}
	?>
	<div class="notice notice-info">
		<p><strong>Karjeros puslapyje darbo skelbimai atsinaujina automatiškai.</strong> Redaguokite esamą skelbimą arba spauskite „Kurti naują“.</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'g5tech_career_jobs_admin_notice' );

function g5tech_career_content_admin_url( $section = '' ) {
	$url = admin_url( 'edit.php?post_type=g5_job&page=g5tech-career-content' );

	if ( ! $section ) {
		return $url;
	}

	$section = sanitize_key( $section );

	return add_query_arg( 'section', $section, $url ) . '#' . sanitize_html_class( $section );
}

function g5tech_career_page_defaults() {
	return array(
		'hero_eyebrow'       => 'Karjera',
		'hero_title'         => 'Karjera telekomunikacijų ir energetikos projektuose.',
		'hero_lead'          => 'Projektai Lietuvoje ir kitose Europos šalyse, praktiniai mokymai, darbui reikalingi sertifikatai bei saugi darbo aplinka.',
		'benefits_eyebrow'   => 'Ką suteikiame',
		'benefits_title'     => 'Darbo sąlygos.',
		'benefit_1_number'   => '01',
		'benefit_1_title'    => 'Mokymai ir kvalifikacijos',
		'benefit_1_text'     => 'Apmokame aukštalipio, elektrosaugos, pirmosios pagalbos ir kitus darbui reikalingus mokymus.',
		'benefit_2_number'   => '02',
		'benefit_2_title'    => 'Darbo priemonės',
		'benefit_2_text'     => 'Suteikiame darbo aprangą, įrankius ir sertifikuotas asmenines apsaugos priemones.',
		'benefit_3_number'   => '03',
		'benefit_3_title'    => 'Europos projektai',
		'benefit_3_text'     => 'Apmokame su darbu susijusias keliones, transportą ir apgyvendinimą komandiruočių metu.',
		'positions_eyebrow'  => 'Pozicijos',
		'positions_title'    => 'Atviros pozicijos.',
		'positions_empty'    => 'Šiuo metu atvirų pozicijų nėra. Galite palikti CV – susisieksime atsiradus tinkamai pozicijai.',
		'selection_eyebrow'  => 'Atranka',
		'selection_title'    => 'Kaip vyksta atranka.',
		'selection_1_number' => '01',
		'selection_1_title'  => 'Kandidatavimas',
		'selection_1_text'   => 'Pasirinkite poziciją arba palikite CV.',
		'selection_2_number' => '02',
		'selection_2_title'  => 'Vertinimas',
		'selection_2_text'   => 'Peržiūrime patirtį ir tinkamumą pozicijai.',
		'selection_3_number' => '03',
		'selection_3_title'  => 'Pokalbis',
		'selection_3_text'   => 'Aptariame patirtį, lūkesčius ir darbo specifiką.',
		'selection_4_number' => '04',
		'selection_4_title'  => 'Įgūdžiai',
		'selection_4_text'   => 'Prireikus įvertiname technines žinias.',
		'selection_5_number' => '05',
		'selection_5_title'  => 'Pasiūlymas',
		'selection_5_text'   => 'Suderiname sąlygas ir prisijungimo datą.',
		'growth_eyebrow'     => 'Augimas',
		'growth_title'       => 'Mokymai ir praktinis pasirengimas darbui.',
		'growth_1_label'     => '5GTECH Academy',
		'growth_1_title'     => 'Praktiniai mokymai būsimiems telekomunikacijų specialistams.',
		'growth_1_link'      => 'Apie programą →',
		'growth_1_url'       => '/akademija/',
		'growth_2_label'     => 'Mokymai',
		'growth_2_title'     => 'Praktinė mokymų salė ir darbas su realia įranga.',
		'growth_2_link'      => 'Apie mokymus →',
		'growth_2_url'       => '/mokymai/',
		'growth_3_label'     => 'DUK',
		'growth_3_title'     => 'Atsakymai apie komandiruotes, sąlygas ir saugą.',
		'growth_3_link'      => 'Peržiūrėti DUK →',
		'growth_3_url'       => '/duk/',
	);
}

function g5tech_career_page_content() {
	$saved = get_option( 'g5tech_career_page_content', array() );
	$saved = is_array( $saved ) ? $saved : array();
	$data  = wp_parse_args( $saved, g5tech_career_page_defaults() );

	if ( ! array_key_exists( 'benefits', $saved ) ) {
		$data['benefits'] = g5tech_legacy_repeater_items(
			$data,
			'benefit_',
			3,
			array( 'title' => 'title', 'text' => 'text' )
		);
	}

	if ( ! array_key_exists( 'selection_steps', $saved ) ) {
		$data['selection_steps'] = g5tech_legacy_repeater_items(
			$data,
			'selection_',
			5,
			array( 'title' => 'title', 'text' => 'text' )
		);
	}

	if ( ! array_key_exists( 'growth_cards', $saved ) ) {
		$data['growth_cards'] = g5tech_legacy_repeater_items(
			$data,
			'growth_',
			3,
			array( 'label' => 'label', 'title' => 'title', 'link' => 'link', 'url' => 'url' )
		);
	}

	return $data;
}

function g5tech_career_repeater_configs() {
	return array(
		'benefits' => array(
			'key'         => 'benefits',
			'add_label'   => 'Pridėti privalumą',
			'empty_label' => 'Privalumų dar nėra.',
			'schema'      => array(
				'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
				'text'  => array( 'label' => 'Tekstas', 'type' => 'textarea' ),
			),
		),
		'selection' => array(
			'key'         => 'selection_steps',
			'add_label'   => 'Pridėti atrankos etapą',
			'empty_label' => 'Atrankos etapų dar nėra.',
			'schema'      => array(
				'title' => array( 'label' => 'Etapas', 'type' => 'text' ),
				'text'  => array( 'label' => 'Trumpas paaiškinimas', 'type' => 'textarea' ),
			),
		),
		'growth' => array(
			'key'         => 'growth_cards',
			'add_label'   => 'Pridėti augimo kortelę',
			'empty_label' => 'Augimo kortelių dar nėra.',
			'schema'      => array(
				'label' => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
				'title' => array( 'label' => 'Antraštė', 'type' => 'text' ),
				'link'  => array( 'label' => 'Nuorodos tekstas', 'type' => 'text' ),
				'url'   => array( 'label' => 'Nuoroda', 'type' => 'url' ),
			),
		),
	);
}

function g5tech_career_content_groups() {
	return array(
		'hero' => array(
			'label'  => 'Puslapio pradžia',
			'fields' => array( 'hero_eyebrow', 'hero_title', 'hero_lead' ),
		),
		'benefits' => array(
			'label'  => 'Kodėl verta rinktis',
			'fields' => array( 'benefits_eyebrow', 'benefits_title' ),
		),
		'positions' => array(
			'label'  => 'Atviros pozicijos',
			'fields' => array( 'positions_eyebrow', 'positions_title', 'positions_empty' ),
		),
		'selection' => array(
			'label'  => 'Atrankos eiga',
			'fields' => array( 'selection_eyebrow', 'selection_title' ),
		),
		'growth' => array(
			'label'  => 'Augimas',
			'fields' => array( 'growth_eyebrow', 'growth_title' ),
		),
	);
}

function g5tech_career_field_label( $key ) {
	$labels = array(
		'hero_eyebrow'      => 'Trumpa žyma',
		'hero_title'        => 'Pagrindinė antraštė',
		'hero_lead'         => 'Įžangos tekstas',
		'benefits_eyebrow'  => 'Trumpa žyma',
		'benefits_title'    => 'Sekcijos antraštė',
		'positions_eyebrow' => 'Trumpa žyma',
		'positions_title'   => 'Sekcijos antraštė',
		'positions_empty'   => 'Tekstas, kai pozicijų nėra',
		'selection_eyebrow' => 'Trumpa žyma',
		'selection_title'   => 'Sekcijos antraštė',
		'growth_eyebrow'    => 'Trumpa žyma',
		'growth_title'      => 'Sekcijos antraštė',
	);

	if ( isset( $labels[ $key ] ) ) {
		return $labels[ $key ];
	}

	if ( preg_match( '/^(benefit|selection)_(\\d+)_(number|title|text)$/', $key, $match ) ) {
		$parts = array(
			'number' => 'numeris',
			'title'  => 'antraštė',
			'text'   => 'tekstas',
		);

		return $match[2] . ' punkto ' . $parts[ $match[3] ];
	}

	if ( preg_match( '/^growth_(\\d+)_(label|title|link|url)$/', $key, $match ) ) {
		$parts = array(
			'label' => 'žyma',
			'title' => 'antraštė',
			'link'  => 'nuorodos tekstas',
			'url'   => 'nuoroda',
		);

		return $match[1] . ' kortelės ' . $parts[ $match[2] ];
	}

	return $key;
}

function g5tech_add_career_content_page() {
	add_submenu_page(
		'edit.php?post_type=g5_job',
		'Karjeros puslapio turinys',
		'Karjeros puslapis',
		'edit_g5_jobs',
		'g5tech-career-content',
		'g5tech_render_career_content_page'
	);
}
add_action( 'admin_menu', 'g5tech_add_career_content_page', 20 );

function g5tech_render_career_content_page() {
	if ( ! current_user_can( 'edit_g5_jobs' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti Karjeros puslapio.', '5gtech-core' ) );
	}

	$content = g5tech_career_page_content();
	$groups  = g5tech_career_content_groups();
	$repeaters = g5tech_career_repeater_configs();
	$page_url = home_url( '/karjera/' );
	?>
	<div class="wrap g5tech-admin-page g5tech-career-content-admin">
		<?php
		g5tech_render_unified_admin_header(
			array(
				'title'       => 'Karjeros puslapis',
				'description' => 'Viršuje keiskite sekcijų tvarką, žemiau redaguokite jų turinį. Darbo pozicijos valdomos atskirame sąraše.',
				'page_url'    => $page_url,
				'actions'     => array(
					array(
						'label' => 'Darbo pozicijos',
						'url'   => admin_url( 'edit.php?post_type=g5_job' ),
					),
					array(
						'label'   => 'Kurti darbo poziciją',
						'url'     => admin_url( 'post-new.php?post_type=g5_job' ),
						'primary' => true,
					),
				),
			)
		);
		?>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Karjeros puslapio turinys išsaugotas.</p></div>
		<?php endif; ?>

		<?php g5tech_render_page_module_manager( 'career' ); ?>

		<div class="g5tech-admin-layout">
			<div class="g5tech-admin-editor">
				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="g5tech_save_career_content">
					<?php wp_nonce_field( 'g5tech_save_career_content' ); ?>

					<?php foreach ( $groups as $group_key => $group ) : ?>
						<?php $is_fixed = 'hero' === $group_key; ?>
						<details class="g5tech-admin-group g5tech-career-content-group" id="<?php echo esc_attr( $group_key ); ?>" <?php echo $is_fixed || ( isset( $_GET['section'] ) && $group_key === sanitize_key( wp_unslash( $_GET['section'] ) ) ) ? 'open' : ''; ?>>
							<summary>
								<span><?php echo esc_html( $group['label'] ); ?></span>
								<?php if ( $is_fixed ) : ?><span class="g5tech-admin-group__meta">Fiksuota vieta</span><?php endif; ?>
							</summary>
							<div class="g5tech-admin-group__content g5tech-career-content-group__fields">
								<?php foreach ( $group['fields'] as $field_key ) : ?>
									<?php $is_textarea = str_ends_with( $field_key, '_text' ) || in_array( $field_key, array( 'hero_lead', 'positions_empty' ), true ); ?>
									<p>
										<label for="g5tech-career-<?php echo esc_attr( $field_key ); ?>"><strong><?php echo esc_html( g5tech_career_field_label( $field_key ) ); ?></strong></label>
										<?php if ( $is_textarea ) : ?>
											<textarea class="large-text" id="g5tech-career-<?php echo esc_attr( $field_key ); ?>" name="g5tech_career_content[<?php echo esc_attr( $field_key ); ?>]" rows="3"><?php echo esc_textarea( $content[ $field_key ] ); ?></textarea>
										<?php else : ?>
											<input class="large-text" id="g5tech-career-<?php echo esc_attr( $field_key ); ?>" name="g5tech_career_content[<?php echo esc_attr( $field_key ); ?>]" type="text" value="<?php echo esc_attr( $content[ $field_key ] ); ?>">
										<?php endif; ?>
									</p>
								<?php endforeach; ?>
								<?php if ( isset( $repeaters[ $group_key ] ) ) : ?>
									<?php
									$config = $repeaters[ $group_key ];
									g5tech_render_repeater(
										array(
											'name'        => 'g5tech_career_content[' . $config['key'] . ']',
											'items'       => $content[ $config['key'] ],
											'schema'      => $config['schema'],
											'add_label'   => $config['add_label'],
											'empty_label' => $config['empty_label'],
										)
									);
									?>
								<?php endif; ?>
							</div>
						</details>
					<?php endforeach; ?>

					<div class="g5tech-admin-actions">
						<?php submit_button( 'Išsaugoti pakeitimus', 'primary', 'submit', false ); ?>
						<a class="button" href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener">Atidaryti puslapį ↗</a>
					</div>
				</form>
			</div>
			<?php g5tech_render_unified_admin_preview( $page_url, 'Karjeros puslapio' ); ?>
		</div>
	</div>
	<style>
		.g5tech-career-content-group__fields label{display:block;margin-bottom:6px}
	</style>
	<?php
}

function g5tech_save_career_content() {
	if ( ! current_user_can( 'edit_g5_jobs' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti Karjeros puslapio.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_save_career_content' );

	$defaults  = g5tech_career_page_defaults();
	$submitted = isset( $_POST['g5tech_career_content'] )
		? (array) wp_unslash( $_POST['g5tech_career_content'] )
		: array();
	$sanitized = array();

	foreach ( $defaults as $key => $default ) {
		$value = $submitted[ $key ] ?? $default;

		if ( str_ends_with( $key, '_url' ) ) {
			$sanitized[ $key ] = esc_url_raw( $value );
		} elseif ( str_ends_with( $key, '_text' ) || in_array( $key, array( 'hero_lead', 'positions_empty' ), true ) ) {
			$sanitized[ $key ] = sanitize_textarea_field( $value );
		} else {
			$sanitized[ $key ] = sanitize_text_field( $value );
		}
	}

	foreach ( g5tech_career_repeater_configs() as $config ) {
		$sanitized[ $config['key'] ] = g5tech_sanitize_repeater_items(
			$submitted[ $config['key'] ] ?? array(),
			$config['schema']
		);
	}

	update_option( 'g5tech_career_page_content', $sanitized, false );
	wp_safe_redirect( add_query_arg( 'updated', '1', g5tech_career_content_admin_url() ) );
	exit;
}
add_action( 'admin_post_g5tech_save_career_content', 'g5tech_save_career_content' );

function g5tech_render_job_meta_box( $post ) {
	$active = metadata_exists( 'post', $post->ID, 'g5_job_active' )
		? (bool) get_post_meta( $post->ID, 'g5_job_active', true )
		: true;

	wp_nonce_field( 'g5tech_save_job', 'g5tech_job_nonce' );

	echo '<p class="description">Pavadinimas įrašomas puslapio viršuje. Skelbimo struktūra ir dizainas visoms pozicijoms vienodi.</p>';

	foreach ( g5tech_job_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<div style="margin:0 0 20px;">';
		printf(
			'<label for="%1$s" style="display:block;font-weight:600;margin-bottom:6px;">%2$s</label>',
			esc_attr( $key ),
			esc_html( $field['label'] )
		);

		if ( 'select' === $field['type'] ) {
			printf( '<select id="%1$s" name="%1$s">', esc_attr( $key ) );
			foreach ( $field['options'] as $option_value => $option_label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $option_value ),
					selected( $value ?: 'lithuania', $option_value, false ),
					esc_html( $option_label )
				);
			}
			echo '</select>';
		} elseif ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea class="large-text" id="%1$s" name="%1$s" rows="%2$d">%3$s</textarea>',
				esc_attr( $key ),
				in_array( $key, array( 'g5_job_responsibilities', 'g5_job_requirements', 'g5_job_offer' ), true ) ? 7 : 4,
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
	?>
	<p>
		<label>
			<input type="checkbox" name="g5_job_active" value="1" <?php checked( $active ); ?>>
			<strong>Rodyti poziciją Karjeros puslapyje ir priimti kandidatūras</strong>
		</label>
	</p>
	<?php
}

function g5tech_sanitize_job_group( $value ) {
	return in_array( $value, array( 'lithuania', 'europe', 'office' ), true )
		? $value
		: 'lithuania';
}

function g5tech_save_job_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_job_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_job_nonce'] ) ),
			'g5tech_save_job'
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

	foreach ( g5tech_job_fields() as $key => $field ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';

		if ( 'select' === $field['type'] ) {
			$value = g5tech_sanitize_job_group( sanitize_text_field( $value ) );
		} elseif ( 'textarea' === $field['type'] ) {
			$value = sanitize_textarea_field( $value );
		} else {
			$value = sanitize_text_field( $value );
		}

		update_post_meta( $post_id, $key, $value );
	}

	update_post_meta( $post_id, 'g5_job_active', isset( $_POST['g5_job_active'] ) ? '1' : '0' );
}
add_action( 'save_post_g5_job', 'g5tech_save_job_meta' );

function g5tech_job_admin_columns( $columns ) {
	$updated = array();

	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated['g5_job_location'] = 'Vieta';
			$updated['g5_job_group']    = 'Grupė';
			$updated['g5_job_expires']  = 'Galioja iki';
			$updated['g5_job_status']   = 'Rodoma svetainėje';
		}
	}

	unset( $updated['date'] );

	return $updated;
}
add_filter( 'manage_g5_job_posts_columns', 'g5tech_job_admin_columns' );

function g5tech_job_group_label( $group ) {
	$labels = array(
		'lithuania' => 'Lietuvos projektai',
		'europe'    => 'Europos projektai',
		'office'    => 'Biuro pozicijos',
	);

	return $labels[ $group ] ?? 'Lietuvos projektai';
}

function g5tech_job_admin_column_content( $column, $post_id ) {
	if ( 'g5_job_location' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'g5_job_location', true ) ?: '—' );
	}

	if ( 'g5_job_group' === $column ) {
		echo esc_html( g5tech_job_group_label( get_post_meta( $post_id, 'g5_job_group', true ) ) );
	}

	if ( 'g5_job_expires' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'g5_job_expires', true ) ?: 'Neribojama' );
	}

	if ( 'g5_job_status' === $column ) {
		echo g5tech_job_is_active( $post_id )
			? '<strong style="color:#008a20;">Taip</strong>'
			: '<strong style="color:#b32d2e;">Ne</strong>';
	}
}
add_action( 'manage_g5_job_posts_custom_column', 'g5tech_job_admin_column_content', 10, 2 );

function g5tech_job_lines( $post_id, $key ) {
	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $post_id, $key, true ) )
			)
		)
	);
}

function g5tech_job_is_active( $post_id ) {
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}

	if ( metadata_exists( 'post', $post_id, 'g5_job_active' ) && ! get_post_meta( $post_id, 'g5_job_active', true ) ) {
		return false;
	}

	$expires = get_post_meta( $post_id, 'g5_job_expires', true );

	return ! $expires || $expires >= wp_date( 'Y-m-d' );
}

function g5tech_get_active_jobs( $group = '' ) {
	$jobs = get_posts(
		array(
			'post_type'      => 'g5_job',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'meta_query'     => $group
				? array(
					array(
						'key'   => 'g5_job_group',
						'value' => $group,
					),
				)
				: array(),
		)
	);

	return array_values(
		array_filter(
			$jobs,
			function( $job ) {
				return g5tech_job_is_active( $job->ID );
			}
		)
	);
}

function g5tech_register_job_blocks() {
	register_block_type(
		'g5tech/career-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH karjera',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_career_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);

	register_block_type(
		'g5tech/job-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH darbo pozicija',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_job_page',
			'uses_context'    => array( 'postId', 'postType' ),
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_job_blocks' );

function g5tech_render_job_list( $group ) {
	$jobs = g5tech_get_active_jobs( $group );

	if ( ! $jobs ) {
		return '';
	}

	ob_start();
	?>
	<div class="job-list">
		<?php foreach ( $jobs as $job ) : ?>
			<?php
			$level    = get_post_meta( $job->ID, 'g5_job_level', true );
			$location = get_post_meta( $job->ID, 'g5_job_location', true );
			$salary   = get_post_meta( $job->ID, 'g5_job_salary', true );
			$detail   = 'europe' === $group && $salary ? $salary : $location;
			?>
			<a class="job-row" href="<?php echo esc_url( get_permalink( $job ) ); ?>">
				<strong><?php echo esc_html( get_the_title( $job ) ); ?></strong>
				<span><?php echo esc_html( $level ); ?></span>
				<span><?php echo esc_html( $detail ); ?> →</span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_career_page_legacy() {
	$content = g5tech_career_page_content();
	$groups = array(
		'lithuania' => 'Lietuvos projektai',
		'europe'    => 'Europos projektai',
		'office'    => 'Biuro pozicijos',
	);
	$has_jobs = (bool) g5tech_get_active_jobs();

	ob_start();
	?>
	<section class="inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid">
			<div class="inner-hero__copy">
				<div class="g5-eyebrow"><?php echo esc_html( $content['hero_eyebrow'] ); ?></div>
				<h1 class="g5-display-xl" id="page-title"><?php echo esc_html( $content['hero_title'] ); ?></h1>
				<p class="g5-body-lg"><?php echo esc_html( $content['hero_lead'] ); ?></p>
				<div class="inner-hero__actions">
					<a class="g5-button g5-button--primary" href="#positions">Peržiūrėti pozicijas <span class="g5-button__icon" aria-hidden="true">↓</span></a>
					<a class="g5-button g5-button--outline-light" href="<?php echo esc_url( home_url( '/kandidatuoti/' ) ); ?>">Palikti CV</a>
				</div>
			</div>
		</div>
	</section>

	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="benefits-title" data-g5-core-module="career_benefits">
		<div class="g5-container section-head"><div class="g5-eyebrow"><?php echo esc_html( $content['benefits_eyebrow'] ); ?></div><div class="section-head__copy"><h2 class="g5-display-md" id="benefits-title"><?php echo esc_html( $content['benefits_title'] ); ?></h2></div></div>
		<div class="g5-container card-grid">
			<?php foreach ( $content['benefits'] as $index => $benefit ) : ?>
				<div class="info-card"><span class="info-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( $benefit['title'] ); ?></h3><p><?php echo esc_html( $benefit['text'] ); ?></p></div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="g5-section g5-grid-lines" id="positions" aria-labelledby="positions-title" data-g5-core-module="career_positions">
		<div class="g5-container section-head"><div class="g5-eyebrow"><?php echo esc_html( $content['positions_eyebrow'] ); ?></div><div class="section-head__copy"><h2 class="g5-display-md" id="positions-title"><?php echo esc_html( $content['positions_title'] ); ?></h2></div></div>
		<div class="g5-container">
			<?php if ( $has_jobs ) : ?>
				<?php foreach ( $groups as $group_key => $group_label ) : ?>
					<?php $list = g5tech_render_job_list( $group_key ); ?>
					<?php if ( $list ) : ?>
						<div class="g5-job-group">
							<h3 class="g5-heading-md"><?php echo esc_html( $group_label ); ?></h3>
							<?php echo $list; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="g5-body-lg"><?php echo esc_html( $content['positions_empty'] ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="selection-title" data-g5-core-module="career_selection">
		<div class="g5-container section-head section-head--dark"><div class="g5-eyebrow"><?php echo esc_html( $content['selection_eyebrow'] ); ?></div><div class="section-head__copy"><h2 class="g5-display-md" id="selection-title"><?php echo esc_html( $content['selection_title'] ); ?></h2></div></div>
		<ol class="g5-container steps">
			<?php foreach ( $content['selection_steps'] as $index => $step ) : ?>
				<li><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( $step['title'] ); ?></strong><p><?php echo esc_html( $step['text'] ); ?></p></li>
			<?php endforeach; ?>
		</ol>
	</section>

	<section class="g5-section g5-grid-lines" aria-labelledby="growth-title" data-g5-core-module="career_growth">
		<div class="g5-container section-head"><div class="g5-eyebrow"><?php echo esc_html( $content['growth_eyebrow'] ); ?></div><div class="section-head__copy"><h2 class="g5-display-md" id="growth-title"><?php echo esc_html( $content['growth_title'] ); ?></h2></div></div>
		<div class="g5-container card-grid">
			<?php foreach ( $content['growth_cards'] as $card ) : ?>
				<?php
				$link = $card['url'];
				$link = str_starts_with( $link, 'http://' ) || str_starts_with( $link, 'https://' ) ? $link : home_url( '/' . ltrim( $link, '/' ) );
				?>
				<a class="info-card" href="<?php echo esc_url( $link ); ?>"><span class="info-card__number"><?php echo esc_html( $card['label'] ); ?></span><h3 class="g5-heading-md"><?php echo esc_html( $card['title'] ); ?></h3><span class="info-card__link"><?php echo esc_html( $card['link'] ); ?></span></a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php echo g5tech_render_page_modules( 'career' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_career_page() {
	return g5tech_compose_modular_page( 'career', g5tech_get_legacy_page_html( 'career' ) );
}

function g5tech_job_page_post_id( $block ) {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}

	return get_the_ID();
}

function g5tech_render_job_page( $attributes, $content, $block ) {
	$post_id          = g5tech_job_page_post_id( $block );
	$group            = get_post_meta( $post_id, 'g5_job_group', true ) ?: 'lithuania';
	$group_labels     = array(
		'lithuania' => 'Lietuvos projektai',
		'europe'    => 'Europos projektai',
		'office'    => 'Biuro pozicijos',
	);
	$level            = get_post_meta( $post_id, 'g5_job_level', true );
	$location         = get_post_meta( $post_id, 'g5_job_location', true );
	$salary           = get_post_meta( $post_id, 'g5_job_salary', true );
	$rotation         = get_post_meta( $post_id, 'g5_job_rotation', true );
	$driving          = get_post_meta( $post_id, 'g5_job_driving', true );
	$summary          = get_post_meta( $post_id, 'g5_job_summary', true );
	$responsibilities = g5tech_job_lines( $post_id, 'g5_job_responsibilities' );
	$requirements     = g5tech_job_lines( $post_id, 'g5_job_requirements' );
	$offer            = g5tech_job_lines( $post_id, 'g5_job_offer' );
	$apply_url        = add_query_arg( 'pozicija', $post_id, home_url( '/kandidatuoti/' ) );

	ob_start();
	?>
	<section class="inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid">
			<div class="inner-hero__copy">
				<nav class="breadcrumbs" aria-label="Puslapio kelias"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a><span>/</span><a href="<?php echo esc_url( home_url( '/karjera/' ) ); ?>">Karjera</a><span>/</span><span><?php echo esc_html( get_the_title( $post_id ) ); ?></span></nav>
				<div class="g5-eyebrow"><?php echo esc_html( $group_labels[ $group ] ?? $group_labels['lithuania'] ); ?><?php echo $level ? ' · ' . esc_html( $level ) : ''; ?></div>
				<h1 class="g5-display-xl" id="page-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
				<?php if ( $summary ) : ?><p class="g5-body-lg"><?php echo esc_html( $summary ); ?></p><?php endif; ?>
				<div class="inner-hero__actions"><a class="g5-button g5-button--primary" href="<?php echo esc_url( $apply_url ); ?>">Kandidatuoti <span class="g5-button__icon" aria-hidden="true">→</span></a></div>
			</div>
		</div>
	</section>

	<?php
	$conditions = array_filter(
		array(
			array( $salary, 'per mėnesį į rankas' ),
			array( $rotation, 'įprasta komandiruotė' ),
			array( $location, 'projekto arba darbo vieta' ),
			array( $driving, 'vairuotojo kategorija' ),
		),
		function( $condition ) {
			return (bool) $condition[0];
		}
	);
	?>
	<?php if ( $conditions ) : ?>
		<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-label="Pagrindinės darbo sąlygos">
			<div class="g5-container stats-grid">
				<?php foreach ( $conditions as $condition ) : ?><div class="stat"><strong><?php echo esc_html( $condition[0] ); ?></strong><span><?php echo esc_html( $condition[1] ); ?></span></div><?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="g5-section g5-grid-lines" aria-labelledby="role-title">
		<div class="g5-container section-head"><div class="g5-eyebrow">Darbo pobūdis</div><div class="section-head__copy"><h2 class="g5-display-md" id="role-title">Pagrindinės atsakomybės.</h2></div></div>
		<div class="g5-container split-layout">
			<div class="split-layout__main"><?php if ( $responsibilities ) : ?><ul class="check-list"><?php foreach ( $responsibilities as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul><?php endif; ?></div>
			<?php if ( $requirements ) : ?><aside class="split-layout__side"><h3 class="g5-heading-md">Ko tikimės</h3><ul class="plain-list"><?php foreach ( $requirements as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul></aside><?php endif; ?>
		</div>
	</section>

	<?php if ( $offer ) : ?>
		<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="offer-title">
			<div class="g5-container section-head"><div class="g5-eyebrow">Suteikiame</div><div class="section-head__copy"><h2 class="g5-display-md" id="offer-title">Darbo priemonės, mokymai ir komandos palaikymas.</h2></div></div>
			<ul class="g5-container check-list"><?php foreach ( $offer as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
		</section>
	<?php endif; ?>

	<section class="g5-section cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="cta-title">
		<div class="g5-container cta-grid"><div><div class="g5-eyebrow">Kandidatuokite</div><h2 class="g5-display-lg" id="cta-title">Pateikite kandidatūrą.</h2></div><a class="g5-button g5-button--primary" href="<?php echo esc_url( $apply_url ); ?>">Pildyti formą <span class="g5-button__icon" aria-hidden="true">→</span></a></div>
	</section>
	<?php
	return (string) ob_get_clean();
}

function g5tech_redirect_inactive_job() {
	if ( is_singular( 'g5_job' ) && ! g5tech_job_is_active( get_queried_object_id() ) ) {
		wp_safe_redirect( home_url( '/karjera/#positions' ), 302 );
		exit;
	}
}
add_action( 'template_redirect', 'g5tech_redirect_inactive_job' );
