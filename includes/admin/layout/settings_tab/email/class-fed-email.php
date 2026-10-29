<?php
/**
 * Email & SMTP Settings with Deliverability Logs & Queue Controls.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use FED\Services\Mail\MailDriverManager;
use FED\Services\Mail\MailMessage;
use FED\Services\Mail\MailQueueManager;

if ( ! class_exists( 'FEDEmail' ) ) {
	/**
	 * Class FEDEmail
	 */
	class FEDEmail {

		/**
		 * Settings.
		 *
		 * @var array
		 */
		private $settings;

		/**
		 * FEDEmail constructor.
		 */
		public function __construct() {
			$this->settings = get_option( 'fed_settings_email', array() );
			if ( ! is_array( $this->settings ) ) {
				$this->settings = array();
			}
		}

		/**
		 * Standalone Admin Page View (Frontend Dashboard > Email).
		 */
		public function show_page() {
			global $wpdb;

			$via        = fed_get_data( 'via', $this->settings, 'WP_MAIL' );
			$from_email = fed_get_data( 'credentials.email', $this->settings, get_option( 'admin_email' ) );
			$from_name  = fed_get_data( 'credentials.from_name', $this->settings, get_bloginfo( 'name' ) );
			$host       = fed_get_data( 'smtp.host_name', $this->settings, '' );
			$port       = fed_get_data( 'smtp.port', $this->settings, '587' );
			$username   = fed_get_data( 'smtp.user_name', $this->settings, '' );
			$password   = fed_get_data( 'smtp.password', $this->settings, '' );
			$encryption = fed_get_data( 'smtp.encryption', $this->settings, 'TLS' );
			$auth       = fed_get_data( 'smtp.auth', $this->settings, 'yes' );
			$is_queue   = 'yes' === fed_get_data( 'queue.enabled', $this->settings, 'no' );
			$is_smtp    = in_array( strtoupper( (string) $via ), array( 'SMTP' ), true );

			$ajax_url = admin_url( 'admin-ajax.php' );

			$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$total_logs_count = (int) $wpdb->get_var( "SELECT COUNT(id) FROM `$table`" );

			// Initial 20 logs for fast server-side render.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$initial_logs = $wpdb->get_results( "SELECT * FROM `$table` ORDER BY id DESC LIMIT 20", ARRAY_A );

			$driver_manager    = new MailDriverManager();
			$available_drivers = $driver_manager->getDrivers();
			?>
			<div class="wrap fed-admin-wrap" style="width: calc(100% - 20px); max-width: 100%; margin: 20px 20px 40px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; box-sizing: border-box;">
				<style>
					.fed-toggle-switch input:checked + .fed-toggle-slider {
						background-color: #4f46e5 !important;
					}
					.fed-toggle-switch input:checked + .fed-toggle-slider .fed-toggle-knob {
						transform: translateX(20px) !important;
					}
					.fed-toggle-switch:hover .fed-toggle-slider {
						filter: brightness(0.97);
					}
				</style>
				<!-- Top Header Card -->
				<div style="background: linear-gradient(135deg, #033333 0%, #0aaaaa 100%); border-radius: 16px; padding: 24px 32px; color: #ffffff; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(10, 170, 170, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
					<div style="display: flex; align-items: center; gap: 16px;">
						<div style="width: 48px; height: 48px; border-radius: 14px; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 22px; color: #ffffff; flex-shrink: 0;">
							<i class="fa fa-envelope" style="color: #ffffff !important;"></i>
						</div>
						<div>
							<div style="display: flex; align-items: center; gap: 10px;">
								<h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; line-height: 1.2;"><?php esc_html_e( 'Email & Deliverability Engine', 'frontend-dashboard' ); ?></h1>
								<span style="display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; background: rgba(255, 255, 255, 0.25); color: #ffffff; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Core Subsystem', 'frontend-dashboard' ); ?></span>
							</div>
							<p style="color: rgba(255, 255, 255, 0.9); margin: 6px 0 0 0; font-size: 13px; font-weight: 400;"><?php esc_html_e( 'Manage SMTP transport relays, monitor delivery audit logs, and configure background retries.', 'frontend-dashboard' ); ?></p>
						</div>
					</div>

					<div>
						<span style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #ffffff; color: #033333; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
							<span style="width: 8px; height: 8px; border-radius: 50%; background: <?php echo $is_smtp ? '#10b981' : '#64748b'; ?>;"></span>
							<span><?php echo $is_smtp ? esc_html__( 'SMTP Relay Active', 'frontend-dashboard' ) : esc_html__( 'WP Mail Active', 'frontend-dashboard' ); ?></span>
						</span>
					</div>
				</div>

				<!-- Vertical Subtabs Layout -->
				<div class="flex flex-col lg:flex-row gap-6 items-start w-full fed-settings-subtab-container" id="fed_email_subtabs_wrap">
					<!-- Left Vertical Subtab Sidebar -->
					<div class="w-full lg:w-64 shrink-0">
						<div class="bg-white rounded-3xl p-3 border border-slate-200/80 shadow-xs space-y-1.5" role="tablist">
							<!-- Subtab 1: Settings & Providers -->
							<button type="button"
								data-target="fed_email_tab_config"
								class="fed-email-nav-btn w-full flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-left fed-subtab-active bg-indigo-50 border border-indigo-200 text-indigo-700 shadow-2xs font-bold">
								<div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 bg-indigo-600 text-white">
									<i class="fa fa-cogs"></i>
								</div>
								<span class="truncate"><?php esc_html_e( 'Settings & Providers', 'frontend-dashboard' ); ?></span>
							</button>

							<!-- Subtab 2: Deliverability Logs -->
							<button type="button"
								data-target="fed_email_tab_logs"
								class="fed-email-nav-btn w-full flex items-center justify-between px-3.5 py-3 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-left text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent">
								<div class="flex items-center gap-3 truncate">
									<div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 bg-slate-100 text-slate-500">
										<i class="fa fa-list-alt"></i>
									</div>
									<span class="truncate"><?php esc_html_e( 'Deliverability Logs', 'frontend-dashboard' ); ?></span>
								</div>
								<span id="fed_logs_count_badge" class="ml-1.5 px-2 py-0.5 text-[10px] bg-slate-100 text-slate-700 rounded-full font-bold shrink-0">
									<?php echo (int) $total_logs_count; ?>
								</span>
							</button>

							<!-- Subtab 3: Send Test Email -->
							<button type="button"
								data-target="fed_email_tab_diagnostics"
								class="fed-email-nav-btn w-full flex items-center gap-3 px-3.5 py-3 rounded-2xl text-xs font-semibold transition-all cursor-pointer text-left text-slate-600 hover:bg-slate-50 hover:text-slate-900 border border-transparent">
								<div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 bg-slate-100 text-slate-500">
									<i class="fa fa-paper-plane"></i>
								</div>
								<span class="truncate"><?php esc_html_e( 'Send Test Email', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>

					<!-- Right Subtab Content Pane -->
					<div class="flex-1 min-w-0 w-full space-y-6">

						<!-- PANEL 1: SETTINGS & PROVIDERS -->
						<div id="fed_email_tab_config" class="fed-email-tab-panel">
							<form method="post" id="fed_email_settings_form" class="fed_ajax space-y-6" action="<?php echo esc_url( $ajax_url ); ?>">
								<input type="hidden" name="action" value="fed_ajax_request" />
								<input type="hidden" name="fed_action_hook" value="FEDEmail@update" />
								<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

								<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-7">
									<!-- Panel Header -->
									<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
										<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0">
											<i class="fa fa-cogs"></i>
										</div>
										<div>
											<h2 class="text-base font-bold text-slate-900 m-0 p-0"><?php esc_html_e( 'Email Provider & Routing Configuration', 'frontend-dashboard' ); ?></h2>
											<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Select your transport driver, authenticate credentials, and configure sender identity.', 'frontend-dashboard' ); ?></p>
										</div>
									</div>

									<!-- 1. Active Delivery Provider Cards -->
									<div>
										<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-3">
											<?php esc_html_e( 'Active Delivery Provider', 'frontend-dashboard' ); ?>
										</label>
										<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
											<?php
											foreach ( $available_drivers as $d_id => $driver ) :
												$is_active = ( strtolower( $via ) === strtolower( $d_id ) ) || ( 'WP_MAIL' === $via && 'wp_mail' === $d_id ) || ( 'SMTP' === $via && 'smtp' === $d_id );
												$icon      = ( 'smtp' === $d_id ) ? 'fa fa-server' : ( ( 'wp_mail' === $d_id ) ? 'fa fa-envelope' : 'fa fa-bolt' );
												?>
												<label class="fed-mail-method-card relative flex items-start p-4 rounded-2xl border-2 transition-all cursor-pointer select-none <?php echo $is_active ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-200 hover:border-slate-300 bg-white'; ?>">
													<input type="radio" name="via" value="<?php echo esc_attr( ( 'smtp' === $d_id ) ? 'SMTP' : ( ( 'wp_mail' === $d_id ) ? 'WP_MAIL' : $d_id ) ); ?>" class="sr-only fed-email-via-radio" <?php checked( $is_active ); ?> />
													<div class="flex items-start gap-3">
														<div class="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0 <?php echo $is_active ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500'; ?>" style="<?php echo $is_active ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : ''; ?>">
															<i class="<?php echo esc_attr( $icon ); ?>"></i>
														</div>
														<div>
															<span class="block text-xs font-bold text-slate-900"><?php echo esc_html( $driver->getTitle() ); ?></span>
															<span class="block text-[11px] text-slate-500 mt-0.5 leading-relaxed"><?php echo esc_html( $driver->getDescription() ); ?></span>
														</div>
													</div>
												</label>
											<?php endforeach; ?>
										</div>
									</div>

									<!-- 2. Sender Identity Details -->
									<div class="pt-6 border-t border-slate-100">
										<h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4"><?php esc_html_e( 'Sender Identity Details', 'frontend-dashboard' ); ?></h3>
										<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
											<div>
												<label for="fed_from_email" class="block text-xs font-semibold text-slate-700 mb-1.5">
													<?php esc_html_e( 'From Email Address', 'frontend-dashboard' ); ?>
												</label>
												<input type="email" id="fed_from_email" name="credentials[email]" value="<?php echo esc_attr( $from_email ); ?>" placeholder="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none" style="min-height: 42px !important; height: 42px !important;" />
												<p class="text-[11px] text-slate-500 mt-1"><?php esc_html_e( 'Must be an authorised, verified sender on your SMTP provider to satisfy SPF/DKIM policies.', 'frontend-dashboard' ); ?></p>
											</div>

											<div>
												<label for="fed_from_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
													<?php esc_html_e( 'From Name', 'frontend-dashboard' ); ?>
												</label>
												<input type="text" id="fed_from_name" name="credentials[from_name]" value="<?php echo esc_attr( $from_name ); ?>" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none" style="min-height: 42px !important; height: 42px !important;" />
												<p class="text-[11px] text-slate-500 mt-1"><?php esc_html_e( 'Display name seen by recipients in their email inbox.', 'frontend-dashboard' ); ?></p>
											</div>
										</div>
									</div>

									<!-- 3. Asynchronous Queue & Auto-Retry Toggle -->
									<div class="pt-6 border-t border-slate-100">
										<div class="flex items-start justify-between gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
											<div class="flex items-start gap-3">
												<div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm shrink-0">
													<i class="fa fa-layer-group"></i>
												</div>
												<div>
													<h4 class="text-xs font-bold text-slate-900 m-0"><?php esc_html_e( 'Background Queue & Exponential Auto-Retries', 'frontend-dashboard' ); ?></h4>
													<p class="text-[11px] text-slate-500 m-0 mt-0.5 leading-relaxed"><?php esc_html_e( 'When enabled, emails dispatch asynchronously via WP-Cron (< 50ms page response). Transient failures automatically retry after 2m, 10m, and 30m.', 'frontend-dashboard' ); ?></p>
												</div>
											</div>
											<label class="fed-toggle-switch" style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer; flex-shrink: 0; vertical-align: middle;">
												<input type="checkbox" name="queue[enabled]" id="fed_queue_enabled" value="yes" <?php checked( $is_queue ); ?> style="position: absolute; opacity: 0; width: 0; height: 0; margin: 0; pointer-events: none;" />
												<span class="fed-toggle-slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: <?php echo $is_queue ? '#4f46e5' : '#cbd5e1'; ?>; transition: all 0.25s ease; border-radius: 24px;">
													<span class="fed-toggle-knob" style="position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #ffffff; border-radius: 50%; transition: all 0.25s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.25); <?php echo $is_queue ? 'transform: translateX(20px);' : ''; ?>"></span>
												</span>
											</label>
										</div>
									</div>

									<!-- 4. SMTP Relay Credentials (Expandable) -->
									<div id="fed_smtp_config_container" class="<?php echo ! $is_smtp ? 'hidden' : ''; ?> pt-6 border-t border-slate-100">
										<div class="p-6 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-5">
											<div class="flex items-center gap-3">
												<div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
													<i class="fa fa-server"></i>
												</div>
												<div>
													<h4 class="text-xs font-bold text-slate-900 m-0"><?php esc_html_e( 'SMTP Server Relay Credentials', 'frontend-dashboard' ); ?></h4>
													<p class="text-[11px] text-slate-500 m-0"><?php esc_html_e( 'Configure relay host, port, authentication, and secure STARTTLS/SSL certificate transport.', 'frontend-dashboard' ); ?></p>
												</div>
											</div>

											<div class="p-3 bg-white border border-slate-200 rounded-xl flex items-center justify-between flex-wrap gap-2.5">
												<div class="flex items-center gap-2">
													<i class="fa fa-magic text-indigo-500 text-xs"></i>
													<span class="text-xs font-semibold text-slate-700"><?php esc_html_e( 'Quick SMTP Presets:', 'frontend-dashboard' ); ?></span>
												</div>
												<div class="flex items-center gap-2 flex-wrap">
													<button type="button" class="fed-smtp-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-slate-700 hover:text-indigo-600 transition-all cursor-pointer" data-host="relay.mailsenpai.com" data-port="587" data-enc="STARTTLS" data-auth="yes" data-tip="<?php echo esc_attr__( 'MailSenpai preset applied (relay.mailsenpai.com:587 STARTTLS). Get credentials from en.mailsenpai.com/smtp-api', 'frontend-dashboard' ); ?>">
														MailSenpai (EU)
													</button>
													<button type="button" class="fed-smtp-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-slate-700 hover:text-indigo-600 transition-all cursor-pointer" data-host="smtp.sendgrid.net" data-port="587" data-enc="TLS" data-auth="yes" data-tip="<?php echo esc_attr__( 'SendGrid preset applied (smtp.sendgrid.net:587 TLS).', 'frontend-dashboard' ); ?>">
														SendGrid
													</button>
													<button type="button" class="fed-smtp-preset-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 text-slate-700 hover:text-indigo-600 transition-all cursor-pointer" data-host="email-smtp.us-east-1.amazonaws.com" data-port="587" data-enc="STARTTLS" data-auth="yes" data-tip="<?php echo esc_attr__( 'Amazon SES preset applied (email-smtp.us-east-1.amazonaws.com:587 STARTTLS).', 'frontend-dashboard' ); ?>">
														Amazon SES
													</button>
												</div>
											</div>

											<div id="fed_smtp_preset_alert" class="hidden text-[11px] font-medium text-indigo-700 bg-indigo-50/80 p-2.5 rounded-lg border border-indigo-100 flex items-center gap-2">
												<i class="fa fa-check-circle text-indigo-600"></i>
												<span id="fed_smtp_preset_alert_text"></span>
											</div>

											<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
												<div class="sm:col-span-2">
													<label for="fed_smtp_host" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'SMTP Host / Server', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
													</label>
													<input type="text" id="fed_smtp_host" name="smtp[host_name]" value="<?php echo esc_attr( $host ); ?>" placeholder="relay.mailsenpai.com or smtp.sendgrid.net" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
												</div>

												<div>
													<label for="fed_smtp_port" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'SMTP Port', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
													</label>
													<input type="number" id="fed_smtp_port" name="smtp[port]" value="<?php echo esc_attr( $port ); ?>" placeholder="587" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
												</div>
											</div>

											<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
												<div>
													<label for="fed_smtp_encryption" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'Encryption Protocol', 'frontend-dashboard' ); ?>
													</label>
													<select id="fed_smtp_encryption" name="smtp[encryption]" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none" style="min-height: 42px !important; height: 42px !important;">
														<option value="TLS" <?php selected( strtoupper( $encryption ), 'TLS' ); ?>>TLS (Port 587 - Recommended)</option>
														<option value="SSL" <?php selected( strtoupper( $encryption ), 'SSL' ); ?>>SSL (Port 465)</option>
														<option value="STARTTLS" <?php selected( strtoupper( $encryption ), 'STARTTLS' ); ?>>STARTTLS (Port 587)</option>
														<option value="NONE" <?php selected( strtoupper( $encryption ), 'NONE' ); ?>>None / Plain (Port 25)</option>
													</select>
												</div>

												<div>
													<label for="fed_smtp_auth" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'SMTP Authentication', 'frontend-dashboard' ); ?>
													</label>
													<select id="fed_smtp_auth" name="smtp[auth]" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none" style="min-height: 42px !important; height: 42px !important;">
														<option value="yes" <?php selected( $auth, 'yes' ); ?>><?php esc_html_e( 'Yes (Requires Username & Password)', 'frontend-dashboard' ); ?></option>
														<option value="no" <?php selected( $auth, 'no' ); ?>><?php esc_html_e( 'No (Anonymous Relay)', 'frontend-dashboard' ); ?></option>
													</select>
												</div>
											</div>

											<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
												<div>
													<label for="fed_smtp_user" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'SMTP Username', 'frontend-dashboard' ); ?>
													</label>
													<input type="text" id="fed_smtp_user" name="smtp[user_name]" value="<?php echo esc_attr( $username ); ?>" placeholder="username or api key" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
												</div>

												<div>
													<label for="fed_smtp_pass" class="block text-xs font-semibold text-slate-700 mb-1.5">
														<?php esc_html_e( 'SMTP Password', 'frontend-dashboard' ); ?>
													</label>
													<div class="relative flex items-center">
														<input type="password" id="fed_smtp_pass" name="smtp[password]" value="<?php echo esc_attr( $password ); ?>" placeholder="••••••••••••" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="padding-right: 40px !important; min-height: 42px !important; height: 42px !important;" />
														<button type="button" id="fed_toggle_smtp_pass" class="absolute right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" style="right: 12px !important; position: absolute !important; z-index: 10 !important;" title="<?php esc_attr_e( 'Show/Hide password', 'frontend-dashboard' ); ?>">
															<i class="fa fa-eye text-xs"></i>
														</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- Action Footer -->
									<div class="pt-4 border-t border-slate-100 flex items-center justify-end">
										<button type="submit" id="fed_save_email_btn" class="fed-btn-primary px-6 py-2.5 rounded-xl font-bold text-xs inline-flex items-center gap-2 cursor-pointer transition-all shadow-xs" style="background-color: #4f46e5 !important; color: #ffffff !important;">
											<i class="fa fa-check" style="color: #ffffff !important;"></i>
											<span><?php esc_html_e( 'Save Email Settings', 'frontend-dashboard' ); ?></span>
										</button>
									</div>
								</div>
							</form>
						</div>

						<!-- PANEL 2: DELIVERABILITY LOGS -->
						<div id="fed_email_tab_logs" class="fed-email-tab-panel hidden space-y-4">
							<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-5">
								<!-- Header & Refresh -->
								<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
									<div class="flex items-center gap-3.5">
										<div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shrink-0">
											<i class="fa fa-list-alt"></i>
										</div>
										<div>
											<h2 class="text-base font-bold text-slate-900 m-0 p-0"><?php esc_html_e( 'Email Deliverability & Audit Logs', 'frontend-dashboard' ); ?></h2>
											<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Live audit trail of outbound emails, failure details, and delivery operations.', 'frontend-dashboard' ); ?></p>
										</div>
									</div>

									<button type="button" id="fed_refresh_logs_btn" class="fed-btn-secondary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2 cursor-pointer transition-all border border-slate-200 hover:bg-slate-50">
										<i class="fa fa-refresh text-slate-500"></i>
										<span><?php esc_html_e( 'Refresh Logs', 'frontend-dashboard' ); ?></span>
									</button>
								</div>

								<!-- Toolbar: Robust Responsive Filters & Actions -->
								<div class="fed-logs-toolbar" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 16px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; box-sizing: border-box; width: 100%;">
									<!-- Search & Filters -->
									<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; flex: 1 1 auto; min-width: 260px;">
										<!-- Search Input -->
										<div style="position: relative; flex: 1 1 220px; min-width: 180px;">
											<span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none; z-index: 2;">
												<i class="fa fa-search"></i>
											</span>
											<input type="text" id="fed_log_search" placeholder="<?php esc_attr_e( 'Search recipient, subject, UUID...', 'frontend-dashboard' ); ?>" style="width: 100% !important; min-width: 180px !important; height: 38px !important; min-height: 38px !important; padding: 0 12px 0 34px !important; font-size: 12px !important; border-radius: 10px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important; box-sizing: border-box !important; color: #1e293b !important; line-height: 38px !important;" />
										</div>

										<!-- Status Filter -->
										<div style="flex: 0 0 auto; width: 135px;">
											<select id="fed_log_status_filter" style="width: 100% !important; height: 38px !important; min-height: 38px !important; padding: 0 24px 0 10px !important; font-size: 12px !important; border-radius: 10px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important; color: #334155 !important; cursor: pointer !important; box-sizing: border-box !important;">
												<option value=""><?php esc_html_e( 'All Statuses', 'frontend-dashboard' ); ?></option>
												<option value="sent"><?php esc_html_e( 'Sent', 'frontend-dashboard' ); ?></option>
												<option value="failed"><?php esc_html_e( 'Failed', 'frontend-dashboard' ); ?></option>
												<option value="queued"><?php esc_html_e( 'Queued', 'frontend-dashboard' ); ?></option>
												<option value="cancelled"><?php esc_html_e( 'Cancelled', 'frontend-dashboard' ); ?></option>
											</select>
										</div>

										<!-- Provider Filter -->
										<div style="flex: 0 0 auto; width: 135px;">
											<select id="fed_log_provider_filter" style="width: 100% !important; height: 38px !important; min-height: 38px !important; padding: 0 24px 0 10px !important; font-size: 12px !important; border-radius: 10px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important; color: #334155 !important; cursor: pointer !important; box-sizing: border-box !important;">
												<option value=""><?php esc_html_e( 'All Providers', 'frontend-dashboard' ); ?></option>
												<option value="smtp"><?php esc_html_e( 'SMTP Relay', 'frontend-dashboard' ); ?></option>
												<option value="wp_mail"><?php esc_html_e( 'WP Mail', 'frontend-dashboard' ); ?></option>
											</select>
										</div>
									</div>

									<!-- Bulk Actions -->
									<div style="display: flex; align-items: center; gap: 8px; flex: 0 0 auto;">
										<select id="fed_log_bulk_action" style="width: 135px !important; height: 38px !important; min-height: 38px !important; padding: 0 24px 0 10px !important; font-size: 12px !important; border-radius: 10px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important; color: #334155 !important; cursor: pointer !important; box-sizing: border-box !important;">
											<option value=""><?php esc_html_e( 'Bulk Actions', 'frontend-dashboard' ); ?></option>
											<option value="resend"><?php esc_html_e( 'Resend Selected', 'frontend-dashboard' ); ?></option>
											<option value="delete"><?php esc_html_e( 'Delete Selected', 'frontend-dashboard' ); ?></option>
										</select>
										<button type="button" id="fed_log_bulk_apply_btn" style="height: 38px !important; min-height: 38px !important; padding: 0 16px !important; font-size: 12px !important; font-weight: 600 !important; border-radius: 10px !important; border: 1px solid #cbd5e1 !important; background-color: #ffffff !important; color: #334155 !important; cursor: pointer !important; box-sizing: border-box !important; transition: all 0.15s ease;">
											<?php esc_html_e( 'Apply', 'frontend-dashboard' ); ?>
										</button>
									</div>
								</div>

								<!-- Table Container -->
								<div class="overflow-x-auto border border-slate-200/80 rounded-2xl relative min-h-[160px]">
									<div id="fed_logs_loading_overlay" style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(2px); z-index: 40; align-items: center; justify-content: center;">
										<div style="display: inline-flex; align-items: center; gap: 10px; padding: 10px 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1); font-size: 12px; font-weight: 700; color: #4f46e5;">
											<i class="fa fa-spinner fa-spin" style="font-size: 16px;"></i>
											<span><?php esc_html_e( 'Loading logs...', 'frontend-dashboard' ); ?></span>
										</div>
									</div>

									<table class="w-full text-left text-xs text-slate-600">
										<thead class="bg-slate-50/90 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
											<tr>
												<th class="py-3 px-3.5 w-8 text-center">
													<input type="checkbox" id="fed_log_check_all" class="rounded border-slate-300 text-indigo-600 focus:ring-0 cursor-pointer" />
												</th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Date / Time', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Recipient', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Subject & Event', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Provider', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5"><?php esc_html_e( 'Attempts', 'frontend-dashboard' ); ?></th>
												<th class="py-3 px-3.5 text-right"><?php esc_html_e( 'Actions', 'frontend-dashboard' ); ?></th>
											</tr>
										</thead>
										<tbody id="fed_email_logs_tbody" class="divide-y divide-slate-100 font-medium">
											<?php
											if ( ! empty( $initial_logs ) ) {
												echo $this->render_log_rows( $initial_logs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											} else {
												?>
												<tr>
													<td colspan="8" class="p-10 text-center text-slate-400">
														<i class="fa fa-inbox text-2xl mb-1.5 text-slate-300 block"></i>
														<?php esc_html_e( 'No email delivery logs recorded yet.', 'frontend-dashboard' ); ?>
													</td>
												</tr>
												<?php
											}
											?>
										</tbody>
									</table>
								</div>

								<!-- Pagination -->
								<div class="flex items-center justify-between text-xs text-slate-500 pt-2">
									<span id="fed_logs_pagination_info">
										<?php
										/* translators: 1: start item, 2: end item, 3: total items */
										echo esc_html( sprintf( __( 'Showing %1$d to %2$d of %3$d logs', 'frontend-dashboard' ), absint( ( 0 === $total_logs_count ) ? 0 : 1 ), absint( min( 20, $total_logs_count ) ), absint( $total_logs_count ) ) );
										?>
									</span>
									<div id="fed_logs_pagination_controls" class="flex items-center gap-1.5"></div>
								</div>
							</div>
						</div>

						<!-- PANEL 3: SEND TEST EMAIL -->
						<div id="fed_email_tab_diagnostics" class="fed-email-tab-panel hidden">
							<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-6">
								<div class="flex items-center gap-3.5 pb-5 border-b border-slate-100">
									<div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center text-base shrink-0">
										<i class="fa fa-paper-plane"></i>
									</div>
									<div>
										<h2 class="text-base font-bold text-slate-900 m-0 p-0"><?php esc_html_e( 'Send a Deliverability Diagnostic Email', 'frontend-dashboard' ); ?></h2>
										<p class="text-xs text-slate-500 m-0 mt-0.5"><?php esc_html_e( 'Execute an immediate test message to verify transport handoff, headers, and inbox receipt.', 'frontend-dashboard' ); ?></p>
									</div>
								</div>

								<div class="max-w-xl space-y-4">
									<label for="fed_test_email_recipient" class="block text-xs font-semibold text-slate-700">
										<?php esc_html_e( 'Target Recipient Email Address', 'frontend-dashboard' ); ?>
									</label>
									<div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
										<div class="relative flex-1 flex items-center">
											<span class="absolute left-3.5 flex items-center pointer-events-none text-slate-400 text-xs" style="left: 14px !important; position: absolute !important; z-index: 10 !important;">
												<i class="fa fa-envelope"></i>
											</span>
											<input type="email" id="fed_test_email_recipient" placeholder="your-email@domain.com" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 transition-all outline-none" style="padding-left: 38px !important; min-height: 42px !important; height: 42px !important;" />
										</div>
										<button type="button" id="fed_send_test_email_btn" class="fed-btn-secondary px-5 py-2.5 rounded-xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer transition-all border border-slate-200 hover:bg-slate-100" style="min-height: 42px !important; height: 42px !important;">
											<i class="fa fa-paper-plane text-emerald-600"></i>
											<span><?php esc_html_e( 'Send Test Email', 'frontend-dashboard' ); ?></span>
										</button>
									</div>

									<div id="fed_test_email_feedback" class="hidden mt-3 text-xs p-4 rounded-2xl font-medium leading-relaxed"></div>
								</div>
							</div>
						</div>

					</div>
				</div>

				<!-- Enterprise Confirmation Modal -->
				<div id="fed_email_confirm_modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(3px); z-index: 999999; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;">
					<div style="background: #ffffff; border-radius: 18px; max-width: 440px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3); overflow: hidden; border: 1px solid #e2e8f0;">
						<div style="padding: 22px 24px 16px 24px; display: flex; align-items: flex-start; gap: 14px;">
							<div id="fed_confirm_icon_box" style="width: 42px; height: 42px; border-radius: 12px; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
								<i id="fed_confirm_icon" class="fa fa-redo"></i>
							</div>
							<div style="flex: 1;">
								<h3 id="fed_confirm_title" style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.3;">
									<?php esc_html_e( 'Confirm Action', 'frontend-dashboard' ); ?>
								</h3>
								<p id="fed_confirm_desc" style="margin: 6px 0 0 0; font-size: 12.5px; color: #64748b; line-height: 1.5;">
									<?php esc_html_e( 'Are you sure you want to proceed?', 'frontend-dashboard' ); ?>
								</p>
							</div>
						</div>
						<div style="padding: 14px 24px 18px 24px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; justify-content: flex-end; gap: 10px;">
							<button type="button" id="fed_confirm_btn_cancel" style="padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 600; color: #475569; background: #ffffff; border: 1px solid #cbd5e1; cursor: pointer; transition: all 0.15s ease;">
								<?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" id="fed_confirm_btn_action" style="padding: 8px 18px; border-radius: 10px; font-size: 12px; font-weight: 700; color: #ffffff; background: #4f46e5; border: 1px solid #4f46e5; cursor: pointer; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);">
								<?php esc_html_e( 'Confirm', 'frontend-dashboard' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>

			<script>
				(function($) {
					'use strict';

					var currentPaged    = 1;
					var ajaxUrl         = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
					var nonce           = <?php echo wp_json_encode( wp_create_nonce( 'fed_nonce' ) ); ?>;
					var confirmCallback = null;

					// Enterprise Notification Toast Helper
					function showNotification(message, isSuccess) {
						if (typeof fedAdminAlert !== 'undefined' && fedAdminAlert.showToast) {
							fedAdminAlert.showToast(message, !isSuccess);
						}
						var $toast = $('#fed_toast_notification');
						if (!$toast.length) {
							$('body').append(
								'<div id="fed_toast_notification" style="position: fixed; bottom: 24px; right: 24px; z-index: 99999999; display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-radius: 14px; background: #0f172a; color: #ffffff; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.3); border: 1px solid #334155; font-size: 13px; font-weight: 600; transition: all 0.25s ease; opacity: 0; transform: translateY(16px); pointer-events: none;">' +
									'<span id="fed_toast_icon" style="font-size: 16px;"></span>' +
									'<span id="fed_toast_text"></span>' +
								'</div>'
							);
							$toast = $('#fed_toast_notification');
						}
						var $icon = $toast.find('#fed_toast_icon');
						var $text = $toast.find('#fed_toast_text');
						$text.text(message);
						if (isSuccess) {
							$icon.html('<i class="fa fa-check-circle" style="color: #10b981;"></i>');
							$toast.css('border-color', '#059669');
						} else {
							$icon.html('<i class="fa fa-exclamation-circle" style="color: #ef4444;"></i>');
							$toast.css('border-color', '#dc2626');
						}
						$toast.css({ opacity: 1, transform: 'translateY(0)', pointerEvents: 'auto' });
						clearTimeout(window.fedNotificationTimer);
						window.fedNotificationTimer = setTimeout(function() {
							$toast.css({ opacity: 0, transform: 'translateY(16px)', pointerEvents: 'none' });
						}, 4000);
					}

					// Enterprise Confirmation Modal Helper
					function showConfirmModal(options, onConfirm) {
						confirmCallback = onConfirm;
						$('#fed_confirm_title').text(options.title || '<?php echo esc_js( __( 'Confirm Action', 'frontend-dashboard' ) ); ?>');
						$('#fed_confirm_desc').text(options.desc || '<?php echo esc_js( __( 'Are you sure you want to proceed?', 'frontend-dashboard' ) ); ?>');
						$('#fed_confirm_btn_action').text(options.confirmText || '<?php echo esc_js( __( 'Confirm', 'frontend-dashboard' ) ); ?>');

						var isDanger = options.type === 'danger';
						if (isDanger) {
							$('#fed_confirm_icon_box').css({ background: '#fef2f2', color: '#dc2626' });
							$('#fed_confirm_icon').attr('class', options.icon || 'fa fa-trash');
							$('#fed_confirm_btn_action').css({ background: '#dc2626', borderColor: '#dc2626', boxShadow: '0 2px 6px rgba(220, 38, 38, 0.25)' });
						} else {
							$('#fed_confirm_icon_box').css({ background: '#eef2ff', color: '#4f46e5' });
							$('#fed_confirm_icon').attr('class', options.icon || 'fa fa-redo');
							$('#fed_confirm_btn_action').css({ background: '#4f46e5', borderColor: '#4f46e5', boxShadow: '0 2px 6px rgba(79, 70, 229, 0.25)' });
						}

						$('#fed_email_confirm_modal').css('display', 'flex');
					}

					function hideConfirmModal() {
						$('#fed_email_confirm_modal').hide();
						confirmCallback = null;
					}

					$(document).on('click', '#fed_confirm_btn_cancel', function() {
						hideConfirmModal();
					});

					$(document).on('click', '#fed_confirm_btn_action', function() {
						if (typeof confirmCallback === 'function') {
							var cb = confirmCallback;
							hideConfirmModal();
							cb();
						} else {
							hideConfirmModal();
						}
					});

					function fetchLogs(page) {
						if (page) currentPaged = page;
						$('#fed_logs_loading_overlay').css('display', 'flex');

						var search = $('#fed_log_search').val();
						var status = $('#fed_log_status_filter').val();
						var provider = $('#fed_log_provider_filter').val();

						$.ajax({
							url: ajaxUrl,
							type: 'POST',
							data: {
								action: 'fed_ajax_request',
								fed_action_hook: 'FEDEmail@fetch_logs',
								fed_nonce: nonce,
								search: search,
								status: status,
								provider: provider,
								paged: currentPaged
							},
							success: function(res) {
								$('#fed_logs_loading_overlay').hide();
								if (res && res.success && res.data) {
									$('#fed_email_logs_tbody').html(res.data.rows_html);
									$('#fed_logs_count_badge').text(res.data.total_count);
									$('#fed_logs_pagination_info').text(res.data.pagination_info);
									$('#fed_logs_pagination_controls').html(res.data.pagination_controls);
									$('#fed_log_check_all').prop('checked', false);
								} else {
									$('#fed_email_logs_tbody').html('<tr><td colspan="8" class="p-8 text-center text-slate-400">Failed to load logs.</td></tr>');
								}
							},
							error: function() {
								$('#fed_logs_loading_overlay').hide();
								$('#fed_email_logs_tbody').html('<tr><td colspan="8" class="p-8 text-center text-rose-500">Network error loading logs.</td></tr>');
							}
						});
					}

					$(document).ready(function() {
						// Vertical Subtab Navigation
						$(document).on('click', '.fed-email-nav-btn', function(e) {
							e.preventDefault();
							var target = $(this).data('target');

							// Reset all buttons to inactive state
							$('.fed-email-nav-btn')
								.removeClass('fed-subtab-active bg-indigo-50 border-indigo-200 text-indigo-700 shadow-2xs font-bold')
								.addClass('text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-transparent');
							$('.fed-email-nav-btn').find('.w-8')
								.removeClass('bg-indigo-600 text-white')
								.addClass('bg-slate-100 text-slate-500');

							// Highlight clicked button
							$(this)
								.removeClass('text-slate-600 hover:bg-slate-50 hover:text-slate-900 border-transparent')
								.addClass('fed-subtab-active bg-indigo-50 border-indigo-200 text-indigo-700 shadow-2xs font-bold');
							$(this).find('.w-8')
								.removeClass('bg-slate-100 text-slate-500')
								.addClass('bg-indigo-600 text-white');

							// Switch content panels
							$('.fed-email-tab-panel').addClass('hidden');
							$('#' + target).removeClass('hidden');

							// If switching to logs and the table is empty, refresh dynamically
							if (target === 'fed_email_tab_logs') {
								var rowCount = $('#fed_email_logs_tbody tr').length;
								if (rowCount === 0) {
									fetchLogs(1);
								}
							}
						});

						// Toggle SMTP Config container based on selected driver radio
						$(document).on('change', '.fed-email-via-radio', function() {
							var selected = $(this).val();
							var $cards = $('.fed-mail-method-card');
							$cards.removeClass('border-indigo-600 bg-indigo-50/30').addClass('border-slate-200 bg-white');
							$cards.find('.w-9').removeClass('bg-indigo-600 text-white').addClass('bg-slate-100 text-slate-500');

							var $parent = $(this).closest('.fed-mail-method-card');
							$parent.removeClass('border-slate-200 bg-white').addClass('border-indigo-600 bg-indigo-50/30');
							$parent.find('.w-9').removeClass('bg-slate-100 text-slate-500').addClass('bg-indigo-600 text-white');

							if (selected === 'SMTP' || selected === 'smtp') {
								$('#fed_smtp_config_container').slideDown(200);
							} else {
								$('#fed_smtp_config_container').slideUp(200);
							}
						});

						// Toggle Password visibility
						$('#fed_toggle_smtp_pass').on('click', function() {
							var $input = $('#fed_smtp_pass');
							var $icon = $(this).find('i');
							if ($input.attr('type') === 'password') {
								$input.attr('type', 'text');
								$icon.removeClass('fa-eye').addClass('fa-eye-slash');
							} else {
								$input.attr('type', 'password');
								$icon.removeClass('fa-eye-slash').addClass('fa-eye');
							}
						});

						// Quick SMTP Preset Buttons
						$(document).on('click', '.fed-smtp-preset-btn', function(e) {
							e.preventDefault();
							var host = $(this).data('host');
							var port = $(this).data('port');
							var enc  = $(this).data('enc');
							var auth = $(this).data('auth');
							var tip  = $(this).data('tip');

							$('#fed_smtp_host').val(host);
							$('#fed_smtp_port').val(port);
							$('#fed_smtp_encryption').val(enc);
							$('#fed_smtp_auth').val(auth);

							if (tip) {
								$('#fed_smtp_preset_alert_text').text(tip);
								$('#fed_smtp_preset_alert').removeClass('hidden').hide().fadeIn(200);
							}
						});

						// Background Queue Toggle Switch
						$(document).on('change', '#fed_queue_enabled', function() {
							var checked = $(this).is(':checked');
							var $slider = $(this).siblings('.fed-toggle-slider');
							var $knob = $slider.find('.fed-toggle-knob');
							if (checked) {
								$slider.css('background-color', '#4f46e5');
								$knob.css('transform', 'translateX(20px)');
							} else {
								$slider.css('background-color', '#cbd5e1');
								$knob.css('transform', 'translateX(0px)');
							}
						});

						// Search & Filter event handlers with debounce
						var searchTimer;
						$('#fed_log_search').on('input', function() {
							clearTimeout(searchTimer);
							searchTimer = setTimeout(function() {
								fetchLogs(1);
							}, 300);
						});

						$('#fed_log_status_filter, #fed_log_provider_filter').on('change', function() {
							fetchLogs(1);
						});

						$('#fed_refresh_logs_btn').on('click', function() {
							fetchLogs();
						});

						// Check All Toggle
						$(document).on('change', '#fed_log_check_all', function() {
							var isChecked = $(this).is(':checked');
							$('.fed-log-row-check').prop('checked', isChecked);
						});

						// Pagination Click
						$(document).on('click', '.fed-log-page-btn', function(e) {
							e.preventDefault();
							var page = $(this).data('page');
							if (page) fetchLogs(page);
						});

						// Send Test Email AJAX + Live Dynamic Update
						$(document).on('click', '#fed_send_test_email_btn', function(e) {
							e.preventDefault();
							var $btn = $(this);
							var recipient = $('#fed_test_email_recipient').val().trim();
							var $feedback = $('#fed_test_email_feedback');

							if (!recipient) {
								$feedback.removeClass('hidden bg-emerald-50 text-emerald-800 border-emerald-200')
									.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
									.text('<?php echo esc_js( __( 'Please enter a recipient email address.', 'frontend-dashboard' ) ); ?>');
								return;
							}

							$btn.prop('disabled', true).addClass('opacity-75').html('<i class="fa fa-spinner fa-spin text-emerald-600"></i> <span><?php echo esc_js( __( 'Sending...', 'frontend-dashboard' ) ); ?></span>');
							$feedback.removeClass('hidden bg-rose-50 text-rose-700 border-rose-200 bg-emerald-50 text-emerald-800 border-emerald-200')
								.addClass('bg-slate-50 text-slate-600 border border-slate-200 block')
								.text('<?php echo esc_js( __( 'Executing diagnostic delivery...', 'frontend-dashboard' ) ); ?>');

							$.ajax({
								url: ajaxUrl,
								type: 'POST',
								data: {
									action: 'fed_ajax_request',
									fed_action_hook: 'FEDEmail@test_email',
									recipient: recipient,
									fed_nonce: nonce
								},
								success: function(response) {
									$btn.prop('disabled', false).removeClass('opacity-75').html('<i class="fa fa-paper-plane text-emerald-600"></i> <span><?php echo esc_js( __( 'Send Test Email', 'frontend-dashboard' ) ); ?></span>');
									if (response && response.success) {
										var okMsg = (response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Test email sent successfully! Check your inbox.', 'frontend-dashboard' ) ); ?>';
										$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-rose-50 text-rose-700 border-rose-200')
											.addClass('bg-emerald-50 text-emerald-800 border border-emerald-200 block')
											.html('<i class="fa fa-check-circle mr-1.5 text-emerald-600"></i> ' + okMsg);
										showNotification(okMsg, true);
									} else {
										var errMsg = (response && response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Failed to send test email.', 'frontend-dashboard' ) ); ?>';
										$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-800 border-emerald-200')
											.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
											.html('<i class="fa fa-exclamation-circle mr-1.5 text-rose-500"></i> ' + errMsg);
										showNotification(errMsg, false);
									}
									fetchLogs(1);
								},
								error: function() {
									$btn.prop('disabled', false).removeClass('opacity-75').html('<i class="fa fa-paper-plane text-emerald-600"></i> <span><?php echo esc_js( __( 'Send Test Email', 'frontend-dashboard' ) ); ?></span>');
									$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-800 border-emerald-200')
										.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
										.html('<i class="fa fa-exclamation-circle mr-1.5 text-rose-500"></i> <?php echo esc_js( __( 'Network connection error while dispatching test email.', 'frontend-dashboard' ) ); ?>');
									showNotification('<?php echo esc_js( __( 'Network connection error while dispatching test email.', 'frontend-dashboard' ) ); ?>', false);
									fetchLogs(1);
								}
							});
						});

						// Resend Single Email with Confirmation Modal
						$(document).on('click', '.fed-resend-email-btn', function(e) {
							e.preventDefault();
							var $btn = $(this);
							var logId = $btn.data('id');

							showConfirmModal({
								title: '<?php echo esc_js( __( 'Resend Email Delivery?', 'frontend-dashboard' ) ); ?>',
								desc: '<?php echo esc_js( __( 'An immediate delivery attempt will be queued and dispatched to this recipient. Do you want to proceed?', 'frontend-dashboard' ) ); ?>',
								confirmText: '<?php echo esc_js( __( 'Resend Email', 'frontend-dashboard' ) ); ?>',
								icon: 'fa fa-redo',
								type: 'info'
							}, function() {
								$btn.prop('disabled', true).addClass('opacity-50').html('<i class="fa fa-spinner fa-spin text-[10px]"></i>');

								$.ajax({
									url: ajaxUrl,
									type: 'POST',
									data: {
										action: 'fed_ajax_request',
										fed_action_hook: 'FEDEmail@resend_email',
										log_id: logId,
										fed_nonce: nonce
									},
									success: function(response) {
										$btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fa fa-redo text-[10px]"></i>');
										if (response && response.success) {
											var msg = (response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Email resent successfully!', 'frontend-dashboard' ) ); ?>';
											showNotification(msg, true);
											fetchLogs();
										} else {
											var err = (response && response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Failed to resend email.', 'frontend-dashboard' ) ); ?>';
											showNotification(err, false);
										}
									},
									error: function() {
										$btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fa fa-redo text-[10px]"></i>');
										showNotification('<?php echo esc_js( __( 'Failed to connect to server.', 'frontend-dashboard' ) ); ?>', false);
									}
								});
							});
						});

						// Delete Single Email with Confirmation Modal
						$(document).on('click', '.fed-delete-email-btn', function(e) {
							e.preventDefault();
							var $btn = $(this);
							var logId = $btn.data('id');

							showConfirmModal({
								title: '<?php echo esc_js( __( 'Delete Email Log Entry?', 'frontend-dashboard' ) ); ?>',
								desc: '<?php echo esc_js( __( 'This will permanently remove this email delivery log record. This action cannot be undone.', 'frontend-dashboard' ) ); ?>',
								confirmText: '<?php echo esc_js( __( 'Delete Entry', 'frontend-dashboard' ) ); ?>',
								icon: 'fa fa-trash',
								type: 'danger'
							}, function() {
								$btn.prop('disabled', true).addClass('opacity-50').html('<i class="fa fa-spinner fa-spin text-[10px]"></i>');

								$.ajax({
									url: ajaxUrl,
									type: 'POST',
									data: {
										action: 'fed_ajax_request',
										fed_action_hook: 'FEDEmail@delete_log',
										log_id: logId,
										fed_nonce: nonce
									},
									success: function(response) {
										if (response && response.success) {
											showNotification('<?php echo esc_js( __( 'Log entry was deleted.', 'frontend-dashboard' ) ); ?>', true);
											fetchLogs();
										} else {
											var err = (response && response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Failed to delete log.', 'frontend-dashboard' ) ); ?>';
											$btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fa fa-trash text-[10px]"></i>');
											showNotification(err, false);
										}
									},
									error: function() {
										$btn.prop('disabled', false).removeClass('opacity-50').html('<i class="fa fa-trash text-[10px]"></i>');
										showNotification('<?php echo esc_js( __( 'Connection error while deleting log.', 'frontend-dashboard' ) ); ?>', false);
									}
								});
							});
						});

						// Bulk Action Apply with Confirmation Modal
						$('#fed_log_bulk_apply_btn').on('click', function(e) {
							e.preventDefault();
							var action = $('#fed_log_bulk_action').val();
							if (!action) {
								showNotification('<?php echo esc_js( __( 'Please select a bulk action from the dropdown.', 'frontend-dashboard' ) ); ?>', false);
								return;
							}

							var selectedIds = [];
							$('.fed-log-row-check:checked').each(function() {
								selectedIds.push($(this).val());
							});

							if (selectedIds.length === 0) {
								showNotification('<?php echo esc_js( __( 'Please select at least one email log row.', 'frontend-dashboard' ) ); ?>', false);
								return;
							}

							var isDelete = (action === 'delete');
							var title = isDelete ? '<?php echo esc_js( __( 'Delete Selected Logs?', 'frontend-dashboard' ) ); ?>' : '<?php echo esc_js( __( 'Resend Selected Emails?', 'frontend-dashboard' ) ); ?>';
							var desc = isDelete
								? '<?php echo esc_js( __( 'Are you sure you want to permanently delete the selected email log entries? This action cannot be undone.', 'frontend-dashboard' ) ); ?>'
								: '<?php echo esc_js( __( 'Are you sure you want to re-queue delivery for the selected emails?', 'frontend-dashboard' ) ); ?>';
							var confirmText = isDelete ? '<?php echo esc_js( __( 'Delete Selected', 'frontend-dashboard' ) ); ?>' : '<?php echo esc_js( __( 'Resend Selected', 'frontend-dashboard' ) ); ?>';

							showConfirmModal({
								title: title,
								desc: desc,
								confirmText: confirmText,
								icon: isDelete ? 'fa fa-trash' : 'fa fa-redo',
								type: isDelete ? 'danger' : 'info'
							}, function() {
								var $btn = $('#fed_log_bulk_apply_btn');
								$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> <?php echo esc_js( __( 'Applying...', 'frontend-dashboard' ) ); ?>');

								$.ajax({
									url: ajaxUrl,
									type: 'POST',
									data: {
										action: 'fed_ajax_request',
										fed_action_hook: 'FEDEmail@bulk_action_logs',
										action_type: action,
										ids: selectedIds,
										fed_nonce: nonce
									},
									success: function(response) {
										$btn.prop('disabled', false).text('<?php echo esc_js( __( 'Apply', 'frontend-dashboard' ) ); ?>');
										if (response && response.success) {
											var okMsg = (response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Bulk operation completed successfully.', 'frontend-dashboard' ) ); ?>';
											showNotification(okMsg, true);
											fetchLogs();
										} else {
											var err = (response && response.data && response.data.message) ? response.data.message : '<?php echo esc_js( __( 'Bulk action failed.', 'frontend-dashboard' ) ); ?>';
											showNotification(err, false);
										}
									},
									error: function() {
										$btn.prop('disabled', false).text('<?php echo esc_js( __( 'Apply', 'frontend-dashboard' ) ); ?>');
										showNotification('<?php echo esc_js( __( 'Connection error during bulk operation.', 'frontend-dashboard' ) ); ?>', false);
									}
								});
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Helper to Render HTML Table Rows for Email Logs.
		 *
		 * @param array $rows Array of log rows.
		 * @return string HTML rows.
		 */
		public function render_log_rows( array $rows ): string {
			$html = '';

			foreach ( $rows as $row ) {
				$st        = $row['status'];
				$badge_cls = 'bg-slate-100 text-slate-700';
				if ( 'sent' === $st ) {
					$badge_cls = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
				} elseif ( 'failed' === $st ) {
					$badge_cls = 'bg-rose-50 text-rose-700 border border-rose-200';
				} elseif ( 'queued' === $st ) {
					$badge_cls = 'bg-indigo-50 text-indigo-700 border border-indigo-200';
				} elseif ( 'cancelled' === $st ) {
					$badge_cls = 'bg-amber-50 text-amber-700 border border-amber-200';
				}

				$html .= '<tr class="hover:bg-slate-50/50 transition-colors">';
				$html .= '<td class="py-3 px-3.5 text-center"><input type="checkbox" class="fed-log-row-check rounded border-slate-300 text-indigo-600 focus:ring-0 cursor-pointer" value="' . (int) $row['id'] . '" /></td>';
				$html .= '<td class="py-3 px-3.5 text-slate-500 whitespace-nowrap">' . esc_html( $row['created_at'] ) . '</td>';
				$html .= '<td class="py-3 px-3.5 font-semibold text-slate-900">' . esc_html( $row['to_email'] ) . '</td>';
				$html .= '<td class="py-3 px-3.5 max-w-xs truncate">';
				$html .= '<span class="block font-medium text-slate-800">' . esc_html( $row['subject'] ) . '</span>';
				$html .= '<span class="block text-[10px] text-slate-400">' . esc_html( $row['event_name'] ) . ' &bull; UUID: ' . esc_html( substr( $row['uuid'], 0, 8 ) ) . '...</span>';
				if ( ! empty( $row['error_message'] ) ) {
					$html .= '<span class="block text-[10px] text-rose-600 mt-0.5 truncate" title="' . esc_attr( $row['error_message'] ) . '">';
					$html .= '<i class="fa fa-exclamation-triangle mr-1"></i> ' . esc_html( $row['error_message'] );
					$html .= '</span>';
				}
				$html .= '</td>';
				$html .= '<td class="py-3 px-3.5 uppercase text-[10px] font-bold text-slate-500">' . esc_html( $row['provider'] ) . '</td>';
				$html .= '<td class="py-3 px-3.5 whitespace-nowrap"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ' . esc_attr( $badge_cls ) . '">' . esc_html( $st ) . '</span></td>';
				$html .= '<td class="py-3 px-3.5 text-slate-500 whitespace-nowrap">' . (int) $row['attempts'] . ' / ' . (int) $row['max_attempts'] . '</td>';
				$html .= '<td class="py-3 px-3.5 text-right whitespace-nowrap">';
				$html .= '<button type="button" class="fed-resend-email-btn p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors cursor-pointer mr-1" data-id="' . (int) $row['id'] . '" title="' . esc_attr__( 'Resend Email', 'frontend-dashboard' ) . '"><i class="fa fa-redo text-xs"></i></button>';
				$html .= '<button type="button" class="fed-delete-email-btn p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" data-id="' . (int) $row['id'] . '" title="' . esc_attr__( 'Delete Log', 'frontend-dashboard' ) . '"><i class="fa fa-trash text-xs"></i></button>';
				$html .= '</td>';
				$html .= '</tr>';
			}

			return $html;
		}

		/**
		 * Unified Save for Email & SMTP Settings.
		 */
		public function update() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$via        = fed_get_data( 'via', $request, 'WP_MAIL' );
			$from_email = fed_get_data( 'credentials.email', $request, '' );
			$from_name  = fed_get_data( 'credentials.from_name', $request, '' );

			if ( ! empty( $from_email ) && ! is_email( $from_email ) ) {
				wp_send_json_error( array( 'message' => __( 'Please enter a valid "From" email address.', 'frontend-dashboard' ) ) );
			}

			$this->settings['via']                      = sanitize_text_field( $via );
			$this->settings['credentials']['email']     = sanitize_email( $from_email );
			$this->settings['credentials']['from_name'] = sanitize_text_field( $from_name );
			$this->settings['queue']['enabled']         = ( isset( $request['queue']['enabled'] ) && 'yes' === $request['queue']['enabled'] ) ? 'yes' : 'no';

			if ( in_array( strtoupper( (string) $via ), array( 'SMTP' ), true ) ) {
				$host_name  = fed_get_data( 'smtp.host_name', $request, '' );
				$port       = fed_get_data( 'smtp.port', $request, '587' );
				$username   = fed_get_data( 'smtp.user_name', $request, '' );
				$password   = fed_get_data( 'smtp.password', $request, '' );
				$encryption = fed_get_data( 'smtp.encryption', $request, 'TLS' );
				$auth       = fed_get_data( 'smtp.auth', $request, 'yes' );

				if ( empty( $host_name ) ) {
					wp_send_json_error( array( 'message' => __( 'Please enter your SMTP Host / Server.', 'frontend-dashboard' ) ) );
				}
				if ( empty( $port ) ) {
					wp_send_json_error( array( 'message' => __( 'Please enter your SMTP Port (e.g. 587 or 465).', 'frontend-dashboard' ) ) );
				}

				$this->settings['smtp'] = array(
					'host_name'  => sanitize_text_field( $host_name ),
					'port'       => (int) $port,
					'user_name'  => sanitize_text_field( $username ),
					'password'   => $password, // Passwords preserved as entered.
					'encryption' => sanitize_text_field( $encryption ),
					'auth'       => ( 'no' === $auth ) ? 'no' : 'yes',
				);
			}

			update_option( 'fed_settings_email', $this->settings );

			wp_send_json_success( array( 'message' => __( 'Email and SMTP settings saved successfully.', 'frontend-dashboard' ) ) );
		}

		/**
		 * Send Test Email AJAX endpoint.
		 */
		public function test_email() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$recipient = isset( $request['recipient'] ) ? sanitize_email( $request['recipient'] ) : '';
			if ( empty( $recipient ) || ! is_email( $recipient ) ) {
				wp_send_json_error( array( 'message' => __( 'Please provide a valid recipient email address.', 'frontend-dashboard' ) ) );
			}

			/* translators: %s: site name */
			$subject = sprintf( __( '[%s] Test Email from Frontend Dashboard', 'frontend-dashboard' ), get_bloginfo( 'name' ) );
			$via     = fed_get_data( 'via', $this->settings, 'WP_MAIL' );
			/* translators: 1: site name, 2: routing method, 3: sender name, 4: sender email */
			$body = sprintf(
				__( "Hello!\n\nThis is a confirmation test email sent from Frontend Dashboard on %1\$s.\n\nRouting Method: %2\$s\nSender: %3\$s <%4\$s>\n\nIf you received this, your email configuration is working perfectly!", 'frontend-dashboard' ),
				get_bloginfo( 'name' ),
				esc_html( $via ),
				esc_html( fed_get_data( 'credentials.from_name', $this->settings, get_bloginfo( 'name' ) ) ),
				esc_html( fed_get_data( 'credentials.email', $this->settings, get_option( 'admin_email' ) ) )
			);

			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

			$message = new MailMessage(
				$recipient,
				$subject,
				$body,
				$headers,
				array(),
				'test_email',
				false
			);

			$queue_manager = new MailQueueManager();
			$result        = $queue_manager->dispatch( $message, true ); // Force sync for immediate feedback.

			if ( $result->isSuccess() ) {
				/* translators: %s: recipient email address */
				$success_msg = sprintf( __( 'Test email sent successfully to %s! Please check your inbox.', 'frontend-dashboard' ), esc_html( $recipient ) );
				wp_send_json_success( array( 'message' => $success_msg ) );
			} else {
				$msg = $result->getErrorMessage() ?? __( 'Email delivery failed. Please check your credentials and configuration.', 'frontend-dashboard' );
				wp_send_json_error( array( 'message' => $msg ) );
			}
		}

		/**
		 * AJAX Handler to Fetch Deliverability Logs with Filtering & Pagination.
		 */
		public function fetch_logs() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			global $wpdb;
			$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

			$search   = isset( $request['search'] ) ? trim( sanitize_text_field( $request['search'] ) ) : '';
			$status   = isset( $request['status'] ) ? sanitize_key( $request['status'] ) : '';
			$provider = isset( $request['provider'] ) ? sanitize_key( $request['provider'] ) : '';
			$paged    = isset( $request['paged'] ) ? max( 1, absint( $request['paged'] ) ) : 1;
			$per_page = 20;
			$offset   = ( $paged - 1 ) * $per_page;

			$where  = array( '1=1' );
			$params = array();

			if ( ! empty( $search ) ) {
				$where[]  = '(to_email LIKE %s OR subject LIKE %s OR uuid LIKE %s)';
				$like     = '%' . $wpdb->esc_like( $search ) . '%';
				$params[] = $like;
				$params[] = $like;
				$params[] = $like;
			}

			if ( ! empty( $status ) ) {
				$where[]  = 'status = %s';
				$params[] = $status;
			}

			if ( ! empty( $provider ) ) {
				$where[]  = 'provider = %s';
				$params[] = $provider;
			}

			$where_sql = implode( ' AND ', $where );

			// Total Count.
			$count_sql = "SELECT COUNT(id) FROM `$table` WHERE $where_sql";
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
			$total = ! empty( $params ) ? (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $params ) ) : (int) $wpdb->get_var( $count_sql );

			// Paginated Rows.
			$query_sql = "SELECT * FROM `$table` WHERE $where_sql ORDER BY id DESC LIMIT %d OFFSET %d";
			$q_params  = array_merge( $params, array( $per_page, $offset ) );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
			$rows = $wpdb->get_results( $wpdb->prepare( $query_sql, $q_params ), ARRAY_A );

			$html = '';
			if ( empty( $rows ) ) {
				$html .= '<tr><td colspan="8" class="p-10 text-center text-slate-400">';
				$html .= '<i class="fa fa-inbox text-2xl mb-1.5 text-slate-300 block"></i>';
				$html .= esc_html__( 'No matching email logs found.', 'frontend-dashboard' );
				$html .= '</td></tr>';
			} else {
				$html .= $this->render_log_rows( $rows );
			}

			// Pagination Info.
			$total_pages = max( 1, (int) ceil( $total / $per_page ) );
			$start_item  = ( 0 === $total ) ? 0 : $offset + 1;
			$end_item    = min( $total, $offset + count( $rows ) );
			/* translators: 1: start item, 2: end item, 3: total items */
			$pagination_info = sprintf( esc_html__( 'Showing %1$d to %2$d of %3$d logs', 'frontend-dashboard' ), $start_item, $end_item, $total );

			$controls = '';
			if ( $total_pages > 1 ) {
				if ( $paged > 1 ) {
					$controls .= '<button type="button" class="fed-log-page-btn px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer" data-page="' . ( $paged - 1 ) . '">&laquo; ' . esc_html__( 'Prev', 'frontend-dashboard' ) . '</button>';
				}
				$controls .= '<span class="px-2 py-1 text-xs font-bold text-slate-700">' . (int) $paged . ' / ' . (int) $total_pages . '</span>';
				if ( $paged < $total_pages ) {
					$controls .= '<button type="button" class="fed-log-page-btn px-2.5 py-1 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer" data-page="' . ( $paged + 1 ) . '">' . esc_html__( 'Next', 'frontend-dashboard' ) . ' &raquo;</button>';
				}
			}

			wp_send_json_success(
				array(
					'rows_html'           => $html,
					'total_count'         => $total,
					'pagination_info'     => $pagination_info,
					'pagination_controls' => $controls,
				)
			);
		}

		/**
		 * Resend Email AJAX endpoint.
		 */
		public function resend_email() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$log_id = isset( $request['log_id'] ) ? absint( $request['log_id'] ) : 0;
			if ( ! $log_id ) {
				wp_send_json_error( array( 'message' => __( 'Invalid email log identifier.', 'frontend-dashboard' ) ) );
			}

			$queue_manager = new MailQueueManager();
			$result        = $queue_manager->resend( $log_id );

			if ( $result->isSuccess() ) {
				wp_send_json_success( array( 'message' => __( 'Email was resent successfully.', 'frontend-dashboard' ) ) );
			} else {
				$msg = $result->getErrorMessage() ?? __( 'Failed to resend email.', 'frontend-dashboard' );
				wp_send_json_error( array( 'message' => $msg ) );
			}
		}

		/**
		 * Delete Single Email Log AJAX endpoint.
		 */
		public function delete_log() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$log_id = isset( $request['log_id'] ) ? absint( $request['log_id'] ) : 0;
			if ( ! $log_id ) {
				wp_send_json_error( array( 'message' => __( 'Invalid log identifier.', 'frontend-dashboard' ) ) );
			}

			global $wpdb;
			$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->delete( $table, array( 'id' => $log_id ), array( '%d' ) );

			wp_send_json_success( array( 'message' => __( 'Log entry deleted.', 'frontend-dashboard' ) ) );
		}

		/**
		 * Bulk Action Logs (Delete or Resend) AJAX endpoint.
		 */
		public function bulk_action_logs() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$action_type = isset( $request['action_type'] ) ? sanitize_key( $request['action_type'] ) : '';
			$ids         = isset( $request['ids'] ) ? array_map( 'absint', (array) $request['ids'] ) : array();

			if ( empty( $ids ) ) {
				wp_send_json_error( array( 'message' => __( 'No log items selected.', 'frontend-dashboard' ) ) );
			}

			global $wpdb;
			$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

			if ( 'delete' === $action_type ) {
				$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare
				$wpdb->query( $wpdb->prepare( "DELETE FROM `$table` WHERE id IN ($placeholders)", $ids ) );
				wp_send_json_success( array( 'message' => __( 'Selected logs deleted.', 'frontend-dashboard' ) ) );
			} elseif ( 'resend' === $action_type ) {
				$queue_manager = new MailQueueManager();
				foreach ( $ids as $id ) {
					$queue_manager->resend( $id );
				}
				wp_send_json_success( array( 'message' => __( 'Selected emails resent.', 'frontend-dashboard' ) ) );
			}

			wp_send_json_error( array( 'message' => __( 'Unknown bulk action.', 'frontend-dashboard' ) ) );
		}

		/**
		 * Sender Email Filter.
		 *
		 * @param  string $email Email.
		 * @return string
		 */
		public function sender_email( $email ) {
			$saved = fed_get_data( 'credentials.email', $this->settings );
			return ( $saved && is_email( $saved ) ) ? $saved : $email;
		}

		/**
		 * Sender Name Filter.
		 *
		 * @param  string $name Name.
		 * @return string
		 */
		public function sender_name( $name ) {
			$saved = fed_get_data( 'credentials.from_name', $this->settings );
			return ( $saved && ! empty( $saved ) ) ? $saved : $name;
		}
	}
}
