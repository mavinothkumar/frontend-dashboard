<?php

function fed_register_form_submit( $post ) {

	fed_validate_captcha( $post, 'register' );

	$redirect_url = home_url();

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
	$extra_fields = fed_fetch_user_profile_extra_fields_key_value();

	if ( count( $extra_fields ) > 0 ) {
		$methods = $extra_fields;

		foreach ( $methods as $index => $extra_field ) {

			if ( $update == false ) {
				$meta[ $index ] = isset( $_REQUEST[ $index ] ) ? esc_attr( $_REQUEST[ $index ] ) : '';
			}

			if ( $update == true ) {
//				var_dump(array('index' => $index, 'request' => array_keys( $_REQUEST )));
				if ( in_array( $index, array_keys( $_REQUEST ) ) ) {
					$meta[ $index ] = esc_attr( $_REQUEST[ $index ] );
//					var_dump('set : '. $index .' Request : ' . $_REQUEST[ $index ]);
				} else {
					$meta[ $index ] = $user->has_prop( $index ) ? $user->get( $index ) : '';
//					var_dump('not set : '. $index .' Request : ' . $_REQUEST[ $index ]);
				}
			}

		}

	}

//	var_dump( $meta );
//	exit();


	return apply_filters( 'fed_user_extra_fields_registration', $meta );
}