<?php
/**
 * Esamų svetainės sekcijų prijungimas prie bendros modulių bibliotekos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_builtin_module_definitions() {
	return array(
		'home_intro' => array(
			'title' => 'Titulinis · Įvadas',
			'page'  => 'home',
			'source_label' => 'Bendri duomenys ir titulinis',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'home_services' => array(
			'title' => 'Titulinis · Paslaugos',
			'page'  => 'home',
			'source_label' => 'Paslaugos',
			'source_url'   => admin_url( 'edit.php?post_type=g5_service' ),
		),
		'home_standards' => array(
			'title' => 'Titulinis · Darbo standartas',
			'page'  => 'home',
			'source_label' => 'Bendri duomenys ir titulinis',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'home_process' => array(
			'title' => 'Titulinis · Kaip dirbame',
			'page'  => 'home',
			'source_label' => 'Bendri duomenys ir titulinis',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'home_experience' => array(
			'title' => 'Titulinis · Patirtis ir geografija',
			'page'  => 'home',
			'source_label' => 'Bendri duomenys ir projektai',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'home_equipment' => array(
			'title' => 'Titulinis · Įrangos gamintojai',
			'page'  => 'home',
			'source_label' => 'Partneriai ir įranga',
			'source_url'   => admin_url( 'edit.php?post_type=g5_partner' ),
		),
		'home_team' => array(
			'title' => 'Titulinis · Komanda',
			'page'  => 'home',
			'source_label' => 'Komanda',
			'source_url'   => admin_url( 'edit.php?post_type=g5_team' ),
		),
		'home_audiences' => array(
			'title' => 'Titulinis · Karjeros kryptys',
			'page'  => 'home',
			'source_label' => 'Titulinio auditorijos',
			'source_url'   => g5tech_structured_content_admin_url( 'home_audiences' ),
		),
		'home_news' => array(
			'title' => 'Titulinis · Naujienos',
			'page'  => 'home',
			'source_label' => 'Naujienos',
			'source_url'   => admin_url( 'edit.php' ),
		),
		'services_list' => array(
			'title' => 'Paslaugos · Paslaugų tinklelis',
			'page'  => 'services',
			'source_label' => 'Paslaugos',
			'source_url'   => admin_url( 'edit.php?post_type=g5_service' ),
		),
		'services_standard' => array(
			'title' => 'Paslaugos · Darbo standartas',
			'page'  => 'services',
			'source_label' => 'Bendri duomenys',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'projects_list' => array(
			'title' => 'Projektai · Projektų tinklelis',
			'page'  => 'projects',
			'source_label' => 'Projektai',
			'source_url'   => admin_url( 'edit.php?post_type=g5_project' ),
		),
		'experience_numbers' => array(
			'title' => 'Patirtis · Pagrindiniai skaičiai',
			'page'  => 'experience',
			'source_label' => 'Bendri duomenys',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'experience_geography' => array(
			'title' => 'Patirtis · Geografija',
			'page'  => 'experience',
			'source_label' => 'Projektai',
			'source_url'   => admin_url( 'edit.php?post_type=g5_project' ),
		),
		'experience_projects' => array(
			'title' => 'Patirtis · Projektai',
			'page'  => 'experience',
			'source_label' => 'Projektai',
			'source_url'   => admin_url( 'edit.php?post_type=g5_project' ),
		),
		'experience_partners' => array(
			'title' => 'Patirtis · Operatoriai ir gamintojai',
			'page'  => 'experience',
			'source_label' => 'Partneriai ir įranga',
			'source_url'   => admin_url( 'edit.php?post_type=g5_partner' ),
		),
		'experience_certifications' => array(
			'title' => 'Patirtis · Sertifikatai',
			'page'  => 'experience',
			'source_label' => 'Bendri duomenys',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'about_story' => array(
			'title' => 'Apie mus · Kas esame',
			'page'  => 'about',
			'source_label' => 'Apie mus turinys',
			'source_url'   => g5tech_about_admin_url( 'content', 'story' ),
		),
		'about_purpose' => array(
			'title' => 'Apie mus · Misija ir vizija',
			'page'  => 'about',
			'source_label' => 'Apie mus turinys',
			'source_url'   => g5tech_about_admin_url( 'content', 'purpose' ),
		),
		'about_values' => array(
			'title' => 'Apie mus · Vertybės',
			'page'  => 'about',
			'source_label' => 'Apie mus turinys',
			'source_url'   => g5tech_about_admin_url( 'content', 'values' ),
		),
		'about_team' => array(
			'title' => 'Apie mus · Pagrindinė komanda',
			'page'  => 'about',
			'source_label' => 'Komanda',
			'source_url'   => admin_url( 'edit.php?post_type=g5_team' ),
		),
		'about_strategy' => array(
			'title' => 'Apie mus · Kokybiškas ir tvarus augimas',
			'page'  => 'about',
			'source_label' => 'Apie mus turinys',
			'source_url'   => g5tech_about_admin_url( 'content', 'strategy' ),
		),
		'about_competence' => array(
			'title' => 'Apie mus · Kompetencijos',
			'page'  => 'about',
			'source_label' => 'Apie mus turinys',
			'source_url'   => g5tech_about_admin_url( 'content', 'competence' ),
		),
		'career_benefits' => array(
			'title' => 'Karjera · Kodėl verta rinktis',
			'page'  => 'career',
			'source_label' => 'Karjeros turinys',
			'source_url'   => g5tech_career_content_admin_url( 'benefits' ),
		),
		'career_positions' => array(
			'title' => 'Karjera · Atviros pozicijos',
			'page'  => 'career',
			'source_label' => 'Darbo skelbimai',
			'source_url'   => admin_url( 'edit.php?post_type=g5_job' ),
		),
		'career_selection' => array(
			'title' => 'Karjera · Atrankos eiga',
			'page'  => 'career',
			'source_label' => 'Karjeros turinys',
			'source_url'   => g5tech_career_content_admin_url( 'selection' ),
		),
		'career_growth' => array(
			'title' => 'Karjera · Augimas',
			'page'  => 'career',
			'source_label' => 'Karjeros turinys',
			'source_url'   => g5tech_career_content_admin_url( 'growth' ),
		),
		'academy_program' => array(
			'title' => 'Academy · Programa',
			'page'  => 'academy',
			'source_label' => 'Academy puslapis',
			'source_url'   => g5tech_structured_content_admin_url( 'academy' ),
		),
		'academy_included' => array(
			'title' => 'Academy · Kas įtraukta',
			'page'  => 'academy',
			'source_label' => 'Academy puslapis',
			'source_url'   => g5tech_structured_content_admin_url( 'academy' ),
		),
		'training_equipment' => array(
			'title' => 'Mokymai · Naudojama įranga',
			'page'  => 'training',
			'source_label' => 'Mokymų puslapis',
			'source_url'   => admin_url( 'admin.php?page=g5tech-training-content' ),
		),
		'training_room' => array(
			'title' => 'Mokymai · Mokymų aplinka',
			'page'  => 'training',
			'source_label' => 'Mokymų puslapis',
			'source_url'   => admin_url( 'admin.php?page=g5tech-training-content' ),
		),
		'candidate_faq_groups' => array(
			'title' => 'DUK kandidatams · Klausimų grupės',
			'page'  => 'candidate_faq',
			'source_label' => 'Dažniausi klausimai',
			'source_url'   => admin_url( 'edit.php?post_type=g5_faq' ),
		),
		'leaders_reasons' => array(
			'title' => 'Vadovams · Kodėl 5G TECH',
			'page'  => 'leaders',
			'source_label' => 'Vadovų puslapis',
			'source_url'   => g5tech_structured_content_admin_url( 'leaders' ),
		),
		'project_managers_scope' => array(
			'title' => 'Projektų vadovams · Techninė apimtis',
			'page'  => 'project_managers',
			'source_label' => 'Projektų vadovų puslapis',
			'source_url'   => g5tech_structured_content_admin_url( 'project_managers' ),
		),
		'project_managers_equipment' => array(
			'title' => 'Projektų vadovams · Įranga',
			'page'  => 'project_managers',
			'source_label' => 'Partneriai ir įranga',
			'source_url'   => g5tech_structured_content_admin_url( 'project_managers' ),
		),
		'contact_form' => array(
			'title' => 'Kontaktai · Projekto užklausa',
			'page'  => 'contact',
			'source_label' => 'Bendri kontaktai',
			'source_url'   => admin_url( 'admin.php?page=g5tech-settings' ),
		),
		'contact_people' => array(
			'title' => 'Kontaktai · Tiesioginiai kontaktai',
			'page'  => 'contact',
			'source_label' => 'Komanda',
			'source_url'   => admin_url( 'edit.php?post_type=g5_team' ),
		),
		'news_list' => array(
			'title' => 'Naujienos · Naujienų tinklelis',
			'page'  => 'news',
			'source_label' => 'Naujienos',
			'source_url'   => admin_url( 'edit.php' ),
		),
	);
}

function g5tech_builtin_page_layouts() {
	return array(
		'home' => array(
			'legacy_callback' => 'g5tech_render_homepage_legacy',
			'container_xpath' => './/*[contains(concat(" ", normalize-space(@class), " "), " home-sections ")]',
			'anchor_xpath'    => '',
		),
		'services' => array(
			'legacy_callback' => 'g5tech_render_services_grid_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'projects' => array(
			'legacy_callback' => 'g5tech_render_projects_archive_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'experience' => array(
			'legacy_callback' => 'g5tech_render_experience_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'about' => array(
			'legacy_callback' => 'g5tech_render_about_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'career' => array(
			'legacy_callback' => 'g5tech_render_career_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => '',
		),
		'academy' => array(
			'legacy_callback' => 'g5tech_render_academy_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'training' => array(
			'legacy_callback' => 'g5tech_render_training_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'candidate_faq' => array(
			'legacy_callback' => 'g5tech_render_candidate_faq_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'leaders' => array(
			'legacy_callback' => 'g5tech_render_leaders_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'project_managers' => array(
			'legacy_callback' => 'g5tech_render_project_managers_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => './/*[@data-g5-page-anchor="cta"]',
		),
		'contact' => array(
			'legacy_callback' => 'g5tech_render_contact_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => '',
		),
		'news' => array(
			'legacy_callback' => 'g5tech_render_news_page_legacy',
			'container_xpath' => '.',
			'anchor_xpath'    => '',
		),
	);
}

function g5tech_get_builtin_module_definition( $dynamic_key ) {
	$definitions = g5tech_builtin_module_definitions();

	return $definitions[ sanitize_key( $dynamic_key ) ] ?? null;
}

function g5tech_get_builtin_module( $dynamic_key, $statuses = array( 'publish', 'draft', 'pending', 'private', 'future' ) ) {
	$modules = get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => $statuses,
			'posts_per_page' => 1,
			'meta_key'       => 'g5_module_dynamic_key',
			'meta_value'     => sanitize_key( $dynamic_key ),
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	return $modules ? $modules[0] : null;
}

function g5tech_dom_load_fragment( $html, $root_id ) {
	$document = new DOMDocument( '1.0', 'UTF-8' );
	$previous = libxml_use_internal_errors( true );
	$document->loadHTML(
		'<?xml encoding="utf-8" ?><!doctype html><html><body><div id="' . esc_attr( $root_id ) . '">' . $html . '</div></body></html>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	return $document;
}

function g5tech_dom_inner_html( DOMNode $node ) {
	$html = '';

	foreach ( $node->childNodes as $child ) {
		$html .= $node->ownerDocument->saveHTML( $child );
	}

	return $html;
}

function g5tech_get_legacy_page_html( $page_key ) {
	static $cache = array();

	if ( isset( $cache[ $page_key ] ) ) {
		return $cache[ $page_key ];
	}

	$layouts = g5tech_builtin_page_layouts();
	$layout  = $layouts[ $page_key ] ?? null;

	if ( ! $layout || ! is_callable( $layout['legacy_callback'] ) ) {
		return '';
	}

	$previous = $GLOBALS['g5tech_rendering_legacy_page'] ?? false;
	$GLOBALS['g5tech_rendering_legacy_page'] = true;
	$html = (string) call_user_func( $layout['legacy_callback'] );
	$GLOBALS['g5tech_rendering_legacy_page'] = $previous;
	$cache[ $page_key ] = $html;

	return $html;
}

function g5tech_render_dynamic_content_module( $module ) {
	$module = is_numeric( $module ) ? get_post( absint( $module ) ) : $module;

	if ( ! $module instanceof WP_Post || 'publish' !== $module->post_status ) {
		return '';
	}

	$dynamic_key = get_post_meta( $module->ID, 'g5_module_dynamic_key', true );
	$definition  = g5tech_get_builtin_module_definition( $dynamic_key );

	if ( ! $definition ) {
		return '';
	}

	$legacy_html = g5tech_get_legacy_page_html( $definition['page'] );

	if ( ! $legacy_html ) {
		return '';
	}

	$document = g5tech_dom_load_fragment( $legacy_html, 'g5-dynamic-source' );
	$xpath    = new DOMXPath( $document );
	$nodes    = $xpath->query( '//*[@data-g5-core-module="' . $dynamic_key . '"]' );
	$output   = '';

	foreach ( $nodes as $node ) {
		if ( $node instanceof DOMElement && $node->hasAttribute( 'style' ) && false !== strpos( $node->getAttribute( 'style' ), 'order:' ) ) {
			$node->removeAttribute( 'style' );
		}

		if ( $node instanceof DOMElement ) {
			$node->setAttribute( 'data-content-module', (string) $module->ID );
		}

		$output .= $document->saveHTML( $node );
	}

	return $output;
}

function g5tech_append_module_html( DOMDocument $document, DOMNode $container, ?DOMNode $anchor, $html ) {
	if ( ! $html ) {
		return;
	}

	$fragment_document = g5tech_dom_load_fragment( $html, 'g5-module-fragment' );
	$fragment_root     = $fragment_document->getElementById( 'g5-module-fragment' );
	$nodes             = array();

	foreach ( $fragment_root->childNodes as $child ) {
		$nodes[] = $child;
	}

	foreach ( $nodes as $node ) {
		$imported = $document->importNode( $node, true );

		if ( $anchor && $anchor->parentNode === $container ) {
			$container->insertBefore( $imported, $anchor );
		} else {
			$container->appendChild( $imported );
		}
	}
}

function g5tech_compose_modular_page( $page_key, $legacy_html = '' ) {
	if ( ! get_option( 'g5tech_builtin_modules_migrated_1' ) ) {
		return $legacy_html ?: g5tech_get_legacy_page_html( $page_key );
	}

	$layouts = g5tech_builtin_page_layouts();
	$layout  = $layouts[ $page_key ] ?? null;

	if ( ! $layout ) {
		return $legacy_html;
	}

	$legacy_html = $legacy_html ?: g5tech_get_legacy_page_html( $page_key );
	$document    = g5tech_dom_load_fragment( $legacy_html, 'g5-modular-root' );
	$xpath       = new DOMXPath( $document );
	$root        = $document->getElementById( 'g5-modular-root' );
	$container   = '.' === $layout['container_xpath']
		? $root
		: $xpath->query( $layout['container_xpath'], $root )->item( 0 );

	if ( ! $container ) {
		return $legacy_html;
	}

	$removable = array();

	foreach ( $xpath->query( './/*[@data-g5-core-module]', $root ) as $node ) {
		$removable[ spl_object_id( $node ) ] = $node;
	}

	foreach ( $xpath->query( './/*[@data-content-module]', $root ) as $node ) {
		$removable[ spl_object_id( $node ) ] = $node;
	}

	foreach ( $removable as $node ) {
		if ( $node->parentNode ) {
			$node->parentNode->removeChild( $node );
		}
	}

	$anchor = $layout['anchor_xpath']
		? $xpath->query( $layout['anchor_xpath'], $container )->item( 0 )
		: null;

	foreach ( g5tech_get_page_modules( $page_key ) as $module ) {
		g5tech_append_module_html(
			$document,
			$container,
			$anchor,
			g5tech_render_content_module( $module )
		);
	}

	return g5tech_dom_inner_html( $root );
}

function g5tech_seed_builtin_modules() {
	if ( get_option( 'g5tech_builtin_modules_migrated_1' ) ) {
		return;
	}

	$definitions = g5tech_builtin_module_definitions();
	$page_modules = array_fill_keys( array_keys( g5tech_module_page_choices() ), array() );

	foreach ( $definitions as $dynamic_key => $definition ) {
		$module = g5tech_get_builtin_module( $dynamic_key );

		if ( ! $module ) {
			$module_id = wp_insert_post(
				array(
					'post_type'   => 'g5_module',
					'post_status' => 'publish',
					'post_title'  => $definition['title'],
				),
				true
			);

			if ( is_wp_error( $module_id ) ) {
				continue;
			}

			update_post_meta( $module_id, 'g5_module_type', 'dynamic' );
			update_post_meta( $module_id, 'g5_module_heading', $definition['title'] );
			update_post_meta( $module_id, 'g5_module_dynamic_key', $dynamic_key );
			$module = get_post( $module_id );
		}

		$page_modules[ $definition['page'] ][] = (int) $module->ID;
	}

	$current = g5tech_module_placements();

	foreach ( $page_modules as $page_key => $builtin_ids ) {
		$existing_ids = array_values(
			array_filter(
				$current[ $page_key ] ?? array(),
				static function( $module_id ) {
					return 'dynamic' !== get_post_meta( $module_id, 'g5_module_type', true );
				}
			)
		);

		if ( 'training' === $page_key ) {
			$topics_module = g5tech_get_source_module( 'training_topics' );
			$topics_ids    = $topics_module ? array( (int) $topics_module->ID ) : array();
			$existing_ids  = array_values( array_diff( $existing_ids, $topics_ids ) );
			$current[ $page_key ] = array_merge( $topics_ids, $builtin_ids, $existing_ids );
		} else {
			$current[ $page_key ] = array_merge( $builtin_ids, $existing_ids );
		}
	}

	update_option( 'g5tech_module_placements', $current, false );
	update_option( 'g5tech_builtin_modules_migrated_1', 1, false );
}
add_action( 'admin_init', 'g5tech_seed_builtin_modules', 30 );
