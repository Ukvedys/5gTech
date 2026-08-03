<?php
/**
 * Senų turinio ekranų nukreipimai į vieną redagavimo vietą.
 *
 * Po turinio perkėlimo į blokus (žr. migrations.php) puslapių tekstai gyvena
 * pačiuose puslapiuose (post_content), todėl seni atskiri ekranai nebekeičia
 * to, kas rodoma viešai. Kad neliktų dviejų tiesų, jų adresai nukreipiami į
 * atitinkamo puslapio blokų redaktorių.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Puslapio raktas → puslapio slug.
 */
function g5tech_page_key_slug_map() {
	return array(
		'home'             => 'pagrindinis',
		'services'         => 'paslaugos',
		'projects'         => 'projektai',
		'experience'       => 'patirtis',
		'about'            => 'apie-mus',
		'career'           => 'karjera',
		'academy'          => 'akademija',
		'training'         => 'mokymai',
		'candidate_faq'    => 'duk',
		'leaders'          => 'vadovams',
		'project_managers' => 'projektu-vadovams',
		'contact'          => 'kontaktai',
		'news'             => 'naujienos',
	);
}

/**
 * Puslapio blokų redaktoriaus adresas pagal slug.
 */
function g5tech_page_editor_url( $slug ) {
	$page = get_page_by_path( sanitize_title( $slug ) );

	if ( $page instanceof WP_Post ) {
		return admin_url( 'post.php?post=' . (int) $page->ID . '&action=edit' );
	}

	return admin_url( 'edit.php?post_type=page' );
}

/**
 * Seni ekranų adresai (nuorodos, žymės) nukreipiami į blokų redaktorių.
 */
function g5tech_redirect_legacy_content_screens() {
	$screen = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) );

	$legacy = array(
		'g5tech-page-modules',
		'g5tech-structured-content',
		'g5tech-training-content',
		'g5tech-career-content',
		'g5tech-about-order',
	);

	if ( ! in_array( $screen, $legacy, true ) ) {
		return;
	}

	$slug = '';

	switch ( $screen ) {
		case 'g5tech-page-modules':
			$page_key = sanitize_key( wp_unslash( $_GET['page_key'] ?? '' ) );
			$slug     = g5tech_page_key_slug_map()[ $page_key ] ?? '';
			break;

		case 'g5tech-structured-content':
			$section = sanitize_key( wp_unslash( $_GET['section'] ?? 'academy' ) );
			$map     = array(
				'academy'          => 'akademija',
				'leaders'          => 'vadovams',
				'project_managers' => 'projektu-vadovams',
				'home_audiences'   => 'pagrindinis',
			);
			$slug    = $map[ $section ] ?? 'akademija';
			break;

		case 'g5tech-training-content':
			$slug = 'mokymai';
			break;

		case 'g5tech-career-content':
			$slug = 'karjera';
			break;

		case 'g5tech-about-order':
			$slug = 'apie-mus';
			break;
	}

	wp_safe_redirect( $slug ? g5tech_page_editor_url( $slug ) : admin_url( 'edit.php?post_type=page' ) );
	exit;
}
// Svarbu: kablys turi suveikti PRIEŠ wp-admin/includes/menu.php prieigos
// tikrinimą (jis vyksta prieš admin_init), kitaip neregistruotas ekrano
// adresas baigiasi 403 dar iki nukreipimo.
add_action( 'admin_menu', 'g5tech_redirect_legacy_content_screens', 999 );
