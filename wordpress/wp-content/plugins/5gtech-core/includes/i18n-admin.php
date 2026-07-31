<?php
/**
 * Turinio modulių vertimų administravimas.
 *
 * Lietuviški laukai lieka pagrindinis duomenų šaltinis. EN ir DE vertimai
 * saugomi prie to paties modulio ir papildo viešos svetainės vertimų katalogą.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Administravime redaguojamos kalbos.
 *
 * @return array<string,array<string,string>>
 */
function g5tech_admin_translation_languages() {
	return array_filter(
		g5tech_languages(),
		static function ( $language, $code ) {
			return 'lt' !== $code;
		},
		ARRAY_FILTER_USE_BOTH
	);
}

/**
 * Modulio vertimų meta duomenys.
 */
function g5tech_get_module_translations( $module_id ) {
	$translations = get_post_meta( $module_id, '_g5tech_module_translations', true );

	return is_array( $translations ) ? $translations : array();
}

/**
 * Vieno lauko pradinis vertimas iš esamo svetainės katalogo.
 */
function g5tech_module_translation_default( $value, $language, $multiline = false ) {
	$value = (string) $value;

	if ( '' === $value ) {
		return '';
	}

	if ( ! $multiline ) {
		return g5tech_t( $value, $language );
	}

	$lines = preg_split( '/\R/u', $value );
	$lines = array_map(
		static function ( $line ) use ( $language ) {
			$trimmed = trim( $line );

			return '' === $trimmed ? '' : g5tech_t( $trimmed, $language );
		},
		$lines
	);

	return implode( "\n", $lines );
}

/**
 * Iš modulio HTML surenka lankytojui matomus tekstus.
 *
 * Dinaminiai moduliai turinį gauna iš bendrų duomenų šaltinių, todėl vertimų
 * skirtuke rodome realiai tame modulyje matomus tekstus.
 *
 * @return array<string,string> Lauko ID => lietuviškas tekstas.
 */
