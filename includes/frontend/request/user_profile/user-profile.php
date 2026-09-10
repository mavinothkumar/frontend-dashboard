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
	$post_payload    = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	$message = 'Something Went Wrong';

	if (
		isset( $_REQUEST, $post_payload['tab_id'] ) &&
		isset( $_REQUEST['menu_type'] ) &&
		( 'user' === wp_slash( $_REQUEST['menu_type'] ) )
	) {
		fed_verify_nonce();

		$validation = fed_validate_user_profile_form( $post_payload );

		if ( $validation instanceof WP_Error ) {
			$message = array(
				'type'    => 'danger',
				'message' => implode( '<br>', $validation->get_error_messages() ),
			);
		} else {
			$user_data = fed_process_update_user_profile( $post_payload );

			if ( is_wp_error( $user_data ) ) {
				$message = array(
					'type'    => 'danger',
					'message' => implode( '<br>', $user_data->get_error_messages() ),
				);
			} else {
				$saved = wp_update_user( $user_data );
				if ( is_wp_error( $saved ) ) {
					$message = array(
						'type'    => 'danger',
						'message' => implode( '<br>', $saved->get_error_messages() ),
					);
				} else {
					$message = array(
						'type'    => 'success',
						'message' => __( 'Successfully Updated', 'frontend-dashboard' ),
					);
				}
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

	// Process and save custom extra user profile fields into WordPress user meta
	global $wpdb;
	$all_fields = array();
	if ( function_exists( 'fed_fetch_rows_by_table' ) ) {
		$all_fields = fed_fetch_rows_by_table( BC_FED_TABLE_USER_PROFILE );
	}
	if ( empty( $all_fields ) && ! empty( $wpdb ) ) {
		$tbl = $wpdb->prefix . ( defined( 'BC_FED_TABLE_USER_PROFILE' ) ? BC_FED_TABLE_USER_PROFILE : 'fed_user_profile' );
		$all_fields = $wpdb->get_results( "SELECT * FROM $tbl", ARRAY_A );
	}
	if ( is_array( $all_fields ) ) {
		$core_keys = array(
			'user_login',
			'user_pass',
			'confirmation_password',
			'user_email',
			'user_nicename',
			'display_name',
			'first_name',
			'last_name',
			'nickname',
			'description',
			'show_admin_bar_front',
			'user_url',
		);

		$submitted_tab = isset( $post['tab_id'] ) ? $post['tab_id'] : ( isset( $post['menu_slug'] ) ? $post['menu_slug'] : '' );

		foreach ( $all_fields as $field ) {
			$meta_key = isset( $field['input_meta'] ) ? $field['input_meta'] : '';
			if ( empty( $meta_key ) || in_array( $meta_key, $core_keys, true ) ) {
				continue;
			}

			if ( array_key_exists( $meta_key, $post ) ) {
				$raw_val = $post[ $meta_key ];
				if ( is_array( $raw_val ) ) {
					$sanitized_val = maybe_serialize( $raw_val );
				} else {
					$input_type = isset( $field['input_type'] ) ? $field['input_type'] : '';
					if ( in_array( $input_type, array( 'textarea', 'multi_line' ), true ) ) {
						$sanitized_val = wp_kses_post( wp_unslash( $raw_val ) );
					} else {
						$sanitized_val = sanitize_text_field( wp_unslash( $raw_val ) );
					}
				}
				update_user_meta( $current_user->ID, $meta_key, $sanitized_val );
			} elseif ( ! empty( $submitted_tab ) && isset( $field['menu'] ) && $field['menu'] === $submitted_tab ) {
				$input_type = isset( $field['input_type'] ) ? $field['input_type'] : '';
				if ( in_array( $input_type, array( 'checkbox', 'select', 'radio' ), true ) ) {
					update_user_meta( $current_user->ID, $meta_key, '' );
				}
			}
		}
	}

	// Escape data pulled from DB.
	return add_magic_quotes( $new_value );
}

