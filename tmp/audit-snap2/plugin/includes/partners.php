<?php
/**
 * Centralizuotas partnerių, operatorių ir įrangos gamintojų katalogas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_partner_type() {
	register_post_type(
		'g5_partner',
		array(
			'labels' => array(
				'name'               => 'Partneriai ir įranga',
				'singular_name'      => 'Partneris arba gamintojas',
				'add_new'            => 'Pridėti įrašą',
				'add_new_item'       => 'Pridėti partnerį arba gamintoją',
				'edit_item'          => 'Redaguoti partnerį arba gamintoją',
				'new_item'           => 'Naujas partneris arba gamintojas',
				'view_item'          => 'Peržiūrėti įrašą',
				'search_items'       => 'Ieškoti kataloge',
				'not_found'          => 'Įrašų nerasta',
				'not_found_in_trash' => 'Šiukšlinėje įrašų nėra',
				'menu_name'          => 'Partneriai ir įranga',
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => false,
			'menu_icon'    => 'dashicons-networking',
			'menu_position'=> 21,
			'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_meta(
		'g5_partner',
		'g5_partner_type',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => 'manufacturer',
			'sanitize_callback' => 'g5tech_sanitize_partner_type',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'g5_partner',
		'g5_partner_show_home',
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
add_action( 'init', 'g5tech_register_partner_type' );

function g5tech_register_partner_blocks() {
	register_block_type(
		'g5tech/partner-marquee',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH gamintojų juosta',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_partner_marquee',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_partner_blocks' );

function g5tech_sanitize_partner_type( $value ) {
	return in_array( $value, array( 'manufacturer', 'equipment', 'operator', 'partner' ), true )
		? $value
		: 'manufacturer';
}

function g5tech_add_partner_meta_box() {
	add_meta_box(
		'g5tech-partner-details',
		'Įrašo informacija',
		'g5tech_render_partner_meta_box',
		'g5_partner',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_partner_meta_box' );

function g5tech_render_partner_meta_box( $post ) {
	$type      = get_post_meta( $post->ID, 'g5_partner_type', true ) ?: 'manufacturer';
	$show_home = metadata_exists( 'post', $post->ID, 'g5_partner_show_home' )
		? (bool) get_post_meta( $post->ID, 'g5_partner_show_home', true )
		: true;
	wp_nonce_field( 'g5tech_save_partner', 'g5tech_partner_nonce' );
	?>
	<p>
		<label for="g5_partner_type"><strong>Tipas</strong></label>
	</p>
	<select id="g5_partner_type" name="g5_partner_type">
		<option value="manufacturer" <?php selected( $type, 'manufacturer' ); ?>>Įrangos gamintojas</option>
		<option value="equipment" <?php selected( $type, 'equipment' ); ?>>Įranga arba sistema</option>
		<option value="operator" <?php selected( $type, 'operator' ); ?>>Operatorius</option>
		<option value="partner" <?php selected( $type, 'partner' ); ?>>Kitas partneris</option>
	</select>
	<p>
		<label>
			<input type="checkbox" name="g5_partner_show_home" value="1" <?php checked( $show_home ); ?>>
			<strong>Rodyti tituliniame puslapyje</strong>
		</label>
	</p>
	<p class="description">
		Pavadinimas įrašomas viršuje, logotipas įkeliamas skiltyje „Specialusis paveikslėlis“.
		Įkėlę arba pakeitę logotipą, dešinėje paspauskite „Update“.
		Nebenaudojamą įrašą galima palikti juodraščiu.
	</p>
	<?php
}

function g5tech_save_partner_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_partner_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_partner_nonce'] ) ),
			'g5tech_save_partner'
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

	$type = isset( $_POST['g5_partner_type'] )
		? g5tech_sanitize_partner_type( sanitize_text_field( wp_unslash( $_POST['g5_partner_type'] ) ) )
		: 'manufacturer';

	update_post_meta( $post_id, 'g5_partner_type', $type );
	update_post_meta( $post_id, 'g5_partner_show_home', isset( $_POST['g5_partner_show_home'] ) ? '1' : '0' );
}
add_action( 'save_post_g5_partner', 'g5tech_save_partner_meta' );

function g5tech_partner_type_label( $type ) {
	$labels = array(
		'manufacturer' => 'Įrangos gamintojas',
		'equipment'    => 'Įranga arba sistema',
		'operator'     => 'Operatorius',
		'partner'      => 'Kitas partneris',
	);

	return $labels[ $type ] ?? $labels['manufacturer'];
}

function g5tech_partner_columns( $columns ) {
	$updated = array();

	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated['g5_partner_type'] = 'Tipas';
		}
	}

	return $updated;
}
add_filter( 'manage_g5_partner_posts_columns', 'g5tech_partner_columns' );

function g5tech_partner_column_content( $column, $post_id ) {
	if ( 'g5_partner_type' !== $column ) {
		return;
	}

	echo esc_html(
		g5tech_partner_type_label(
			get_post_meta( $post_id, 'g5_partner_type', true )
		)
	);
}
add_action( 'manage_g5_partner_posts_custom_column', 'g5tech_partner_column_content', 10, 2 );

function g5tech_partner_title_placeholder( $placeholder, $post ) {
	if ( 'g5_partner' === $post->post_type ) {
		return 'Gamintojo, operatoriaus arba partnerio pavadinimas';
	}

	return $placeholder;
}
add_filter( 'enter_title_here', 'g5tech_partner_title_placeholder', 10, 2 );

function g5tech_get_partners( $type = '', $include = array(), $home_only = false ) {
	$args = array(
		'post_type'      => 'g5_partner',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'order'          => 'ASC',
	);

	$meta_query = array();

	if ( $type ) {
		$meta_query[] = array(
			'key'   => 'g5_partner_type',
			'value' => $type,
		);
	}

	if ( $home_only ) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key'     => 'g5_partner_show_home',
				'value'   => '1',
				'compare' => '=',
			),
			array(
				'key'     => 'g5_partner_show_home',
				'compare' => 'NOT EXISTS',
			),
		);
	}

	if ( $meta_query ) {
		$args['meta_query'] = count( $meta_query ) > 1
			? array_merge( array( 'relation' => 'AND' ), $meta_query )
			: $meta_query;
	}

	if ( $include ) {
		$args['post__in'] = array_map( 'absint', $include );
		$args['orderby']  = 'post__in';
	}

	return get_posts( $args );
}

function g5tech_render_partner_marquee() {
	$manufacturers = g5tech_get_partners( 'manufacturer', array(), true );

	if ( ! $manufacturers ) {
		return '';
	}

	ob_start();
	?>
	<section class="g5-equipment-experience g5-grid-lines g5-grid-lines--dark" aria-labelledby="g5-equipment-experience-title">
		<div class="g5-container">
			<div class="g5-equipment-experience__head g5-grid">
				<div>
					<div class="g5-eyebrow">Partneriai</div>
					<h2 class="g5-display-md" id="g5-equipment-experience-title">Gamintojai, su kurių įranga dirbame.</h2>
				</div>
			</div>
			<div class="g5-equipment-marquee" aria-label="Įrangos gamintojai, su kurių įranga komanda turi praktinės patirties">
				<div class="g5-equipment-marquee__track">
					<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
						<div class="g5-equipment-marquee__group" <?php echo 1 === $copy ? 'aria-hidden="true"' : ''; ?>>
							<?php foreach ( $manufacturers as $manufacturer ) : ?>
								<span class="g5-equipment-marquee__item"><?php echo esc_html( get_the_title( $manufacturer ) ); ?></span>
							<?php endforeach; ?>
						</div>
					<?php endfor; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}
