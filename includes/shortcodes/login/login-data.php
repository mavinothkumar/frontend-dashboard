<?php
/**
 * Login Form Filters and hook.
 *
 * @package Frontend Dashboard.
 */

/**
 * Login Form.
 *
 * @return mixed|void
 */
function fed_login_form() {
	$form = array(
		'login'           => array(
			'label' => __( 'Login', 'frontend-dashboard' ),
			'html'  => fed_login_only(),
		),
		'register'        => array(
			'label' => __( 'Register', 'frontend-dashboard' ),
			'html'  => fed_register_only(),
		),
		'forgot_password' => array(
			'label' => __( 'Forgot Password', 'frontend-dashboard' ),
			'html'  => fed_forgot_password_only(),
		),
		'reset_password'  => array(
			'label' => __( 'Reset Password', 'frontend-dashboard' ),
			'html'  => fed_reset_password_only(),
		),
	);

	return apply_filters( 'fed_login_form_filter', $form );
}

/**
 * Login Only.
 *
 * @return mixed
 */
function fed_login_only() {
	$login_info = fed_fetch_table_rows_with_key( BC_FED_TABLE_USER_PROFILE, 'input_meta' );

	$login = array(
		'menu'     => array(
			'id'   => 'fed_login_tab',
			'name' => __( 'Login', 'frontend-dashboard' ),
		),
		'content'  => array(
			'user_login'    => array(
				'name' => sprintf(
				/* translators: %s: User Login Label */
					esc_html__(
						'%s',
						'frontend-dashboard'
					), $login_info['user_login']['label_name']
				),

				'input'       => fed_input_box(
					'user_login',
					array(
						'placeholder' => sprintf(
						/* Translators:  %s: User Login Placeholder */
							esc_html__(
								'%s',
								'frontend-dashboard'
							), $login_info['user_login']['placeholder']
						),
					), 'single_line'
				),
				'input_order' => 7,
				'input_type'  => 'single_line',
			),
			'user_password' => array(
				'name'        => sprintf(
				/* translators: %s: User Password Label */
					esc_html__(
						'%s',
						'frontend-dashboard'
					), $login_info['user_pass']['label_name']
				),
				'input'       => fed_input_box(
					'user_password',
					array(
						'placeholder' => sprintf(
						/* translators: %s: User Password Placeholder */
							esc_html__(
								'%s',
								'frontend-dashboard'
							), $login_info['user_pass']['label_name']
						),
					), 'password'
				),
				'input_order' => 9,
				'input_type'  => 'single_line',
			),
			'remember'      => array(
				'name'        => '',
				'input'       => fed_input_box(
					'remember',
					array(
						'name'  => 'remember',
						'label' => apply_filters(
							'fed_login_remember_me',
							__( 'Remember Me', 'frontend-dashboard' )
						),
					), 'checkbox'
				),
				'input_order' => 15,
				'input_type'  => '',
			),
		),
		'selected' => true,
		'button'   => __( 'Login', 'frontend-dashboard' ),
	);

	return apply_filters( 'fed_login_only_filter', $login );
}

/**
 * Register Only.
 *
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
		'button'   => __( 'Register', 'frontend-dashboard' ),
	);

	return apply_filters( 'fed_register_only_filter', $register );
}

/**
 * Forgot Password Only.
 *
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
				'input'       => fed_input_box(
					'user_login',
					array( 'placeholder' => __( 'User Name / Email Address', 'frontend-dashboard' ) ),
					'single_line'
				),
				'input_order' => 7,
			),
		),
		'selected' => false,
		'button'   => __( 'Forgot Password', 'frontend-dashboard' ),
	);
}

/**
 * Reset Password Only.
 *
 * @return array
 */
function fed_reset_password_only() {
	$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );

	return array(
		'menu'     => array(
			'id'   => 'fed_reset_password_tab',
			'name' => __( 'Reset Password', 'frontend-dashboard' ),
		),
		'content'  => array(
			'user_password'         => array(
				'name'        => __( 'Password', 'frontend-dashboard' ),
				'input'       => fed_input_box(
					'user_password',
					array( 'placeholder' => __( 'Password', 'frontend-dashboard' ) ), 'password'
				),
				'input_order' => 7,
			),
			'confirmation_password' => array(
				'name'        => __( 'Confirmation Password', 'frontend-dashboard' ),
				'input'       => fed_input_box(
					'confirmation_password',
					array( 'placeholder' => __( 'Confirmation Password', 'frontend-dashboard' ) ),
					'password'
				),
				'input_order' => 10,
			),
			'key'                   => array(
				'name'        => '',
				'input'       => fed_input_box(
					'key',
					array( 'value' => isset( $get_payload['key'] ) ? $get_payload['key'] : '' ), 'hidden'
				),
				'input_order' => 30,
			),
			'login'                 => array(
				'name'        => '',
				'input'       => fed_input_box(
					'login',
					array( 'value' => isset( $get_payload['login'] ) ? $get_payload['login'] : '' ), 'hidden'
				),
				'input_order' => 30,
			),
		),
		'selected' => false,
		'button'   => __( 'Reset Password', 'frontend-dashboard' ),
	);
}

/**
 * Registration Mandatory Fields.
 *
 * @return mixed|void
 */
function fed_registration_mandatory_fields() {
	$fields = fed_process_user_profile_required_field();

	return apply_filters( 'fed_registration_mandatory_fields', $fields );
}

/**
 * Login Mandatory Fields.
 *
 * @return mixed|void
 */
function fed_login_mandatory_fields() {
	$fields = array(
		'user_login'    => __( 'Please enter user name', 'frontend-dashboard' ),
		'user_password' => __( 'Please enter user password', 'frontend-dashboard' ),
	);

	return apply_filters( 'fed_login_mandatory_fields', $fields );
}
