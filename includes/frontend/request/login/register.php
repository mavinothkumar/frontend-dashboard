<?php
/**
 * Register.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Register Form Submit.
 *
 * @param  array $post  Post.
 */
function fed_register_form_submit( $post ) {
	// Security: Explicitly unset reserved WordPress user fields before anything else
	unset(
		$post['ID'],
		$post['id'],
		$post['user_registered'],
		$post['user_activation_key'],
		$post['user_status'],
		$post['spam'],
		$post['deleted']
	);

	do_action( 'fed_register_before_validation', $post );

	if ( ! get_option( 'users_can_register' ) ) {
		wp_send_json_error( array( 'user' => array( __( 'User registration is currently closed.', 'frontend-dashboard' ) ) ), 403 );
		exit();
	}

	$redirect_url    = fed_registration_redirect();
	$fed_admin_login = get_option( 'fed_admin_login' );
	$notification    = isset( $fed_admin_login['register']['register_email_notification'] ) ? $fed_admin_login['register']['register_email_notification'] : '';

	$errors = fed_validate_registration_form( $post );

	if ( $errors instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
		exit();
	}

	// Determine valid user role
	$allowed_roles = fed_is_role_in_registration();
	$default_role  = get_option( 'default_role', 'subscriber' );
	if ( fed_is_elevated_role( $default_role ) ) {
		$default_role = 'subscriber';
	}

	$role = $default_role;
	if ( $allowed_roles && isset( $post['role'] ) && array_key_exists( $post['role'], $allowed_roles ) && ! fed_is_elevated_role( $post['role'] ) ) {
		$role = sanitize_text_field( $post['role'] );
	}

	// Construct clean userdata strictly for new user insertion
	$userdata = array(
		'user_login' => sanitize_user( $post['user_login'], true ),
		'user_email' => sanitize_email( $post['user_email'] ),
		'user_pass'  => (string) $post['user_pass'],
		'role'       => $role,
	);

	// Include optional standard profile fields if submitted
	$optional_fields = array( 'first_name', 'last_name', 'display_name', 'user_url', 'description', 'nickname' );
	foreach ( $optional_fields as $field ) {
		if ( isset( $post[ $field ] ) && ! empty( $post[ $field ] ) ) {
			$userdata[ $field ] = sanitize_text_field( $post[ $field ] );
		}
	}

	// Ensure ID is never present under any circumstances
	unset( $userdata['ID'], $userdata['id'] );

	$userdata = apply_filters( 'fed_register_form_submit', $userdata );

	// Double-safety unset after filters
	if ( is_array( $userdata ) ) {
		unset( $userdata['ID'], $userdata['id'] );
	}

	$status = wp_insert_user( $userdata );

	if ( $status instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $status->get_error_messages() ) );
		exit();
	}

	wp_send_new_user_notifications( $status, $notification );

	if ( $fed_admin_login && isset( $fed_admin_login['register']['auto_login'] ) && ( 'yes' === $fed_admin_login['register']['auto_login'] ) ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $status );
		wp_set_auth_cookie( $status );

		$redirect_url = apply_filters(
			'fed_registration_redirect_url',
			fed_registration_redirect(),
			new WP_User( $status )
		);
	}

	do_action( 'fed_registration_success', $status );

	wp_send_json_success(
		array(
			'user'    => $status,
			'message' => __( 'Successfully Registered', 'frontend-dashboard' ),
			'url'     => $redirect_url,
		)
	);
}


/**
 * Filter for add extra fields to save.
 */
add_filter( 'insert_user_meta', 'fed_insert_user_meta', 10, 3 );
add_filter( 'pre_user_login', 'fed_skip_user_name_on_registration' );

/**
 * Insert User Meta.
 *
 * @param  array    $meta  Meta.
 * @param  \WP_User $user  User.
 * @param  bool     $update  Update.
 *
 * @return mixed|void
 */
function fed_insert_user_meta( $meta, $user, $update ) {
	$get_profile_meta_by_menu = array();
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['tab_id'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$get_profile_meta_by_menu = fed_fetch_user_profile_columns( sanitize_text_field( wp_unslash( $_REQUEST['tab_id'] ) ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_REQUEST['fed_registration_form'] ) ) {
		/**
		 * Fetch registration form field and add it in the meta fields
		 */
		$get_profile_meta_by_menu = fed_fetch_user_profile_by_registration();
	}

	if ( count( $get_profile_meta_by_menu ) > 0 ) {
		foreach ( $get_profile_meta_by_menu as $key => $extra_field ) {
			if (
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				isset( $_REQUEST[ $extra_field['input_meta'] ] ) && is_array(
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$_REQUEST[ $extra_field['input_meta'] ]
				)
			) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$raw_val                            = wp_unslash( $_REQUEST[ $extra_field['input_meta'] ] );
				$meta[ $extra_field['input_meta'] ] = serialize(
					fed_sanitize_text_field( $raw_val )
				);
			} else {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$raw_val = isset( $_REQUEST[ $extra_field['input_meta'] ] ) ? wp_unslash( $_REQUEST[ $extra_field['input_meta'] ] ) : '';
				if ( isset( $extra_field['input_type'] ) && 'wp_editor' === $extra_field['input_type'] ) {
					$meta[ $extra_field['input_meta'] ] = ! empty( $raw_val ) ? wp_kses_post( $raw_val ) : '';
				} elseif ( isset( $extra_field['input_type'] ) && in_array( $extra_field['input_type'], array( 'multi_line', 'textarea', 'multiline' ), true ) ) {
					$meta[ $extra_field['input_meta'] ] = ! empty( $raw_val ) ? sanitize_textarea_field( $raw_val ) : '';
				} else {
					$meta[ $extra_field['input_meta'] ] = ! empty( $raw_val ) ? fed_sanitize_text_field( $raw_val ) : '';
				}
			}
		}
	}

	return apply_filters( 'fed_user_extra_fields_registration', $meta );
}

/**
 * Skip User Name on Registration.
 *
 * @param  string $sanitized_user_login  Sanitized user name.
 *
 * @return string
 */
function fed_skip_user_name_on_registration( $sanitized_user_login ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$post_payload = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	if ( isset( $post_payload['submit'] ) && ! isset( $post_payload['user_login'] ) && 'register' === $post_payload['submit'] ) {
		return sanitize_user( $post_payload['user_email'] . '_' . wp_rand( 1, 999 ), true );
	}

	return $sanitized_user_login;
}
