<?php
/**
 * All Login, Register and Reset Password Request
 */

add_action( 'wp_ajax_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );
add_action( 'wp_ajax_nopriv_fed_login_form_post', 'fed_wp_ajax_fed_login_form_post' );

function fed_wp_ajax_fed_login_form_post() {
	if ( isset( $_POST['submit'] ) ) {
		$form = fed_login_form();
		if ( $_POST['submit'] == $form['Login']['button'] ) {
			fed_login_form_submit( $_POST );
		} elseif ( $_POST['submit'] == $form['Register']['button'] ) {
			fed_register_form_submit( $_POST );
		} elseif ( $_POST['submit'] == $form['Forgot Password']['button'] ) {
			fed_forgot_form_submit( $_POST );
		} elseif ( $_POST['submit'] == $form['Reset Password']['button'] ) {
			fed_reset_form_submit( $_POST );
		} else {
			do_action( 'fed_login_form_submit_custom' );
		}
	}
}