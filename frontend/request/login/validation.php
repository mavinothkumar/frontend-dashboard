<?php

/**
 * Registration Form Validation
 *
 * @param array $post post.
 * @param bool $tab User Profile Save or Register.
 *
 * @return bool|WP_Error
 */
function fed_validate_registration_form( $post ) {
	/**
	 * Both Password Match.
	 */
	$fed_error        = new WP_Error();
	$mandatory_fields = fed_registration_mandatory_fields();
	$role             = fed_is_role_in_registration();


	if ( $post['user_pass'] !== $post['confirmation_password'] ) {
		$fed_error->add( 'password_not_match', __( 'Password not match','frontend-dashboard' ) );
	}

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( $post[ $key ] == '' ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( $role && ! array_key_exists( $post['role'], $role ) ) {
		$fed_error->add( 'invalid_role', __('Invalid Role','frontend-dashboard') );
	}

	if ( ! $role && isset($post['role'])){
		$fed_error->add( 'invalid_role', __('You are trying to hack the user role','frontend-dashboard') );
	}


	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}

/**
 * Login Form Validation.
 *
 * @param array $post post.
 *
 * @return bool|WP_Error
 */
function fed_validate_login_form( $post ) {
	$fed_error        = new WP_Error();
	$mandatory_fields = fed_login_mandatory_fields();

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( $post[ $key ] == '' ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}

/**
 * Lost Password Validation.
 *
 * @param array $post post.
 *
 * @return false|WP_Error|WP_User
 */
function fed_validate_forgot_password( $post ) {
	$errors = new WP_Error();

	if ( empty( $post['user_login'] ) || $post['user_login'] == '' ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.' ) );
	} elseif ( strpos( $post['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( wp_unslash( $post['user_login'] ) ) );
		if ( empty( $user_data ) ) {
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.' ) );
		}
	} else {
		$login     = trim( $post['user_login'] );
		$user_data = get_user_by( 'login', $login );
	}


	if ( $errors->get_error_code() ) {
		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
	}

	if ( ! $user_data ) {
		$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: Invalid username or email.' ) );

		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
		exit();
	}

	return $user_data;
}

