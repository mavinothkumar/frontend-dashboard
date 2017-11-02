<?php

/**
 * Login Form Filters and hook
 */

function fed_login_form() {
	$form = array(
		'Login'           => fed_login_only(),
		'Register'        => fed_register_only(),
		'Forgot Password' => fed_forgot_password_only(),
		'Reset Password'  => fed_reset_password_only()
	);

	return apply_filters( 'fed_login_form_filter', $form );
}

function fed_login_only() {
	$login = array(
		'menu'     => array(
			'id'   => 'fed_login_tab',
			'name' => 'Login',
		),
		'content'  => array(
			'user_login'    => array(
				'name'        => 'User Name',
				'input'       => fed_input_box( 'user_login', array( 'placeholder' => 'User Name' ), 'single_line' ),
				'input_order' => 7
			),
			'user_password' => array(
				'name'        => 'Password',
				'input'       => fed_input_box( 'user_password', array( 'placeholder' => 'Password' ), 'password' ),
				'input_order' => 9
			),
			'remember'      => array(
				'name'        => '',
				'input'       => fed_input_box( 'remember', array(
					'name'        => 'remember',
					'placeholder' => 'Password',
					'label'       => 'Remember Me'
				), 'checkbox' ),
				'input_order' => 15
			),
		),
		'selected' => true,
		'button'   => 'Login'
	);

	return apply_filters( 'fed_login_only_filter', $login );
}

function fed_register_only() {
	$register = array(
		'menu'     => array(
			'id'   => 'fed_register_tab',
			'name' => 'Register',
		),
		'content'  => fed_get_registration_content_fields(),
		'selected' => false,
		'button'   => 'Register'
	);



	return apply_filters('fed_register_only_filter',$register);
}

function fed_forgot_password_only() {
	return array(
		'menu'     => array(
			'id'   => 'fed_forgot_password_tab',
			'name' => 'Forgot Password',
		),
		'content'  => array(
			'user_email' => array(
				'name'        => 'User Name / Email Address',
				'input'       => fed_input_box( 'user_login', array( 'placeholder' => 'User Name / Email Address' ), 'single_line' ),
				'input_order' => 7
			),
		),
		'selected' => false,
		'button'   => 'Forgot Password'
	);
}

function fed_reset_password_only() {
	return array(
		'menu'     => array(
			'id'   => 'fed_reset_password_tab',
			'name' => 'Reset Password',
		),
		'content'  => array(
			'user_password'         => array(
				'name'        => 'Password',
				'input'       => fed_input_box( 'user_password', array( 'placeholder' => 'Password' ), 'password' ),
				'input_order' => 7
			),
			'confirmation_password' => array(
				'name'        => 'Confirmation Password',
				'input'       => fed_input_box( 'confirmation_password', array( 'placeholder' => 'Confirmation Password' ), 'password' ),
				'input_order' => 10
			),
		),
		'selected' => false,
		'button'   => 'Reset Password'
	);
}

function fed_registration_mandatory_fields() {
	$fields = fed_process_user_profile_required_field();

	return apply_filters( 'fed_registration_mandatory_fields', $fields );
}

function fed_login_mandatory_fields() {
	$fields = array(
		'user_login'    => __( 'Please enter user name', 'fed' ),
		'user_password' => __( 'Please enter user password', 'fed' )
	);

	return apply_filters( 'fed_login_mandatory_fields', $fields );
}