<?php
/**
 * Redaguojami puslapio turinio blokai.
 *
 * Blokai registruojami iš `build/` katalogo (šaltiniai — `blocks-src/`,
 * kompiliuojama su `npm run build`). Redaktoriuje jie turi tikrą sąsają su
 * įterpiamais kartotiniais elementais, o viešoje svetainėje atvaizduojami
 * serveryje ir duoda tokį patį markup'ą kaip ankstesnės PHP funkcijos.
 *
 * @package 5gtech-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sekcijos fono klasės pagal pasirinktą temą.
 */
function g5tech_section_theme_classes( $theme ) {
	switch ( $theme ) {
		case 'paper':
			return 'g5-section g5-section--paper g5-grid-lines';
		case 'dark':
			return 'g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark';
	}

	return 'g5-section g5-grid-lines';
}

function g5tech_register_content_blocks() {
	$blocks = array(
		'page-hero' => 'g5tech_render_page_hero_block',
		'section'   => 'g5tech_render_section_block',
		'card-grid' => 'g5tech_render_card_grid_block',
		'card'      => 'g5tech_render_card_block',
		'link-card' => 'g5tech_render_link_card_block',
		'steps'      => 'g5tech_render_steps_block',
		'step'       => 'g5tech_render_step_block',
		'check-list' => 'g5tech_render_check_list_block',
		'check-item' => 'g5tech_render_check_item_block',
		'partner-stats' => 'g5tech_render_partner_stats_block',
		'equipment-logos' => 'g5tech_render_equipment_logos_block',
		'media-frame'     => 'g5tech_render_media_frame_block',
		'page-cta'  => 'g5tech_render_page_cta_block',
		'news-grid'          => 'g5tech_render_news_grid_block',
		'faq-group'          => 'g5tech_render_faq_group_block',
		'job-groups'         => 'g5tech_render_job_groups_block',
		'contact-form-split' => 'g5tech_render_contact_form_split_block',
		'contact-people'     => 'g5tech_render_contact_people_block',
		'application-form'   => 'g5tech_render_application_form_block',
		'stats-band'         => 'g5tech_render_stats_band_block',
		'certification-grid' => 'g5tech_render_certification_grid_block',
		'partner-tag-split'  => 'g5tech_render_partner_tag_split_block',
		'projects-preview'   => 'g5tech_render_projects_preview_block',
		'geo-section'        => 'g5tech_render_geo_section_block',
	);

	foreach ( $blocks as $dir => $callback ) {
		$path = G5TECH_CORE_DIR . 'build/' . $dir;

		if ( ! file_exists( $path . '/block.json' ) ) {
			continue;
		}

		register_block_type( $path, array( 'render_callback' => $callback ) );
	}
}
add_action( 'init', 'g5tech_register_content_blocks' );

/**
 * Santykinė nuoroda paverčiama pilnu adresu, kad turinys nebūtų pririštas
 * prie konkrečios aplinkos (lokali, staging, produkcija).
 */
function g5tech_block_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	if ( 0 === strpos( $url, '/' ) ) {
		return home_url( $url );
	}

	return $url;
}

function g5tech_render_page_hero_block( $attributes = array() ) {
	$button    = array();
	$secondary = array();

	if ( ! empty( $attributes['buttonLabel'] ) && ! empty( $attributes['buttonUrl'] ) ) {
		$button = array(
			'label' => (string) $attributes['buttonLabel'],
			'url'   => g5tech_block_url( $attributes['buttonUrl'] ),
			'icon'  => (string) ( $attributes['buttonIcon'] ?? '→' ),
		);
	}

	if ( ! empty( $attributes['button2Label'] ) && ! empty( $attributes['button2Url'] ) ) {
		$secondary = array(
			'label' => (string) $attributes['button2Label'],
			'url'   => g5tech_block_url( $attributes['button2Url'] ),
		);
	}

	if ( 'site' === ( $attributes['dialect'] ?? 'internal' ) ) {
		return g5tech_site_hero_markup( $attributes, $button, $secondary );
	}

	ob_start();
	g5tech_content_hero(
		(string) ( $attributes['eyebrow'] ?? '' ),
		(string) ( $attributes['title'] ?? '' ),
		(string) ( $attributes['lead'] ?? '' ),
		$button,
		! empty( $attributes['compact'] ),
		$secondary
	);

	return (string) ob_get_clean();
}

