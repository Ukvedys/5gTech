<?php
/**
 * Dinaminiai paslaugos puslapio blokai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_service_blocks() {
	$blocks = array(
		'service-hero'      => 'g5tech_render_service_hero',
		'service-scope'     => 'g5tech_render_service_scope',
		'service-process'   => 'g5tech_render_service_process',
		'service-equipment' => 'g5tech_render_service_equipment',
		'service-faq'       => 'g5tech_render_service_faq',
		'service-cta'       => 'g5tech_render_service_cta',
		'services-grid'     => 'g5tech_render_services_grid',
	);

	foreach ( $blocks as $name => $callback ) {
		register_block_type(
			"g5tech/$name",
			array(
				'api_version'     => 3,
				'title'           => ucwords( str_replace( '-', ' ', $name ) ),
				'category'        => 'theme',
				'render_callback' => $callback,
				'uses_context'    => array( 'postId', 'postType' ),
				'supports'        => array(
					'autoRegister' => true,
					'html'         => false,
					'inserter'     => false,
				),
			)
		);
	}
}
add_action( 'init', 'g5tech_register_service_blocks' );

function g5tech_service_post_id( $block = array() ) {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}

	return get_the_ID();
}

function g5tech_render_service_hero( $attributes, $content, $block ) {
	$post_id  = g5tech_service_post_id( $block );
	$title    = get_the_title( $post_id );
	$category = get_post_meta( $post_id, 'g5_service_category', true ) ?: 'Paslauga';
	$summary  = get_post_meta( $post_id, 'g5_service_summary', true );
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	$generated    = g5tech_service_generated_visual( $post_id );
	$image_alt    = $thumbnail_id
		? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) )
		: '';

	if ( $generated && g5tech_is_demo_attachment( $thumbnail_id ) ) {
		$image_alt = $generated['alt'];
	}

	if ( '' === $image_alt ) {
		$image_alt = sprintf(
			/* translators: %s: service title. */
			__( 'Techninė infrastruktūra – %s', '5gtech-core' ),
			$title
		);
	}

	if ( $generated && g5tech_is_demo_attachment( $thumbnail_id ) ) {
		$image = sprintf(
			'<img src="%1$s" alt="%2$s" loading="eager" fetchpriority="high">',
			esc_url( $generated['url'] ),
			esc_attr( $image_alt )
		);
	} else {
		$image = get_the_post_thumbnail(
			$post_id,
			'full',
			array(
				'alt'     => $image_alt,
				'loading' => 'eager',
			)
		);
	}
	$contact_url = g5tech_setting( 'contact_page_url', '/kontaktai/' );
	$hero_label  = g5tech_setting( 'hero_button_label', 'Aptarkime projektą' );

	ob_start();
	?>
	<section class="g5-service-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-service-title">
		<div class="g5-container g5-grid g5-service-hero__grid">
			<div class="g5-service-hero__copy">
				<nav class="g5-breadcrumbs" aria-label="Puslapio kelias">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a>
					<span>/</span>
					<span>Paslaugos</span>
					<span>/</span>
					<span><?php echo esc_html( $title ); ?></span>
				</nav>
				<div class="g5-eyebrow"><?php echo esc_html( $category ); ?></div>
				<h1 class="g5-display-xl" id="g5-service-title"><?php echo esc_html( $title ); ?></h1>
				<?php if ( $summary ) : ?>
					<p class="g5-body-lg"><?php echo esc_html( $summary ); ?></p>
				<?php endif; ?>
				<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( $contact_url ) ); ?>">
					<?php echo esc_html( $hero_label ); ?>
					<span class="g5-button__icon" aria-hidden="true">→</span>
				</a>
			</div>
			<figure class="g5-service-hero__media">
				<?php echo $image ? wp_kses_post( $image ) : ''; ?>
			</figure>
			<div class="g5-service-proof" aria-label="5G TECH patirties rodikliai">
				<?php foreach ( array_slice( g5tech_stats(), 0, 3 ) as $stat ) : ?>
					<div class="g5-service-proof__item">
						<strong><?php echo esc_html( $stat['value'] ); ?></strong>
						<span><?php echo esc_html( $stat['label'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_service_scope( $attributes, $content, $block ) {
	$post_id = g5tech_service_post_id( $block );
	$items   = g5tech_service_lines( 'g5_service_work', $post_id );

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<section class="g5-section g5-grid-lines" aria-labelledby="g5-scope-title">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Ką atliekame</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-scope-title">Atliekami darbai.</h2>
				<p class="g5-body">Darbų apimtį pritaikome pagal objekto būklę, techninę užduotį ir užsakovo reikalavimus.</p>
			</div>
		</div>
		<div class="g5-container g5-service-list">
			<?php foreach ( $items as $index => $item ) : ?>
				<div class="g5-service-list__item">
					<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<p><?php echo esc_html( $item ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_service_process() {
	$steps = g5tech_process_steps();

	ob_start();
	?>
	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-process-title">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Kaip dirbame</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-process-title">Projekto etapai.</h2>
				<p class="g5-body" style="color:rgba(255,255,255,.66)">Tas pats darbo standartas taikomas nepriklausomai nuo projekto dydžio ar šalies.</p>
			</div>
		</div>
		<ol class="g5-container g5-process-list">
			<?php foreach ( $steps as $index => $step ) : ?>
				<li>
					<span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<strong><?php echo esc_html( $step['title'] ); ?></strong>
					<p><?php echo esc_html( $step['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_service_equipment( $attributes, $content, $block ) {
	$post_id     = g5tech_service_post_id( $block );
	$partner_ids = g5tech_sanitize_partner_ids(
		get_post_meta( $post_id, 'g5_service_partners', true )
	);
	$items       = g5tech_get_partners( '', $partner_ids );

	if ( ! $partner_ids || ! $items ) {
		return '';
	}

	ob_start();
	?>
	<section class="g5-section g5-grid-lines" aria-labelledby="g5-equipment-title">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Techninė patirtis</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-equipment-title">Įranga, su kuria dirbame.</h2>
			</div>
		</div>
		<ul class="g5-container g5-equipment-list">
			<?php foreach ( $items as $item ) : ?>
				<li>
					<?php if ( has_post_thumbnail( $item ) ) : ?>
						<?php
						echo get_the_post_thumbnail(
							$item,
							'medium',
							array(
								'alt'     => get_the_title( $item ),
								'loading' => 'lazy',
								'class'   => 'g5-equipment-list__logo',
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
	return ob_get_clean();
}

function g5tech_render_service_faq( $attributes, $content, $block ) {
	$post_id = g5tech_service_post_id( $block );
	$items   = array();

	if ( function_exists( 'g5tech_get_faqs_for_service' ) ) {
		foreach ( g5tech_get_faqs_for_service( $post_id ) as $faq ) {
			$answer = trim( (string) get_post_meta( $faq->ID, 'g5_faq_answer', true ) );

			if ( $faq->post_title && $answer ) {
				$items[] = array( $faq->post_title, $answer );
			}
		}
	}

	// Senų paslaugų duomenys lieka atsarginiu šaltiniu iki migracijos.
	if ( ! $items ) {
		foreach ( g5tech_service_lines( 'g5_service_faq', $post_id ) as $row ) {
			$parts = array_map( 'trim', explode( '|', $row, 2 ) );

			if ( 2 === count( $parts ) && $parts[0] && $parts[1] ) {
				$items[] = $parts;
			}
		}
	}

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="g5-service-faq-title">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Dažniausi klausimai</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-service-faq-title">Projekto vykdymas.</h2>
			</div>
		</div>
		<div class="g5-container g5-faq-list">
			<?php foreach ( $items as $item ) : ?>
				<details>
					<summary><?php echo esc_html( $item[0] ); ?></summary>
					<p><?php echo esc_html( $item[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_service_cta( $attributes, $content, $block ) {
	$post_id = g5tech_service_post_id( $block );
	$title   = get_post_meta( $post_id, 'g5_service_cta_title', true ) ?: g5tech_setting( 'cta_title' );
	$text    = get_post_meta( $post_id, 'g5_service_cta_text', true ) ?: g5tech_setting( 'cta_text' );
	$label   = g5tech_setting( 'cta_button_label', 'Susisiekti' );
	$url     = g5tech_setting( 'contact_page_url', '/kontaktai/' );

	ob_start();
	?>
	<section class="g5-section g5-service-cta g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-cta-title">
		<div class="g5-container g5-service-cta__grid">
			<div class="g5-service-cta__copy">
				<div class="g5-eyebrow">Kitas žingsnis</div>
				<h2 class="g5-display-lg" id="g5-cta-title"><?php echo esc_html( $title ); ?></h2>
				<p class="g5-body"><?php echo esc_html( $text ); ?></p>
			</div>
			<div class="g5-service-cta__action">
				<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( $url ) ); ?>">
					<?php echo esc_html( $label ); ?>
					<span class="g5-button__icon" aria-hidden="true">→</span>
				</a>
			</div>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_services_grid_legacy() {
	$services = get_posts(
		array(
			'post_type'      => 'g5_service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'order'          => 'ASC',
		)
	);

	if ( ! $services ) {
		return '';
	}

	$certifications = g5tech_setting_lines( 'certifications' );
	$cta_title       = g5tech_setting( 'cta_title', 'Aptarkime jūsų techninę užduotį.' );
	$cta_text        = g5tech_setting( 'cta_text' );
	$cta_label       = g5tech_setting( 'cta_button_label', 'Susisiekti' );
	$contact_url     = g5tech_setting( 'contact_page_url', '/kontaktai/' );

	ob_start();
	?>
	<main id="content">
	<section class="g5-inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-services-page-title">
		<div class="g5-container g5-grid">
			<div class="g5-inner-hero__copy">
				<div class="g5-eyebrow">Paslaugos</div>
				<h1 class="g5-display-xl" id="g5-services-page-title">Telekomunikacijų, energetikos ir inžinerinių sistemų paslaugos.</h1>
			</div>
		</div>
	</section>

	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="g5-services-list-title" data-g5-core-module="services_list">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">6 kryptys</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-services-list-title">Paslaugų sritys.</h2>
			</div>
		</div>
		<div class="g5-container g5-services-grid">
			<?php foreach ( $services as $index => $service ) : ?>
				<?php
				$card_title   = get_post_meta( $service->ID, 'g5_service_card_title', true ) ?: get_the_title( $service );
				$card_summary = get_post_meta( $service->ID, 'g5_service_card_summary', true )
					?: get_post_meta( $service->ID, 'g5_service_summary', true );
				?>
				<a class="g5-service-card" href="<?php echo esc_url( get_permalink( $service ) ); ?>">
					<span class="g5-service-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="g5-heading-md"><?php echo esc_html( $card_title ); ?></h3>
					<?php if ( $card_summary ) : ?>
						<p><?php echo esc_html( $card_summary ); ?></p>
					<?php endif; ?>
					<span class="g5-service-card__link">Peržiūrėti →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>

	<?php if ( $certifications ) : ?>
		<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-services-standard-title" data-g5-core-module="services_standard">
			<div class="g5-container g5-section-head g5-section-head--dark">
				<div class="g5-eyebrow">Darbo standartas</div>
				<div class="g5-section-head__copy">
					<h2 class="g5-display-md" id="g5-services-standard-title">ISO standartai ir SSVA kvalifikacija.</h2>
				</div>
			</div>
			<div class="g5-container g5-stats-grid">
				<?php foreach ( $certifications as $certification ) : ?>
					<?php $parts = array_map( 'trim', explode( '|', $certification, 2 ) ); ?>
					<div class="g5-stat">
						<strong><?php echo esc_html( $parts[0] ); ?></strong>
						<?php if ( ! empty( $parts[1] ) ) : ?>
							<span><?php echo esc_html( $parts[1] ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php echo g5tech_render_page_modules( 'services' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<section class="g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-services-cta-title" data-g5-page-anchor="cta">
		<div class="g5-container g5-cta-grid">
			<div>
				<div class="g5-eyebrow">Projektas</div>
				<h2 class="g5-display-lg" id="g5-services-cta-title"><?php echo esc_html( $cta_title ); ?></h2>
				<?php if ( $cta_text ) : ?>
					<p class="g5-body"><?php echo esc_html( $cta_text ); ?></p>
				<?php endif; ?>
			</div>
			<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( $contact_url ) ); ?>">
				<?php echo esc_html( $cta_label ); ?>
				<span class="g5-button__icon" aria-hidden="true">→</span>
			</a>
		</div>
	</section>
	</main>
	<?php
	return ob_get_clean();
}

function g5tech_render_services_grid() {
	return g5tech_compose_modular_page( 'services', g5tech_get_legacy_page_html( 'services' ) );
}
