<?php
/**
 * Save the User Profile data
 */


add_action( 'wp_ajax_fed_user_profile_save', 'fed_user_profile_save_fn' );
add_action( 'wp_ajax_nopriv_fed_user_profile_save', 'fed_block_the_action' );

function fed_user_profile_save_fn() {

	$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	if ( ! wp_verify_nonce( $post['fed_user_profile_save'], 'fed_user_profile_save' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		exit();
	}


	/**
	 * TODO :
	 * 1. check for mandatory fields - Done
	 * 2. check for fed_no_update_fields
	 * 3. collect the respect tab fields
	 * 4. save the appropriate fields
	 */

	$validation = fed_validate_user_profile_form( $post );

	if ( $validation instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $validation->get_error_messages() ) );
		exit();
	}

	$user_data = fed_process_update_user_profile( $post );

	$status = wp_update_user( $user_data );

	if ( $status instanceof WP_Error ) {
		wp_send_json_error( array( 'user' => $status->get_error_messages() ) );
		exit();
	}

	wp_send_json_success( array(
		'user'    => $status,
		'message' => 'Successfully Updated'
	) );

}

function fed_block_the_action() {
	wp_die( 'Inappropriate Action' );
}

function fed_process_update_user_profile( $post ) {
	$current_user = wp_get_current_user();

	$user_obj = get_userdata( $current_user->ID );

	$site_options = array_keys( fed_fetch_user_profile_not_extra_fields_key_value() );

	if ( ! $user_obj ) {
		return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
	}

	$new_value       = array();
	$new_value['ID'] = $current_user->ID;
	$new_value['user_login'] = $current_user->user_login;


	foreach ( $site_options as $site_option ) {
		if ( $site_option == 'user_pass' || $site_option == 'confirmation_password' ) {
			if ( isset( $post['user_pass'] ) && ! empty( $post['user_pass'] ) && $post['user_pass'] === $post['confirmation_password'] ) {
				$new_value[ $site_option ] = $post['user_pass'];
			} else {
				$new_value[ $site_option ] = '';
			}
		}
		else {
			if(in_array( $site_option, array_keys( $post ) )){
				$new_value[ $site_option ] = esc_attr( $post[ $site_option ] );
			} else {
				$new_value[ $site_option ] = $user_obj->has_prop( $site_option ) ? $user_obj->get( $site_option ) : '';
			}
		}
	}

	// Escape data pulled from DB.
	$user = add_magic_quotes( $new_value );

	return $user;
}

