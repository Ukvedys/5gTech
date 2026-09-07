<?php
/** Reference transformations shared by import and regression tests. */
if ( PHP_SAPI !== 'cli' ) { http_response_code( 403 ); exit; }

function g5sync_check( $value, $context, $require_value = true ) {
	if ( is_wp_error( $value ) || ( $require_value && ! $value ) ) {
		throw new RuntimeException( $context . ': ' . ( is_wp_error( $value ) ? $value->get_error_message() : 'write failed' ) );
	}
}

function g5sync_ids( $value, $map ) {
	if ( is_array( $value ) ) return array_map( static fn( $id ) => g5sync_ids( $id, $map ), $value );
	if ( ! $value ) return 0;
	if ( ! isset( $map[(int) $value] ) ) throw new RuntimeException( 'Unresolved content reference: ' . $value );
	return (int) $map[(int) $value];
}

function g5sync_content( $content, $map ) {
	$walk = static function ( $blocks ) use ( &$walk, $map ) {
		foreach ( $blocks as &$block ) {
			foreach ( (array) $block['attrs'] as $key => $value ) {
				$is_media = preg_match( '/^(image\d*Id|videoId)$/', $key ) || ( in_array( $block['blockName'], array( 'core/image', 'core/video', 'core/file', 'core/gallery' ), true ) && in_array( $key, array( 'id','ids' ), true ) );
				$is_post = 'partnerIds' === $key || ( 'core/block' === $block['blockName'] && 'ref' === $key );
				if ( $is_media || $is_post ) $block['attrs'][$key] = g5sync_ids( $value, $map );
			}
			$block['innerBlocks'] = $walk( $block['innerBlocks'] );
		}
		return $blocks;
	};
	return serialize_blocks( $walk( parse_blocks( $content ) ) );
}

function g5sync_meta( $meta, $map ) {
	foreach ( array( 'g5_team_operators', 'g5_service_partners', 'g5_project_service', 'g5_faq_service', 'g5_module_source_id' ) as $key ) {
		if ( isset( $meta[$key] ) ) $meta[$key] = g5sync_ids( $meta[$key], $map );
	}
	return $meta;
}

function g5sync_options( $options, $map ) {
	foreach ( array( 'story_image_1_id','story_image_2_id' ) as $key ) {
		if ( isset( $options['g5tech_about_content'][$key] ) ) $options['g5tech_about_content'][$key] = g5sync_ids( $options['g5tech_about_content'][$key], $map );
	}
	foreach ( array( 'image_id','equipment_ids' ) as $key ) {
		if ( isset( $options['g5tech_training_page_content'][$key] ) ) $options['g5tech_training_page_content'][$key] = g5sync_ids( $options['g5tech_training_page_content'][$key], $map );
	}
	if ( isset( $options['g5tech_module_placements'] ) ) $options['g5tech_module_placements'] = g5sync_ids( $options['g5tech_module_placements'], $map );
	return $options;
}

function g5sync_urls( $value, $source, $target ) {
	if ( is_array( $value ) ) return array_map( static fn( $v ) => g5sync_urls( $v, $source, $target ), $value );
	return is_string( $value ) && $source ? str_replace( $source, $target, $value ) : $value;
}
