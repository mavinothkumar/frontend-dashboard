<?php
/**
 * Validation.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Registration Form Validation
 *
 * @param  array $post  post.
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

	// Security: Strictly disallow ID to prevent mass-assignment account takeover
	if ( isset( $post['ID'] ) || isset( $post['id'] ) ) {
		$fed_error->add( 'invalid_request', __( 'Invalid Registration Request', 'frontend-dashboard' ) );
		return $fed_error;
	}

	if ( ! isset( $post['user_pass'] ) || ! isset( $post['confirmation_password'] ) || '' === $post['user_pass'] || $post['user_pass'] !== $post['confirmation_password'] ) {
		$fed_error->add( 'password_not_match', __( 'Password not match', 'frontend-dashboard' ) );
	}

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( ! isset( $post[ $key ] ) || '' === $post[ $key ] ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( isset( $post['role'] ) && ! empty( $post['role'] ) ) {
		if ( 'administrator' === strtolower( (string) $post['role'] ) ) {
			$fed_error->add( 'invalid_role', __( 'Administrator role cannot be registered.', 'frontend-dashboard' ) );
		} elseif ( $role && ! array_key_exists( $post['role'], $role ) ) {
			$fed_error->add( 'invalid_role', __( 'Invalid Role', 'frontend-dashboard' ) );
		} elseif ( ! $role ) {
			$fed_error->add( 'invalid_role', __( 'You are trying to hack the user role', 'frontend-dashboard' ) );
		}
	}

	if ( isset( $post['user_login'] ) ) {
		if ( fed_validate_username( $post['user_login'] ) ) {
			$fed_error->add(
				'invalid_username', __( 'This Username is Illegal to use in this website', 'frontend-dashboard' )
			);
		} elseif ( username_exists( $post['user_login'] ) ) {
			$fed_error->add(
				'username_exists', __( 'This username is already registered. Please choose another one.', 'frontend-dashboard' )
			);
		}
	}

	if ( isset( $post['user_email'] ) ) {
		if ( ! is_email( $post['user_email'] ) ) {
			$fed_error->add( 'invalid_email', __( 'The email address isn’t correct.', 'frontend-dashboard' ) );
		} elseif ( email_exists( $post['user_email'] ) ) {
			$fed_error->add( 'email_exists', __( 'This email is already registered, please choose another one.', 'frontend-dashboard' ) );
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}

/**
 * Login Form Validation.
 *
 * @param  array $post  post.
 *
 * @return bool|WP_Error
 */
function fed_validate_login_form( $post ) {
	$fed_error        = new WP_Error();
	$mandatory_fields = fed_login_mandatory_fields();

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( '' == $post[ $key ] ) {
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
 * @param  array $post  post.
 *
 * @return false|WP_Error|WP_User
 */
function fed_validate_forgot_password( $post ) {
	$errors = new WP_Error();

	if ( empty( $post['user_login'] ) || '' == $post['user_login'] ) {
		$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.', 'frontend-dashboard' ) );
	} elseif ( strpos( $post['user_login'], '@' ) ) {
		$user_data = get_user_by( 'email', trim( wp_unslash( $post['user_login'] ) ) );
		if ( empty( $user_data ) ) {
			$errors->add(
				'invalid_email',
				__( '<strong>ERROR</strong>: There is no user registered with that email address.', 'frontend-dashboard' )
			);
		}
	} else {
		$login     = trim( $post['user_login'] );
		$user_data = get_user_by( 'login', $login );
	}

	if ( $errors->get_error_code() ) {
		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
	}

	if ( ! $user_data ) {
		$errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: Invalid username or email.', 'frontend-dashboard' ) );

		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
		exit();
	}

	return $user_data;
}

