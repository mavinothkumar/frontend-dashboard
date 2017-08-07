<?php
/**
 * Admin Setting User Profile Request
 */
function fed_admin_setting_upl_request() {
	$request                            = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$fed_admin_settings_upl             = get_option( 'fed_admin_settings_upl' );
	$fed_admin_settings_upl['settings'] = array(
		'fed_upl_change_profile_pic' => isset( $request['settings']['fed_upl_change_profile_pic'] ) ? sanitize_text_field($request['settings']['fed_upl_change_profile_pic']) : '',
		'fed_upl_disable_desc'       => isset( $request['settings']['fed_upl_disable_desc'] ) ? sanitize_text_field($request['settings']['fed_upl_disable_desc']) : '',
		'fed_upl_no_recent_post'     => isset( $request['settings']['fed_upl_no_recent_post'] ) ? (int) $request['settings']['fed_upl_no_recent_post'] : '5',
	);

	apply_filters( 'fed_admin_settings_upl', $fed_admin_settings_upl );

	update_option( 'fed_admin_settings_upl', $fed_admin_settings_upl );

	wp_send_json_success( array(
		'message' => __( 'User Profile Settings Updated Successfully ' )
	) );
}
