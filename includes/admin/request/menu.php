<?php
/**
 * Menu.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Menu Save
 *
 * @param  array  $request  Request.
 * @param  string $post_id  Post ID.
 */
function fed_admin_menu_save( $request, $post_id = '' ) {
	global $wpdb;
	$menu_slug = $request['menu_slug'];

	/**
	 * TODO: changed prefix to get_blog_prefix() for multisite check.
	 */
	$table_name = $wpdb->get_blog_prefix() . BC_FED_TABLE_MENU;

	fed_admin_menu_restrictive_words_check( $request['menu_slug'] );

	// Update.
	if ( '' != $post_id ) {
		/**
		 * Check for input meta already exist
		 */
		$duplicate = $wpdb->get_row(
			"SELECT * FROM $table_name WHERE menu_slug LIKE '{$menu_slug}' AND NOT id = $post_id "
		);

		if ( null !== $duplicate ) {
			wp_send_json_error(
				array(
					'message' => 'Sorry, you have previously added ' . strtoupper(
							$duplicate->menu
						) . ' with order ' . strtoupper( $duplicate->menu_order ),
				)
			);
			exit();
		}

		/**
		 * No duplicate found, so we can update the record.
		 */
		unset( $request['menu_slug'] );
		$status = $wpdb->update( $table_name, $request, array( 'id' => (int) $post_id ) );

		if ( false === $status ) {
			wp_send_json_error( array( 'message' => 'Sorry no record found to update your new details' ) );
		}

		wp_send_json_success(
			array(
				'message' => $request['menu'] . ' has been successfully updated',
			)
		);
	}
	else {
		/**
		 * Check for input meta already exist
		 */

		$duplicate = $wpdb->get_row( "SELECT * FROM $table_name WHERE menu_slug LIKE '{$menu_slug}'" );

		if ( null !== $duplicate ) {
			wp_send_json_error(
				array(
					'message' => 'Sorry, you have previously added ' . strtoupper(
							$duplicate->menu
						) . ' with order ' . strtoupper( $duplicate->menu_order ),
				)
			);
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
			wp_send_json_error(
				array( 'message' => 'Sorry, Something went wrong in storing values in DB, please try again later or contact support' )
			);
		}

		wp_send_json_success(
			array(
				'message' => $request['menu'] . ' has been Successfully added',
				'reload'  => admin_url() . 'admin.php?page=fed_dashboard_menu',
			)
		);
	}
}

/**
 * Admin Menu Restrictive Words.
 *
 * @param  string $slug  Slug.
 */
function fed_admin_menu_restrictive_words_check( $slug ) {
	$restrictive_menu = array(
		'payment',
		'chat',
		'post',
	);

	$restrictive_menu = apply_filters( 'fed_restrictive_menu_names', $restrictive_menu );

	if ( in_array( $slug, $restrictive_menu, false ) ) {
		wp_send_json_error( array( 'message' => 'Sorry, You cannot use the restrictive slug name "' . $slug . '"' ) );
	}
}

add_action( 'wp_ajax_fed_menu_sorting_items', 'fed_menu_sorting_items' );
/**
 * Menu Sorting Items.
 */
function fed_menu_sorting_items() {

	$request           = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$default_menu_type = fed_get_default_menu_type();
	$menus             = array();

	if ( isset( $request['data'] ) ) {
		foreach ( $request['data'] as $data ) {
			$menu   = fed_menu_split( $data['id'] );
			$parent = isset( $data['parentId'] ) ? fed_menu_split( $data['parentId'] ) : 0;

			if (
				isset( $menu[0], $menu[1] ) &&
				in_array( $menu[0], $default_menu_type ) &&
				! empty( $menu[0] ) &&
				! empty( $menu[1] ) &&
				( 0 === $parent || ( isset( $parent[0], $parent[1] ) && ! empty( $parent[0] ) && ! empty( $parent[1] ) ) )
			) {
				$menus[ $data['id'] ] = array(
					'menu_id'     => $menu[1],
					'menu_type'   => $menu[0],
					'parent_id'   => isset( $parent[1] ) ? $parent[1] : null,
					'parent_type' => isset( $parent[0] ) ? $parent[0] : null,
					'order'       => $data['order'],
				);
			}
			else {
				wp_send_json_error( array( 'message' => 'There is some issue in your custom menu, please check' ) );
			}
		}
	}
	update_option( 'fed_admin_menu_sort', $menus );
	wp_send_json_success( array( 'message' => 'Successfully Updated' ) );
}
