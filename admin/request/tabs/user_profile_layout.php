<?php
/**
 * Admin Setting User Profile Request
 */
function fed_admin_setting_upl_request() {
	$request                            = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$fed_admin_settings_upl             = get_option( 'fed_admin_settings_upl' );
	$fed_admin_settings_upl['settings'] = array(
		'fed_upl_change_profile_pic' => isset( $request['settings']['fed_upl_change_profile_pic'] ) ? sanitize_text_field( $request['settings']['fed_upl_change_profile_pic'] ) : '',
		'fed_upl_disable_desc'       => isset( $request['settings']['fed_upl_disable_desc'] ) ? sanitize_text_field( $request['settings']['fed_upl_disable_desc'] ) : '',
		'fed_upl_no_recent_post'     => isset( $request['settings']['fed_upl_no_recent_post'] ) ? (int) $request['settings']['fed_upl_no_recent_post'] : '5',
		'fed_upl_collapse_menu'     => isset( $request['settings']['fed_upl_collapse_menu'] ) ?  $request['settings']['fed_upl_collapse_menu'] : null,
		'fed_upl_disable_logout'     => isset( $request['settings']['fed_upl_disable_logout'] ) ?  $request['settings']['fed_upl_disable_logout'] : null,
		'fed_upl_disable_collapse_menu'     => isset( $request['settings']['fed_upl_disable_collapse_menu'] ) ? $request['settings']['fed_upl_disable_collapse_menu'] : null,
	);

	$new_settings = apply_filters( 'fed_admin_settings_upl', $fed_admin_settings_upl, $request );

	update_option( 'fed_admin_settings_upl', $new_settings );

	wp_send_json_success( array(
		'message' => __( 'User Profile Settings Updated Successfully ' )
	) );
}

function fed_admin_setting_upl_color_request() {
	$request                         = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	$fed_admin_settings_upl          = get_option( 'fed_admin_setting_upl_color' );
	$fed_admin_settings_upl['color'] = array(
		'fed_upl_color_bg_color' => isset( $request['color']['fed_upl_color_bg_color'] ) ? sanitize_text_field( $request['color']['fed_upl_color_bg_color'] ) : '#0AAAAA',

		'fed_upl_color_bg_font_color'       => isset( $request['color']['fed_upl_color_bg_font_color'] ) ? sanitize_text_field( $request['color']['fed_upl_color_bg_font_color'] ) : '#ffffff',

		'fed_upl_color_sbg_color' => isset( $request['color']['fed_upl_color_sbg_color'] ) ? sanitize_text_field( $request['color']['fed_upl_color_sbg_color'] ) : '#033333',

		'fed_upl_color_sbg_font_color'       => isset( $request['color']['fed_upl_color_sbg_font_color'] ) ? sanitize_text_field( $request['color']['fed_upl_color_sbg_font_color'] ) : '#ffffff',

	);

	$new_value = apply_filters( 'fed_admin_settings_upl_color', $fed_admin_settings_upl, $request );

	update_option( 'fed_admin_setting_upl_color', $new_value );

	wp_send_json_success( array(
		'message' => __( 'Color Settings Updated Successfully ' )
	) );
}
