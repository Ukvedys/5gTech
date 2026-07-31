<?php
/**
 * Teisinės informacijos puslapiai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_legal_blocks() {
	register_block_type(
		'g5tech/privacy-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH privatumo politika',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_privacy_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
	register_block_type(
		'g5tech/cookies-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH slapukų politika',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_cookies_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_legal_blocks' );

/**
 * Redaguojami teisinių puslapių blokai.
 *
 * Šie blokai naudoja WP 7.0 PHP-only registraciją (`autoRegister`), todėl
 * tekstiniai atributai redaktoriuje automatiškai gauna laukus, o pats blokas
 * piešiamas serveryje. Rekvizitai ir el. paštas lieka bendruose nustatymuose,
 * kad nebūtų dubliuojami teisiniame tekste.
 */
function g5tech_register_legal_content_blocks() {
	register_block_type(
		'g5tech/legal-hero',
		array(
			'api_version'     => 3,
			'title'           => 'Teisinio puslapio antraštė',
			'category'        => 'theme',
			'attributes'      => array(
				'eyebrow' => array(
					'type'    => 'string',
					'default' => 'Teisinė informacija',
				),
				'title'   => array(
					'type'    => 'string',
					'default' => '',
				),
				'lead'    => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'g5tech_render_legal_hero_block',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
			),
		)
	);

	register_block_type(
		'g5tech/company-line',
		array(
			'api_version'     => 3,
			'title'           => 'Įmonės rekvizitai (iš nustatymų)',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_company_line_block',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
			),
		)
	);

	register_block_type(
		'g5tech/contact-email',
		array(
			'api_version'     => 3,
			'title'           => 'Kontaktinis el. paštas (iš nustatymų)',
			'category'        => 'theme',
			'attributes'      => array(
				'intro' => array(
					'type'    => 'string',
					'default' => 'Klausimais rašykite',
				),
			),
			'render_callback' => 'g5tech_render_contact_email_block',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_legal_content_blocks' );

function g5tech_render_legal_hero_block( $attributes = array() ) {
	$eyebrow = isset( $attributes['eyebrow'] ) ? (string) $attributes['eyebrow'] : '';
	$title   = isset( $attributes['title'] ) ? (string) $attributes['title'] : '';
	$lead    = isset( $attributes['lead'] ) ? (string) $attributes['lead'] : '';

	ob_start();
	?>
	<section class="inner-hero inner-hero--compact g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid"><div class="inner-hero__copy">
			<?php if ( '' !== $eyebrow ) : ?>
				<div class="g5-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
			<?php endif; ?>
			<h1 class="g5-display-xl" id="page-title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( '' !== $lead ) : ?>
				<p class="g5-body-lg"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
		</div></div>
	</section>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_company_line_block() {
	$address      = g5tech_setting( 'address' );
	$company_code = g5tech_setting( 'company_code' );

	$line = 'UAB „5GTECH“';

	if ( $company_code ) {
		$line .= ', įmonės kodas ' . $company_code;
	}

	if ( $address ) {
		$line .= ', ' . str_replace( "\n", ', ', $address );
	}

	return '<p>' . esc_html( $line ) . '.</p>';
}

function g5tech_render_contact_email_block( $attributes = array() ) {
	$intro = isset( $attributes['intro'] ) ? (string) $attributes['intro'] : '';
	$email = sanitize_email( g5tech_setting( 'email' ) ) ?: sanitize_email( get_option( 'admin_email' ) );

	if ( ! $email ) {
		return '';
	}

	return sprintf(
		'<p>%s <a href="mailto:%s">%s</a>.</p>',
		esc_html( $intro ),
		esc_attr( antispambot( $email ) ),
		esc_html( antispambot( $email ) )
	);
}

function g5tech_legal_hero( $title, $lead ) {
	?>
	<section class="inner-hero inner-hero--compact g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid"><div class="inner-hero__copy"><div class="g5-eyebrow">Teisinė informacija</div><h1 class="g5-display-xl" id="page-title"><?php echo esc_html( $title ); ?></h1><p class="g5-body-lg"><?php echo esc_html( $lead ); ?></p></div></div>
	</section>
	<?php
}

function g5tech_render_privacy_page() {
	$email        = sanitize_email( g5tech_setting( 'email' ) ) ?: sanitize_email( get_option( 'admin_email' ) );
	$address      = g5tech_setting( 'address' );
	$company_code = g5tech_setting( 'company_code' );

	ob_start();
	g5tech_legal_hero( 'Privatumo politika', 'Kaip renkame, naudojame ir saugome jūsų asmens duomenis.' );
	?>
	<article class="g5-section g5-grid-lines"><div class="g5-container article">
		<h2>Kas tvarko jūsų duomenis</h2><p>UAB „5GTECH“<?php if ( $company_code ) : ?>, įmonės kodas <?php echo esc_html( $company_code ); ?><?php endif; ?><?php if ( $address ) : ?>, <?php echo esc_html( str_replace( "\n", ', ', $address ) ); ?><?php endif; ?>.</p>
		<h2>Kokius duomenis renkame</h2><p>Kontaktų formose pateiktus duomenis, užklausų turinį, kandidatavimo informaciją ir gyvenimo aprašymą.</p>
		<h2>Kodėl juos naudojame</h2><p>Atsakyti į užklausas, vykdyti darbuotojų atranką ir administruoti svetainės veikimą.</p>
		<h2>Kiek laiko saugome</h2><p>Užklausų duomenis saugome tiek, kiek būtina atsakymui ir bendradarbiavimui. Kandidatų duomenų saugojimo terminą nustatome pagal atrankos tikslą ir taikomus teisės aktus.</p>
		<h2>Jūsų teisės</h2><p>Turite teisę susipažinti su duomenimis, juos ištaisyti, ištrinti, apriboti jų tvarkymą ar pateikti skundą.</p>
		<h2>Kontaktas</h2><p>Dėl privatumo klausimų rašykite <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>.</p>
	</div></article>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_cookies_page() {
	$email = sanitize_email( g5tech_setting( 'email' ) ) ?: sanitize_email( get_option( 'admin_email' ) );

	ob_start();
	g5tech_legal_hero( 'Slapukų politika', 'Kaip svetainėje naudojami slapukai ir kaip galite valdyti savo pasirinkimus.' );
	?>
	<article class="g5-section g5-grid-lines"><div class="g5-container article">
		<h2>Kas yra slapukai</h2><p>Slapukai yra nedideli failai, išsaugomi jūsų įrenginyje lankantis svetainėje.</p>
		<h2>Būtinieji slapukai</h2><p>Jie reikalingi pagrindiniam svetainės veikimui, saugumui ir jūsų pasirinkimų išsaugojimui.</p>
		<h2>Analitiniai slapukai</h2><p>Juos naudojame tik gavę jūsų sutikimą. Šie slapukai padeda suprasti, kaip lankytojai naudojasi svetaine.</p>
		<h2>Kaip pakeisti pasirinkimą</h2><p>Slapukų nustatymus galite pakeisti sutikimų valdymo lange arba savo naršyklės nustatymuose.</p>
		<h2>Kontaktas</h2><p>Dėl slapukų naudojimo rašykite <a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>.</p>
	</div></article>
	<?php

	return (string) ob_get_clean();
}
