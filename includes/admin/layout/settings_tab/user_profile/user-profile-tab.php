<?php
/**
 * User Profile Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Profile Layout
 */
function fed_user_profile_layout_design() {
	$fed_admin_options = get_option( 'fed_admin_settings_upl' );
	$tabs              = fed_user_profile_layout_options( $fed_admin_options );
	fed_common_layouts_admin_settings( $fed_admin_options, $tabs );
}

/**
 * User Profile Layout Options.
 *
 * @param  array $fed_admin_options  Admin Options.
 *
 * @return mixed|void
 */
function fed_user_profile_layout_options( $fed_admin_options ) {
	$options = array(
		'fed_admin_user_profile_layout_colors'    => array(
			'icon'      => 'fa fa-paint-brush',
			'name'      => __( 'Colors', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_profile_colors_tab',
			'arguments' => $fed_admin_options,
		),
		'fed_admin_user_profile_layout_settings'  => array(
			'icon'      => 'fa fa-cogs',
			'name'      => __( 'Settings', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_profile_settings_tab',
			'arguments' => $fed_admin_options,
		),
		'fed_admin_user_profile_layout_templates' => array(
			'icon'      => 'fa fa-palette',
			'name'      => __( 'Templates', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_profile_templates_tab',
			'arguments' => $fed_admin_options,
		),
		'fed_admin_user_profile_layout_hide_bar'  => array(
			'icon'      => 'fa fa-eye-slash',
			'name'      => __( 'Admin Bar', 'frontend-dashboard' ),
			'callable'  => 'fed_admin_user_profile_hide_bar_tab',
			'arguments' => get_option( 'fed_admin_settings_upl_hide_admin_bar' ),
		),
	);

	return apply_filters( 'fed_customize_admin_user_profile_layout_options', $options, $fed_admin_options );
}
