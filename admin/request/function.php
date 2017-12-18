<?php

/**
 * Get Login URL
 *
 * @return bool|false|string
 */
function fed_get_login_url() {
	$fed_admin_options = get_option( 'fed_admin_login', false );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_login_url'] ) && $fed_admin_options['settings']['fed_login_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_login_url'] );
	}

	return false;
}


/**
 * Get Registration URL
 */
function fed_get_registration_url() {
	$fed_admin_options = get_option( 'fed_admin_login', false );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_register_url'] ) && $fed_admin_options['settings']['fed_register_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_register_url'] );
	}

	return false;
}

/**
 * Get Forgot password URL
 * @return bool|false|string
 */
function fed_get_forgot_password_url() {
	$fed_admin_options = get_option( 'fed_admin_login', false );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_forgot_password_url'] ) && $fed_admin_options['settings']['fed_forgot_password_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_forgot_password_url'] );
	}

	return false;
}

/**
 * Get Login Redirect URL
 *
 * @return bool|false|string
 */
function fed_get_login_redirect_url() {
	$fed_admin_options = get_option( 'fed_admin_login' );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_redirect_login_url'] ) && $fed_admin_options['settings']['fed_redirect_login_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_redirect_login_url'] );
	}

	return false;
}

/**
 * Get Logout Redirect URL
 *
 * @return bool|false|string
 */
function fed_get_logout_redirect_url() {
	$fed_admin_options = get_option( 'fed_admin_login' );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_redirect_logout_url'] ) && $fed_admin_options['settings']['fed_redirect_logout_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_redirect_logout_url'] );
	}

	return false;
}

/**
 * Get Registration Redirect URL
 *
 * @return bool|false|string
 */
function fed_get_register_redirect_url() {
	$fed_admin_options = get_option( 'fed_admin_login' );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_redirect_register_url'] ) && $fed_admin_options['settings']['fed_redirect_register_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_redirect_register_url'] );
	}

	return false;
}


/**
 * Get Dashboard URL
 *
 * @return bool|false|string
 */
function fed_get_dashboard_url() {
	$fed_admin_options = get_option( 'fed_admin_login' );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['fed_dashboard_url'] ) && $fed_admin_options['settings']['fed_dashboard_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['fed_dashboard_url'] );
	}

	return false;
}

/**
 * Get Invoice URL
 */

function fed_get_invoice_url() {
	$fed_admin_options = get_option( 'fed_admin_settings_invoice' );
	if ( $fed_admin_options && isset( $fed_admin_options['settings']['invoice_url'] ) && $fed_admin_options['settings']['invoice_url'] != '' ) {
		return get_permalink( $fed_admin_options['settings']['invoice_url'] );
	}

	return false;
}