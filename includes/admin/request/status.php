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
add_action( 'wp_ajax_fed_tools_purge_all', 'fed_tools_purge_all' );
add_action( 'wp_ajax_fed_tools_clear_activity_log', 'fed_tools_clear_activity_log' );

add_action( 'wp_ajax_nopriv_fed_status_delete_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_empty_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_create_table', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_optimize_tables', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_delete_option', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_delete_all_option', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_clear_log', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_status_run_cron', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_tools_purge_all', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_tools_clear_activity_log', 'fed_block_the_action' );

/**
 * Drop Database Table.
 */
function fed_status_delete_table() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
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
			if ( function_exists( 'fed_log_activity' ) ) {
				fed_log_activity( 'database', 'Database: Dropped Table', sprintf( 'Table "%s" was dropped permanently from MySQL.', $table_name ), 'warning' );
			}
			wp_send_json_success(
				array(
					'message' => sprintf( __( 'Table "%s" was successfully dropped.', 'frontend-dashboard' ), $table_name ),
					'reload'  => admin_url( 'admin.php?page=fed_tools#database_tables' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
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
			if ( function_exists( 'fed_log_activity' ) ) {
				fed_log_activity( 'database', 'Database: Emptied / Truncated Table', sprintf( 'All records from table "%s" were emptied.', $table_name ), 'warning' );
			}
			wp_send_json_success(
				array(
					'message' => sprintf( __( 'Table "%s" was successfully emptied.', 'frontend-dashboard' ), $table_name ),
					'reload'  => admin_url( 'admin.php?page=fed_tools#database_tables' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	if ( function_exists( 'fed_plugin_activation' ) ) {
		fed_plugin_activation();
	}
	if ( function_exists( 'fed_next_updates' ) ) {
		fed_next_updates();
	}

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity( 'database', 'Database: Schema Builder / Table Repair', 'Verified and created/repaired plugin database tables via dbDelta.', 'success' );
	}

	wp_send_json_success(
		array(
			'message' => __( 'Plugin database schema created / updated successfully.', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#database_tables' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
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

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity( 'database', 'Database: Optimized & Repaired Tables', sprintf( 'Successfully optimized and repaired %d plugin tables.', count( $tables ) ), 'success' );
	}

	wp_send_json_success(
		array(
			'message' => sprintf( __( 'Successfully optimized and repaired %d plugin tables.', 'frontend-dashboard' ), count( $tables ) ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#database_tables' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	if ( ! empty( $request['option_id'] ) ) {
		global $wpdb;
		$option_id = (int) $request['option_id'];
		$opt_row   = $wpdb->get_row( $wpdb->prepare( "SELECT option_name FROM `{$wpdb->options}` WHERE option_id = %d", $option_id ) );
		$opt_name  = $opt_row ? $opt_row->option_name : "ID #{$option_id}";

		$status    = $wpdb->delete( $wpdb->options, array( 'option_id' => $option_id ), array( '%d' ) );

		if ( $status ) {
			if ( function_exists( 'fed_log_activity' ) ) {
				fed_log_activity( 'option', 'Option Store: Deleted Option', sprintf( 'Deleted option key "%s" (ID #%d).', $opt_name, $option_id ), 'warning' );
			}
			wp_send_json_success(
				array(
					'message' => __( 'Option successfully deleted.', 'frontend-dashboard' ),
					'reload'  => admin_url( 'admin.php?page=fed_tools#plugin_options' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	global $wpdb;
	$deleted = $wpdb->query( "DELETE FROM `{$wpdb->options}` WHERE option_name LIKE 'fed%' OR option_name LIKE 'fed_admin_%'" );

	if ( false !== $deleted ) {
		if ( function_exists( 'fed_log_activity' ) ) {
			fed_log_activity( 'option', 'Option Store: Deleted All Plugin Options', sprintf( 'Permanently deleted %d plugin options from wp_options.', (int) $deleted ), 'warning' );
		}
		wp_send_json_success(
			array(
				'message' => sprintf( __( 'Successfully removed %d plugin options.', 'frontend-dashboard' ), (int) $deleted ),
				'reload'  => admin_url( 'admin.php?page=fed_tools#plugin_options' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	$log_file = BC_FED_PLUGIN_DIR . '/log/dashboard.log';
	if ( file_exists( $log_file ) ) {
		file_put_contents( $log_file, '' );
		if ( function_exists( 'fed_log_activity' ) ) {
			fed_log_activity( 'log', 'Log: Cleared File Log', 'Cleared debug log file (dashboard.log) from disk.', 'warning' );
		}
		wp_send_json_success(
			array(
				'message' => __( 'Log file cleared successfully.', 'frontend-dashboard' ),
				'reload'  => admin_url( 'admin.php?page=fed_tools#activity_log' ),
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

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	if ( ! empty( $request['hook'] ) ) {
		$hook = sanitize_text_field( $request['hook'] );
		$args = isset( $request['args'] ) && is_array( $request['args'] ) ? $request['args'] : array();

		do_action_ref_array( $hook, $args );

		if ( function_exists( 'fed_log_activity' ) ) {
			fed_log_activity( 'cron', 'Cron: Executed Hook', sprintf( 'Manually triggered scheduled cron task "%s".', $hook ), 'success' );
		}

		wp_send_json_success(
			array(
				'message' => sprintf( __( 'Cron hook "%s" was executed successfully.', 'frontend-dashboard' ), $hook ),
				'reload'  => admin_url( 'admin.php?page=fed_tools#scheduled_crons' ),
			)
		);
	}

	wp_send_json_error( array( 'message' => __( 'Cron hook name is missing.', 'frontend-dashboard' ) ) );
}

/**
 * Helper: Find or Create Page by Slug.
 *
 * @param string $slug
 * @param string $title
 * @param string $content
 * @param string $template
 * @return int Page ID
 */
function fed_tools_find_or_create_page( $slug, $title, $content, $template = 'fed-canvas' ) {
	$page = get_page_by_path( $slug );
	if ( $page && isset( $page->ID ) && 'trash' !== $page->post_status ) {
		return (int) $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'     => $title,
			'post_name'      => $slug,
			'post_content'   => $content,
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		)
	);

	if ( ! is_wp_error( $page_id ) && $page_id > 0 ) {
		if ( ! empty( $template ) ) {
			update_post_meta( $page_id, '_wp_page_template', $template );
		}
		return (int) $page_id;
	}

	return 0;
}

/**
 * Seeder: Initialize Core Pages and Map to Login Settings.
 */
function fed_tools_seed_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	// 1. Dashboard Page
	$dashboard_id = fed_tools_find_or_create_page( 'dashboard', 'Dashboard', '<!-- wp:fed/dashboard /-->[fed_dashboard]', 'fed-canvas-full' );

	// 2. Login Page
	$login_id = fed_tools_find_or_create_page( 'login', 'Login', '[fed_login]', 'fed-canvas' );

	// 3. Register Page
	$register_id = fed_tools_find_or_create_page( 'register', 'Register', '[fed_register_only]', 'fed-canvas' );

	// 4. Forgot Password Page
	$forgot_id = fed_tools_find_or_create_page( 'forgot-password', 'Forgot Password', '[fed_forgot_password_only]', 'fed-canvas' );

	// Update fed_admin_login settings mapping
	$fed_admin_login = get_option( 'fed_admin_login', array() );
	$fed_admin_login['settings'] = array(
		'fed_login_url'             => (int) $login_id,
		'fed_register_url'          => (int) $register_id,
		'fed_forgot_password_url'   => (int) $forgot_id,
		'fed_redirect_login_url'    => (int) $dashboard_id,
		'fed_redirect_register_url' => (int) $dashboard_id,
		'fed_redirect_logout_url'   => (int) $login_id,
		'fed_dashboard_url'         => (int) $dashboard_id,
	);

	update_option( 'fed_admin_login', $fed_admin_login );

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity(
			'seeder',
			'Seeder: Core Pages & Login Settings',
			sprintf( "Created/Mapped Pages:\n- Dashboard: #%d\n- Login: #%d\n- Register: #%d\n- Forgot Password: #%d", $dashboard_id, $login_id, $register_id, $forgot_id ),
			'success'
		);
	}

	wp_send_json_success(
		array(
			'message' => __( 'Authentication and Dashboard pages created and mapped to Settings successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#seeder' ),
		)
	);
}
add_action( 'wp_ajax_fed_tools_seed_pages', 'fed_tools_seed_pages' );
add_action( 'wp_ajax_nopriv_fed_tools_seed_pages', 'fed_block_the_action' );

/**
 * Seeder: Initialize Dashboard Menus.
 */
function fed_tools_seed_menus() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	global $wpdb;
	$menu_table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_MENU' ) ? BC_FED_TABLE_MENU : 'fed_menu' );
	$user_roles = serialize( array_keys( fed_get_user_roles() ) );

	$default_menus = array(
		array( 'menu_slug' => 'dashboard', 'menu' => 'Dashboard', 'menu_order' => 1, 'menu_image_id' => 'fas fa-tachometer-alt', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'profile', 'menu' => 'Profile', 'menu_order' => 2, 'menu_image_id' => 'fas fa-user', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'post', 'menu' => 'Posts', 'menu_order' => 3, 'menu_image_id' => 'fas fa-newspaper', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'payments', 'menu' => 'Payments', 'menu_order' => 4, 'menu_image_id' => 'fas fa-credit-card', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'logout', 'menu' => 'Logout', 'menu_order' => 99, 'menu_image_id' => 'fas fa-sign-out-alt', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
	);

	$inserted_count = 0;
	foreach ( $default_menus as $m ) {
		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$menu_table} WHERE menu_slug = %s", $m['menu_slug'] ) );
		if ( ! $existing ) {
			$wpdb->insert( $menu_table, $m );
			$inserted_count++;
		}
	}

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity(
			'seeder',
			'Seeder: Standard Navigation Menus',
			sprintf( 'Initialized default dashboard navigation tabs (Dashboard, Profile, Posts, Payments, Logout). %d new tabs inserted.', $inserted_count ),
			'success'
		);
	}

	wp_send_json_success(
		array(
			'message' => __( 'Standard Dashboard navigation menus seeded successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#seeder' ),
		)
	);
}
add_action( 'wp_ajax_fed_tools_seed_menus', 'fed_tools_seed_menus' );
add_action( 'wp_ajax_nopriv_fed_tools_seed_menus', 'fed_block_the_action' );

/**
 * Seeder: Initialize Standard User Profile Fields.
 */
function fed_tools_seed_profile_fields() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	if ( function_exists( 'fed_plugin_meta_data' ) ) {
		fed_plugin_meta_data();
	}

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity(
			'seeder',
			'Seeder: User Profile Fields',
			'Initialized default user profile fields (First Name, Last Name, Nickname, Email, Website, Bio, Password) in database.',
			'success'
		);
	}

	wp_send_json_success(
		array(
			'message' => __( 'User profile fields schema initialized successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#seeder' ),
		)
	);
}
add_action( 'wp_ajax_fed_tools_seed_profile_fields', 'fed_tools_seed_profile_fields' );
add_action( 'wp_ajax_nopriv_fed_tools_seed_profile_fields', 'fed_block_the_action' );

/**
 * Seeder: Run Complete 1-Click Suite.
 */
function fed_tools_seed_all() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	// 1. Tables
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	if ( function_exists( 'fed_plugin_activation' ) ) {
		fed_plugin_activation();
	}
	if ( function_exists( 'fed_next_updates' ) ) {
		fed_next_updates();
	}

	// 2. Core Pages & Settings
	$dashboard_id = fed_tools_find_or_create_page( 'dashboard', 'Dashboard', '<!-- wp:fed/dashboard /-->[fed_dashboard]', 'fed-canvas-full' );
	$login_id     = fed_tools_find_or_create_page( 'login', 'Login', '[fed_login]', 'fed-canvas' );
	$register_id  = fed_tools_find_or_create_page( 'register', 'Register', '[fed_register_only]', 'fed-canvas' );
	$forgot_id    = fed_tools_find_or_create_page( 'forgot-password', 'Forgot Password', '[fed_forgot_password_only]', 'fed-canvas' );

	$fed_admin_login = get_option( 'fed_admin_login', array() );
	$fed_admin_login['settings'] = array(
		'fed_login_url'             => (int) $login_id,
		'fed_register_url'          => (int) $register_id,
		'fed_forgot_password_url'   => (int) $forgot_id,
		'fed_redirect_login_url'    => (int) $dashboard_id,
		'fed_redirect_register_url' => (int) $dashboard_id,
		'fed_redirect_logout_url'   => (int) $login_id,
		'fed_dashboard_url'         => (int) $dashboard_id,
	);
	update_option( 'fed_admin_login', $fed_admin_login );

	// 3. Menus
	global $wpdb;
	$menu_table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_MENU' ) ? BC_FED_TABLE_MENU : 'fed_menu' );
	$user_roles = serialize( array_keys( fed_get_user_roles() ) );

	$default_menus = array(
		array( 'menu_slug' => 'dashboard', 'menu' => 'Dashboard', 'menu_order' => 1, 'menu_image_id' => 'fas fa-tachometer-alt', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'profile', 'menu' => 'Profile', 'menu_order' => 2, 'menu_image_id' => 'fas fa-user', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'post', 'menu' => 'Posts', 'menu_order' => 3, 'menu_image_id' => 'fas fa-newspaper', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'payments', 'menu' => 'Payments', 'menu_order' => 4, 'menu_image_id' => 'fas fa-credit-card', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
		array( 'menu_slug' => 'logout', 'menu' => 'Logout', 'menu_order' => 99, 'menu_image_id' => 'fas fa-sign-out-alt', 'show_user_profile' => 'Enable', 'extra' => 'no', 'parent_id' => '0', 'user_role' => $user_roles ),
	);

	foreach ( $default_menus as $m ) {
		$existing = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$menu_table} WHERE menu_slug = %s", $m['menu_slug'] ) );
		if ( ! $existing ) {
			$wpdb->insert( $menu_table, $m );
		}
	}

	// 4. Meta Data
	if ( function_exists( 'fed_plugin_meta_data' ) ) {
		fed_plugin_meta_data();
	}

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity(
			'seeder',
			'Seeder: 1-Click Bootstrap Suite',
			sprintf( "Full setup suite executed:\n- Database tables verified/repaired\n- Core pages created (Dashboard #%d, Login #%d, Register #%d, Forgot #%d)\n- Standard menus seeded\n- Profile meta schema initialized", $dashboard_id, $login_id, $register_id, $forgot_id ),
			'success'
		);
	}

	wp_send_json_success(
		array(
			'message' => __( 'All Frontend Dashboard settings, core pages, menus, and profile schemas seeded successfully!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#seeder' ),
		)
	);
}
add_action( 'wp_ajax_fed_tools_seed_all', 'fed_tools_seed_all' );
add_action( 'wp_ajax_nopriv_fed_tools_seed_all', 'fed_block_the_action' );

/**
 * Seeder: 1-Click Purge / Reset All Seeded Data.
 */
function fed_tools_purge_all() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	global $wpdb;
	$purged_items = array();

	// 1. Delete Core Pages (/dashboard, /login, /register, /forgot-password)
	$slugs_to_delete = array( 'dashboard', 'login', 'register', 'forgot-password' );
	foreach ( $slugs_to_delete as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && isset( $page->ID ) ) {
			$page_id = (int) $page->ID;
			wp_delete_post( $page_id, true ); // Force permanent deletion so re-seeding is clean
			$purged_items[] = "Deleted page '/{$slug}/' (ID #{$page_id})";
		}
	}

	// 2. Clear Login Page Mappings from fed_admin_login
	$fed_admin_login = get_option( 'fed_admin_login', array() );
	if ( isset( $fed_admin_login['settings'] ) ) {
		$fed_admin_login['settings'] = array();
		update_option( 'fed_admin_login', $fed_admin_login );
		$purged_items[] = 'Cleared Login & Redirect page mappings from Settings';
	}

	// 3. Remove Default Navigation Menus
	$menu_table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_MENU' ) ? BC_FED_TABLE_MENU : 'fed_menu' );
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$menu_table}'" ) === $menu_table ) {
		$deleted_menus = $wpdb->query( "DELETE FROM `{$menu_table}` WHERE menu_slug IN ('dashboard', 'profile', 'post', 'payments', 'logout')" );
		if ( false !== $deleted_menus && $deleted_menus > 0 ) {
			$purged_items[] = "Removed {$deleted_menus} standard navigation menu items";
		}
	}

	// 4. Record Activity in Database Table
	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity(
			'seeder',
			'Seeder: Purge / Reset Seeded Data',
			implode( "\n", $purged_items ),
			'warning'
		);
	}

	wp_send_json_success(
		array(
			'message' => __( 'All seeded pages, navigation menus, and login page mappings were successfully purged and reset!', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#seeder' ),
		)
	);
}

/**
 * Activity Log: Clear / Truncate Database Activity Log Table.
 */
function fed_tools_clear_activity_log() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied. Administrator access required.', 'frontend-dashboard' ) ) );
	}

	$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
	fed_verify_nonce( $request );

	global $wpdb;
	$table_name = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {
		$wpdb->query( "TRUNCATE TABLE `{$table_name}`" );
	}

	if ( function_exists( 'fed_log_activity' ) ) {
		fed_log_activity( 'log', 'Activity Log: Table Cleared', 'All activity audit log history was cleared.', 'warning' );
	}

	wp_send_json_success(
		array(
			'message' => __( 'Activity log table cleared successfully.', 'frontend-dashboard' ),
			'reload'  => admin_url( 'admin.php?page=fed_tools#activity_log' ),
		)
	);
}
add_action( 'wp_ajax_fed_tools_seed_all', 'fed_tools_seed_all' );
add_action( 'wp_ajax_nopriv_fed_tools_seed_all', 'fed_block_the_action' );

