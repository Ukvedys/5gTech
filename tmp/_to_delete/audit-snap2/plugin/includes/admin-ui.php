<?php
/**
 * Vieninga modulinių puslapių administravimo sąsaja.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_is_unified_page_admin() {
	$page = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) );

	return in_array(
		$page,
		array(
			'g5tech-training-content',
			'g5tech-about-order',
			'g5tech-career-content',
			'g5tech-structured-content',
			'g5tech-page-modules',
		),
		true
	);
}

function g5tech_enqueue_unified_page_admin_assets() {
	if ( ! g5tech_is_unified_page_admin() ) {
		return;
	}

	wp_enqueue_style(
		'g5tech-unified-page-admin',
		G5TECH_CORE_URL . 'assets/admin-page-editor.css',
		array(),
		G5TECH_CORE_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'g5tech_enqueue_unified_page_admin_assets' );

function g5tech_render_unified_admin_header( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'title'       => '',
			'description' => '',
			'page_url'    => '',
			'actions'     => array(),
		)
	);
	?>
	<div class="g5tech-admin-header">
		<div>
			<h1><?php echo esc_html( $args['title'] ); ?></h1>
			<?php if ( $args['description'] ) : ?>
				<p><?php echo esc_html( $args['description'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="g5tech-admin-header__actions">
			<?php foreach ( $args['actions'] as $action ) : ?>
				<a class="button <?php echo ! empty( $action['primary'] ) ? 'button-primary' : ''; ?>" href="<?php echo esc_url( $action['url'] ); ?>">
					<?php echo esc_html( $action['label'] ); ?>
				</a>
			<?php endforeach; ?>
			<?php if ( $args['page_url'] ) : ?>
				<a class="button" href="<?php echo esc_url( $args['page_url'] ); ?>" target="_blank" rel="noopener">Atidaryti puslapį ↗</a>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

function g5tech_render_unified_admin_preview( $page_url, $title ) {
	?>
	<aside class="g5tech-admin-preview">
		<div class="g5tech-admin-preview__head">
			<strong>Viešo puslapio peržiūra</strong>
			<span>Atnaujinama išsaugojus</span>
		</div>
		<iframe
			title="<?php echo esc_attr( $title . ' peržiūra' ); ?>"
			src="<?php echo esc_url( add_query_arg( 'admin_preview', time(), $page_url ) ); ?>"
			loading="lazy"
		></iframe>
	</aside>
	<?php
}
