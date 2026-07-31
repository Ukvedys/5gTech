<?php
/**
 * Redaktoriaus kuravimas.
 *
 * Vietoj to, kad puslapių redaktorius būtų apeinamas savais ekranais, jis
 * apribojamas WordPress priemonėmis: leistinų blokų sąrašu ir draudimu
 * atrakinti užrakintą struktūrą.
 *
 * @package 5gtech-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Saugūs branduolio blokai, kuriuos redaktorius gali įterpti puslapiuose.
 */
function g5tech_allowed_core_blocks() {
	return array(
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/image',
		'core/table',
		'core/quote',
		'core/separator',
		'core/spacer',
		'core/group',
		'core/columns',
		'core/column',
		'core/buttons',
		'core/button',
	);
}

/**
 * Puslapiuose leidžiami tik saugūs branduolio blokai ir 5G TECH blokai.
 *
 * Visi registruoti g5tech/* blokai įtraukiami automatiškai, kad jau esamas
 * turinys niekada netaptų „nepalaikomu bloku".
 */
function g5tech_filter_allowed_block_types( $allowed_blocks, $context ) {
	if ( ! isset( $context->post ) || ! ( $context->post instanceof WP_Post ) ) {
		return $allowed_blocks;
	}

	if ( 'page' !== $context->post->post_type ) {
		return $allowed_blocks;
	}

	$ours = array();

	foreach ( array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ) as $block_name ) {
		if ( 0 === strpos( $block_name, 'g5tech/' ) ) {
			$ours[] = $block_name;
		}
	}

	return array_values( array_unique( array_merge( g5tech_allowed_core_blocks(), $ours ) ) );
}
add_filter( 'allowed_block_types_all', 'g5tech_filter_allowed_block_types', 10, 2 );

/**
 * Užrakintos struktūros atrakinti gali tik tas, kas gali keisti temą.
 *
 * Be šito redaktorius mygtuku „Modify" apeitų templateLock: contentOnly.
 */
function g5tech_restrict_block_locking( $settings ) {
	$settings['canLockBlocks'] = current_user_can( 'edit_theme_options' );

	return $settings;
}
add_filter( 'block_editor_settings_all', 'g5tech_restrict_block_locking' );
