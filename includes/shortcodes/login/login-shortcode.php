<?php
/**
 * Login, Register, Forgot Password, Reset Password
 *
 * @package frontend-dashboard
 */

if ( ! shortcode_exists( 'fed_login' ) && ! function_exists( 'fed_fn_login' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_fn_login() {

		$templates = new FED_Template_Loader;
		ob_start();

		if ( is_user_logged_in() ) {
			$templates->get_template_part( 'login/registered', 'user' );
		} else {
			$templates->get_template_part( 'login/unregistered', 'user' );
		}

		return ob_get_clean();
	}

	add_shortcode( 'fed_login', 'fed_fn_login' );
}

/**
 * Restricting logged in users not to use the login page
 */
add_action( 'template_redirect', 'fed_login_template_redirect' );

function fed_login_template_redirect() {
	if ( is_user_logged_in() ) {
		$location   = fed_get_login_redirect_url();
		$login_page = fed_get_login_url();
		if ( $login_page != false && is_page( url_to_postid( $login_page ) ) ) {
			$location = $location == false ? home_url() : $location;

			wp_safe_redirect( $location );
		}

	}
}