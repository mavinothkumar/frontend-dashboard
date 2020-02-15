<?php
/**
 * Register Only
 *
 * @package frontend-dashboard
 */

if ( ! shortcode_exists( 'fed_register_only' ) && ! function_exists( 'fed_fn_register_only' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_fn_register_only() {

		$templates = new FED_Template_Loader;
		ob_start();

		if ( is_user_logged_in() ) {
			$templates->get_template_part( 'login/registered', 'user' );
		} else {
			$templates->get_template_part( 'login/register-only', 'user' );
		}

		return ob_get_clean();
	}

	add_shortcode( 'fed_register_only', 'fed_fn_register_only' );
}