function g5tech_module_visible_strings( $module ) {
	$module = is_numeric( $module ) ? get_post( absint( $module ) ) : $module;

	if ( ! $module instanceof WP_Post || 'g5_module' !== $module->post_type ) {
		return array();
	}

	$html = function_exists( 'g5tech_render_dynamic_content_module' )
		? g5tech_render_dynamic_content_module( $module )
		: '';

	if ( ! $html ) {
		return array();
	}

	$document = new DOMDocument( '1.0', 'UTF-8' );
	$previous = libxml_use_internal_errors( true );
	$document->loadHTML(
		'<?xml encoding="utf-8" ?><!doctype html><html><body><div id="g5-i18n-source">' . $html . '</div></body></html>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	$xpath   = new DOMXPath( $document );
	$strings = array();
	$nodes   = $xpath->query(
		'//*[@id="g5-i18n-source"]//text()[normalize-space() and not(ancestor::script) and not(ancestor::style) and not(ancestor::svg)]'
	);

	foreach ( $nodes as $node ) {
		$value = preg_replace( '/\s+/u', ' ', trim( html_entity_decode( $node->nodeValue, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );

		if ( '' === $value || preg_match( '/^[\p{N}\p{P}\p{S}\s]+$/u', $value ) ) {
			continue;
		}

		$key = md5( $value );

		if ( ! isset( $strings[ $key ] ) ) {
			$strings[ $key ] = $value;
		}
	}

	return $strings;
}

/**
 * Vienoda LT / EN / DE modulio turinio sąsaja.
 */
function g5tech_render_module_translation_editor( $post ) {
	$type         = get_post_meta( $post->ID, 'g5_module_type', true ) ?: 'list';
	$is_dynamic   = 'dynamic' === $type;
	$eyebrow      = get_post_meta( $post->ID, 'g5_module_eyebrow', true );
	$heading      = get_post_meta( $post->ID, 'g5_module_heading', true );
	$lead         = get_post_meta( $post->ID, 'g5_module_lead', true );
	$content      = get_post_meta( $post->ID, 'g5_module_content', true );
	$theme        = get_post_meta( $post->ID, 'g5_module_theme', true ) ?: 'light';
	$translations = g5tech_get_module_translations( $post->ID );
	$visible      = $is_dynamic ? g5tech_module_visible_strings( $post ) : array();
	$languages    = g5tech_languages();

	wp_nonce_field( 'g5tech_save_module_translations', 'g5tech_module_i18n_nonce' );
	?>
	<div class="g5tech-i18n-editor" data-g5-i18n-editor>
		<div class="g5tech-i18n-editor__head">
			<div>
				<strong>Turinio kalba</strong>
				<p>Keiskite kalbą neišeidami iš šio modulio.</p>
			</div>
			<div class="g5tech-i18n-tabs" role="tablist" aria-label="Modulio turinio kalba">
				<?php foreach ( $languages as $code => $language ) : ?>
					<button
						type="button"
						class="g5tech-i18n-tab <?php echo 'lt' === $code ? 'is-active' : ''; ?>"
						role="tab"
						aria-selected="<?php echo 'lt' === $code ? 'true' : 'false'; ?>"
						aria-controls="g5tech-module-language-<?php echo esc_attr( $code ); ?>"
						data-g5-i18n-tab="<?php echo esc_attr( $code ); ?>"
					>
						<span><?php echo esc_html( $language['label'] ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<section
			id="g5tech-module-language-lt"
			class="g5tech-i18n-panel is-active"
			role="tabpanel"
			data-g5-i18n-panel="lt"
		>
			<?php if ( $is_dynamic ) : ?>
				<?php
				$dynamic_key = get_post_meta( $post->ID, 'g5_module_dynamic_key', true );
				$definition  = function_exists( 'g5tech_get_builtin_module_definition' )
					? g5tech_get_builtin_module_definition( $dynamic_key )
					: null;
				?>
				<div class="g5tech-i18n-source-card">
					<div>
						<strong>Lietuviškas turinys valdomas bendrame duomenų šaltinyje.</strong>
						<p>EN ir DE tekstus galite redaguoti šio modulio kalbų skirtukuose.</p>
					</div>
					<?php if ( $definition && ! empty( $definition['source_url'] ) ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( $definition['source_url'] ); ?>">
							Redaguoti LT turinį: <?php echo esc_html( $definition['source_label'] ); ?>
						</a>
					<?php endif; ?>
				</div>
				<input type="hidden" name="g5_module_type" value="dynamic">
			<?php else : ?>
				<p class="description">Įrašo pavadinimas viršuje skirtas moduliui rasti administravime. Svetainėje rodomi šie laukai.</p>
				<?php g5tech_render_module_source_fields( $type, $eyebrow, $heading, $lead, $content, $theme ); ?>
			<?php endif; ?>
		</section>

		<?php foreach ( g5tech_admin_translation_languages() as $code => $language ) : ?>
			<section
				id="g5tech-module-language-<?php echo esc_attr( $code ); ?>"
				class="g5tech-i18n-panel"
				role="tabpanel"
				hidden
				data-g5-i18n-panel="<?php echo esc_attr( $code ); ?>"
			>
				<?php if ( $is_dynamic ) : ?>
					<?php g5tech_render_dynamic_module_translation_fields( $visible, $translations, $code ); ?>
				<?php else : ?>
					<?php g5tech_render_static_module_translation_fields( compact( 'eyebrow', 'heading', 'lead', 'content' ), $translations, $code ); ?>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Lietuviški modulio laukai.
 */
function g5tech_render_module_source_fields( $type, $eyebrow, $heading, $lead, $content, $theme ) {
	?>
	<table class="form-table g5tech-i18n-fields" role="presentation">
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
				<p class="description">Fonas yra bendras visoms kalboms.</p>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Statinio modulio vertimo laukai.
 */
function g5tech_render_static_module_translation_fields( $source, $translations, $language ) {
	$labels = array(
		'eyebrow' => 'Trumpa žyma',
		'heading' => 'Sekcijos antraštė',
		'lead'    => 'Įžanginis tekstas',
		'content' => 'Turinys',
	);
	$saved  = $translations[ $language ]['fields'] ?? array();

	?>
	<table class="form-table g5tech-i18n-fields" role="presentation">
		<?php foreach ( $labels as $key => $label ) : ?>
			<?php
			$value = array_key_exists( $key, $saved )
				? $saved[ $key ]
				: g5tech_module_translation_default( $source[ $key ], $language, 'content' === $key );
			$rows  = 'content' === $key ? 10 : ( 'lead' === $key ? 3 : 1 );
			?>
			<tr>
				<th scope="row">
					<label for="g5_module_i18n_<?php echo esc_attr( $language . '_' . $key ); ?>"><?php echo esc_html( $label ); ?></label>
					<small><?php echo esc_html( $source[ $key ] ); ?></small>
				</th>
				<td>
					<?php if ( 1 === $rows ) : ?>
						<input
							class="large-text"
							id="g5_module_i18n_<?php echo esc_attr( $language . '_' . $key ); ?>"
							name="g5_module_i18n[<?php echo esc_attr( $language ); ?>][<?php echo esc_attr( $key ); ?>]"
							type="text"
							value="<?php echo esc_attr( $value ); ?>"
						>
					<?php else : ?>
						<textarea
							class="large-text"
							id="g5_module_i18n_<?php echo esc_attr( $language . '_' . $key ); ?>"
							name="g5_module_i18n[<?php echo esc_attr( $language ); ?>][<?php echo esc_attr( $key ); ?>]"
							rows="<?php echo (int) $rows; ?>"
						><?php echo esc_textarea( $value ); ?></textarea>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php
}

/**
 * Dinaminio modulio vertimo laukai.
 */
function g5tech_render_dynamic_module_translation_fields( $visible, $translations, $language ) {
	$saved = $translations[ $language ]['pairs'] ?? array();

	if ( ! $visible ) {
		echo '<div class="notice notice-warning inline"><p>Nepavyko nuskaityti modulio tekstų. Patikrinkite, ar lietuviškame duomenų šaltinyje yra turinio.</p></div>';
		return;
	}
	?>
	<div class="g5tech-i18n-string-list">
		<?php foreach ( $visible as $key => $source ) : ?>
			<?php $value = array_key_exists( $source, $saved ) ? $saved[ $source ] : g5tech_t( $source, $language ); ?>
			<div class="g5tech-i18n-string">
				<label for="g5_module_i18n_pair_<?php echo esc_attr( $language . '_' . $key ); ?>">
					<span>LT</span>
					<strong><?php echo esc_html( $source ); ?></strong>
				</label>
				<input
					type="hidden"
					name="g5_module_i18n_pairs[<?php echo esc_attr( $language ); ?>][<?php echo esc_attr( $key ); ?>][source]"
					value="<?php echo esc_attr( $source ); ?>"
				>
				<textarea
					class="large-text"
					id="g5_module_i18n_pair_<?php echo esc_attr( $language . '_' . $key ); ?>"
					name="g5_module_i18n_pairs[<?php echo esc_attr( $language ); ?>][<?php echo esc_attr( $key ); ?>][translation]"
					rows="<?php echo strlen( $source ) > 90 ? 3 : 2; ?>"
				><?php echo esc_textarea( $value ); ?></textarea>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Išsaugo vieno modulio EN ir DE vertimus.
 */
function g5tech_save_module_translations( $post_id ) {
	if (
		! isset( $_POST['g5tech_module_i18n_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['g5tech_module_i18n_nonce'] ) ), 'g5tech_save_module_translations' )
		|| defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE
		|| 'g5_module' !== get_post_type( $post_id )
		|| ! current_user_can( 'edit_post', $post_id )
	) {
		return;
	}

	// Jei formoje vertimų laukų iš viso nebuvo (modulis buvo juodraštis, jo
	// tekstų nepavyko nuskaityti arba išsaugoma kitu keliu), esami vertimai
	// paliekami nepaliesti. Anksčiau tokiu atveju jie būdavo ištrinami.
	if ( ! isset( $_POST['g5_module_i18n'] ) && ! isset( $_POST['g5_module_i18n_pairs'] ) ) {
		return;
	}

	$existing     = get_post_meta( $post_id, '_g5tech_module_translations', true );
	$translations = is_array( $existing ) ? $existing : array();
	$field_input  = isset( $_POST['g5_module_i18n'] ) ? (array) wp_unslash( $_POST['g5_module_i18n'] ) : array();
	$pair_input   = isset( $_POST['g5_module_i18n_pairs'] ) ? (array) wp_unslash( $_POST['g5_module_i18n_pairs'] ) : array();

	foreach ( g5tech_admin_translation_languages() as $language => $definition ) {
		$has_fields = isset( $field_input[ $language ] );
		$has_pairs  = isset( $pair_input[ $language ] );

		// Tos kalbos formoje nebuvo – jos vertimai nekeičiami.
		if ( ! $has_fields && ! $has_pairs ) {
			continue;
		}

		$entry = isset( $translations[ $language ] ) && is_array( $translations[ $language ] )
			? $translations[ $language ]
			: array(
				'fields' => array(),
				'pairs'  => array(),
			);

		if ( $has_fields ) {
			$fields = array();

			foreach ( array( 'eyebrow', 'heading', 'lead', 'content' ) as $key ) {
				if ( isset( $field_input[ $language ][ $key ] ) ) {
					$fields[ $key ] = sanitize_textarea_field( $field_input[ $language ][ $key ] );
				}
			}

			$entry['fields'] = $fields;
		}

		if ( $has_pairs ) {
			$pairs = array();

			foreach ( (array) $pair_input[ $language ] as $item ) {
				$source      = sanitize_textarea_field( $item['source'] ?? '' );
				$translation = sanitize_textarea_field( $item['translation'] ?? '' );

				if ( '' !== $source && '' !== $translation && $translation !== $source ) {
					$pairs[ $source ] = $translation;
				}
			}

			$entry['pairs'] = $pairs;
		}

		$entry['updated'] = time();

		$translations[ $language ] = $entry;
	}

	update_post_meta( $post_id, '_g5tech_module_translations', $translations );
	g5tech_rebuild_content_translation_overrides();
}
add_action( 'save_post_g5_module', 'g5tech_save_module_translations', 30 );

/**
 * Vieno modulio vertimus paverčia tikslių LT => vertimas porų rinkiniu.
 */
function g5tech_compile_module_translation_pairs( $module_id, $language ) {
	$translations = g5tech_get_module_translations( $module_id );
	$language_data = $translations[ $language ] ?? array();
	$result         = (array) ( $language_data['pairs'] ?? array() );
	$fields         = (array) ( $language_data['fields'] ?? array() );

	if ( $fields ) {
		$sources = array(
			'eyebrow' => get_post_meta( $module_id, 'g5_module_eyebrow', true ),
			'heading' => get_post_meta( $module_id, 'g5_module_heading', true ),
			'lead'    => get_post_meta( $module_id, 'g5_module_lead', true ),
			'content' => get_post_meta( $module_id, 'g5_module_content', true ),
		);

		foreach ( array( 'eyebrow', 'heading', 'lead' ) as $key ) {
			$source      = trim( (string) $sources[ $key ] );
			$translation = trim( (string) ( $fields[ $key ] ?? '' ) );

			if ( '' !== $source && '' !== $translation && $source !== $translation ) {
				$result[ $source ] = $translation;
			}
		}

		$source_lines      = preg_split( '/\R/u', (string) $sources['content'] );
		$translation_lines = preg_split( '/\R/u', (string) ( $fields['content'] ?? '' ) );

		foreach ( $source_lines as $index => $source ) {
			$source      = trim( $source );
			$translation = trim( (string) ( $translation_lines[ $index ] ?? '' ) );

			if ( '' !== $source && '' !== $translation && $source !== $translation ) {
				$result[ $source ] = $translation;
			}
		}
	}

	return $result;
}

/**
 * Sukuria greitai viešoje svetainėje naudojamą turinio vertimų katalogą.
 */
function g5tech_rebuild_content_translation_overrides() {
	$overrides = array(
		'en' => array(),
		'de' => array(),
	);
	$modules = get_posts(
		array(
			'post_type'      => 'g5_module',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'ID'         => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);

	foreach ( $modules as $module ) {
		foreach ( array_keys( $overrides ) as $language ) {
			$overrides[ $language ] = array_replace(
				$overrides[ $language ],
				g5tech_compile_module_translation_pairs( $module->ID, $language )
			);
		}
	}

	update_option( 'g5tech_content_translation_overrides', $overrides, false );

	return $overrides;
}

/**
 * Modulių vertimai turi pirmenybę prieš pradinį failų katalogą.
 */
function g5tech_content_translation_catalog( $catalogue, $language ) {
	$overrides = get_option( 'g5tech_content_translation_overrides', null );

	if ( ! is_array( $overrides ) ) {
		$overrides = g5tech_rebuild_content_translation_overrides();
	}

	return array_replace( $catalogue, (array) ( $overrides[ $language ] ?? array() ) );
}
add_filter( 'g5tech_translation_catalog', 'g5tech_content_translation_catalog', 20, 2 );

/**
 * Puslapių formose saugomi vertimai.
 *
 * Nuotraukos, nuorodos, pasirinkimai ir kiti struktūriniai duomenys lieka
 * bendri. Čia laikomos tik tikslios LT => EN / DE tekstų poros.
 */
function g5tech_get_admin_content_translations( $context = '' ) {
	$translations = get_option( 'g5tech_admin_content_translations', array() );
	$translations = is_array( $translations ) ? $translations : array();

	if ( '' === $context ) {
		return $translations;
	}

	$context = sanitize_key( $context );

	return isset( $translations[ $context ] ) && is_array( $translations[ $context ] )
		? $translations[ $context ]
		: array();
}

/**
 * Iš puslapio duomenų surenka tekstines reikšmes pradiniams vertimams.
 *
 * @return string[]
 */
function g5tech_admin_content_source_strings( $content ) {
	$strings = array();

	$walk = static function ( $value ) use ( &$walk, &$strings ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $child ) {
				$walk( $child );
			}
			return;
		}

		if ( ! is_string( $value ) ) {
			return;
		}

		$value = trim( $value );

		if (
			'' === $value
			|| filter_var( $value, FILTER_VALIDATE_URL )
			|| is_email( $value )
		) {
			return;
		}

		$strings[ $value ] = $value;
	};

	$walk( $content );

	return array_values( $strings );
}

/**
 * Parengia puslapio formos EN ir DE pradines reikšmes.
 */
function g5tech_admin_content_editor_data( $context, $content ) {
	$saved   = g5tech_get_admin_content_translations( $context );
	$sources = g5tech_admin_content_source_strings( $content );
	$data    = array();

	foreach ( g5tech_admin_translation_languages() as $language => $definition ) {
		unset( $definition );
		$data[ $language ] = array();

		foreach ( $sources as $source ) {
			$translated = isset( $saved[ $language ][ $source ] )
				? (string) $saved[ $language ][ $source ]
				: g5tech_module_translation_default( $source, $language, str_contains( $source, "\n" ) );

			$data[ $language ][ $source ] = $translated;
		}

		foreach ( (array) ( $saved[ $language ] ?? array() ) as $source => $translated ) {
			$data[ $language ][ (string) $source ] = (string) $translated;
		}
	}

	return $data;
}

/**
 * Prijungia vienodą LT / EN / DE redagavimą prie pasirinktinės puslapio formos.
 *
 * $args leidžia sudėtingesniuose WordPress ekranuose tiksliai nurodyti:
 * - selector: kuriuos teksto laukus versti;
 * - container: kur rodyti kalbų skirtukus.
 */
function g5tech_render_admin_content_translation_context( $context, $content, $args = array() ) {
	$context = sanitize_key( $context );
	$args    = wp_parse_args(
		$args,
		array(
			'selector'  => '',
			'container' => '',
		)
	);

	if ( '' === $context ) {
		return;
	}

	wp_nonce_field( 'g5tech_save_admin_content_translations_' . $context, 'g5tech_admin_i18n_nonce' );
	?>
	<div
		class="g5tech-page-i18n-config"
		data-g5-page-i18n
		data-g5-page-i18n-context="<?php echo esc_attr( $context ); ?>"
		<?php echo '' !== $args['selector'] ? 'data-g5-page-i18n-selector="' . esc_attr( $args['selector'] ) . '"' : ''; ?>
		<?php echo '' !== $args['container'] ? 'data-g5-page-i18n-container="' . esc_attr( $args['container'] ) . '"' : ''; ?>
		hidden
	>
		<input type="hidden" name="g5tech_admin_i18n_context" value="<?php echo esc_attr( $context ); ?>">
		<textarea name="g5tech_admin_i18n_payload" data-g5-page-i18n-payload hidden></textarea>
		<script type="application/json" data-g5-page-i18n-data><?php echo wp_json_encode( g5tech_admin_content_editor_data( $context, $content ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
	</div>
	<?php
}

/**
 * Patikrina ir išsaugo vienos puslapio formos vertimų poras.
 */
function g5tech_save_admin_content_translations( $expected_context ) {
	$expected_context = sanitize_key( $expected_context );
	$context          = sanitize_key( wp_unslash( $_POST['g5tech_admin_i18n_context'] ?? '' ) );
	$nonce            = sanitize_text_field( wp_unslash( $_POST['g5tech_admin_i18n_nonce'] ?? '' ) );

	if (
		'' === $expected_context
		|| $expected_context !== $context
		|| ! wp_verify_nonce( $nonce, 'g5tech_save_admin_content_translations_' . $expected_context )
	) {
		return false;
	}

	$raw_payload = (string) ( $_POST['g5tech_admin_i18n_payload'] ?? '' );

	// Tuščias arba sugadintas krovinys reiškia, kad naršyklės scenarijus
	// nesuveikė – tokiu atveju esami vertimai paliekami nepaliesti.
	// Sąmoningas išvalymas atsiunčia korektišką {"en":[],"de":[]}.
	if ( '' === trim( $raw_payload ) ) {
		return false;
	}

	$payload = json_decode( wp_unslash( $raw_payload ), true );

	if ( ! is_array( $payload ) ) {
		return false;
	}

	$clean   = array(
		'en' => array(),
		'de' => array(),
	);

	foreach ( array_keys( $clean ) as $language ) {
		$pairs = isset( $payload[ $language ] ) && is_array( $payload[ $language ] )
			? array_slice( $payload[ $language ], 0, 1000 )
			: array();

		foreach ( $pairs as $pair ) {
			if ( ! is_array( $pair ) ) {
				continue;
			}

			$source      = sanitize_textarea_field( $pair['source'] ?? '' );
			$translation = sanitize_textarea_field( $pair['translation'] ?? '' );

			if ( '' !== $source && '' !== $translation && $source !== $translation ) {
				$clean[ $language ][ $source ] = $translation;
			}
		}
	}

	$all                      = g5tech_get_admin_content_translations();
	$all[ $expected_context ] = $clean;
	update_option( 'g5tech_admin_content_translations', $all, false );
	g5tech_rebuild_admin_content_translation_overrides();

	return true;
}

/**
 * Kelių eilučių laukus papildo atskirų eilučių vertimais.
 */
function g5tech_expand_admin_translation_pair( &$target, $source, $translation ) {
	$source      = trim( (string) $source );
	$translation = trim( (string) $translation );

	if ( '' === $source || '' === $translation || $source === $translation ) {
		return;
	}

	$target[ $source ] = $translation;

	if ( ! str_contains( $source, "\n" ) ) {
		return;
	}

	$source_lines      = preg_split( '/\R/u', $source );
	$translation_lines = preg_split( '/\R/u', $translation );

	foreach ( $source_lines as $index => $source_line ) {
		$source_line      = trim( $source_line );
		$translation_line = trim( (string) ( $translation_lines[ $index ] ?? '' ) );

		if ( '' !== $source_line && '' !== $translation_line && $source_line !== $translation_line ) {
			$target[ $source_line ] = $translation_line;
		}
	}
}

/**
 * Sukuria greitai viešoje svetainėje naudojamą puslapių laukų katalogą.
 */
function g5tech_rebuild_admin_content_translation_overrides() {
	$overrides   = array( 'en' => array(), 'de' => array() );
	$translations = g5tech_get_admin_content_translations();

	foreach ( $translations as $context => $languages ) {
		unset( $context );

		foreach ( array_keys( $overrides ) as $language ) {
			foreach ( (array) ( $languages[ $language ] ?? array() ) as $source => $translation ) {
				g5tech_expand_admin_translation_pair(
					$overrides[ $language ],
					$source,
					$translation
				);
			}
		}
	}

	update_option( 'g5tech_admin_translation_overrides', $overrides, false );

	return $overrides;
}

/**
 * Puslapių šaltiniuose redaguoti vertimai turi pirmenybę prieš failų katalogą
 * ir konkretaus modulio ankstesnes reikšmes.
 */
function g5tech_admin_content_translation_catalog( $catalogue, $language ) {
	$overrides = get_option( 'g5tech_admin_translation_overrides', null );

	if ( ! is_array( $overrides ) ) {
		$overrides = g5tech_rebuild_admin_content_translation_overrides();
	}

	return array_replace( $catalogue, (array) ( $overrides[ $language ] ?? array() ) );
}
add_filter( 'g5tech_translation_catalog', 'g5tech_admin_content_translation_catalog', 30, 2 );

/**
 * Pašalinus, atkūrus arba pakeitus modulio būseną nepalieka pasenusių porų.
 */
function g5tech_refresh_translation_overrides_on_module_status( $new_status, $old_status, $post ) {
	if ( $post instanceof WP_Post && 'g5_module' === $post->post_type && $new_status !== $old_status ) {
		g5tech_rebuild_content_translation_overrides();
	}
}
add_action( 'transition_post_status', 'g5tech_refresh_translation_overrides_on_module_status', 30, 3 );

function g5tech_refresh_translation_overrides_after_module_delete( $post_id, $post ) {
	if ( $post instanceof WP_Post && 'g5_module' === $post->post_type ) {
		g5tech_rebuild_content_translation_overrides();
	}
}
add_action( 'deleted_post', 'g5tech_refresh_translation_overrides_after_module_delete', 30, 2 );

/**
 * Modulį redaguojant įkelia kalbų skirtukų išvaizdą ir veikimą.
 */
function g5tech_enqueue_module_translation_assets( $hook ) {
	$screen = get_current_screen();

	if ( ! $screen || 'g5_module' !== $screen->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	wp_enqueue_style(
		'g5tech-module-i18n-admin',
		G5TECH_CORE_URL . 'assets/admin-module-i18n.css',
		array(),
		G5TECH_CORE_VERSION
	);
	wp_enqueue_script(
		'g5tech-module-i18n-admin',
		G5TECH_CORE_URL . 'assets/admin-module-i18n.js',
		array(),
		G5TECH_CORE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'g5tech_enqueue_module_translation_assets' );

/**
 * Vienodą puslapių laukų kalbų sąsają įkelia tik ją naudojančiuose ekranuose.
 */
function g5tech_enqueue_admin_content_translation_assets( $hook ) {
	$page = sanitize_key( wp_unslash( $_GET['page'] ?? '' ) );
	$screen = get_current_screen();
	$is_service_editor = $screen
		&& 'g5_service' === $screen->post_type
		&& in_array( $hook, array( 'post.php', 'post-new.php' ), true );

	if ( ! $is_service_editor && ! in_array(
		$page,
		array(
			'g5tech-about-order',
			'g5tech-career-content',
			'g5tech-training-content',
			'g5tech-structured-content',
			'g5tech-settings',
		),
		true
	) ) {
		return;
	}

	wp_enqueue_style(
		'g5tech-admin-content-i18n',
		G5TECH_CORE_URL . 'assets/admin-content-i18n.css',
		array(),
		G5TECH_CORE_VERSION
	);
	wp_enqueue_script(
		'g5tech-admin-content-i18n',
		G5TECH_CORE_URL . 'assets/admin-content-i18n.js',
		array(),
		G5TECH_CORE_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'g5tech_enqueue_admin_content_translation_assets', 30 );
