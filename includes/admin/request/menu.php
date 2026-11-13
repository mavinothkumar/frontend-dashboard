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
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ) );
	}

	global $wpdb;
	$menu_slug = sanitize_key( $request['menu_slug'] );

	/**
	 * TODO: changed prefix to get_blog_prefix() for multisite check.
	 */
	$table_name = $wpdb->get_blog_prefix() . BC_FED_TABLE_MENU;

	// Update.
	if ( '' !== $post_id && 0 !== (int) $post_id ) {
		$existing = fed_fetch_table_row_by_id( BC_FED_TABLE_MENU, (int) $post_id );

		if ( is_wp_error( $existing ) || ! is_array( $existing ) ) {
			wp_send_json_error( array( 'message' => __( 'Sorry, no record found to update your new details', 'frontend-dashboard' ) ) );
		}

		$existing_slug = isset( $existing['menu_slug'] ) ? $existing['menu_slug'] : '';
		$slug_changed  = ( $existing_slug !== $menu_slug );

		if ( $slug_changed ) {
			$is_extra     = ( isset( $existing['extra'] ) && 'no' === $existing['extra'] ) ? 'no' : 'yes';
			$locked_slugs = array( 'profile', 'logout', 'post' );
			if ( defined( 'FED_MEMBERSHIPS_VERSION' ) || defined( 'FED_MEMBERSHIP_VERSION' ) ) {
				$locked_slugs[] = 'my-membership';
			}
			if ( defined( 'FED_PAY_PER_POST_VERSION' ) ) {
				$locked_slugs[] = 'my-submissions';
			}
			$is_system_core = ( 'no' === $is_extra || in_array( $existing_slug, $locked_slugs, true ) );
			$is_system_core = (bool) apply_filters( 'fed_is_system_menu', $is_system_core, $existing, $existing_slug );

			if ( $is_system_core ) {
				wp_send_json_error( array( 'message' => __( 'System menu slugs are protected and cannot be modified.', 'frontend-dashboard' ) ) );
			}

			fed_admin_menu_restrictive_words_check( $menu_slug );

			/**
			 * Check for input meta already exist
			 */
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$duplicate = $wpdb->get_row(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$table_name} WHERE menu_slug = %s AND id != %d",
					$menu_slug,
					(int) $post_id
				)
			);

			if ( null !== $duplicate ) {
				wp_send_json_error(
					array(
						'message' => sprintf(
							/* translators: 1: Menu title, 2: Menu order */
							__( 'Sorry, you have previously added %1$s with order %2$s', 'frontend-dashboard' ),
							strtoupper( $duplicate->menu ),
							strtoupper( $duplicate->menu_order )
						),
					)
				);
			}

			$user_profile_table = $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->update(
				$user_profile_table,
				array( 'menu' => $menu_slug ),
				array( 'menu' => $existing_slug )
			);
		} else {
			// Slug is unchanged - protect system slugs and ensure menu_slug is not modified.
			unset( $request['menu_slug'] );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$status = $wpdb->update( $table_name, $request, array( 'id' => (int) $post_id ) );

		if ( false === $status ) {
			wp_send_json_error( array( 'message' => __( 'Sorry, something went wrong in updating values in DB, please try again later or contact support', 'frontend-dashboard' ) ) );
		}

		$menu_title = isset( $request['menu'] ) ? $request['menu'] : ( isset( $request['menu_name'] ) ? $request['menu_name'] : '' );

		wp_send_json_success(
			array(
				/* translators: %s: Menu title */
				'message' => sprintf( __( '%s has been successfully updated', 'frontend-dashboard' ), $menu_title ),
			)
		);
	} else {
		fed_admin_menu_restrictive_words_check( $menu_slug );

		/**
		 * Check for input meta already exist
		 */
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$duplicate = $wpdb->get_row(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT * FROM {$table_name} WHERE menu_slug = %s",
				$menu_slug
			)
		);

		if ( null !== $duplicate ) {
			wp_send_json_error(
				array(
					'message' => sprintf(
						/* translators: 1: Menu title, 2: Menu order */
						__( 'Sorry, you have previously added %1$s with order %2$s', 'frontend-dashboard' ),
						strtoupper( $duplicate->menu ),
						strtoupper( $duplicate->menu_order )
					),
				)
			);
		}

		/**
		 * Now we are free to insert the row
		 */
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$status = $wpdb->insert(
			$table_name,
			$request
		);

		if ( false === $status ) {
			wp_send_json_error(
				array( 'message' => __( 'Sorry, Something went wrong in storing values in DB, please try again later or contact support', 'frontend-dashboard' ) )
			);
		}

		$menu_title = isset( $request['menu'] ) ? $request['menu'] : ( isset( $request['menu_name'] ) ? $request['menu_name'] : '' );

		wp_send_json_success(
			array(
				/* translators: %s: Menu title */
				'message' => sprintf( __( '%s has been Successfully added', 'frontend-dashboard' ), $menu_title ),
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

	if ( in_array( $slug, $restrictive_menu, true ) ) {
		wp_send_json_error(
			array(
				/* translators: %s: Restrictive slug name */
				'message' => sprintf( __( 'Sorry, You cannot use the restrictive slug name "%s"', 'frontend-dashboard' ), esc_html( $slug ) ),
			)
		);
	}
}

add_action( 'wp_ajax_fed_menu_sorting_items', 'fed_menu_sorting_items' );
/**
 * Menu Sorting Items.
 */
function fed_menu_sorting_items() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ) );
	}

	fed_verify_nonce();

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$request           = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
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
			} else {
				wp_send_json_error( array( 'message' => 'There is some issue in your custom menu, please check' ) );
			}
		}
	}
	update_option( 'fed_admin_menu_sort', $menus );
	wp_send_json_success( array( 'message' => 'Successfully Updated' ) );
}
