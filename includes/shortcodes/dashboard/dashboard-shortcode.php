<?php
/**
 * Dashboard
 *
 * @package frontend-dashboard
 */

if ( ! shortcode_exists( 'fed_dashboard' ) && ! function_exists( 'fed_fn_dashboard' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_fn_dashboard() {

		$templates = new FED_Template_Loader;

		ob_start();

		$templates->get_template_part( 'dashboard' );

		return ob_get_clean();
	}

	add_shortcode( 'fed_dashboard', 'fed_fn_dashboard' );
}

/**
 * Restricting logged in users not to use the dashboard page
 */
add_action( 'template_redirect', 'fed_dashboard_template_redirect' );

function fed_dashboard_template_redirect() {

	if ( ! is_user_logged_in() ) {
		$location   = fed_get_dashboard_url();
		$login_page = fed_get_login_url();
		if ( $location != false && get_permalink() ==  $location) {
			$login_page = $login_page == false ? wp_login_url() : $login_page;

			wp_safe_redirect( $login_page );
		}
	}
}

add_filter('widget_text','do_shortcode');