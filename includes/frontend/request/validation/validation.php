<?php
/**
 * Validation.
 *
 * @package Forntend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Validate User Profile Form.
 *
 * @param  array $post  Request.
 *
 * @return bool|WP_Error
 */
function fed_validate_user_profile_form( $post ) {
	/**
	 * Both Password Match.
	 */
	$fed_error        = new WP_Error();
	$tab_id           = isset( $post['tab_id'] ) ? $post['tab_id'] : '';
	$mandatory_fields = fed_process_user_profile_required_by_menu( $tab_id );
	if ( isset( $mandatory_fields['user_pass'] ) ) {
		unset( $mandatory_fields['user_pass'] );
		unset( $mandatory_fields['confirmation_password'] );
	}

	if ( isset( $post['user_pass'] ) && ( '' != $post['user_pass'] ) && ( $post['user_pass'] !== $post['confirmation_password'] ) ) {
		$fed_error->add( 'password_not_match', __( 'Password not match', 'frontend-dashboard' ) );
	}

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( ! isset( $post[ $key ] ) || '' === trim( (string) $post[ $key ] ) ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	// Fetch all fields for validation checks (email, url, number, etc.)
	$all_fields = array();
	if ( ! empty( $tab_id ) && function_exists( 'fed_fetch_user_profile_by_menu_slug' ) ) {
		$tab_fields = fed_fetch_user_profile_by_menu_slug( $tab_id );
		if ( is_array( $tab_fields ) ) {
			$all_fields = $tab_fields;
		}
	}

	if ( empty( $all_fields ) && function_exists( 'fed_fetch_rows_by_table' ) ) {
		$table_fields = fed_fetch_rows_by_table( BC_FED_TABLE_USER_PROFILE );
		if ( is_array( $table_fields ) ) {
			$all_fields = $table_fields;
		}
	}

	$current_user_id = function_exists( 'get_current_user_id' ) ? get_current_user_id() : 0;

	if ( is_array( $all_fields ) ) {
		foreach ( $all_fields as $field ) {
			$meta_key   = isset( $field['input_meta'] ) ? $field['input_meta'] : '';
			$input_type = isset( $field['input_type'] ) ? $field['input_type'] : '';
			$label      = ! empty( $field['label_name'] ) ? $field['label_name'] : $meta_key;

			if ( empty( $meta_key ) || ! array_key_exists( $meta_key, $post ) ) {
				continue;
			}

			$val = is_string( $post[ $meta_key ] ) ? trim( $post[ $meta_key ] ) : $post[ $meta_key ];

			// Email format validation
			if ( 'email' === $input_type || 'user_email' === $meta_key ) {
				if ( '' !== $val ) {
					if ( ! is_email( $val ) ) {
						$fed_error->add(
							$meta_key . '_invalid',
							sprintf( __( 'Please enter a valid email address for %s.', 'frontend-dashboard' ), $label )
						);
					} elseif ( 'user_email' === $meta_key && function_exists( 'email_exists' ) ) {
						$user_id_by_email = email_exists( $val );
						if ( $user_id_by_email && (int) $user_id_by_email !== (int) $current_user_id ) {
							$fed_error->add(
								'email_exists',
								__( 'This email address is already in use by another account.', 'frontend-dashboard' )
							);
						}
					}
				}
			}

			// URL format validation
			if ( 'url' === $input_type || 'user_url' === $meta_key ) {
				if ( '' !== $val && is_string( $val ) ) {
					if ( ! filter_var( $val, FILTER_VALIDATE_URL ) && ! ( function_exists( 'wp_http_validate_url' ) && wp_http_validate_url( $val ) ) ) {
						$fed_error->add(
							$meta_key . '_invalid',
							sprintf( __( 'Please enter a valid URL for %s.', 'frontend-dashboard' ), $label )
						);
					}
				}
			}

			// Number validation
			if ( 'number' === $input_type ) {
				if ( '' !== $val && ! is_numeric( $val ) ) {
					$fed_error->add(
						$meta_key . '_invalid',
						sprintf( __( '%s must be a valid number.', 'frontend-dashboard' ), $label )
					);
				}
			}
		}
	}

	// Direct fallback check for user_email if present in POST but not covered
	if ( isset( $post['user_email'] ) && '' !== trim( (string) $post['user_email'] ) ) {
		$email_val = trim( $post['user_email'] );
		if ( ! is_email( $email_val ) ) {
			if ( ! in_array( 'user_email_invalid', $fed_error->get_error_codes(), true ) ) {
				$fed_error->add( 'user_email_invalid', __( 'Please enter a valid email address.', 'frontend-dashboard' ) );
			}
		} elseif ( function_exists( 'email_exists' ) ) {
			$user_id_by_email = email_exists( $email_val );
			if ( $user_id_by_email && (int) $user_id_by_email !== (int) $current_user_id ) {
				if ( ! in_array( 'email_exists', $fed_error->get_error_codes(), true ) ) {
					$fed_error->add( 'email_exists', __( 'This email address is already in use by another account.', 'frontend-dashboard' ) );
				}
			}
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}

/**
 * Validate New Manual Order
 *
 * @param  array $request  Request.
 *
 * @return bool|WP_Error
 */
function fed_order_add_validation( $request ) {
	$fed_error = new WP_Error();

	if ( empty( $request['user_id'] ) || ! isset( $request['user_id'] ) ) {
		wp_send_json_error(
			array(
				'message' => __(
					'You can able to add only the registered user, So please use the search functionality to find the user',
					'frontend-dashboard'
				),
			)
		);
		exit();
	}

	$mandatory_fields = array(
		'email'          => 'Email',
		'first_name'     => 'First Name',
		'last_name'      => 'Last Name',
		'amount'         => 'Amount',
		'user_id'        => 'User ID',
		'currency_type'  => 'Currency Type',
		'payment_source' => 'Payment Source',
	);
	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( '' == $request[ $key ] ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}
