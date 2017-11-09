<?php
function fed_admin_setting_post_options_request() {
	$message                 = '';
	$request                 = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$fed_admin_settings_post = get_option( 'fed_admin_settings_post' );

	/**
	 * Settings
	 */
	if ( isset( $request['fed_admin_unique_post'] ) && 'fed_admin_settings_post' == $request['fed_admin_unique_post'] ) {
		$fed_admin_settings_post['settings'] = fed_process_admin_settings_post_settings( $request['settings'] );
		$message                             = 'Post Settings ';
	}
	/**
	 * Dashboard
	 */
	if ( isset( $request['fed_admin_unique_post'] ) && 'fed_admin_settings_dashboard' == $request['fed_admin_unique_post'] ) {
		$fed_admin_settings_post['dashboard'] = fed_process_admin_settings_post_dashboard( $request['dashboard'] );
		$message                              = 'Post Dashboard Settings ';
	}
	/**
	 * Menu
	 */
	if ( isset( $request['fed_admin_unique_post'] ) && 'fed_admin_settings_post_menu' == $request['fed_admin_unique_post'] ) {
		$fed_admin_settings_post['menu'] = fed_process_admin_settings_post_menu( $request['fed_post_options'] );
		$message                         = 'Post Menu ';
	}

	/**
	 * Permissions
	 */
	if ( isset( $request['fed_admin_unique_post'] ) && 'fed_admin_permission_post' == $request['fed_admin_unique_post'] ) {
		$fed_admin_settings_post['permissions'] = fed_process_admin_settings_post_permissions( $request['permissions'] );
		$message                                = 'Post Permission ';
	}

	apply_filters( 'fed_admin_settings_post', $fed_admin_settings_post );

	update_option( 'fed_admin_settings_post', $fed_admin_settings_post );

	wp_send_json_success( array(
		'message' => __( $message . ' Updated Successfully ' )
	) );
}

function fed_process_admin_settings_post_permissions( $request ) {
	return array(
		'post_permission'   => isset( $request['post_permission'] ) ? $request['post_permission'] : array(),
		'fed_upload_permission' => isset( $request['fed_upload_permission'] ) ? $request['fed_upload_permission'] : array(),
	);
}

function fed_process_admin_settings_post_settings( $request ) {
	return array(
		'fed_post_status' => isset( $request['fed_post_status'] ) ? sanitize_text_field( $request['fed_post_status'] ) : 'publish',
	);
}

function fed_process_admin_settings_post_menu( $request ) {
	return array(
		'rename_post'    => isset( $request['menu']['rename_post'] ) ? sanitize_text_field( $request['menu']['rename_post'] ) : 'Post',
		'post_position'  => isset( $request['menu']['post_position'] ) ? sanitize_text_field( $request['menu']['post_position'] ) : 2,
		'post_menu_icon' => isset( $request['menu']['post_menu_icon'] ) ? sanitize_text_field( $request['menu']['post_menu_icon'] ) : 'fa fa-file-text',
	);
}

function fed_process_admin_settings_post_dashboard( $request ) {
	return array(
		'post_content'                  => isset( $request['post_content'] ) ? sanitize_text_field( $request['post_content'] ) : '',
		'fed_post_dashboard_category'       => isset( $request['fed_post_dashboard_category'] ) ? sanitize_text_field( $request['fed_post_dashboard_category'] ) : '',
		'fed_post_dashboard_tag'            => isset( $request['fed_post_dashboard_tag'] ) ? sanitize_text_field( $request['fed_post_dashboard_tag'] ) : '',
		'featured_image' => isset( $request['featured_image'] ) ? sanitize_text_field( $request['featured_image'] ) : '',
		'post_format'    => isset( $request['post_format'] ) ? sanitize_text_field( $request['post_format'] ) : '',
		'allow_comments' => isset( $request['allow_comments'] ) ? sanitize_text_field( $request['allow_comments'] ) : '',
	);
}