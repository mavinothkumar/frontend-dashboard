<?php
/**
 * Add-ons Marketplace & Extensions AJAX Operations.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_fed_addon_activate', 'fed_addon_activate_handler' );
add_action( 'wp_ajax_fed_addon_deactivate', 'fed_addon_deactivate_handler' );
add_action( 'wp_ajax_fed_addon_refresh_catalog', 'fed_addon_refresh_catalog_handler' );

add_action( 'wp_ajax_nopriv_fed_addon_activate', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_addon_deactivate', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_addon_refresh_catalog', 'fed_block_the_action' );

/**
 * Activate Add-on / Plugin via AJAX.
 */
function fed_addon_activate_handler() {
	if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	$plugin_file = isset( $request['plugin_file'] ) ? sanitize_text_field( $request['plugin_file'] ) : '';

	if ( empty( $plugin_file ) ) {
		wp_send_json_error( array( 'message' => __( 'Plugin file parameter is missing.', 'frontend-dashboard' ) ) );
	}

	// Security validation: ensure plugin file is within WP_PLUGIN_DIR
	$full_path = WP_PLUGIN_DIR . '/' . $plugin_file;
	if ( ! file_exists( $full_path ) ) {
		wp_send_json_error( array( 'message' => __( 'Plugin file does not exist on server.', 'frontend-dashboard' ) ) );
	}

	if ( ! function_exists( 'activate_plugin' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$result = activate_plugin( $plugin_file );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success(
		array(
			'message' => __( 'Extension activated successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_plugin_pages' ),
		)
	);
}

/**
 * Deactivate Add-on / Plugin via AJAX.
 */
function fed_addon_deactivate_handler() {
	if ( ! current_user_can( 'deactivate_plugins' ) && ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	$plugin_file = isset( $request['plugin_file'] ) ? sanitize_text_field( $request['plugin_file'] ) : '';

	if ( empty( $plugin_file ) ) {
		wp_send_json_error( array( 'message' => __( 'Plugin file parameter is missing.', 'frontend-dashboard' ) ) );
	}

	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	deactivate_plugins( $plugin_file );

	wp_send_json_success(
		array(
			'message' => __( 'Extension deactivated successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_plugin_pages' ),
		)
	);
}

/**
 * Refresh Addon Catalog Cache.
 */
function fed_addon_refresh_catalog_handler() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing
	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	delete_transient( 'fed_plugin_list_api' );

	wp_send_json_success(
		array(
			'message' => __( 'Add-ons catalog refreshed successfully.', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_plugin_pages' ),
		)
	);
}
