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

	do_action( 'fed_register_before_validation', $post );

	$redirect_url    = fed_registration_redirect();
	$fed_admin_login = get_option( 'fed_admin_login' );
	$notification    = isset( $fed_admin_login['register']['register_email_notification'] ) ? $fed_admin_login['register']['register_email_notification'] : '';

	$errors = fed_validate_registration_form( $post );

	if ( $errors instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $errors->get_error_messages() ) );
		exit();
	}

	apply_filters( 'fed_register_form_submit', $post );

	$status = wp_insert_user( $post );

	if ( $status instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $status->get_error_messages() ) );
		exit();
	}

	wp_send_new_user_notifications( $status, $notification );

	if ( $fed_admin_login && isset( $fed_admin_login['register']['auto_login'] ) && ( 'yes' === $fed_admin_login['register']['auto_login'] ) ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $status );
		wp_set_auth_cookie( $status );

		$redirect_url = apply_filters( 'fed_registration_redirect_url', fed_registration_redirect(),
			new WP_User( $status ) );
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
	if ( isset( $_REQUEST['tab_id'] ) ) {
		$get_profile_meta_by_menu = fed_fetch_user_profile_columns( sanitize_text_field( wp_unslash( $_REQUEST['tab_id'] ) ) );
	}

	if ( isset( $_REQUEST['fed_registration_form'] ) ) {
		/**
		 * Fetch registration form field and add it in the meta fields
		 */
		$get_profile_meta_by_menu = fed_fetch_user_profile_by_registration();
	}

	if ( count( $get_profile_meta_by_menu ) > 0 ) {
		foreach ( $get_profile_meta_by_menu as $key => $extra_field ) {
			if (
				isset( $_REQUEST[ $extra_field['input_meta'] ] ) && is_array(
					$_REQUEST[ $extra_field['input_meta'] ]
				)
			) {
				$meta[ $extra_field['input_meta'] ] = serialize(
				// phpcs:ignore
					fed_sanitize_text_field( $_REQUEST[ $extra_field['input_meta'] ] )
				);
			} else {
				if ( isset( $extra_field['input_type'] ) && 'wp_editor' === $extra_field['input_type'] ) {
					$meta[ $extra_field['input_meta'] ] = isset( $_REQUEST[ $extra_field['input_meta'] ] ) ? wp_kses_post(
						$_REQUEST[ $extra_field['input_meta'] ]
					) : '';
				} elseif ( isset( $extra_field['input_type'] ) && 'multi_line' === $extra_field['input_type'] ) {
					$meta[ $extra_field['input_meta'] ] = isset( $_REQUEST[ $extra_field['input_meta'] ] ) ? wp_kses(
						$_REQUEST[ $extra_field['input_meta'] ],
						array()
					) : '';
				} else {
					$meta[ $extra_field['input_meta'] ] = isset( $_REQUEST[ $extra_field['input_meta'] ] ) ? fed_sanitize_text_field(
						$_REQUEST[ $extra_field['input_meta'] ]
					) : '';
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
	$post_payload = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	if ( isset( $post_payload['submit'] ) && ! isset( $post_payload['user_login'] ) && 'register' === $post_payload['submit'] ) {
		return sanitize_user( $post_payload['user_email'] . '_' . mt_rand( 1, 999 ), true );
	}

	return $sanitized_user_login;
}
