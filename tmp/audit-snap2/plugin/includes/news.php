<?php
/**
 * Naujienų sąrašas ir vieno WordPress įrašo pateikimas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_news_blocks() {
	register_block_type(
		'g5tech/news-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH naujienos',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_news_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);

	register_block_type(
		'g5tech/news-article',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH naujienos įrašas',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_news_article',
			'uses_context'    => array( 'postId', 'postType' ),
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_news_blocks' );

/**
 * Standartinius WordPress įrašus administracijoje vadiname naujienomis.
 *
 * Paliekamas įprastas blokų redaktorius, kategorijos, žymos, specialusis
 * paveikslėlis ir santrauka.
 */
function g5tech_news_post_type_labels( $labels ) {
	$labels->name                  = 'Naujienos';
	$labels->singular_name         = 'Naujiena';
	$labels->menu_name             = 'Naujienos';
	$labels->name_admin_bar        = 'Naujiena';
	$labels->add_new               = 'Pridėti naujieną';
	$labels->add_new_item          = 'Pridėti naują naujieną';
	$labels->edit_item             = 'Redaguoti naujieną';
	$labels->new_item              = 'Nauja naujiena';
	$labels->view_item             = 'Peržiūrėti naujieną';
	$labels->view_items            = 'Peržiūrėti naujienas';
	$labels->search_items          = 'Ieškoti naujienų';
	$labels->not_found             = 'Naujienų nerasta';
	$labels->not_found_in_trash    = 'Šiukšlinėje naujienų nerasta';
	$labels->all_items             = 'Visos naujienos';
	$labels->archives              = 'Naujienų archyvas';
	$labels->attributes            = 'Naujienos nustatymai';
	$labels->featured_image        = 'Pagrindinė naujienos nuotrauka';
	$labels->set_featured_image    = 'Pasirinkti pagrindinę nuotrauką';
	$labels->remove_featured_image = 'Pašalinti pagrindinę nuotrauką';
	$labels->use_featured_image    = 'Naudoti kaip pagrindinę nuotrauką';

	return $labels;
}
add_filter( 'post_type_labels_post', 'g5tech_news_post_type_labels' );

function g5tech_news_category_label( $post_id ) {
	$categories = get_the_category( $post_id );

	return $categories ? $categories[0]->name : 'Naujienos';
}

function g5tech_render_news_cards( $limit = 12 ) {
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $posts ) {
		return '<p class="g5-body-lg">Naujų įrašų šiuo metu nėra.</p>';
	}

	ob_start();
	?>
	<div class="card-grid">
		<?php foreach ( $posts as $news_post ) : ?>
			<?php
			$excerpt = get_the_excerpt( $news_post );
			$excerpt = $excerpt ?: wp_trim_words( wp_strip_all_tags( $news_post->post_content ), 20 );
			?>
			<a class="info-card" href="<?php echo esc_url( get_permalink( $news_post ) ); ?>">
				<span class="info-card__number"><?php echo esc_html( get_the_date( 'Y', $news_post ) ); ?> · <?php echo esc_html( g5tech_news_category_label( $news_post->ID ) ); ?></span>
				<h3 class="g5-heading-md"><?php echo esc_html( get_the_title( $news_post ) ); ?></h3>
				<?php if ( $excerpt ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
				<span class="info-card__link">Skaityti →</span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_news_page_legacy() {
	ob_start();
	?>
	<section class="inner-hero inner-hero--compact g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid">
			<div class="inner-hero__copy">
				<div class="g5-eyebrow">Naujienos</div>
				<h1 class="g5-display-xl" id="page-title">Projektai, komanda ir techninės įžvalgos.</h1>
			</div>
		</div>
	</section>
	<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="news-title" data-g5-core-module="news_list">
		<div class="g5-container section-head">
			<div class="g5-eyebrow">Naujausia</div>
			<div class="section-head__copy"><h2 class="g5-display-md" id="news-title">Naujausi įrašai.</h2></div>
		</div>
		<div class="g5-container"><?php echo g5tech_render_news_cards(); ?></div>
	</section>
	<?php echo g5tech_render_page_modules( 'news' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_news_page() {
	return g5tech_compose_modular_page( 'news', g5tech_get_legacy_page_html( 'news' ) );
}

function g5tech_news_article_post_id( $block ) {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}

	return get_the_ID();
}

function g5tech_render_news_article( $attributes, $content, $block ) {
	$post_id = g5tech_news_article_post_id( $block );
	$image   = get_the_post_thumbnail(
		$post_id,
		'large',
		array(
			'alt'     => get_the_title( $post_id ),
			'loading' => 'eager',
		)
	);
	$caption = has_post_thumbnail( $post_id )
		? wp_get_attachment_caption( get_post_thumbnail_id( $post_id ) )
		: '';

	ob_start();
	?>
	<section class="inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid">
			<div class="inner-hero__copy">
				<nav class="breadcrumbs" aria-label="Puslapio kelias"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a><span>/</span><a href="<?php echo esc_url( home_url( '/naujienos/' ) ); ?>">Naujienos</a></nav>
				<div class="g5-eyebrow"><?php echo esc_html( g5tech_news_category_label( $post_id ) ); ?> · <?php echo esc_html( get_the_date( 'Y m d', $post_id ) ); ?></div>
				<h1 class="g5-display-xl" id="page-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
			</div>
		</div>
	</section>
	<article class="g5-section g5-grid-lines">
		<div class="g5-container article">
			<?php if ( $image ) : ?>
				<figure class="media-frame g5-news-image">
					<?php echo $image; ?>
					<?php if ( $caption ) : ?><figcaption><?php echo esc_html( $caption ); ?></figcaption><?php endif; ?>
				</figure>
			<?php endif; ?>
			<?php echo apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) ); ?>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}
