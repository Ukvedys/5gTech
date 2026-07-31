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
		'steps'      => 'g5tech_render_steps_block',
		'step'       => 'g5tech_render_step_block',
		'check-list' => 'g5tech_render_check_list_block',
		'check-item' => 'g5tech_render_check_item_block',
		'partner-stats' => 'g5tech_render_partner_stats_block',
		'equipment-logos' => 'g5tech_render_equipment_logos_block',
		'media-frame'     => 'g5tech_render_media_frame_block',
		'page-cta'  => 'g5tech_render_page_cta_block',
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
	$button = array();

	if ( ! empty( $attributes['buttonLabel'] ) && ! empty( $attributes['buttonUrl'] ) ) {
		$button = array(
			'label' => (string) $attributes['buttonLabel'],
			'url'   => g5tech_block_url( $attributes['buttonUrl'] ),
		);
	}

	ob_start();
	g5tech_content_hero(
		(string) ( $attributes['eyebrow'] ?? '' ),
		(string) ( $attributes['title'] ?? '' ),
		(string) ( $attributes['lead'] ?? '' ),
		$button,
		! empty( $attributes['compact'] )
	);

	return (string) ob_get_clean();
}

function g5tech_render_page_cta_block( $attributes = array() ) {
	ob_start();
	g5tech_content_cta(
		(string) ( $attributes['eyebrow'] ?? '' ),
		(string) ( $attributes['title'] ?? '' ),
		(string) ( $attributes['body'] ?? '' ),
		(string) ( $attributes['buttonLabel'] ?? '' ),
		g5tech_block_url( $attributes['buttonUrl'] ?? '' )
	);

	return (string) ob_get_clean();
}

function g5tech_render_section_block( $attributes, $content ) {
	$theme    = (string) ( $attributes['theme'] ?? 'light' );
	$anchor   = sanitize_title( (string) ( $attributes['anchorId'] ?? '' ) );
	$anchor   = $anchor ?: 'section-' . substr( md5( (string) ( $attributes['title'] ?? '' ) ), 0, 8 );
	$is_dark  = 'dark' === $theme;

	ob_start();
	?>
	<section class="<?php echo esc_attr( g5tech_section_theme_classes( $theme ) ); ?>" aria-labelledby="<?php echo esc_attr( $anchor ); ?>">
		<?php
		g5tech_content_section_head(
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
			if ( 'g5tech/card' !== $inner->name ) {
				continue;
			}

			$cards[] = array(
				'title' => (string) ( $inner->attributes['title'] ?? '' ),
				'text'  => (string) ( $inner->attributes['text'] ?? '' ),
			);
		}
	}

	if ( ! $cards ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container card-grid"><?php foreach ( $cards as $index => $card ) : ?><div class="info-card"><span class="info-card__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><h3 class="g5-heading-sm"><?php echo esc_html( wp_strip_all_tags( $card['title'] ) ); ?></h3><p><?php echo esc_html( wp_strip_all_tags( $card['text'] ) ); ?></p></div><?php endforeach; ?></div>
	<?php

	return (string) ob_get_clean();
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
