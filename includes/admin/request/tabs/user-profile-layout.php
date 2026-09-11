<?php
/**
 * User Profile.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Setting User Profile Request
 */
function fed_admin_setting_upl_request() {
	$request                            = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	$fed_admin_settings_upl             = get_option( 'fed_admin_settings_upl', array() );
	if ( ! is_array( $fed_admin_settings_upl ) ) {
		$fed_admin_settings_upl = array();
	}
	if ( ! isset( $fed_admin_settings_upl['settings'] ) || ! is_array( $fed_admin_settings_upl['settings'] ) ) {
		$fed_admin_settings_upl['settings'] = array();
	}

	$fed_admin_settings_upl['settings'] = array_merge(
		$fed_admin_settings_upl['settings'],
		array(
			'fed_upl_website_logo'          => isset( $request['settings']['fed_upl_website_logo'] ) ? (int) $request['settings']['fed_upl_website_logo'] : ( $fed_admin_settings_upl['settings']['fed_upl_website_logo'] ?? null ),
			'fed_upl_website_logo_width'    => isset( $request['settings']['fed_upl_website_logo_width'] ) ? sanitize_text_field( $request['settings']['fed_upl_website_logo_width'] ) : ( $fed_admin_settings_upl['settings']['fed_upl_website_logo_width'] ?? '' ),
			'fed_upl_website_logo_height'   => isset( $request['settings']['fed_upl_website_logo_height'] ) ? sanitize_text_field( $request['settings']['fed_upl_website_logo_height'] ) : ( $fed_admin_settings_upl['settings']['fed_upl_website_logo_height'] ?? '' ),
			'fed_upl_template_model'        => isset( $request['settings']['fed_upl_template_model'] ) ? sanitize_text_field( $request['settings']['fed_upl_template_model'] ) : ( $fed_admin_settings_upl['settings']['fed_upl_template_model'] ?? 'default' ),
			'fed_upl_change_profile_pic'    => isset( $request['settings']['fed_upl_change_profile_pic'] ) ? sanitize_text_field(
				$request['settings']['fed_upl_change_profile_pic']
			) : ( $fed_admin_settings_upl['settings']['fed_upl_change_profile_pic'] ?? '' ),
			'fed_upl_disable_desc'          => isset( $request['settings']['fed_upl_disable_desc'] ) ? sanitize_text_field(
				$request['settings']['fed_upl_disable_desc']
			) : ( $fed_admin_settings_upl['settings']['fed_upl_disable_desc'] ?? '' ),
			'fed_upl_no_recent_post'        => isset( $request['settings']['fed_upl_no_recent_post'] ) ? (int) $request['settings']['fed_upl_no_recent_post'] : ( $fed_admin_settings_upl['settings']['fed_upl_no_recent_post'] ?? 5 ),
			'fed_upl_collapse_menu'         => isset( $request['settings']['fed_upl_collapse_menu'] ) ? $request['settings']['fed_upl_collapse_menu'] : ( $fed_admin_settings_upl['settings']['fed_upl_collapse_menu'] ?? null ),
			'fed_upl_disable_logout'        => isset( $request['settings']['fed_upl_disable_logout'] ) ? $request['settings']['fed_upl_disable_logout'] : ( $fed_admin_settings_upl['settings']['fed_upl_disable_logout'] ?? null ),
			'fed_upl_disable_collapse_menu' => isset( $request['settings']['fed_upl_disable_collapse_menu'] ) ? $request['settings']['fed_upl_disable_collapse_menu'] : ( $fed_admin_settings_upl['settings']['fed_upl_disable_collapse_menu'] ?? null ),
		)
	);

	$new_settings = apply_filters( 'fed_admin_settings_upl', $fed_admin_settings_upl, $request );

	update_option( 'fed_admin_settings_upl', $new_settings );

	wp_send_json_success(
		array(
			'message' => __( 'Dashboard Settings Updated Successfully', 'frontend-dashboard' ),
		)
	);
}

/**
 * Admin Setting Hide Admin Bar Request.
 */
function fed_admin_setting_upl_hide_bar_request() {
	$request                = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	$fed_admin_settings_upl = get_option( 'fed_admin_settings_upl_hide_admin_bar', array() );
	if ( ! is_array( $fed_admin_settings_upl ) ) {
		$fed_admin_settings_upl = array();
	}

	$fed_admin_settings_upl['hide_admin_menu_bar'] = array(
		'role' => isset( $request['hide_menu_bar']['role'] ) && is_array( $request['hide_menu_bar']['role'] ) ? $request['hide_menu_bar']['role'] : array(),
	);

	$new_settings = apply_filters( 'fed_admin_settings_upl_hide_admin_bar', $fed_admin_settings_upl, $request );
	update_option( 'fed_admin_settings_upl_hide_admin_bar', $new_settings );

	wp_send_json_success(
		array(
			'message' => __( 'Admin Bar Visibility Updated Successfully', 'frontend-dashboard' ),
		)
	);
}

