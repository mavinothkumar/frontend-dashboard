<?php
/**
 * Enqueue Script at Admin and Front End
 */

add_action( 'admin_enqueue_scripts', 'fed_script_admin' );
add_action( 'wp_enqueue_scripts', 'fed_script_front_end' );
/**
 * Admin Scripts.
 *
 * @param $hook
 */
function fed_script_admin( $hook ) {
	
	if ( ($hook === 'post.php' || $hook === 'profile.php') || (isset( $_GET['page'] ) && in_array( $_GET['page'], fed_get_script_loading_pages(),false ) )) {

		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'fed_sweetalert', plugins_url( '/common/assets/js/sweetalert2.js', BC_FED_PLUGIN ), array() );

		wp_enqueue_script( 'fed_admin_script', plugins_url( '/admin/assets/fed_admin_script.js', BC_FED_PLUGIN ), array( ) );

		wp_enqueue_script( 'fed_bootstrap_script', plugins_url( '/common/assets/js/bootstrap.js', BC_FED_PLUGIN ), array() );

		wp_enqueue_script( 'fed_jscolor_script', plugins_url( '/common/assets/js/jscolor.js', BC_FED_PLUGIN ), array() );

		wp_enqueue_script( 'fed_flatpickr', plugins_url( '/common/assets/js/flatpickr.js', BC_FED_PLUGIN ), array() );

		wp_enqueue_style( 'fed_admin_bootstrap',
			plugins_url( '/common/assets/css/bootstrap.css', BC_FED_PLUGIN ),
			array(), BC_FED_PLUGIN_VERSION, 'all' );


		wp_enqueue_style( 'fed_frontend_sweetalert',
			plugins_url( '/common/assets/css/sweetalert2.css', BC_FED_PLUGIN ),
			array(), BC_FED_PLUGIN_VERSION, 'all' );


		wp_enqueue_style( 'fed_admin_font_awesome',
			plugins_url( '/common/assets/css/font-awesome.css', BC_FED_PLUGIN ),
			array(), BC_FED_PLUGIN_VERSION, 'all' );

		wp_enqueue_style( 'fed_flatpikcer',
			plugins_url( '/common/assets/css/flatpickr.css', BC_FED_PLUGIN ),
			array(), BC_FED_PLUGIN_VERSION, 'all' );


		wp_enqueue_style( 'fed_admin_style',
			plugins_url( 'admin/assets/fed_admin_style.css', BC_FED_PLUGIN ),
			array(), BC_FED_PLUGIN_VERSION, 'all' );

		$translation_array = array(
			'plugin_url'          => plugins_url( BC_FED_PLUGIN_NAME ),
			'payment_info'        => get_option( 'fed_payment_info', 'no' ),
			'fed_admin_form_post' => admin_url( 'admin-ajax.php?action=fed_admin_form_post&nonce=' . wp_create_nonce( "fed_admin_form_post" ) ),
		);

		wp_localize_script( 'fed_admin_script', 'fed', $translation_array );

		wp_enqueue_media();
	}
}

/**
 * Frontend Script.
 */
function fed_script_front_end() {

	wp_enqueue_script( 'fed_script', plugins_url( '/common/assets/js/fed_script.js', BC_FED_PLUGIN ), array( 'jquery' ) );
	wp_enqueue_script( 'fed_sweetalert', plugins_url( '/common/assets/js/sweetalert2.js', BC_FED_PLUGIN ), array() );

	wp_enqueue_script( 'fed_bootstrap_script', plugins_url( '/common/assets/js/bootstrap.js', BC_FED_PLUGIN ), array() );

	wp_enqueue_script( 'fed_jscolor_script', plugins_url( '/common/assets/js/jscolor.js', BC_FED_PLUGIN ), array() );

	wp_enqueue_script( 'fed_flatpickr', plugins_url( '/common/assets/js/flatpickr.js', BC_FED_PLUGIN ), array() );

	wp_enqueue_style( 'fed_frontend_bootstrap',
		plugins_url( '/common/assets/css/bootstrap.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_style( 'fed_frontend_font_awesome',
		plugins_url( '/common/assets/css/font-awesome.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_style( 'fed_frontend_sweetalert',
		plugins_url( '/common/assets/css/sweetalert2.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_style( 'fed_frontend_animate',
		plugins_url( '/common/assets/css/animate.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_style( 'fed_flatpikcer',
		plugins_url( '/common/assets/css/flatpickr.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_style( 'fed_fontend_style',
		plugins_url( '/common/assets/css/common-style.css', BC_FED_PLUGIN ),
		array(), BC_FED_PLUGIN_VERSION, 'all' );

	wp_enqueue_script( 'recaptcha', 'https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit&hl=en', array(), BC_FED_PLUGIN_VERSION, 'all' );

	// Pass PHP value to JavaScript
	$translation_array = array(
		'plugin_url'          => plugins_url( BC_FED_PLUGIN_NAME ),
		'payment_info'        => get_option( 'fed_payment_info', 'no' ),
		'fed_login_form_post' => admin_url( 'admin-ajax.php?action=fed_login_form_post&nonce=' . wp_create_nonce( "fed_login_form_post" ) ),
		'fed_captcha_details' => fed_get_captcha_details(),
	);

	wp_localize_script( 'fed_script', 'fed', $translation_array );

	wp_enqueue_media();

}

/**
 * Add Async Attribute
 *
 * @param string $tag Tag
 * @param string $handle Handle
 *
 * @return mixed
 */
function fed_add_async_attribute( $tag, $handle ) {
	if ( 'recaptcha' !== $handle ) {
		return $tag;
	}

	return str_replace( ' src', ' defer="defer" async="async" src', $tag );
}

add_filter( 'script_loader_tag', 'fed_add_async_attribute', 10, 2 );