<?php
if ( ! shortcode_exists( 'fed_user' ) && ! function_exists( 'fed_user_fn' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_user_fn( $role ) {

		$role = shortcode_atts( array(
			'role' => 'subscriber',
		), $role, 'fed_user' );


		$templates = new FED_Template_Loader;
		ob_start();
		$templates->set_template_data( $role,'fed_user_attr' );
		$templates->get_template_part( 'user_role' );


		return ob_get_clean();
	}

	add_shortcode( 'fed_user', 'fed_user_fn' );
}