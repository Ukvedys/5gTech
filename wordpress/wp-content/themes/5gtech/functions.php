<?php
/**
 * 5G TECH temos funkcijos.
 *
 * @package 5gtech
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_theme_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'editor-styles' );

	// Redaktorius gauna visus svetainės stilius, kad puslapis redagavimo
	// drobėje atrodytų taip pat, kaip viešoje svetainėje.
	$editor_styles = array(
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
		'assets/css/site.css',
		'assets/css/home/home.css',
		'assets/css/team/tokens.css',
		'assets/css/team/components.css',
		'assets/css/team/team.css',
		'assets/css/internal/shared.css',
	);

	add_editor_style( $editor_styles );
}
add_action( 'after_setup_theme', 'g5tech_theme_setup' );

function g5tech_editor_style_group( $post_id ) {
	if ( ! $post_id ) {
		return 'base';
	}

	$post_type = get_post_type( $post_id );
	$content   = (string) get_post_field( 'post_content', $post_id );

	if ( has_block( 'g5tech/homepage', $content ) ) {
		return 'home';
	}

	if (
		'g5_team' === $post_type
		|| has_block( 'g5tech/about-page', $content )
		|| has_block( 'g5tech/team-profile', $content )
	) {
		return 'team';
	}

	$internal_blocks = array(
		'g5tech/contact-page',
		'g5tech/privacy-page',
		'g5tech/cookies-page',
		'g5tech/application-page',
		'g5tech/career-page',
		'g5tech/job-page',
		'g5tech/news-page',
		'g5tech/news-article',
		'g5tech/academy-page',
		'g5tech/training-page',
		'g5tech/candidate-faq-page',
		'g5tech/leaders-page',
		'g5tech/project-managers-page',
	);

	foreach ( $internal_blocks as $block_name ) {
		if ( has_block( $block_name, $content ) ) {
			return 'internal';
		}
	}

	if ( in_array( $post_type, array( 'g5_job', 'post' ), true ) ) {
		return 'internal';
	}

	return 'base';
}

function g5tech_enqueue_assets() {
	$stylesheet_path = get_theme_file_path( 'assets/css/site.css' );

	// Puslapio LT atitikmens slug: vertimų kopijos (EN/DE) gauna tuos pačius
	// stilius kaip jų lietuviškas originalas.
	$g5_page_slug = '';

	if ( is_page() ) {
		$g5_page_id = get_queried_object_id();

		if ( function_exists( 'pll_get_post' ) ) {
			$g5_lt_id = pll_get_post( $g5_page_id, 'lt' );

			if ( $g5_lt_id ) {
				$g5_page_id = $g5_lt_id;
			}
		}

		$g5_page_slug = (string) get_post_field( 'post_name', $g5_page_id );
	}

	wp_enqueue_style(
		'g5tech-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'g5tech-site',
		get_theme_file_uri( 'assets/css/site.css' ),
		array( 'g5tech-fonts' ),
		file_exists( $stylesheet_path ) ? (string) filemtime( $stylesheet_path ) : wp_get_theme()->get( 'Version' )
	);

	if ( is_front_page() ) {
		$home_style_path = get_theme_file_path( 'assets/css/home/home.css' );
		$home_script_path = get_theme_file_path( 'assets/js/home.js' );

		wp_enqueue_style(
			'g5tech-home',
			get_theme_file_uri( 'assets/css/home/home.css' ),
			array( 'g5tech-site' ),
			file_exists( $home_style_path ) ? (string) filemtime( $home_style_path ) : wp_get_theme()->get( 'Version' )
		);
		wp_enqueue_script(
			'g5tech-home',
			get_theme_file_uri( 'assets/js/home.js' ),
			array(),
			file_exists( $home_script_path ) ? (string) filemtime( $home_script_path ) : wp_get_theme()->get( 'Version' ),
			true
		);
		wp_script_add_data( 'g5tech-home', 'strategy', 'defer' );
	} elseif ( 'apie-mus' === $g5_page_slug || is_singular( 'g5_team' ) ) {
		$team_styles = array(
			'g5tech-team-tokens'     => 'assets/css/team/tokens.css',
			'g5tech-team-components' => 'assets/css/team/components.css',
			'g5tech-team-page'       => 'assets/css/team/team.css',
		);
		$dependencies = array( 'g5tech-site' );

		foreach ( $team_styles as $handle => $relative_path ) {
			$absolute_path = get_theme_file_path( $relative_path );

			wp_enqueue_style(
				$handle,
				get_theme_file_uri( $relative_path ),
				$dependencies,
				file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : wp_get_theme()->get( 'Version' )
			);

			$dependencies = array( $handle );
		}
	} elseif (
		in_array( $g5_page_slug, array( 'karjera', 'naujienos', 'kontaktai', 'kandidatuoti', 'privatumo-politika', 'slapukai', 'akademija', 'mokymai', 'duk', 'vadovams', 'projektu-vadovams' ), true )
		|| is_singular( 'g5_job' )
		|| is_singular( 'post' )
	) {
		$internal_styles = array(
			'g5tech-internal-tokens'     => 'assets/css/team/tokens.css',
			'g5tech-internal-components' => 'assets/css/team/components.css',
			'g5tech-internal-page'       => 'assets/css/internal/shared.css',
		);
		$dependencies = array( 'g5tech-site' );

		foreach ( $internal_styles as $handle => $relative_path ) {
			$absolute_path = get_theme_file_path( $relative_path );

			wp_enqueue_style(
				$handle,
				get_theme_file_uri( $relative_path ),
				$dependencies,
				file_exists( $absolute_path ) ? (string) filemtime( $absolute_path ) : wp_get_theme()->get( 'Version' )
			);

			$dependencies = array( $handle );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'g5tech_enqueue_assets' );
