<?php
/**
 * All Login, Register and Reset Password Request
 */

add_action( 'wp_ajax_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );
add_action( 'wp_ajax_nopriv_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );

function fed_wp_ajax_fed_login_form_post() {
	if ( isset( $_POST['submit'] ) ) {
		if ( 'Login' === $_POST['submit'] ) {
			fed_login_form_submit( $_POST );
		} elseif ( 'Register' === $_POST['submit'] ) {
			fed_register_form_submit( $_POST );
		} elseif ( 'Forgot Password' === $_POST['submit'] ) {
			fed_forgot_form_submit( $_POST );
		} elseif ( 'Reset Password' === $_POST['submit'] ) {
			fed_reset_form_submit( $_POST );
		} else {
			do_action( 'fed_login_form_submit_custom' );
		}
	}
}