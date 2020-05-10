<?php
/**
 * Login, Register, Forgot Password, Reset Password.
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

		$templates = new FED_Template_Loader();
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

/**
 * Login Template Redirect.
 */
function fed_login_template_redirect() {
	if ( is_user_logged_in() ) {
		$login_page = fed_get_login_url();
		// Check if WordPress VIP.
		// phpcs:ignore
		$url_to_post_id = function_exists( 'wpcom_vip_url_to_postid' ) ? wpcom_vip_url_to_postid( $login_page ) : url_to_postid( $login_page );
		$location       = fed_get_login_redirect_url();

		if ( ( false != $login_page ) && is_page( $url_to_post_id ) ) {
			$location = ( false == $location ) ? home_url() : $location;

			wp_safe_redirect( $location );
			exit();
		}
	}
}
