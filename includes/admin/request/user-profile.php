<?php
/**
 * User Profile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Save Profile.
 *
 * @param  array  $request  Request.
 * @param  string $action  Action.
 * @param  string $post_id  Post ID.
 */
function fed_save_profile_post( $request, $action = '', $post_id = '' ) {
	global $wpdb;
	$input_meta = $request['input_meta'];

	if ( 'profile' === $action ) {
		$table_name = $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
	}
	elseif ( 'post' === $action ) {
		$table_name = $wpdb->prefix . BC_FED_TABLE_POST;
	}
	else {
		wp_send_json_error( array( 'message' => __( 'Hey, you are trying something naughty', 'frontend-dashboard' ) ) );
	}

	if ( ! empty( $post_id ) ) {

		/**
		 * Check for input meta already exist
		 */

		$duplicate = $wpdb->get_row(
			"SELECT * FROM $table_name WHERE input_meta LIKE '{$input_meta}' AND NOT id = $post_id "
		);

		if ( null !== $duplicate ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
					/* Translators: 1%s 2%s : 1. Label Name  2. Input Type*/
						__( 'Sorry, you have previously added %1$s with input type %2$s', 'frontend-dashboard' ),
						esc_attr(
							strtoupper(
								$duplicate->label_name
							)
						),
						strtoupper(
							fed_convert_this_to_that(
								$duplicate->input_type,
								'_', ' '
							)
						)
					),
				)
			);
		}

		/**
		 * No duplicate found, so we can update the record.
		 */
		$status = $wpdb->update( $table_name, $request, array( 'id' => (int) $post_id ) );

		if ( false === $status ) {
			wp_send_json_error(
				array(
					'message' => __( 'Sorry no record found to update your new details', 'frontend-dashboard' ),
				)
			);
		}
		wp_send_json_success( array( 'message' => $request['label_name'] . ' has been successfully updated' ) );
	}
	else {
		/**
		 * Check for input meta already exist
		 */
		$duplicate = $wpdb->get_row( "SELECT * FROM $table_name WHERE input_meta = '{$input_meta}'" );

		if ( null !== $duplicate ) {
			$error_message_2 = 'User Profile';
			if ( 'post' === $action ) {
				$error_message_2 = 'Post Type ' . $duplicate->post_type;
			}
			$error_message = fed_convert_this_to_that( $duplicate->input_type, '_', ' ' );
			wp_send_json_error(
				array(
					'message' => sprintf(
					/* Translators: %1$s : Label Name, %2$s : Error Message 1, %3$s : Error Message 2  */
						__(
							'Sorry, you have previously added %1$s  with input type %2$s on %3$s', 'frontend-dashboard'
						),
						esc_attr(
							strtoupper(
								$duplicate->label_name
							)
						),
						esc_attr( $error_message ),
						esc_attr( $error_message_2 )
					),
				)
			);
		}
		/**
		 * Now we are free to insert the row
		 */
		$status = $wpdb->insert(
			$table_name,
			$request
		);

		if ( false === $status ) {
			wp_send_json_error(
				array(
					'message' => __(
						'Sorry, Something went wrong in storing values in DB, please try again later or contact support',
						'frontend-dashboard'
					),
				)
			);
		}

		wp_send_json_success( array( 'message' => $request['label_name'] . ' has been Successfully added' ) );
	}
}

/**
 * Sorting.
 */
add_action( 'wp_ajax_fed_admin_menu_sorting', 'fed_admin_menu_sorting' );
/**
 * Admin Menu Sorting.
 */
function fed_admin_menu_sorting() {
	global $wpdb;

	$request_post = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$request_get  = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );

	fed_verify_nonce( $request_get );

	$tables = fed_get_tables();

	if ( isset( $request_get['table'] ) && array_key_exists( $request_get['table'], $tables ) ) {
		foreach ( $request_post['sort'] as $sort => $id ) {
			$wpdb->update(
				$wpdb->prefix . fed_sanitize_text_field( $request_get['table'] ),
				array( $tables[ $request_get['table'] ]['order'] => $sort + 1 ),
				array( 'id' => (int) $id )
			);
		}

		wp_send_json_success( array( 'message' => 'Successfully sorted' ) );
	}

	wp_send_json_error( array( 'message' => 'Something went wrong' ) );
}
