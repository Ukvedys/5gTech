<?php
/**
 * Bazinis SEO, socialinių tinklų informacija ir saugūs senų URL nukreipimai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Neleidžia dubliuoti žymų, jei ateityje bus įjungtas atskiras SEO papildinys.
 */
function g5tech_has_external_seo_plugin() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'AIOSEO_VERSION' );
}

function g5tech_seo_clean_description( $value ) {
	$value = preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $value ) );
	$value = trim( (string) $value );

	return wp_html_excerpt( $value, 158, '…' );
}

function g5tech_seo_title() {
	return html_entity_decode( wp_get_document_title(), ENT_QUOTES | ENT_HTML5, get_bloginfo( 'charset' ) ?: 'UTF-8' );
}

function g5tech_seo_description() {
	if ( is_front_page() ) {
		return g5tech_seo_clean_description( g5tech_setting( 'home_hero_lead' ) );
	}

	if ( is_post_type_archive( 'g5_service' ) ) {
		return 'Telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektavimo, įrengimo ir priežiūros paslaugos.';
	}

	if ( is_post_type_archive( 'g5_project' ) ) {
		return '5G TECH įgyvendinti telekomunikacijų ir inžinerinės infrastruktūros projektai Lietuvoje ir Europoje.';
	}

	if ( is_singular() ) {
		$post_id   = get_queried_object_id();
		$post_type = get_post_type( $post_id );
		$meta_keys = array(
			'g5_service' => 'g5_service_summary',
			'g5_project' => 'g5_project_summary',
			'g5_team'    => 'g5_team_summary',
			'g5_job'     => 'g5_job_summary',
		);

		if ( isset( $meta_keys[ $post_type ] ) ) {
			$description = get_post_meta( $post_id, $meta_keys[ $post_type ], true );

			if ( $description ) {
				return g5tech_seo_clean_description( $description );
			}
		}

		$excerpt = get_the_excerpt( $post_id );

		if ( $excerpt ) {
			return g5tech_seo_clean_description( $excerpt );
		}
	}

	return 'Telekomunikacijų, energetikos ir inžinerinės infrastruktūros projektai.';
}

function g5tech_seo_url() {
	if ( is_singular() ) {
		return get_permalink( get_queried_object_id() );
	}

	if ( is_front_page() ) {
		return home_url( '/' );
	}

	if ( is_post_type_archive() ) {
		return get_post_type_archive_link( get_query_var( 'post_type' ) );
	}

	return '';
}

function g5tech_seo_image() {
	$post_id = is_singular() || is_front_page() ? get_queried_object_id() : 0;

	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$image = wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'full' );

		if ( $image ) {
			return $image;
		}
	}

	$front_page_id = (int) get_option( 'page_on_front' );

	if ( $front_page_id && has_post_thumbnail( $front_page_id ) ) {
		$image = wp_get_attachment_image_url( get_post_thumbnail_id( $front_page_id ), 'full' );

		if ( $image ) {
			return $image;
		}
	}

	$site_icon = get_site_icon_url( 512 );

	if ( $site_icon ) {
		return $site_icon;
	}

	return get_theme_file_uri( 'assets/images/5gtech-logo-white.png' );
}

function g5tech_organization_logo() {
	$site_icon = get_site_icon_url( 512 );

	return $site_icon ?: get_theme_file_uri( 'assets/images/5gtech-logo-white.png' );
}

