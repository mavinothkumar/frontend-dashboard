<?php
/**
 * Login Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Login Tab
 */
function fed_admin_login_tab() {
	$fed_login = get_option( 'fed_admin_login' );
	$tabs      = fed_get_admin_login_options( $fed_login );
	fed_common_layouts_admin_settings( $fed_login, $tabs );
}

/**
 * Admin Login Options.
 *
 * @param  string $fed_login  Login.
 *
 * @return array
 */
function fed_get_admin_login_options( $fed_login ) {
	return apply_filters(
		'fed_customize_admin_login_options', array(
			'fed_admin_login_settings'      => array(
				'icon'      => 'fa fa-cogs',
				'name'      => __( 'Settings', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_login_settings_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_register_settings'   => array(
				'icon'      => 'fas fa-door-open',
				'name'      => __( 'Register', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_register_settings_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_restrict_wp_admin'   => array(
				'icon'      => 'fa fa-user-secret',
				'name'      => __( 'Restrict WP Admin Area', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_restrict_wp_admin_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_username_restrict'   => array(
				'icon'      => 'fa fa-ban',
				'name'      => __( 'Restrict Username', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_username_restrict_tab',
				'arguments' => $fed_login,
			),
			'fed_admin_frontend_login_menu' => array(
				'icon'      => 'fa fa-align-justify',
				'name'      => __( 'Frontend Login Menu', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_frontend_login_menu_tab',
				'arguments' => $fed_login,
			),
		)
	);
}
