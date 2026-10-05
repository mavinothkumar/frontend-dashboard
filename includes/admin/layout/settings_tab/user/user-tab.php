<?php
/**
 * User Tab.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Option Tab
 */
function fed_admin_user_options_tab() {
	$fed_admin_options = get_option( 'fed_admin_settings_user' );
	$tabs              = fed_get_admin_user_options( $fed_admin_options );
	fed_common_layouts_admin_settings( $fed_admin_options, $tabs );
}

/**
 * Admin User Options.
 *
 * @param  array $fed_admin_options  Admin options.
 *
 * @return mixed|void
 */
function fed_get_admin_user_options( $fed_admin_options ) {
	return apply_filters(
		'fed_customize_admin_user_options',
		array(
			'fed_admin_user_profile_settings'  => array(
				'icon'      => 'fa fa-user-plus',
				'name'      => __( 'Add/Delete Custom Role', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_user_role_tab',
				'arguments' => $fed_admin_options,
			),
			'fed_admin_user_upload_permission' => array(
				'icon'      => 'fa fa-upload',
				'name'      => __( 'User Upload Permission', 'frontend-dashboard' ),
				'callable'  => 'fed_admin_user_upload_permission_tab',
				'arguments' => $fed_admin_options,
			),
		),
		$fed_admin_options
	);
}
