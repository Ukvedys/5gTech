<?php
/**
 * Bendri vidinių puslapių blokai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_site_blocks() {
	register_block_type(
		'g5tech/experience-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH patirties puslapis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_experience_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_site_blocks' );

function g5tech_render_experience_page_legacy() {
	$countries      = g5tech_setting_lines( 'countries' );
	$certifications = g5tech_setting_lines( 'certifications' );
	$operators      = g5tech_get_partners( 'operator' );
	$manufacturers  = g5tech_get_partners( 'manufacturer' );
	$projects       = g5tech_get_projects( 3 );
	$country_text   = '';

	if ( $countries ) {
		$last_country = array_pop( $countries );
		$country_text = $countries
			? implode( ', ', $countries ) . ' ir ' . $last_country . '.'
			: $last_country . '.';
		$countries[] = $last_country;
	}

	ob_start();
	?>
	<section class="g5-inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-experience-title">
		<div class="g5-container g5-grid">
			<div class="g5-inner-hero__copy">
				<div class="g5-eyebrow">Patirtis</div>
				<h1 class="g5-display-xl" id="g5-experience-title">Patirtis Europos infrastruktūros projektuose.</h1>
				<p class="g5-body-lg">Dirbame šešiose Europos šalyse, pagal skirtingų operatorių standartus ir su įvairių gamintojų įranga.</p>
			</div>
		</div>
	</section>

	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-experience-numbers-title" data-g5-core-module="experience_numbers">
		<div class="g5-container g5-section-head g5-section-head--dark">
			<div class="g5-eyebrow">Skaičiai</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-experience-numbers-title">Pagrindiniai skaičiai.</h2>
			</div>
		</div>
		<div class="g5-container g5-stats-grid">
			<?php foreach ( g5tech_stats() as $stat ) : ?>
				<div class="g5-stat">
					<strong><?php echo esc_html( $stat['value'] ); ?></strong>
					<span><?php echo esc_html( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="g5-section g5-grid-lines" aria-labelledby="g5-countries-title" data-g5-core-module="experience_geography">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Geografija</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-countries-title">Šešios Europos šalys.</h2>
				<?php if ( $country_text ) : ?>
					<p class="g5-body"><?php echo esc_html( $country_text ); ?></p>
				<?php endif; ?>
			</div>
		</div>
		<figure class="g5-container g5-media-frame g5-media-frame--map">
			<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/europe-footprint-map.png' ) ); ?>" alt="<?php echo esc_attr( '5G TECH projektų geografija Europoje: ' . rtrim( $country_text, '.' ) ); ?>">
		</figure>
	</section>

	<?php if ( $projects ) : ?>
		<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-projects-preview-title" data-g5-core-module="experience_projects">
			<div class="g5-container g5-section-head g5-section-head--dark">
				<div class="g5-eyebrow">Projektai</div>
				<div class="g5-section-head__copy">
					<h2 class="g5-display-md" id="g5-projects-preview-title">Atrinkti projektai.</h2>
				</div>
			</div>
			<div class="g5-container">
				<?php echo g5tech_render_project_cards( $projects ); ?>
				<div class="g5-projects-more">
					<a class="g5-button g5-button--outline-light" href="<?php echo esc_url( get_post_type_archive_link( 'g5_project' ) ); ?>">Visi projektai →</a>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="g5-operators-title" data-g5-core-module="experience_partners">
		<div class="g5-container g5-section-head">
			<div class="g5-eyebrow">Patirtis</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-operators-title">Partneriai.</h2>
			</div>
		</div>
		<div class="g5-container g5-partner-split">
			<div class="g5-partner-split__main">
				<h3 class="g5-heading-md">Operatoriai</h3>
				<ul class="g5-tag-list">
					<?php foreach ( $operators as $operator ) : ?>
						<li><?php echo esc_html( get_the_title( $operator ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="g5-partner-split__side">
				<h3 class="g5-heading-md">Įrangos gamintojai</h3>
				<ul class="g5-tag-list">
					<?php foreach ( $manufacturers as $manufacturer ) : ?>
						<li><?php echo esc_html( get_the_title( $manufacturer ) ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-certifications-title" data-g5-core-module="experience_certifications">
		<div class="g5-container g5-section-head g5-section-head--dark">
			<div class="g5-eyebrow">Darbo standartas</div>
			<div class="g5-section-head__copy">
				<h2 class="g5-display-md" id="g5-certifications-title">ISO standartai ir SSVA kvalifikacija.</h2>
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

	<?php echo g5tech_render_page_modules( 'experience' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<section class="g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-experience-cta-title" data-g5-page-anchor="cta">
		<div class="g5-container g5-cta-grid">
			<div>
				<div class="g5-eyebrow">Naujas projektas</div>
				<h2 class="g5-display-lg" id="g5-experience-cta-title">Aptarkime jūsų projektą.</h2>
				<p class="g5-body">Nurodykite rinką, operatorių ar įrangą – pateiksime aktualią patirtį.</p>
			</div>
			<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( g5tech_setting( 'contact_page_url', '/kontaktai/' ) ) ); ?>">
				Susisiekti
				<span class="g5-button__icon" aria-hidden="true">→</span>
			</a>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

function g5tech_render_experience_page() {
	return g5tech_compose_modular_page( 'experience', g5tech_get_legacy_page_html( 'experience' ) );
}
