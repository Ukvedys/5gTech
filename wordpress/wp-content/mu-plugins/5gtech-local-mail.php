<?php
/**
 * Plugin Name: 5G TECH Local Mail Interceptor
 * Description: Vietinėje aplinkoje sustabdo išorinius laiškus ir išsaugo tik techninį paskutinio bandymo rezultatą.
 */

if ( 'local' !== wp_get_environment_type() ) {
	return;
}

add_filter(
	'pre_wp_mail',
	static function ( $return, $attributes ) {
		update_option(
			'_g5tech_last_mail_test',
			array(
				'to'               => array_map( 'sanitize_email', (array) ( $attributes['to'] ?? array() ) ),
				'subject'          => sanitize_text_field( $attributes['subject'] ?? '' ),
				'headers'          => array_map( 'sanitize_text_field', (array) ( $attributes['headers'] ?? array() ) ),
				'attachment_count' => count( (array) ( $attributes['attachments'] ?? array() ) ),
				'tested_at'        => current_time( 'mysql' ),
			),
			false
		);

		return true;
	},
	10,
	2
);
