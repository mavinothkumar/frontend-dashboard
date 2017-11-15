<?php
function fed_admin_restrict_wp_admin_tab($fed_login_register) {
	$user_role  = isset( $fed_login_register['restrict_wp']['role'] ) ? array_keys( $fed_login_register['restrict_wp']['role'] ) : array();

	$user_roles = fed_get_user_roles_without_admin();

	$array = array(
		'form'   => array(
			'method' => '',
			'class'  => 'fed_admin_menu fed_ajax',
			'attr'   => '',
			'action' => array( 'url' => '', 'action' => 'fed_admin_setting_form' ),
			'nonce'  => array( 'action' => '', 'name' => '' ),
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
				'user_value' => 'fed_wp_restrict_settings',
				'input_meta' => 'fed_admin_unique_login',
			),
		),
		'input'  => array(
			'sur'             => array(
				'col'     => 'col-md-12',
				'header'  => 'Restrict User Role(s) to access the WP admin area',
				'sub_col' => 'col-md-4'
			),
		),
		'note'   => array( 'header' => '', 'footer' => '' ),
	);

	foreach ( $user_roles as $key => $role ) {
		$c_value                                         = in_array( $key, $user_role, false ) ? 'Enable' : 'Disable';
		$array['input']['sur']['extra']['input'][ $key ] = array(
			'input_meta'    => 'fed_admin_login[role][' . $key . ']',
			'user_value'    => $c_value,
			'input_type'    => 'checkbox',
			'label'         => $role,
			'default_value' => 'Enable',
		);
	}

	apply_filters( 'fed_admin_login_wp_restrict_template', $array, $fed_login_register );

	fed_common_simple_layout( $array );
}