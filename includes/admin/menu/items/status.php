<?php
/**
 * System Status, Health Diagnostics, Database Manager, Options Store, Cron & Logs.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_get_status_menu' ) ) {
	/**
	 * Render System Status and Health Dashboard.
	 */
	function fed_get_status_menu() {
		global $wp_version, $wpdb;

		// ----------------------------------------------------
		// 1. DATA GATHERING: Environment & Server Info
		// ----------------------------------------------------
		$php_version        = PHP_VERSION;
		$server_software    = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : 'Unknown';
		$wp_memory_limit    = WP_MEMORY_LIMIT;
		$php_memory_limit   = ini_get( 'memory_limit' );
		$max_execution_time = ini_get( 'max_execution_time' );
		$max_input_vars     = ini_get( 'max_input_vars' );
		$upload_max_filesize= ini_get( 'upload_max_filesize' );
		$post_max_size      = ini_get( 'post_max_size' );
		$is_ssl             = is_ssl();
		$uploads_dir        = wp_upload_dir();
		$uploads_writable   = wp_is_writable( $uploads_dir['basedir'] );
		$log_file           = BC_FED_PLUGIN_DIR . '/log/dashboard.log';
		$log_file_writable  = file_exists( $log_file ) ? wp_is_writable( $log_file ) : wp_is_writable( dirname( $log_file ) );
		$log_file_size      = file_exists( $log_file ) ? size_format( filesize( $log_file ), 2 ) : '0 B';
		$log_file_modified  = file_exists( $log_file ) ? date_i18n( 'M j, Y H:i:s', filemtime( $log_file ) ) : __( 'Never', 'frontend-dashboard' );

		// ----------------------------------------------------
		// 2. DATA GATHERING: Core Frontend Dashboard Modules
		// ----------------------------------------------------
		$fed_login_opt     = get_option( 'fed_admin_login', array() );
		$login_configured  = ! empty( $fed_login_opt['settings']['fed_login_url'] );
		$register_opt      = get_option( 'fed_login_details', array() );
		$fed_cp_opt        = get_option( 'fed_cp_admin_settings', array() );
		$post_configured   = ! empty( $fed_cp_opt['post'] );
		$upl_opt           = get_option( 'fed_admin_settings_upl', array() );
		$upl_configured    = ! empty( $upl_opt['settings'] );

		// Addon plugins
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		$addons = array(
			'frontend-dashboard-custom-post'    => array( 'name' => 'Custom Post & Taxonomies', 'file' => 'frontend-dashboard-custom-post/frontend-dashboard-custom-post.php' ),
			'frontend-dashboard-captcha'        => array( 'name' => 'reCAPTCHA Spam Protection', 'file' => 'frontend-dashboard-captcha/frontend-dashboard-captcha.php' ),
			'frontend-dashboard-user-management'=> array( 'name' => 'User Management', 'file' => 'frontend-dashboard-user-management/frontend-dashboard-user-management.php' ),
			'frontend-dashboard-templates'      => array( 'name' => 'Dashboard Templates', 'file' => 'frontend-dashboard-templates/frontend-dashboard-templates.php' ),
			'frontend-dashboard-social-chat'    => array( 'name' => 'Social Chat', 'file' => 'frontend-dashboard-social-chat/frontend-dashboard-social-chat.php' ),
			'frontend-dashboard-payments'       => array( 'name' => 'Payments & Subscriptions', 'file' => 'frontend-dashboard-payments/frontend-dashboard-payments.php' ),
		);

		// ----------------------------------------------------
		// 3. DATA GATHERING: Database Tables
		// ----------------------------------------------------
		$expected_core_tables = array(
			$wpdb->prefix . BC_FED_TABLE_USER_PROFILE => array( 'label' => 'User Profile Fields', 'schema' => 'BC_FED_TABLE_USER_PROFILE' ),
			$wpdb->prefix . BC_FED_TABLE_POST         => array( 'label' => 'Post & Custom Post Fields', 'schema' => 'BC_FED_TABLE_POST' ),
			$wpdb->prefix . BC_FED_TABLE_MENU         => array( 'label' => 'Dashboard Menus', 'schema' => 'BC_FED_TABLE_MENU' ),
			$wpdb->prefix . BC_FED_TABLE_MENU_META    => array( 'label' => 'Dashboard Menu Metadata', 'schema' => 'BC_FED_TABLE_MENU_META' ),
			$wpdb->prefix . BC_FED_TABLE_PAYMENT      => array( 'label' => 'Payments', 'schema' => 'BC_FED_TABLE_PAYMENT' ),
			$wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS=> array( 'label' => 'Payment Items', 'schema' => 'BC_FED_TABLE_PAYMENT_ITEMS' ),
		);

		$db_existing_tables = $wpdb->get_col( "SHOW TABLES LIKE '{$wpdb->prefix}fed%'" );
		$all_table_keys     = array_unique( array_merge( array_keys( $expected_core_tables ), $db_existing_tables ) );

		$tables_data = array();
		$total_db_size = 0;
		$total_db_rows = 0;

		foreach ( $all_table_keys as $t_name ) {
			$exists = in_array( $t_name, $db_existing_tables, true );
			$label  = isset( $expected_core_tables[ $t_name ] ) ? $expected_core_tables[ $t_name ]['label'] : __( 'Plugin Table', 'frontend-dashboard' );

			if ( $exists ) {
				$status_row = $wpdb->get_row( $wpdb->prepare( "SHOW TABLE STATUS LIKE %s", $t_name ) );
				$rows       = isset( $status_row->Rows ) ? (int) $status_row->Rows : 0;
				$data_len   = isset( $status_row->Data_length ) ? (int) $status_row->Data_length : 0;
				$index_len  = isset( $status_row->Index_length ) ? (int) $status_row->Index_length : 0;
				$engine     = isset( $status_row->Engine ) ? $status_row->Engine : 'InnoDB';
				$collation  = isset( $status_row->Collation ) ? $status_row->Collation : 'utf8mb4_unicode_ci';

				$total_size    = $data_len + $index_len;
				$total_db_size += $total_size;
				$total_db_rows += $rows;

				$tables_data[ $t_name ] = array(
					'exists'    => true,
					'name'      => $t_name,
					'label'     => $label,
					'rows'      => $rows,
					'data_size' => size_format( $data_len, 2 ),
					'index_size'=> size_format( $index_len, 2 ),
					'total_size'=> size_format( $total_size, 2 ),
					'engine'    => $engine,
					'collation' => $collation,
				);
			} else {
				$tables_data[ $t_name ] = array(
					'exists'    => false,
					'name'      => $t_name,
					'label'     => $label,
					'rows'      => 0,
					'data_size' => '0 B',
					'index_size'=> '0 B',
					'total_size'=> '0 B',
					'engine'    => '-',
					'collation' => '-',
				);
			}
		}

		// ----------------------------------------------------
		// 4. DATA GATHERING: Options Store
		// ----------------------------------------------------
		$options_query = $wpdb->get_results( "SELECT option_id, option_name, option_value, autoload FROM `{$wpdb->options}` WHERE option_name LIKE 'fed%' OR option_name LIKE 'fed_admin_%' ORDER BY option_name ASC" );
		$total_options = count( $options_query );

		// ----------------------------------------------------
		// 5. DATA GATHERING: Scheduled Cron Jobs
		// ----------------------------------------------------
		$cron_array = _get_cron_array();
		$cron_jobs  = array();
		$current_time = time();

		if ( is_array( $cron_array ) ) {
			foreach ( $cron_array as $timestamp => $hooks ) {
				foreach ( $hooks as $hook_name => $hook_data ) {
					foreach ( $hook_data as $key => $details ) {
						$is_fed = ( strpos( $hook_name, 'fed' ) !== false );
						$cron_jobs[] = array(
							'hook'       => $hook_name,
							'is_fed'     => $is_fed,
							'timestamp'  => $timestamp,
							'schedule'   => ! empty( $details['schedule'] ) ? $details['schedule'] : __( 'One-off', 'frontend-dashboard' ),
							'interval'   => isset( $details['interval'] ) ? $details['interval'] : null,
							'args'       => isset( $details['args'] ) ? $details['args'] : array(),
							'diff'       => human_time_diff( $current_time, $timestamp ),
							'is_past'    => $timestamp < $current_time,
						);
					}
				}
			}
		}

		// Sort crons by timestamp
		usort( $cron_jobs, function( $a, $b ) {
			return $a['timestamp'] - $b['timestamp'];
		});

		// ----------------------------------------------------
		// 6. DATA GATHERING: Log File Content
		// ----------------------------------------------------
		$log_lines = array();
		if ( file_exists( $log_file ) && is_readable( $log_file ) ) {
			$raw_log = file_get_contents( $log_file );
			if ( ! empty( $raw_log ) ) {
				$lines = explode( "\n", trim( $raw_log ) );
				$log_lines = array_slice( $lines, -150 ); // last 150 lines
			}
		}
		?>

		<!-- Scoped Styles for System Status Dashboard -->
		<style>
			/* Primary & Secondary Action Buttons */
			.fed-btn-primary,
			button.fed-btn-primary,
			a.fed-btn-primary {
				background-color: #4f46e5 !important;
				color: #ffffff !important;
				border: 1px solid #4338ca !important;
				box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2) !important;
			}
			.fed-btn-primary:hover,
			button.fed-btn-primary:hover,
			a.fed-btn-primary:hover {
				background-color: #4338ca !important;
				color: #ffffff !important;
			}
			.fed-btn-secondary,
			button.fed-btn-secondary,
			a.fed-btn-secondary {
				background-color: #ffffff !important;
				color: #334155 !important;
				border: 1px solid #e2e8f0 !important;
			}
			.fed-btn-secondary:hover,
			button.fed-btn-secondary:hover,
			a.fed-btn-secondary:hover {
				background-color: #f8fafc !important;
				border-color: #cbd5e1 !important;
				color: #0f172a !important;
			}

			/* Top Navigation Tabs Bar */
			#fed_status_tabs_bar {
				background-color: #ffffff !important;
				border: 1px solid #e2e8f0 !important;
				padding: 6px !important;
				border-radius: 16px !important;
				display: flex !important;
				flex-wrap: wrap !important;
				gap: 6px !important;
			}
			.fed-main-tab-btn,
			a.fed-main-tab-btn {
				display: inline-flex !important;
				align-items: center !important;
				gap: 8px !important;
				padding: 9px 18px !important;
				border-radius: 12px !important;
				font-size: 13px !important;
				font-weight: 600 !important;
				color: #64748b !important;
				background-color: transparent !important;
				border: 1px solid transparent !important;
				text-decoration: none !important;
				transition: all 0.15s ease !important;
				box-shadow: none !important;
				cursor: pointer !important;
				line-height: 1.4 !important;
			}
			.fed-main-tab-btn:hover,
			a.fed-main-tab-btn:hover {
				color: #0f172a !important;
				background-color: #f1f5f9 !important;
			}
			.fed-main-tab-btn.fed-tab-active,
			a.fed-main-tab-btn.fed-tab-active {
				background-color: #4f46e5 !important;
				color: #ffffff !important;
				border-color: #4338ca !important;
				font-weight: 700 !important;
				box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
			}
			.fed-main-tab-btn.fed-tab-active i,
			.fed-main-tab-btn.fed-tab-active span,
			a.fed-main-tab-btn.fed-tab-active i,
			a.fed-main-tab-btn.fed-tab-active span {
				color: #ffffff !important;
			}

			/* Table Styling & Spacing */
			.bc_fed table {
				width: 100% !important;
				border-collapse: separate !important;
				border-spacing: 0 !important;
			}
			.bc_fed table th {
				padding: 14px 18px !important;
				background-color: #f8fafc !important;
				font-size: 11px !important;
				font-weight: 700 !important;
				text-transform: uppercase !important;
				letter-spacing: 0.05em !important;
				color: #64748b !important;
				border-bottom: 1px solid #e2e8f0 !important;
			}
			.bc_fed table td {
				padding: 14px 18px !important;
				font-size: 12px !important;
				line-height: 1.5 !important;
				vertical-align: middle !important;
				border-bottom: 1px solid #f1f5f9 !important;
			}
			.bc_fed table tbody tr:hover td {
				background-color: #f8fafc !important;
			}
			.bc_fed table tfoot td {
				padding: 14px 18px !important;
				background-color: #f8fafc !important;
				border-top: 1px solid #e2e8f0 !important;
			}

			/* Options Search Input with Icon */
			.fed-search-input-wrap {
				position: relative !important;
				display: inline-flex !important;
				align-items: center !important;
			}
			.fed-search-input-wrap .fed-search-icon {
				position: absolute !important;
				left: 14px !important;
				top: 50% !important;
				transform: translateY(-50%) !important;
				color: #94a3b8 !important;
				pointer-events: none !important;
				font-size: 12px !important;
				line-height: 1 !important;
				z-index: 2 !important;
			}
			.fed-search-input-wrap input#fed_options_search_input,
			.fed-search-input-wrap input.fed-status-search-input {
				padding-left: 38px !important;
				padding-right: 16px !important;
				padding-top: 8px !important;
				padding-bottom: 8px !important;
				border-radius: 12px !important;
				border: 1px solid #e2e8f0 !important;
				background-color: #f8fafc !important;
				font-size: 12px !important;
				line-height: 1.5 !important;
				min-width: 250px !important;
				height: 38px !important;
				color: #334155 !important;
				transition: all 0.2s ease !important;
				box-sizing: border-box !important;
			}
			.fed-search-input-wrap input#fed_options_search_input:focus,
			.fed-search-input-wrap input.fed-status-search-input:focus {
				background-color: #ffffff !important;
				border-color: #6366f1 !important;
				box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
				outline: none !important;
			}

			/* Modals & Overlay Loaders */
			.fed-status-modal {
				position: fixed !important;
				inset: 0 !important;
				z-index: 999999 !important;
				display: none !important;
				align-items: center !important;
				justify-content: center !important;
				background-color: rgba(15, 23, 42, 0.65) !important;
				backdrop-filter: blur(4px) !important;
				-webkit-backdrop-filter: blur(4px) !important;
			}
			.fed-status-modal.fed-modal-open {
				display: flex !important;
			}
			.fed-status-modal .status-modal-content {
				background-color: #ffffff !important;
				border-radius: 24px !important;
				padding: 32px !important;
				max-width: 440px !important;
				width: 100% !important;
				margin: 16px !important;
				box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
				border: 1px solid #f1f5f9 !important;
				text-align: center !important;
				transform: scale(0.95);
				opacity: 0;
				transition: all 0.2s ease;
			}
			.fed-status-modal.fed-modal-open .status-modal-content {
				transform: scale(1) !important;
				opacity: 1 !important;
			}

			.fed-status-loader-wrap {
				position: fixed !important;
				inset: 0 !important;
				z-index: 9999999 !important;
				display: none !important;
				align-items: center !important;
				justify-content: center !important;
				background-color: rgba(15, 23, 42, 0.65) !important;
				backdrop-filter: blur(4px) !important;
				-webkit-backdrop-filter: blur(4px) !important;
			}
			.fed-status-loader-wrap.fed-loader-open {
				display: flex !important;
			}
		</style>

		<div class="bc_fed fed-admin-wrap w-full max-w-none px-4 sm:px-8 py-6 sm:py-8 font-sans text-slate-800" data-nonce="<?php echo esc_attr( wp_create_nonce( 'fed_nonce' ) ); ?>" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			<?php echo fed_loader(); ?>

			<!-- Toast Notification Element -->
			<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">
				<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>
				<span id="fed_toast_message" class="text-xs font-semibold tracking-wide">Operation completed successfully.</span>
			</div>

			<!-- Master Header -->
			<div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/90 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
				<div class="flex items-center gap-4">
					<div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-xl shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
						<i class="fas fa-heartbeat" style="color: #ffffff !important;"></i>
					</div>
					<div>
						<div class="flex items-center gap-2.5 flex-wrap">
							<h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight m-0 p-0">
								<?php esc_html_e( 'System Status & Maintenance', 'frontend-dashboard' ); ?>
							</h1>
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
								<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
								<?php esc_html_e( 'System Operational', 'frontend-dashboard' ); ?>
							</span>
						</div>
						<p class="text-xs text-slate-500 m-0 mt-1 font-medium">
							<?php esc_html_e( 'Comprehensive health diagnostics, database table management, options store, scheduled crons, and log audits.', 'frontend-dashboard' ); ?>
						</p>
					</div>
				</div>

				<!-- Header Quick Stats -->
				<div class="flex items-center gap-2.5 flex-wrap shrink-0">
					<div class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
						<span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400"><?php esc_html_e( 'DB Tables', 'frontend-dashboard' ); ?></span>
						<span class="text-xs font-extrabold text-slate-800"><?php echo count( $db_existing_tables ); ?> / <?php echo count( $all_table_keys ); ?></span>
					</div>
					<div class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
						<span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400"><?php esc_html_e( 'Options', 'frontend-dashboard' ); ?></span>
						<span class="text-xs font-extrabold text-slate-800"><?php echo (int) $total_options; ?></span>
					</div>
					<div class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
						<span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400"><?php esc_html_e( 'Crons', 'frontend-dashboard' ); ?></span>
						<span class="text-xs font-extrabold text-slate-800"><?php echo count( $cron_jobs ); ?></span>
					</div>
					<div class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
						<span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400"><?php esc_html_e( 'Log Size', 'frontend-dashboard' ); ?></span>
						<span class="text-xs font-extrabold text-slate-800"><?php echo esc_html( $log_file_size ); ?></span>
					</div>
				</div>
			</div>

			<!-- Main Navigation Tabs Bar -->
			<div class="bg-white rounded-2xl p-1.5 shadow-xs border border-slate-200/80 mb-6 flex flex-wrap gap-1.5" id="fed_status_tabs_bar" role="tablist">
				<a href="#system_health" data-tab="system_health" role="tab" class="fed-main-tab-btn fed-tab-active">
					<i class="fas fa-stethoscope text-xs"></i>
					<span><?php esc_html_e( 'System & Health', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#database_tables" data-tab="database_tables" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-database text-xs"></i>
					<span><?php esc_html_e( 'Database Tables', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#plugin_options" data-tab="plugin_options" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-sliders-h text-xs"></i>
					<span><?php esc_html_e( 'Options Store', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#cron_jobs" data-tab="cron_jobs" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-clock text-xs"></i>
					<span><?php esc_html_e( 'Scheduled Crons', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#file_logs" data-tab="file_logs" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-terminal text-xs"></i>
					<span><?php esc_html_e( 'Activity & File Logs', 'frontend-dashboard' ); ?></span>
				</a>
			</div>

			<!-- Tab 1: System Health & Diagnostics -->
			<div class="fed-status-pane block space-y-6" id="pane_system_health" data-pane="system_health">
				<!-- Server Environment & PHP Info Grid -->
				<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
					<!-- Card 1: Server Environment -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
						<div class="flex items-center gap-3 pb-4 border-b border-slate-100">
							<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
								<i class="fas fa-server"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Server Environment', 'frontend-dashboard' ); ?></h3>
								<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Hardware, PHP execution parameters, and web server software.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-3">
							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'PHP Version', 'frontend-dashboard' ); ?></span>
								<span class="font-mono font-bold <?php echo version_compare( $php_version, '7.4', '>=' ) ? 'text-emerald-600' : 'text-amber-600'; ?>">
									<?php echo esc_html( $php_version ); ?>
									<?php if ( version_compare( $php_version, '8.0', '>=' ) ) : ?>
										<span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-sans font-semibold">Modern</span>
									<?php endif; ?>
								</span>
							</div>

							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'WordPress Version', 'frontend-dashboard' ); ?></span>
								<span class="font-mono font-bold text-slate-800"><?php echo esc_html( $wp_version ); ?></span>
							</div>

							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'Web Server', 'frontend-dashboard' ); ?></span>
								<span class="font-mono text-slate-700 truncate max-w-[200px] text-right" title="<?php echo esc_attr( $server_software ); ?>"><?php echo esc_html( $server_software ); ?></span>
							</div>

							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'PHP Memory Limit', 'frontend-dashboard' ); ?></span>
								<span class="font-mono font-bold text-slate-800"><?php echo esc_html( $php_memory_limit ); ?> (WP: <?php echo esc_html( $wp_memory_limit ); ?>)</span>
							</div>

							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'Max Execution Time', 'frontend-dashboard' ); ?></span>
								<span class="font-mono font-bold text-slate-800"><?php echo esc_html( $max_execution_time ); ?>s</span>
							</div>

							<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'Max Upload / Post Size', 'frontend-dashboard' ); ?></span>
								<span class="font-mono font-bold text-slate-800"><?php echo esc_html( $upload_max_filesize ); ?> / <?php echo esc_html( $post_max_size ); ?></span>
							</div>

							<div class="flex items-center justify-between py-2 text-xs">
								<span class="font-medium text-slate-500"><?php esc_html_e( 'HTTPS / SSL Status', 'frontend-dashboard' ); ?></span>
								<?php if ( $is_ssl ) : ?>
									<span class="inline-flex items-center gap-1 font-bold text-emerald-600"><i class="fas fa-lock text-[10px]"></i> <?php esc_html_e( 'Active (Secure)', 'frontend-dashboard' ); ?></span>
								<?php else : ?>
									<span class="inline-flex items-center gap-1 font-bold text-amber-600"><i class="fas fa-unlock text-[10px]"></i> <?php esc_html_e( 'Not Enabled', 'frontend-dashboard' ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<!-- Card 2: PHP Extensions & Filesystem -->
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
						<div class="flex items-center gap-3 pb-4 border-b border-slate-100">
							<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
								<i class="fas fa-puzzle-piece"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'PHP Extensions & Filesystem', 'frontend-dashboard' ); ?></h3>
								<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Required modules, cryptographic libraries, and writable folders.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="space-y-3">
							<?php
							$extensions = array(
								'cURL'     => extension_loaded( 'curl' ),
								'JSON'     => extension_loaded( 'json' ),
								'OpenSSL'  => extension_loaded( 'openssl' ),
								'GD / Imagick' => ( extension_loaded( 'gd' ) || extension_loaded( 'imagick' ) ),
								'mbstring' => extension_loaded( 'mbstring' ),
								'SimpleXML'=> extension_loaded( 'simplexml' ),
							);
							foreach ( $extensions as $ext_name => $is_loaded ) :
							?>
								<div class="flex items-center justify-between py-2 border-b border-slate-50 text-xs">
									<span class="font-medium text-slate-600"><?php echo esc_html( $ext_name ); ?></span>
									<?php if ( $is_loaded ) : ?>
										<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
											<i class="fas fa-check text-[10px] mr-1"></i> <?php esc_html_e( 'Loaded', 'frontend-dashboard' ); ?>
										</span>
									<?php else : ?>
										<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
											<i class="fas fa-times text-[10px] mr-1"></i> <?php esc_html_e( 'Missing', 'frontend-dashboard' ); ?>
										</span>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>

							<div class="flex items-center justify-between py-2 text-xs">
								<span class="font-medium text-slate-600"><?php esc_html_e( 'Uploads Directory Writable', 'frontend-dashboard' ); ?></span>
								<?php if ( $uploads_writable ) : ?>
									<span class="font-bold text-emerald-600 flex items-center gap-1"><i class="fas fa-check text-[10px]"></i> <?php esc_html_e( 'Writable', 'frontend-dashboard' ); ?></span>
								<?php else : ?>
									<span class="font-bold text-rose-600 flex items-center gap-1"><i class="fas fa-exclamation-triangle text-[10px]"></i> <?php esc_html_e( 'Not Writable', 'frontend-dashboard' ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<!-- Core Modules Status Card -->
				<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
					<div class="flex items-center gap-3 pb-4 border-b border-slate-100">
						<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
							<i class="fas fa-cubes"></i>
						</div>
						<div>
							<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Frontend Dashboard Core Modules & Add-Ons', 'frontend-dashboard' ); ?></h3>
							<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Configuration state of all major features across the plugin ecosystem.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
						<!-- Module 1: Login & Registration -->
						<div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2">
							<div class="flex items-center justify-between">
								<span class="text-xs font-bold text-slate-800 flex items-center gap-1.5"><i class="fas fa-sign-in-alt text-indigo-600"></i> <?php esc_html_e( 'Login & Register', 'frontend-dashboard' ); ?></span>
								<?php if ( $login_configured ) : ?>
									<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Configured</span>
								<?php else : ?>
									<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Default</span>
								<?php endif; ?>
							</div>
							<p class="text-[11px] text-slate-500 m-0 leading-relaxed"><?php esc_html_e( 'Handles user authentication, password resets, role assignment, and WP Admin access restriction.', 'frontend-dashboard' ); ?></p>
						</div>

						<!-- Module 2: User Profile & Custom Fields -->
						<div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2">
							<div class="flex items-center justify-between">
								<span class="text-xs font-bold text-slate-800 flex items-center gap-1.5"><i class="fas fa-id-card text-indigo-600"></i> <?php esc_html_e( 'User Profile Layout', 'frontend-dashboard' ); ?></span>
								<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Active</span>
							</div>
							<p class="text-[11px] text-slate-500 m-0 leading-relaxed"><?php esc_html_e( 'Manages frontend profile editing, dynamic custom user fields, avatars, and role access permissions.', 'frontend-dashboard' ); ?></p>
						</div>

						<!-- Module 3: Posts & Custom Posts -->
						<div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/80 space-y-2">
							<div class="flex items-center justify-between">
								<span class="text-xs font-bold text-slate-800 flex items-center gap-1.5"><i class="fas fa-file-alt text-indigo-600"></i> <?php esc_html_e( 'Post & Custom Posts', 'frontend-dashboard' ); ?></span>
								<?php if ( $post_configured ) : ?>
									<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">Active</span>
								<?php else : ?>
									<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-600">Default</span>
								<?php endif; ?>
							</div>
							<p class="text-[11px] text-slate-500 m-0 leading-relaxed"><?php esc_html_e( 'Enables front-end publishing, post editing, taxonomy tagging, comments, and media management.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>

					<!-- Addons Grid -->
					<div class="pt-2">
						<h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3"><?php esc_html_e( 'Installed Add-on Plugins', 'frontend-dashboard' ); ?></h4>
						<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
							<?php foreach ( $addons as $slug => $addon_info ) : 
								$is_active = is_plugin_active( $addon_info['file'] );
							?>
								<div class="flex items-center justify-between p-3 rounded-xl border <?php echo $is_active ? 'bg-indigo-50/50 border-indigo-100' : 'bg-slate-50/60 border-slate-100'; ?>">
									<span class="text-xs font-semibold <?php echo $is_active ? 'text-slate-800' : 'text-slate-500'; ?>"><?php echo esc_html( $addon_info['name'] ); ?></span>
									<?php if ( $is_active ) : ?>
										<span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-600 text-white">Active</span>
									<?php else : ?>
										<span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-500">Not Active</span>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Tab 2: Database Tables Manager -->
			<div class="fed-status-pane hidden space-y-6" id="pane_database_tables" data-pane="database_tables">
				<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
					<!-- Top Actions Bar -->
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
						<div class="flex items-center gap-3">
							<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
								<i class="fas fa-table"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Plugin Database Tables', 'frontend-dashboard' ); ?></h3>
								<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Manage custom tables, schema integrity, and storage consumption.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="flex items-center gap-2.5 flex-wrap">
							<button type="button" id="fed_action_create_all_tables_btn" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
								<i class="fas fa-plus text-[10px]"></i>
								<span><?php esc_html_e( 'Create / Repair Schema', 'frontend-dashboard' ); ?></span>
							</button>
							<button type="button" id="fed_action_optimize_tables_btn" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
								<i class="fas fa-wrench text-[10px]"></i>
								<span><?php esc_html_e( 'Optimize & Repair All', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Tables Grid / List -->
					<div class="overflow-x-auto rounded-2xl border border-slate-200/80">
						<table class="w-full text-left border-collapse text-xs">
							<thead>
								<tr class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
									<th class="py-3 px-4"><?php esc_html_e( 'Table Name & Purpose', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4"><?php esc_html_e( 'Engine', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-center"><?php esc_html_e( 'Rows', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-center"><?php esc_html_e( 'Data Size', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-center"><?php esc_html_e( 'Total Size', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-right"><?php esc_html_e( 'Actions', 'frontend-dashboard' ); ?></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-slate-100 bg-white">
								<?php foreach ( $tables_data as $tbl_name => $tbl_info ) : ?>
									<tr class="hover:bg-slate-50/60 transition-colors">
										<td class="py-3.5 px-4 font-mono font-bold text-slate-800">
											<?php echo esc_html( $tbl_name ); ?>
											<span class="block font-sans font-normal text-[11px] text-slate-400 mt-0.5"><?php echo esc_html( $tbl_info['label'] ); ?></span>
										</td>
										<td class="py-3.5 px-4">
											<?php if ( $tbl_info['exists'] ) : ?>
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
													<i class="fas fa-check text-[9px] mr-1"></i> <?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
												</span>
											<?php else : ?>
												<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
													<i class="fas fa-exclamation-triangle text-[9px] mr-1"></i> <?php esc_html_e( 'Missing', 'frontend-dashboard' ); ?>
												</span>
											<?php endif; ?>
										</td>
										<td class="py-3.5 px-4 font-mono text-slate-600"><?php echo esc_html( $tbl_info['engine'] ); ?></td>
										<td class="py-3.5 px-4 font-mono text-center font-semibold text-slate-700"><?php echo esc_html( number_format_i18n( $tbl_info['rows'] ) ); ?></td>
										<td class="py-3.5 px-4 font-mono text-center text-slate-600"><?php echo esc_html( $tbl_info['data_size'] ); ?></td>
										<td class="py-3.5 px-4 font-mono text-center font-bold text-slate-800"><?php echo esc_html( $tbl_info['total_size'] ); ?></td>
										<td class="py-3.5 px-4 text-right">
											<div class="inline-flex items-center gap-1.5 justify-end">
												<?php if ( $tbl_info['exists'] ) : ?>
													<button type="button"
															class="fed-trigger-empty-table p-2 rounded-xl text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer"
															data-table="<?php echo esc_attr( $tbl_name ); ?>"
															title="<?php esc_attr_e( 'Empty / Truncate Table', 'frontend-dashboard' ); ?>">
														<i class="fas fa-eraser text-xs"></i>
													</button>
													<button type="button"
															class="fed-trigger-delete-table p-2 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
															data-table="<?php echo esc_attr( $tbl_name ); ?>"
															title="<?php esc_attr_e( 'Drop / Delete Table', 'frontend-dashboard' ); ?>">
														<i class="fas fa-trash-alt text-xs"></i>
													</button>
												<?php else : ?>
													<button type="button"
															class="fed-trigger-create-table px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs transition-colors cursor-pointer"
															data-table="<?php echo esc_attr( $tbl_name ); ?>">
														<i class="fas fa-plus text-[10px] mr-1"></i> <?php esc_html_e( 'Create', 'frontend-dashboard' ); ?>
													</button>
												<?php endif; ?>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr class="bg-slate-50 border-t border-slate-200/80 font-bold text-xs text-slate-800">
									<td class="py-3 px-4" colspan="3"><?php esc_html_e( 'Total Plugin Storage & Records', 'frontend-dashboard' ); ?></td>
									<td class="py-3 px-4 text-center font-mono"><?php echo esc_html( number_format_i18n( $total_db_rows ) ); ?></td>
									<td class="py-3 px-4 text-center font-mono">-</td>
									<td class="py-3 px-4 text-center font-mono"><?php echo esc_html( size_format( $total_db_size, 2 ) ); ?></td>
									<td class="py-3 px-4"></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>

			<!-- Tab 3: Options Store -->
			<div class="fed-status-pane hidden space-y-6" id="pane_plugin_options" data-pane="plugin_options">
				<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
					<!-- Header & Actions -->
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
						<div class="flex items-center gap-3">
							<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
								<i class="fas fa-sliders-h"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Frontend Dashboard Options Store', 'frontend-dashboard' ); ?></h3>
								<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Browse, inspect raw payloads, and purge individual or all plugin settings.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>

						<div class="flex items-center gap-3">
							<div class="fed-search-input-wrap">
								<span class="fed-search-icon">
									<i class="fas fa-search"></i>
								</span>
								<input type="text" id="fed_options_search_input" placeholder="<?php esc_attr_e( 'Filter options...', 'frontend-dashboard' ); ?>" class="fed-status-search-input" />
							</div>
							<button type="button" id="fed_action_delete_all_options_btn" class="px-3.5 h-[38px] rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-2xs">
								<i class="fas fa-trash-alt text-[10px]"></i>
								<span><?php esc_html_e( 'Delete All Options', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Options Table -->
					<div class="overflow-x-auto rounded-2xl border border-slate-200/80">
						<table class="w-full text-left border-collapse text-xs" id="fed_options_table">
							<thead>
								<tr class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
									<th class="py-3 px-4"><?php esc_html_e( 'Option Key', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4"><?php esc_html_e( 'Value Preview', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-center"><?php esc_html_e( 'Autoload', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-center"><?php esc_html_e( 'Size', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-right"><?php esc_html_e( 'Action', 'frontend-dashboard' ); ?></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-slate-100 bg-white">
								<?php foreach ( $options_query as $opt ) : 
									$val_len = strlen( (string) $opt->option_value );
									$is_serialized = is_serialized( $opt->option_value );
									$preview = wp_trim_words( esc_html( $opt->option_value ), 12, '...' );
								?>
									<tr class="fed-option-row hover:bg-slate-50/60 transition-colors" data-name="<?php echo esc_attr( strtolower( $opt->option_name ) ); ?>">
										<td class="py-3 px-4 font-mono font-bold text-slate-800"><?php echo esc_html( $opt->option_name ); ?></td>
										<td class="py-3 px-4 font-mono text-slate-600 max-w-xs truncate" title="<?php echo esc_attr( $opt->option_value ); ?>">
											<?php if ( $is_serialized ) : ?>
												<span class="inline-block px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-sans mr-1">Array/Object</span>
											<?php endif; ?>
											<?php echo esc_html( $preview ); ?>
										</td>
										<td class="py-3 px-4 text-center font-mono">
											<span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo 'yes' === $opt->autoload ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-500'; ?>">
												<?php echo esc_html( strtoupper( $opt->autoload ) ); ?>
											</span>
										</td>
										<td class="py-3 px-4 text-center font-mono text-slate-500"><?php echo size_format( $val_len, 2 ); ?></td>
										<td class="py-3 px-4 text-right">
											<button type="button"
													class="fed-trigger-delete-option p-2 rounded-xl text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
													data-id="<?php echo esc_attr( $opt->option_id ); ?>"
													data-name="<?php echo esc_attr( $opt->option_name ); ?>"
													title="<?php esc_attr_e( 'Delete Option', 'frontend-dashboard' ); ?>">
												<i class="fas fa-trash-alt text-xs"></i>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Tab 4: Scheduled Cron Jobs -->
			<div class="fed-status-pane hidden space-y-6" id="pane_cron_jobs" data-pane="cron_jobs">
				<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
					<div class="flex items-center gap-3 pb-5 border-b border-slate-100">
						<div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-sm">
							<i class="fas fa-clock"></i>
						</div>
						<div>
							<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Scheduled Cron Jobs & Background Tasks', 'frontend-dashboard' ); ?></h3>
							<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Inspect active WordPress cron schedules, recurrence intervals, and manually trigger tasks.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>

					<div class="overflow-x-auto rounded-2xl border border-slate-200/80">
						<table class="w-full text-left border-collapse text-xs">
							<thead>
								<tr class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
									<th class="py-3 px-4"><?php esc_html_e( 'Hook Event Name', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4"><?php esc_html_e( 'Schedule / Recurrence', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4"><?php esc_html_e( 'Next Execution', 'frontend-dashboard' ); ?></th>
									<th class="py-3 px-4 text-right"><?php esc_html_e( 'Actions', 'frontend-dashboard' ); ?></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-slate-100 bg-white">
								<?php if ( ! empty( $cron_jobs ) ) : ?>
									<?php foreach ( $cron_jobs as $cron ) : ?>
										<tr class="hover:bg-slate-50/60 transition-colors">
											<td class="py-3.5 px-4 font-mono font-bold text-slate-800">
												<?php echo esc_html( $cron['hook'] ); ?>
												<?php if ( $cron['is_fed'] ) : ?>
													<span class="inline-block ml-1.5 px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-sans font-bold">Frontend Dashboard</span>
												<?php endif; ?>
											</td>
											<td class="py-3.5 px-4 font-semibold text-slate-600"><?php echo esc_html( ucfirst( $cron['schedule'] ) ); ?></td>
											<td class="py-3.5 px-4">
												<?php if ( $cron['is_past'] ) : ?>
													<span class="font-bold text-amber-600 flex items-center gap-1"><i class="fas fa-exclamation-circle text-[10px]"></i> <?php esc_html_e( 'Due now / Overdue', 'frontend-dashboard' ); ?></span>
												<?php else : ?>
													<span class="font-mono text-slate-700"><?php echo sprintf( __( 'In %s', 'frontend-dashboard' ), $cron['diff'] ); ?></span>
												<?php endif; ?>
												<span class="block text-[10px] font-mono text-slate-400"><?php echo date_i18n( 'M j, Y H:i:s', $cron['timestamp'] ); ?></span>
											</td>
											<td class="py-3.5 px-4 text-right">
												<button type="button"
														class="fed-trigger-run-cron px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 text-slate-700 hover:text-white font-semibold text-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer"
														data-hook="<?php echo esc_attr( $cron['hook'] ); ?>">
													<i class="fas fa-play text-[10px]"></i>
													<span><?php esc_html_e( 'Run Now', 'frontend-dashboard' ); ?></span>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr>
										<td colspan="4" class="py-8 text-center text-slate-400"><?php esc_html_e( 'No active cron tasks scheduled.', 'frontend-dashboard' ); ?></td>
									</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Tab 5: File Logs & Activity Console -->
			<div class="fed-status-pane hidden space-y-6" id="pane_file_logs" data-pane="file_logs">
				<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
					<!-- Console Header & Actions -->
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
						<div class="flex items-center gap-3">
							<div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-xs">
								<i class="fas fa-terminal"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'System & Activity Log Console', 'frontend-dashboard' ); ?></h3>
								<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $log_file_size ); ?>)</p>
							</div>
						</div>

						<div class="flex items-center gap-2.5 flex-wrap">
							<button type="button" id="fed_action_refresh_log_btn" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
								<i class="fas fa-sync-alt text-[10px]"></i>
								<span><?php esc_html_e( 'Refresh', 'frontend-dashboard' ); ?></span>
							</button>
							<a href="<?php echo esc_url( plugins_url( 'log/dashboard.log', BC_FED_PLUGIN ) ); ?>" download="frontend-dashboard.log" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 no-underline shadow-2xs">
								<i class="fas fa-download text-[10px]"></i>
								<span><?php esc_html_e( 'Download', 'frontend-dashboard' ); ?></span>
							</a>
							<button type="button" id="fed_action_clear_log_btn" class="px-3.5 h-9 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer">
								<i class="fas fa-trash-alt text-[10px]"></i>
								<span><?php esc_html_e( 'Clear Log', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Dark-Mode Monospace Terminal Console -->
					<div class="rounded-2xl bg-slate-950 p-4 border border-slate-800 text-slate-200 font-mono text-xs overflow-x-auto max-h-[500px] overflow-y-auto space-y-1 shadow-inner" id="fed_log_terminal">
						<?php if ( ! empty( $log_lines ) ) : ?>
							<?php foreach ( $log_lines as $idx => $line ) : 
								$line_class = 'text-slate-300';
								if ( stripos( $line, 'error' ) !== false || stripos( $line, 'fatal' ) !== false ) {
									$line_class = 'text-rose-400 font-bold';
								} elseif ( stripos( $line, 'warn' ) !== false ) {
									$line_class = 'text-amber-300 font-semibold';
								} elseif ( stripos( $line, 'info' ) !== false || stripos( $line, 'success' ) !== false ) {
									$line_class = 'text-emerald-400';
								}
							?>
								<div class="flex items-start gap-3 hover:bg-slate-900/60 px-1 py-0.5 rounded">
									<span class="text-slate-600 select-none text-[11px] w-8 text-right shrink-0"><?php echo esc_html( $idx + 1 ); ?></span>
									<span class="<?php echo esc_attr( $line_class ); ?> whitespace-pre-wrap break-all"><?php echo esc_html( $line ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="py-12 text-center text-slate-500 font-sans">
								<i class="fas fa-check-circle text-2xl mb-2 text-slate-600"></i>
								<p class="m-0 text-xs"><?php esc_html_e( 'Log file is clean and empty. No errors or notices reported.', 'frontend-dashboard' ); ?></p>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>

			<!-- Global Status Loader Overlay -->
			<div id="fed_status_global_loader" class="fed-status-loader-wrap">
				<div class="bg-white rounded-3xl p-8 max-w-xs w-full mx-4 shadow-2xl border border-slate-100 text-center transform scale-100 flex flex-col items-center justify-center gap-4">
					<div class="relative w-12 h-12 flex items-center justify-center">
						<div class="w-12 h-12 rounded-full border-4 border-indigo-100 border-t-indigo-600 animate-spin" style="border-top-color: #4f46e5 !important;"></div>
					</div>
					<div>
						<h4 class="text-sm font-bold text-slate-800 m-0" id="fed_loader_heading"><?php esc_html_e( 'Processing...', 'frontend-dashboard' ); ?></h4>
						<p class="text-xs text-slate-500 m-0 mt-1" id="fed_loader_subtext"><?php esc_html_e( 'Please wait a moment', 'frontend-dashboard' ); ?></p>
					</div>
				</div>
			</div>

			<!-- Modal 1: Drop Table Confirmation -->
			<div id="fed_status_delete_table_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-trash-alt"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Drop Database Table?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_drop_table_desc">
						<?php esc_html_e( 'Are you sure you want to drop this table? All records and table schema will be permanently removed from MySQL.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_drop_table_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Drop Table', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 2: Empty Table Confirmation -->
			<div id="fed_status_empty_table_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-eraser"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Empty / Truncate Table?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_empty_table_desc">
						<?php esc_html_e( 'Are you sure you want to empty all records from this table? The table structure will be retained, but all data will be cleared.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_empty_table_btn" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95">
							<?php esc_html_e( 'Yes, Empty Table', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 3: Delete Option Confirmation -->
			<div id="fed_status_delete_option_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-trash-alt"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Delete Option?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_delete_option_desc">
						<?php esc_html_e( 'Are you sure you want to delete this option key from WordPress?', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_delete_option_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Delete', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 4: Delete All Options Confirmation -->
			<div id="fed_status_delete_all_options_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-radiation-alt"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-rose-600 mb-1.5">
						<?php esc_html_e( 'Delete All Options?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-600 leading-relaxed mb-6">
						<?php esc_html_e( 'WARNING: This will permanently delete ALL Frontend Dashboard settings and configuration options from WordPress database. This action cannot be reversed.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_delete_all_options_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Delete All Options', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 5: Run Cron Confirmation -->
			<div id="fed_status_run_cron_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-play"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Execute Scheduled Cron?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6">
						<?php esc_html_e( 'Are you sure you want to manually trigger the cron hook', 'frontend-dashboard' ); ?> <span id="fed_run_cron_hook_name" class="font-mono font-bold text-indigo-600"></span> <?php esc_html_e( 'now?', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_run_cron_btn" class="fed-btn-primary px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Run Cron Task', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 6: Clear Log Confirmation -->
			<div id="fed_status_clear_log_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-trash-alt"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Clear System Activity Log?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6">
						<?php esc_html_e( 'Are you sure you want to clear dashboard.log? All recorded errors and debug records will be permanently erased.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_clear_log_btn" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Clear Log', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 7: Create All Tables Confirmation -->
			<div id="fed_status_create_tables_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-tools"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Create / Repair Database Schema?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6">
						<?php esc_html_e( 'This will verify and build any missing plugin tables and update the database schema using dbDelta. Existing data will not be lost.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_create_tables_btn" class="fed-btn-primary px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Run Schema Builder', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 8: Optimize Tables Confirmation -->
			<div id="fed_status_optimize_tables_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs">
						<i class="fas fa-wrench"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5">
						<?php esc_html_e( 'Optimize & Repair All Tables?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6">
						<?php esc_html_e( 'This will perform MySQL table optimization and defragmentation on all Frontend Dashboard database tables.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-all cursor-pointer">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_optimize_tables_btn" class="fed-btn-primary px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Start Optimization', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Client-Side JavaScript -->
		<script>
		(function($) {
			'use strict';

			$(document).ready(function() {
				var $wrap = $('.fed-admin-wrap');
				var nonce = $wrap.data('nonce');
				var ajaxUrl = $wrap.data('ajax-url');
				var pendingTable = null;
				var pendingOptionId = null;
				var pendingCronHook = null;

				// Global Loader Helpers
				function showLoader(heading, subtext) {
					if (heading) {
						$('#fed_loader_heading').text(heading);
					} else {
						$('#fed_loader_heading').text('Processing...');
					}
					if (subtext) {
						$('#fed_loader_subtext').text(subtext);
					} else {
						$('#fed_loader_subtext').text('Please wait a moment');
					}
					$('#fed_status_global_loader').addClass('fed-loader-open');
					$('.preview-area').removeClass('hide');
				}

				function hideLoader() {
					$('#fed_status_global_loader').removeClass('fed-loader-open');
					$('.preview-area').addClass('hide');
				}

				// Toast Notification Helper
				function showToast(message, isError) {
					var $toast = $('#fed_toast_notification');
					var $msg = $('#fed_toast_message');
					var $icon = $('#fed_toast_icon');

					$msg.text(message);
					if (isError) {
						$icon.html('<i class="fas fa-exclamation-circle text-rose-400"></i>');
						$toast.addClass('border-rose-500/50');
					} else {
						$icon.html('<i class="fas fa-check-circle text-emerald-400"></i>');
						$toast.removeClass('border-rose-500/50');
					}

					$toast.removeClass('translate-y-16 opacity-0 pointer-events-none').addClass('translate-y-0 opacity-100');
					setTimeout(function() {
						$toast.removeClass('translate-y-0 opacity-100').addClass('translate-y-16 opacity-0 pointer-events-none');
					}, 3500);
				}

				// Tab Navigation
				function switchTab(tabKey) {
					if (!tabKey) return;
					$('.fed-main-tab-btn').removeClass('fed-tab-active');
					$('.fed-main-tab-btn[data-tab="' + tabKey + '"]').addClass('fed-tab-active');

					$('.fed-status-pane').addClass('hidden').removeClass('block');
					$('#pane_' + tabKey).removeClass('hidden').addClass('block');
				}

				$(document).on('click', '.fed-main-tab-btn', function(e) {
					e.preventDefault();
					var tabKey = $(this).data('tab');
					switchTab(tabKey);
					if (history.pushState) {
						history.pushState(null, null, '#' + tabKey);
					} else {
						location.hash = '#' + tabKey;
					}
				});

				var hash = window.location.hash ? window.location.hash.replace('#', '') : '';
				if (hash && $('#pane_' + hash).length) {
					switchTab(hash);
				}

				// Modal Helpers
				function openModal($modal) {
					if (!$modal || !$modal.length) return;
					$modal.addClass('fed-modal-open');
				}

				function closeModal($modal) {
					if (!$modal || !$modal.length) return;
					$modal.removeClass('fed-modal-open');
				}

				$('.fed-cancel-status-modal-btn').on('click', function() {
					closeModal($(this).closest('.fed-status-modal'));
				});

				// Create Missing Tables / Run Schema Modal Trigger
				$('#fed_action_create_all_tables_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_create_tables_modal'));
				});

				$('#fed_confirm_create_tables_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_create_tables_modal'));
					showLoader('Installing Schema', 'Building and verifying plugin database tables...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_create_table',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message || 'Tables installed / updated successfully.', false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error creating tables.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server communication error.', true);
						}
					});
				});

				// Single Table Create Trigger
				$(document).on('click', '.fed-trigger-create-table', function(e) {
					e.preventDefault();
					showLoader('Creating Table', 'Installing table schema in database...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_create_table',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message || 'Table created successfully.', false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error creating table.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server communication error.', true);
						}
					});
				});

				// Optimize Tables Modal Trigger
				$('#fed_action_optimize_tables_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_optimize_tables_modal'));
				});

				$('#fed_confirm_optimize_tables_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_optimize_tables_modal'));
					showLoader('Optimizing Database', 'Optimizing and defragmenting MySQL tables...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_optimize_tables',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error optimizing tables.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error during optimization.', true);
						}
					});
				});

				// Drop Table Modal Trigger
				$(document).on('click', '.fed-trigger-delete-table', function(e) {
					e.preventDefault();
					pendingTable = $(this).data('table');
					$('#fed_drop_table_desc').text('Are you sure you want to drop table "' + pendingTable + '"? All records and schema will be lost.');
					openModal($('#fed_status_delete_table_modal'));
				});

				$('#fed_confirm_drop_table_btn').on('click', function(e) {
					e.preventDefault();
					if (!pendingTable) return;
					closeModal($('#fed_status_delete_table_modal'));
					showLoader('Dropping Table', 'Deleting ' + pendingTable + ' from MySQL...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_delete_table',
							table_name: pendingTable,
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error dropping table.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error.', true);
						}
					});
				});

				// Empty Table Modal Trigger
				$(document).on('click', '.fed-trigger-empty-table', function(e) {
					e.preventDefault();
					pendingTable = $(this).data('table');
					$('#fed_empty_table_desc').text('Are you sure you want to truncate table "' + pendingTable + '"? All data records will be erased.');
					openModal($('#fed_status_empty_table_modal'));
				});

				$('#fed_confirm_empty_table_btn').on('click', function(e) {
					e.preventDefault();
					if (!pendingTable) return;
					closeModal($('#fed_status_empty_table_modal'));
					showLoader('Emptying Table', 'Erasing all records from ' + pendingTable + '...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_empty_table',
							table_name: pendingTable,
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error emptying table.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error.', true);
						}
					});
				});

				// Options Search Filter
				$('#fed_options_search_input').on('input keyup', function() {
					var q = $.trim($(this).val()).toLowerCase();
					$('.fed-option-row').each(function() {
						var name = ($(this).data('name') || '').toString().toLowerCase();
						if (!q || name.indexOf(q) !== -1) {
							$(this).removeClass('hidden');
						} else {
							$(this).addClass('hidden');
						}
					});
				});

				// Delete Option Trigger
				$(document).on('click', '.fed-trigger-delete-option', function(e) {
					e.preventDefault();
					pendingOptionId = $(this).data('id');
					var optName = $(this).data('name');
					$('#fed_delete_option_desc').text('Are you sure you want to delete option "' + optName + '"?');
					openModal($('#fed_status_delete_option_modal'));
				});

				$('#fed_confirm_delete_option_btn').on('click', function(e) {
					e.preventDefault();
					if (!pendingOptionId) return;
					closeModal($('#fed_status_delete_option_modal'));
					showLoader('Deleting Option', 'Removing option key from WordPress...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_delete_option',
							option_id: pendingOptionId,
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								$('button[data-id="' + pendingOptionId + '"]').closest('tr').fadeOut(200, function() { $(this).remove(); });
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error deleting option.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error.', true);
						}
					});
				});

				// Delete All Options Modal Trigger
				$('#fed_action_delete_all_options_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_delete_all_options_modal'));
				});

				$('#fed_confirm_delete_all_options_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_delete_all_options_modal'));
					showLoader('Deleting All Options', 'Purging plugin settings and database configuration...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_delete_all_option',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error deleting all options.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error.', true);
						}
					});
				});

				// Run Cron Hook Modal Trigger
				$(document).on('click', '.fed-trigger-run-cron', function(e) {
					e.preventDefault();
					pendingCronHook = $(this).data('hook');
					$('#fed_run_cron_hook_name').text('"' + pendingCronHook + '"');
					openModal($('#fed_status_run_cron_modal'));
				});

				$('#fed_confirm_run_cron_btn').on('click', function(e) {
					e.preventDefault();
					if (!pendingCronHook) return;
					closeModal($('#fed_status_run_cron_modal'));
					showLoader('Running Cron Task', 'Executing scheduled hook ' + pendingCronHook + '...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_run_cron',
							hook: pendingCronHook,
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error executing cron hook.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error executing cron.', true);
						}
					});
				});

				// Clear Log File Modal Trigger
				$('#fed_action_clear_log_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_clear_log_modal'));
				});

				$('#fed_confirm_clear_log_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_clear_log_modal'));
					showLoader('Clearing Log', 'Purging activity and error logs from disk...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_status_clear_log',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								$('#fed_log_terminal').html('<div class="py-12 text-center text-slate-500 font-sans"><i class="fas fa-check-circle text-2xl mb-2 text-slate-600"></i><p class="m-0 text-xs">Log file is clean and empty. No errors or notices reported.</p></div>');
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error clearing log.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error clearing log file.', true);
						}
					});
				});

				// Refresh Log Trigger
				$('#fed_action_refresh_log_btn').on('click', function(e) {
					e.preventDefault();
					showLoader('Refreshing Log', 'Reloading latest system log entries...');
					setTimeout(function() {
						location.reload();
					}, 300);
				});
			});
		})(jQuery);
		</script>
		<?php
	}
}
