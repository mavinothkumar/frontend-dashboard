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

/**
 * @return mixed|void
 */
function fed_login_only() {
	$login = array(
		'menu'     => array(
			'id'   => 'fed_login_tab',
			'name' => __( 'Login', 'frontend-dashboard' ),
		),
		'content'  => array(
			'user_login'    => array(
				'name'        => __( 'User Name', 'frontend-dashboard' ),
				'input'       => fed_input_box( 'user_login', array( 'placeholder' => __( 'User Name', 'frontend-dashboard' ) ), 'single_line' ),
				'input_order' => 7
			),
			'user_password' => array(
				'name'        => __( 'Password', 'frontend-dashboard' ),
				'input'       => fed_input_box( 'user_password', array( 'placeholder' => __( 'Password', 'frontend-dashboard' ) ), 'password' ),
				'input_order' => 9
			),
			'remember'      => array(
				'name'        => '',
				'input'       => fed_input_box( 'remember', array(
					'name'        => 'remember',
					'label'       => __( 'Remember Me', 'frontend-dashboard' )
				), 'checkbox' ),
				'input_order' => 15
			),
		),
		'selected' => true,
		'button'   => __( 'Login', 'frontend-dashboard' )
	);

	return apply_filters( 'fed_login_only_filter', $login );
}

/**
 * @return mixed|void
 */
function fed_register_only() {
	$register = array(
		'menu'     => array(
			'id'   => 'fed_register_tab',
			'name' => __( 'Register', 'frontend-dashboard' ),
		),
		'content'  => fed_get_registration_content_fields(),
		'selected' => false,
		'button'   => __( 'Register', 'frontend-dashboard' )
	);


	return apply_filters( 'fed_register_only_filter', $register );
}

/**
 * @return array
 */
function fed_forgot_password_only() {
	return array(
		'menu'     => array(
			'id'   => 'fed_forgot_password_tab',
			'name' => __( 'Forgot Password', 'frontend-dashboard' ),
		),
		'content'  => array(
			'user_email' => array(
				'name'        => __( 'User Name / Email Address', 'frontend-dashboard' ),
				'input'       => fed_input_box( 'user_login', array( 'placeholder' => __( 'User Name / Email Address', 'frontend-dashboard' ) ), 'single_line' ),
				'input_order' => 7
			),
		),
		'selected' => false,
		'button'   => __( 'Forgot Password', 'frontend-dashboard' )
	);
}

/**
 * @return array
 */
function fed_reset_password_only() {
	return array(
		'menu'     => array(
			'id'   => 'fed_reset_password_tab',
			'name' => __( 'Reset Password', 'frontend-dashboard' ),
		),
		'content'  => array(
			'user_password'         => array(
				'name'        => __( 'Password', 'frontend-dashboard' ),
				'input'       => fed_input_box( 'user_password', array( 'placeholder' => __( 'Password', 'frontend-dashboard' ) ), 'password' ),
				'input_order' => 7
			),
			'confirmation_password' => array(
				'name'        => __( 'Confirmation Password', 'frontend-dashboard' ),
				'input'       => fed_input_box( 'confirmation_password', array( 'placeholder' => __( 'Confirmation Password', 'frontend-dashboard' ) ), 'password' ),
				'input_order' => 10
			),
			'key'                   => array(
				'name'        => '',
				'input'       => fed_input_box( 'key', array( 'value' => isset( $_GET['key'] ) ? $_GET['key'] : '' ), 'hidden' ),
				'input_order' => 30
			),
			'login'                 => array(
				'name'        => '',
				'input'       => fed_input_box( 'login', array( 'value' => isset( $_GET['login'] ) ? $_GET['login'] : '' ), 'hidden' ),
				'input_order' => 30
			),
		),
		'selected' => false,
		'button'   => __( 'Reset Password', 'frontend-dashboard' )
	);
}

/**
 * @return mixed|void
 */
function fed_registration_mandatory_fields() {
	$fields = fed_process_user_profile_required_field();

	return apply_filters( 'fed_registration_mandatory_fields', $fields );
}

/**
 * @return mixed|void
 */
function fed_login_mandatory_fields() {
	$fields = array(
		'user_login'    => __( 'Please enter user name', 'frontend-dashboard' ),
		'user_password' => __( 'Please enter user password', 'frontend-dashboard' )
	);

	return apply_filters( 'fed_login_mandatory_fields', $fields );
}