/**
 * Svetainės dialekto (g5-*) puslapio antraštė. Naudojama puslapiuose,
 * kurių stilius aprašytas site.css (pvz. Patirtis).
 */
function g5tech_site_hero_markup( $attributes, $button = array(), $secondary = array() ) {
	$anchor = sanitize_title( (string) ( $attributes['anchorId'] ?? '' ) ) ?: 'page-title';
	$lead   = (string) ( $attributes['lead'] ?? '' );

	ob_start();
	?>
	<section class="g5-inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="<?php echo esc_attr( $anchor ); ?>">
		<div class="g5-container g5-grid">
			<div class="g5-inner-hero__copy">
				<div class="g5-eyebrow"><?php echo esc_html( (string) ( $attributes['eyebrow'] ?? '' ) ); ?></div>
				<h1 class="g5-display-xl" id="<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( (string) ( $attributes['title'] ?? '' ) ); ?></h1>
				<?php if ( $lead ) : ?><p class="g5-body-lg"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
				<?php if ( $button ) : ?><div class="g5-inner-hero__actions"><a class="g5-button g5-button--primary" href="<?php echo esc_url( $button['url'] ); ?>"><?php echo esc_html( $button['label'] ); ?> <span class="g5-button__icon" aria-hidden="true"><?php echo esc_html( $button['icon'] ?? '→' ); ?></span></a><?php if ( $secondary ) : ?><a class="g5-button g5-button--outline-light" href="<?php echo esc_url( $secondary['url'] ); ?>"><?php echo esc_html( $secondary['label'] ); ?></a><?php endif; ?></div><?php endif; ?>
			</div>
		</div>
	</section>
	<?php

	return (string) ob_get_clean();
}

/**
 * Svetainės dialekto sekcijos antraštė.
 */
function g5tech_site_section_head( $eyebrow, $title, $id, $dark = false, $lead = '' ) {
	?>
	<div class="g5-container g5-section-head<?php echo $dark ? ' g5-section-head--dark' : ''; ?>">
		<div class="g5-eyebrow"><?php echo esc_html( $eyebrow ); ?></div>
		<div class="g5-section-head__copy"><h2 class="g5-display-md" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></h2><?php if ( $lead ) : ?><p class="g5-body"><?php echo esc_html( $lead ); ?></p><?php endif; ?></div>
	</div>
	<?php
}

function g5tech_render_page_cta_block( $attributes = array() ) {
	$body = (string) ( $attributes['body'] ?? '' );

	// Kai tekstas turi sutapti su nustatymų reikšme (pvz. karjeros el. paštu),
	// jis imamas iš nustatymų, kad nebūtų dubliuojamas dviejose vietose.
	if ( '' === $body && ! empty( $attributes['bodySetting'] ) && function_exists( 'g5tech_setting' ) ) {
		$body = (string) g5tech_setting( sanitize_key( $attributes['bodySetting'] ) );
	}

	if ( 'site' === ( $attributes['dialect'] ?? 'internal' ) ) {
		$anchor = sanitize_title( (string) ( $attributes['anchorId'] ?? '' ) ) ?: 'cta-title';

		ob_start();
		?>
	<section class="g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="<?php echo esc_attr( $anchor ); ?>">
		<div class="g5-container g5-cta-grid">
			<div>
				<div class="g5-eyebrow"><?php echo esc_html( (string) ( $attributes['eyebrow'] ?? '' ) ); ?></div>
				<h2 class="g5-display-lg" id="<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( (string) ( $attributes['title'] ?? '' ) ); ?></h2>
				<?php if ( $body ) : ?><p class="g5-body"><?php echo esc_html( $body ); ?></p><?php endif; ?>
			</div>
			<a class="g5-button g5-button--primary" href="<?php echo esc_url( g5tech_block_url( $attributes['buttonUrl'] ?? '' ) ); ?>"><?php echo esc_html( (string) ( $attributes['buttonLabel'] ?? '' ) ); ?> <span class="g5-button__icon" aria-hidden="true">→</span></a>
		</div>
	</section>
		<?php

		return (string) ob_get_clean();
	}

	ob_start();
	g5tech_content_cta(
		(string) ( $attributes['eyebrow'] ?? '' ),
		(string) ( $attributes['title'] ?? '' ),
		$body,
		(string) ( $attributes['buttonLabel'] ?? '' ),
		g5tech_block_url( $attributes['buttonUrl'] ?? '' )
	);

	return (string) ob_get_clean();
}

