<?php
/**
 * Bendras kartotinių turinio objektų valdiklis.
 *
 * Jį naudoja puslapių sekcijos, kuriose administratorius turi galėti
 * pridėti, pašalinti ir perrikiuoti vienodos struktūros elementus.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pašalina seną rankiniu būdu įrašytą numerį iš žymos.
 */
function g5tech_repeater_strip_number_prefix( $value ) {
	return trim( preg_replace( '/^\s*\d+\s*(?:\/|\.|-|–)\s*/u', '', (string) $value ) );
}

/**
 * Patikimai išvalo kartotinio valdiklio duomenis pagal jo laukų schemą.
 */
function g5tech_sanitize_repeater_items( $input, $schema, $max_items = 50 ) {
	$items = array();
	$input = is_array( $input ) ? array_slice( $input, 0, max( 1, absint( $max_items ) ) ) : array();

	foreach ( $input as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$clean = array(
			'_id' => ! empty( $row['_id'] )
				? sanitize_key( $row['_id'] )
				: wp_generate_uuid4(),
		);
		$has_content = false;

		foreach ( $schema as $key => $field ) {
			// Duomenys čia ateina jau po wp_unslash() kviečiančiojoje formoje
			// (career, about, structured, Settings API), todėl antrą kartą
			// jų atrišti negalima – dingtų pasvirieji brūkšniai.
			$value = isset( $row[ $key ] ) && is_scalar( $row[ $key ] )
				? (string) $row[ $key ]
				: '';
			$type  = isset( $field['type'] ) ? $field['type'] : 'text';

			switch ( $type ) {
				case 'textarea':
					$value = sanitize_textarea_field( $value );
					break;
				case 'url':
					$value = esc_url_raw( $value );
					break;
				case 'email':
					$value = sanitize_email( $value );
					break;
				case 'integer':
					$value = (string) absint( $value );
					break;
				default:
					$value = sanitize_text_field( $value );
					break;
			}

			$clean[ $key ] = $value;

			if ( '' !== trim( (string) $value ) ) {
				$has_content = true;
			}
		}

		if ( $has_content ) {
			$items[] = $clean;
		}
	}

	return array_values( $items );
}

/**
 * Senus sunumeruotus laukus paverčia į kartotinių elementų masyvą.
 */
function g5tech_legacy_repeater_items( $source, $prefix, $count, $field_map ) {
	$items = array();

	for ( $index = 1; $index <= absint( $count ); $index++ ) {
		$item        = array( '_id' => wp_generate_uuid4() );
		$has_content = false;

		foreach ( $field_map as $target_key => $legacy_suffix ) {
			$legacy_key = $prefix . $index . '_' . $legacy_suffix;
			$value      = isset( $source[ $legacy_key ] ) ? (string) $source[ $legacy_key ] : '';

			if ( 'label' === $target_key ) {
				$value = g5tech_repeater_strip_number_prefix( $value );
			}

			$item[ $target_key ] = $value;
			$has_content          = $has_content || '' !== trim( $value );
		}

		if ( $has_content ) {
			$items[] = $item;
		}
	}

	return $items;
}

/**
 * Išveda vieną kartotinio valdiklio eilutę.
 */
