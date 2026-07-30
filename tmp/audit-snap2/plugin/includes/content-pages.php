<?php
/**
 * Paprasti informaciniai puslapiai, naudojant patvirtintus komponentus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_content_page_blocks() {
	$blocks = array(
		'academy-page'          => array( '5GTECH Academy', 'g5tech_render_academy_page' ),
		'training-page'         => array( '5G TECH mokymai', 'g5tech_render_training_page' ),
		'candidate-faq-page'    => array( '5G TECH DUK kandidatams', 'g5tech_render_candidate_faq_page' ),
		'leaders-page'          => array( '5G TECH vadovams', 'g5tech_render_leaders_page' ),
		'project-managers-page' => array( '5G TECH projektų vadovams', 'g5tech_render_project_managers_page' ),
	);

	foreach ( $blocks as $name => $configuration ) {
		register_block_type(
			'g5tech/' . $name,
			array(
				'api_version'     => 3,
				'title'           => $configuration[0],
				'category'        => 'theme',
				'render_callback' => $configuration[1],
				'supports'        => array(
					'autoRegister' => true,
					'html'         => false,
					'inserter'     => false,
				),
			)
		);
	}
}
add_action( 'init', 'g5tech_register_content_page_blocks' );

function g5tech_content_hero( $eyebrow, $title, $lead = '', $button = array(), $compact = false ) {
	?>
	<section class="inner-hero <?php echo $compact ? 'inner-hero--compact' : ''; ?> g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid"><div class="inner-hero__copy">
			<div class="g5-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<h1 class="g5-display-xl" id="page-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $lead ) : ?><p class="g5-body-lg"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
			<?php if ( $button ) : ?><div class="inner-hero__actions"><a class="g5-button g5-button--primary" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['label'] ); ?> <span class="g5-button__icon" aria-hidden="true">→</span></a></div><?php endif; ?>
		</div></div>
	</section>
	<?php
}

function g5tech_content_section_head( $eyebrow, $title, $id, $dark = false, $lead = '' ) {
	?>
	<div class="g5-container section-head <?php echo $dark ? 'section-head--dark' : ''; ?>">
		<div class="g5-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
		<div class="section-head__copy"><h2 class="g5-display-md" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></h2><?php if ( $lead ) : ?><p class="g5-body"><?php echo esc_html( $lead ); ?></p><?php endif; ?></div>
	</div>
	<?php
}

function g5tech_content_cta( $eyebrow, $title, $body, $label, $url ) {
	?>
	<section class="g5-section cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="cta-title" data-g5-page-anchor="cta">
		<div class="g5-container cta-grid">
			<div><div class="g5-eyebrow"><?php echo esc_html( $eyebrow ); ?></div><h2 class="g5-display-lg" id="cta-title"><?php echo esc_html( $title ); ?></h2><?php if ( $body ) : ?><p class="g5-body"><?php echo esc_html( $body ); ?></p><?php endif; ?></div>
			<a class="g5-button g5-button--primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?> <span class="g5-button__icon" aria-hidden="true">→</span></a>
		</div>
	</section>
	<?php
}

function g5tech_render_academy_page_legacy() {
	$content  = g5tech_structured_section( 'academy' );
	$steps    = $content['program'];
	$included = $content['included'];

	ob_start();
	g5tech_content_hero(
		'5GTECH Academy',
		'Praktinis pasirengimas darbui telekomunikacijų projektuose.',
		'Programa norintiems įgyti techninių pagrindų ir išbandyti darbą mobiliojo ryšio infrastruktūros projekte.',
		array( 'label' => 'Kandidatuoti', 'url' => home_url( '/kandidatuoti/' ) )
	);
	?>
	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="program-title" data-g5-core-module="academy_program">
		<?php g5tech_content_section_head( $content['program_eyebrow'], $content['program_title'], 'program-title', true ); ?>
		<ol class="g5-container steps"><?php foreach ( $steps as $index => $step ) : ?><li><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( $step['title'] ); ?></strong><p><?php echo esc_html( $step['text'] ); ?></p></li><?php endforeach; ?></ol>
	</section>
	<section class="g5-section g5-grid-lines" aria-labelledby="included-title" data-g5-core-module="academy_included">
		<?php g5tech_content_section_head( $content['included_eyebrow'], $content['included_title'], 'included-title' ); ?>
		<ul class="g5-container check-list"><?php foreach ( $included as $item ) : ?><li><?php echo esc_html( $item['text'] ); ?></li><?php endforeach; ?></ul>
	</section>
	<?php
	echo g5tech_render_page_modules( 'academy' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	g5tech_content_cta( 'Pradėkite', 'Norite pradėti karjerą telekomunikacijose?', 'Techninė patirtis – privalumas. Svarbiausia atsakingas požiūris ir noras mokytis.', 'Pildyti formą', home_url( '/kandidatuoti/' ) );

	return (string) ob_get_clean();
}

function g5tech_render_academy_page() {
	return g5tech_compose_modular_page( 'academy', g5tech_get_legacy_page_html( 'academy' ) );
}

function g5tech_render_training_page_legacy() {
	$content   = g5tech_training_page_content();
	$topics    = g5tech_training_page_lines( 'topics' );
	$order     = g5tech_sanitize_training_section_order( $content['section_order'] ?? array() );
	$page_modules = g5tech_get_page_modules( 'training' );
	$topics_module = g5tech_get_source_module( 'training_topics' );
	$extra_modules = array();

	foreach ( $page_modules as $page_module ) {
		if ( $topics_module && (int) $page_module->ID === (int) $topics_module->ID ) {
			continue;
		}

		if ( 'dynamic' === get_post_meta( $page_module->ID, 'g5_module_type', true ) ) {
			continue;
		}

		$extra_modules[] = $page_module;
	}

	$equipment = g5tech_get_partners(
		'',
		g5tech_sanitize_partner_ids( $content['equipment_ids'] ?? array() )
	);
	$image_url = ! empty( $content['image_id'] )
		? wp_get_attachment_image_url( (int) $content['image_id'], 'full' )
		: '';

	if ( ! $image_url ) {
		$image_url = get_theme_file_uri( 'assets/images/generated/training-technical-lab-v1.jpg' );
	}

	$sections = array();

	ob_start();
	g5tech_content_hero( $content['hero_eyebrow'], $content['hero_title'], $content['hero_lead'] );

	if ( $topics_module ) {
		$sections['topics'] = g5tech_module_is_on_page( $topics_module->ID, 'training' )
			? g5tech_render_content_module( $topics_module )
			: '';
	} elseif ( ! get_option( 'g5tech_content_modules_migrated_1' ) ) {
		ob_start();
		?>
		<section class="g5-section g5-grid-lines" aria-labelledby="topics-title">
			<?php g5tech_content_section_head( $content['topics_eyebrow'], $content['topics_title'], 'topics-title' ); ?>
			<ul class="g5-container check-list"><?php foreach ( $topics as $topic ) : ?><li><?php echo esc_html( $topic ); ?></li><?php endforeach; ?></ul>
		</section>
		<?php
		$sections['topics'] = (string) ob_get_clean();
	} else {
		$sections['topics'] = '';
	}

	ob_start();
	?>
	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="equipment-title" data-g5-core-module="training_equipment">
		<?php g5tech_content_section_head( $content['equipment_eyebrow'], $content['equipment_title'], 'equipment-title' ); ?>
		<ul class="g5-container equipment-list">
			<?php foreach ( $equipment as $item ) : ?>
				<li>
					<?php if ( has_post_thumbnail( $item ) ) : ?>
						<?php
						echo get_the_post_thumbnail(
							$item,
							'medium',
							array(
								'alt'     => get_the_title( $item ),
								'loading' => 'lazy',
								'class'   => 'equipment-list__logo',
							)
						);
						?>
					<?php else : ?>
						<?php echo esc_html( get_the_title( $item ) ); ?>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
	$sections['equipment'] = (string) ob_get_clean();

	ob_start();
	?>
	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="training-room-title" data-g5-core-module="training_room">
		<?php g5tech_content_section_head( $content['image_eyebrow'], $content['image_title'], 'training-room-title', true ); ?>
		<figure class="g5-container media-frame" style="aspect-ratio: 16 / 8"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $content['image_alt'] ); ?>"></figure>
	</section>
	<?php
	$sections['image'] = (string) ob_get_clean();

	foreach ( $order as $section_key ) {
		echo $sections[ $section_key ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	foreach ( $extra_modules as $extra_module ) {
		echo g5tech_render_content_module( $extra_module ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	g5tech_content_cta(
		$content['cta_eyebrow'],
		$content['cta_title'],
		'',
		$content['cta_button_label'],
		$content['cta_button_url']
	);

	return (string) ob_get_clean();
}

function g5tech_render_training_page() {
	return g5tech_compose_modular_page( 'training', g5tech_get_legacy_page_html( 'training' ) );
}

function g5tech_training_page_defaults() {
	return array(
		'hero_eyebrow'        => 'Mokymai',
		'hero_title'          => 'Praktiniai techniniai mokymai.',
		'hero_lead'           => 'Mokymų salė skirta naujų darbuotojų parengimui ir patyrusių specialistų kompetencijų tobulinimui.',
		'topics_eyebrow'      => 'Programa',
		'topics_title'        => 'Mokymų temos.',
		'topics'              => "Asmeninės apsaugos priemonės ir jų naudojimas\nDarbų aukštyje specifika ir saugos taisyklės\n2G, 3G, 4G-LTE ir 5G technologijų pagrindai\nAntenų ir radijo modulių montavimas\nKabelių instaliacija ir prijungimas\nElektros instaliacijos principai\nTechninės dokumentacijos rengimas\nBazinės stoties konfigūravimo pagrindai",
		'equipment_eyebrow'   => 'Įranga',
		'equipment_title'     => 'Mokomės su realiuose projektuose naudojama įranga.',
		'equipment_ids'       => array(),
		'image_eyebrow'       => 'Aplinka',
		'image_title'         => 'Mokymų salė.',
		'image_id'            => 0,
		'image_alt'           => '5G TECH praktinių mokymų aplinka',
		'cta_eyebrow'         => 'Karjera',
		'cta_title'           => 'Norite prisijungti prie komandos?',
		'cta_button_label'    => 'Peržiūrėti pozicijas',
		'cta_button_url'      => '/karjera/',
		'section_order'       => array( 'topics', 'equipment', 'image' ),
	);
}

function g5tech_training_page_content() {
	$saved   = get_option( 'g5tech_training_page_content', array() );
	$content = wp_parse_args( $saved, g5tech_training_page_defaults() );

	if ( ! array_key_exists( 'equipment_ids', $saved ) ) {
		$content['equipment_ids'] = g5tech_training_default_equipment_ids();
	}

	$content['section_order'] = g5tech_sanitize_training_section_order( $content['section_order'] ?? array() );

	return $content;
}

function g5tech_training_section_keys() {
	return array( 'topics', 'equipment', 'image' );
}

function g5tech_sanitize_training_section_order( $order ) {
	$allowed = g5tech_training_section_keys();
	$order   = is_string( $order ) ? explode( ',', $order ) : (array) $order;
	$clean   = array();

	foreach ( $order as $section_key ) {
		$section_key = sanitize_key( $section_key );

		if ( in_array( $section_key, $allowed, true ) && ! in_array( $section_key, $clean, true ) ) {
			$clean[] = $section_key;
		}
	}

	return array_merge( $clean, array_values( array_diff( $allowed, $clean ) ) );
}

function g5tech_training_default_equipment_ids() {
	$wanted = array( 'Nokia', 'Ericsson', 'Huawei', '3Z-RFVision', 'SiteMaster', 'Sonel' );
	$found  = array();

	foreach (
		array_merge(
			g5tech_get_partners( 'manufacturer' ),
			g5tech_get_partners( 'equipment' )
		) as $item
	) {
		$found[ get_the_title( $item ) ] = (int) $item->ID;
	}

	return array_values(
		array_filter(
			array_map(
				static function( $title ) use ( $found ) {
					return $found[ $title ] ?? 0;
				},
				$wanted
			)
		)
	);
}

function g5tech_training_page_lines( $key ) {
	$content = g5tech_training_page_content();

	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', (string) ( $content[ $key ] ?? '' ) )
			)
		)
	);
}

function g5tech_render_candidate_faq_group( $group ) {
	$faqs = g5tech_get_faqs_by_topic( 'candidate', $group );

	if ( ! $faqs ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container faq-list">
		<?php foreach ( $faqs as $faq ) : ?><details><summary><?php echo esc_html( get_the_title( $faq ) ); ?></summary><p><?php echo esc_html( get_post_meta( $faq->ID, 'g5_faq_answer', true ) ); ?></p></details><?php endforeach; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_candidate_faq_page_legacy() {
	$groups = array(
		'start'  => array( 'Darbo pradžia', 'Kandidatavimas ir pasirengimas.' ),
		'travel' => array( 'Komandiruotės', 'Darbas Europos projektuose.' ),
		'safety' => array( 'Sauga ir priemonės', 'Darbas aukštyje ir sauga.' ),
		'daily'  => array( 'Kasdienis darbas', 'Kasdienė darbo eiga.' ),
	);

	ob_start();
	g5tech_content_hero( 'DUK kandidatams', 'Dažniausi klausimai apie darbą 5G TECH.', '', array(), true );
	$index = 0;
	foreach ( $groups as $group => $labels ) :
		$content = g5tech_render_candidate_faq_group( $group );
		if ( ! $content ) {
			continue;
		}
		?>
		<section class="g5-section <?php echo 1 === $index % 2 ? 'g5-section--paper' : ''; ?> g5-grid-lines" aria-labelledby="<?php echo esc_attr( $group ); ?>-title" data-g5-core-module="candidate_faq_groups">
			<?php g5tech_content_section_head( $labels[0], $labels[1], $group . '-title' ); ?>
			<?php echo $content; ?>
		</section>
		<?php
		$index++;
	endforeach;
	echo g5tech_render_page_modules( 'candidate_faq' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	g5tech_content_cta( 'Neradote atsakymo?', 'Susisiekite su personalo komanda.', g5tech_setting( 'career_email' ), 'Kandidatuoti', home_url( '/kandidatuoti/' ) );

	return (string) ob_get_clean();
}

function g5tech_render_candidate_faq_page() {
	return g5tech_compose_modular_page( 'candidate_faq', g5tech_get_legacy_page_html( 'candidate_faq' ) );
}

function g5tech_render_leaders_page_legacy() {
	$content = g5tech_structured_section( 'leaders' );
	$cards   = $content['reasons'];

	ob_start();
	g5tech_content_hero(
		'Informacija įmonių vadovams',
		'Projektų vykdymas su aiškiomis atsakomybėmis.',
		'Telekomunikacijų, energetikos ir inžinerinių sistemų projektai Lietuvoje bei kitose Europos šalyse.',
		array( 'label' => 'Susisiekti su vadovu', 'url' => home_url( '/kontaktai/' ) )
	);
	?>
	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="reasons-title" data-g5-core-module="leaders_reasons">
		<?php g5tech_content_section_head( $content['reasons_eyebrow'], $content['reasons_title'], 'reasons-title' ); ?>
		<div class="g5-container card-grid"><?php foreach ( $cards as $index => $card ) : ?><div class="info-card"><span class="info-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( $card['title'] ); ?></h3><p><?php echo esc_html( $card['text'] ); ?></p></div><?php endforeach; ?></div>
	</section>
	<?php
	echo g5tech_render_page_modules( 'leaders' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	g5tech_content_cta( 'Tiesioginis kontaktas', 'Aptarkime projekto apimtį, rizikas ir atsakomybes.', 'Aleksandras Iljinas · generalinis direktorius', 'Susisiekti', home_url( '/kontaktai/' ) );

	return (string) ob_get_clean();
}

function g5tech_render_leaders_page() {
	return g5tech_compose_modular_page( 'leaders', g5tech_get_legacy_page_html( 'leaders' ) );
}

function g5tech_render_project_managers_page_legacy() {
	$content   = g5tech_structured_section( 'project_managers' );
	$tasks     = $content['tasks'];
	$equipment = g5tech_get_partners( '', $content['equipment_ids'] );

	ob_start();
	g5tech_content_hero(
		'Projektų vadovams ir techniniam personalui',
		'Techninė projekto eiga ir dokumentacija.',
		'Dirbame pagal užsakovo techninius standartus ir tiesiogiai informuojame apie darbų eigą, neatitikimus bei dokumentaciją.',
		array( 'label' => 'Atsiųsti techninę užduotį', 'url' => home_url( '/kontaktai/' ) )
	);
	?>
	<section class="g5-section g5-grid-lines" aria-labelledby="technical-title" data-g5-core-module="project_managers_scope">
		<?php g5tech_content_section_head( $content['scope_eyebrow'], $content['scope_title'], 'technical-title' ); ?>
		<ul class="g5-container check-list"><?php foreach ( $tasks as $task ) : ?><li><?php echo esc_html( $task['text'] ); ?></li><?php endforeach; ?></ul>
	</section>
	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="equipment-title" data-g5-core-module="project_managers_equipment">
		<?php g5tech_content_section_head( $content['equipment_eyebrow'], $content['equipment_title'], 'equipment-title', true ); ?>
		<div class="g5-container stats-grid"><?php foreach ( $equipment as $item ) : ?><div class="stat"><strong><?php echo esc_html( get_the_title( $item ) ); ?></strong><span><?php echo esc_html( g5tech_partner_type_label( get_post_meta( $item->ID, 'g5_partner_type', true ) ) ); ?></span></div><?php endforeach; ?></div>
	</section>
	<?php
	echo g5tech_render_page_modules( 'project_managers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	g5tech_content_cta( 'Techninė užduotis', 'Atsiųskite turimą dokumentaciją arba darbų apimtį.', '', 'Susisiekti', home_url( '/kontaktai/' ) );

	return (string) ob_get_clean();
}

function g5tech_render_project_managers_page() {
	return g5tech_compose_modular_page( 'project_managers', g5tech_get_legacy_page_html( 'project_managers' ) );
}