function g5tech_render_section_block( $attributes, $content ) {
	$theme      = (string) ( $attributes['theme'] ?? 'light' );
	$anchor     = sanitize_title( (string) ( $attributes['anchorId'] ?? '' ) );
	$anchor     = $anchor ?: 'section-' . substr( md5( (string) ( $attributes['title'] ?? '' ) ), 0, 8 );
	$section_id = sanitize_title( (string) ( $attributes['sectionId'] ?? '' ) );
	$is_dark    = 'dark' === $theme;

	ob_start();
	?>
	<section class="<?php echo esc_attr( g5tech_section_theme_classes( $theme ) ); ?>"<?php echo $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?> aria-labelledby="<?php echo esc_attr( $anchor ); ?>">
		<?php
		$g5tech_section_head = 'site' === ( $attributes['dialect'] ?? 'internal' )
			? 'g5tech_site_section_head'
			: 'g5tech_content_section_head';
		$g5tech_section_head(
			(string) ( $attributes['eyebrow'] ?? '' ),
			(string) ( $attributes['title'] ?? '' ),
			$anchor,
			$is_dark,
			(string) ( $attributes['lead'] ?? '' )
		);
		?>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — vidiniai blokai jau atvaizduoti. ?>
	</section>
	<?php

	return (string) ob_get_clean();
}

/**
 * Kortelių tinklelis su automatine numeracija.
 *
 * Numeriai skaičiuojami iš kortelių eilės, todėl persirikiavus jie
 * atsinaujina patys ir jų nereikia taisyti ranka.
 */