function g5tech_render_repeater_row( $base_name, $index, $item, $schema, $title_field ) {
	$item_id = ! empty( $item['_id'] ) ? $item['_id'] : wp_generate_uuid4();
	$title   = ! empty( $item[ $title_field ] ) ? $item[ $title_field ] : 'Naujas elementas';
	?>
	<div class="g5tech-repeater__row">
		<div class="g5tech-repeater__row-head">
			<button type="button" class="g5tech-repeater__handle" aria-label="Keisti vietą">↕</button>
			<strong><span class="g5tech-repeater__number"><?php echo esc_html( str_pad( (string) ( (int) $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span> · <span class="g5tech-repeater__summary"><?php echo esc_html( $title ); ?></span></strong>
			<button type="button" class="button-link-delete g5tech-repeater__remove">Pašalinti</button>
		</div>
		<div class="g5tech-repeater__fields">
			<input type="hidden" data-field="_id" name="<?php echo esc_attr( $base_name . '[' . $index . '][_id]' ); ?>" value="<?php echo esc_attr( $item_id ); ?>">
			<?php foreach ( $schema as $key => $field ) : ?>
				<?php
				$value = isset( $item[ $key ] ) ? $item[ $key ] : '';
				$type  = isset( $field['type'] ) ? $field['type'] : 'text';
				?>
				<label class="g5tech-repeater__field">
					<span><?php echo esc_html( $field['label'] ); ?></span>
					<?php if ( 'textarea' === $type ) : ?>
						<textarea
							class="large-text"
							rows="<?php echo esc_attr( isset( $field['rows'] ) ? absint( $field['rows'] ) : 3 ); ?>"
							data-field="<?php echo esc_attr( $key ); ?>"
							data-summary="<?php echo esc_attr( $key === $title_field ? '1' : '0' ); ?>"
							name="<?php echo esc_attr( $base_name . '[' . $index . '][' . $key . ']' ); ?>"
						><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input
							class="regular-text"
							type="<?php echo esc_attr( in_array( $type, array( 'url', 'email', 'number' ), true ) ? $type : 'text' ); ?>"
							data-field="<?php echo esc_attr( $key ); ?>"
							data-summary="<?php echo esc_attr( $key === $title_field ? '1' : '0' ); ?>"
							name="<?php echo esc_attr( $base_name . '[' . $index . '][' . $key . ']' ); ?>"
							value="<?php echo esc_attr( $value ); ?>"
						>
					<?php endif; ?>
					<?php if ( ! empty( $field['description'] ) ) : ?>
						<small><?php echo esc_html( $field['description'] ); ?></small>
					<?php endif; ?>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Išveda kartotinį administravimo valdiklį.
 */
function g5tech_render_repeater( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'name'        => '',
			'items'       => array(),
			'schema'      => array(),
			'add_label'   => 'Pridėti elementą',
			'empty_label' => 'Dar nėra elementų.',
			'title_field' => 'title',
			'min_items'   => 0,
		)
	);

	if ( ! $args['name'] || ! $args['schema'] ) {
		return;
	}

	$GLOBALS['g5tech_repeater_assets_needed'] = true;
	wp_enqueue_script( 'jquery-ui-sortable' );

	$items = is_array( $args['items'] ) ? array_values( $args['items'] ) : array();
	?>
	<div
		class="g5tech-repeater"
		data-name="<?php echo esc_attr( $args['name'] ); ?>"
		data-min="<?php echo esc_attr( absint( $args['min_items'] ) ); ?>"
		data-empty-label="<?php echo esc_attr( $args['empty_label'] ); ?>"
	>
		<div class="g5tech-repeater__items">
			<?php
			foreach ( $items as $index => $item ) {
				g5tech_render_repeater_row(
					$args['name'],
					$index,
					$item,
					$args['schema'],
					$args['title_field']
				);
			}
			?>
		</div>
		<p class="g5tech-repeater__empty" <?php echo $items ? 'hidden' : ''; ?>><?php echo esc_html( $args['empty_label'] ); ?></p>
		<button type="button" class="button button-secondary g5tech-repeater__add">+ <?php echo esc_html( $args['add_label'] ); ?></button>
		<template class="g5tech-repeater__template">
			<?php
			g5tech_render_repeater_row(
				$args['name'],
				'__index__',
				array( '_id' => '' ),
				$args['schema'],
				$args['title_field']
			);
			?>
		</template>
	</div>
	<?php
}

/**
 * Bendri kartotinių valdiklių stiliai ir veiksmai.
 */
function g5tech_repeater_admin_footer() {
	if ( empty( $GLOBALS['g5tech_repeater_assets_needed'] ) ) {
		return;
	}
	?>
	<style>
		.g5tech-repeater { margin: 18px 0 8px; }
		.g5tech-repeater__items { display: grid; gap: 12px; margin-bottom: 14px; }
		.g5tech-repeater__row { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; }
		.g5tech-repeater__row.ui-sortable-helper { box-shadow: 0 8px 24px rgba(0,0,0,.15); }
		.g5tech-repeater__row-head { align-items: center; background: #f6f7f7; border-bottom: 1px solid #dcdcde; display: grid; gap: 10px; grid-template-columns: 38px 1fr auto; padding: 10px 12px; }
		.g5tech-repeater__handle { background: transparent; border: 0; color: #50575e; cursor: grab; font-size: 22px; line-height: 1; padding: 5px; }
		.g5tech-repeater__handle:active { cursor: grabbing; }
		.g5tech-repeater__fields { display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 16px; }
		.g5tech-repeater__field { display: grid; gap: 6px; }
		.g5tech-repeater__field > span { font-weight: 600; }
		.g5tech-repeater__field small { color: #646970; }
		.g5tech-repeater__field textarea, .g5tech-repeater__field input { width: 100%; max-width: none; }
		.g5tech-repeater__empty { color: #646970; font-style: italic; }
		@media (max-width: 782px) {
			.g5tech-repeater__fields { grid-template-columns: 1fr; }
		}
	</style>
	<script>
		jQuery(function($) {
			function updateRepeater($repeater) {
				var base = $repeater.data('name');
				var count = 0;
				$repeater.find('> .g5tech-repeater__items > .g5tech-repeater__row').each(function(index) {
					var $row = $(this);
					count++;
					$row.find('[data-field]').each(function() {
						this.name = base + '[' + index + '][' + $(this).data('field') + ']';
					});
					$row.find('.g5tech-repeater__number').text(String(index + 1).padStart(2, '0'));
				});
				$repeater.find('> .g5tech-repeater__empty').prop('hidden', count > 0);
			}

			function enableSorting($repeater) {
				$repeater.find('> .g5tech-repeater__items').sortable({
					handle: '.g5tech-repeater__handle',
					axis: 'y',
					update: function() { updateRepeater($repeater); }
				});
			}

			$('.g5tech-repeater').each(function() {
				enableSorting($(this));
				updateRepeater($(this));
			});

			$(document).on('click', '.g5tech-repeater__add', function() {
				var $repeater = $(this).closest('.g5tech-repeater');
				var index = $repeater.find('> .g5tech-repeater__items > .g5tech-repeater__row').length;
				var html = $repeater.find('> .g5tech-repeater__template').html().replaceAll('__index__', index);
				$repeater.find('> .g5tech-repeater__items').append(html);
				$repeater.find('> .g5tech-repeater__items > .g5tech-repeater__row').last().find('[data-field="_id"]').val('item-' + Date.now() + '-' + Math.random().toString(16).slice(2));
				updateRepeater($repeater);
				$repeater.find('> .g5tech-repeater__items > .g5tech-repeater__row').last().find('input:not([type="hidden"]), textarea').first().trigger('focus');
			});

			$(document).on('click', '.g5tech-repeater__remove', function() {
				var $repeater = $(this).closest('.g5tech-repeater');
				var count = $repeater.find('> .g5tech-repeater__items > .g5tech-repeater__row').length;
				var min = Number($repeater.data('min') || 0);
				if (count <= min) {
					window.alert('Bent ' + min + ' elementas turi likti.');
					return;
				}
				$(this).closest('.g5tech-repeater__row').remove();
				updateRepeater($repeater);
			});

			$(document).on('input', '.g5tech-repeater [data-summary="1"]', function() {
				var value = String($(this).val()).trim() || 'Naujas elementas';
				$(this).closest('.g5tech-repeater__row').find('.g5tech-repeater__summary').text(value);
			});

			$('form').on('submit', function() {
				$(this).find('.g5tech-repeater').each(function() {
					updateRepeater($(this));
				});
			});
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'g5tech_repeater_admin_footer' );