function g5tech_render_social_meta() {
	if ( g5tech_has_external_seo_plugin() || is_admin() || is_search() || is_404() ) {
		return;
	}

	$title       = g5tech_seo_title();
	$description = g5tech_seo_description();
	$url         = g5tech_seo_url();
	$image       = g5tech_seo_image();
	$type        = is_singular( 'post' ) ? 'article' : 'website';

	if ( ! $description || ! $url ) {
		return;
	}

	printf( "\n<meta name=\"description\" content=\"%s\">\n", esc_attr( $description ) );
	printf( "<meta property=\"og:locale\" content=\"lt_LT\">\n" );
	printf( "<meta property=\"og:type\" content=\"%s\">\n", esc_attr( $type ) );
	printf( "<meta property=\"og:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta property=\"og:description\" content=\"%s\">\n", esc_attr( $description ) );
	printf( "<meta property=\"og:url\" content=\"%s\">\n", esc_url( $url ) );
	printf( "<meta property=\"og:site_name\" content=\"%s\">\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( "<meta name=\"twitter:card\" content=\"summary_large_image\">\n" );
	printf( "<meta name=\"twitter:title\" content=\"%s\">\n", esc_attr( $title ) );
	printf( "<meta name=\"twitter:description\" content=\"%s\">\n", esc_attr( $description ) );

	if ( $image ) {
		printf( "<meta property=\"og:image\" content=\"%s\">\n", esc_url( $image ) );
		printf( "<meta name=\"twitter:image\" content=\"%s\">\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'g5tech_render_social_meta', 4 );

function g5tech_render_archive_canonical() {
	if ( g5tech_has_external_seo_plugin() || ! is_post_type_archive() ) {
		return;
	}

	$url = get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );

	if ( $url ) {
		printf( "<link rel=\"canonical\" href=\"%s\">\n", esc_url( $url ) );
	}
}
add_action( 'wp_head', 'g5tech_render_archive_canonical', 10 );

function g5tech_schema_compact( $values ) {
	return array_filter(
		$values,
		static function ( $value ) {
			return '' !== $value && null !== $value && array() !== $value;
		}
	);
}

function g5tech_render_schema() {
	if ( g5tech_has_external_seo_plugin() || is_admin() || is_search() || is_404() ) {
		return;
	}

	$site_url       = home_url( '/' );
	$organization_id = trailingslashit( $site_url ) . '#organization';
	$page_url       = g5tech_seo_url();

	if ( ! $page_url ) {
		return;
	}

	$graph = array(
		g5tech_schema_compact(
			array(
				'@type'   => 'Organization',
				'@id'     => $organization_id,
				'name'    => get_bloginfo( 'name' ),
				'url'     => $site_url,
				'logo'    => g5tech_organization_logo(),
				'email'   => g5tech_setting( 'email' ),
				'telephone' => g5tech_setting( 'phone' ),
			)
		),
		array(
			'@type'       => 'WebSite',
			'@id'         => trailingslashit( $site_url ) . '#website',
			'url'         => $site_url,
			'name'        => get_bloginfo( 'name' ),
			'publisher'   => array( '@id' => $organization_id ),
			'inLanguage'  => 'lt-LT',
		),
		array(
			'@type'       => 'WebPage',
			'@id'         => trailingslashit( $page_url ) . '#webpage',
			'url'         => $page_url,
			'name'        => g5tech_seo_title(),
			'description' => g5tech_seo_description(),
			'isPartOf'    => array( '@id' => trailingslashit( $site_url ) . '#website' ),
			'inLanguage'  => 'lt-LT',
		),
	);

	if ( is_singular() ) {
		$post_id     = get_queried_object_id();
		$post_type   = get_post_type( $post_id );
		$description = g5tech_seo_description();
		$image       = has_post_thumbnail( $post_id )
			? wp_get_attachment_image_url( get_post_thumbnail_id( $post_id ), 'full' )
			: '';

		if ( 'g5_service' === $post_type ) {
			$graph[] = g5tech_schema_compact(
				array(
					'@type'       => 'Service',
					'@id'         => trailingslashit( $page_url ) . '#service',
					'name'        => get_the_title( $post_id ),
					'description' => $description,
					'url'         => $page_url,
					'image'       => $image,
					'provider'    => array( '@id' => $organization_id ),
				)
			);
		} elseif ( 'g5_project' === $post_type ) {
			$graph[] = g5tech_schema_compact(
				array(
					'@type'       => 'CreativeWork',
					'@id'         => trailingslashit( $page_url ) . '#project',
					'name'        => get_the_title( $post_id ),
					'description' => $description,
					'url'         => $page_url,
					'image'       => $image,
					'creator'     => array( '@id' => $organization_id ),
				)
			);
		} elseif ( 'post' === $post_type ) {
			$graph[] = g5tech_schema_compact(
				array(
					'@type'         => 'Article',
					'@id'           => trailingslashit( $page_url ) . '#article',
					'headline'      => get_the_title( $post_id ),
					'description'   => $description,
					'url'           => $page_url,
					'image'         => $image,
					'datePublished' => get_the_date( DATE_W3C, $post_id ),
					'dateModified'  => get_the_modified_date( DATE_W3C, $post_id ),
					'publisher'     => array( '@id' => $organization_id ),
				)
			);
		}
	}

	echo "\n<script type=\"application/ld+json\">";
	echo wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
	);
	echo "</script>\n";
}
add_action( 'wp_head', 'g5tech_render_schema', 30 );

/**
 * Asmeniniai WordPress autorių archyvai šiame įmonės puslapyje nereikalingi.
 */
function g5tech_disable_user_sitemap( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'g5tech_disable_user_sitemap', 10, 2 );

function g5tech_sitemap_excluded_posts( $post_type ) {
	$post_ids = get_posts(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	return array_values(
		array_filter(
			$post_ids,
			static function ( $post_id ) use ( $post_type ) {
				if ( 'g5_project' === $post_type ) {
					return ! g5tech_project_is_visible( $post_id );
				}

				if ( 'g5_job' === $post_type ) {
					return ! g5tech_job_is_active( $post_id );
				}

				if ( 'g5_team' === $post_type ) {
					return ! (bool) get_post_meta( $post_id, 'g5_team_show_profile', true );
				}

				return false;
			}
		)
	);
}

function g5tech_filter_sitemap_posts( $args, $post_type ) {
	if ( ! in_array( $post_type, array( 'g5_project', 'g5_job', 'g5_team' ), true ) ) {
		return $args;
	}

	$excluded = g5tech_sitemap_excluded_posts( $post_type );

	if ( $excluded ) {
		$args['post__not_in'] = array_values(
			array_unique(
				array_merge( $args['post__not_in'] ?? array(), $excluded )
			)
		);
	}

	return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'g5tech_filter_sitemap_posts', 10, 2 );

function g5tech_filter_search_results( $posts, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return $posts;
	}

	return array_values(
		array_filter(
			$posts,
			static function ( $post ) {
				if ( 'g5_project' === $post->post_type ) {
					return g5tech_project_is_visible( $post->ID );
				}

				if ( 'g5_job' === $post->post_type ) {
					return g5tech_job_is_active( $post->ID );
				}

				if ( 'g5_team' === $post->post_type ) {
					return (bool) get_post_meta( $post->ID, 'g5_team_show_profile', true );
				}

				return true;
			}
		)
	);
}
add_filter( 'the_posts', 'g5tech_filter_search_results', 10, 2 );

function g5tech_search_excerpt( $excerpt, $post ) {
	if ( $excerpt || ! $post instanceof WP_Post ) {
		return $excerpt;
	}

	$meta_keys = array(
		'g5_service' => 'g5_service_summary',
		'g5_project' => 'g5_project_summary',
		'g5_team'    => 'g5_team_summary',
		'g5_job'     => 'g5_job_summary',
	);

	if ( isset( $meta_keys[ $post->post_type ] ) ) {
		return g5tech_seo_clean_description( get_post_meta( $post->ID, $meta_keys[ $post->post_type ], true ) );
	}

	return $excerpt;
}
add_filter( 'get_the_excerpt', 'g5tech_search_excerpt', 10, 2 );

function g5tech_search_heading( $block_content, $block ) {
	if ( ! is_search() || 'core/query-title' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}

	return sprintf(
		'<h1 class="g5-display-lg wp-block-query-title">Paieškos rezultatai: „%s“</h1>',
		esc_html( get_search_query() )
	);
}
add_filter( 'render_block_core/query-title', 'g5tech_search_heading', 10, 2 );

function g5tech_noindex_404( $robots ) {
	if ( is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'] );
	}

	return $robots;
}
add_filter( 'wp_robots', 'g5tech_noindex_404' );

/**
 * Patvirtinti senos svetainės adresai, kurie naujoje struktūroje pasikeitė.
 */
function g5tech_redirect_legacy_urls() {
	if ( is_admin() || wp_doing_ajax() ) {
		return;
	}

	$path = wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), PHP_URL_PATH );
	$path = '/' . trim( (string) $path, '/' ) . '/';
	$map  = array(
		'/5gtech-telecom-academy/' => '/akademija/',
		'/rinkis-mus/'              => '/patirtis/',
		'/karjeros-forma/'          => '/kandidatuoti/',
	);

	if ( isset( $map[ $path ] ) ) {
		wp_safe_redirect( home_url( $map[ $path ] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'g5tech_redirect_legacy_urls', 1 );
