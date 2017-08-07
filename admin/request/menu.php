<?php

/**
 * Admin Menu Save
 *
 * @param array $request Request
 * @param string $post_id Post ID
 */
function fed_admin_menu_save( $request, $post_id = '' ) {
	global $wpdb;
	$menu_slug = $request['menu_slug'];

	$table_name = $wpdb->prefix . BC_FED_MENU_DB;

	fed_admin_menu_restrictive_words_check( $request['menu_slug'] );

	if ( '' != $post_id ) {
		/**
		 * Check for input meta already exist
		 */
		$duplicate = $wpdb->get_row( "SELECT * FROM $table_name WHERE menu_slug LIKE '{$menu_slug}' AND NOT id = $post_id " );

		if ( null !== $duplicate ) {
			wp_send_json_error( array( 'message' => 'Sorry, you have previously added ' . strtoupper( $duplicate->menu ) . ' with order ' . strtoupper( $duplicate->menu_order ) ) );
			exit();
		}

		/**
		 * No duplicate found, so we can update the record.
		 */

		$status = $wpdb->update( $table_name, $request, array( 'id' => (int) $post_id ) );

		if ( $status === false ) {
			wp_send_json_error( array( 'message' => 'Sorry no record found to update your new details' ) );
			exit();
		}

		wp_send_json_success( array(
			'message' => $request['menu'] . ' has been successfully updated'
		) );
		exit();
	} else {
		/**
		 * Check for input meta already exist
		 */

		$duplicate = $wpdb->get_row( "SELECT * FROM $table_name WHERE menu_slug LIKE '{$menu_slug}'" );

		if ( null !== $duplicate ) {
			wp_send_json_error( array( 'message' => 'Sorry, you have previously added ' . strtoupper( $duplicate->menu ) . ' with order ' . strtoupper( $duplicate->menu_order ) ) );
			exit();
		}


		/**
		 * Now we are free to insert the row
		 */
		$status = $wpdb->insert(
			$table_name,
			$request
		);

		if ( false === $status ) {
			wp_send_json_error( array( 'message' => 'Sorry, Something went wrong in storing values in DB, please try again later or contact support' ) );
			exit();
		}


		wp_send_json_success( array(
			'message' => $request['menu'] . ' has been Successfully added',
			'reload'  => admin_url() . 'admin.php?page=fed_dashboard_menu',
		) );
		exit();

	}
}

/**
 * Admin Menu Restrictive Words
 *
 * @param $slug
 */
function fed_admin_menu_restrictive_words_check( $slug ) {
	$restrictive_menu = array(
		'payment',
		'post'
	);

	apply_filters( 'fed_restrictive_menu_names', $restrictive_menu );

	if ( in_array( $slug, $restrictive_menu, false ) ) {
		wp_send_json_error( array( 'message' => 'Sorry, You cannot use the restrictive slug name "' . $slug . '"' ) );
		exit();
	}
}