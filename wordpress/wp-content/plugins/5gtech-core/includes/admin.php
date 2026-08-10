<?php
/**
 * Klientui skirtos rolės ir supaprastinta WordPress administracija.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_content_type_capability_map() {
	return array(
		'g5_partner' => array( 'g5_partner', 'g5_partners' ),
		'g5_module'  => array( 'g5_module', 'g5_modules' ),
		'g5_service' => array( 'g5_service', 'g5_services' ),
		'g5_project' => array( 'g5_project', 'g5_projects' ),
		'g5_faq'     => array( 'g5_faq', 'g5_faqs' ),
		'g5_team'    => array( 'g5_team_member', 'g5_team_members' ),
		'g5_job'     => array( 'g5_job', 'g5_jobs' ),
	);
}

function g5tech_filter_post_type_capabilities( $args, $post_type ) {
	$map = g5tech_content_type_capability_map();

	if ( isset( $map[ $post_type ] ) ) {
		$args['capability_type'] = $map[ $post_type ];
		$args['map_meta_cap']    = true;
	}

	return $args;
}
add_filter( 'register_post_type_args', 'g5tech_filter_post_type_capabilities', 10, 2 );

function g5tech_primitive_capabilities( $singular, $plural ) {
	return array(
		"edit_{$plural}",
		"edit_others_{$plural}",
		"publish_{$plural}",
		"read_private_{$plural}",
		"delete_{$plural}",
		"delete_private_{$plural}",
		"delete_published_{$plural}",
		"delete_others_{$plural}",
		"edit_private_{$plural}",
		"edit_published_{$plural}",
	);
}

function g5tech_all_custom_capabilities() {
	$capabilities = array();

	foreach ( g5tech_content_type_capability_map() as $types ) {
		$capabilities = array_merge(
			$capabilities,
			g5tech_primitive_capabilities( $types[0], $types[1] )
		);
	}

	return array_values( array_unique( $capabilities ) );
}

function g5tech_add_capabilities( $role, $capabilities ) {
	if ( ! $role ) {
		return;
	}

	foreach ( $capabilities as $capability ) {
		$role->add_cap( $capability );
	}
}

function g5tech_remove_capabilities( $role, $capabilities ) {
	if ( ! $role ) {
		return;
	}

	foreach ( $capabilities as $capability ) {
		$role->remove_cap( $capability );
	}
}

function g5tech_sync_editor_roles() {
	$editor_role = get_role( 'editor' );
	$content_role = get_role( 'g5_content_editor' );
	$hr_role      = get_role( 'g5_hr_editor' );

	if ( ! $content_role ) {
		$content_role = add_role(
			'g5_content_editor',
			'5G TECH turinio redaktorius',
			$editor_role ? $editor_role->capabilities : array( 'read' => true )
		);
	}

	if ( ! $hr_role ) {
		$hr_role = add_role(
			'g5_hr_editor',
			'5G TECH personalo redaktorius',
			array(
				'read'         => true,
				'upload_files' => true,
			)
		);
	}

	if ( ! $content_role || ! $hr_role ) {
		return;
	}

	if ( $editor_role && $content_role ) {
		foreach ( $editor_role->capabilities as $capability => $granted ) {
			if ( $granted ) {
				$content_role->add_cap( $capability );
			}
		}
	}

	$content_role->remove_cap( 'moderate_comments' );
	$content_role->remove_cap( 'manage_links' );

	// Puslapių turinys po perkėlimo gyvena blokų redaktoriuje, todėl abu
	// redaktoriai gali keisti puslapius, bet negali jų trinti.
	$page_edit_caps = array(
		'edit_pages',
		'edit_others_pages',
		'publish_pages',
		'read_private_pages',
		'edit_private_pages',
		'edit_published_pages',
	);
	g5tech_add_capabilities( $content_role, $page_edit_caps );
	g5tech_add_capabilities( $hr_role, $page_edit_caps );
	g5tech_remove_capabilities(
		$content_role,
		array(
			'delete_pages',
			'delete_private_pages',
			'delete_published_pages',
			'delete_others_pages',
		)
	);
	$content_role->add_cap( 'manage_g5tech_settings' );
	$content_role->add_cap( 'read_g5tech_guide' );
	$hr_role->add_cap( 'read_g5tech_guide' );

	$all_custom_caps = g5tech_all_custom_capabilities();
	g5tech_remove_capabilities( $content_role, $all_custom_caps );
	g5tech_remove_capabilities( $hr_role, $all_custom_caps );

	$content_types = array( 'g5_partner', 'g5_module', 'g5_service', 'g5_project', 'g5_faq' );
	$hr_types      = array( 'g5_module', 'g5_team', 'g5_job', 'g5_faq' );
	$type_map      = g5tech_content_type_capability_map();

	foreach ( $content_types as $post_type ) {
		g5tech_add_capabilities(
			$content_role,
			g5tech_primitive_capabilities( $type_map[ $post_type ][0], $type_map[ $post_type ][1] )
		);
	}

	foreach ( $hr_types as $post_type ) {
		g5tech_add_capabilities(
			$hr_role,
			g5tech_primitive_capabilities( $type_map[ $post_type ][0], $type_map[ $post_type ][1] )
		);
	}

	$administrator = get_role( 'administrator' );

	if ( $administrator ) {
		g5tech_add_capabilities( $administrator, $all_custom_caps );
		$administrator->add_cap( 'manage_g5tech_settings' );
		$administrator->add_cap( 'read_g5tech_guide' );
	}

	update_option( 'g5tech_roles_version', '4' );
}

function g5tech_maybe_sync_editor_roles() {
	if ( '4' !== get_option( 'g5tech_roles_version' ) ) {
		g5tech_sync_editor_roles();
	}
}
add_action( 'init', 'g5tech_maybe_sync_editor_roles', 1 );

function g5tech_settings_option_capability() {
	return 'manage_g5tech_settings';
}
add_filter( 'option_page_capability_g5tech_settings_group', 'g5tech_settings_option_capability' );

function g5tech_user_has_role( $role ) {
	$user = wp_get_current_user();

	return in_array( $role, (array) $user->roles, true );
}

function g5tech_add_guide_page() {
	add_menu_page(
		'5G TECH darbo atmintinė',
		'Darbo atmintinė',
		'read_g5tech_guide',
		'g5tech-guide',
		'g5tech_render_guide_page',
		'dashicons-welcome-learn-more',
		3
	);
}
add_action( 'admin_menu', 'g5tech_add_guide_page', 20 );

function g5tech_add_training_content_page() {
	add_submenu_page(
		'g5tech-settings',
		'Mokymų puslapio turinys',
		'Mokymų puslapis',
		'manage_g5tech_settings',
		'g5tech-training-content',
		'g5tech_render_training_content_page'
	);
}
// Ekranas išjungtas: Mokymų puslapis redaguojamas Puslapiai → blokų redaktoriuje.

function g5tech_training_admin_fields() {
	return array(
		'Puslapio pradžia' => array(
			'hero_eyebrow' => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
			'hero_title'   => array( 'label' => 'Pagrindinė antraštė', 'type' => 'text' ),
			'hero_lead'    => array( 'label' => 'Įžangos tekstas', 'type' => 'textarea' ),
		),
		'Mokymų programa' => array(
			'topics_eyebrow' => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
			'topics_title'   => array( 'label' => 'Sekcijos antraštė', 'type' => 'text' ),
			'topics'         => array( 'label' => 'Mokymų temos', 'type' => 'lines', 'help' => 'Viena tema vienoje eilutėje.' ),
		),
		'Naudojama įranga' => array(
			'equipment_eyebrow' => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
			'equipment_title'   => array( 'label' => 'Sekcijos antraštė', 'type' => 'text' ),
		),
		'Mokymų aplinka' => array(
			'image_eyebrow' => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
			'image_title'   => array( 'label' => 'Sekcijos antraštė', 'type' => 'text' ),
			'image_alt'     => array( 'label' => 'Nuotraukos aprašymas', 'type' => 'text', 'help' => 'Trumpai aprašykite, kas matoma nuotraukoje.' ),
		),
		'Baigiamasis kvietimas' => array(
			'cta_eyebrow'      => array( 'label' => 'Trumpa žyma', 'type' => 'text' ),
			'cta_title'        => array( 'label' => 'Antraštė', 'type' => 'text' ),
			'cta_button_label' => array( 'label' => 'Mygtuko tekstas', 'type' => 'text' ),
			'cta_button_url'   => array(
				'label' => 'Mygtuko nuoroda',
				'type'  => 'text',
				'help'  => 'Vidiniam puslapiui galima naudoti trumpą adresą, pvz. /karjera/.',
			),
		),
	);
}

function g5tech_training_sortable_sections() {
	return array(
		'topics'    => 'Mokymų programa',
		'equipment' => 'Naudojama įranga',
		'image'     => 'Mokymų aplinka',
	);
}

function g5tech_render_training_admin_field( $content, $key, $field ) {
	$value = isset( $content[ $key ] ) ? $content[ $key ] : '';
	$rows  = 'lines' === $field['type'] ? 8 : 4;
	?>
	<div class="g5tech-training-field">
		<label for="g5tech-training-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
		<?php if ( in_array( $field['type'], array( 'textarea', 'lines' ), true ) ) : ?>
			<textarea
				class="large-text"
				id="g5tech-training-<?php echo esc_attr( $key ); ?>"
				name="g5tech_training_content[<?php echo esc_attr( $key ); ?>]"
				rows="<?php echo (int) $rows; ?>"
			><?php echo esc_textarea( $value ); ?></textarea>
		<?php else : ?>
			<input
				class="large-text"
				id="g5tech-training-<?php echo esc_attr( $key ); ?>"
				name="g5tech_training_content[<?php echo esc_attr( $key ); ?>]"
				type="<?php echo esc_attr( $field['type'] ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
			>
		<?php endif; ?>
		<?php if ( ! empty( $field['help'] ) ) : ?><p class="description"><?php echo esc_html( $field['help'] ); ?></p><?php endif; ?>
	</div>
	<?php
}

function g5tech_render_training_equipment_selector( $content ) {
	$items = array_merge(
		g5tech_get_partners( 'manufacturer' ),
		g5tech_get_partners( 'equipment' )
	);
	$selected = g5tech_sanitize_partner_ids( $content['equipment_ids'] ?? array() );
	?>
	<div class="g5tech-training-field">
		<div class="g5tech-training-field__heading">
			<strong>Rodoma įranga ir gamintojai</strong>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=g5_partner' ) ); ?>">Tvarkyti bendrą katalogą ↗</a>
		</div>
		<p class="description">Pasirinkite elementus iš bendro katalogo. Pavadinimas ir logotipas visoje svetainėje keičiami vienoje vietoje – skiltyje „Partneriai ir įranga“.</p>
		<?php if ( ! $items ) : ?>
			<p>Katalogas tuščias.</p>
		<?php else : ?>
			<div class="g5tech-training-equipment-options">
				<?php foreach ( $items as $item ) : ?>
					<label>
						<input
							type="checkbox"
							name="g5tech_training_content[equipment_ids][]"
							value="<?php echo (int) $item->ID; ?>"
							<?php checked( in_array( (int) $item->ID, $selected, true ) ); ?>
						>
						<span>
							<strong><?php echo esc_html( get_the_title( $item ) ); ?></strong>
							<small><?php echo esc_html( g5tech_partner_type_label( get_post_meta( $item->ID, 'g5_partner_type', true ) ) ); ?></small>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

function g5tech_render_training_topics_module_reference( $module ) {
	$is_active = $module && g5tech_module_is_on_page( $module->ID, 'training' );
	?>
	<div class="g5tech-training-module-reference">
		<div>
			<strong><?php echo esc_html( get_post_meta( $module->ID, 'g5_module_heading', true ) ?: get_the_title( $module ) ); ?></strong>
			<span><?php echo $is_active ? 'Susietas su Mokymų puslapiu' : 'Šiuo metu Mokymų puslapyje nerodomas'; ?></span>
		</div>
		<p>Ši sekcija yra bendras turinio modulis. Ją galima rodyti keliuose puslapiuose arba sukurti nepriklausomą kopiją.</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( get_edit_post_link( $module->ID ) ); ?>">Redaguoti modulį</a>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=g5tech_duplicate_module&module_id=' . $module->ID ), 'g5tech_duplicate_module_' . $module->ID ) ); ?>">Nepriklausoma kopija</a>
		</p>
	</div>
	<?php
}

function g5tech_render_training_content_page() {
	if ( ! current_user_can( 'manage_g5tech_settings' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio puslapio.', '5gtech-core' ) );
	}

	wp_enqueue_media();

	$content       = g5tech_training_page_content();
	$fields        = g5tech_training_admin_fields();
	$section_map   = g5tech_training_sortable_sections();
	$section_order = array( 'topics', 'equipment', 'image' );
	$topics_module = g5tech_get_source_module( 'training_topics' );
	$fallback_url  = get_theme_file_uri( 'assets/images/from-live-site/training-room-wide.jpg' );
	$attachment_id = absint( $content['image_id'] );
	$preview_url   = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium_large' ) : $fallback_url;
	$page_url      = home_url( '/mokymai/' );
	?>
	<div class="wrap g5tech-admin-page g5tech-training-admin">
		<?php
		g5tech_render_unified_admin_header(
			array(
				'title'       => 'Mokymų puslapis',
				'description' => 'Viršuje keiskite sekcijų tvarką, žemiau redaguokite jų turinį ir iš karto matykite viešo puslapio peržiūrą.',
				'page_url'    => $page_url,
			)
		);
		?>

		<?php if ( isset( $_GET['updated'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p>Mokymų puslapio pakeitimai išsaugoti.</p></div>
		<?php endif; ?>

		<?php g5tech_render_page_module_manager( 'training' ); ?>

		<div class="g5tech-admin-layout g5tech-training-admin__layout">
			<div class="g5tech-admin-editor g5tech-training-admin__editor">
				<form class="g5tech-training-admin__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="g5tech_save_training_content">
				<?php wp_nonce_field( 'g5tech_save_training_content' ); ?>
				<?php g5tech_render_admin_content_translation_context( 'training', $content ); ?>

				<details class="g5tech-admin-group g5tech-training-group g5tech-training-group--fixed" open>
					<summary>Puslapio pradžia <span class="g5tech-admin-group__meta">Fiksuota vieta</span></summary>
					<div class="g5tech-admin-group__content g5tech-training-group__content">
						<?php foreach ( $fields['Puslapio pradžia'] as $key => $field ) : ?>
							<?php g5tech_render_training_admin_field( $content, $key, $field ); ?>
						<?php endforeach; ?>
					</div>
				</details>

				<div class="g5tech-admin-section-intro g5tech-training-sortable-intro">
					<strong>Sekcijų turinys</strong>
					<span>Jų vieta puslapyje valdoma modulių sąraše viršuje.</span>
				</div>

				<div>
					<?php foreach ( $section_order as $section_key ) : ?>
						<?php
						$group_label  = $section_map[ $section_key ];
						$group_fields = $fields[ $group_label ];
						?>
						<details class="g5tech-admin-group g5tech-training-group" open>
							<summary>
								<span><?php echo esc_html( $group_label ); ?></span>
							</summary>
							<div class="g5tech-admin-group__content g5tech-training-group__content">
								<?php if ( 'topics' === $section_key && $topics_module ) : ?>
									<?php g5tech_render_training_topics_module_reference( $topics_module ); ?>
								<?php elseif ( 'topics' === $section_key && get_option( 'g5tech_content_modules_migrated_1' ) ) : ?>
									<p>Mokymų programos modulis pašalintas. Sukurkite naują modulį bibliotekoje ir įkelkite jį į Mokymų puslapį.</p>
									<p><a class="button" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=g5_module' ) ); ?>">Kurti turinio modulį</a></p>
								<?php else : ?>
									<?php foreach ( $group_fields as $key => $field ) : ?>
										<?php g5tech_render_training_admin_field( $content, $key, $field ); ?>
									<?php endforeach; ?>
								<?php endif; ?>
								<?php if ( 'equipment' === $section_key ) : ?>
									<?php g5tech_render_training_equipment_selector( $content ); ?>
								<?php endif; ?>
								<?php if ( 'image' === $section_key ) : ?>
									<div class="g5tech-training-field">
										<label>Mokymų aplinkos nuotrauka</label>
										<div class="g5tech-training-media-preview"><img src="<?php echo esc_url( $preview_url ); ?>" alt=""></div>
										<input type="hidden" id="g5tech-training-image-id" name="g5tech_training_content[image_id]" value="<?php echo (int) $attachment_id; ?>">
										<p>
											<button class="button" id="g5tech-training-image-select" type="button">Pasirinkti nuotrauką</button>
											<button class="button-link-delete" id="g5tech-training-image-remove" type="button">Naudoti numatytąją</button>
										</p>
									</div>
								<?php endif; ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>

				<details class="g5tech-admin-group g5tech-training-group g5tech-training-group--fixed" open>
					<summary>Baigiamasis kvietimas <span class="g5tech-admin-group__meta">Fiksuota vieta</span></summary>
					<div class="g5tech-admin-group__content g5tech-training-group__content">
						<?php foreach ( $fields['Baigiamasis kvietimas'] as $key => $field ) : ?>
							<?php g5tech_render_training_admin_field( $content, $key, $field ); ?>
						<?php endforeach; ?>
					</div>
				</details>

					<div class="g5tech-admin-actions g5tech-training-admin__actions">
						<?php submit_button( 'Išsaugoti pakeitimus', 'primary', 'submit', false ); ?>
						<a class="button" href="<?php echo esc_url( $page_url ); ?>" target="_blank" rel="noopener">Atidaryti puslapį ↗</a>
					</div>
				</form>
			</div>

			<?php g5tech_render_unified_admin_preview( $page_url, 'Mokymų puslapio' ); ?>
		</div>
	</div>
	<style>
		.g5tech-training-admin{max-width:1600px}
		.g5tech-training-admin__layout{display:grid;grid-template-columns:minmax(420px,620px) minmax(540px,1fr);gap:24px;align-items:start}
		.g5tech-training-admin__editor,.g5tech-training-admin__form,.g5tech-training-admin__preview{min-width:0}
		.g5tech-training-group{margin-bottom:12px;border:1px solid #dcdcde;background:#fff}
		.g5tech-training-group summary{display:flex;align-items:center;gap:10px;padding:15px 18px;font-size:15px;font-weight:600;cursor:pointer}
		.g5tech-training-group summary::-webkit-details-marker{margin-right:4px}
		.g5tech-training-group--fixed summary{justify-content:space-between}
		.g5tech-training-group--fixed summary span{color:#757575;font-size:12px;font-weight:400}
		.g5tech-training-group__drag{display:inline-grid;width:30px;height:30px;flex:0 0 30px;place-items:center;margin:-7px 0 -7px -9px;border:1px solid #c3c4c7;border-radius:4px;background:#f6f7f7;color:#50575e;cursor:grab;font-size:18px;line-height:1;user-select:none}
		.g5tech-training-group__drag:active{cursor:grabbing}
		.g5tech-training-group[open] summary{border-bottom:1px solid #dcdcde}
		.g5tech-training-group__content{padding:18px}
		.g5tech-training-sortable-intro{display:flex;align-items:baseline;justify-content:space-between;gap:14px;margin:20px 0 10px;padding:0 2px}
		.g5tech-training-sortable-intro span{color:#646970;font-size:12px;text-align:right}
		.g5tech-training-group-placeholder{height:64px;margin-bottom:12px;border:2px dashed #2271b1;background:#f0f6fc}
		.g5tech-training-group--dragging{box-shadow:0 8px 24px rgba(0,0,0,.14)}
		.g5tech-training-field{margin-bottom:18px}
		.g5tech-training-field:last-child{margin-bottom:0}
		.g5tech-training-field label{display:block;margin-bottom:7px;font-weight:600}
		.g5tech-training-field__heading{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:7px}
		.g5tech-training-equipment-options{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:14px}
		.g5tech-training-equipment-options label{display:flex;align-items:center;gap:10px;min-height:58px;margin:0;padding:10px 12px;border:1px solid #dcdcde;background:#fff}
		.g5tech-training-equipment-options input{margin:0}
		.g5tech-training-equipment-options span{display:flex;min-width:0;flex-direction:column;gap:2px}
		.g5tech-training-equipment-options small{color:#646970;font-weight:400}
		.g5tech-training-module-reference{padding:16px;border:1px solid #c3c4c7;background:#f6f7f7}
		.g5tech-training-module-reference>div{display:flex;align-items:center;justify-content:space-between;gap:12px}
		.g5tech-training-module-reference>div span{color:#646970;font-size:12px}
		.g5tech-training-media-preview{height:220px;background:#f0f0f1}
		.g5tech-training-media-preview img{width:100%;height:100%;display:block;object-fit:cover}
		.g5tech-training-admin__actions{display:flex;align-items:center;gap:10px;margin-top:20px}
		.g5tech-training-admin__preview{position:sticky;top:44px;border:1px solid #c3c4c7;background:#fff}
		.g5tech-training-admin__preview-head{display:flex;justify-content:space-between;gap:12px;padding:12px 15px;border-bottom:1px solid #dcdcde}
		.g5tech-training-admin__preview-head span{color:#646970}
		.g5tech-training-admin__preview iframe{display:block;width:100%;height:calc(100vh - 125px);min-height:720px;border:0}
		@media(max-width:1180px){.g5tech-training-admin__layout{grid-template-columns:1fr}.g5tech-training-admin__preview{position:static}.g5tech-training-admin__preview iframe{height:800px}}
		@media(max-width:782px){.g5tech-training-equipment-options{grid-template-columns:1fr}}
	</style>
	<script>
		jQuery(function($) {
			$('#g5tech-training-image-select').on('click', function() {
				const frame = wp.media({
					title: 'Pasirinkite mokymų aplinkos nuotrauką',
					button: { text: 'Naudoti nuotrauką' },
					multiple: false
				});

				frame.on('select', function() {
					const attachment = frame.state().get('selection').first().toJSON();
					$('#g5tech-training-image-id').val(attachment.id);
					$('.g5tech-training-media-preview img').attr('src', attachment.sizes && attachment.sizes.medium_large ? attachment.sizes.medium_large.url : attachment.url);
				});

				frame.open();
			});

			$('#g5tech-training-image-remove').on('click', function() {
				$('#g5tech-training-image-id').val('0');
				$('.g5tech-training-media-preview img').attr('src', <?php echo wp_json_encode( $fallback_url ); ?>);
			});
		});
	</script>
	<?php
}

function g5tech_save_training_content() {
	if ( ! current_user_can( 'manage_g5tech_settings' ) ) {
		wp_die( esc_html__( 'Neturite teisės keisti šio puslapio.', '5gtech-core' ) );
	}

	check_admin_referer( 'g5tech_save_training_content' );
	g5tech_save_admin_content_translations( 'training' );

	$defaults  = g5tech_training_page_defaults();
	$current   = g5tech_training_page_content();
	$submitted = isset( $_POST['g5tech_training_content'] )
		? (array) wp_unslash( $_POST['g5tech_training_content'] )
		: array();
	$sanitized = array();

	foreach ( $defaults as $key => $default ) {
		$value = array_key_exists( $key, $submitted )
			? $submitted[ $key ]
			: ( $current[ $key ] ?? $default );

		if ( 'section_order' === $key ) {
			$sanitized[ $key ] = g5tech_sanitize_training_section_order( $value );
		} elseif ( 'equipment_ids' === $key ) {
			$sanitized[ $key ] = g5tech_sanitize_partner_ids( is_array( $value ) ? $value : array() );
		} elseif ( 'image_id' === $key ) {
			$sanitized[ $key ] = absint( $value );
		} elseif ( 'cta_button_url' === $key ) {
			$sanitized[ $key ] = esc_url_raw( $value );
		} elseif ( in_array( $key, array( 'hero_lead', 'topics', 'equipment' ), true ) ) {
			$sanitized[ $key ] = sanitize_textarea_field( $value );
		} else {
			$sanitized[ $key ] = sanitize_text_field( $value );
		}
	}

	update_option( 'g5tech_training_page_content', $sanitized, false );

	wp_safe_redirect( add_query_arg( 'updated', '1', admin_url( 'admin.php?page=g5tech-training-content' ) ) );
	exit;
}
add_action( 'admin_post_g5tech_save_training_content', 'g5tech_save_training_content' );

function g5tech_redirect_training_page_editor() {
	$post_id = absint( $_GET['post'] ?? 0 );
	$content = $post_id ? (string) get_post_field( 'post_content', $post_id ) : '';

	if (
		! current_user_can( 'manage_g5tech_settings' )
		|| 'edit' !== ( $_GET['action'] ?? '' )
		|| ! has_block( 'g5tech/training-page', $content )
	) {
		return;
	}

	wp_safe_redirect( admin_url( 'admin.php?page=g5tech-training-content' ) );
	exit;
}
// Blokų redaktorius nebeperšokamas.

function g5tech_admin_guide_link( $url, $title, $text ) {
	?>
	<a class="g5tech-guide-card" href="<?php echo esc_url( $url ); ?>">
		<strong><?php echo esc_html( $title ); ?></strong>
		<span><?php echo esc_html( $text ); ?></span>
	</a>
	<?php
}

function g5tech_render_guide_page() {
	if ( ! current_user_can( 'read_g5tech_guide' ) ) {
		wp_die( esc_html__( 'Neturite teisės peržiūrėti šio puslapio.', '5gtech-core' ) );
	}

	$show_content = current_user_can( 'edit_g5_services' ) || current_user_can( 'manage_options' );
	$show_hr      = current_user_can( 'edit_g5_jobs' ) || current_user_can( 'manage_options' );
	?>
	<div class="wrap g5tech-guide">
		<h1>5G TECH darbo atmintinė</h1>
		<p class="g5tech-guide__lead">Pasirinkite norimą veiksmą. Puslapių struktūra ir dizainas keičiant turinį išlieka vienodi.</p>

		<?php if ( $show_content ) : ?>
			<h2>Svetainės turinys</h2>
			<div class="g5tech-guide-grid">
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=page' ), 'Puslapiai', 'Visų puslapių tekstai ir sekcijos vienoje vietoje – blokų redaktoriuje.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'admin.php?page=g5tech-settings' ), 'Bendri duomenys', 'Kontaktai, rodikliai ir kita visoje svetainėje naudojama informacija.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_service' ), 'Paslaugos', 'Paslaugų aprašymai, darbai, vaizdai ir susijusi įranga.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_project' ), 'Projektai', 'Šalys, technologijos, darbų apimtis ir viešumo būsena.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_partner' ), 'Partneriai ir įranga', 'Vienoje vietoje tvarkomi pavadinimai, logotipai ir rodymo būsena.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_module' ), 'Turinio moduliai', 'Pakartotinai naudojamos sekcijos, susietos versijos ir nepriklausomos kopijos.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php' ), 'Naujienos', 'Naujo įrašo parengimas, peržiūra ir publikavimas.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_faq' ), 'Dažniausi klausimai', 'Klausimai paslaugų puslapiams ir kandidatams.' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_hr ) : ?>
			<h2>Komanda ir karjera</h2>
			<div class="g5tech-guide-grid">
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_team' ), 'Komanda', 'Pareigos, kontaktai, patirtis ir viešas profilis.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_job' ), 'Darbo pozicijos', 'Naujos pozicijos, galiojimo data ir aktyvi būsena.' ); ?>
				<?php g5tech_admin_guide_link( admin_url( 'edit.php?post_type=g5_faq' ), 'DUK kandidatams', 'Centralizuoti klausimai apie darbą, komandiruotes ir saugą.' ); ?>
			</div>
		<?php endif; ?>


		<div class="g5tech-guide-steps">
			<h2>Saugi publikavimo eiga</h2>
			<ol>
				<li>Atidarykite esamą įrašą arba pasirinkite „Pridėti naują“.</li>
				<li>Užpildykite tik reikalingus laukus ir įkelkite vaizdą.</li>
				<li>Naują turinį pirmiausia išsaugokite kaip juodraštį.</li>
				<li>Pasirinkite „Peržiūrėti“ ir patikrinkite viešą vaizdą.</li>
				<li>Publikuokite tik patikrintą ir viešinti leidžiamą informaciją.</li>
			</ol>
		</div>
	</div>
	<style>
		.g5tech-guide{max-width:1100px}.g5tech-guide__lead{max-width:760px;font-size:16px;line-height:1.6}
		.g5tech-guide h2{margin-top:32px}.g5tech-guide-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
		.g5tech-guide-card{min-height:130px;padding:22px;display:flex;flex-direction:column;gap:12px;border:1px solid #dcdcde;background:#fff;color:#063354;text-decoration:none}
		.g5tech-guide-card:hover,.g5tech-guide-card:focus{border-color:#ec0062;box-shadow:0 4px 20px rgba(3,31,53,.08)}
		.g5tech-guide-card strong{font-size:18px}.g5tech-guide-card span{color:#526a79;line-height:1.55}
		.g5tech-guide-steps{margin-top:32px;padding:24px 30px;border-left:5px solid #ec0062;background:#fff}
		.g5tech-guide-steps h2{margin-top:0}.g5tech-guide-steps li{margin:0 0 10px;line-height:1.5}
		@media(max-width:900px){.g5tech-guide-grid{grid-template-columns:1fr}}
	</style>
	<?php
}

function g5tech_add_dashboard_guide_widget() {
	if ( current_user_can( 'read_g5tech_guide' ) ) {
		wp_add_dashboard_widget(
			'g5tech-guide-widget',
			'5G TECH turinio valdymas',
			'g5tech_render_dashboard_guide_widget'
		);
	}
}
add_action( 'wp_dashboard_setup', 'g5tech_add_dashboard_guide_widget' );

function g5tech_render_dashboard_guide_widget() {
	?>
	<p>Trumpa instrukcija ir tiesioginės nuorodos į jūsų valdomas svetainės skiltis.</p>
	<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=g5tech-guide' ) ); ?>">Atidaryti darbo atmintinę</a></p>
	<?php
}

function g5tech_simplify_admin_menu() {
	if ( g5tech_user_has_role( 'g5_content_editor' ) ) {
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );

		global $menu, $submenu;

		foreach ( $menu as &$item ) {
			if ( 'edit.php' === $item[2] ) {
				$item[0] = 'Naujienos';
			}
		}
		unset( $item );

		if ( isset( $submenu['edit.php'][5][0] ) ) {
			$submenu['edit.php'][5][0] = 'Visos naujienos';
		}
	}

	if ( g5tech_user_has_role( 'g5_hr_editor' ) ) {
		remove_menu_page( 'edit.php' );
		remove_menu_page( 'edit.php?post_type=page' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'plugins.php' );
		remove_menu_page( 'users.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'options-general.php' );
		remove_menu_page( 'g5tech-settings' );
	}
}
add_action( 'admin_menu', 'g5tech_simplify_admin_menu', 999 );
