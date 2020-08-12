<?php
/**
 * User Profile
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Save the User Profile data
 */

//add_action( 'template_redirect', 'fed_store_user_profile_save' );
add_action( 'admin_post_fed_save_user_profile', 'fed_store_user_profile_save' );
add_action( 'admin_post_nopriv_fed_save_user_profile', 'fed_block_the_action' );

/**
 * Store User Profile.
 */
function fed_store_user_profile_save() {
	$post_payload    = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$message = 'Something Went Wrong';

	if (
		isset( $_REQUEST, $post_payload['tab_id'] ) &&
		isset( $_REQUEST['menu_type'] ) &&
		( 'user' === wp_slash( $_REQUEST['menu_type'] ) )
	) {
		fed_verify_nonce();

		$validation = fed_validate_user_profile_form( $post_payload );

		if ( $validation instanceof WP_Error ) {
			$message = $validation->get_error_messages();
		} else {
			$user_data = fed_process_update_user_profile( $post_payload );

			if ( wp_update_user( $user_data ) ) {
				$message = 'Successfully Updated';
			}
		}
		fed_set_alert( 'fed_profile_save_message', $message );
	}

	wp_safe_redirect( add_query_arg( array( 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ),
		$post_payload['_wp_http_referer'] ) );

}

/**
 * Block The Action.
 */
function fed_block_the_action() {
	wp_die( 'Inappropriate Action' );
}

/**
 * Process Update User Profile.
 *
 * @param  array $post  Post.
 *
 * @return array|\WP_Error
 */
function fed_process_update_user_profile( $post ) {
	$current_user = wp_get_current_user();

	$user_obj = get_userdata( $current_user->ID );

	$site_options = array_keys( fed_fetch_user_profile_not_extra_fields_key_value() );

	if ( ! $user_obj ) {
		return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.' ) );
	}

	$new_value               = array();
	$new_value['ID']         = $current_user->ID;
	$new_value['user_login'] = $current_user->user_login;

	foreach ( $site_options as $site_option ) {
		if ( ( 'user_pass' == $site_option ) || ( 'confirmation_password' == $site_option ) ) {
			if ( isset( $post['user_pass'] ) && ! empty( $post['user_pass'] ) && $post['user_pass'] === $post['confirmation_password'] ) {
				$new_value[ $site_option ] = $post['user_pass'];
			} else {
				$new_value[ $site_option ] = '';
			}
		} else {
			if ( array_key_exists( $site_option, $post ) ) {
				$new_value[ $site_option ] = is_array( $post[ $site_option ] ) ? serialize(
					$post[ $site_option ]
				) : fed_sanitize_text_field( $post[ $site_option ] );
			} else {
				$new_value[ $site_option ] = $user_obj->has_prop( $site_option ) ? $user_obj->get( $site_option ) : '';
			}
		}
	}

	// Escape data pulled from DB.
	return add_magic_quotes( $new_value );
}

