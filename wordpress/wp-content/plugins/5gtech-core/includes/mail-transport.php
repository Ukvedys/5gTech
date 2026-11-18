<?php
/** Production mail transport. Credentials stay outside the public document root. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'phpmailer_init', static function ( $mailer ) {
	$config_file = dirname( rtrim( ABSPATH, '/\\' ) ) . '/5gtech-smtp.php';
	if ( 'production' !== wp_get_environment_type() || ! is_readable( $config_file ) ) {
		return;
	}
	$config = require $config_file;
	if ( ! is_array( $config ) || empty( $config['username'] ) || empty( $config['password'] ) || empty( $config['host'] ) ) {
		throw new RuntimeException( 'SMTP configuration is incomplete.' );
	}
	$mailer->isSMTP();
	$mailer->Host = $config['host'];
	$mailer->Port = 465;
	$mailer->SMTPSecure = 'ssl';
	$mailer->SMTPAuth = true;
	$mailer->Username = $config['username'];
	$mailer->Password = $config['password'];
	$mailer->Timeout = 20;
	$mailer->SMTPDebug = 0;
	$mailer->setFrom( $config['username'], '5G TECH', false );
	$mailer->Sender = $config['username'];
} );

/** Return normal form feedback even if the hosting mail transport throws an Error. */
function g5tech_send_form_mail( $to, $subject, $message, $headers = array(), $attachments = array() ) {
	try {
		return wp_mail( $to, $subject, $message, $headers, $attachments );
	} catch ( Throwable $error ) {
		// Do not log addresses, CV contents, SMTP credentials or exception messages.
		error_log( '5G TECH: form mail transport failed (' . get_class( $error ) . ').' );
		return false;
	}
}
