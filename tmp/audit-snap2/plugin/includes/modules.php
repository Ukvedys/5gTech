<?php
/**
 * Pakartotinai naudojamų turinio modulių biblioteka.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_content_module_type() {
	register_post_type(
		'g5_module',
		array(
			'labels' => array(
				'name'               => 'Turinio moduliai',
				'singular_name'      => 'Turinio modulis',
				'add_new'            => 'Pridėti modulį',
				'add_new_item'       => 'Pridėti turinio modulį',
				'edit_item'          => 'Redaguoti turinio modulį',
				'new_item'           => 'Naujas turinio modulis',
				'view_item'          => 'Peržiūrėti modulį',
				'search_items'       => 'Ieškoti modulių',
				'not_found'          => 'Modulių nerasta',
				'not_found_in_trash' => 'Šiukšlinėje modulių nėra',
				'menu_name'          => 'Turinio moduliai',
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-screenoptions',
			'menu_position' => 22,
			'supports'      => array( 'title', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'g5tech_register_content_module_type', 5 );

function g5tech_add_page_modules_admin_page() {
	add_submenu_page(
		'edit.php?post_type=g5_module',
		'Puslapių moduliai',
		'Puslapių moduliai',
		'edit_g5_modules',
		'g5tech-page-modules',
		'g5tech_render_page_modules_admin_page'
	);
}
add_action( 'admin_menu', 'g5tech_add_page_modules_admin_page', 25 );

function g5tech_redirect_special_page_modules_admin() {
	if (
		'g5tech-page-modules' !== sanitize_key( wp_unslash( $_GET['page'] ?? '' ) )
		|| 'g5_module' !== sanitize_key( wp_unslash( $_GET['post_type'] ?? '' ) )
	) {
		return;
	}

	$page_key = sanitize_key( wp_unslash( $_GET['page_key'] ?? '' ) );

	if ( ! in_array( $page_key, array( 'about', 'career', 'training', 'academy', 'leaders', 'project_managers' ), true ) ) {
		return;
	}

	if ( ! g5tech_user_can_manage_module_page( $page_key ) ) {
		return;
	}

	wp_safe_redirect( g5tech_module_page_admin_url( $page_key ) );
	exit;
}
add_action( 'admin_init', 'g5tech_redirect_special_page_modules_admin', 20 );

function g5tech_module_meta_keys() {
	return array(
		'g5_module_type',
		'g5_module_eyebrow',
		'g5_module_heading',
		'g5_module_lead',
		'g5_module_content',
		'g5_module_theme',
		'g5_module_source_key',
		'g5_module_source_id',
		'g5_module_dynamic_key',
		'_g5tech_module_translations',
	);
}

function g5tech_module_page_choices() {
	return array(
		'home'             => 'Titulinis',
		'services'         => 'Paslaugos',
		'projects'         => 'Projektai',
		'experience'       => 'Patirtis',
		'about'            => 'Apie mus',
		'career'           => 'Karjera',
		'academy'          => '5GTECH Academy',
		'training'         => 'Mokymai',
		'candidate_faq'    => 'DUK kandidatams',
		'leaders'          => 'Vadovams',
		'project_managers' => 'Projektų vadovams',
		'contact'          => 'Kontaktai',
		'news'             => 'Naujienos',
	);
}

function g5tech_module_page_public_url( $page_key ) {
	$urls = array(
		'home'             => home_url( '/' ),
		'services'         => get_post_type_archive_link( 'g5_service' ),
		'projects'         => get_post_type_archive_link( 'g5_project' ),
		'experience'       => home_url( '/patirtis/' ),
		'about'            => home_url( '/apie-mus/' ),
		'career'           => home_url( '/karjera/' ),
		'academy'          => home_url( '/akademija/' ),
		'training'         => home_url( '/mokymai/' ),
		'candidate_faq'    => home_url( '/duk/' ),
		'leaders'          => home_url( '/vadovams/' ),
		'project_managers' => home_url( '/projektu-vadovams/' ),
		'contact'          => home_url( '/kontaktai/' ),
		'news'             => home_url( '/naujienos/' ),
	);

	return $urls[ $page_key ] ?? home_url( '/' );
}

function g5tech_module_page_key_for_slug( $slug ) {
	$map = array(
		'pagrindinis'        => 'home',
		'patirtis'           => 'experience',
		'apie-mus'           => 'about',
		'karjera'            => 'career',
		'akademija'          => 'academy',
		'mokymai'            => 'training',
		'duk'                => 'candidate_faq',
		'vadovams'           => 'leaders',
		'projektu-vadovams'  => 'project_managers',
		'kontaktai'          => 'contact',
		'naujienos'          => 'news',
	);

	return $map[ sanitize_title( $slug ) ] ?? '';
}

function g5tech_module_placements() {
	$saved  = get_option( 'g5tech_module_placements', array() );
	$result = array();

	foreach ( g5tech_module_page_choices() as $page_key => $page_label ) {
		$ids = isset( $saved[ $page_key ] ) && is_array( $saved[ $page_key ] )
			? $saved[ $page_key ]
			: array();

		$result[ $page_key ] = array_values(
			array_unique(
				array_filter(
					array_map( 'absint', $ids ),
					static function( $module_id ) {
						return $module_id && 'g5_module' === get_post_type( $module_id );
					}
				)
			)
		);
	}

	return $result;
}

function g5tech_get_page_modules( $page_key ) {
	$placements = g5tech_module_placements();
	$ids        = $placements[ $page_key ] ?? array();

	if ( ! $ids ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'orderby'        => 'post__in',
		)
	);
}

function g5tech_get_page_modules_for_admin( $page_key ) {
	$placements = g5tech_module_placements();
	$ids        = $placements[ $page_key ] ?? array();

	if ( ! $ids ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => array( 'publish', 'draft', 'pending', 'private', 'future' ),
			'posts_per_page' => -1,
			'post__in'       => $ids,
			'orderby'        => 'post__in',
		)
	);
}

function g5tech_get_source_module( $source_key ) {
	$modules = get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'meta_key'       => 'g5_module_source_key',
			'meta_value'     => sanitize_key( $source_key ),
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	return $modules ? $modules[0] : null;
}

function g5tech_module_is_on_page( $module_id, $page_key ) {
	$placements = g5tech_module_placements();

	return in_array( absint( $module_id ), $placements[ $page_key ] ?? array(), true );
}

function g5tech_set_module_pages( $module_id, $selected_pages ) {
	$module_id     = absint( $module_id );
	$selected_pages = array_map( 'sanitize_key', (array) $selected_pages );
	$placements    = g5tech_module_placements();

	foreach ( g5tech_module_page_choices() as $page_key => $page_label ) {
		$ids = array_values( array_diff( $placements[ $page_key ] ?? array(), array( $module_id ) ) );

		if ( in_array( $page_key, $selected_pages, true ) ) {
			$ids[] = $module_id;
		}

		$placements[ $page_key ] = array_values( array_unique( array_map( 'absint', $ids ) ) );
	}

	update_option( 'g5tech_module_placements', $placements, false );
}

function g5tech_set_module_page_active( $module_id, $page_key, $active ) {
	if ( ! isset( g5tech_module_page_choices()[ $page_key ] ) ) {
		return;
	}

	$module_id  = absint( $module_id );
	$placements = g5tech_module_placements();
	$ids        = array_values( array_diff( $placements[ $page_key ] ?? array(), array( $module_id ) ) );

	if ( $active && $module_id && 'g5_module' === get_post_type( $module_id ) ) {
		$ids[] = $module_id;
	}

	$placements[ $page_key ] = array_values( array_unique( array_map( 'absint', $ids ) ) );
	update_option( 'g5tech_module_placements', $placements, false );
}

function g5tech_remove_module_from_all_pages( $module_id ) {
	$module_id  = absint( $module_id );
	$placements = g5tech_module_placements();

	foreach ( $placements as $page_key => $ids ) {
		$placements[ $page_key ] = array_values( array_diff( $ids, array( $module_id ) ) );
	}

	update_option( 'g5tech_module_placements', $placements, false );
}

function g5tech_set_page_module_order( $page_key, $order ) {
	if ( ! isset( g5tech_module_page_choices()[ $page_key ] ) ) {
		return false;
	}

	$placements = g5tech_module_placements();
	$current    = $placements[ $page_key ] ?? array();
	$order      = array_values( array_unique( array_filter( array_map( 'absint', (array) $order ) ) ) );
	$ordered    = array_values( array_intersect( $order, $current ) );
	$preserved  = array_values( array_diff( $current, $ordered ) );

	$placements[ $page_key ] = array_merge( $preserved, $ordered );
	update_option( 'g5tech_module_placements', $placements, false );

	return true;
}

function g5tech_module_page_admin_url( $page_key ) {
	if ( 'training' === $page_key ) {
		return admin_url( 'admin.php?page=g5tech-training-content' );
	}

	if ( 'about' === $page_key ) {
		return admin_url( 'edit.php?post_type=g5_team&page=g5tech-about-order' );
	}

	if ( 'career' === $page_key && function_exists( 'g5tech_career_content_admin_url' ) ) {
		return g5tech_career_content_admin_url();
	}

	if ( in_array( $page_key, array( 'academy', 'leaders', 'project_managers' ), true ) && function_exists( 'g5tech_structured_content_admin_url' ) ) {
		return g5tech_structured_content_admin_url( $page_key );
	}

	return add_query_arg(
		array(
			'post_type' => 'g5_module',
			'page'      => 'g5tech-page-modules',
			'page_key'  => $page_key,
		),
		admin_url( 'edit.php' )
	);
}

function g5tech_user_can_manage_module_page( $page_key ) {
	if ( ! isset( g5tech_module_page_choices()[ $page_key ] ) || ! current_user_can( 'edit_g5_modules' ) ) {
		return false;
	}

	if ( function_exists( 'g5tech_user_has_role' ) && g5tech_user_has_role( 'g5_hr_editor' ) ) {
		return in_array(
			$page_key,
			array( 'about', 'career', 'academy', 'training', 'candidate_faq' ),
			true
		);
	}

	return true;
}

function g5tech_module_lines( $module_id ) {
	$content = (string) get_post_meta( $module_id, 'g5_module_content', true );

	return array_values(
		array_filter(
			array_map(
				'trim',
				preg_split( '/\r\n|\r|\n/', $content )
			)
		)
	);
}

function g5tech_render_content_module( $module ) {
	$module = is_numeric( $module ) ? get_post( absint( $module ) ) : $module;

	if ( ! $module instanceof WP_Post || 'g5_module' !== $module->post_type || 'publish' !== $module->post_status ) {
		return '';
	}

	$type    = get_post_meta( $module->ID, 'g5_module_type', true ) ?: 'list';

	if ( 'dynamic' === $type && function_exists( 'g5tech_render_dynamic_content_module' ) ) {
		return g5tech_render_dynamic_content_module( $module );
	}

	$eyebrow = get_post_meta( $module->ID, 'g5_module_eyebrow', true );
	$heading = get_post_meta( $module->ID, 'g5_module_heading', true );
	$lead    = get_post_meta( $module->ID, 'g5_module_lead', true );
	$content = get_post_meta( $module->ID, 'g5_module_content', true );
	$theme   = get_post_meta( $module->ID, 'g5_module_theme', true ) ?: 'light';
	$is_dark = 'dark' === $theme;
	$classes = array( 'g5-section', 'g5-grid-lines', 'g5-content-module' );

	if ( 'paper' === $theme ) {
		$classes[] = 'g5-section--paper';
	} elseif ( $is_dark ) {
		$classes[] = 'g5-section--dark';
		$classes[] = 'g5-grid-lines--dark';
	}

	$heading = $heading ?: get_the_title( $module );
	$title_id = 'g5-content-module-' . $module->ID;

	ob_start();
	?>
	<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>" data-content-module="<?php echo (int) $module->ID; ?>">
		<?php g5tech_content_section_head( $eyebrow, $heading, $title_id, $is_dark, $lead ); ?>
		<?php if ( 'list' === $type ) : ?>
			<ul class="g5-container check-list">
				<?php foreach ( g5tech_module_lines( $module->ID ) as $item ) : ?>
					<li><?php echo esc_html( $item ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $content ) : ?>
			<div class="g5-container g5-content-module__body g5-body">
				<?php foreach ( g5tech_module_lines( $module->ID ) as $paragraph ) : ?>
					<p><?php echo esc_html( $paragraph ); ?></p>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_page_modules( $page_key, $exclude_ids = array() ) {
	if ( ! empty( $GLOBALS['g5tech_rendering_legacy_page'] ) ) {
		return '';
	}

	$exclude_ids = array_map( 'absint', (array) $exclude_ids );
	$output      = '';

	foreach ( g5tech_get_page_modules( $page_key ) as $module ) {
		if ( in_array( (int) $module->ID, $exclude_ids, true ) ) {
			continue;
		}

		$output .= g5tech_render_content_module( $module );
	}

	return $output;
}

function g5tech_create_content_module_copy( $source_id ) {
	$source_id = absint( $source_id );
	$source    = get_post( $source_id );

	if ( ! $source || 'g5_module' !== $source->post_type ) {
		return new WP_Error( 'g5tech_invalid_module', 'Modulis nerastas.' );
	}

	if ( 'dynamic' === get_post_meta( $source_id, 'g5_module_type', true ) ) {
		return new WP_Error( 'g5tech_dynamic_module_copy', 'Dinaminė sekcija visada naudoja bendrą duomenų šaltinį, todėl atskira jos kopija nekuriama.' );
	}

	$copy_id = wp_insert_post(
		array(
			'post_type'   => 'g5_module',
			'post_status' => 'draft',
			'post_title'  => $source->post_title . ' – kopija',
			'menu_order'  => $source->menu_order,
		),
		true
	);

	if ( is_wp_error( $copy_id ) ) {
		return $copy_id;
	}

	foreach ( g5tech_module_meta_keys() as $meta_key ) {
		if ( in_array( $meta_key, array( 'g5_module_source_key', 'g5_module_source_id' ), true ) ) {
			continue;
		}

		update_post_meta( $copy_id, $meta_key, get_post_meta( $source_id, $meta_key, true ) );
	}

	update_post_meta( $copy_id, 'g5_module_source_id', $source_id );

	return $copy_id;
}

function g5tech_render_page_module_manager( $page_key, $exclude_ids = array() ) {
	if ( ! g5tech_user_can_manage_module_page( $page_key ) ) {
		return;
	}

	$page_choices = g5tech_module_page_choices();

	if ( ! isset( $page_choices[ $page_key ] ) ) {
		return;
	}

	$exclude_ids = array_map( 'absint', (array) $exclude_ids );
	$placed      = array_values(
		array_filter(
			g5tech_get_page_modules_for_admin( $page_key ),
			static function( $module ) use ( $exclude_ids ) {
				return ! in_array( (int) $module->ID, $exclude_ids, true );
			}
		)
	);
	$placed_ids  = array_map(
		static function( $module ) {
			return (int) $module->ID;
		},
		g5tech_get_page_modules_for_admin( $page_key )
	);
	$available   = get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__not_in'   => $placed_ids,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);
	$dialog_id = 'g5tech-module-picker-' . $page_key;
	?>
	<section class="g5tech-page-modules" aria-labelledby="<?php echo esc_attr( $dialog_id . '-title' ); ?>">
		<div class="g5tech-page-modules__head">
			<div>
				<h2 id="<?php echo esc_attr( $dialog_id . '-title' ); ?>">Puslapio turinio moduliai</h2>
				<p>Tempkite sekcijas norima tvarka, pašalinkite nereikalingas arba įkelkite modulį iš bendros bibliotekos. Puslapio pradžia ir baigiamasis kvietimas lieka fiksuoti.</p>
			</div>
			<button class="button button-primary g5tech-module-picker-open" type="button" data-dialog="<?php echo esc_attr( $dialog_id ); ?>">Įkelti turinio modulį</button>
		</div>

		<?php if ( isset( $_GET['module_added'] ) ) : ?>
			<div class="notice notice-success inline"><p>Turinio modulis įkeltas.</p></div>
		<?php elseif ( isset( $_GET['module_removed'] ) ) : ?>
			<div class="notice notice-success inline"><p>Modulis pašalintas iš šio puslapio. Bibliotekoje jis liko.</p></div>
		<?php elseif ( isset( $_GET['module_deleted'] ) ) : ?>
			<div class="notice notice-success inline"><p>Modulis pašalintas iš visų puslapių ir perkeltas į šiukšlinę.</p></div>
		<?php elseif ( isset( $_GET['module_ordered'] ) ) : ?>
			<div class="notice notice-success inline"><p>Modulių tvarka išsaugota.</p></div>
		<?php endif; ?>

		<?php if ( $placed ) : ?>
			<div class="g5tech-page-modules__list" data-module-sortable>
				<?php foreach ( $placed as $module ) : ?>
					<?php
					$is_dynamic = 'dynamic' === get_post_meta( $module->ID, 'g5_module_type', true );
					$definition = $is_dynamic && function_exists( 'g5tech_get_builtin_module_definition' )
						? g5tech_get_builtin_module_definition( get_post_meta( $module->ID, 'g5_module_dynamic_key', true ) )
						: null;
					$edit_url   = $definition['source_url'] ?? get_edit_post_link( $module->ID );
					$edit_label = $is_dynamic ? 'Redaguoti turinį' : 'Redaguoti';
					?>
					<div class="g5tech-page-module-card" data-module-id="<?php echo (int) $module->ID; ?>">
						<button class="g5tech-page-module-card__drag" type="button" title="Tempti modulį" aria-label="Tempti modulį">↕</button>
						<div class="g5tech-page-module-card__info">
							<strong><?php echo esc_html( get_the_title( $module ) ); ?></strong>
							<span>
								<?php echo $is_dynamic ? esc_html( 'Bendras duomenų šaltinis: ' . ( $definition['source_label'] ?? 'svetainės turinys' ) ) : esc_html( get_post_meta( $module->ID, 'g5_module_heading', true ) ); ?>
								<?php if ( 'publish' !== $module->post_status ) : ?>
									· <?php echo esc_html( get_post_status_object( $module->post_status )->label ?? 'Juodraštis' ); ?>
								<?php endif; ?>
							</span>
						</div>
						<div class="g5tech-page-module-card__actions">
							<a class="button" href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( $edit_label ); ?></a>
							<?php if ( $is_dynamic ) : ?>
								<a class="button-link" href="<?php echo esc_url( add_query_arg( 'content_lang', 'en', get_edit_post_link( $module->ID ) ) ); ?>#g5tech-module-content">LT / EN / DE</a>
								<a class="button-link" href="<?php echo esc_url( get_edit_post_link( $module->ID ) ); ?>">Kur naudojamas</a>
							<?php endif; ?>
							<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
								<input type="hidden" name="action" value="g5tech_remove_module_from_page">
								<input type="hidden" name="page_key" value="<?php echo esc_attr( $page_key ); ?>">
								<input type="hidden" name="module_id" value="<?php echo (int) $module->ID; ?>">
								<?php wp_nonce_field( 'g5tech_remove_module_from_' . $page_key ); ?>
								<button class="button-link-delete" type="submit">Pašalinti iš puslapio</button>
							</form>
							<?php if ( current_user_can( 'delete_post', $module->ID ) ) : ?>
								<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-delete-module-form>
									<input type="hidden" name="action" value="g5tech_delete_module_from_library">
									<input type="hidden" name="page_key" value="<?php echo esc_attr( $page_key ); ?>">
									<input type="hidden" name="module_id" value="<?php echo (int) $module->ID; ?>">
									<?php wp_nonce_field( 'g5tech_delete_module_from_library_' . $module->ID ); ?>
									<button class="button-link-delete" type="submit">Ištrinti iš bibliotekos</button>
								</form>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( count( $placed ) > 1 ) : ?>
				<form class="g5tech-page-modules__order" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" data-module-order-form>
					<input type="hidden" name="action" value="g5tech_reorder_page_modules">
					<input type="hidden" name="page_key" value="<?php echo esc_attr( $page_key ); ?>">
					<input type="hidden" name="module_order" value="<?php echo esc_attr( implode( ',', wp_list_pluck( $placed, 'ID' ) ) ); ?>" data-module-order>
					<?php wp_nonce_field( 'g5tech_reorder_page_modules_' . $page_key ); ?>
					<button class="button button-primary" type="submit">Išsaugoti modulių tvarką</button>
				</form>
			<?php endif; ?>
		<?php else : ?>
			<p class="description">Šiame puslapyje turinio modulių nėra.</p>
		<?php endif; ?>

		<?php if ( ! $available ) : ?>
			<p class="description">Visi paskelbti bibliotekos moduliai jau naudojami šiame puslapyje. <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=g5_module' ) ); ?>">Sukurti naują modulį</a>.</p>
		<?php endif; ?>

		<dialog class="g5tech-module-picker" id="<?php echo esc_attr( $dialog_id ); ?>">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="g5tech_add_module_to_page">
				<input type="hidden" name="page_key" value="<?php echo esc_attr( $page_key ); ?>">
				<?php wp_nonce_field( 'g5tech_add_module_to_' . $page_key ); ?>
				<div class="g5tech-module-picker__head">
					<h2>Įkelti turinio modulį</h2>
					<button class="g5tech-module-picker-close" type="button" aria-label="Uždaryti">×</button>
				</div>
				<p>
					<label for="<?php echo esc_attr( $dialog_id . '-module' ); ?>"><strong>Modulis</strong></label>
					<select class="widefat" id="<?php echo esc_attr( $dialog_id . '-module' ); ?>" name="module_id" required>
						<option value="">Pasirinkite modulį</option>
						<?php foreach ( $available as $module ) : ?>
							<option
								value="<?php echo (int) $module->ID; ?>"
								data-dynamic="<?php echo 'dynamic' === get_post_meta( $module->ID, 'g5_module_type', true ) ? '1' : '0'; ?>"
							><?php echo esc_html( get_the_title( $module ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<?php if ( ! $available ) : ?>
					<p class="description">Nėra neįkeltų paskelbtų modulių. <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=g5_module' ) ); ?>">Sukurti naują modulį</a>.</p>
				<?php endif; ?>
				<fieldset class="g5tech-module-picker__modes">
					<legend><strong>Kaip naudoti?</strong></legend>
					<label>
						<input type="radio" name="module_mode" value="linked" checked>
						<span><strong>Naudoti tą patį modulį</strong><small>Pakeitimai atsinaujins visuose puslapiuose, kuriuose jis naudojamas.</small></span>
					</label>
					<label>
						<input type="radio" name="module_mode" value="copy">
						<span><strong>Sukurti nepriklausomą kopiją</strong><small>Bus sukurtas juodraštis. Kopiją galėsite pakeisti ir paskelbti atskirai.</small></span>
					</label>
				</fieldset>
				<div class="g5tech-module-picker__actions">
					<button class="button" type="button" data-module-picker-cancel>Atšaukti</button>
					<button class="button button-primary" type="submit" <?php disabled( ! $available ); ?>>Įkelti modulį</button>
				</div>
			</form>
		</dialog>
	</section>
	<style>
		.g5tech-page-modules{margin:24px 0;padding:20px;border:1px solid #c3c4c7;background:#fff}
		.g5tech-page-modules__head{display:flex;align-items:center;justify-content:space-between;gap:20px}
		.g5tech-page-modules__head h2{margin:0 0 4px}
		.g5tech-page-modules__head p{margin:0;color:#646970}
		.g5tech-page-modules__list{display:grid;gap:8px;margin-top:18px}
		.g5tech-page-module-card{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:12px 14px;border:1px solid #dcdcde;background:#f6f7f7}
		.g5tech-page-module-card.is-dragging{opacity:.55}
		.g5tech-page-module-card__drag{display:grid;width:32px;height:32px;flex:0 0 32px;place-items:center;padding:0;border:1px solid #c3c4c7;border-radius:3px;background:#fff;color:#50575e;cursor:grab;font-size:18px;line-height:1}
		.g5tech-page-module-card__drag:active{cursor:grabbing}
		.g5tech-page-module-card__info{display:flex;min-width:0;flex:1 1 auto;flex-direction:column;gap:3px}
		.g5tech-page-module-card span{overflow:hidden;color:#646970;text-overflow:ellipsis;white-space:nowrap}
		.g5tech-page-module-card__actions{display:flex;align-items:center;gap:12px;flex:0 0 auto}
		.g5tech-page-module-card__actions form{margin:0}
		.g5tech-page-modules__order{display:flex;justify-content:flex-end;margin-top:12px}
		.g5tech-module-picker{width:min(560px,calc(100vw - 40px));padding:0;border:0;box-shadow:0 18px 70px rgba(0,0,0,.3)}
		.g5tech-module-picker::backdrop{background:rgba(0,0,0,.52)}
		.g5tech-module-picker>form{padding:22px}
		.g5tech-module-picker__head{display:flex;align-items:center;justify-content:space-between;gap:20px}
		.g5tech-module-picker__head h2{margin:0}
		.g5tech-module-picker-close{padding:0;border:0;background:transparent;color:#646970;cursor:pointer;font-size:28px;line-height:1}
		.g5tech-module-picker__modes{display:grid;gap:8px;margin:20px 0;padding:0;border:0}
		.g5tech-module-picker__modes legend{margin-bottom:8px}
		.g5tech-module-picker__modes label{display:flex;align-items:flex-start;gap:10px;padding:12px;border:1px solid #dcdcde}
		.g5tech-module-picker__modes input{margin-top:3px}
		.g5tech-module-picker__modes span{display:flex;flex-direction:column;gap:3px}
		.g5tech-module-picker__modes small{color:#646970}
		.g5tech-module-picker__actions{display:flex;justify-content:flex-end;gap:10px;margin-top:20px}
		@media(max-width:782px){.g5tech-page-modules__head,.g5tech-page-module-card{align-items:flex-start;flex-direction:column}.g5tech-page-module-card__actions{flex-wrap:wrap}}
	</style>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const dialog = document.getElementById(<?php echo wp_json_encode( $dialog_id ); ?>);
			const openButton = document.querySelector('[data-dialog="<?php echo esc_attr( $dialog_id ); ?>"]');

			if (!dialog || !openButton) {
				return;
			}

			openButton.addEventListener('click', function() {
				dialog.showModal();
			});

			dialog.querySelectorAll('.g5tech-module-picker-close,[data-module-picker-cancel]').forEach(function(button) {
				button.addEventListener('click', function() {
					dialog.close();
				});
			});

			const moduleSelect = dialog.querySelector('select[name="module_id"]');
			const copyInput = dialog.querySelector('input[name="module_mode"][value="copy"]');
			const linkedInput = dialog.querySelector('input[name="module_mode"][value="linked"]');

			if (moduleSelect && copyInput && linkedInput) {
				const updateCopyMode = function() {
					const option = moduleSelect.options[moduleSelect.selectedIndex];
					const isDynamic = option && option.dataset.dynamic === '1';
					copyInput.disabled = isDynamic;

					if (isDynamic && copyInput.checked) {
						linkedInput.checked = true;
					}
				};

				moduleSelect.addEventListener('change', updateCopyMode);
				updateCopyMode();
			}

			document.querySelectorAll('[data-delete-module-form]').forEach(function(form) {
				form.addEventListener('submit', function(event) {
					if (!window.confirm('Modulis bus pašalintas iš visų puslapių ir perkeltas į šiukšlinę. Tęsti?')) {
						event.preventDefault();
					}
				});
			});

			const sortable = document.querySelector('[data-module-sortable]');
			const orderField = document.querySelector('[data-module-order]');

			if (sortable && orderField) {
				const updateOrder = function() {
					orderField.value = Array.from(sortable.querySelectorAll('[data-module-id]'))
						.map(function(card) { return card.dataset.moduleId; })
						.join(',');
				};

				sortable.querySelectorAll('[data-module-id]').forEach(function(card) {
					const handle = card.querySelector('.g5tech-page-module-card__drag');

					handle.addEventListener('mousedown', function() {
						card.draggable = true;
					});

					handle.addEventListener('touchstart', function() {
						card.draggable = true;
					}, {passive: true});

					card.addEventListener('dragstart', function(event) {
						card.classList.add('is-dragging');
						event.dataTransfer.effectAllowed = 'move';
					});

					card.addEventListener('dragend', function() {
						card.classList.remove('is-dragging');
						card.draggable = false;
						updateOrder();
					});
				});

				sortable.addEventListener('dragover', function(event) {
					event.preventDefault();
					const dragging = sortable.querySelector('.is-dragging');

					if (!dragging) {
						return;
					}

					const siblings = Array.from(sortable.querySelectorAll('[data-module-id]:not(.is-dragging)'));
					const next = siblings.find(function(card) {
						const box = card.getBoundingClientRect();
						return event.clientY < box.top + box.height / 2;
					});

					sortable.insertBefore(dragging, next || null);
				});
			}
		});
	</script>
	<?php
}

function g5tech_render_page_modules_admin_page() {
	if ( ! current_user_can( 'edit_g5_modules' ) ) {
		wp_die( esc_html__( 'Neturite teisės valdyti turinio modulių.', '5gtech-core' ) );
	}

	$choices  = g5tech_module_page_choices();
	$page_key = sanitize_key( wp_unslash( $_GET['page_key'] ?? 'home' ) );

	if ( ! isset( $choices[ $page_key ] ) || ! g5tech_user_can_manage_module_page( $page_key ) ) {
		foreach ( array_keys( $choices ) as $candidate ) {
			if ( g5tech_user_can_manage_module_page( $candidate ) ) {
				$page_key = $candidate;
				break;
			}
		}
	}

	$available_choices = array_filter(
		$choices,
		static function( $label, $key ) {
			return g5tech_user_can_manage_module_page( $key );
		},
		ARRAY_FILTER_USE_BOTH
	);
	?>
	<div class="wrap g5tech-admin-page g5tech-page-modules-admin">
		<?php
		g5tech_render_unified_admin_header(
			array(
				'title'       => $choices[ $page_key ] . ' puslapis',
				'description' => 'Prijunkite sekcijas iš bendros turinio modulių bibliotekos ir pakeiskite jų rodymo tvarką.',
				'page_url'    => g5tech_module_page_public_url( $page_key ),
				'actions'     => array(
					array(
						'label' => 'Modulių biblioteka',
						'url'   => admin_url( 'edit.php?post_type=g5_module' ),
					),
				),
			)
		);
		?>

		<form class="g5tech-page-modules-admin__selector" method="get" action="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">
			<input type="hidden" name="post_type" value="g5_module">
			<input type="hidden" name="page" value="g5tech-page-modules">
			<label for="g5tech-page-key"><strong>Puslapis</strong></label>
			<select id="g5tech-page-key" name="page_key">
				<?php foreach ( $available_choices as $choice_key => $choice_label ) : ?>
					<option value="<?php echo esc_attr( $choice_key ); ?>" <?php selected( $page_key, $choice_key ); ?>><?php echo esc_html( $choice_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<button class="button" type="submit">Rodyti</button>
		</form>

		<?php g5tech_render_page_module_manager( $page_key ); ?>
		<?php g5tech_render_unified_admin_preview( g5tech_module_page_public_url( $page_key ), $choices[ $page_key ] . ' puslapio' ); ?>
	</div>
	<style>
		.g5tech-page-modules-admin__selector{display:flex;align-items:center;gap:10px;margin:24px 0 4px}
		.g5tech-page-modules-admin__selector select{min-width:260px}
		.g5tech-page-modules-admin>.g5tech-admin-preview{position:static;margin-top:24px}
		.g5tech-page-modules-admin>.g5tech-admin-preview iframe{height:760px}
	</style>
	<?php
}

function g5tech_add_content_module_meta_boxes() {
	add_meta_box(
		'g5tech-module-content',
		'Modulio turinys',
		'g5tech_render_content_module_meta_box',
		'g5_module',
		'normal',
		'high'
	);

	add_meta_box(
		'g5tech-module-usage',
		'Kur naudojamas',
		'g5tech_render_content_module_usage_meta_box',
		'g5_module',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_content_module_meta_boxes' );

function g5tech_render_content_module_meta_box( $post ) {
	if ( function_exists( 'g5tech_render_module_translation_editor' ) ) {
		wp_nonce_field( 'g5tech_save_content_module', 'g5tech_module_nonce' );
		g5tech_render_module_translation_editor( $post );
		return;
	}

	$type    = get_post_meta( $post->ID, 'g5_module_type', true ) ?: 'list';
	$eyebrow = get_post_meta( $post->ID, 'g5_module_eyebrow', true );
	$heading = get_post_meta( $post->ID, 'g5_module_heading', true );
	$lead    = get_post_meta( $post->ID, 'g5_module_lead', true );
	$content = get_post_meta( $post->ID, 'g5_module_content', true );
	$theme   = get_post_meta( $post->ID, 'g5_module_theme', true ) ?: 'light';

	wp_nonce_field( 'g5tech_save_content_module', 'g5tech_module_nonce' );

	if ( 'dynamic' === $type ) {
		$dynamic_key = get_post_meta( $post->ID, 'g5_module_dynamic_key', true );
		$definition  = function_exists( 'g5tech_get_builtin_module_definition' )
			? g5tech_get_builtin_module_definition( $dynamic_key )
			: null;
		?>
		<p>Ši sekcija turinį pasiima iš bendrų svetainės duomenų. Čia valdoma, kuriuose puslapiuose ji rodoma ir kokia jos vieta.</p>
		<?php if ( $definition && ! empty( $definition['source_url'] ) ) : ?>
			<p><a class="button button-primary" href="<?php echo esc_url( $definition['source_url'] ); ?>">Redaguoti: <?php echo esc_html( $definition['source_label'] ); ?></a></p>
		<?php endif; ?>
		<input type="hidden" name="g5_module_type" value="dynamic">
		<?php
		return;
	}
	?>
	<p class="description">Viršuje esantis įrašo pavadinimas skirtas moduliui rasti administravime. Svetainėje rodoma žemiau įrašyta sekcijos antraštė.</p>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><label for="g5_module_type">Modulio tipas</label></th>
			<td>
				<select id="g5_module_type" name="g5_module_type">
					<option value="list" <?php selected( $type, 'list' ); ?>>Sąrašas</option>
					<option value="text" <?php selected( $type, 'text' ); ?>>Teksto sekcija</option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="g5_module_eyebrow">Trumpa žyma</label></th>
			<td><input class="large-text" id="g5_module_eyebrow" name="g5_module_eyebrow" type="text" value="<?php echo esc_attr( $eyebrow ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="g5_module_heading">Sekcijos antraštė</label></th>
			<td><input class="large-text" id="g5_module_heading" name="g5_module_heading" type="text" value="<?php echo esc_attr( $heading ); ?>"></td>
		</tr>
		<tr>
			<th scope="row"><label for="g5_module_lead">Įžanginis tekstas</label></th>
			<td><textarea class="large-text" id="g5_module_lead" name="g5_module_lead" rows="3"><?php echo esc_textarea( $lead ); ?></textarea></td>
		</tr>
		<tr>
			<th scope="row"><label for="g5_module_content">Turinys</label></th>
			<td>
				<textarea class="large-text" id="g5_module_content" name="g5_module_content" rows="10"><?php echo esc_textarea( $content ); ?></textarea>
				<p class="description">Sąrašo tipui rašykite po vieną punktą eilutėje. Teksto sekcijai – po vieną pastraipą eilutėje.</p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="g5_module_theme">Fonas</label></th>
			<td>
				<select id="g5_module_theme" name="g5_module_theme">
					<option value="light" <?php selected( $theme, 'light' ); ?>>Baltas</option>
					<option value="paper" <?php selected( $theme, 'paper' ); ?>>Šviesiai pilkas</option>
					<option value="dark" <?php selected( $theme, 'dark' ); ?>>Tamsus</option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

function g5tech_render_content_module_usage_meta_box( $post ) {
	$source_id = absint( get_post_meta( $post->ID, 'g5_module_source_id', true ) );
	$is_dynamic = 'dynamic' === get_post_meta( $post->ID, 'g5_module_type', true );
	?>
	<p>Pažymėkite puslapius, kuriuose turi būti rodomas tas pats modulis.</p>
	<?php foreach ( g5tech_module_page_choices() as $page_key => $page_label ) : ?>
		<p>
			<label>
				<input type="checkbox" name="g5_module_pages[]" value="<?php echo esc_attr( $page_key ); ?>" <?php checked( g5tech_module_is_on_page( $post->ID, $page_key ) ); ?>>
				<?php echo esc_html( $page_label ); ?>
			</label>
		</p>
	<?php endforeach; ?>
	<hr>
	<p><strong>Susiejimo principas</strong></p>
	<p class="description">Tas pats modulis visuose pažymėtuose puslapiuose atsinaujina kartu.</p>
	<?php if ( $post->ID && ! $is_dynamic ) : ?>
		<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=g5tech_duplicate_module&module_id=' . $post->ID ), 'g5tech_duplicate_module_' . $post->ID ) ); ?>">Sukurti nepriklausomą kopiją</a></p>
	<?php endif; ?>
	<?php if ( $is_dynamic ) : ?>
		<p class="description">Dinaminė sekcija visada naudoja bendrus duomenis, todėl nepriklausoma jos kopija nekuriama.</p>
	<?php endif; ?>
	<?php if ( $source_id ) : ?>
		<p class="description">Tai nepriklausoma <a href="<?php echo esc_url( get_edit_post_link( $source_id ) ); ?>">modulio Nr. <?php echo (int) $source_id; ?></a> kopija. Originalo pakeitimai jos nebeatnaujina.</p>
	<?php endif; ?>
	<?php
}

function g5tech_save_content_module( $post_id ) {
	if (
		! isset( $_POST['g5tech_module_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['g5tech_module_nonce'] ) ), 'g5tech_save_content_module' )
		|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
		|| 'g5_module' !== get_post_type( $post_id )
		|| ! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	$type = isset( $_POST['g5_module_type'] ) ? sanitize_key( wp_unslash( $_POST['g5_module_type'] ) ) : 'list';
	$type = in_array( $type, array( 'list', 'text', 'dynamic' ), true ) ? $type : 'list';
	$theme = isset( $_POST['g5_module_theme'] ) ? sanitize_key( wp_unslash( $_POST['g5_module_theme'] ) ) : 'light';
	$theme = in_array( $theme, array( 'light', 'paper', 'dark' ), true ) ? $theme : 'light';

	update_post_meta( $post_id, 'g5_module_type', $type );

	if ( 'dynamic' !== $type ) {
		update_post_meta( $post_id, 'g5_module_eyebrow', sanitize_text_field( wp_unslash( $_POST['g5_module_eyebrow'] ?? '' ) ) );
		update_post_meta( $post_id, 'g5_module_heading', sanitize_text_field( wp_unslash( $_POST['g5_module_heading'] ?? '' ) ) );
		update_post_meta( $post_id, 'g5_module_lead', sanitize_textarea_field( wp_unslash( $_POST['g5_module_lead'] ?? '' ) ) );
		update_post_meta( $post_id, 'g5_module_content', sanitize_textarea_field( wp_unslash( $_POST['g5_module_content'] ?? '' ) ) );
		update_post_meta( $post_id, 'g5_module_theme', $theme );
	}

	$pages = isset( $_POST['g5_module_pages'] ) ? (array) wp_unslash( $_POST['g5_module_pages'] ) : array();
	g5tech_set_module_pages( $post_id, $pages );
}
add_action( 'save_post_g5_module', 'g5tech_save_content_module' );

function g5tech_add_module_to_page() {
	$page_key = sanitize_key( wp_unslash( $_POST['page_key'] ?? '' ) );
	$module_id = absint( $_POST['module_id'] ?? 0 );
	$mode      = sanitize_key( wp_unslash( $_POST['module_mode'] ?? 'linked' ) );

	if (
		! g5tech_user_can_manage_module_page( $page_key )
		|| ! $module_id
		|| 'g5_module' !== get_post_type( $module_id )
		|| 'publish' !== get_post_status( $module_id )
	) {
		wp_die( esc_html__( 'Modulio įkelti nepavyko.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_add_module_to_' . $page_key );

	if ( 'copy' === $mode ) {
		$copy_id = g5tech_create_content_module_copy( $module_id );

		if ( is_wp_error( $copy_id ) ) {
			wp_die( esc_html( $copy_id->get_error_message() ) );
		}

		g5tech_set_module_page_active( $copy_id, $page_key, true );
		wp_safe_redirect(
			add_query_arg(
				array(
					'g5tech_duplicated' => '1',
					'g5tech_page_key'   => $page_key,
				),
				admin_url( 'post.php?post=' . $copy_id . '&action=edit' )
			)
		);
		exit;
	}

	g5tech_set_module_page_active( $module_id, $page_key, true );
	wp_safe_redirect( add_query_arg( 'module_added', '1', g5tech_module_page_admin_url( $page_key ) ) );
	exit;
}
add_action( 'admin_post_g5tech_add_module_to_page', 'g5tech_add_module_to_page' );

function g5tech_remove_module_from_page() {
	$page_key = sanitize_key( wp_unslash( $_POST['page_key'] ?? '' ) );
	$module_id = absint( $_POST['module_id'] ?? 0 );

	if ( ! g5tech_user_can_manage_module_page( $page_key ) || ! $module_id ) {
		wp_die( esc_html__( 'Modulio pašalinti nepavyko.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_remove_module_from_' . $page_key );
	g5tech_set_module_page_active( $module_id, $page_key, false );
	wp_safe_redirect( add_query_arg( 'module_removed', '1', g5tech_module_page_admin_url( $page_key ) ) );
	exit;
}
add_action( 'admin_post_g5tech_remove_module_from_page', 'g5tech_remove_module_from_page' );

function g5tech_reorder_page_modules() {
	$page_key = sanitize_key( wp_unslash( $_POST['page_key'] ?? '' ) );

	if ( ! g5tech_user_can_manage_module_page( $page_key ) ) {
		wp_die( esc_html__( 'Modulių tvarkos pakeisti nepavyko.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_reorder_page_modules_' . $page_key );

	$submitted  = sanitize_text_field( wp_unslash( $_POST['module_order'] ?? '' ) );
	$order      = array_values( array_filter( array_map( 'absint', explode( ',', $submitted ) ) ) );
	g5tech_set_page_module_order( $page_key, $order );

	wp_safe_redirect( add_query_arg( 'module_ordered', '1', g5tech_module_page_admin_url( $page_key ) ) );
	exit;
}
add_action( 'admin_post_g5tech_reorder_page_modules', 'g5tech_reorder_page_modules' );

function g5tech_delete_module_from_library() {
	$page_key  = sanitize_key( wp_unslash( $_POST['page_key'] ?? '' ) );
	$module_id = absint( $_POST['module_id'] ?? 0 );

	if (
		! g5tech_user_can_manage_module_page( $page_key )
		|| ! $module_id
		|| 'g5_module' !== get_post_type( $module_id )
		|| ! current_user_can( 'delete_post', $module_id )
	) {
		wp_die( esc_html__( 'Modulio ištrinti nepavyko.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_delete_module_from_library_' . $module_id );
	g5tech_remove_module_from_all_pages( $module_id );

	if ( ! wp_trash_post( $module_id ) ) {
		wp_die( esc_html__( 'Modulio perkelti į šiukšlinę nepavyko.', '5gtech-core' ) );
	}

	wp_safe_redirect( add_query_arg( 'module_deleted', '1', g5tech_module_page_admin_url( $page_key ) ) );
	exit;
}
add_action( 'admin_post_g5tech_delete_module_from_library', 'g5tech_delete_module_from_library' );

function g5tech_cleanup_deleted_module_placements( $post_id ) {
	if ( 'g5_module' === get_post_type( $post_id ) ) {
		g5tech_remove_module_from_all_pages( $post_id );
	}
}
add_action( 'trashed_post', 'g5tech_cleanup_deleted_module_placements' );
add_action( 'before_delete_post', 'g5tech_cleanup_deleted_module_placements' );

function g5tech_duplicate_content_module() {
	$source_id = absint( $_GET['module_id'] ?? 0 );

	if ( ! $source_id || 'g5_module' !== get_post_type( $source_id ) || ! current_user_can( 'edit_post', $source_id ) ) {
		wp_die( esc_html__( 'Modulio nukopijuoti nepavyko.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_duplicate_module_' . $source_id );

	$copy_id = g5tech_create_content_module_copy( $source_id );

	if ( is_wp_error( $copy_id ) ) {
		wp_die( esc_html( $copy_id->get_error_message() ) );
	}

	wp_safe_redirect(
		add_query_arg(
			'g5tech_duplicated',
			'1',
			admin_url( 'post.php?post=' . $copy_id . '&action=edit' )
		)
	);
	exit;
}
add_action( 'admin_post_g5tech_duplicate_module', 'g5tech_duplicate_content_module' );

function g5tech_content_module_row_actions( $actions, $post ) {
	if ( 'g5_module' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
		return $actions;
	}

	if ( 'dynamic' !== get_post_meta( $post->ID, 'g5_module_type', true ) ) {
		$actions['g5tech_duplicate'] = sprintf(
			'<a href="%s">Nepriklausoma kopija</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=g5tech_duplicate_module&module_id=' . $post->ID ), 'g5tech_duplicate_module_' . $post->ID ) )
		);
	}

	return $actions;
}
add_filter( 'post_row_actions', 'g5tech_content_module_row_actions', 10, 2 );

function g5tech_page_module_row_action( $actions, $post ) {
	if ( 'page' !== $post->post_type ) {
		return $actions;
	}

	$page_key = g5tech_module_page_key_for_slug( $post->post_name );

	if ( ! $page_key || ! g5tech_user_can_manage_module_page( $page_key ) ) {
		return $actions;
	}

	$actions['g5tech_modules'] = sprintf(
		'<a href="%s">Turinio moduliai</a>',
		esc_url( g5tech_module_page_admin_url( $page_key ) )
	);

	return $actions;
}
add_filter( 'page_row_actions', 'g5tech_page_module_row_action', 20, 2 );

function g5tech_redirect_managed_page_editor_to_modules() {
	if (
		! isset( $_GET['post'], $_GET['action'] )
		|| 'edit' !== sanitize_key( wp_unslash( $_GET['action'] ) )
	) {
		return;
	}

	$post_id = absint( $_GET['post'] );

	if ( 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	$page_key = g5tech_module_page_key_for_slug( get_post_field( 'post_name', $post_id ) );

	if ( ! $page_key || ! g5tech_user_can_manage_module_page( $page_key ) ) {
		return;
	}

	wp_safe_redirect( g5tech_module_page_admin_url( $page_key ) );
	exit;
}
add_action( 'load-post.php', 'g5tech_redirect_managed_page_editor_to_modules', 5 );

function g5tech_replace_managed_page_edit_action( $actions, $post ) {
	if ( 'page' !== $post->post_type ) {
		return $actions;
	}

	$page_key = g5tech_module_page_key_for_slug( $post->post_name );

	if ( ! $page_key || ! g5tech_user_can_manage_module_page( $page_key ) ) {
		return $actions;
	}

	$actions['edit'] = sprintf(
		'<a href="%s">Redaguoti puslapį</a>',
		esc_url( g5tech_module_page_admin_url( $page_key ) )
	);

	return $actions;
}
add_filter( 'page_row_actions', 'g5tech_replace_managed_page_edit_action', 5, 2 );

function g5tech_content_module_admin_notice() {
	$screen = get_current_screen();

	if ( empty( $_GET['g5tech_duplicated'] ) || ! $screen || 'g5_module' !== $screen->post_type ) {
		return;
	}
	?>
	<div class="notice notice-success is-dismissible">
		<p>Sukurta nepriklausoma modulio kopija. Pasirinkite jos puslapį, prireikus pakeiskite turinį ir publikuokite.</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'g5tech_content_module_admin_notice' );

function g5tech_content_module_columns( $columns ) {
	return array(
		'cb'       => $columns['cb'],
		'title'    => 'Modulis',
		'type'     => 'Tipas',
		'pages'    => 'Naudojamas puslapiuose',
		'modified' => 'Atnaujintas',
	);
}
add_filter( 'manage_g5_module_posts_columns', 'g5tech_content_module_columns' );

function g5tech_content_module_column( $column, $post_id ) {
	if ( 'type' === $column ) {
		$type = get_post_meta( $post_id, 'g5_module_type', true );

		if ( 'dynamic' === $type ) {
			echo 'Dinaminė sekcija';
		} elseif ( 'text' === $type ) {
			echo 'Teksto sekcija';
		} else {
			echo 'Sąrašas';
		}
	} elseif ( 'pages' === $column ) {
		$pages = array();

		foreach ( g5tech_module_page_choices() as $page_key => $page_label ) {
			if ( g5tech_module_is_on_page( $post_id, $page_key ) ) {
				$pages[] = $page_label;
			}
		}

		echo $pages ? esc_html( implode( ', ', $pages ) ) : '–';
	} elseif ( 'modified' === $column ) {
		echo esc_html( get_the_modified_date( 'Y-m-d H:i', $post_id ) );
	}
}
add_action( 'manage_g5_module_posts_custom_column', 'g5tech_content_module_column', 10, 2 );

function g5tech_maybe_create_training_topics_module() {
	if ( get_option( 'g5tech_content_modules_migrated_1' ) ) {
		return;
	}

	$existing = g5tech_get_source_module( 'training_topics' );

	if ( $existing ) {
		update_option( 'g5tech_content_modules_migrated_1', 1, false );
		return;
	}

	$training = function_exists( 'g5tech_training_page_content' )
		? g5tech_training_page_content()
		: array();
	$module_id = wp_insert_post(
		array(
			'post_type'   => 'g5_module',
			'post_status' => 'publish',
			'post_title'  => 'Mokymų temos',
		),
		true
	);

	if ( is_wp_error( $module_id ) ) {
		return;
	}

	update_post_meta( $module_id, 'g5_module_type', 'list' );
	update_post_meta( $module_id, 'g5_module_eyebrow', $training['topics_eyebrow'] ?? 'Programa' );
	update_post_meta( $module_id, 'g5_module_heading', $training['topics_title'] ?? 'Mokymų temos.' );
	update_post_meta( $module_id, 'g5_module_lead', '' );
	update_post_meta( $module_id, 'g5_module_content', $training['topics'] ?? '' );
	update_post_meta( $module_id, 'g5_module_theme', 'light' );
	update_post_meta( $module_id, 'g5_module_source_key', 'training_topics' );
	g5tech_set_module_pages( $module_id, array( 'training' ) );
	update_option( 'g5tech_content_modules_migrated_1', 1, false );
}
add_action( 'admin_init', 'g5tech_maybe_create_training_topics_module', 20 );
