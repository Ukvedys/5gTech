<?php
/**
 * Kontaktų ir kandidatavimo formos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_form_blocks() {
	register_block_type(
		'g5tech/contact-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH kontaktų puslapis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_contact_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);

	register_block_type(
		'g5tech/application-page',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH kandidatavimo puslapis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_application_page',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_form_blocks' );

function g5tech_form_status_message( $form ) {
	$status = isset( $_GET['forma'] ) ? sanitize_key( wp_unslash( $_GET['forma'] ) ) : '';

	if ( 'success' === $status ) {
		return array(
			'type' => 'success',
			'text' => 'contact' === $form ? 'Ačiū. Jūsų užklausa išsiųsta.' : 'Ačiū. Jūsų kandidatūra išsiųsta.',
		);
	}

	$messages = array(
		'required' => 'Patikrinkite privalomus laukus ir bandykite dar kartą.',
		'email'    => 'Patikrinkite, ar el. pašto adresas įrašytas teisingai.',
		'consent'  => 'Norint pateikti formą, būtinas sutikimas dėl duomenų naudojimo.',
		'file'     => 'CV turi būti PDF, DOC arba DOCX formato ir ne didesnis nei 5 MB.',
		'mail'     => 'Formos išsiųsti nepavyko. Bandykite dar kartą arba susisiekite el. paštu.',
		'security' => 'Forma nebegalioja. Atnaujinkite puslapį ir bandykite dar kartą.',
	);

	if ( isset( $messages[ $status ] ) ) {
		return array(
			'type' => 'error',
			'text' => $messages[ $status ],
		);
	}

	return array();
}

function g5tech_form_redirect( $path, $status ) {
	wp_safe_redirect(
		add_query_arg(
			'forma',
			$status,
			home_url( $path )
		)
	);
	exit;
}

function g5tech_form_recipient( $setting_key = 'email' ) {
	$recipient = sanitize_email( g5tech_setting( $setting_key ) );

	return $recipient ?: sanitize_email( get_option( 'admin_email' ) );
}

function g5tech_contact_team_cards() {
	if ( ! function_exists( 'g5tech_get_team_members' ) ) {
		return '';
	}

	$members = array_values(
		array_filter(
			g5tech_get_team_members(),
			static function ( $member ) {
				return (bool) get_post_meta( $member->ID, 'g5_team_show_contact', true )
					&& get_post_meta( $member->ID, 'g5_team_email', true );
			}
		)
	);
	$selected_members = array();

	foreach ( $members as $member ) {
		$role       = strtolower( get_post_meta( $member->ID, 'g5_team_role', true ) );
		$member_key = str_contains( $role, 'personal' )
			? 'Karjera'
			: ( str_contains( $role, 'direktor' ) ? 'Vadovybė' : ( str_contains( $role, 'vadov' ) ? 'Projektai' : '' ) );

		if ( $member_key && ! isset( $selected_members[ $member_key ] ) ) {
			$selected_members[ $member_key ] = $member;
		}
	}

	$members = array_filter(
		array(
			'Vadovybė' => $selected_members['Vadovybė'] ?? null,
			'Projektai' => $selected_members['Projektai'] ?? null,
			'Karjera'   => $selected_members['Karjera'] ?? null,
		)
	);

	if ( ! $members ) {
		return '';
	}

	ob_start();
	?>
	<div class="g5-container card-grid">
		<?php foreach ( $members as $label => $member ) : ?>
			<?php
			$role  = get_post_meta( $member->ID, 'g5_team_role', true );
			$email = sanitize_email( get_post_meta( $member->ID, 'g5_team_email', true ) );
			$phone = get_post_meta( $member->ID, 'g5_team_phone', true );
			?>
			<div class="info-card">
				<span class="info-card__number"><?php echo esc_html( $label ); ?></span>
				<h3 class="g5-heading-sm"><?php echo esc_html( get_the_title( $member ) ); ?></h3>
				<p>
					<?php echo esc_html( $role ); ?><br>
					<a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a>
					<?php if ( $phone ) : ?><br><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a><?php endif; ?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_contact_page_legacy() {
	$status       = g5tech_form_status_message( 'contact' );
	$email        = sanitize_email( g5tech_setting( 'email' ) );
	$phone        = g5tech_setting( 'phone' );
	$address      = g5tech_setting( 'address' );
	$company_code = g5tech_setting( 'company_code' );
	$vat_code     = g5tech_setting( 'vat_code' );
	$team_cards   = g5tech_contact_team_cards();

	ob_start();
	?>
	<section class="inner-hero inner-hero--compact g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid"><div class="inner-hero__copy">
			<div class="g5-eyebrow">Kontaktai</div>
			<h1 class="g5-display-xl" id="page-title">Aptarkime jūsų projektą.</h1>
			<p class="g5-body-lg">Aprašykite užduotį arba susisiekite tiesiogiai – atsakysime ir nukreipsime pas tinkamą specialistą.</p>
		</div></div>
	</section>

	<section class="g5-section g5-grid-lines" aria-labelledby="contact-title" data-g5-core-module="contact_form">
		<div class="g5-container section-head">
			<div class="g5-eyebrow">Parašykite</div>
			<div class="section-head__copy"><h2 class="g5-display-md" id="contact-title">Projekto užklausa.</h2></div>
		</div>
		<div class="g5-container split-layout">
			<div class="split-layout__main">
				<form class="form-grid" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
					<input type="hidden" name="action" value="g5tech_contact">
					<?php wp_nonce_field( 'g5tech_contact', 'g5tech_nonce' ); ?>
					<div class="g5-form-trap" aria-hidden="true"><input id="contact-website" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" hidden></div>
					<div class="field"><label for="contact-name">Vardas ir pavardė *</label><input id="contact-name" name="name" autocomplete="name" required></div>
					<div class="field"><label for="contact-company">Įmonė</label><input id="contact-company" name="company" autocomplete="organization"></div>
					<div class="field"><label for="contact-email">El. paštas *</label><input id="contact-email" name="email" type="email" autocomplete="email" required></div>
					<div class="field"><label for="contact-phone">Telefonas</label><input id="contact-phone" name="phone" type="tel" autocomplete="tel"></div>
					<div class="field field--full"><label for="contact-topic">Užklausos tema *</label><select id="contact-topic" name="topic" required><option value="">Pasirinkite</option><option>Bendradarbiavimas</option><option>Techninė užduotis</option><option>Karjera</option><option>Kita</option></select></div>
					<div class="field field--full"><label for="contact-message">Trumpai aprašykite užduotį *</label><textarea id="contact-message" name="message" required></textarea></div>
					<label class="consent field--full"><input type="checkbox" name="consent" value="1" required><span>Sutinku, kad mano duomenys būtų naudojami atsakymui į užklausą. <a href="<?php echo esc_url( home_url( '/privatumo-politika/' ) ); ?>">Privatumo politika</a>.</span></label>
					<div class="field--full"><button class="g5-button g5-button--dark" type="submit">Siųsti užklausą <span class="g5-button__icon" aria-hidden="true">→</span></button></div>
					<?php if ( $status ) : ?><p class="form-status field--full <?php echo 'error' === $status['type'] ? 'form-status--error' : ''; ?>" tabindex="-1"><?php echo esc_html( $status['text'] ); ?></p><?php endif; ?>
				</form>
			</div>
			<aside class="split-layout__side">
				<h3 class="g5-heading-md">UAB „5GTECH“</h3>
				<ul class="plain-list" style="margin-top: var(--5g-space-6)">
					<?php if ( $address ) : ?><li><?php echo nl2br( esc_html( $address ) ); ?></li><?php endif; ?>
					<?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?></a></li><?php endif; ?>
					<?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
					<?php if ( $company_code ) : ?><li>Įmonės kodas <?php echo esc_html( $company_code ); ?></li><?php endif; ?>
					<?php if ( $vat_code ) : ?><li>PVM kodas <?php echo esc_html( $vat_code ); ?></li><?php endif; ?>
				</ul>
			</aside>
		</div>
	</section>

	<?php if ( $team_cards ) : ?>
		<section class="g5-section g5-section--paper g5-grid-lines" aria-labelledby="people-title" data-g5-core-module="contact_people">
			<div class="g5-container section-head"><div class="g5-eyebrow">Komanda</div><div class="section-head__copy"><h2 class="g5-display-md" id="people-title">Tiesioginiai kontaktai.</h2></div></div>
			<?php echo $team_cards; ?>
		</section>
	<?php endif; ?>
	<?php echo g5tech_render_page_modules( 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php

	return (string) ob_get_clean();
}

function g5tech_render_contact_page() {
	return g5tech_compose_modular_page( 'contact', g5tech_get_legacy_page_html( 'contact' ) );
}

function g5tech_application_job_options() {
	return function_exists( 'g5tech_get_active_jobs' ) ? g5tech_get_active_jobs() : array();
}

function g5tech_render_application_page() {
	$status      = g5tech_form_status_message( 'application' );
	$jobs        = g5tech_application_job_options();
	$selected_id = isset( $_GET['pozicija'] ) ? absint( $_GET['pozicija'] ) : 0;

	ob_start();
	?>
	<section class="inner-hero inner-hero--compact g5-grid-lines g5-grid-lines--dark" aria-labelledby="page-title">
		<div class="g5-container g5-grid"><div class="inner-hero__copy">
			<div class="g5-eyebrow">Kandidatavimas</div>
			<h1 class="g5-display-xl" id="page-title">Pateikite kandidatūrą.</h1>
			<p class="g5-body-lg">Įkelkite CV ir nurodykite dominančią poziciją. Jei tinkamos pozicijos šiuo metu nėra, galėsime susisiekti vėliau.</p>
		</div></div>
	</section>

	<section class="g5-section g5-grid-lines" aria-labelledby="form-title">
		<div class="g5-container section-head"><div class="g5-eyebrow">Kandidatavimas</div><div class="section-head__copy"><h2 class="g5-display-md" id="form-title">Kontaktai ir darbo patirtis.</h2><p class="g5-body">Žvaigždute pažymėti laukai yra privalomi.</p></div></div>
		<form class="g5-container form-grid" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="g5tech_application">
			<?php wp_nonce_field( 'g5tech_application', 'g5tech_nonce' ); ?>
			<div class="g5-form-trap" aria-hidden="true"><input id="apply-website" name="website" type="text" tabindex="-1" autocomplete="off" aria-hidden="true" hidden></div>
			<div class="field"><label for="apply-name">Vardas *</label><input id="apply-name" name="name" autocomplete="given-name" required></div>
			<div class="field"><label for="apply-surname">Pavardė *</label><input id="apply-surname" name="surname" autocomplete="family-name" required></div>
			<div class="field"><label for="apply-city">Miestas *</label><input id="apply-city" name="city" autocomplete="address-level2" required></div>
			<div class="field"><label for="apply-phone">Telefonas *</label><input id="apply-phone" name="phone" type="tel" autocomplete="tel" required></div>
			<div class="field"><label for="apply-email">El. paštas *</label><input id="apply-email" name="email" type="email" autocomplete="email" required></div>
			<div class="field"><label for="apply-role">Dominanti pozicija</label><select id="apply-role" name="role"><option value="">Pasirinkite arba palikite tuščią</option><?php foreach ( $jobs as $job ) : ?><option value="<?php echo (int) $job->ID; ?>" <?php selected( $selected_id, $job->ID ); ?>><?php echo esc_html( get_the_title( $job ) ); ?></option><?php endforeach; ?><option value="other">Kita</option></select></div>
			<div class="field field--full"><label for="apply-cv">Gyvenimo aprašymas (CV) *</label><input id="apply-cv" name="cv" type="file" accept=".pdf,.doc,.docx" required><span class="field-hint">PDF, DOC arba DOCX, iki 5 MB.</span></div>
			<div class="field field--full"><label for="apply-experience">Techninė darbo patirtis</label><textarea id="apply-experience" name="experience"></textarea></div>
			<fieldset class="field field--full checkboxes"><legend>Pažymėkite, kas tinka</legend>
				<label><input type="checkbox" name="skills[]" value="height"> Esu dirbęs (-usi) aukštyje</label>
				<label><input type="checkbox" name="skills[]" value="electricity"> Turiu patirties su elektros įranga</label>
				<label><input type="checkbox" name="skills[]" value="english"> Galiu susikalbėti angliškai</label>
				<label><input type="checkbox" name="skills[]" value="driver"> Turiu B kategorijos pažymėjimą</label>
				<label><input type="checkbox" name="skills[]" value="travel"> Galiu vykti į komandiruotes</label>
				<label><input type="checkbox" name="skills[]" value="documents"> Galiu skaityti techninę dokumentaciją</label>
			</fieldset>
			<div class="field field--full"><label for="apply-motivation">Kodėl norėtumėte prisijungti?</label><textarea id="apply-motivation" name="motivation"></textarea></div>
			<label class="consent field--full"><input type="checkbox" name="consent" value="1" required><span>Sutinku, kad mano duomenys ir CV būtų naudojami atrankos tikslais. <a href="<?php echo esc_url( home_url( '/privatumo-politika/' ) ); ?>">Privatumo politika</a>.</span></label>
			<div class="field--full"><button class="g5-button g5-button--dark" type="submit">Pateikti kandidatūrą <span class="g5-button__icon" aria-hidden="true">→</span></button></div>
			<?php if ( $status ) : ?><p class="form-status field--full <?php echo 'error' === $status['type'] ? 'form-status--error' : ''; ?>" tabindex="-1"><?php echo esc_html( $status['text'] ); ?></p><?php endif; ?>
		</form>
	</section>
	<?php

	return (string) ob_get_clean();
}

function g5tech_handle_contact_form() {
	if (
		! isset( $_POST['g5tech_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['g5tech_nonce'] ) ), 'g5tech_contact' )
	) {
		g5tech_form_redirect( '/kontaktai/', 'security' );
	}

	if ( ! empty( $_POST['website'] ) ) {
		g5tech_form_redirect( '/kontaktai/', 'success' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$company = isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$topic   = isset( $_POST['topic'] ) ? sanitize_text_field( wp_unslash( $_POST['topic'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $name || ! $topic || ! $message ) {
		g5tech_form_redirect( '/kontaktai/', 'required' );
	}
	if ( ! is_email( $email ) ) {
		g5tech_form_redirect( '/kontaktai/', 'email' );
	}
	if ( empty( $_POST['consent'] ) ) {
		g5tech_form_redirect( '/kontaktai/', 'consent' );
	}

	$body = implode(
		"\n",
		array(
			'Vardas ir pavardė: ' . $name,
			'Įmonė: ' . $company,
			'El. paštas: ' . $email,
			'Telefonas: ' . $phone,
			'Tema: ' . $topic,
			'',
			$message,
		)
	);
	$sent = wp_mail(
		g5tech_form_recipient(),
		'5G TECH svetainės užklausa: ' . $topic,
		$body,
		array( 'Reply-To: ' . $name . ' <' . $email . '>' )
	);

	g5tech_form_redirect( '/kontaktai/', $sent ? 'success' : 'mail' );
}
add_action( 'admin_post_nopriv_g5tech_contact', 'g5tech_handle_contact_form' );
add_action( 'admin_post_g5tech_contact', 'g5tech_handle_contact_form' );

function g5tech_handle_application_form() {
	if (
		! isset( $_POST['g5tech_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['g5tech_nonce'] ) ), 'g5tech_application' )
	) {
		g5tech_form_redirect( '/kandidatuoti/', 'security' );
	}

	if ( ! empty( $_POST['website'] ) ) {
		g5tech_form_redirect( '/kandidatuoti/', 'success' );
	}

	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$surname    = isset( $_POST['surname'] ) ? sanitize_text_field( wp_unslash( $_POST['surname'] ) ) : '';
	$city       = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';
	$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email      = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$role       = isset( $_POST['role'] ) ? sanitize_text_field( wp_unslash( $_POST['role'] ) ) : '';
	$experience = isset( $_POST['experience'] ) ? sanitize_textarea_field( wp_unslash( $_POST['experience'] ) ) : '';
	$motivation = isset( $_POST['motivation'] ) ? sanitize_textarea_field( wp_unslash( $_POST['motivation'] ) ) : '';
	$skills     = isset( $_POST['skills'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['skills'] ) ) : array();

	if ( ! $name || ! $surname || ! $city || ! $phone ) {
		g5tech_form_redirect( '/kandidatuoti/', 'required' );
	}
	if ( ! is_email( $email ) ) {
		g5tech_form_redirect( '/kandidatuoti/', 'email' );
	}
	if ( empty( $_POST['consent'] ) ) {
		g5tech_form_redirect( '/kandidatuoti/', 'consent' );
	}

	$file = isset( $_FILES['cv'] ) ? $_FILES['cv'] : array();
	if (
		! $file
		|| UPLOAD_ERR_OK !== (int) $file['error']
		|| (int) $file['size'] > 5 * MB_IN_BYTES
	) {
		g5tech_form_redirect( '/kandidatuoti/', 'file' );
	}

	$allowed_types = array(
		'pdf'  => 'application/pdf',
		'doc'  => 'application/msword',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
	);
	$file_check    = wp_check_filetype_and_ext( $file['tmp_name'], sanitize_file_name( $file['name'] ), $allowed_types );

	if ( empty( $file_check['ext'] ) || empty( $file_check['type'] ) ) {
		g5tech_form_redirect( '/kandidatuoti/', 'file' );
	}

	$mail_attachment = trailingslashit( get_temp_dir() )
		. 'g5tech-'
		. wp_generate_password( 10, false )
		. '-'
		. sanitize_file_name( $file['name'] );

	if ( ! copy( $file['tmp_name'], $mail_attachment ) ) {
		g5tech_form_redirect( '/kandidatuoti/', 'file' );
	}

	$role_label = 'Nenurodyta';
	if ( 'other' === $role ) {
		$role_label = 'Kita';
	} elseif ( absint( $role ) ) {
		$job = get_post( absint( $role ) );
		if (
			$job
			&& 'g5_job' === $job->post_type
			&& function_exists( 'g5tech_job_is_active' )
			&& g5tech_job_is_active( $job->ID )
		) {
			$role_label = get_the_title( $job );
		}
	}

	$skill_labels = array(
		'height'      => 'Darbas aukštyje',
		'electricity' => 'Elektros įranga',
		'english'     => 'Anglų kalba',
		'driver'      => 'B kategorija',
		'travel'      => 'Komandiruotės',
		'documents'   => 'Techninė dokumentacija',
	);
	$skills       = array_intersect( array_keys( $skill_labels ), $skills );
	$body         = implode(
		"\n",
		array(
			'Kandidatas: ' . $name . ' ' . $surname,
			'Miestas: ' . $city,
			'Telefonas: ' . $phone,
			'El. paštas: ' . $email,
			'Pozicija: ' . $role_label,
			'Patirtis: ' . $experience,
			'Kompetencijos: ' . implode( ', ', array_intersect_key( $skill_labels, array_flip( $skills ) ) ),
			'Motyvacija: ' . $motivation,
		)
	);
	$sent         = wp_mail(
		g5tech_form_recipient( 'career_email' ),
		'Kandidatūra: ' . $name . ' ' . $surname,
		$body,
		array( 'Reply-To: ' . $name . ' ' . $surname . ' <' . $email . '>' ),
		array( $mail_attachment )
	);
	wp_delete_file( $mail_attachment );

	g5tech_form_redirect( '/kandidatuoti/', $sent ? 'success' : 'mail' );
}
add_action( 'admin_post_nopriv_g5tech_application', 'g5tech_handle_application_form' );
add_action( 'admin_post_g5tech_application', 'g5tech_handle_application_form' );
