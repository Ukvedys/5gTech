<?php
/**
 * Globalūs 5G TECH svetainės duomenys.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_default_settings() {
	return array(
		'stat_1_value'       => '6000+',
		'stat_1_label'       => 'įgyvendintų bazinių stočių',
		'stat_2_value'       => '2G–5G',
		'stat_2_label'       => 'technologijų patirtis',
		'stat_3_value'       => '6',
		'stat_3_label'       => 'Europos šalys',
		'stat_4_value'       => '2020',
		'stat_4_label'       => 'veikiame nuo',
		'process_1_title'    => 'Įsigiliname',
		'process_1_text'     => 'Suprantame techninę užduotį, objekto sąlygas ir atsakomybes.',
		'process_2_title'    => 'Suplanuojame',
		'process_2_text'     => 'Suderiname sprendimus, terminus, komandą ir darbų saugą.',
		'process_3_title'    => 'Įgyvendiname',
		'process_3_text'     => 'Atliekame numatytus montavimo ir integravimo darbus.',
		'process_4_title'    => 'Patikriname',
		'process_4_text'     => 'Testuojame įrangą ir ištaisome nustatytus neatitikimus.',
		'process_5_title'    => 'Perduodame',
		'process_5_text'     => 'Pateikiame dokumentaciją ir perduodame užbaigtą sprendimą.',
		'cta_title'          => 'Aptarkime jūsų techninę užduotį.',
		'cta_text'           => 'Atsiųskite turimą informaciją – įvertinsime darbų apimtį ir pasiūlysime tolesnius veiksmus.',
		'cta_button_label'   => 'Susisiekti',
		'hero_button_label'  => 'Aptarkime projektą',
		'home_hero_eyebrow'  => 'Telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektai',
		'home_hero_title'     => 'Kai svarbus ne tik atliktas darbas, bet ir ramybė dėl rezultato',
		'home_hero_lead'      => 'Techninis partneris nuo pasirengimo ir montavimo iki testavimo, dokumentacijos bei priežiūros.',
		'home_show_intro'     => '1',
		'home_order_intro'    => '1',
		'home_show_services'  => '1',
		'home_order_services' => '2',
		'home_show_standards' => '1',
		'home_order_standards' => '3',
		'home_show_process'   => '1',
		'home_order_process'  => '4',
		'home_show_experience' => '1',
		'home_order_experience' => '5',
		'home_show_equipment' => '1',
		'home_order_equipment' => '6',
		'home_show_team'      => '1',
		'home_order_team'     => '7',
		'home_show_audiences' => '1',
		'home_order_audiences' => '8',
		'home_show_news'      => '1',
		'home_order_news'     => '9',
		'contact_page_url'   => '/kontaktai/',
		'phone'              => '',
		'email'              => '',
		'career_email'       => '',
		'address'            => '',
		'company_code'       => '',
		'vat_code'           => '',
		'countries'          => "Lietuva\nVokietija\nŠvedija\nNorvegija\nDanija\nSuomija",
		'certifications'     => "ISO 9001 | Kokybės vadyba\nISO 14001 | Aplinkos apsaugos vadyba\nISO 45001 | Darbuotojų sveikata ir sauga\nSSVA | Ypatingojo statinio rangovo kvalifikacija",
	);
}

function g5tech_settings() {
	$saved    = get_option( 'g5tech_settings', array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$settings = wp_parse_args( $saved, g5tech_default_settings() );

	if ( ! array_key_exists( 'stats', $saved ) ) {
		$settings['stats'] = g5tech_legacy_repeater_items(
			$settings,
			'stat_',
			4,
			array( 'value' => 'value', 'label' => 'label' )
		);
	}

	if ( ! array_key_exists( 'process_steps', $saved ) ) {
		$headings = array(
			'Įvertiname užduotį ir projekto sąlygas.',
			'Parengiame darbų planą ir techninį sprendimą.',
			'Atliekame montavimo ir integravimo darbus.',
			'Testuojame sistemą ir fiksuojame rezultatus.',
			'Perduodame dokumentaciją ir užbaigtą sprendimą.',
		);
		$settings['process_steps'] = array();

		for ( $index = 1; $index <= 5; $index++ ) {
			$settings['process_steps'][] = array(
				'_id'     => wp_generate_uuid4(),
				'title'   => $settings[ "process_{$index}_title" ],
				'heading' => $headings[ $index - 1 ],
				'text'    => $settings[ "process_{$index}_text" ],
			);
		}
	}

	return $settings;
}

function g5tech_setting( $key, $fallback = '' ) {
	$settings = g5tech_settings();

	return array_key_exists( $key, $settings ) ? $settings[ $key ] : $fallback;
}

function g5tech_setting_lines( $key ) {
	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', (string) g5tech_setting( $key ) )
			)
		)
	);
}

function g5tech_stats() {
	$settings = g5tech_settings();

	return isset( $settings['stats'] ) && is_array( $settings['stats'] ) ? $settings['stats'] : array();
}

function g5tech_stat( $index, $fallback_value = '', $fallback_label = '' ) {
	$stats = g5tech_stats();
	$index = max( 1, absint( $index ) ) - 1;

	return isset( $stats[ $index ] )
		? $stats[ $index ]
		: array( 'value' => $fallback_value, 'label' => $fallback_label );
}

function g5tech_process_steps() {
	$settings = g5tech_settings();

	return isset( $settings['process_steps'] ) && is_array( $settings['process_steps'] )
		? $settings['process_steps']
		: array();
}

function g5tech_settings_repeater_configs() {
	return array(
		'stats' => array(
			'schema' => array(
				'value' => array( 'label' => 'Rodiklis', 'type' => 'text' ),
				'label' => array( 'label' => 'Paaiškinimas', 'type' => 'text' ),
			),
		),
		'process_steps' => array(
			'schema' => array(
				'title'   => array( 'label' => 'Trumpas etapo pavadinimas', 'type' => 'text' ),
				'heading' => array( 'label' => 'Pagrindinė antraštė', 'type' => 'text' ),
				'text'    => array( 'label' => 'Paaiškinimas', 'type' => 'textarea' ),
			),
		),
	);
}

function g5tech_sanitize_settings( $input ) {
	$defaults  = g5tech_default_settings();
	$sanitized = array();

	g5tech_save_admin_content_translations( 'settings' );

	foreach ( $defaults as $key => $default ) {
		$value = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';

		if ( in_array( $key, array( 'contact_page_url' ), true ) ) {
			$sanitized[ $key ] = esc_url_raw( $value );
		} elseif ( in_array( $key, array( 'email', 'career_email' ), true ) ) {
			$sanitized[ $key ] = sanitize_email( $value );
		} elseif (
			str_contains( $key, '_text' )
			|| in_array( $key, array( 'address', 'countries', 'certifications' ), true )
		) {
			$sanitized[ $key ] = sanitize_textarea_field( $value );
		} else {
			$sanitized[ $key ] = sanitize_text_field( $value );
		}
	}

	$repeater_configs = g5tech_settings_repeater_configs();
	$sanitized['stats'] = g5tech_sanitize_repeater_items(
		$input['stats'] ?? array(),
		$repeater_configs['stats']['schema']
	);
	$sanitized['process_steps'] = g5tech_sanitize_repeater_items(
		$input['process_steps'] ?? array(),
		$repeater_configs['process_steps']['schema']
	);

	return $sanitized;
}

function g5tech_register_settings() {
	register_setting(
		'g5tech_settings_group',
		'g5tech_settings',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'g5tech_sanitize_settings',
			'default'           => g5tech_default_settings(),
		)
	);
}
add_action( 'admin_init', 'g5tech_register_settings' );

function g5tech_add_settings_page() {
	add_menu_page(
		'5G TECH nustatymai',
		'5G TECH nustatymai',
		'manage_g5tech_settings',
		'g5tech-settings',
		'g5tech_render_settings_page',
		'dashicons-admin-generic',
		22
	);
}
add_action( 'admin_menu', 'g5tech_add_settings_page' );

function g5tech_settings_text_field( $settings, $key, $label, $description = '', $type = 'text' ) {
	?>
	<tr>
		<th scope="row"><label for="g5tech-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input
				class="regular-text"
				type="<?php echo esc_attr( $type ); ?>"
				id="g5tech-<?php echo esc_attr( $key ); ?>"
				name="g5tech_settings[<?php echo esc_attr( $key ); ?>]"
				value="<?php echo esc_attr( $settings[ $key ] ); ?>"
			>
			<?php if ( $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function g5tech_settings_textarea( $settings, $key, $label, $description = '', $rows = 4 ) {
	?>
	<tr>
		<th scope="row"><label for="g5tech-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<textarea
				class="large-text"
				rows="<?php echo (int) $rows; ?>"
				id="g5tech-<?php echo esc_attr( $key ); ?>"
				name="g5tech_settings[<?php echo esc_attr( $key ); ?>]"
			><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
			<?php if ( $description ) : ?>
				<p class="description"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

function g5tech_settings_home_section_row( $settings, $key, $label ) {
	$show_key  = 'home_show_' . $key;
	$order_key = 'home_order_' . $key;
	?>
	<tr>
		<th scope="row"><?php echo esc_html( $label ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="g5tech_settings[<?php echo esc_attr( $show_key ); ?>]" value="1" <?php checked( $settings[ $show_key ], '1' ); ?>>
				Rodyti tituliniame
			</label>
			<label style="margin-left:1.5rem">
				Eilės numeris
				<input type="number" min="1" max="9" name="g5tech_settings[<?php echo esc_attr( $order_key ); ?>]" value="<?php echo esc_attr( $settings[ $order_key ] ); ?>" style="width:4.5rem">
			</label>
		</td>
	</tr>
	<?php
}

function g5tech_render_settings_page() {
	if ( ! current_user_can( 'manage_g5tech_settings' ) ) {
		return;
	}

	$settings = g5tech_settings();
	$repeaters = g5tech_settings_repeater_configs();
	?>
	<div class="wrap">
		<h1>5G TECH nustatymai</h1>
		<p>Čia keičiama informacija, kuri naudojama keliuose svetainės puslapiuose.</p>
		<form action="options.php" method="post">
			<?php settings_fields( 'g5tech_settings_group' ); ?>
			<?php g5tech_render_admin_content_translation_context( 'settings', $settings ); ?>

			<h2>Bendri rodikliai</h2>
			<p>Rodikliai naudojami tituliniame ir kituose svetainės puslapiuose. Jų eilės tvarka keičiasi tempiant.</p>
			<?php
			g5tech_render_repeater(
				array(
					'name'        => 'g5tech_settings[stats]',
					'items'       => $settings['stats'],
					'schema'      => $repeaters['stats']['schema'],
					'add_label'   => 'Pridėti rodiklį',
					'empty_label' => 'Rodiklių dar nėra.',
					'title_field' => 'value',
				)
			);
			?>

			<h2>Projekto etapai</h2>
			<?php
			g5tech_render_repeater(
				array(
					'name'        => 'g5tech_settings[process_steps]',
					'items'       => $settings['process_steps'],
					'schema'      => $repeaters['process_steps']['schema'],
					'add_label'   => 'Pridėti projekto etapą',
					'empty_label' => 'Projekto etapų dar nėra.',
				)
			);
			?>

			<h2>Numatytasis kvietimas susisiekti</h2>
			<table class="form-table" role="presentation">
				<?php
				g5tech_settings_text_field( $settings, 'cta_title', 'Antraštė' );
				g5tech_settings_textarea( $settings, 'cta_text', 'Tekstas', '', 3 );
				g5tech_settings_text_field( $settings, 'cta_button_label', 'CTA mygtuko tekstas' );
				g5tech_settings_text_field( $settings, 'hero_button_label', 'Hero mygtuko tekstas' );
				g5tech_settings_text_field( $settings, 'contact_page_url', 'Kontaktų puslapio nuoroda', 'Pvz. /kontaktai/' );
				?>
			</table>

			<h2>Titulinis puslapis</h2>
			<p>Hero nuotrauka keičiama redaguojant puslapį „Pagrindinis“ ir pasirenkant pagrindinį paveikslėlį.</p>
			<table class="form-table" role="presentation">
				<?php
				g5tech_settings_text_field( $settings, 'home_hero_eyebrow', 'Hero krypties tekstas' );
				g5tech_settings_textarea( $settings, 'home_hero_title', 'Hero antraštė', '', 2 );
				g5tech_settings_textarea( $settings, 'home_hero_lead', 'Hero paaiškinimas', '', 2 );
				g5tech_settings_home_section_row( $settings, 'intro', 'Įžanga' );
				g5tech_settings_home_section_row( $settings, 'services', 'Paslaugos' );
				g5tech_settings_home_section_row( $settings, 'standards', 'ISO ir SSVA' );
				g5tech_settings_home_section_row( $settings, 'process', 'Kaip dirbame' );
				g5tech_settings_home_section_row( $settings, 'experience', 'Patirties geografija' );
				g5tech_settings_home_section_row( $settings, 'equipment', 'Gamintojai' );
				g5tech_settings_home_section_row( $settings, 'team', 'Komanda' );
				g5tech_settings_home_section_row( $settings, 'audiences', 'Auditorijos' );
				g5tech_settings_home_section_row( $settings, 'news', 'Naujienos' );
				?>
			</table>

			<h2>Kontaktai ir rekvizitai</h2>
			<table class="form-table" role="presentation">
				<?php
				g5tech_settings_text_field( $settings, 'phone', 'Telefonas' );
				g5tech_settings_text_field( $settings, 'email', 'El. paštas', '', 'email' );
				g5tech_settings_text_field( $settings, 'career_email', 'Kandidatūrų gavėjo el. paštas', 'Šiuo adresu siunčiami kandidatų duomenys ir CV.', 'email' );
				g5tech_settings_textarea( $settings, 'address', 'Adresas', '', 2 );
				g5tech_settings_text_field( $settings, 'company_code', 'Įmonės kodas' );
				g5tech_settings_text_field( $settings, 'vat_code', 'PVM kodas' );
				?>
			</table>

			<h2>Patirtis ir standartai</h2>
			<table class="form-table" role="presentation">
				<?php
				g5tech_settings_textarea( $settings, 'countries', 'Šalys', 'Viena šalis vienoje eilutėje.', 7 );
				g5tech_settings_textarea( $settings, 'certifications', 'Sertifikatai', 'Formatas: pavadinimas | paaiškinimas. Vienas sertifikatas vienoje eilutėje.', 7 );
				?>
			</table>

			<?php submit_button( 'Išsaugoti bendrus nustatymus' ); ?>
		</form>
	</div>
	<?php
}
