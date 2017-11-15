<?php
function fed_admin_register_settings_tab( $fed_login_register ) {
	$user_role  = isset( $fed_login_register['register']['role'] ) ? array_keys( $fed_login_register['register']['role'] ) : array();
	$name       = isset( $fed_login_register['register']['name'] ) ? $fed_login_register['register']['name'] : 'User Role';
	$position   = isset( $fed_login_register['register']['position'] ) ? $fed_login_register['register']['position'] : 999;
	$user_roles = fed_get_user_roles();

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
				'user_value' => 'fed_register_settings',
				'input_meta' => 'fed_admin_unique_login',
			),
		),
		'input'  => array(
			'Menu Name'       => array(
				'col'   => 'col-md-6',
				'name'  => 'Menu Name',
				'input' => fed_get_input_details( array(
					'placeholder' => '(eg) User Role',
					'input_meta'  => 'fed_admin_login[name]',
					'user_value'  => $name,
					'input_type'  => 'single_line',
					'required'    => true
				) )
			),
			'Menu Name Order' => array(
				'col'   => 'col-md-6',
				'name'  => 'Menu Name Order',
				'input' => fed_get_input_details( array(
					'placeholder' => '(eg) 40',
					'input_meta'  => 'fed_admin_login[position]',
					'user_value'  => $position,
					'input_type'  => 'number',
					'required'    => true
				) )
			),
			'sur'             => array(
				'col'     => 'col-md-12',
				'header'  => 'Show User Role(s) in Register Form',
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

	apply_filters( 'fed_admin_login_register_template', $array, $fed_login_register );

	fed_common_simple_layout( $array );



}
