<?php
/**
 * Restrict Username.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Restrict User Name Tab.
 *
 * @param  array $fed_login_register  Login Register.
 */
function fed_admin_username_restrict_tab( $fed_login_register ) {

	$array = array(
		'form'   => array(
			'method' => '',
			'class'  => 'fed_admin_menu fed_ajax',
			'attr'   => '',
			'action' => array(
				'url'    => '',
				'action' => 'fed_admin_setting_form',
			),
			'nonce'  => array(
				'action' => '',
				'name'   => '',
			),
			'loader' => '',
		),
		'hidden' => array(
			'fed_admin_unique'       => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_login_details',
				'input_meta' => 'fed_admin_unique',
			),
			'fed_admin_unique_login' => array(
				'input_type' => 'hidden',
				'user_value' => 'fed_restrict_username',
				'input_meta' => 'fed_admin_unique_login',
			),
		),
		'input'  => array(
			'Restrict Username' => array(
				'col'          => 'col-md-12',
				'name'         => __( 'Restrict Username', 'frontend-dashboard' ),
				'input'        => fed_get_input_details(
					array(
						'input_meta'  => 'fed_admin_login[restrict_username]',
						'user_value'  => isset( $fed_login_register['restrict_username'] ) ? $fed_login_register['restrict_username'] : '',
						'input_type'  => 'multi_line',
						'placeholder' => 'Add Restrictive User Name by Comma Separated like ban,admin,support',
					)
				),
				'help_message' => fed_show_help_message( array( 'content' => 'Add Restrictive User Name by Comma Separated' ) ),
			),
		),
		'note'   => array(
			'header' => '',
			'footer' => 'Add Restrictive User Name by Comma Separated',
		),
	);

	fed_common_simple_layout( $array );
}
