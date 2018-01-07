<?php
function fed_forgot_form_submit( $post ) {
	global $wpdb, $wp_hasher;

	$user_data    = fed_validate_forgot_password( $post );
	$redirect_url = fed_get_login_url();


	if ( $user_data instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $user_data->get_error_messages() ) );
		exit();
	}

	// Redefining user_login ensures we return the right case in the email.
	$user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key        = get_password_reset_key( $user_data );

	if ( is_wp_error( $key ) ) {
		return $key;
	}


	$redirect_url = $redirect_url == false ? get_admin_url() : $redirect_url;

	$message = __( 'Someone has requested a password reset for the following account:','frontend-dashboard' ) . "\r\n\r\n";
	$message .= network_home_url( '/' ) . "\r\n\r\n";
	$message .= sprintf( __( 'Username: %s','frontend-dashboard'  ), $user_login ) . "\r\n\r\n";
	$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.','frontend-dashboard' ) . "\r\n\r\n";
	$message .= __( 'To reset your password, visit the following address:','frontend-dashboard' ) . "\r\n\r\n";
	$message .= '<a href="' . $redirect_url . '?action=fed_reset&key=' . $key . '&login=' . rawurlencode( $user_login ) .'">' . $redirect_url . '</a>' . "\r\n\r\n";


//	wp_die($message);

	if ( is_multisite() ) {
		$blogname = $GLOBALS['current_site']->site_name;
	} else {
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'From: ' . $blogname . ' <' . get_bloginfo( 'admin_email' ) . '>'
	);

	$title = sprintf( __( '[ %s ] Password Reset','frontend-dashboard' ), $blogname );

	if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message, $headers ) ) {
		wp_send_json_error( array(
			'user'    => __( 'The email could not be sent. Possible reason: your host may have disabled the mail() function.','frontend-dashboard' ),
			'message' => '',
			'url'     => ''
		) );
		exit();
	}

	wp_send_json_success( array(
		'user'    => $user_data,
		'message' => 'Reset email sent to your email address',
		'url'     => $redirect_url
	) );
	exit();
}

/**
 * Lost password url change filter
 */

add_filter( 'lostpassword_url', 'fed_lostpassword_url' );
function fed_lostpassword_url( $lostpassword_url ) {

	$fed_login_url    = fed_get_login_url();
	$lostpassword_url = $fed_login_url == false ? $lostpassword_url : $fed_login_url . '?action=fed_forgot';

	return $lostpassword_url;


}
