<?php
function fed_reset_form_submit( $request ) {
	if ( ! isset( $request['key'], $request['login'], $request['user_password'], $request['confirmation_password'] ) ) {
		wp_send_json_success( array(
			'message' => 'Invalid details',
			'url'     => fed_get_login_url()
		) );
	}

	if ( empty( $request['user_password'] ) || empty( $request['confirmation_password'] ) ) {
		wp_send_json_error( array( 'user' => __( 'Please enter the Password', 'frontend-dashboard' ) ) );
	}

	if ( $request['user_password'] !== $request['confirmation_password'] ) {
		wp_send_json_error( array( 'user' => __( 'Password not matched', 'frontend-dashboard' ) ) );
	}
	$rp_key   = wp_unslash( $request['key'] );
	$rp_login = wp_unslash( $request['login'] );

	$user = check_password_reset_key( $rp_key, $rp_login );
	if ( $user instanceof WP_User ) {
		reset_password( $user, $request['user_password'] );
		wp_send_json_success( array(
			'message' => 'Successfully Password Reset',
			'url'     => fed_get_login_url()
		) );
	} else {
		if ( $user instanceof WP_Error ) {
			wp_send_json_error( array( 'user' => __( 'Invalid Key, Please try resetting the password again', 'frontend-dashboard' ) ) );
		} else {
			wp_send_json_error( array( 'user' => __( 'Something went wrong, Please try again later', 'frontend-dashboard' ) ) );
		}
	}
}