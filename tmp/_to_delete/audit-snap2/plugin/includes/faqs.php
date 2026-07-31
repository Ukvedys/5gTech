<?php
/**
 * Centralizuoti dažniausiai užduodami klausimai.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_faq_type() {
	register_post_type(
		'g5_faq',
		array(
			'labels' => array(
				'name'               => 'Dažniausi klausimai',
				'singular_name'      => 'Dažniausias klausimas',
				'add_new'            => 'Pridėti klausimą',
				'add_new_item'       => 'Pridėti dažniausią klausimą',
				'edit_item'          => 'Redaguoti dažniausią klausimą',
				'new_item'           => 'Naujas dažniausias klausimas',
				'search_items'       => 'Ieškoti klausimų',
				'not_found'          => 'Klausimų nerasta',
				'not_found_in_trash' => 'Šiukšlinėje klausimų nėra',
				'menu_name'          => 'Dažniausi klausimai',
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => false,
			'menu_icon'     => 'dashicons-editor-help',
			'menu_position' => 22,
			'supports'      => array( 'title', 'page-attributes' ),
		)
	);

	register_post_meta(
		'g5_faq',
		'g5_faq_answer',
		array(
			'type'              => 'string',
			'single'            => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'g5_faq',
		'g5_faq_topic',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => 'client',
			'sanitize_callback' => 'g5tech_sanitize_faq_topic',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'g5_faq',
		'g5_faq_group',
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_key',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'g5tech_register_faq_type' );

function g5tech_sanitize_faq_topic( $value ) {
	return in_array( $value, array( 'client', 'candidate', 'general' ), true )
		? $value
		: 'client';
}

function g5tech_add_faq_meta_box() {
	add_meta_box(
		'g5tech-faq-details',
		'Klausimo informacija',
		'g5tech_render_faq_meta_box',
		'g5_faq',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_faq_meta_box' );

function g5tech_render_faq_meta_box( $post ) {
	$answer      = get_post_meta( $post->ID, 'g5_faq_answer', true );
	$topic       = get_post_meta( $post->ID, 'g5_faq_topic', true ) ?: 'client';
	$group       = get_post_meta( $post->ID, 'g5_faq_group', true );
	$service_ids = array_map( 'absint', get_post_meta( $post->ID, 'g5_faq_service', false ) );
	$services    = get_posts(
		array(
			'post_type'      => 'g5_service',
			'post_status'    => array( 'publish', 'draft' ),
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	);

	wp_nonce_field( 'g5tech_save_faq', 'g5tech_faq_nonce' );
	?>
	<p class="description">Klausimas įrašomas puslapio viršuje. Tą patį įrašą galima priskirti kelioms paslaugoms.</p>
	<p>
		<label for="g5_faq_answer"><strong>Atsakymas</strong></label>
	</p>
	<textarea class="large-text" rows="6" id="g5_faq_answer" name="g5_faq_answer"><?php echo esc_textarea( $answer ); ?></textarea>
	<p>
		<label for="g5_faq_topic"><strong>Tema</strong></label>
	</p>
	<select id="g5_faq_topic" name="g5_faq_topic">
		<option value="client" <?php selected( $topic, 'client' ); ?>>Klientams</option>
		<option value="candidate" <?php selected( $topic, 'candidate' ); ?>>Kandidatams</option>
		<option value="general" <?php selected( $topic, 'general' ); ?>>Bendra</option>
	</select>
	<p>
		<label for="g5_faq_group"><strong>Kandidatų DUK grupė</strong></label>
	</p>
	<select id="g5_faq_group" name="g5_faq_group">
		<option value="" <?php selected( $group, '' ); ?>>Netaikoma</option>
		<option value="start" <?php selected( $group, 'start' ); ?>>Darbo pradžia</option>
		<option value="travel" <?php selected( $group, 'travel' ); ?>>Komandiruotės</option>
		<option value="safety" <?php selected( $group, 'safety' ); ?>>Sauga ir priemonės</option>
		<option value="daily" <?php selected( $group, 'daily' ); ?>>Kasdienis darbas</option>
	</select>
	<?php if ( $services ) : ?>
		<fieldset style="margin-top:1.5rem">
			<legend><strong>Rodyti prie paslaugų</strong></legend>
			<?php foreach ( $services as $service ) : ?>
				<label style="display:block;margin-top:.5rem">
					<input
						type="checkbox"
						name="g5_faq_services[]"
						value="<?php echo (int) $service->ID; ?>"
						<?php checked( in_array( (int) $service->ID, $service_ids, true ) ); ?>
					>
					<?php echo esc_html( get_the_title( $service ) ); ?>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>
	<?php
}

function g5tech_save_faq_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_faq_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_faq_nonce'] ) ),
			'g5tech_save_faq'
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

	$answer = isset( $_POST['g5_faq_answer'] )
		? sanitize_textarea_field( wp_unslash( $_POST['g5_faq_answer'] ) )
		: '';
	$topic  = isset( $_POST['g5_faq_topic'] )
		? g5tech_sanitize_faq_topic( sanitize_text_field( wp_unslash( $_POST['g5_faq_topic'] ) ) )
		: 'client';
	$group  = isset( $_POST['g5_faq_group'] )
		? sanitize_key( wp_unslash( $_POST['g5_faq_group'] ) )
		: '';

	if ( ! in_array( $group, array( '', 'start', 'travel', 'safety', 'daily' ), true ) ) {
		$group = '';
	}

	update_post_meta( $post_id, 'g5_faq_answer', $answer );
	update_post_meta( $post_id, 'g5_faq_topic', $topic );
	update_post_meta( $post_id, 'g5_faq_group', $group );
	delete_post_meta( $post_id, 'g5_faq_service' );

	$service_ids = isset( $_POST['g5_faq_services'] )
		? array_unique( array_filter( array_map( 'absint', (array) wp_unslash( $_POST['g5_faq_services'] ) ) ) )
		: array();

	foreach ( $service_ids as $service_id ) {
		add_post_meta( $post_id, 'g5_faq_service', $service_id );
	}
}

function g5tech_get_faqs_by_topic( $topic, $group = '' ) {
	$meta_query = array(
		array(
			'key'   => 'g5_faq_topic',
			'value' => g5tech_sanitize_faq_topic( $topic ),
		),
	);

	if ( $group ) {
		$meta_query[] = array(
			'key'   => 'g5_faq_group',
			'value' => sanitize_key( $group ),
		);
	}

	return get_posts(
		array(
			'post_type'      => 'g5_faq',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'meta_query'     => $meta_query,
		)
	);
}
add_action( 'save_post_g5_faq', 'g5tech_save_faq_meta' );

function g5tech_get_faqs_for_service( $service_id ) {
	return get_posts(
		array(
			'post_type'      => 'g5_faq',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'meta_query'     => array(
				array(
					'key'     => 'g5_faq_service',
					'value'   => (int) $service_id,
					'compare' => '=',
					'type'    => 'NUMERIC',
				),
			),
		)
	);
}

function g5tech_faq_columns( $columns ) {
	$updated = array();

	foreach ( $columns as $key => $label ) {
		$updated[ $key ] = $label;

		if ( 'title' === $key ) {
			$updated['g5_faq_topic'] = 'Tema';
		}
	}

	return $updated;
}
add_filter( 'manage_g5_faq_posts_columns', 'g5tech_faq_columns' );

function g5tech_faq_column_content( $column, $post_id ) {
	if ( 'g5_faq_topic' !== $column ) {
		return;
	}

	$labels = array(
		'client'    => 'Klientams',
		'candidate' => 'Kandidatams',
		'general'   => 'Bendra',
	);
	$topic  = get_post_meta( $post_id, 'g5_faq_topic', true ) ?: 'client';

	echo esc_html( $labels[ $topic ] ?? $labels['client'] );
}
add_action( 'manage_g5_faq_posts_custom_column', 'g5tech_faq_column_content', 10, 2 );
