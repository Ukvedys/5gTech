<?php
/**
 * Redaktoriaus peržiūros priedai: temos adresas blokų skriptams ir
 * redaktoriaus drobės stilius, kad puslapis redaktoriuje atrodytų
 * kaip viešoje svetainėje.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Temos adresas blokų redaktoriaus skriptams (atsarginėms nuotraukoms).
 */
function g5tech_editor_preview_globals() {
	$links = array();
	foreach ( array( 'g5_team', 'g5_service', 'g5_project', 'g5_partner', 'g5_faq', 'g5_job', 'post' ) as $type ) {
		$object = get_post_type_object( $type );
		if ( $object && current_user_can( $object->cap->edit_posts ) ) {
			$suffix = 'post' === $type ? '' : '?post_type=' . $type;
			$links[ 'edit.php' . $suffix ] = admin_url( 'edit.php' . $suffix );
			if ( current_user_can( $object->cap->create_posts ) ) {
				$links[ 'post-new.php' . $suffix ] = admin_url( 'post-new.php' . $suffix );
			}
		}
	}
	if ( current_user_can( 'manage_g5tech_settings' ) ) {
		$links['admin.php?page=g5tech-settings'] = admin_url( 'admin.php?page=g5tech-settings' );
	}
	$preview = array(
		'themeUri' => get_stylesheet_directory_uri(),
		'editorLinks' => $links,
		'heroStats' => array( g5tech_stat( 1, '6000+', 'bazinių stočių' ), g5tech_stat( 3, '6', 'Europos šalys' ) ),
	);
	wp_add_inline_script(
		'wp-block-editor',
		'window.g5tech = Object.assign(window.g5tech || {}, ' . wp_json_encode( $preview ) . ');',
		'before'
	);
}
add_action( 'enqueue_block_editor_assets', 'g5tech_editor_preview_globals' );

/**
 * Redaktoriaus drobės korekcijos (patenka į redaktoriaus iframe).
 */
function g5tech_editor_canvas_styles() {
	if ( ! is_admin() ) {
		return;
	}

	$path = G5TECH_CORE_DIR . 'assets/editor-canvas.css';

	wp_enqueue_style(
		'g5tech-editor-canvas',
		G5TECH_CORE_URL . 'assets/editor-canvas.css',
		array(),
		file_exists( $path ) ? (string) filemtime( $path ) : G5TECH_CORE_VERSION
	);
}
add_action( 'enqueue_block_assets', 'g5tech_editor_canvas_styles' );
