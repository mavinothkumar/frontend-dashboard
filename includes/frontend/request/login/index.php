<?php
/**
 * Login Index.
 *
 * @package frontend-dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * All Login, Register and Reset Password Request
 */

add_action( 'wp_ajax_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );
add_action( 'wp_ajax_nopriv_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );

/**
 * Login Form Post.
 */
function fed_wp_ajax_fed_login_form_post() {
	$post_payload = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	fed_verify_nonce();

	if ( isset( $post_payload['submit'] ) ) {
		if ( 'login' === $post_payload['submit'] ) {
			fed_login_form_submit( $post_payload );
		} elseif ( 'register' === $post_payload['submit'] ) {
			fed_register_form_submit( $post_payload );
		} elseif ( 'forgot_password' === $post_payload['submit'] ) {
			fed_forgot_form_submit( $post_payload );
		} elseif ( 'reset_password' === $post_payload['submit'] ) {
			fed_reset_form_submit( $post_payload );
		} else {
			do_action( 'fed_login_form_submit_custom' );
		}
	}
}