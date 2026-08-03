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
	wp_add_inline_script(
		'wp-block-editor',
		'window.g5tech = window.g5tech || {}; window.g5tech.themeUri = ' . wp_json_encode( get_stylesheet_directory_uri() ) . ';',
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
