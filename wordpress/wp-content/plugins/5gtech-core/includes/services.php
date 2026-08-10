<?php
/**
 * Paslaugų turinio modelis.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_service_type() {
	$labels = array(
		'name'               => 'Paslaugos',
		'singular_name'      => 'Paslauga',
		'add_new'            => 'Pridėti paslaugą',
		'add_new_item'       => 'Pridėti naują paslaugą',
		'edit_item'          => 'Redaguoti paslaugą',
		'new_item'           => 'Nauja paslauga',
		'view_item'          => 'Peržiūrėti paslaugą',
		'search_items'       => 'Ieškoti paslaugų',
		'not_found'          => 'Paslaugų nerasta',
		'not_found_in_trash' => 'Šiukšlinėje paslaugų nėra',
		'menu_name'          => 'Paslaugos',
	);

	register_post_type(
		'g5_service',
		array(
			'labels'        => $labels,
			'public'        => true,
			'menu_icon'     => 'dashicons-hammer',
			'menu_position' => 20,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'paslaugos',
				'with_front' => false,
			),
			'show_in_rest'  => false,
			'supports'      => array( 'title', 'thumbnail', 'revisions', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'g5tech_register_service_type' );

function g5tech_default_service_content() {
	return implode(
		"\n",
		array(
			'<!-- wp:g5tech/service-hero /-->',
			'<!-- wp:g5tech/service-scope /-->',
			'<!-- wp:g5tech/service-process /-->',
			'<!-- wp:g5tech/service-equipment /-->',
			'<!-- wp:g5tech/service-faq /-->',
			'<!-- wp:g5tech/service-cta /-->',
		)
	);
}

function g5tech_set_default_service_content( $data, $postarr ) {
	if (
		'g5_service' === $data['post_type']
		&& empty( $data['post_content'] )
		&& empty( $postarr['ID'] )
	) {
		$data['post_content'] = g5tech_default_service_content();
	}

	return $data;
}
add_filter( 'wp_insert_post_data', 'g5tech_set_default_service_content', 10, 2 );

function g5tech_service_fields() {
	return array(
		'g5_service_category' => array(
			'label'       => 'Paslaugos kategorija',
			'type'        => 'text',
			'description' => 'Pvz. Telekomunikacijos',
		),
		'g5_service_summary' => array(
			'label'       => 'Trumpas paslaugos aprašymas',
			'type'        => 'textarea',
			'description' => '2–3 sakiniai, matomi paslaugos puslapio pradžioje.',
		),
		'g5_service_work' => array(
			'label'       => 'Atliekami darbai',
			'type'        => 'textarea',
			'description' => 'Vienas darbas vienoje eilutėje.',
		),
		'g5_service_card_title' => array(
			'label'       => 'Trumpas pavadinimas paslaugų sąraše',
			'type'        => 'text',
			'description' => 'Neprivalomas. Palikus tuščią, bus naudojamas pagrindinis paslaugos pavadinimas.',
		),
		'g5_service_card_summary' => array(
			'label'       => 'Trumpas aprašymas paslaugų sąraše',
			'type'        => 'textarea',
			'description' => 'Vienas trumpas sakinys paslaugos kortelei.',
		),
		'g5_service_cta_title' => array(
			'label'       => 'Baigiamojo kvietimo antraštė',
			'type'        => 'text',
			'description' => 'Palikus tuščią, bus rodoma bendra antraštė.',
		),
		'g5_service_cta_text' => array(
			'label'       => 'Baigiamojo kvietimo tekstas',
			'type'        => 'textarea',
			'description' => 'Palikus tuščią, bus rodomas bendras tekstas.',
		),
	);
}

function g5tech_register_service_meta() {
	foreach ( g5tech_service_fields() as $key => $field ) {
		register_post_meta(
			'g5_service',
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => function() {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_post_meta(
		'g5_service',
		'g5_service_partners',
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'sanitize_callback' => 'g5tech_sanitize_partner_ids',
			'auth_callback'     => function() {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'g5tech_register_service_meta' );

function g5tech_sanitize_partner_ids( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	return array_values(
		array_unique(
			array_filter( array_map( 'absint', $value ) )
		)
	);
}

function g5tech_add_service_meta_box() {
	add_meta_box(
		'g5tech-service-details',
		'Paslaugos informacija',
		'g5tech_render_service_meta_box',
		'g5_service',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'g5tech_add_service_meta_box' );

/**
 * Didelį paslaugos turinio bloką visuomet laiko plačiame pagrindiniame stulpelyje.
 *
 * WordPress įsimena, jei vartotojas netyčia nutempia bloką į šoninę juostą,
 * todėl senas išdėstymas čia pataisomas kiekvienam administratoriui.
 */
