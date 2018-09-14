<?php

/**
 * @param $post
 */
function fed_register_form_submit( $post ) {
	do_action( 'fed_register_before_validation', $post );
	$redirect_url = fed_registration_redirect();

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

	wp_send_new_user_notifications( $status, 'admin' );

	wp_send_json_success( array(
		'user'    => $status,
		'message' => 'Successfully Registered',
		'url'     => $redirect_url
	) );

}


/**
 * Filter for add extra fields to save.
 */
add_filter( 'insert_user_meta', 'fed_insert_user_meta', 10, 3 );

function fed_insert_user_meta( $meta, $user, $update ) {
	//$extra_fields = fed_fetch_user_profile_extra_fields_key_value();
	$get_profile_meta_by_menu = array();
	if ( isset( $_REQUEST['tab_id'] ) ) {
		$get_profile_meta_by_menu = fed_fetch_user_profile_columns( $_REQUEST['tab_id'] );
	}
	if ( isset( $_REQUEST['fed_registration_form'] ) ) {
		/**
		 * Fetch registration form field and add it in the meta fields
		 */
		$register                 = fed_fetch_user_profile_by_registration();
		$get_profile_meta_by_menu = array_column( $register, 'input_meta' );
	}
	if ( count( $get_profile_meta_by_menu ) > 0 ) {
		foreach ( $get_profile_meta_by_menu as $extra_field ) {
			$meta[ $extra_field ] = isset( $_REQUEST[ $extra_field ] ) ? esc_attr( $_REQUEST[ $extra_field ] ) : '';
		}

	}

	return apply_filters( 'fed_user_extra_fields_registration', $meta );


}