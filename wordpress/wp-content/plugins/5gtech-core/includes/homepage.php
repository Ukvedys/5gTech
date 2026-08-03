<?php
/**
 * Titulinio puslapio dinaminis surinkimas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function g5tech_register_homepage_block() {
	register_block_type(
		'g5tech/homepage',
		array(
			'api_version'     => 3,
			'title'           => '5G TECH titulinis',
			'category'        => 'theme',
			'render_callback' => 'g5tech_render_homepage',
			'supports'        => array(
				'autoRegister' => true,
				'html'         => false,
				'inserter'     => false,
			),
		)
	);
}
add_action( 'init', 'g5tech_register_homepage_block' );

function g5tech_home_services() {
	return get_posts(
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
}

function g5tech_home_certifications() {
	$items = array();

	foreach ( g5tech_setting_lines( 'certifications' ) as $row ) {
		$parts = array_map( 'trim', explode( '|', $row, 2 ) );

		if ( 2 === count( $parts ) && $parts[0] && $parts[1] ) {
			$items[] = $parts;
		}
	}

	return $items;
}

function g5tech_home_process_media( $index ) {
	$media = array(
		1 => '<svg viewBox="0 0 520 270" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="48" y="42" width="424" height="186"/><circle cx="151" cy="135" r="58"/><path d="M151 77v116M93 135h116M280 79h139M280 113h109M280 147h139M280 181h84"/><path d="m132 134 15 15 31-36" stroke="#ec0062" stroke-width="4"/></svg>',
		2 => '<svg viewBox="0 0 520 270" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M48 219h424M82 219V61h107v158M220 219V95h110v124M361 219V42h77v177"/><path d="M103 87h65M103 113h65M103 139h43M243 122h63M243 148h63M243 174h42M380 69h39M380 95h39M380 121h39"/><circle cx="82" cy="219" r="7" fill="#ec0062" stroke="none"/><circle cx="220" cy="219" r="7" fill="#ec0062" stroke="none"/><circle cx="361" cy="219" r="7" fill="#ec0062" stroke="none"/></svg>',
		3 => '<svg viewBox="0 0 520 270" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M46 226h428"/><path d="M210 226 258 38l48 188M225 169h66M238 117h40M250 70h16"/><path d="m224 169 67 57M291 169l-66 57M238 117l53 52M278 117l-53 52M250 70l28 47M266 70l-28 47"/><rect x="189" y="79" width="32" height="57" rx="4"/><rect x="296" y="70" width="32" height="63" rx="4"/><circle cx="362" cy="116" r="13"/><path d="m355 129-18 34 27 20M338 163l-19 26M354 143l31-7 17-24M364 183l24 27"/><path d="M350 139c-18-6-27-15-31-28" stroke="#ec0062" stroke-width="4"/><circle cx="318" cy="108" r="6" fill="#ec0062" stroke="none"/><path d="m407 196 15 15 34-40" stroke="#ec0062" stroke-width="5"/></svg>',
		4 => '<svg viewBox="0 0 520 270" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M58 212h404M82 186l64-62 61 28 64-91 60 72 91-47"/><path d="M82 65v147M82 186h380"/><circle cx="146" cy="124" r="8" fill="#ec0062" stroke="none"/><circle cx="271" cy="61" r="8" fill="#ec0062" stroke="none"/><circle cx="422" cy="86" r="8" fill="#ec0062" stroke="none"/><path d="m342 190 18 18 39-47" stroke="#ec0062" stroke-width="5"/></svg>',
		5 => '<svg viewBox="0 0 520 270" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="72" y="35" width="236" height="198"/><path d="M112 82h156M112 112h112M112 142h156M112 172h92"/><path d="m111 201 15 15 35-43" stroke="#ec0062" stroke-width="5"/><path d="M338 135h88" stroke-dasharray="8 8"/><path d="m412 119 18 16-18 16"/><circle cx="430" cy="135" r="48"/><path d="m407 136 16 16 32-38" stroke="#ec0062" stroke-width="5"/><circle cx="338" cy="135" r="7" fill="#ec0062" stroke="none"/></svg>',
	);

	$fallback_index = ( ( max( 1, absint( $index ) ) - 1 ) % count( $media ) ) + 1;

	return $media[ $index ] ?? $media[ $fallback_index ];
}

function g5tech_home_team_cards() {
	if ( ! function_exists( 'g5tech_get_team_members' ) ) {
		return '';
	}

	$members = array_slice( g5tech_get_team_members(), 0, 3 );

	if ( ! $members ) {
		return '';
	}

	ob_start();
	?>
	<div class="team-grid">
		<?php foreach ( $members as $member ) : ?>
			<?php
			$role         = get_post_meta( $member->ID, 'g5_team_role', true );
			$summary      = get_post_meta( $member->ID, 'g5_team_summary', true );
			$since        = get_post_meta( $member->ID, 'g5_team_experience_since', true );
			$countries    = g5tech_team_lines( $member->ID, 'g5_team_countries' );
			$operators    = get_post_meta( $member->ID, 'g5_team_operators', false );
			$show_profile = (bool) get_post_meta( $member->ID, 'g5_team_show_profile', true );
			$url          = $show_profile ? get_permalink( $member ) : home_url( '/apie-mus/#komanda' );
			?>
			<a class="team-card" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( 'Peržiūrėti: ' . get_the_title( $member ) ); ?>">
				<div class="team-portrait" aria-hidden="true">
					<?php if ( has_post_thumbnail( $member ) ) : ?>
						<?php echo get_the_post_thumbnail( $member, 'medium_large', array( 'alt' => '' ) ); ?>
					<?php endif; ?>
				</div>
				<div class="team-card-body">
					<?php if ( $role ) : ?><span class="team-role"><?php echo esc_html( $role ); ?></span><?php endif; ?>
					<h3><?php echo esc_html( get_the_title( $member ) ); ?></h3>
					<?php if ( $summary ) : ?><p class="team-card-copy"><?php echo esc_html( wp_trim_words( $summary, 14 ) ); ?></p><?php endif; ?>
				</div>
				<?php if ( $since || $countries || $operators ) : ?>
					<div class="team-card-metrics" aria-label="Patirties rodikliai">
						<?php if ( $since && ctype_digit( (string) $since ) ) : ?><div class="team-metric"><strong><?php echo max( 1, (int) wp_date( 'Y' ) - (int) $since ); ?>+ m.</strong><span>patirties</span></div><?php endif; ?>
						<?php if ( $countries ) : ?><div class="team-metric"><strong><?php echo count( $countries ); ?></strong><span>šalys</span></div><?php endif; ?>
						<?php if ( $operators ) : ?><div class="team-metric"><strong><?php echo count( $operators ); ?></strong><span>operatorių</span></div><?php endif; ?>
					</div>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_home_news_cards() {
	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $posts ) {
		return '';
	}

	ob_start();
	?>
	<div class="news-grid">
		<?php foreach ( $posts as $index => $news_post ) : ?>
			<?php $excerpt = get_the_excerpt( $news_post ); ?>
			<a class="news-card <?php echo 0 === $index ? 'featured' : ''; ?>" href="<?php echo esc_url( get_permalink( $news_post ) ); ?>">
				<span class="news-meta"><?php echo esc_html( g5tech_news_category_label( $news_post->ID ) ); ?> / <?php echo esc_html( get_the_date( 'Y', $news_post ) ); ?></span>
				<div>
					<h3><?php echo esc_html( get_the_title( $news_post ) ); ?></h3>
					<?php if ( $excerpt ) : ?><p><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
				</div>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return (string) ob_get_clean();
}

function g5tech_home_section_is_visible( $section ) {
	return '1' === (string) g5tech_setting( 'home_show_' . sanitize_key( $section ), '1' );
}

function g5tech_home_section_order( $section, $fallback ) {
	$order = absint( g5tech_setting( 'home_order_' . sanitize_key( $section ), $fallback ) );

	return min( 9, max( 1, $order ?: $fallback ) );
}

function g5tech_render_homepage_legacy() {
	$page_id      = (int) get_option( 'page_on_front' );
	$hero_image   = get_the_post_thumbnail_url( $page_id, 'full' ) ?: get_theme_file_uri( 'assets/images/home/hero-sky-worker.png' );
	$hero_slides  = array(
		array(
			'image' => $hero_image,
			'title' => 'Mobiliojo ryšio tinklai',
			'alt'   => '5G TECH specialistas dirba mobiliojo ryšio bokšte kalnų fone',
		),
		array(
			'image' => get_theme_file_uri( 'assets/images/home/hero-indoor-networks.jpg' ),
			'title' => 'Vidinio ryšio tinklai',
			'alt'   => 'Specialistas montuoja vidinio mobiliojo ryšio anteną prie modernaus pastato',
		),
		array(
			'image' => get_theme_file_uri( 'assets/images/home/hero-fixed-networks.jpg' ),
			'title' => 'Fiksuoto ryšio tinklai',
			'alt'   => 'Specialistas tvarko šviesolaidinio ryšio įrangą lauko skirstomojoje spintoje',
		),
		array(
			'image' => get_theme_file_uri( 'assets/images/home/hero-electrical.jpg' ),
			'title' => 'Elektros darbai',
			'alt'   => 'Elektrikas tikrina pramoninę elektros skirstomąją spintą',
		),
		array(
			'image' => get_theme_file_uri( 'assets/images/home/hero-security.jpg' ),
			'title' => 'Apsaugos sistemos',
			'alt'   => 'Specialistas montuoja lauko vaizdo stebėjimo kamerą',
		),
		array(
			'image' => get_theme_file_uri( 'assets/images/home/hero-solar.jpg' ),
			'title' => 'Saulės elektrinės',
			'alt'   => 'Specialistas montuoja saulės modulius ant komercinio objekto stogo',
		),
	);
	$illustration = get_theme_file_uri( 'assets/images/home/infrastructure-line.png' );
	$map_image    = get_theme_file_uri( 'assets/images/home/europe-footprint-map.png' );
	$services     = g5tech_home_services();
	$certificates = g5tech_home_certifications();
	$manufacturers = function_exists( 'g5tech_get_partners' )
		? g5tech_get_partners( 'manufacturer', array(), true )
		: array();
	$contact_url  = home_url( g5tech_setting( 'contact_page_url', '/kontaktai/' ) );
	$email        = g5tech_setting( 'email' );
	$phone        = g5tech_setting( 'phone' );
	$stats        = g5tech_stats();
	$stat_1       = g5tech_stat( 1, '6000+', 'bazinių stočių' );
	$stat_3       = g5tech_stat( 3, '6', 'Europos šalys' );
	$process_steps = g5tech_process_steps();
	$audiences    = g5tech_structured_section( 'home_audiences' );

	ob_start();
	?>
	<section class="hero" id="top" aria-labelledby="hero-title">
		<div
			class="hero-media"
			id="hero-media"
			role="img"
			aria-label="<?php echo esc_attr( $hero_slides[0]['alt'] ); ?>"
		>
			<?php foreach ( $hero_slides as $index => $slide ) : ?>
				<img
					class="hero-bg hero-bg--slide <?php echo 0 === $index ? 'is-active' : ''; ?>"
					src="<?php echo esc_url( $slide['image'] ); ?>"
					alt=""
					aria-hidden="true"
					<?php echo 0 === $index ? 'fetchpriority="high"' : 'loading="lazy"'; ?>
					data-hero-slide
					data-title="<?php echo esc_attr( $slide['title'] ); ?>"
					data-alt="<?php echo esc_attr( $slide['alt'] ); ?>"
				>
			<?php endforeach; ?>
		</div>
		<div class="hero-transition" aria-hidden="true">
			<?php for ( $transition_column = 0; $transition_column < 6; $transition_column++ ) : ?>
				<span class="hero-transition__cell" style="--transition-delay: <?php echo (int) ( $transition_column * 42 ); ?>ms"></span>
			<?php endfor; ?>
		</div>
		<div class="hero-grid" aria-hidden="true"></div>
		<div class="container hero-inner">
			<div class="hero-copy">
				<div class="eyebrow"><?php echo esc_html( g5tech_setting( 'home_hero_eyebrow' ) ); ?></div>
				<h1 id="hero-title"><?php echo esc_html( g5tech_setting( 'home_hero_title' ) ); ?></h1>
				<p class="hero-lead"><?php echo esc_html( g5tech_setting( 'home_hero_lead' ) ); ?></p>
				<div class="hero-actions">
					<a class="btn btn-primary" href="<?php echo esc_url( $contact_url ); ?>">Aptarkime jūsų projektą <span class="circle">→</span></a>
					<a class="btn" href="<?php echo esc_url( home_url( '/paslaugos/' ) ); ?>">Peržiūrėti paslaugas <span class="arrow">→</span></a>
				</div>
			</div>
			<div class="hero-meta" aria-label="Pagrindiniai patirties faktai">
				<div class="meta-item"><strong><?php echo esc_html( $stat_1['value'] ); ?></strong><span><?php echo esc_html( $stat_1['label'] ); ?></span></div>
				<div class="meta-item"><strong><?php echo esc_html( $stat_3['value'] ); ?></strong><span><?php echo esc_html( $stat_3['label'] ); ?></span></div>
				<div class="meta-item"><strong>ISO 9001 / 14001 / 45001</strong><span>sertifikuoti procesai</span></div>
			</div>
		</div>
		<div class="hero-rotator" aria-label="Paslaugų nuotraukos">
			<div class="hero-rotator__next">
				<span class="hero-rotator__label-prefix">Toliau</span>
				<span class="hero-rotator__label" id="hero-rotator-label" aria-live="polite"><?php echo esc_html( $hero_slides[1]['title'] ); ?></span>
			</div>
			<div class="hero-rotator__status" aria-hidden="true">
				<span class="hero-rotator__count" id="hero-rotator-count">01 / <?php echo esc_html( str_pad( (string) count( $hero_slides ), 2, '0', STR_PAD_LEFT ) ); ?></span>
				<span class="hero-rotator__progress"><span class="hero-rotator__progress-fill" id="hero-rotator-progress"></span></span>
			</div>
			<div class="hero-rotator__controls">
				<button class="hero-rotator__arrow" type="button" data-hero-previous aria-label="Ankstesnė paslauga">←</button>
				<div class="hero-rotator__dots" role="group" aria-label="Pasirinkti paslaugą">
					<?php foreach ( $hero_slides as $index => $slide ) : ?>
						<button
							class="hero-rotator__dot <?php echo 0 === $index ? 'is-active' : ''; ?>"
							type="button"
							data-hero-go="<?php echo (int) $index; ?>"
							aria-label="<?php echo esc_attr( $slide['title'] ); ?>"
							aria-pressed="<?php echo 0 === $index ? 'true' : 'false'; ?>"
						></button>
					<?php endforeach; ?>
				</div>
				<button class="hero-rotator__arrow" type="button" data-hero-next aria-label="Kita paslauga">→</button>
			</div>
		</div>
		<div class="scroll-cue" aria-hidden="true">Slinkti</div>
	</section>

	<div class="home-sections">
	<?php if ( g5tech_home_section_is_visible( 'intro' ) ) : ?>
	<section class="intro" id="about" aria-labelledby="intro-title" data-g5-core-module="home_intro" style="order:<?php echo (int) g5tech_home_section_order( 'intro', 1 ); ?>">
		<div class="container">
			<div class="intro-grid">
				<div class="eyebrow">Nuo poreikio iki rezultato</div>
				<div>
					<h2 id="intro-title">Kiekvienas projektas prasideda nuo konkretaus poreikio.</h2>
					<div class="intro-copy"><p>Skiriasi objektai, šalys ir technologijos, tačiau užsakovui visada svarbu, kas prisiims atsakomybę už techninius sprendimus, terminus ir kokybę.</p></div>
				</div>
			</div>
			<div class="network-strip"><img src="<?php echo esc_url( $illustration ); ?>" alt="Telekomunikacijų, apsaugos, elektros ir saulės energetikos infrastruktūros linijinė iliustracija"></div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $services && g5tech_home_section_is_visible( 'services' ) ) : ?>
		<section class="services" id="services" aria-labelledby="services-title" data-g5-core-module="home_services" style="order:<?php echo (int) g5tech_home_section_order( 'services', 2 ); ?>">
			<div class="container">
				<div class="services-heading"><h2 id="services-title">Paslaugos</h2></div>
				<div class="service-grid" role="list" aria-label="Paslaugų kryptys">
					<?php foreach ( $services as $index => $service ) : ?>
						<?php
						$title   = get_post_meta( $service->ID, 'g5_service_card_title', true ) ?: get_the_title( $service );
						$summary = get_post_meta( $service->ID, 'g5_service_card_summary', true ) ?: get_post_meta( $service->ID, 'g5_service_summary', true );
						?>
						<a class="service-tile" href="<?php echo esc_url( get_permalink( $service ) ); ?>" role="listitem">
							<div class="service-tile-top"><span class="num"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="mini-arrow">→</span></div>
							<h3><?php echo esc_html( $title ); ?></h3>
							<?php if ( $summary ) : ?><p><?php echo esc_html( $summary ); ?></p><?php endif; ?>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $certificates && g5tech_home_section_is_visible( 'standards' ) ) : ?>
		<section class="bridge" aria-labelledby="bridge-title" data-g5-core-module="home_standards" style="order:<?php echo (int) g5tech_home_section_order( 'standards', 3 ); ?>">
			<div class="container bridge-inner">
				<div class="bridge-copy"><div class="eyebrow">Darbo standartas</div><h2 id="bridge-title">ISO standartai ir SSVA kvalifikacija.</h2></div>
				<div class="bridge-standards" aria-label="Sertifikatai ir kvalifikacijos">
					<?php foreach ( $certificates as $index => $certificate ) : ?>
						<div class="bridge-standard"><i class="standard-icon <?php echo esc_attr( array( 'icon-quality', 'icon-environment', 'icon-safety', 'icon-qualification' )[ $index ] ?? 'icon-quality' ); ?>" aria-hidden="true"></i><strong><?php echo esc_html( $certificate[0] ); ?></strong><span><?php echo esc_html( $certificate[1] ); ?></span></div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( g5tech_home_section_is_visible( 'process' ) ) : ?>
	<section class="process" id="process" aria-labelledby="process-title" data-g5-core-module="home_process" style="order:<?php echo (int) g5tech_home_section_order( 'process', 4 ); ?>">
		<div class="process-grid">
			<div class="process-intro">
				<div><div class="eyebrow">Kaip dirbame</div><h2 id="process-title">Patikimas rezultatas kuriamas kiekviename projekto etape.</h2></div>
				<div class="process-progress" aria-hidden="true"><div class="progress-track"><span class="progress-fill" id="progress-fill"></span></div><span class="progress-label" id="progress-label">01 / <?php echo esc_html( str_pad( (string) count( $process_steps ), 2, '0', STR_PAD_LEFT ) ); ?></span></div>
			</div>
			<div class="process-steps">
				<?php foreach ( $process_steps as $process_index => $step ) : ?>
					<?php $index = $process_index + 1; ?>
					<article class="process-step" data-step="<?php echo (int) $index; ?>">
						<div class="step-label"><?php echo esc_html( str_pad( (string) $index, 2, '0', STR_PAD_LEFT ) ); ?> / <?php echo esc_html( $step['title'] ); ?></div>
						<h3><?php echo esc_html( $step['heading'] ); ?></h3>
						<p><?php echo esc_html( $step['text'] ); ?></p>
						<div class="step-media blueprint" aria-hidden="true"><?php echo g5tech_home_process_media( $index ); ?></div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( g5tech_home_section_is_visible( 'experience' ) ) : ?>
	<section class="experience" id="experience" aria-labelledby="experience-title" data-g5-core-module="home_experience" style="order:<?php echo (int) g5tech_home_section_order( 'experience', 5 ); ?>">
		<div class="container experience-grid">
			<div>
				<div class="eyebrow">Patirties geografija</div>
				<h2 id="experience-title">Patirtis šešiose Europos šalyse.</h2>
				<div class="proof-grid">
					<?php foreach ( $stats as $stat ) : ?><div class="proof"><strong><?php echo esc_html( $stat['value'] ); ?></strong><span><?php echo esc_html( $stat['label'] ); ?></span></div><?php endforeach; ?>
				</div>
			</div>
			<div class="map-card" aria-label="5G TECH veiklos žemėlapis"><img src="<?php echo esc_url( $map_image ); ?>" alt="5G TECH veiklos Europoje žemėlapis: Lietuva, Vokietija, Švedija, Norvegija, Danija ir Suomija"></div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $manufacturers && g5tech_home_section_is_visible( 'equipment' ) ) : ?>
		<section class="equipment" aria-labelledby="equipment-title" data-g5-core-module="home_equipment" style="order:<?php echo (int) g5tech_home_section_order( 'equipment', 6 ); ?>">
			<div class="container">
				<div class="equipment-head"><div><div class="eyebrow">Partneriai</div><h2 id="equipment-title">Gamintojai, su kurių įranga dirbame.</h2></div></div>
				<div class="equipment-marquee" aria-label="Įrangos gamintojai, su kurių įranga komanda turi praktinės patirties">
					<div class="equipment-track">
						<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?><div class="equipment-group" <?php echo 1 === $copy ? 'aria-hidden="true"' : ''; ?>><?php foreach ( $manufacturers as $manufacturer ) : ?><span class="equipment-name"><?php echo esc_html( get_the_title( $manufacturer ) ); ?></span><?php endforeach; ?></div><?php endfor; ?>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php $team_cards = g5tech_home_team_cards(); ?>
	<?php if ( $team_cards && g5tech_home_section_is_visible( 'team' ) ) : ?>
		<section class="team" id="team" aria-labelledby="team-title" data-g5-core-module="home_team" style="order:<?php echo (int) g5tech_home_section_order( 'team', 7 ); ?>">
			<div class="container">
				<div class="team-head"><div class="eyebrow">Žmonės, kurie atsako už rezultatą</div><div class="team-head-copy"><h2 id="team-title">Patirtis turi vardą, kompetenciją ir atsakomybę.</h2><p class="team-copy">Susipažinkite su žmonėmis, kurie planuoja, įgyvendina ir tikrina projektus.</p></div></div>
				<?php echo $team_cards; ?>
				<div class="team-summary" aria-label="Bendri komandos rodikliai">
					<div class="team-metric"><strong><?php echo esc_html( $stat_1['value'] ); ?></strong><span><?php echo esc_html( $stat_1['label'] ); ?></span></div>
					<div class="team-metric"><strong><?php echo esc_html( $stat_3['value'] ); ?></strong><span><?php echo esc_html( $stat_3['label'] ); ?></span></div>
					<div class="team-metric"><strong>ISO</strong><span>9001 / 14001 / 45001</span></div>
					<div class="team-metric"><strong>SSVA</strong><span>rangovo kvalifikacija</span></div>
				</div>
				<a class="link-line team-link" href="<?php echo esc_url( home_url( '/apie-mus/#komanda' ) ); ?>">Susipažinti su visa komanda <span>→</span></a>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( g5tech_home_section_is_visible( 'audiences' ) ) : ?>
	<section class="audiences" id="careers" aria-labelledby="audiences-title" data-g5-core-module="home_audiences" style="order:<?php echo (int) g5tech_home_section_order( 'audiences', 8 ); ?>">
		<div class="container">
			<div class="audiences-head"><h2 id="audiences-title"><?php echo esc_html( $audiences['title'] ); ?></h2><p><?php echo esc_html( $audiences['lead'] ); ?></p></div>
			<div class="audience-list">
				<?php foreach ( $audiences['cards'] as $audience ) : ?>
					<?php $audience_url = str_starts_with( $audience['url'], 'http://' ) || str_starts_with( $audience['url'], 'https://' ) ? $audience['url'] : home_url( '/' . ltrim( $audience['url'], '/' ) ); ?>
					<a class="audience-item" href="<?php echo esc_url( $audience_url ); ?>"><small><?php echo esc_html( $audience['label'] ); ?></small><h3><?php echo esc_html( $audience['title'] ); ?></h3><p><?php echo esc_html( $audience['text'] ); ?></p><span class="audience-arrow">→</span></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php $news_cards = g5tech_home_news_cards(); ?>
	<?php if ( $news_cards && g5tech_home_section_is_visible( 'news' ) ) : ?>
		<section class="news" aria-labelledby="news-title" data-g5-core-module="home_news" style="order:<?php echo (int) g5tech_home_section_order( 'news', 9 ); ?>">
			<div class="container">
				<div class="section-top"><h2 id="news-title">Projektai, komanda ir techninės įžvalgos.</h2><a class="link-line" href="<?php echo esc_url( home_url( '/naujienos/' ) ); ?>">Visos naujienos <span>→</span></a></div>
				<?php echo $news_cards; ?>
			</div>
		</section>
	<?php endif; ?>
	</div>

	<?php echo g5tech_render_page_modules( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

	<section class="final-cta" id="contact" aria-labelledby="contact-title">
		<div class="container final-grid">
			<div><div class="eyebrow">Pradėkime nuo pokalbio</div><h2 id="contact-title">Aptarkime jūsų projektą.</h2><p>Trumpai aprašykite projektą arba techninę užduotį. Įvertinsime darbų apimtį ir pasiūlysime tolesnius veiksmus.</p></div>
			<div class="contact-stack">
				<?php if ( $email ) : ?><a class="btn" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>"><?php echo esc_html( antispambot( $email ) ); ?> <span>↗</span></a><?php endif; ?>
				<?php if ( $phone ) : ?><a class="btn" href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?> <span>↗</span></a><?php endif; ?>
				<?php if ( ! $email && ! $phone ) : ?><a class="btn" href="<?php echo esc_url( $contact_url ); ?>">Kontaktai <span>→</span></a><?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

function g5tech_render_homepage() {
	return g5tech_compose_modular_page( 'home', g5tech_get_legacy_page_html( 'home' ) );
}