function g5tech_service_meta_box_order( $order ) {
	$order = is_array( $order ) ? $order : array();

	foreach ( array( 'normal', 'side', 'advanced' ) as $column ) {
		$boxes = array_filter(
			array_map(
				'trim',
				explode( ',', (string) ( $order[ $column ] ?? '' ) )
			)
		);
		$boxes = array_values(
			array_diff( $boxes, array( 'g5tech-service-details' ) )
		);
		$order[ $column ] = implode( ',', $boxes );
	}

	$normal          = array_filter( explode( ',', (string) $order['normal'] ) );
	array_unshift( $normal, 'g5tech-service-details' );
	$order['normal'] = implode( ',', array_unique( $normal ) );

	return $order;
}
add_filter( 'get_user_option_meta-box-order_g5_service', 'g5tech_service_meta_box_order' );

function g5tech_service_visible_meta_boxes( $hidden, $screen ) {
	if ( $screen && 'g5_service' === $screen->id ) {
		$hidden = array_values(
			array_diff( (array) $hidden, array( 'g5tech-service-details' ) )
		);
	}

	return $hidden;
}
add_filter( 'hidden_meta_boxes', 'g5tech_service_visible_meta_boxes', 10, 2 );

function g5tech_render_service_meta_box( $post ) {
	wp_nonce_field( 'g5tech_save_service', 'g5tech_service_nonce' );

	$translation_content = array(
		'title' => get_the_title( $post ),
	);

	foreach ( array_keys( g5tech_service_fields() ) as $field_key ) {
		$translation_content[ $field_key ] = get_post_meta( $post->ID, $field_key, true );
	}

	g5tech_render_admin_content_translation_context(
		'service_' . $post->ID,
		$translation_content,
		array(
			'selector'  => '#title, #g5tech-service-details input[type="text"], #g5tech-service-details textarea',
			'container' => '#g5tech-service-details .inside',
		)
	);

	echo '<p>Čia keičiamas paslaugos turinys. Puslapio struktūra ir dizainas lieka vienodi.</p>';

	foreach ( g5tech_service_fields() as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<div style="margin: 0 0 20px;">';
		printf(
			'<label for="%1$s" style="display:block;font-weight:600;margin-bottom:6px;">%2$s</label>',
			esc_attr( $key ),
			esc_html( $field['label'] )
		);

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%1$s" name="%1$s" rows="%2$d" style="width:100%%;">%3$s</textarea>',
				esc_attr( $key ),
				'g5_service_work' === $key ? 8 : 4,
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="text" id="%1$s" name="%1$s" value="%2$s" style="width:100%%;">',
				esc_attr( $key ),
				esc_attr( $value )
			);
		}

		printf(
			'<p class="description">%s</p>',
			esc_html( $field['description'] )
		);
		echo '</div>';
	}

	echo '<div style="margin: 24px 0;">';
	echo '<h3 style="margin-bottom:8px;">Dažniausi klausimai</h3>';
	echo '<p class="description">Klausimai tvarkomi atskiroje administracijos skiltyje „Dažniausi klausimai“. Vieną klausimą galima rodyti prie kelių paslaugų.</p>';
	printf(
		'<p><a class="button" href="%s">Tvarkyti dažniausius klausimus</a></p>',
		esc_url( admin_url( 'edit.php?post_type=g5_faq' ) )
	);
	echo '</div>';

	$manufacturers = array_merge(
		g5tech_get_partners( 'manufacturer' ),
		g5tech_get_partners( 'equipment' )
	);
	$selected      = g5tech_sanitize_partner_ids(
		get_post_meta( $post->ID, 'g5_service_partners', true )
	);

	echo '<div style="margin: 28px 0 8px;">';
	echo '<h3 style="margin-bottom:8px;">Įranga, gamintojai ir sistemos</h3>';
	echo '<p class="description" style="margin-bottom:12px;">Pasirinkite elementus iš bendro katalogo. Pavadinimai ir logotipai keičiami skiltyje „Partneriai ir įranga“.</p>';

	if ( ! $manufacturers ) {
		echo '<p>Gamintojų katalogas tuščias.</p>';
	} else {
		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px 18px;">';
		foreach ( $manufacturers as $manufacturer ) {
			printf(
				'<label style="display:flex;align-items:center;gap:8px;padding:8px;border:1px solid #dcdcde;background:#fff;"><input type="checkbox" name="g5_service_partners[]" value="%1$d" %2$s> %3$s</label>',
				(int) $manufacturer->ID,
				checked( in_array( $manufacturer->ID, $selected, true ), true, false ),
				esc_html( get_the_title( $manufacturer ) )
			);
		}
		echo '</div>';
	}
	echo '</div>';
}

