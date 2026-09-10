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
			$wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' ) => array( 'label' => 'Activity & Audit Log', 'schema' => 'BC_FED_TABLE_ACTIVITY_LOG' ),
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
		// 6. DATA GATHERING: Activity Logs & File Console
		// ----------------------------------------------------
		$activity_log_table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );
		$db_activity_logs   = array();
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$activity_log_table}'" ) === $activity_log_table ) {
			$db_activity_logs = $wpdb->get_results( "SELECT * FROM `{$activity_log_table}` ORDER BY id DESC LIMIT 500", ARRAY_A );
		}

		// Memory-efficient reader: safely extract the most recent N lines even if log file is 10MB+
		if ( ! function_exists( 'fed_tail_file' ) ) {
			function fed_tail_file( $filepath, $lines = 200 ) {
				if ( ! file_exists( $filepath ) || ! is_readable( $filepath ) ) {
					return array();
				}
				$filesize = filesize( $filepath );
				if ( $filesize === 0 ) {
					return array();
				}

				// Small files (< 512KB): read directly
				if ( $filesize < 512 * 1024 ) {
					$raw = file_get_contents( $filepath );
					if ( empty( $raw ) ) {
						return array();
					}
					$all = explode( "\n", trim( $raw ) );
					return array_slice( $all, -$lines );
				}

				// Large files: seek backward in chunks to avoid memory spikes
				$handle = fopen( $filepath, 'rb' );
				if ( ! $handle ) {
					return array();
				}

				$buffer    = '';
				$chunkSize = 8192;
				$pos       = $filesize;
				$lineCount = 0;

				while ( $pos > 0 && $lineCount <= $lines ) {
					$readSize = min( $chunkSize, $pos );
					$pos     -= $readSize;
					fseek( $handle, $pos );
					$chunk     = fread( $handle, $readSize );
					$buffer    = $chunk . $buffer;
					$lineCount = substr_count( $buffer, "\n" );
				}
				fclose( $handle );

				$all = explode( "\n", trim( $buffer ) );
				return array_slice( $all, -$lines );
			}
		}

		$log_lines = fed_tail_file( $log_file, 200 );
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
			.status-modal-content button {
				font-family: inherit !important;
				cursor: pointer !important;
			}
			.fed-cancel-status-modal-btn {
				background-color: #ffffff !important;
				color: #334155 !important;
				border: 1px solid #cbd5e1 !important;
			}
			.fed-cancel-status-modal-btn:hover {
				background-color: #f1f5f9 !important;
				color: #0f172a !important;
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
						<i class="fas fa-tools" style="color: #ffffff !important;"></i>
					</div>
					<div>
						<div class="flex items-center gap-2.5 flex-wrap">
							<h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight m-0 p-0">
								<?php esc_html_e( 'Tools & Maintenance', 'frontend-dashboard' ); ?>
							</h1>
							<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
								<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
								<?php esc_html_e( 'System Operational', 'frontend-dashboard' ); ?>
							</span>
						</div>
						<p class="text-xs text-slate-500 m-0 mt-1 font-medium">
							<?php esc_html_e( 'Health diagnostics, database utilities, options store, scheduled crons, activity logs, and environment seeder.', 'frontend-dashboard' ); ?>
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
				<a href="#database" data-tab="database" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-database text-xs"></i>
					<span><?php esc_html_e( 'Database', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#scheduled_crons" data-tab="scheduled_crons" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-clock text-xs"></i>
					<span><?php esc_html_e( 'Scheduled Crons', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#activity_log" data-tab="activity_log" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-terminal text-xs"></i>
					<span><?php esc_html_e( 'Activity Log', 'frontend-dashboard' ); ?></span>
				</a>
				<a href="#seeder" data-tab="seeder" role="tab" class="fed-main-tab-btn">
					<i class="fas fa-seedling text-xs"></i>
					<span><?php esc_html_e( 'Seeder', 'frontend-dashboard' ); ?></span>
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

			<!-- Tab 2: Database Operations (Tables & Option Store) -->
			<div class="fed-status-pane hidden space-y-6" id="pane_database" data-pane="database">
				<!-- Sub-navigation Pills for Database Category -->
				<div class="flex items-center justify-between bg-white rounded-2xl p-2 border border-slate-200/80 shadow-xs">
					<div class="flex items-center gap-2" role="tablist">
						<button type="button" class="fed-sub-tab-btn fed-subtab-active px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer bg-indigo-50 text-indigo-700 border border-indigo-200" data-subtab="database_tables">
							<i class="fas fa-table mr-1.5"></i> <?php esc_html_e( 'Database Tables', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" class="fed-sub-tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all cursor-pointer border border-transparent" data-subtab="plugin_options">
							<i class="fas fa-sliders-h mr-1.5"></i> <?php esc_html_e( 'Option Store', 'frontend-dashboard' ); ?>
						</button>
					</div>
					<span class="text-[11px] text-slate-400 font-medium hidden sm:inline px-3">
						<?php esc_html_e( 'Manage MySQL tables and WordPress wp_options settings', 'frontend-dashboard' ); ?>
					</span>
				</div>

				<!-- Sub-Pane: Database Tables -->
				<div class="fed-sub-pane block space-y-6" id="subpane_database_tables">
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

				<!-- Sub-Pane: Option Store -->
				<div class="fed-sub-pane hidden space-y-6" id="subpane_plugin_options">
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
			</div>

			<!-- Tab 3: Scheduled Cron Jobs -->
			<div class="fed-status-pane hidden space-y-6" id="pane_scheduled_crons" data-pane="scheduled_crons">
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

			<!-- Tab 4: Activity Log (DB Log vs File Log) -->
			<div class="fed-status-pane hidden space-y-6" id="pane_activity_log" data-pane="activity_log">
				
				<!-- Sub-navigation Pills for Activity Log Category -->
				<div class="flex items-center justify-between bg-white rounded-2xl p-2 border border-slate-200/80 shadow-xs">
					<div class="flex items-center gap-2" role="tablist">
						<button type="button" class="fed-sub-tab-btn fed-subtab-active px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer bg-indigo-50 text-indigo-700 border border-indigo-200" data-subtab="activity_db_log">
							<i class="fas fa-database mr-1.5"></i> <?php esc_html_e( 'DB Log', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" class="fed-sub-tab-btn px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all cursor-pointer border border-transparent" data-subtab="activity_file_log">
							<i class="fas fa-file-code mr-1.5"></i> <?php esc_html_e( 'File Log', 'frontend-dashboard' ); ?>
						</button>
					</div>
					<span class="text-[11px] text-slate-400 font-medium hidden sm:inline px-3">
						<?php esc_html_e( 'Switch between database audit logs and raw dashboard.log file', 'frontend-dashboard' ); ?>
					</span>
				</div>

				<!-- Sub-Pane 1: DB Log (Database Table) -->
				<div class="fed-sub-pane block space-y-6" id="subpane_activity_db_log">
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
						<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-100">
							<div class="flex items-center gap-3">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
									<i class="fas fa-history" style="color: #4f46e5 !important;"></i>
								</div>
								<div>
									<div class="flex items-center gap-2">
										<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Activity & Audit Log (Database)', 'frontend-dashboard' ); ?></h3>
										<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200" id="fed_activity_count_badge">
											<?php echo count( $db_activity_logs ); ?> <?php esc_html_e( 'events', 'frontend-dashboard' ); ?>
										</span>
									</div>
									<p class="text-[11px] text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Real-time audit trail of administrative operations, seeder executions, schema migrations, and system tasks in MySQL.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>

							<!-- Filter & Action Controls -->
							<div class="flex items-center gap-2.5 flex-wrap">
								<!-- Search Input -->
								<div class="fed-search-input-wrap">
									<i class="fas fa-search fed-search-icon"></i>
									<input type="text" id="fed_activity_search_input" placeholder="<?php esc_attr_e( 'Search audit logs...', 'frontend-dashboard' ); ?>" class="fed-status-search-input text-xs" style="width: 200px !important; min-width: 180px !important;" />
								</div>

								<!-- Category Filter Dropdown -->
								<select id="fed_activity_filter_select" class="h-[38px] px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold focus:bg-white focus:border-indigo-500 focus:outline-none transition-all cursor-pointer">
									<option value=""><?php esc_html_e( 'All Categories', 'frontend-dashboard' ); ?></option>
									<option value="seeder"><?php esc_html_e( 'Seeder / Purge', 'frontend-dashboard' ); ?></option>
									<option value="database"><?php esc_html_e( 'Database Tables', 'frontend-dashboard' ); ?></option>
									<option value="option"><?php esc_html_e( 'Options Store', 'frontend-dashboard' ); ?></option>
									<option value="cron"><?php esc_html_e( 'Crons', 'frontend-dashboard' ); ?></option>
									<option value="log"><?php esc_html_e( 'System Logs', 'frontend-dashboard' ); ?></option>
								</select>

								<!-- Refresh Button -->
								<button type="button" id="fed_action_refresh_activity_btn" class="fed-btn-secondary h-[38px] px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
									<i class="fas fa-sync-alt text-[10px]"></i>
									<span><?php esc_html_e( 'Refresh', 'frontend-dashboard' ); ?></span>
								</button>

								<!-- Clear Database Log Button -->
								<button type="button" id="fed_action_clear_activity_btn" class="h-[38px] px-3.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer">
									<i class="fas fa-trash-alt text-[10px]"></i>
									<span><?php esc_html_e( 'Clear Audit History', 'frontend-dashboard' ); ?></span>
								</button>
							</div>
						</div>

						<!-- Real-Time Activity Log Table -->
						<div class="overflow-x-auto rounded-2xl border border-slate-200/80 max-h-[600px] overflow-y-auto">
							<table class="w-full text-left border-collapse">
								<thead class="sticky top-0 z-10 shadow-xs">
									<tr>
										<th class="w-48"><?php esc_html_e( 'User / Actor', 'frontend-dashboard' ); ?></th>
										<th class="w-32"><?php esc_html_e( 'Category', 'frontend-dashboard' ); ?></th>
										<th><?php esc_html_e( 'Action & Description', 'frontend-dashboard' ); ?></th>
										<th class="w-28 text-center"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
										<th class="w-40 text-right"><?php esc_html_e( 'Timestamp', 'frontend-dashboard' ); ?></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-slate-100" id="fed_activity_log_tbody">
									<?php if ( ! empty( $db_activity_logs ) ) : ?>
										<?php foreach ( $db_activity_logs as $act ) : 
											$action_cat   = ! empty( $act['channel'] ) ? $act['channel'] : ( ! empty( $act['action_type'] ) ? $act['action_type'] : 'system' );
											$act_status   = ! empty( $act['status'] ) ? $act['status'] : ( ! empty( $act['level'] ) ? $act['level'] : 'info' );
											$action_title = ! empty( $act['action'] ) ? $act['action'] : ( ! empty( $act['action_title'] ) ? $act['action_title'] : __( 'System Event', 'frontend-dashboard' ) );
											$act_desc     = ! empty( $act['description'] ) ? $act['description'] : ( ! empty( $act['message'] ) && $act['message'] !== $action_title ? $act['message'] : '' );
											$search_str   = strtolower( ( $act['user_login'] ?? '' ) . ' ' . ( $act['user_display_name'] ?? '' ) . ' ' . $action_title . ' ' . $act_desc . ' ' . ( $act['ip_address'] ?? '' ) . ' ' . $action_cat );
											
											// Category Badge Style
											$cat_badge_cls = 'bg-slate-100 text-slate-700 border-slate-200';
											$cat_icon = 'fa-cog';
											if ( $action_cat === 'seeder' ) {
												$cat_badge_cls = 'bg-indigo-50 text-indigo-700 border-indigo-200';
												$cat_icon = 'fa-seedling';
											} elseif ( $action_cat === 'database' ) {
												$cat_badge_cls = 'bg-amber-50 text-amber-700 border-amber-200';
												$cat_icon = 'fa-database';
											} elseif ( $action_cat === 'option' ) {
												$cat_badge_cls = 'bg-sky-50 text-sky-700 border-sky-200';
												$cat_icon = 'fa-sliders-h';
											} elseif ( $action_cat === 'cron' ) {
												$cat_badge_cls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
												$cat_icon = 'fa-clock';
											} elseif ( $action_cat === 'log' ) {
												$cat_badge_cls = 'bg-purple-50 text-purple-700 border-purple-200';
												$cat_icon = 'fa-terminal';
											}

											// Status Badge Style
											$status_badge_cls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
											$status_icon = 'fa-check-circle text-emerald-500';
											if ( $act_status === 'warning' ) {
												$status_badge_cls = 'bg-amber-50 text-amber-700 border-amber-200';
												$status_icon = 'fa-exclamation-triangle text-amber-500';
											} elseif ( in_array( $act_status, [ 'error', 'critical' ], true ) ) {
												$status_badge_cls = 'bg-rose-50 text-rose-700 border-rose-200';
												$status_icon = 'fa-times-circle text-rose-500';
											}

											$time_unix = ! empty( $act['created_at'] ) ? strtotime( $act['created_at'] ) : time();
											$time_diff = human_time_diff( $time_unix, time() ) . ' ' . __( 'ago', 'frontend-dashboard' );
											$time_exact = date_i18n( 'M j, Y H:i:s', $time_unix );
										?>
											<tr class="fed-activity-row hover:bg-slate-50 transition-colors" data-category="<?php echo esc_attr( $action_cat ); ?>" data-search="<?php echo esc_attr( $search_str ); ?>">
												<!-- User / Actor Column -->
												<td>
													<div class="flex items-center gap-2.5">
														<div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-xs text-slate-600 font-bold shrink-0 overflow-hidden">
															<?php if ( ! empty( $act['user_id'] ) ) : ?>
																<?php echo get_avatar( $act['user_id'], 32, '', '', array( 'class' => 'w-full h-full object-cover' ) ); ?>
															<?php else : ?>
																<i class="fas fa-user-shield text-indigo-500 text-xs"></i>
															<?php endif; ?>
														</div>
														<div class="min-w-0">
															<span class="block font-bold text-slate-800 truncate text-xs">
																<?php echo esc_html( ! empty( $act['user_display_name'] ) ? $act['user_display_name'] : ( $act['user_login'] ?? 'System' ) ); ?>
															</span>
															<span class="block text-[10px] text-slate-400 font-mono">
																<?php echo esc_html( ! empty( $act['ip_address'] ) ? $act['ip_address'] : '127.0.0.1' ); ?>
															</span>
														</div>
													</div>
												</td>

												<!-- Category Column -->
												<td>
													<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border <?php echo esc_attr( $cat_badge_cls ); ?>">
														<i class="fas <?php echo esc_attr( $cat_icon ); ?> text-[9px]"></i>
														<span><?php echo esc_html( ucfirst( $action_cat ) ); ?></span>
													</span>
												</td>

												<!-- Action Title & Description Column -->
												<td>
													<div class="space-y-1">
														<span class="font-bold text-slate-900 text-xs block">
															<?php echo esc_html( $action_title ); ?>
														</span>
														<?php if ( ! empty( $act_desc ) ) : ?>
															<div class="text-[11px] text-slate-600 leading-relaxed whitespace-pre-wrap font-sans bg-slate-50/70 p-2 rounded-xl border border-slate-100">
																<?php echo esc_html( $act_desc ); ?>
															</div>
														<?php endif; ?>
													</div>
												</td>

												<!-- Status Column -->
												<td class="text-center">
													<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo esc_attr( $status_badge_cls ); ?>">
														<i class="fas <?php echo esc_attr( $status_icon ); ?> text-[9px]"></i>
														<span><?php echo esc_html( ucfirst( $act_status ) ); ?></span>
													</span>
												</td>

												<!-- Timestamp Column -->
												<td class="text-right">
													<span class="font-bold text-slate-700 text-xs block">
														<?php echo esc_html( $time_diff ); ?>
													</span>
													<span class="text-[10px] text-slate-400 font-mono block" title="<?php echo esc_attr( $time_exact ); ?>">
														<?php echo esc_html( $time_exact ); ?>
													</span>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php else : ?>
										<tr id="fed_activity_empty_row">
											<td colspan="5" class="py-12 text-center text-slate-400">
												<i class="fas fa-inbox text-3xl mb-2 text-slate-300 block"></i>
												<span class="text-xs font-medium"><?php esc_html_e( 'No activity records found in the database. Perform any action to generate logs.', 'frontend-dashboard' ); ?></span>
											</td>
										</tr>
									<?php endif; ?>
									<tr id="fed_activity_no_search_results_row" class="hidden">
										<td colspan="5" class="py-12 text-center text-slate-400">
											<i class="fas fa-search text-3xl mb-2 text-slate-300 block"></i>
											<span class="text-xs font-medium"><?php esc_html_e( 'No activity records matched your search query or filter.', 'frontend-dashboard' ); ?></span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<!-- Pagination Toolbar for DB Activity Log -->
						<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100" id="fed_activity_pagination_bar">
							<div class="flex items-center gap-3 text-xs text-slate-500 flex-wrap">
								<span id="fed_activity_pagination_info"><?php esc_html_e( 'Showing entries...', 'frontend-dashboard' ); ?></span>
								<div class="flex items-center gap-1.5 pl-3 border-l border-slate-200">
									<label for="fed_activity_per_page" class="text-slate-400 font-medium text-[11px]"><?php esc_html_e( 'Per page:', 'frontend-dashboard' ); ?></label>
									<select id="fed_activity_per_page" class="h-7 px-2 py-0.5 rounded-lg border border-slate-200 bg-slate-50 text-slate-700 text-xs font-semibold focus:outline-none focus:bg-white cursor-pointer">
										<option value="15">15</option>
										<option value="25" selected>25</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>
								</div>
							</div>

							<div class="flex items-center gap-1" id="fed_activity_pagination_controls">
								<button type="button" class="fed-page-nav-btn h-8 px-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition-all" id="fed_activity_prev_btn" title="<?php esc_attr_e( 'Previous Page', 'frontend-dashboard' ); ?>">
									<i class="fas fa-chevron-left text-[10px]"></i>
								</button>
								<div class="flex items-center gap-1" id="fed_activity_page_numbers">
									<!-- Dynamically injected page numbers -->
								</div>
								<button type="button" class="fed-page-nav-btn h-8 px-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition-all" id="fed_activity_next_btn" title="<?php esc_attr_e( 'Next Page', 'frontend-dashboard' ); ?>">
									<i class="fas fa-chevron-right text-[10px]"></i>
								</button>
							</div>
						</div>
					</div>
				</div>

				<!-- Sub-Pane 2: File Log (Raw Monospace File Log Console dashboard.log) -->
				<div class="fed-sub-pane hidden space-y-6" id="subpane_activity_file_log">
					<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-4">
						<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
							<div class="flex items-center gap-3">
								<div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-sm shadow-xs">
									<i class="fas fa-terminal"></i>
								</div>
								<div>
									<div class="flex items-center gap-2 flex-wrap">
										<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Raw File Log Console', 'frontend-dashboard' ); ?></h3>
										<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
											<i class="fas fa-eye text-[9px] mr-1 text-indigo-600"></i> <?php echo sprintf( esc_html__( 'Showing latest %d lines', 'frontend-dashboard' ), count( $log_lines ) ); ?>
										</span>
										<?php if ( file_exists( $log_file ) && filesize( $log_file ) > 1024 * 1024 ) : ?>
											<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
												<i class="fas fa-info-circle text-[9px] mr-1"></i> <?php esc_html_e( 'Large file (>1MB) • Download for complete history', 'frontend-dashboard' ); ?>
											</span>
										<?php endif; ?>
									</div>
									<p class="text-[11px] text-slate-500 m-0 mt-0.5 font-mono"><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $log_file_size ); ?>)</p>
								</div>
							</div>

							<div class="flex items-center gap-2.5 flex-wrap">
								<button type="button" id="fed_action_refresh_log_btn" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 cursor-pointer shadow-2xs">
									<i class="fas fa-sync-alt text-[10px]"></i>
									<span><?php esc_html_e( 'Refresh File', 'frontend-dashboard' ); ?></span>
								</button>
								<a href="<?php echo esc_url( plugins_url( 'log/dashboard.log', BC_FED_PLUGIN ) ); ?>" download="frontend-dashboard.log" class="fed-btn-secondary h-9 px-3.5 rounded-xl font-semibold text-xs inline-flex items-center gap-1.5 no-underline shadow-2xs">
									<i class="fas fa-download text-[10px]"></i>
									<span><?php esc_html_e( 'Download', 'frontend-dashboard' ); ?></span>
								</a>
								<button type="button" id="fed_action_clear_log_btn" class="px-3.5 h-9 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer">
									<i class="fas fa-trash-alt text-[10px]"></i>
									<span><?php esc_html_e( 'Clear File Log', 'frontend-dashboard' ); ?></span>
								</button>
							</div>
						</div>

						<!-- Dark-Mode Monospace Terminal Console -->
						<div class="rounded-2xl bg-slate-950 p-4 border border-slate-800 text-slate-200 font-mono text-xs overflow-x-auto max-h-[480px] overflow-y-auto space-y-1 shadow-inner" id="fed_log_terminal">
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
								<div class="py-8 text-center text-slate-500 font-sans">
									<i class="fas fa-check-circle text-2xl mb-2 text-slate-600"></i>
									<p class="m-0 text-xs"><?php esc_html_e( 'Log file is clean and empty. No errors or notices reported.', 'frontend-dashboard' ); ?></p>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

			<!-- Tab 5: Seeder & Demo Engine -->
			<div class="fed-status-pane hidden space-y-6" id="pane_seeder" data-pane="seeder">
				
				<!-- Primary Recommended Hero: Complete 1-Click Bootstrap Suite -->
				<div class="rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%) !important; color: #ffffff !important; border: 1px solid #312e81 !important;">
					<div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
						<div class="space-y-3 max-w-2xl">
							<div class="flex items-center gap-2">
								<span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold" style="background-color: rgba(99, 102, 241, 0.25) !important; color: #c7d2fe !important; border: 1px solid rgba(129, 140, 248, 0.35) !important;">
									<i class="fas fa-magic text-[10px] mr-1.5 text-amber-400"></i> <?php esc_html_e( 'Recommended Setup', 'frontend-dashboard' ); ?>
								</span>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold" style="background-color: rgba(16, 185, 129, 0.2) !important; color: #6ee7b7 !important; border: 1px solid rgba(52, 211, 153, 0.3) !important;">
									<?php esc_html_e( '1-Click Complete', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<h2 class="text-xl sm:text-2xl font-extrabold tracking-tight m-0" style="color: #ffffff !important;">
								<?php esc_html_e( 'Complete 1-Click Bootstrap Suite', 'frontend-dashboard' ); ?>
							</h2>
							<p class="text-xs sm:text-sm leading-relaxed m-0" style="color: #c7d2fe !important;">
								<?php esc_html_e( 'Initialize your entire Frontend Dashboard in a single action. Automatically repairs database tables, creates essential frontend pages with canvas templates & shortcodes, binds authentication routes, populates standard navigation menus, and bootstraps default user profile fields.', 'frontend-dashboard' ); ?>
							</p>

							<!-- Key Inclusions Checklist -->
							<div class="pt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs" style="color: #e0e7ff !important;">
								<div class="flex items-center gap-2">
									<i class="fas fa-check-circle text-emerald-400 text-xs shrink-0"></i>
									<span style="color: #e0e7ff !important;"><?php esc_html_e( 'Core Pages (/dashboard, /login, /register, /forgot-password)', 'frontend-dashboard' ); ?></span>
								</div>
								<div class="flex items-center gap-2">
									<i class="fas fa-check-circle text-emerald-400 text-xs shrink-0"></i>
									<span style="color: #e0e7ff !important;"><?php esc_html_e( 'Auto-bind to Dashboard Login Settings', 'frontend-dashboard' ); ?></span>
								</div>
								<div class="flex items-center gap-2">
									<i class="fas fa-check-circle text-emerald-400 text-xs shrink-0"></i>
									<span style="color: #e0e7ff !important;"><?php esc_html_e( 'Standard Navigation Tabs & Menu Order', 'frontend-dashboard' ); ?></span>
								</div>
								<div class="flex items-center gap-2">
									<i class="fas fa-check-circle text-emerald-400 text-xs shrink-0"></i>
									<span style="color: #e0e7ff !important;"><?php esc_html_e( 'Default User Profile Metadata Fields', 'frontend-dashboard' ); ?></span>
								</div>
							</div>
						</div>

						<div class="lg:shrink-0 flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end justify-center gap-3">
							<!-- 1-Click Suite Execution -->
							<button type="button" id="fed_action_seed_all_btn" class="px-6 h-12 rounded-2xl font-extrabold text-xs inline-flex items-center justify-center gap-2.5 cursor-pointer shadow-lg shadow-indigo-900/50 hover:shadow-indigo-800/80 active:scale-95 transition-all text-nowrap" style="background-color: #6366f1 !important; color: #ffffff !important;">
								<i class="fas fa-bolt text-amber-300 text-sm"></i>
								<span class="text-sm tracking-wide" style="color: #ffffff !important;"><?php esc_html_e( 'Run Complete Suite', 'frontend-dashboard' ); ?></span>
							</button>

							<!-- 1-Click Purge / Reset Button -->
							<button type="button" id="fed_action_purge_all_btn" class="px-5 h-11 rounded-2xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer transition-all border active:scale-95 text-nowrap hover:bg-rose-900/30" style="background-color: rgba(244, 63, 94, 0.15) !important; color: #fecdd3 !important; border-color: rgba(244, 63, 94, 0.4) !important;">
								<i class="fas fa-trash-alt text-rose-400 text-xs"></i>
								<span style="color: #fecdd3 !important;"><?php esc_html_e( 'Purge / Reset Seeded Data', 'frontend-dashboard' ); ?></span>
							</button>

							<span class="text-[11px] text-center lg:text-right" style="color: #94a3b8 !important;">
								<?php esc_html_e( 'Audit recorded in Activity Log table', 'frontend-dashboard' ); ?>
							</span>
						</div>
					</div>
				</div>

				<!-- Section Divider & Title: Individual Module Seeders -->
				<div class="pt-2">
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 pb-3 border-b border-slate-200/80">
						<div>
							<h3 class="text-sm font-bold text-slate-900 m-0">
								<i class="fas fa-cubes text-indigo-600 mr-1.5"></i>
								<?php esc_html_e( 'Individual Module Seeders', 'frontend-dashboard' ); ?>
							</h3>
							<p class="text-[11px] text-slate-500 m-0 mt-0.5">
								<?php esc_html_e( 'Run individual seeders if you only want to install or repair specific modules.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>
				</div>

				<!-- 3-Column Grid for Individual Modules -->
				<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
					
					<!-- Card 1: Core Pages & Login Settings -->
					<div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-5">
						<div class="space-y-3.5">
							<div class="flex items-center justify-between">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base">
									<i class="fas fa-file-invoice"></i>
								</div>
								<?php if ( $login_configured ) : ?>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
										<i class="fas fa-check text-[9px] mr-1"></i> <?php esc_html_e( 'Configured', 'frontend-dashboard' ); ?>
									</span>
								<?php else : ?>
									<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
										<i class="fas fa-exclamation text-[9px] mr-1"></i> <?php esc_html_e( 'Action Needed', 'frontend-dashboard' ); ?>
									</span>
								<?php endif; ?>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Core Pages & Login Settings', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-1 leading-relaxed">
									<?php esc_html_e( 'Creates essential pages with canvas templates and binds them automatically to Dashboard Settings > Login > Settings.', 'frontend-dashboard' ); ?>
								</p>
							</div>
							<div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs">
								<div class="flex items-center justify-between">
									<span class="text-slate-500 font-mono text-[11px]">/dashboard/</span>
									<code class="text-[10px] font-mono text-indigo-600 bg-indigo-50/70 px-1 py-0.5 rounded">[fed_dashboard]</code>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-slate-500 font-mono text-[11px]">/login/</span>
									<code class="text-[10px] font-mono text-indigo-600 bg-indigo-50/70 px-1 py-0.5 rounded">[fed_login]</code>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-slate-500 font-mono text-[11px]">/register/</span>
									<code class="text-[10px] font-mono text-indigo-600 bg-indigo-50/70 px-1 py-0.5 rounded">[fed_register_only]</code>
								</div>
								<div class="flex items-center justify-between">
									<span class="text-slate-500 font-mono text-[11px]">/forgot-password/</span>
									<code class="text-[10px] font-mono text-indigo-600 bg-indigo-50/70 px-1 py-0.5 rounded">[fed_forgot_password_only]</code>
								</div>
							</div>
						</div>
						<div class="pt-2">
							<button type="button" id="fed_action_seed_pages_btn" class="w-full fed-btn-primary h-10 rounded-2xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95 transition-all" style="background-color: #4f46e5 !important; color: #ffffff !important;">
								<i class="fas fa-file-medical text-xs" style="color: #ffffff !important;"></i>
								<span style="color: #ffffff !important;"><?php esc_html_e( 'Seed Pages & Map Settings', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Card 2: Default Navigation Menus -->
					<div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-5">
						<div class="space-y-3.5">
							<div class="flex items-center justify-between">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base">
									<i class="fas fa-bars"></i>
								</div>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
									<?php esc_html_e( 'Menu Engine', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Standard Navigation Menus', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-1 leading-relaxed">
									<?php esc_html_e( 'Populates default dashboard navigation tabs in the database table if not already present, ensuring full sidebar visibility for user roles.', 'frontend-dashboard' ); ?>
								</p>
							</div>
							<div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs font-mono text-slate-700">
								<div class="flex items-center justify-between">
									<span class="font-sans text-slate-500 font-medium text-[11px]">Dashboard</span>
									<span class="text-indigo-600 font-bold text-[11px]">#1 (Overview)</span>
								</div>
								<div class="flex items-center justify-between">
									<span class="font-sans text-slate-500 font-medium text-[11px]">Profile</span>
									<span class="text-indigo-600 font-bold text-[11px]">#2 (User Edit)</span>
								</div>
								<div class="flex items-center justify-between">
									<span class="font-sans text-slate-500 font-medium text-[11px]">Posts</span>
									<span class="text-indigo-600 font-bold text-[11px]">#3 (Submissions)</span>
								</div>
								<div class="flex items-center justify-between">
									<span class="font-sans text-slate-500 font-medium text-[11px]">Payments</span>
									<span class="text-indigo-600 font-bold text-[11px]">#4 (Transactions)</span>
								</div>
							</div>
						</div>
						<div class="pt-2">
							<button type="button" id="fed_action_seed_menus_btn" class="w-full fed-btn-primary h-10 rounded-2xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95 transition-all" style="background-color: #4f46e5 !important; color: #ffffff !important;">
								<i class="fas fa-sitemap text-xs" style="color: #ffffff !important;"></i>
								<span style="color: #ffffff !important;"><?php esc_html_e( 'Seed Standard Menus', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Card 3: Standard Profile Fields -->
					<div class="bg-white rounded-3xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-5">
						<div class="space-y-3.5">
							<div class="flex items-center justify-between">
								<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base">
									<i class="fas fa-user-edit"></i>
								</div>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
									<?php esc_html_e( 'Profile Schema', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'User Profile Fields & Meta', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0 mt-1 leading-relaxed">
									<?php esc_html_e( 'Initializes default user profile fields (First Name, Last Name, Nickname, Email, Website, Bio, Password) in the profile metadata table.', 'frontend-dashboard' ); ?>
								</p>
							</div>
							<div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-600 leading-relaxed">
								<?php esc_html_e( 'Standard inputs configured with optimal positions, validation rules, and role permissions for all registered subscribers and administrators.', 'frontend-dashboard' ); ?>
							</div>
						</div>
						<div class="pt-2">
							<button type="button" id="fed_action_seed_profile_fields_btn" class="w-full fed-btn-primary h-10 rounded-2xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-95 transition-all" style="background-color: #4f46e5 !important; color: #ffffff !important;">
								<i class="fas fa-user-plus text-xs" style="color: #ffffff !important;"></i>
								<span style="color: #ffffff !important;"><?php esc_html_e( 'Seed Profile Fields', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
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
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-trash-alt" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Drop Database Table?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_drop_table_desc" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to drop this table? All records and table schema will be permanently removed from MySQL.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_drop_table_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Drop Table', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 2: Empty Table Confirmation -->
			<div id="fed_status_empty_table_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fffbeb !important; color: #d97706 !important;">
						<i class="fas fa-eraser" style="color: #d97706 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Empty / Truncate Table?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_empty_table_desc" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to empty all records from this table? The table structure will be retained, but all data will be cleared.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_empty_table_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #d97706 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Empty Table', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 3: Delete Option Confirmation -->
			<div id="fed_status_delete_option_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-trash-alt" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Delete Option?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" id="fed_delete_option_desc" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to delete this option key from WordPress?', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_delete_option_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Delete', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 4: Delete All Options Confirmation -->
			<div id="fed_status_delete_all_options_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-radiation-alt" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold mb-1.5" style="color: #e11d48 !important;">
						<?php esc_html_e( 'Delete All Options?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'WARNING: This will permanently delete ALL Frontend Dashboard settings and configuration options from WordPress database. This action cannot be reversed.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_delete_all_options_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Delete All Options', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 5: Run Cron Confirmation -->
			<div id="fed_status_run_cron_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-play" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Execute Scheduled Cron?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to manually trigger the cron hook', 'frontend-dashboard' ); ?> <span id="fed_run_cron_hook_name" class="font-mono font-bold" style="color: #4f46e5 !important;"></span> <?php esc_html_e( 'now?', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_run_cron_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Run Cron Task', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 6: Clear Log Confirmation -->
			<div id="fed_status_clear_log_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-trash-alt" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Clear System Activity Log?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to clear dashboard.log? All recorded errors and debug records will be permanently erased.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_clear_log_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Yes, Clear Log', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 7: Create All Tables Confirmation -->
			<div id="fed_status_create_tables_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-tools" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Create / Repair Database Schema?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will verify and build any missing plugin tables and update the database schema using dbDelta. Existing data will not be lost.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_create_tables_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Run Schema Builder', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 8: Optimize Tables Confirmation -->
			<div id="fed_status_optimize_tables_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-wrench" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Optimize & Repair All Tables?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will perform MySQL table optimization and defragmentation on all Frontend Dashboard database tables.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_optimize_tables_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Start Optimization', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 9: Seed Pages Confirmation -->
			<div id="fed_status_seed_pages_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-file-invoice" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Seed Core Pages & Login Settings?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will create or verify essential pages (/dashboard/, /login/, /register/, /forgot-password/) and automatically assign their IDs into Dashboard Settings > Login > Settings.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_seed_pages_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Seed & Map Pages', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 10: Seed Menus Confirmation -->
			<div id="fed_status_seed_menus_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-bars" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Seed Standard Menus?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will populate the standard frontend dashboard sidebar navigation items (Dashboard, Profile, Posts, Payments, Logout) in the database table.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_seed_menus_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Seed Menus', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 11: Seed Profile Fields Confirmation -->
			<div id="fed_status_seed_profile_fields_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #eef2ff !important; color: #4f46e5 !important;">
						<i class="fas fa-user-edit" style="color: #4f46e5 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Seed Profile Fields Schema?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will initialize the standard profile field metadata schema (First Name, Last Name, Email, Bio, etc.) for frontend user editing.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_seed_profile_fields_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<?php esc_html_e( 'Seed Profile Fields', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 12: Run Full Suite Confirmation -->
			<div id="fed_status_seed_all_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #1e1b4b !important; color: #fbbf24 !important; border: 1px solid #312e81 !important;">
						<i class="fas fa-bolt" style="color: #fbbf24 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Run Complete Bootstrap Suite?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will execute the entire setup: repair tables, create & map core pages to login settings, populate navigation menus, and seed profile fields.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_seed_all_btn" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<i class="fas fa-bolt text-amber-300 text-xs mr-1" style="color: #fde047 !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Run Complete Suite', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 13: Purge / Reset Seeded Data Confirmation -->
			<div id="fed_status_purge_all_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-trash-alt" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Purge / Reset Seeded Data?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'This will permanently delete the seeded pages (/dashboard, /login, /register, /forgot-password), unbind login page mappings from settings, and remove standard navigation menu items. This action is logged to the Activity Log.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_purge_all_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<i class="fas fa-trash-alt text-xs mr-1" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Yes, Purge Seeded Data', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
				</div>
			</div>

			<!-- Modal 14: Clear Activity Log History Confirmation -->
			<div id="fed_status_clear_activity_modal" class="fed-status-modal">
				<div class="status-modal-content">
					<div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4 shadow-xs" style="background-color: #fff1f2 !important; color: #e11d48 !important;">
						<i class="fas fa-history" style="color: #e11d48 !important;"></i>
					</div>
					<h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1.5" style="color: #0f172a !important;">
						<?php esc_html_e( 'Clear Audit & Activity History?', 'frontend-dashboard' ); ?>
					</h3>
					<p class="text-xs text-slate-500 leading-relaxed mb-6" style="color: #64748b !important;">
						<?php esc_html_e( 'Are you sure you want to truncate the activity log table? All audit trail entries recorded in MySQL will be permanently removed.', 'frontend-dashboard' ); ?>
					</p>
					<div class="flex items-center justify-center gap-3">
						<button type="button" class="fed-cancel-status-modal-btn px-5 py-2.5 rounded-xl text-xs sm:text-sm font-semibold transition-all cursor-pointer" style="background-color: #ffffff !important; color: #334155 !important; border: 1px solid #cbd5e1 !important;">
							<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
						</button>
						<button type="button" id="fed_confirm_clear_activity_btn" class="px-5 py-2.5 rounded-xl text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer shadow-sm active:scale-95" style="background-color: #e11d48 !important; color: #ffffff !important;">
							<i class="fas fa-trash-alt text-xs mr-1" style="color: #ffffff !important;"></i>
							<span style="color: #ffffff !important;"><?php esc_html_e( 'Yes, Clear Audit Log', 'frontend-dashboard' ); ?></span>
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

				// Main Tab Navigation
				function switchTab(tabKey) {
					if (!tabKey) return;
					
					// Normalize tab aliases
					if (tabKey === 'cron_jobs') tabKey = 'scheduled_crons';
					if (tabKey === 'file_logs' || tabKey === 'activity_file_log') {
						tabKey = 'activity_log';
						switchSubTab('activity_file_log');
					} else if (tabKey === 'activity_db_log') {
						tabKey = 'activity_log';
						switchSubTab('activity_db_log');
					} else if (tabKey === 'database_tables' || tabKey === 'plugin_options') {
						var subKey = tabKey;
						tabKey = 'database';
						switchSubTab(subKey);
					}

					$('.fed-main-tab-btn').removeClass('fed-tab-active');
					$('.fed-main-tab-btn[data-tab="' + tabKey + '"]').addClass('fed-tab-active');

					$('.fed-status-pane').addClass('hidden').removeClass('block');
					$('#pane_' + tabKey).removeClass('hidden').addClass('block');
				}

				// Sub-Tab Navigation (Scoped to active parent tab)
				function switchSubTab(subKey) {
					if (!subKey) return;
					var $btn = $('.fed-sub-tab-btn[data-subtab="' + subKey + '"]');
					var $bar = $btn.closest('[role="tablist"]');
					if ($bar.length) {
						$bar.find('.fed-sub-tab-btn').removeClass('fed-subtab-active bg-indigo-50 text-indigo-700 border-indigo-200').addClass('text-slate-600 border-transparent');
						$btn.addClass('fed-subtab-active bg-indigo-50 text-indigo-700 border-indigo-200').removeClass('text-slate-600 border-transparent');
					}

					var $subpane = $('#subpane_' + subKey);
					if ($subpane.length) {
						var $parentPane = $subpane.closest('.fed-status-pane');
						if ($parentPane.length) {
							$parentPane.find('.fed-sub-pane').addClass('hidden').removeClass('block');
							$subpane.removeClass('hidden').addClass('block');
						}
					}
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

				$(document).on('click', '.fed-sub-tab-btn', function(e) {
					e.preventDefault();
					var subKey = $(this).data('subtab');
					switchSubTab(subKey);
					if (history.pushState) {
						history.pushState(null, null, '#' + subKey);
					} else {
						location.hash = '#' + subKey;
					}
				});

				var hash = window.location.hash ? window.location.hash.replace('#', '') : '';
				if (hash) {
					if ($('#pane_' + hash).length) {
						switchTab(hash);
					} else if (hash === 'database_tables' || hash === 'plugin_options') {
						switchTab('database');
						switchSubTab(hash);
					} else if (hash === 'activity_db_log' || hash === 'activity_file_log') {
						switchTab('activity_log');
						switchSubTab(hash);
					} else if (hash === 'cron_jobs') {
						switchTab('scheduled_crons');
					} else if (hash === 'file_logs') {
						switchTab('activity_log');
						switchSubTab('activity_file_log');
					}
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

				// ==========================================
				// SEEDER ACTIONS & TRIGGERS
				// ==========================================
				
				// 1. Seed Core Pages Trigger
				$('#fed_action_seed_pages_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_seed_pages_modal'));
				});

				$('#fed_confirm_seed_pages_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_seed_pages_modal'));
					showLoader('Bootstrapping Core Pages', 'Creating /dashboard/, /login/, /register/, /forgot-password/ and updating Settings...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_seed_pages',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error seeding pages.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error creating pages.', true);
						}
					});
				});

				// 2. Seed Menus Trigger
				$('#fed_action_seed_menus_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_seed_menus_modal'));
				});

				$('#fed_confirm_seed_menus_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_seed_menus_modal'));
					showLoader('Seeding Navigation Menus', 'Populating standard dashboard sidebar menu tabs...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_seed_menus',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error seeding menus.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error seeding menus.', true);
						}
					});
				});

				// 3. Seed Profile Fields Trigger
				$('#fed_action_seed_profile_fields_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_seed_profile_fields_modal'));
				});

				$('#fed_confirm_seed_profile_fields_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_seed_profile_fields_modal'));
					showLoader('Seeding Profile Schema', 'Initializing default user profile fields...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_seed_profile_fields',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error seeding profile fields.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error seeding profile fields.', true);
						}
					});
				});

				// 4. Seed All 1-Click Suite Trigger
				$('#fed_action_seed_all_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_seed_all_modal'));
				});

				$('#fed_confirm_seed_all_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_seed_all_modal'));
					showLoader('Running Bootstrap Suite', 'Executing full installation: DB tables, core pages, login mapping, menus, and profile schema...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_seed_all',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 800);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error running bootstrap suite.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error running bootstrap suite.', true);
						}
					});
				});

				// 5. Purge / Reset Seeded Data Trigger
				$('#fed_action_purge_all_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_purge_all_modal'));
				});

				$('#fed_confirm_purge_all_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_purge_all_modal'));
					showLoader('Purging Seeded Data', 'Deleting core pages, unbinding login routes, and removing default menus...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_purge_all',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 800);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error purging seeded data.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error during purge operation.', true);
						}
					});
				});

				// ==========================================
				// ACTIVITY LOG AUDIT ACTIONS & PAGINATION
				// ==========================================

				var activityCurrentPage = 1;
				var activityPageSize    = parseInt($('#fed_activity_per_page').val(), 10) || 25;
				var matchingRows        = [];

				function renderActivityPagination() {
					var q   = $.trim($('#fed_activity_search_input').val()).toLowerCase();
					var cat = $.trim($('#fed_activity_filter_select').val()).toLowerCase();
					matchingRows = [];

					// 1. Identify all matching rows based on search & category
					$('.fed-activity-row').each(function() {
						var rowSearch = ($(this).data('search') || '').toString().toLowerCase();
						var rowCat    = ($(this).data('category') || '').toString().toLowerCase();

						var matchSearch = (!q || rowSearch.indexOf(q) !== -1);
						var matchCat    = (!cat || rowCat === cat);

						if (matchSearch && matchCat) {
							matchingRows.push($(this));
						} else {
							$(this).addClass('hidden');
						}
					});

					var totalMatching = matchingRows.length;
					var totalPages    = Math.ceil(totalMatching / activityPageSize) || 1;

					if (activityCurrentPage > totalPages) {
						activityCurrentPage = totalPages;
					}
					if (activityCurrentPage < 1) {
						activityCurrentPage = 1;
					}

					var startIndex = (activityCurrentPage - 1) * activityPageSize;
					var endIndex   = Math.min(startIndex + activityPageSize, totalMatching);

					// 2. Hide all rows first, then reveal only current page slice
					$('.fed-activity-row').addClass('hidden');
					for (var i = startIndex; i < endIndex; i++) {
						matchingRows[i].removeClass('hidden');
					}

					// 3. Show/hide empty search state
					if (totalMatching === 0 && $('.fed-activity-row').length > 0) {
						$('#fed_activity_no_search_results_row').removeClass('hidden');
						$('#fed_activity_pagination_bar').addClass('hidden');
					} else {
						$('#fed_activity_no_search_results_row').addClass('hidden');
						$('#fed_activity_pagination_bar').removeClass('hidden');
					}

					// 4. Update info label
					if (totalMatching > 0) {
						$('#fed_activity_pagination_info').text('Showing ' + (startIndex + 1) + ' to ' + endIndex + ' of ' + totalMatching + ' entries');
					} else {
						$('#fed_activity_pagination_info').text('Showing 0 entries');
					}

					// 5. Update prev/next button states
					$('#fed_activity_prev_btn').prop('disabled', activityCurrentPage <= 1);
					$('#fed_activity_next_btn').prop('disabled', activityCurrentPage >= totalPages);

					// 6. Build page number buttons (smart window around current page)
					var $numWrap = $('#fed_activity_page_numbers');
					$numWrap.empty();

					if (totalPages > 1) {
						var startPage = Math.max(1, activityCurrentPage - 2);
						var endPage   = Math.min(totalPages, activityCurrentPage + 2);

						if (startPage > 1) {
							$numWrap.append('<button type="button" class="fed-page-btn h-8 w-8 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 cursor-pointer" data-page="1">1</button>');
							if (startPage > 2) {
								$numWrap.append('<span class="text-slate-400 text-xs px-1">...</span>');
							}
						}

						for (var p = startPage; p <= endPage; p++) {
							var activeCls = (p === activityCurrentPage) ? 'bg-indigo-600 text-white font-bold border-indigo-600 shadow-xs' : 'bg-white text-slate-700 font-semibold border-slate-200 hover:bg-slate-50';
							$numWrap.append('<button type="button" class="fed-page-btn h-8 w-8 rounded-xl border ' + activeCls + ' text-xs cursor-pointer" data-page="' + p + '">' + p + '</button>');
						}

						if (endPage < totalPages) {
							if (endPage < totalPages - 1) {
								$numWrap.append('<span class="text-slate-400 text-xs px-1">...</span>');
							}
							$numWrap.append('<button type="button" class="fed-page-btn h-8 w-8 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50 cursor-pointer" data-page="' + totalPages + '">' + totalPages + '</button>');
						}
					}
				}

				// Initial pagination render
				renderActivityPagination();

				$('#fed_activity_search_input').on('input keyup', function() {
					activityCurrentPage = 1;
					renderActivityPagination();
				});

				$('#fed_activity_filter_select').on('change', function() {
					activityCurrentPage = 1;
					renderActivityPagination();
				});

				$('#fed_activity_per_page').on('change', function() {
					activityPageSize    = parseInt($(this).val(), 10) || 25;
					activityCurrentPage = 1;
					renderActivityPagination();
				});

				$(document).on('click', '#fed_activity_prev_btn', function(e) {
					e.preventDefault();
					if (activityCurrentPage > 1) {
						activityCurrentPage--;
						renderActivityPagination();
					}
				});

				$(document).on('click', '#fed_activity_next_btn', function(e) {
					e.preventDefault();
					var totalPages = Math.ceil(matchingRows.length / activityPageSize) || 1;
					if (activityCurrentPage < totalPages) {
						activityCurrentPage++;
						renderActivityPagination();
					}
				});

				$(document).on('click', '.fed-page-btn', function(e) {
					e.preventDefault();
					var p = parseInt($(this).data('page'), 10);
					if (p && p !== activityCurrentPage) {
						activityCurrentPage = p;
						renderActivityPagination();
					}
				});

				// Refresh Activity Log Trigger
				$('#fed_action_refresh_activity_btn').on('click', function(e) {
					e.preventDefault();
					showLoader('Refreshing Audit Trail', 'Loading recent database activity logs...');
					setTimeout(function() {
						location.reload();
					}, 300);
				});

				// Clear Database Activity Log Trigger
				$('#fed_action_clear_activity_btn').on('click', function(e) {
					e.preventDefault();
					openModal($('#fed_status_clear_activity_modal'));
				});

				$('#fed_confirm_clear_activity_btn').on('click', function(e) {
					e.preventDefault();
					closeModal($('#fed_status_clear_activity_modal'));
					showLoader('Clearing Activity History', 'Truncating activity log database table...');

					$.ajax({
						type: 'POST',
						url: ajaxUrl,
						data: {
							action: 'fed_tools_clear_activity_log',
							fed_nonce: nonce
						},
						success: function(res) {
							hideLoader();
							if (res && res.success) {
								showToast(res.data.message, false);
								setTimeout(function() { location.reload(); }, 700);
							} else {
								showToast(res.data && res.data.message ? res.data.message : 'Error clearing activity log.', true);
							}
						},
						error: function() {
							hideLoader();
							showToast('Server error clearing activity log.', true);
						}
					});
				});

			});
		})(jQuery);
		</script>
		<?php
	}
}
