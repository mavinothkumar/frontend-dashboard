<?php

namespace FED\Models\User;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UserProfileModel
 *
 * Handles database and data operations related to WordPress user profiles.
 */
class UserProfileModel {

	/**
	 * Process and update user profile data.
	 *
	 * @param array $post Form input post data.
	 * @return array|WP_Error
	 */
	public function process_update_user_profile( array $post ) {
		$current_user = wp_get_current_user();

		if ( ! $current_user || ! $current_user->ID ) {
			return new WP_Error( 'not_logged_in', __( 'You must be logged in to update your profile.', 'frontend-dashboard' ) );
		}

		$user_obj = get_userdata( $current_user->ID );
		if ( ! $user_obj ) {
			return new WP_Error( 'invalid_user_id', __( 'Invalid user ID.', 'frontend-dashboard' ) );
		}

		$core_keys = [
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
		];

		$raw_options  = function_exists( 'fed_fetch_user_profile_not_extra_fields_key_value' )
			? fed_fetch_user_profile_not_extra_fields_key_value()
			: [];
		$site_options = is_array( $raw_options ) ? array_intersect( array_keys( $raw_options ), $core_keys ) : [];

		$new_value               = [];
		$new_value['ID']         = $current_user->ID;
		$new_value['user_login'] = $current_user->user_login;

		foreach ( $site_options as $site_option ) {
			if ( 'user_pass' === $site_option || 'confirmation_password' === $site_option ) {
				if ( ! empty( $post['user_pass'] ) && isset( $post['confirmation_password'] ) && $post['user_pass'] === $post['confirmation_password'] ) {
					$new_value[ $site_option ] = $post['user_pass'];
				} else {
					$new_value[ $site_option ] = '';
				}
			} else {
				if ( array_key_exists( $site_option, $post ) ) {
					if ( is_array( $post[ $site_option ] ) ) {
						$new_value[ $site_option ] = serialize( $post[ $site_option ] );
					} else {
						$new_value[ $site_option ] = function_exists( 'fed_sanitize_text_field' )
							? fed_sanitize_text_field( $post[ $site_option ] )
							: sanitize_text_field( $post[ $site_option ] );
					}
				} else {
					$new_value[ $site_option ] = $user_obj->has_prop( $site_option ) ? $user_obj->get( $site_option ) : '';
				}
			}
		}

		// Never allow role or capability elevation via profile update payload
		unset(
			$new_value['role'],
			$new_value['roles'],
			$new_value['caps'],
			$new_value['wp_capabilities'],
			$new_value['user_activation_key'],
			$new_value['user_status'],
			$new_value['user_level']
		);

		// Process and save custom extra user profile fields into WordPress user meta
		global $wpdb;
		$all_fields = [];
		if ( function_exists( 'fed_fetch_rows_by_table' ) ) {
			$all_fields = fed_fetch_rows_by_table( BC_FED_TABLE_USER_PROFILE );
		}
		if ( empty( $all_fields ) && ! empty( $wpdb ) ) {
			$tbl = $wpdb->prefix . ( defined( 'BC_FED_TABLE_USER_PROFILE' ) ? BC_FED_TABLE_USER_PROFILE : 'fed_user_profile' );
			$all_fields = $wpdb->get_results( "SELECT * FROM $tbl", ARRAY_A );
		}
		if ( is_array( $all_fields ) ) {
			$disallowed_meta = [
				'role',
				'roles',
				'caps',
				'capabilities',
				'wp_capabilities',
				'user_level',
				'session_tokens',
				'account_status',
				'primary_blog',
				'source_domain',
			];

			$submitted_tab = isset( $post['tab_id'] ) ? $post['tab_id'] : ( isset( $post['menu_slug'] ) ? $post['menu_slug'] : '' );

			foreach ( $all_fields as $field ) {
				$meta_key = isset( $field['input_meta'] ) ? $field['input_meta'] : '';
				if ( empty( $meta_key ) || in_array( $meta_key, $core_keys, true ) ) {
					continue;
				}

				// Reject dangerous or capability-related meta keys
				$meta_lower = strtolower( $meta_key );
				if (
					in_array( $meta_lower, $disallowed_meta, true ) ||
					strpos( $meta_lower, 'capabilities' ) !== false ||
					strpos( $meta_lower, 'user_level' ) !== false
				) {
					continue;
				}

				if ( array_key_exists( $meta_key, $post ) ) {
					$raw_val = $post[ $meta_key ];
					if ( is_array( $raw_val ) ) {
						$sanitized_val = maybe_serialize( $raw_val );
					} else {
						$input_type = isset( $field['input_type'] ) ? $field['input_type'] : '';
						if ( in_array( $input_type, [ 'textarea', 'multi_line', 'multiline' ], true ) ) {
							$sanitized_val = sanitize_textarea_field( wp_unslash( $raw_val ) );
						} else {
							$sanitized_val = sanitize_text_field( wp_unslash( $raw_val ) );
						}
					}
					update_user_meta( $current_user->ID, $meta_key, $sanitized_val );
				} elseif ( ! empty( $submitted_tab ) && isset( $field['menu'] ) && $field['menu'] === $submitted_tab ) {
					// Checkbox / multi-choice field submitted as unchecked
					$input_type = isset( $field['input_type'] ) ? $field['input_type'] : '';
					if ( in_array( $input_type, [ 'checkbox', 'select', 'radio' ], true ) ) {
						update_user_meta( $current_user->ID, $meta_key, '' );
					}
				}
			}
		}

		return add_magic_quotes( $new_value );
	}

	/**
	 * Save updated profile data to WordPress.
	 *
	 * @param array $user_data Prepared user array.
	 * @return int|WP_Error
	 */
	public function save( array $user_data ) {
		return wp_update_user( $user_data );
	}
}