function g5tech_save_service_meta( $post_id ) {
	if (
		! isset( $_POST['g5tech_service_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['g5tech_service_nonce'] ) ),
			'g5tech_save_service'
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

	g5tech_save_admin_content_translations( 'service_' . $post_id );

	foreach ( g5tech_service_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$value = wp_unslash( $_POST[ $key ] );
		$value = 'textarea' === $field['type']
			? sanitize_textarea_field( $value )
			: sanitize_text_field( $value );

		update_post_meta( $post_id, $key, $value );
	}

	$partner_ids = isset( $_POST['g5_service_partners'] )
		? g5tech_sanitize_partner_ids( wp_unslash( $_POST['g5_service_partners'] ) )
		: array();

	update_post_meta( $post_id, 'g5_service_partners', $partner_ids );
}
add_action( 'save_post_g5_service', 'g5tech_save_service_meta' );

function g5tech_service_lines( $key, $post_id = 0 ) {
	$value = get_post_meta( $post_id ?: get_the_ID(), $key, true );

	return array_values(
		array_filter(
			array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $value ) )
		)
	);
}

/**
 * Patikrina, ar įrašo nuotrauka yra ankstyvojo prototipo demonstracinis vaizdas.
 */
function g5tech_is_demo_attachment( $attachment_id ) {
	$file = $attachment_id ? get_attached_file( $attachment_id ) : '';

	if ( ! $file ) {
		return true;
	}

	return (bool) preg_match( '/^service-(?:demo(?:-\d+)?|team)\.(?:png|jpe?g|webp)$/i', basename( $file ) );
}

/**
 * Grąžina paslaugai skirtą numatytąją techninę iliustraciją.
 */
function g5tech_service_generated_visual( $post_id ) {
	$visuals = array(
		'mobiliojo-rysio-tinklai' => array(
			'file' => 'from-live-site/service-mobile-networks.png',
			'alt'  => 'Mobiliojo ryšio bazinė stotis lauko objekte',
		),
		'vidinio-rysio-tinklai' => array(
			'file' => 'from-live-site/service-indoor-networks.png',
			'alt'  => 'Ryšio įranga ir šviesolaidžio jungtys techninėje spintoje',
		),
		'fiksuoto-rysio-tinklai' => array(
			'file' => 'service-fixed-networks-v1.jpg',
			'alt'  => 'Šviesolaidinio ryšio skirstomoji spinta ir optinių skaidulų jungtys',
		),
		'elektros-darbai' => array(
			'file' => 'service-electrical-v1.jpg',
			'alt'  => 'Elektros skirstomieji skydai, apsaugos aparatai ir kabelių trasos',
		),
		'apsaugos-ir-stebejimo-sistemos' => array(
			'file' => 'service-security-v1.jpg',
			'alt'  => 'Vaizdo stebėjimo kameros, įeigos kontrolė ir apsaugos sistemų spinta',
		),
		'saules-elektrines' => array(
			'file' => 'from-live-site/service-solar.png',
			'alt'  => 'Antžeminė saulės elektrinė',
		),
	);
	$slug    = get_post_field( 'post_name', $post_id );

	if ( empty( $visuals[ $slug ] ) ) {
		return array();
	}

	return array(
		'url' => get_theme_file_uri( 'assets/images/' . ( false !== strpos( $visuals[ $slug ]['file'], '/' ) ? '' : 'generated/' ) . $visuals[ $slug ]['file'] ),
		'alt' => $visuals[ $slug ]['alt'],
	);
}
