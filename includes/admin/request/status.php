<?php
/**
 * System Status, Database Tables, Options, Cron, and Logs AJAX Operations.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_fed_status_delete_table', 'fed_status_delete_table' );
add_action( 'wp_ajax_fed_status_empty_table', 'fed_status_empty_table' );
add_action( 'wp_ajax_fed_status_create_table', 'fed_status_create_table' );
add_action( 'wp_ajax_fed_status_optimize_tables', 'fed_status_optimize_tables' );
add_action( 'wp_ajax_fed_status_delete_option', 'fed_status_delete_option' );
add_action( 'wp_ajax_fed_status_delete_all_option', 'fed_status_delete_all_option' );
add_action( 'wp_ajax_fed_status_clear_log', 'fed_status_clear_log' );
add_action( 'wp_ajax_fed_status_run_cron', 'fed_status_run_cron' );

add_action( 'wp_ajax_nopriv_fed_status_delete_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_empty_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_create_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_optimize_tables', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_delete_option', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_delete_all_option', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_clear_log', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_run_cron', 'fed_block_the_action' );

/**
 * Drop Database Table.
 */
function fed_status_delete_table() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	if ( ! empty( $request['table_name'] ) ) {
		global $wpdb;
		$table_name = sanitize_text_field( $request['table_name'] );

		// Security: Only allow tables with WordPress prefix and starting with fed
		if ( strpos( $table_name, $wpdb->prefix . 'fed' ) !== 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid table name specified.', 'frontend-dashboard' ) ) );
		}

		$status = $wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );

		if ( false !== $status ) {
			wp_send_json_success(
				array(
					'message' => sprintf( __( 'Table "%s" was successfully dropped.', 'frontend-dashboard' ), $table_name ),
					'reload'  => admin_url( 'admin.php?page=fed_status#database_tables' ),
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Could not drop table. Please verify database permissions.', 'frontend-dashboard' ) ) );
	}

	wp_send_json_error( array( 'message' => __( 'Table name parameter is missing.', 'frontend-dashboard' ) ) );
}

/**
 * Truncate / Empty Database Table.
 */
function fed_status_empty_table() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	if ( ! empty( $request['table_name'] ) ) {
		global $wpdb;
		$table_name = sanitize_text_field( $request['table_name'] );

		// Security: Only allow tables with WordPress prefix and starting with fed
		if ( strpos( $table_name, $wpdb->prefix . 'fed' ) !== 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid table name specified.', 'frontend-dashboard' ) ) );
		}

		$status = $wpdb->query( "TRUNCATE TABLE `{$table_name}`" );

		if ( false !== $status ) {
			wp_send_json_success(
				array(
					'message' => sprintf( __( 'Table "%s" was successfully emptied.', 'frontend-dashboard' ), $table_name ),
					'reload'  => admin_url( 'admin.php?page=fed_status#database_tables' ),
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Could not truncate table.', 'frontend-dashboard' ) ) );
	}

	wp_send_json_error( array( 'message' => __( 'Table name parameter is missing.', 'frontend-dashboard' ) ) );
}

/**
 * Create Missing Table or All Tables.
 */
function fed_status_create_table() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	if ( function_exists( 'fed_plugin_activation' ) ) {
		fed_plugin_activation();
	}
	if ( function_exists( 'fed_next_updates' ) ) {
		fed_next_updates();
	}

	wp_send_json_success(
		array(
			'message' => __( 'Plugin database schema created / updated successfully.', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_status#database_tables' ),
		)
	);
}

/**
 * Optimize & Repair Database Tables.
 */
function fed_status_optimize_tables() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	global $wpdb;
	$sql    = "SHOW TABLES LIKE '{$wpdb->prefix}fed%'";
	$tables = $wpdb->get_col( $sql );

	if ( ! empty( $tables ) ) {
		foreach ( $tables as $table_name ) {
			$wpdb->query( "OPTIMIZE TABLE `{$table_name}`" );
			$wpdb->query( "REPAIR TABLE `{$table_name}`" );
		}
	}

	wp_send_json_success(
		array(
			'message' => sprintf( __( 'Successfully optimized and repaired %d plugin tables.', 'frontend-dashboard' ), count( $tables ) ),
			'reload'  => admin_url( 'admin.php?page=fed_status#database_tables' ),
		)
	);
}

/**
 * Delete a Single WordPress Option.
 */
function fed_status_delete_option() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	if ( ! empty( $request['option_id'] ) ) {
		global $wpdb;
		$option_id = (int) $request['option_id'];
		$status    = $wpdb->delete( $wpdb->options, array( 'option_id' => $option_id ), array( '%d' ) );

		if ( $status ) {
			wp_send_json_success(
				array(
					'message' => __( 'Option successfully deleted.', 'frontend-dashboard' ),
					'reload'  => admin_url( 'admin.php?page=fed_status#plugin_options' ),
				)
			);
		}
		wp_send_json_error( array( 'message' => __( 'Could not delete option from database.', 'frontend-dashboard' ) ) );
	}

	wp_send_json_error( array( 'message' => __( 'Option ID is missing.', 'frontend-dashboard' ) ) );
}

/**
 * Delete All Plugin Options.
 */
function fed_status_delete_all_option() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	global $wpdb;
	$deleted = $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE option_name LIKE 'fed%' OR option_name LIKE 'fed_admin_%'" );

	if ( false !== $deleted ) {
		wp_send_json_success(
			array(
				'message' => sprintf( __( 'Successfully removed %d plugin options.', 'frontend-dashboard' ), (int) $deleted ),
				'reload'  => admin_url( 'admin.php?page=fed_status#plugin_options' ),
			)
		);
	}
	wp_send_json_error( array( 'message' => __( 'Could not delete options.', 'frontend-dashboard' ) ) );
}

/**
 * Clear / Empty the Log File.
 */
function fed_status_clear_log() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	$log_file = BC_FED_PLUGIN_DIR . '/log/dashboard.log';
	if ( file_exists( $log_file ) ) {
		file_put_contents( $log_file, '' );
		wp_send_json_success(
			array(
				'message' => __( 'Log file cleared successfully.', 'frontend-dashboard' ),
				'reload'  => admin_url( 'admin.php?page=fed_status#file_logs' ),
			)
		);
	}

	wp_send_json_error( array( 'message' => __( 'Log file does not exist or cannot be written.', 'frontend-dashboard' ) ) );
}

/**
 * Execute a Scheduled Cron Job Immediately.
 */
function fed_status_run_cron() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	fed_verify_nonce( $request );

	if ( ! empty( $request['hook'] ) ) {
		$hook = sanitize_text_field( $request['hook'] );
		$args = isset( $request['args'] ) && is_array( $request['args'] ) ? $request['args'] : array();

		do_action_ref_array( $hook, $args );

		wp_send_json_success(
			array(
				'message' => sprintf( __( 'Cron hook "%s" was executed successfully.', 'frontend-dashboard' ), $hook ),
				'reload'  => admin_url( 'admin.php?page=fed_status#cron_jobs' ),
			)
		);
	}

	wp_send_json_error( array( 'message' => __( 'Cron hook name is missing.', 'frontend-dashboard' ) ) );
}