function g5tech_render_card_grid_block( $attributes, $content, $block = null ) {
	$cards = array();

	if ( $block instanceof WP_Block ) {
		foreach ( $block->inner_blocks as $inner ) {
			if ( 'g5tech/card' === $inner->name ) {
				$cards[] = array(
					'type'  => 'card',
					'title' => (string) ( $inner->attributes['title'] ?? '' ),
					'text'  => (string) ( $inner->attributes['text'] ?? '' ),
				);
			} elseif ( 'g5tech/link-card' === $inner->name ) {
				$cards[] = array(
					'type'  => 'link',
					'label' => (string) ( $inner->attributes['label'] ?? '' ),
					'title' => (string) ( $inner->attributes['title'] ?? '' ),
					'link'  => (string) ( $inner->attributes['linkText'] ?? '' ),
					'url'   => g5tech_block_url( $inner->attributes['url'] ?? '' ),
				);
			}
		}
	}

	if ( ! $cards ) {
		return '';
	}

	$number = 0;

	ob_start();
	?>
	<div class="g5-container card-grid"><?php foreach ( $cards as $card ) : ?><?php if ( 'link' === $card['type'] ) : ?><a class="info-card" href="<?php echo esc_url( $card['url'] ); ?>"><span class="info-card__number"><?php echo esc_html( wp_strip_all_tags( $card['label'] ) ); ?></span><h3 class="g5-heading-md"><?php echo esc_html( wp_strip_all_tags( $card['title'] ) ); ?></h3><span class="info-card__link"><?php echo esc_html( wp_strip_all_tags( $card['link'] ) ); ?></span></a><?php else : ?><?php $number++; ?><div class="info-card"><span class="info-card__number"><?php echo esc_html( str_pad( (string) $number, 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( wp_strip_all_tags( $card['title'] ) ); ?></h3><p><?php echo esc_html( wp_strip_all_tags( $card['text'] ) ); ?></p></div><?php endif; ?><?php endforeach; ?></div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Nuorodos kortelę atvaizduoja tėvinis tinklelis.
 */
function g5tech_render_link_card_block() {
	return '';
}

/**
 * Atskira kortelė atvaizduojama tėvinio tinklelio, todėl pati nieko negrąžina.
 */
function g5tech_render_card_block() {
	return '';
}

/**
 * Numeruotų žingsnių sąrašas. Numeracija skaičiuojama iš eilės.
 */
function g5tech_render_steps_block( $attributes, $content, $block = null ) {
	$steps = array();

	if ( $block instanceof WP_Block ) {
		foreach ( $block->inner_blocks as $inner ) {
			if ( 'g5tech/step' !== $inner->name ) {
				continue;
			}

			$steps[] = array(
				'title' => (string) ( $inner->attributes['title'] ?? '' ),
				'text'  => (string) ( $inner->attributes['text'] ?? '' ),
			);
		}
	}

	if ( ! $steps ) {
		return '';
	}

	ob_start();
	?>
	<ol class="g5-container steps"><?php foreach ( $steps as $index => $step ) : ?><li><span><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( wp_strip_all_tags( $step['title'] ) ); ?></strong><p><?php echo esc_html( wp_strip_all_tags( $step['text'] ) ); ?></p></li><?php endforeach; ?></ol>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_step_block() {
	return '';
}

/**
 * Varnelių sąrašas.
 */
function g5tech_render_check_list_block( $attributes, $content, $block = null ) {
	$items = array();

	if ( $block instanceof WP_Block ) {
		foreach ( $block->inner_blocks as $inner ) {
			if ( 'g5tech/check-item' !== $inner->name ) {
				continue;
			}

			$text = trim( wp_strip_all_tags( (string) ( $inner->attributes['text'] ?? '' ) ) );

			if ( '' !== $text ) {
				$items[] = $text;
			}
		}
	}

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<ul class="g5-container check-list"><?php foreach ( $items as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?></ul>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_check_item_block() {
	return '';
}

/**
 * Įrangos ir gamintojų tinklelis. Pavadinimai visada imami iš katalogo,
 * todėl pervadinus gamintoją atsinaujina visuose puslapiuose.
 */
function g5tech_render_partner_stats_block( $attributes = array() ) {
	$ids = array_values( array_filter( array_map( 'absint', (array) ( $attributes['partnerIds'] ?? array() ) ) ) );

	if ( ! $ids || ! function_exists( 'g5tech_get_partners' ) ) {
		return '';
	}

	$items = g5tech_get_partners( '', $ids );

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container stats-grid"><?php foreach ( $items as $item ) : ?><div class="stat"><strong><?php echo esc_html( get_the_title( $item ) ); ?></strong><span><?php echo esc_html( g5tech_partner_type_label( get_post_meta( $item->ID, 'g5_partner_type', true ) ) ); ?></span></div><?php endforeach; ?></div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Įrangos logotipų juosta. Logotipai ir pavadinimai imami iš katalogo.
 */
function g5tech_render_equipment_logos_block( $attributes = array() ) {
	$ids = array_values( array_filter( array_map( 'absint', (array) ( $attributes['partnerIds'] ?? array() ) ) ) );

	if ( ! $ids || ! function_exists( 'g5tech_get_partners' ) ) {
		return '';
	}

	$items = g5tech_get_partners( '', $ids );

	if ( ! $items ) {
		return '';
	}

	ob_start();
	?>
	<ul class="g5-container equipment-list">
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
	<?php

	return (string) ob_get_clean();
}

/**
 * Nuotrauka fiksuoto formato rėmelyje.
 */
function g5tech_render_media_frame_block( $attributes = array() ) {
	$image_id = absint( $attributes['imageId'] ?? 0 );
	$url      = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	if ( ! $url ) {
		$url = (string) ( $attributes['imageUrl'] ?? '' );
	}

	// Temos atsarginė nuotrauka saugoma santykiniu keliu, kad nebūtų pririšta
	// prie konkrečios aplinkos adreso.
	if ( ! $url && ! empty( $attributes['themeFallback'] ) ) {
		$url = get_theme_file_uri( ltrim( (string) $attributes['themeFallback'], '/' ) );
	}

	if ( ! $url ) {
		return '';
	}

	$ratio = trim( (string) ( $attributes['ratio'] ?? '16 / 8' ) ) ?: '16 / 8';

	ob_start();
	?>
	<figure class="g5-container media-frame" style="aspect-ratio: <?php echo esc_attr( $ratio ); ?>"><img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( (string) ( $attributes['alt'] ?? '' ) ); ?>"></figure>
	<?php

	return (string) ob_get_clean();
}

/**
 * Naujienų tinklelis. Įrašai imami iš skilties „Naujienos“.
 */
function g5tech_render_news_grid_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_render_news_cards' ) ) {
		return '';
	}

	$limit = absint( $attributes['limit'] ?? 12 );

	return '<div class="g5-container">' . g5tech_render_news_cards( $limit ?: 12 ) . '</div>';
}

/**
 * Kandidatų DUK grupė. Klausimai valdomi skiltyje „Dažniausi klausimai“.
 */
function g5tech_render_faq_group_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_render_candidate_faq_group' ) ) {
		return '';
	}

	$allowed = array( 'start', 'travel', 'safety', 'daily' );
	$group   = (string) ( $attributes['group'] ?? 'start' );

	if ( ! in_array( $group, $allowed, true ) ) {
		$group = 'start';
	}

	return g5tech_render_candidate_faq_group( $group );
}

/**
 * Aktyvių darbo pozicijų sąrašas grupėmis.
 */
function g5tech_render_job_groups_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_get_active_jobs' ) || ! function_exists( 'g5tech_render_job_list' ) ) {
		return '';
	}

	$groups = array(
		'lithuania' => (string) ( $attributes['lithuaniaLabel'] ?? 'Lietuvos projektai' ),
		'europe'    => (string) ( $attributes['europeLabel'] ?? 'Europos projektai' ),
		'office'    => (string) ( $attributes['officeLabel'] ?? 'Biuro pozicijos' ),
	);
	$empty  = (string) ( $attributes['emptyText'] ?? '' );

	ob_start();
	?>
	<div class="g5-container">
		<?php if ( g5tech_get_active_jobs() ) : ?>
			<?php foreach ( $groups as $group_key => $group_label ) : ?>
				<?php $list = g5tech_render_job_list( $group_key ); ?>
				<?php if ( $list ) : ?>
					<div class="g5-job-group">
						<h3 class="g5-heading-md"><?php echo esc_html( $group_label ); ?></h3>
						<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php else : ?>
			<p class="g5-body-lg"><?php echo esc_html( $empty ); ?></p>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Kontaktų forma su įmonės rekvizitais iš nustatymų.
 */
function g5tech_render_contact_form_split_block() {
	if ( ! function_exists( 'g5tech_form_status_message' ) ) {
		return '';
	}

	$status       = g5tech_form_status_message( 'contact' );
	$email        = sanitize_email( g5tech_setting( 'email' ) );
	$phone        = g5tech_setting( 'phone' );
	$address      = g5tech_setting( 'address' );
	$company_code = g5tech_setting( 'company_code' );
	$vat_code     = g5tech_setting( 'vat_code' );

	ob_start();
	?>
	<div class="g5-container split-layout">
		<div class="split-layout__main">
			<form class="form-grid" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="g5tech_contact">
				<?php wp_nonce_field( 'g5tech_contact', 'g5tech_nonce' ); ?>
				<div class="g5-form-trap" aria-hidden="true"><input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" hidden></div>
				<div class="field"><label for="contact-name">Vardas ir pavardė *</label><input id="contact-name" name="name" autocomplete="name" required></div>
				<div class="field"><label for="contact-company">Įmonė</label><input id="contact-company" name="company" autocomplete="organization"></div>
				<div class="field"><label for="contact-email">El. paštas *</label><input id="contact-email" name="email" type="email" autocomplete="email" required></div>
				<div class="field"><label for="contact-phone">Telefonas</label><input id="contact-phone" name="phone" type="tel" autocomplete="tel"></div>
				<div class="field field--full"><label for="contact-topic">Užklausos tema *</label><select id="contact-topic" name="topic" required><option value="">Pasirinkite</option><option>Bendradarbiavimas</option><option>Techninė užduotis</option><option>Karjera</option><option>Kita</option></select></div>
				<div class="field field--full"><label for="contact-message">Trumpai aprašykite užduotį *</label><textarea id="contact-message" name="message" required></textarea></div>
				<label class="consent field--full"><input type="checkbox" name="consent" value="1" required><span>Sutinku, kad mano duomenys būtų naudojami atsakymui į užklausą. <a href="<?php echo esc_url( home_url( '/privatumo-politika/' ) ); ?>">Privatumo politika</a>.</span></label>
				<div class="field--full"><button class="g5-button g5-button--dark" type="submit">Siųsti užklausą <span class="g5-button__icon" aria-hidden="true">→</span></button></div>
				<?php if ( $status ) : ?><p class="form-status field--full <?php echo 'error' === $status['type'] ? 'form-status--error' : ''; ?>" tabindex="-1"><?php echo esc_html( $status['text'] ); ?></p><?php endif; ?>
			</form>
		</div>
		<aside class="split-layout__side">
			<h3 class="g5-heading-md">UAB „5GTECH“</h3>
			<ul class="plain-list" style="margin-top: var(--5g-space-6)">
				<?php if ( $address ) : ?><li><?php echo nl2br( esc_html( $address ) ); ?></li><?php endif; ?>
				<?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li><?php endif; ?>
				<?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
				<?php if ( $company_code ) : ?><li>Įmonės kodas <?php echo esc_html( $company_code ); ?></li><?php endif; ?>
				<?php if ( $vat_code ) : ?><li>PVM kodas <?php echo esc_html( $vat_code ); ?></li><?php endif; ?>
			</ul>
		</aside>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Komandos kontaktų kortelės.
 */
function g5tech_render_contact_people_block() {
	if ( ! function_exists( 'g5tech_contact_team_cards' ) ) {
		return '';
	}

	return g5tech_contact_team_cards();
}

/**
 * Kandidatavimo forma su CV įkėlimu.
 */
function g5tech_render_application_form_block() {
	if ( ! function_exists( 'g5tech_form_status_message' ) || ! function_exists( 'g5tech_application_job_options' ) ) {
		return '';
	}

	$status      = g5tech_form_status_message( 'application' );
	$jobs        = g5tech_application_job_options();
	$selected_id = isset( $_GET['pozicija'] ) ? absint( $_GET['pozicija'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<form class="g5-container form-grid" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="g5tech_application">
		<?php wp_nonce_field( 'g5tech_application', 'g5tech_nonce' ); ?>
		<div class="g5-form-trap" aria-hidden="true"><input id="apply-website" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" hidden></div>
		<div class="field"><label for="apply-name">Vardas *</label><input id="apply-name" name="name" autocomplete="given-name" required></div>
		<div class="field"><label for="apply-surname">Pavardė *</label><input id="apply-surname" name="surname" autocomplete="family-name" required></div>
		<div class="field"><label for="apply-city">Miestas *</label><input id="apply-city" name="city" autocomplete="address-level2" required></div>
		<div class="field"><label for="apply-phone">Telefonas *</label><input id="apply-phone" name="phone" type="tel" autocomplete="tel" required></div>
		<div class="field"><label for="apply-email">El. paštas *</label><input id="apply-email" name="email" type="email" autocomplete="email" required></div>
		<div class="field"><label for="apply-role">Dominanti pozicija</label><select id="apply-role" name="role"><option value="">Pasirinkite arba palikite tuščią</option><?php foreach ( $jobs as $job ) : ?><option value="<?php echo (int) $job->ID; ?>" <?php selected( $selected_id, $job->ID ); ?>><?php echo esc_html( get_the_title( $job ) ); ?></option><?php endforeach; ?><option value="other">Kita</option></select></div>
		<div class="field field--full"><label for="apply-cv">Gyvenimo aprašymas (CV) *</label><input id="apply-cv" name="cv" type="file" accept=".pdf,.doc,.docx" required><span class="field-hint">PDF, DOC arba DOCX, iki 5 MB.</span></div>
		<div class="field field--full"><label for="apply-experience">Techninė darbo patirtis</label><textarea id="apply-experience" name="experience"></textarea></div>
		<fieldset class="field field--full checkboxes"><legend>Pažymėkite, kas tinka</legend>
			<label><input type="checkbox" name="skills[]" value="height"> Esu dirbęs (-usi) aukštyje</label>
			<label><input type="checkbox" name="skills[]" value="electricity"> Turiu patirties su elektros įranga</label>
			<label><input type="checkbox" name="skills[]" value="english"> Galiu susikalbėti angliškai</label>
			<label><input type="checkbox" name="skills[]" value="driver"> Turiu B kategorijos pažymėjimą</label>
			<label><input type="checkbox" name="skills[]" value="travel"> Galiu vykti į komandiruotes</label>
			<label><input type="checkbox" name="skills[]" value="documents"> Galiu skaityti techninę dokumentaciją</label>
		</fieldset>
		<div class="field field--full"><label for="apply-motivation">Kodėl norėtumėte prisijungti?</label><textarea id="apply-motivation" name="motivation"></textarea></div>
		<label class="consent field--full"><input type="checkbox" name="consent" value="1" required><span>Sutinku, kad mano duomenys ir CV būtų naudojami atrankos tikslais. <a href="<?php echo esc_url( home_url( '/privatumo-politika/' ) ); ?>">Privatumo politika</a>.</span></label>
		<div class="field--full"><button class="g5-button g5-button--dark" type="submit">Pateikti kandidatūrą <span class="g5-button__icon" aria-hidden="true">→</span></button></div>
		<?php if ( $status ) : ?><p class="form-status field--full <?php echo 'error' === $status['type'] ? 'form-status--error' : ''; ?>" tabindex="-1"><?php echo esc_html( $status['text'] ); ?></p><?php endif; ?>
	</form>
	<?php

	return (string) ob_get_clean();
}

/**
 * Skaičių juosta iš nustatymų (g5tech_stats).
 */
function g5tech_render_stats_band_block() {
	if ( ! function_exists( 'g5tech_stats' ) ) {
		return '';
	}

	$stats = g5tech_stats();

	if ( ! $stats ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container g5-stats-grid">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="g5-stat">
				<strong><?php echo esc_html( $stat['value'] ); ?></strong>
				<span><?php echo esc_html( $stat['label'] ); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Sertifikatų juosta iš nustatymų eilučių „reikšmė | paaiškinimas“.
 */
function g5tech_render_certification_grid_block() {
	if ( ! function_exists( 'g5tech_setting_lines' ) ) {
		return '';
	}

	$certifications = g5tech_setting_lines( 'certifications' );

	if ( ! $certifications ) {
		return '';
	}

	ob_start();
	?>
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
	<?php

	return (string) ob_get_clean();
}

/**
 * Operatorių ir gamintojų sąrašai dviem stulpeliais.
 */
function g5tech_render_partner_tag_split_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_get_partners' ) ) {
		return '';
	}

	$operators     = g5tech_get_partners( 'operator' );
	$manufacturers = g5tech_get_partners( 'manufacturer' );

	ob_start();
	?>
	<div class="g5-container g5-partner-split">
		<div class="g5-partner-split__main">
			<h3 class="g5-heading-md"><?php echo esc_html( (string) ( $attributes['leftTitle'] ?? 'Operatoriai' ) ); ?></h3>
			<ul class="g5-tag-list">
				<?php foreach ( $operators as $operator ) : ?>
					<li><?php echo esc_html( get_the_title( $operator ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="g5-partner-split__side">
			<h3 class="g5-heading-md"><?php echo esc_html( (string) ( $attributes['rightTitle'] ?? 'Įrangos gamintojai' ) ); ?></h3>
			<ul class="g5-tag-list">
				<?php foreach ( $manufacturers as $manufacturer ) : ?>
					<li><?php echo esc_html( get_the_title( $manufacturer ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Naujausi projektai su nuoroda į visą archyvą.
 */
function g5tech_render_projects_preview_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_get_projects' ) || ! function_exists( 'g5tech_render_project_cards' ) ) {
		return '';
	}

	$limit    = absint( $attributes['limit'] ?? 3 ) ?: 3;
	$projects = g5tech_get_projects( $limit );

	if ( ! $projects ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container">
		<?php echo g5tech_render_project_cards( $projects ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="g5-projects-more">
			<a class="g5-button g5-button--outline-light" href="<?php echo esc_url( get_post_type_archive_link( 'g5_project' ) ); ?>"><?php echo esc_html( (string) ( $attributes['buttonLabel'] ?? 'Visi projektai →' ) ); ?></a>
		</div>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Geografijos sekcija: šalys iš nustatymų, žemėlapis iš temos arba medijos.
 *
 * Sekcija atvaizduojama visa (su antrašte), nes įžangos tekstas
 * skaičiuojamas iš šalių sąrašo nustatymuose.
 */
function g5tech_render_geo_section_block( $attributes = array() ) {
	if ( ! function_exists( 'g5tech_setting_lines' ) ) {
		return '';
	}

	$countries    = g5tech_setting_lines( 'countries' );
	$country_text = '';

	if ( $countries ) {
		$last_country = array_pop( $countries );
		$country_text = $countries
			? implode( ', ', $countries ) . ' ir ' . $last_country . '.'
			: $last_country . '.';
	}

	$anchor   = sanitize_title( (string) ( $attributes['anchorId'] ?? '' ) ) ?: 'g5-countries-title';
	$image_id = absint( $attributes['imageId'] ?? 0 );
	$url      = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';

	if ( ! $url ) {
		$url = get_theme_file_uri( 'assets/images/europe-footprint-map.png' );
	}

	ob_start();
	?>
	<section class="g5-section g5-grid-lines" aria-labelledby="<?php echo esc_attr( $anchor ); ?>">
		<?php
		g5tech_site_section_head(
			(string) ( $attributes['eyebrow'] ?? '' ),
			(string) ( $attributes['title'] ?? '' ),
			$anchor,
			false,
			$country_text
		);
		?>
		<figure class="g5-container g5-media-frame g5-media-frame--map">
			<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( '5G TECH projektų geografija Europoje: ' . rtrim( $country_text, '.' ) ); ?>">
		</figure>
	</section>
	<?php

	return (string) ob_get_clean();
}
