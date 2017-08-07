<?php
/**
 * Handle all Admin login settings request
 */
function fed_admin_setting_login_request() {
	$message         = '';
	$requests        = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$fed_admin_login = get_option( 'fed_admin_login' );
	$request         = $requests['fed_admin_login'];

	if ( isset( $requests['fed_admin_unique_login'] ) && 'fed_login_settings' === $requests['fed_admin_unique_login'] ) {
		$fed_admin_login['settings'] = fed_admin_login_settings_save( $request );
		$message                     = 'Login ';
	}

	if ( isset( $requests['fed_admin_unique_login'] ) && 'fed_register_settings' === $requests['fed_admin_unique_login'] ) {
		$fed_admin_login['register'] = fed_admin_login_register_save( $request );
		$message                     = 'Register ';
	}

	apply_filters( 'fed_admin_login', $fed_admin_login );

	update_option( 'fed_admin_login', $fed_admin_login );

	wp_send_json_success( array(
		'message' => __( $message . ' Settings Updated Successfully' )
	) );


}

function fed_admin_login_settings_save( $request ) {
	return   array(
		'fed_login_url'           => isset( $request['settings']['fed_login_url'] ) ? (int) $request['settings']['fed_login_url'] : '',
		'fed_register_url'           => isset( $request['settings']['fed_register_url'] ) ? (int) $request['settings']['fed_register_url'] : '',
		'fed_forgot_password_url'           => isset( $request['settings']['fed_forgot_password_url'] ) ? (int) $request['settings']['fed_forgot_password_url'] : '',
		'fed_redirect_login_url'  => isset( $request['settings']['fed_redirect_login_url'] ) ? (int) $request['settings']['fed_redirect_login_url'] : '',
		'fed_redirect_logout_url' => isset( $request['settings']['fed_redirect_logout_url'] ) ? (int) $request['settings']['fed_redirect_logout_url'] : '',
		'fed_dashboard_url'       => isset( $request['settings']['fed_dashboard_url'] ) ? (int) $request['settings']['fed_dashboard_url'] : '',
	);

}

function fed_admin_login_register_save( $request ) {
	return array(
		'role'           => isset( $request['role'] ) ? $request['role'] : array(),
		'name'           => isset( $request['name'] ) ? $request['name'] : 'User Role',
		'position'           => isset( $request['position'] ) ? $request['position'] : 999,
	);
}

/**
 * Login URL
 */
add_filter( 'login_url', 'fed_login_url', 10, 3 );
/**
 * Custom Login URL.
 *
 * @param string $login_url Login URL
 * @param string $redirect Redirect URL
 * @param string $force_reauth ReAuth
 *
 * @return string
 */
function fed_login_url( $login_url, $redirect, $force_reauth ) {
	$fed_get_login_url = fed_get_login_url();
	$login_url         = $fed_get_login_url === false ? $login_url : $fed_get_login_url;

	return $login_url;
}

/**
 * Login Redirect URL
 */
add_filter( 'login_redirect', 'fed_login_redirect', 10, 3 );

/**
 * Login Redirect.
 *
 * @param string $redirect_to Redirect to URL
 * @param string $request Request
 * @param WP_User $user User
 *
 * @return bool|false|string
 */
function fed_login_redirect( $redirect_to, $request, $user ) {
	$fed_get_login_redirect_url = fed_get_login_redirect_url();
	$redirect_to                = $fed_get_login_redirect_url === false ? $redirect_to : $fed_get_login_redirect_url;

	return $redirect_to;
}

/**
 * Logout Redirect URL
 */
add_filter( 'logout_redirect', 'fed_logout_redirect', 10, 3 );
/**
 * Logout Redirect URL
 *
 * @param string $logout_url Logout URL.
 * @param string $redirect Redirect URL.
 * @param WP_User $user User
 *
 * @return bool|false|string
 */
function fed_logout_redirect( $logout_url, $redirect, $user ) {
	$fed_get_logout_redirect_url = fed_get_logout_redirect_url();
	$logout_url                  = $fed_get_logout_redirect_url == false ? $logout_url : $fed_get_logout_redirect_url;

	return $logout_url;
}