if ( ! function_exists( 'fed_hide_admin_bar_init_handler' ) ) {
	/**
	 * Hide WordPress Admin Bar on frontend based on User Role settings.
	 */
	function fed_hide_admin_bar_init_handler() {
		$fed_admin_options = get_option( 'fed_admin_settings_upl_hide_admin_bar' );
		if ( empty( $fed_admin_options['hide_admin_menu_bar']['role'] ) ) {
			return;
		}

		$roles_to_hide = $fed_admin_options['hide_admin_menu_bar']['role'];

		if ( ! is_user_logged_in() ) {
			if ( array_key_exists( 'fed_disable_all_user', $roles_to_hide ) ) {
				show_admin_bar( false );
			}
			return;
		}

		$user = wp_get_current_user();
		if ( ! $user || empty( $user->roles ) ) {
			return;
		}

		foreach ( (array) $user->roles as $role ) {
			if ( array_key_exists( $role, $roles_to_hide ) ) {
				show_admin_bar( false );
				break;
			}
		}
	}
	add_action( 'init', 'fed_hide_admin_bar_init_handler' );
}


/**
 * Admin Setting User Profile Level Color request.
 */
function fed_admin_setting_upl_color_request() {
	$request                = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	$fed_admin_settings_upl = get_option( 'fed_admin_setting_upl_color', array() );
	if ( ! is_array( $fed_admin_settings_upl ) ) {
		$fed_admin_settings_upl = array();
	}

	$colors = isset( $request['color'] ) && is_array( $request['color'] ) ? $request['color'] : array();

	$fed_admin_settings_upl['color'] = array(
		'fed_upl_color_bg_color'       => isset( $colors['fed_upl_color_bg_color'] ) ? sanitize_text_field( $colors['fed_upl_color_bg_color'] ) : '#4F46E5',
		'fed_upl_color_bg_font_color'  => isset( $colors['fed_upl_color_bg_font_color'] ) ? sanitize_text_field( $colors['fed_upl_color_bg_font_color'] ) : '#FFFFFF',
		'fed_upl_color_sbg_color'      => isset( $colors['fed_upl_color_sbg_color'] ) ? sanitize_text_field( $colors['fed_upl_color_sbg_color'] ) : '#0F172A',
		'fed_upl_color_sbg_font_color' => isset( $colors['fed_upl_color_sbg_font_color'] ) ? sanitize_text_field( $colors['fed_upl_color_sbg_font_color'] ) : '#FFFFFF',
		'fed_upl_color_sidebar_bg'     => isset( $colors['fed_upl_color_sidebar_bg'] ) ? sanitize_text_field( $colors['fed_upl_color_sidebar_bg'] ) : '#FFFFFF',
		'fed_upl_color_sidebar_text'   => isset( $colors['fed_upl_color_sidebar_text'] ) ? sanitize_text_field( $colors['fed_upl_color_sidebar_text'] ) : '#475569',
		'fed_upl_color_active_bg'      => isset( $colors['fed_upl_color_active_bg'] ) ? sanitize_text_field( $colors['fed_upl_color_active_bg'] ) : '#EEF2FF',
		'fed_upl_color_active_text'    => isset( $colors['fed_upl_color_active_text'] ) ? sanitize_text_field( $colors['fed_upl_color_active_text'] ) : '#4338CA',
		'fed_upl_color_body_bg'        => isset( $colors['fed_upl_color_body_bg'] ) ? sanitize_text_field( $colors['fed_upl_color_body_bg'] ) : '#F8FAFC',
		'fed_upl_color_card_bg'        => isset( $colors['fed_upl_color_card_bg'] ) ? sanitize_text_field( $colors['fed_upl_color_card_bg'] ) : '#FFFFFF',
		'fed_upl_color_text_main'      => isset( $colors['fed_upl_color_text_main'] ) ? sanitize_text_field( $colors['fed_upl_color_text_main'] ) : '#0F172A',
		'fed_upl_color_border'         => isset( $colors['fed_upl_color_border'] ) ? sanitize_text_field( $colors['fed_upl_color_border'] ) : '#E2E8F0',
	);

	$new_value = apply_filters( 'fed_admin_settings_upl_color', $fed_admin_settings_upl, $request );

	update_option( 'fed_admin_setting_upl_color', $new_value );

	wp_send_json_success(
		array(
			'message' => __( 'Color Theme Settings Updated Successfully', 'frontend-dashboard' ),
		)
	);
}
