<?php
/**
 * Projektų turinio modelis, sąrašas ir vieno projekto puslapis.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_project_type() {
	register_post_type(
		'g5_project',
		array(
			'labels' => array(
				'name'               => 'Projektai',
				'singular_name'      => 'Projektas',
				'add_new'            => 'Pridėti projektą',
				'add_new_item'       => 'Pridėti naują projektą',
				'edit_item'          => 'Redaguoti projektą',
				'new_item'           => 'Naujas projektas',
				'view_item'          => 'Peržiūrėti projektą',
				'search_items'       => 'Ieškoti projektų',
				'not_found'          => 'Projektų nerasta',
				'not_found_in_trash' => 'Šiukšlinėje projektų nėra',
				'menu_name'          => 'Projektai',
			),
			'public'        => true,
			'has_archive'   => 'projektai',
			'rewrite'       => array(
				'slug'       => 'projektai',
				'with_front' => false,
			),
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-portfolio',
			'menu_position' => 22,
			'supports'      => array( 'title', 'thumbnail', 'revisions', 'page-attributes' ),
		)
	);

	$taxonomy_args = array(
		'public'            => false,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => false,
		'hierarchical'      => true,
		'rewrite'           => false,
	);

	register_taxonomy(
		'g5_project_country',
		array( 'g5_project' ),
		array_merge(
			$taxonomy_args,
			array(
				'labels' => array(
					'name'          => 'Šalys',
					'singular_name' => 'Šalis',
					'search_items'  => 'Ieškoti šalių',
					'all_items'     => 'Visos šalys',
					'edit_item'     => 'Redaguoti šalį',
					'update_item'   => 'Atnaujinti šalį',
					'add_new_item'  => 'Pridėti šalį',
					'new_item_name' => 'Šalies pavadinimas',
					'menu_name'     => 'Šalys',
				),
			)
		)
	);

	register_taxonomy(
		'g5_project_technology',
		array( 'g5_project' ),
		array_merge(
			$taxonomy_args,
			array(
				'labels' => array(
					'name'          => 'Technologijos',
					'singular_name' => 'Technologija',
					'search_items'  => 'Ieškoti technologijų',
					'all_items'     => 'Visos technologijos',
					'edit_item'     => 'Redaguoti technologiją',
					'update_item'   => 'Atnaujinti technologiją',
					'add_new_item'  => 'Pridėti technologiją',
					'new_item_name' => 'Technologijos pavadinimas',
					'menu_name'     => 'Technologijos',
				),
			)
		)
	);
}
add_action( 'init', 'g5tech_register_project_type' );

function g5tech_project_fields() {
	return array(
		'g5_project_summary' => array(
			'label'       => 'Trumpas projekto aprašymas',
			'type'        => 'textarea',
			'description' => 'Vienas arba du sakiniai projekto kortelei ir puslapio pradžiai.',
		),
		'g5_project_year' => array(
			'label'       => 'Metai',
			'type'        => 'text',
			'description' => 'Pvz. 2025. Lauką galima palikti tuščią.',
		),
		'g5_project_location' => array(
			'label'       => 'Vieta',
			'type'        => 'text',
			'description' => 'Miestas arba regionas, jei šią informaciją galima viešinti.',
		),
		'g5_project_scope' => array(
			'label'       => 'Atlikti darbai',
			'type'        => 'textarea',
			'description' => 'Vienas darbas vienoje eilutėje.',
		),
		'g5_project_result' => array(
			'label'       => 'Rezultatas',
			'type'        => 'textarea',
			'description' => 'Trumpas, viešai skelbiamas projekto rezultatas.',
		),
	);
}

function g5tech_register_project_meta() {
	foreach ( g5tech_project_fields() as $key => $field ) {
		register_post_meta(
			'g5_project',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => false,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'g5_project',
		'g5_project_service',
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'g5_project',
		'g5_project_visible',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'default'           => true,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'g5tech_register_project_meta' );

function g5tech_add_project_meta_box() {
	add_meta_box(
		'g5tech-project-details',
		'Projekto informacija',
		'g5tech_render_project_meta_box',
		'g5_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_project_meta_box' );

function g5tech_render_project_meta_box( $post ) {
	$visible = metadata_exists( 'post', $post->ID, 'g5_project_visible' )
		? (bool) get_post_meta( $post->ID, 'g5_project_visible', true )
		: true;
	$service_id = absint( get_post_meta( $post->ID, 'g5_project_service', true ) );
	$services   = get_posts(
		array(
			'post_type'      => 'g5_service',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	wp_nonce_field( 'g5tech_save_project', 'g5tech_project_nonce' );
	?>
	<p class="description">Pavadinimas įrašomas puslapio viršuje, o projekto vaizdas – skiltyje „Specialusis paveikslėlis“.</p>
	<?php foreach ( g5tech_project_fields() as $key => $field ) : ?>
		<?php $value = get_post_meta( $post->ID, $key, true ); ?>
		<div style="margin:0 0 20px;">
			<label for="<?php echo esc_attr( $key ); ?>" style="display:block;font-weight:600;margin-bottom:6px;"><?php echo esc_html( $field['label'] ); ?></label>
			<?php if ( 'textarea' === $field['type'] ) : ?>
				<textarea class="large-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" rows="<?php echo 'g5_project_scope' === $key ? '7' : '4'; ?>"><?php echo esc_textarea( $value ); ?></textarea>
			<?php else : ?>
				<input class="regular-text" type="text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php endif; ?>
			<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
		</div>
	<?php endforeach; ?>
	<div style="margin:0 0 20px;">
		<label for="g5_project_service" style="display:block;font-weight:600;margin-bottom:6px;">Susijusi paslauga</label>
		<select id="g5_project_service" name="g5_project_service">
			<option value="0">Nepasirinkta</option>
			<?php foreach ( $services as $service ) : ?>
				<option value="<?php echo esc_attr( $service->ID ); ?>" <?php selected( $service_id, $service->ID ); ?>><?php echo esc_html( get_the_title( $service ) ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<p>
		<label>
			<input type="checkbox" name="g5_project_visible" value="1" <?php checked( $visible ); ?>>
			<strong>Rodyti projektą viešoje svetainėje</strong>
		</label>
	</p>
	<?php
}

function g5tech_save_project_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_project_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_project_nonce'] ) ),
			'g5tech_save_project'
		)
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( g5tech_project_fields() as $key => $field ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';

		update_post_meta(
			$post_id,
			$key,
			'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value )
		);
	}

	$service_id = isset( $_POST['g5_project_service'] ) ? absint( $_POST['g5_project_service'] ) : 0;

	if ( $service_id && 'g5_service' !== get_post_type( $service_id ) ) {
		$service_id = 0;
	}

	update_post_meta( $post_id, 'g5_project_service', $service_id );
	update_post_meta( $post_id, 'g5_project_visible', isset( $_POST['g5_project_visible'] ) ? '1' : '0' );
}
add_action( 'save_post_g5_project', 'g5tech_save_project_meta' );

function g5tech_project_title_placeholder( $placeholder, $post ) {
	return 'g5_project' === $post->post_type ? 'Projekto pavadinimas' : $placeholder;
}
add_filter( 'enter_title_here', 'g5tech_project_title_placeholder', 10, 2 );

function g5tech_project_columns( $columns ) {
	$updated = array();

	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated['g5_project_year']    = 'Metai';
			$updated['g5_project_service'] = 'Paslauga';
			$updated['g5_project_visible'] = 'Viešas';
		}
	}

	return $updated;
}
add_filter( 'manage_g5_project_posts_columns', 'g5tech_project_columns' );

function g5tech_project_column_content( $column, $post_id ) {
	if ( 'g5_project_year' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'g5_project_year', true ) ?: '—' );
	}

	if ( 'g5_project_service' === $column ) {
		$service_id = absint( get_post_meta( $post_id, 'g5_project_service', true ) );
		echo esc_html( $service_id ? get_the_title( $service_id ) : '—' );
	}

	if ( 'g5_project_visible' === $column ) {
		echo g5tech_project_is_visible( $post_id ) ? 'Taip' : 'Ne';
	}
}
add_action( 'manage_g5_project_posts_custom_column', 'g5tech_project_column_content', 10, 2 );

function g5tech_project_is_visible( $post_id ) {
	if ( 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}

	return ! metadata_exists( 'post', $post_id, 'g5_project_visible' )
		|| (bool) get_post_meta( $post_id, 'g5_project_visible', true );
}

function g5tech_get_projects( $limit = -1 ) {
	$projects = get_posts(
		array(
			'post_type'      => 'g5_project',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
		)
	);
	$projects = array_values(
		array_filter(
			$projects,
			function( $project ) {
				return g5tech_project_is_visible( $project->ID );
			}
		)
	);

	return $limit > -1 ? array_slice( $projects, 0, $limit ) : $projects;
}

function g5tech_project_terms( $post_id, $taxonomy ) {
	$terms = wp_get_post_terms(
		$post_id,
		$taxonomy,
		array(
			'fields' => 'names',
		)
	);

	return is_wp_error( $terms ) ? array() : $terms;
}

function g5tech_project_lines( $post_id, $key ) {
	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', (string) get_post_meta( $post_id, $key, true ) )
			)
		)
	);
}

function g5tech_project_image( $post_id, $size = 'large', $decorative = false ) {
	if ( ! has_post_thumbnail( $post_id ) ) {
		return '';
	}

	$image_id = get_post_thumbnail_id( $post_id );
	$alt      = $decorative ? '' : get_post_meta( $image_id, '_wp_attachment_image_alt', true );
	$alt      = $decorative ? '' : ( $alt ?: get_the_title( $post_id ) );

	return wp_get_attachment_image(
		$image_id,
		$size,
		false,
		array(
			'alt'     => $alt,
			'loading' => $decorative ? 'lazy' : 'eager',
		)
	);
}

function g5tech_render_project_cards( $projects ) {
	if ( ! $projects ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-project-grid">
		<?php foreach ( $projects as $project ) : ?>
			<?php
			$countries   = g5tech_project_terms( $project->ID, 'g5_project_country' );
			$technologies = g5tech_project_terms( $project->ID, 'g5_project_technology' );
			$summary     = get_post_meta( $project->ID, 'g5_project_summary', true );
			$year        = get_post_meta( $project->ID, 'g5_project_year', true );
			$meta        = array_filter( array( implode( ', ', $countries ), $year ) );
			?>
			<a class="g5-project-card" href="<?php echo esc_url( get_permalink( $project ) ); ?>">
				<?php if ( has_post_thumbnail( $project->ID ) ) : ?>
					<div class="g5-project-card__media"><?php echo g5tech_project_image( $project->ID, 'large', true ); ?></div>
				<?php endif; ?>
				<div class="g5-project-card__body">
					<?php if ( $meta ) : ?><span class="g5-project-card__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></span><?php endif; ?>
					<h3 class="g5-heading-md"><?php echo esc_html( get_the_title( $project ) ); ?></h3>
					<?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
					<?php if ( $technologies ) : ?>
						<span class="g5-project-card__technology"><?php echo esc_html( implode( ' / ', $technologies ) ); ?></span>
					<?php endif; ?>
					<span class="g5-project-card__link">Peržiūrėti projektą →</span>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_register_project_blocks() {
	register_block_type(
		'g5tech/projects-archive',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH projektų sąrašas',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_projects_archive',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);

	register_block_type(
		'g5tech/project-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH projekto puslapis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_project_page',
			'uses_context'    => array( 'postId', 'postType' ),
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_project_blocks' );

function g5tech_render_projects_archive_legacy() {
	$projects = g5tech_get_projects();

	ob_start();
	?>
	<main>
		<section class="g5-inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-projects-title">
			<div class="g5-container g5-grid">
				<div class="g5-inner-hero__copy">
					<div class="g5-eyebrow">Projektai</div>
					<h1 class="g5-display-xl" id="g5-projects-title">Infrastruktūros projektai Europoje.</h1>
					<p class="g5-body-lg">Telekomunikacijų, energetikos ir inžinerinės infrastruktūros darbai Lietuvoje bei kitose Europos šalyse.</p>
				</div>
			</div>
		</section>
		<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="g5-projects-list-title" data-g5-core-module="projects_list">
			<div class="g5-container g5-section-head">
				<div class="g5-eyebrow">Patirtis</div>
				<div class="g5-section-head__copy"><h2 class="g5-display-md" id="g5-projects-list-title">Atrinkti projektai.</h2></div>
			</div>
			<div class="g5-container">
				<?php if ( $projects ) : ?>
					<?php echo g5tech_render_project_cards( $projects ); ?>
				<?php else : ?>
					<p class="g5-body-lg">Projektų informacija ruošiama.</p>
				<?php endif; ?>
			</div>
		</section>
		<?php echo g5tech_render_page_modules( 'projects' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<section class="g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-projects-cta-title" data-g5-page-anchor="cta">
			<div class="g5-container g5-cta-grid">
				<div>
					<div class="g5-eyebrow">Naujas projektas</div>
					<h2 class="g5-display-lg" id="g5-projects-cta-title">Aptarkime jūsų projektą.</h2>
					<p class="g5-body">Atsiūskite turimą informaciją – įvertinsime darbų apimtį ir tolesnius veiksmus.</p>
				</div>
				<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( g5tech_setting( 'contact_page_url', '/kontaktai/' ) ) ); ?>">Susisiekti <span class="g5-button__icon" aria-hidden="true">→</span></a>
			</div>
		</section>
	</main>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_projects_archive() {
	return g5tech_compose_modular_page( 'projects', g5tech_get_legacy_page_html( 'projects' ) );
}

function g5tech_project_page_post_id( $block ) {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}

	return get_the_ID();
}

function g5tech_render_project_page( $attributes, $content, $block ) {
	$post_id      = g5tech_project_page_post_id( $block );
	$summary      = get_post_meta( $post_id, 'g5_project_summary', true );
	$year         = get_post_meta( $post_id, 'g5_project_year', true );
	$location     = get_post_meta( $post_id, 'g5_project_location', true );
	$result       = get_post_meta( $post_id, 'g5_project_result', true );
	$service_id   = absint( get_post_meta( $post_id, 'g5_project_service', true ) );
	$scope        = g5tech_project_lines( $post_id, 'g5_project_scope' );
	$countries    = g5tech_project_terms( $post_id, 'g5_project_country' );
	$technologies = g5tech_project_terms( $post_id, 'g5_project_technology' );
	$hero_meta    = array_filter( array( implode( ', ', $countries ), $year ) );
	$facts        = array_filter(
		array(
			'Šalis'        => implode( ', ', $countries ),
			'Vieta'        => $location,
			'Technologija' => implode( ', ', $technologies ),
			'Paslauga'     => $service_id ? get_the_title( $service_id ) : '',
		)
	);

	ob_start();
	?>
	<main>
		<section class="g5-inner-hero g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-project-title">
			<div class="g5-container g5-grid">
				<div class="g5-inner-hero__copy">
					<nav class="g5-breadcrumbs" aria-label="Puslapio kelias"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Pagrindinis</a><span>/</span><a href="<?php echo esc_url( get_post_type_archive_link( 'g5_project' ) ); ?>">Projektai</a></nav>
					<?php if ( $hero_meta ) : ?><div class="g5-eyebrow"><?php echo esc_html( implode( ' · ', $hero_meta ) ); ?></div><?php endif; ?>
					<h1 class="g5-display-xl" id="g5-project-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h1>
					<?php if ( $summary ) : ?><p class="g5-body-lg"><?php echo esc_html( $summary ); ?></p><?php endif; ?>
				</div>
			</div>
		</section>

		<?php if ( $facts ) : ?>
			<section class="g5-project-facts-section g5-grid-lines g5-grid-lines--dark" aria-label="Projekto informacija">
				<div class="g5-container g5-project-facts">
					<?php foreach ( $facts as $label => $value ) : ?>
						<div><span><?php echo esc_html( $label ); ?></span><strong><?php echo esc_html( $value ); ?></strong></div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<section class="g5-section g5-section--paper g5-grid-lines">
				<figure class="g5-container g5-project-image"><?php echo g5tech_project_image( $post_id ); ?></figure>
			</section>
		<?php endif; ?>

		<?php if ( $scope ) : ?>
			<section class="g5-section g5-grid-lines" aria-labelledby="g5-project-scope-title">
				<div class="g5-container g5-section-head">
					<div class="g5-eyebrow">Apimtis</div>
					<div class="g5-section-head__copy"><h2 class="g5-display-md" id="g5-project-scope-title">Atlikti darbai.</h2></div>
				</div>
				<ul class="g5-container g5-project-scope">
					<?php foreach ( $scope as $item ) : ?><li><?php echo esc_html( $item ); ?></li><?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( $result ) : ?>
			<section class="g5-section g5-section--dark g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-project-result-title">
				<div class="g5-container g5-section-head g5-section-head--dark">
					<div class="g5-eyebrow">Rezultatas</div>
					<div class="g5-section-head__copy">
						<h2 class="g5-display-md" id="g5-project-result-title">Projekto rezultatas.</h2>
						<p class="g5-body-lg"><?php echo esc_html( $result ); ?></p>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="g5-section g5-cta-section g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-project-cta-title">
			<div class="g5-container g5-cta-grid">
				<div>
					<div class="g5-eyebrow">Naujas projektas</div>
					<h2 class="g5-display-lg" id="g5-project-cta-title">Aptarkime jūsų projektą.</h2>
				</div>
				<a class="g5-button g5-button--primary" href="<?php echo esc_url( home_url( g5tech_setting( 'contact_page_url', '/kontaktai/' ) ) ); ?>">Susisiekti <span class="g5-button__icon" aria-hidden="true">→</span></a>
			</div>
		</section>
	</main>
	<?php
	return (string) ob_get_clean();
}

function g5tech_redirect_hidden_project() {
	if ( is_singular( 'g5_project' ) && ! g5tech_project_is_visible( get_queried_object_id() ) ) {
		wp_safe_redirect( get_post_type_archive_link( 'g5_project' ) );
		exit;
	}
}
add_action( 'template_redirect', 'g5tech_redirect_hidden_project' );
