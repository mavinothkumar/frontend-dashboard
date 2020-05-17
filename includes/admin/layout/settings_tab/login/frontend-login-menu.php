<?php
/**
 * Login Menu.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Login Menu Tab.
 *
 * @param  array $fed_login_register  FED Login Register.
 */
function fed_admin_frontend_login_menu_tab( $fed_login_register ) {
	$array = array(
		'form'  => array(
			'method' => '',
			'class'  => 'fed_admin_menu fed_ajax',
			'attr'   => '',
			'action' => array(
				'url'        => '',
				'action'     => 'fed_ajax_request',
				'parameters' => array(
					'fed_action_hook_fn' => 'fed_admin_frontend_login_menu_save',
				),
			),
			'nonce'  => array(
				'action' => '',
				'name'   => '',
			),
			'loader' => '',
		),
		'input' => array(
			'Assign Login Menu' => array(
				'col'          => 'col-md-7',
				'name'         => __( 'Assign Login Menu', 'frontend-dashboard' ),
				'input'        => fed_get_input_details(
					array(
						'input_meta'  => 'menu_item',
						'input_value' =>
							array(
								'' => __( 'Please Select', 'frontend-dashboard' ),
							) + get_registered_nav_menus(),
						'user_value'  => isset( $fed_login_register['login_menu']['menu_item'] ) ? $fed_login_register['login_menu']['menu_item'] : '',
						'input_type'  => 'select',
					)
				),
				'help_message' => fed_show_help_message(
					array(
						'content' => __(
							'Select the respective menu items to show the Login, Logout and Dashboard Menu Items',
							'frontend-dashboard'
						),
					)
				),
			),
		),
	);

	fed_common_simple_layout( $array );
}

/**
 * Save Login Menu.
 *
 * @param  array $request  Request.
 */
function fed_admin_frontend_login_menu_save( $request ) {
	$fed_login = get_option( 'fed_admin_login' );

	$fed_login['login_menu']['menu_item'] = fed_get_data( 'menu_item', $request );

	update_option( 'fed_admin_login', $fed_login );

	wp_send_json_success( array( 'message' => __( 'Login menu successfully assigned' ) ) );

}
