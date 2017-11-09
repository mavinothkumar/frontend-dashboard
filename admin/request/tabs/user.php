<?php

function fed_admin_user_options_request() {
	$message = '';
	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	//fed_admin_settings_user = get_option( 'fed_admin_settings_user' );

	/**
	 * User Upload
	 */
	if ( isset( $request['fed_admin_unique_user'] ) && 'fed_admin_user_upload' == $request['fed_admin_unique_user'] ) {
		fed_admin_tab_user_upload( $request );
	}
	/**
	 * Add User Role
	 */
	if ( isset( $request['fed_admin_unique_user'] ) && 'fed_admin_setting_role' == $request['fed_admin_unique_user'] ) {
		fed_admin_tab_post_role( $request );
	}
	/**
	 * Delete User Role
	 */
	if ( isset( $request['fed_admin_unique_user'] ) && 'fed_admin_setting_role_delete' == $request['fed_admin_unique_user'] ) {
		fed_admin_tab_post_role_delete( $request );
	}


//	apply_filters( 'fed_admin_settings_user', $fed_admin_settings_user );
//
//	update_option( 'fed_admin_settings_user', $fed_admin_settings_user );

	wp_send_json_success( array(
		'message' => __( $message . ' Updated Successfully ' )
	) );
	exit();
}

function fed_admin_tab_post_role( $request ) {
	global $wpdb;
	$user_roles = $wpdb->prefix . 'user_roles';
	$roles      = get_option( $user_roles );
	$role_name  = esc_attr( $request['user']['role']['role_name'] );
	$role_slug  = sanitize_title_with_dashes( $request['user']['role']['role_slug'] );

	/**
	 * Check for empty
	 */
	if ( empty( $role_name ) || empty( $role_slug ) ) {
		wp_send_json_error( array(
			'message' => __( 'Please fill all the fields' ),
		) );
		exit();
	}

	/**
	 * New user role should not be same as default
	 */
	$default_user_role = fed_default_user_roles();
	if ( array_key_exists( $role_slug, $default_user_role ) ) {
		wp_send_json_error( array(
			'message' => __( 'Sorry! You cannot add the default user roles ' . $role_name ),
		) );
		exit();
	}
	/**
	 * New user role should not be same as already exist
	 */
	if ( array_key_exists( $role_slug, fed_get_extra_user_roles() ) ) {
		wp_send_json_error( array(
			'message' => __( 'Sorry! You have already added the user role ' . $role_name ),
		) );
		exit();
	}
	$roles[ $role_slug ] = array(
		'name'         => $role_name,
		'capabilities' => array(
			'read'         => true,
			'level_0'      => true,
			'upload_files' => true,
		),
	);
	update_option( $user_roles, $roles );

	wp_send_json_success( array(
		'message' => __( $role_name . ' User Role Added Successfully ' ),
		'reload'  => admin_url() . 'admin.php?page=fed_settings_menu#user'
	) );
}

function fed_admin_tab_post_role_delete( $request ) {
	global $wpdb;
	$user_roles = $wpdb->prefix . 'user_roles';
	$roles      = get_option( $user_roles );
	$role_name  = esc_attr( $request['user']['role']['role_name'] );
	$role_slug  = sanitize_title_with_dashes( $request['user']['role']['role_slug'] );

	/**
	 * Trying to delete the default user role
	 */
	$default_user_role = fed_default_user_roles();
	if ( array_key_exists( $role_slug, $default_user_role ) ) {
		wp_send_json_error( array(
			'message' => __( 'Sorry! You cannot delete the default user roles ' . $role_name ),
		) );
		exit();
	}
	/**
	 * Trying to delete the unavailable user role
	 */
	if ( ! array_key_exists( $role_slug, fed_get_extra_user_roles() ) ) {
		wp_send_json_error( array(
			'message' => __( 'Sorry! The user role "' . $role_name . '" is not available.' ),
		) );
		exit();
	}

	unset( $roles[ $role_slug ] );

	update_option( $user_roles, $roles );

	wp_send_json_success( array(
		'message' => __( 'User Role "' . $role_name . '" Deleted Successfully ' ),
		'reload'  => admin_url() . 'admin.php?page=fed_settings_menu#user'
	) );
	exit();

}

function fed_admin_tab_user_upload( $request ) {
	$user_options         = get_option( 'fed_admin_settings_user' );
	$user_options['user'] = array(
		'upload_permission' => isset( $request['user']['upload_permission'] ) ? $request['user']['upload_permission'] : array(),
	);

	/**
	 * Unset User Roles
	 */
	$all_users = fed_get_user_roles();
	foreach ( $all_users as $keys => $user_roles ) {
		$contributor = get_role( $keys );
		if ( array_key_exists( $keys, $user_options['user']['upload_permission'] ) ) {
			$contributor->add_cap( 'upload_files' );
		} else {
			if ( $keys === 'administrator' ) {
				$contributor->add_cap( 'upload_files' );
			} else {
				$contributor->remove_cap( 'upload_files' );
			}
		}
	}

	update_option( 'fed_admin_settings_user', $user_options );
	wp_send_json_success( array(
		'message' => __( 'User Upload Permission Updated Successfully ' )
	) );
}