<?php
defined( 'ABSPATH' ) || exit;

// Both constants belong in the private wp-config.php, never in the content snapshot.
function g5tech_turnstile_enabled() {
	return defined( 'G5TECH_TURNSTILE_SITE_KEY' ) || defined( 'G5TECH_TURNSTILE_SECRET' );
}

function g5tech_turnstile_field( $action ) {
	if ( ! g5tech_turnstile_enabled() || ! defined( 'G5TECH_TURNSTILE_SITE_KEY' ) ) {
		return;
	}
	wp_enqueue_script( 'g5tech-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, true );
	printf( '<div class="cf-turnstile field--full" data-size="flexible" data-language="%s" data-action="%s" data-sitekey="%s"></div>', esc_attr( g5tech_current_language() ), esc_attr( $action ), esc_attr( G5TECH_TURNSTILE_SITE_KEY ) );
}

function g5tech_turnstile_passed( $action ) {
	if ( ! g5tech_turnstile_enabled() ) {
		return true;
	}
	$token = $_POST['cf-turnstile-response'] ?? '';
	if ( ! defined( 'G5TECH_TURNSTILE_SECRET' ) || ! defined( 'G5TECH_TURNSTILE_SITE_KEY' ) || ! G5TECH_TURNSTILE_SECRET || ! G5TECH_TURNSTILE_SITE_KEY || ! is_string( $token ) || '' === $token || strlen( $token ) > 2048 ) {
		return false;
	}
	$response = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
		'timeout' => 8,
		'redirection' => 0,
		'body' => array( 'secret' => G5TECH_TURNSTILE_SECRET, 'response' => wp_unslash( $token ) ),
	) );
	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		return false;
	}
	$result = json_decode( wp_remote_retrieve_body( $response ), true );
	return true === ( $result['success'] ?? false )
		&& $action === ( $result['action'] ?? '' )
		&& wp_parse_url( home_url(), PHP_URL_HOST ) === ( $result['hostname'] ?? '' );
}
