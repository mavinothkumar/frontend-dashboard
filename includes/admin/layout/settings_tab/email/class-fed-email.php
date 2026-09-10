<?php
/**
 * Email & SMTP Settings.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		 * Main render method for the Email Tab.
		 */
		public function show() {
			$via         = fed_get_data( 'via', $this->settings, 'WP_MAIL' );
			$from_email  = fed_get_data( 'credentials.email', $this->settings, get_option( 'admin_email' ) );
			$from_name   = fed_get_data( 'credentials.from_name', $this->settings, get_bloginfo( 'name' ) );
			$host        = fed_get_data( 'smtp.host_name', $this->settings, '' );
			$port        = fed_get_data( 'smtp.port', $this->settings, '587' );
			$username    = fed_get_data( 'smtp.user_name', $this->settings, '' );
			$password    = fed_get_data( 'smtp.password', $this->settings, '' );
			$encryption  = fed_get_data( 'smtp.encryption', $this->settings, 'TLS' );
			$auth        = fed_get_data( 'smtp.auth', $this->settings, 'yes' );
			$is_smtp     = ( 'SMTP' === $via );
			$ajax_action = esc_url( fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDEmail@update' );
			$test_action = esc_url( fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDEmail@test_email' );
			?>
			<div class="bc_fed max-w-4xl mx-auto space-y-6">
				<!-- Settings Form -->
				<form method="post" id="fed_email_settings_form" class="fed_ajax space-y-6" action="<?php echo $ajax_action; ?>">
					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<!-- Main Settings Card -->
					<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
						<!-- Header -->
						<div class="px-6 py-5 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
							<div class="flex items-center gap-3.5">
								<div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
									<i class="fas fa-envelope text-sm" style="color: #ffffff !important;"></i>
								</div>
								<div>
									<h2 class="text-base font-bold text-slate-900 m-0 p-0"><?php esc_html_e( 'Email & SMTP Configuration', 'frontend-dashboard' ); ?></h2>
									<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium"><?php esc_html_e( 'Manage how Frontend Dashboard sends outgoing notification emails.', 'frontend-dashboard' ); ?></p>
								</div>
							</div>
							<div class="flex items-center gap-2">
								<span id="fed_email_mode_badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo $is_smtp ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-700 border border-slate-200'; ?>">
									<span class="w-2 h-2 rounded-full <?php echo $is_smtp ? 'bg-indigo-500' : 'bg-slate-400'; ?>"></span>
									<span id="fed_email_mode_badge_text"><?php echo $is_smtp ? esc_html__( 'SMTP Active', 'frontend-dashboard' ) : esc_html__( 'WP Mail Active', 'frontend-dashboard' ); ?></span>
								</span>
							</div>
						</div>

						<div class="p-6 sm:p-8 space-y-7">
							<!-- 1. Send Email Routing Method -->
							<div>
								<label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-3">
									<?php esc_html_e( 'Send Email Via', 'frontend-dashboard' ); ?>
								</label>
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
									<!-- WP_MAIL Option Card -->
									<label class="fed-mail-method-card relative flex items-start p-4 rounded-xl border-2 transition-all cursor-pointer select-none <?php echo ! $is_smtp ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-200 hover:border-slate-300 bg-white'; ?>">
										<input type="radio" name="via" value="WP_MAIL" class="sr-only fed-email-via-radio" <?php checked( $via, 'WP_MAIL' ); ?> />
										<div class="flex items-center gap-3">
											<div class="w-9 h-9 rounded-lg flex items-center justify-center text-base <?php echo ! $is_smtp ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500'; ?>" style="<?php echo ! $is_smtp ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : ''; ?>">
												<i class="fab fa-wordpress-simple"></i>
											</div>
											<div>
												<span class="block text-xs font-bold text-slate-900"><?php esc_html_e( 'Default WordPress Mail', 'frontend-dashboard' ); ?></span>
												<span class="block text-[11px] text-slate-500 mt-0.5"><?php esc_html_e( 'Standard wp_mail() function without external credentials.', 'frontend-dashboard' ); ?></span>
											</div>
										</div>
									</label>

									<!-- SMTP Option Card -->
									<label class="fed-mail-method-card relative flex items-start p-4 rounded-xl border-2 transition-all cursor-pointer select-none <?php echo $is_smtp ? 'border-indigo-600 bg-indigo-50/30' : 'border-slate-200 hover:border-slate-300 bg-white'; ?>">
										<input type="radio" name="via" value="SMTP" class="sr-only fed-email-via-radio" <?php checked( $via, 'SMTP' ); ?> />
										<div class="flex items-center gap-3">
											<div class="w-9 h-9 rounded-lg flex items-center justify-center text-base <?php echo $is_smtp ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-500'; ?>" style="<?php echo $is_smtp ? 'background-color: #4f46e5 !important; color: #ffffff !important;' : ''; ?>">
												<i class="fas fa-server"></i>
											</div>
											<div>
												<span class="block text-xs font-bold text-slate-900"><?php esc_html_e( 'Custom SMTP Server', 'frontend-dashboard' ); ?></span>
												<span class="block text-[11px] text-slate-500 mt-0.5"><?php esc_html_e( 'Authenticate via external SMTP provider for higher deliverability.', 'frontend-dashboard' ); ?></span>
											</div>
										</div>
									</label>
								</div>
							</div>

							<!-- 2. Sender Identity Details -->
							<div class="pt-6 border-t border-slate-100">
								<h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-4"><?php esc_html_e( 'Sender Identity Details', 'frontend-dashboard' ); ?></h3>
								<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
									<!-- From Email Address -->
									<div>
										<label for="fed_email_from_address" class="block text-xs font-semibold text-slate-700 mb-1.5">
											<?php esc_html_e( 'From Email Address', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
										</label>
										<div class="relative flex items-center">
											<span class="absolute left-3.5 flex items-center pointer-events-none text-slate-400 text-xs" style="left: 14px !important; position: absolute !important; z-index: 10 !important;">
												<i class="fas fa-at"></i>
											</span>
											<input type="email" id="fed_email_from_address" name="credentials[email]" value="<?php echo esc_attr( $from_email ); ?>" placeholder="notifications@example.com" class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 transition-all outline-none" style="padding-left: 38px !important; min-height: 42px !important; height: 42px !important;" required />
										</div>
										<p class="text-[11px] text-slate-400 mt-1"><?php esc_html_e( 'Outgoing sender email displayed to recipients in their inbox.', 'frontend-dashboard' ); ?></p>
									</div>

									<!-- From Name -->
									<div>
										<label for="fed_email_from_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
											<?php esc_html_e( 'From Sender Name', 'frontend-dashboard' ); ?>
										</label>
										<div class="relative flex items-center">
											<span class="absolute left-3.5 flex items-center pointer-events-none text-slate-400 text-xs" style="left: 14px !important; position: absolute !important; z-index: 10 !important;">
												<i class="fas fa-user-tag"></i>
											</span>
											<input type="text" id="fed_email_from_name" name="credentials[from_name]" value="<?php echo esc_attr( $from_name ); ?>" placeholder="<?php esc_attr_e( 'Frontend Dashboard', 'frontend-dashboard' ); ?>" class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 transition-all outline-none" style="padding-left: 38px !important; min-height: 42px !important; height: 42px !important;" />
										</div>
										<p class="text-[11px] text-slate-400 mt-1"><?php esc_html_e( 'Display name shown alongside the sender email address.', 'frontend-dashboard' ); ?></p>
									</div>
								</div>
							</div>

							<!-- 3. SMTP Server Details (Smooth Conditional Disclosure) -->
							<div id="fed_smtp_config_container" class="<?php echo ! $is_smtp ? 'hidden' : ''; ?> pt-6 border-t border-slate-100">
								<div class="p-6 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-5">
									<div class="flex items-center gap-3">
										<div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm font-bold">
											<i class="fas fa-network-wired"></i>
										</div>
										<div>
											<h4 class="text-xs font-bold text-slate-900 m-0"><?php esc_html_e( 'SMTP Server Credentials', 'frontend-dashboard' ); ?></h4>
											<p class="text-[11px] text-slate-500 m-0"><?php esc_html_e( 'Configure your SMTP relay host, port, authentication, and secure encryption.', 'frontend-dashboard' ); ?></p>
										</div>
									</div>

									<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
										<!-- SMTP Host -->
										<div class="sm:col-span-2">
											<label for="fed_smtp_host" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'SMTP Host / Server', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
											</label>
											<input type="text" id="fed_smtp_host" name="smtp[host_name]" value="<?php echo esc_attr( $host ); ?>" placeholder="smtp.mailgun.org or smtp.gmail.com" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
										</div>

										<!-- SMTP Port -->
										<div>
											<label for="fed_smtp_port" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'SMTP Port', 'frontend-dashboard' ); ?> <span class="text-rose-500">*</span>
											</label>
											<input type="number" id="fed_smtp_port" name="smtp[port]" value="<?php echo esc_attr( $port ); ?>" placeholder="587" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
										</div>
									</div>

									<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
										<!-- Encryption -->
										<div>
											<label for="fed_smtp_encryption" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'Encryption Protocol', 'frontend-dashboard' ); ?>
											</label>
											<select id="fed_smtp_encryption" name="smtp[encryption]" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none">
												<option value="TLS" <?php selected( strtoupper( $encryption ), 'TLS' ); ?>>TLS (Recommended: Port 587)</option>
												<option value="SSL" <?php selected( strtoupper( $encryption ), 'SSL' ); ?>>SSL (Port 465)</option>
												<option value="STARTTLS" <?php selected( strtoupper( $encryption ), 'STARTTLS' ); ?>>STARTTLS</option>
												<option value="NONE" <?php selected( strtoupper( $encryption ), 'NONE' ); ?>>None / Plain (Port 25)</option>
											</select>
										</div>

										<!-- Authentication Toggle -->
										<div>
											<label for="fed_smtp_auth" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'SMTP Authentication', 'frontend-dashboard' ); ?>
											</label>
											<select id="fed_smtp_auth" name="smtp[auth]" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none">
												<option value="yes" <?php selected( $auth, 'yes' ); ?>><?php esc_html_e( 'Yes (Requires Username & Password)', 'frontend-dashboard' ); ?></option>
												<option value="no" <?php selected( $auth, 'no' ); ?>><?php esc_html_e( 'No (Anonymous Relay)', 'frontend-dashboard' ); ?></option>
											</select>
										</div>
									</div>

									<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
										<!-- SMTP Username -->
										<div>
											<label for="fed_smtp_user" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'SMTP Username', 'frontend-dashboard' ); ?>
											</label>
											<input type="text" id="fed_smtp_user" name="smtp[user_name]" value="<?php echo esc_attr( $username ); ?>" placeholder="username or api key" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="min-height: 42px !important; height: 42px !important;" />
										</div>

										<!-- SMTP Password -->
										<div>
											<label for="fed_smtp_pass" class="block text-xs font-semibold text-slate-700 mb-1.5">
												<?php esc_html_e( 'SMTP Password', 'frontend-dashboard' ); ?>
											</label>
											<div class="relative flex items-center">
												<input type="password" id="fed_smtp_pass" name="smtp[password]" value="<?php echo esc_attr( $password ); ?>" placeholder="••••••••••••" class="w-full text-xs text-slate-800 bg-white border border-slate-200 rounded-xl focus:border-indigo-500 transition-all outline-none font-mono" style="padding-right: 40px !important; min-height: 42px !important; height: 42px !important;" />
												<button type="button" id="fed_toggle_smtp_pass" class="absolute right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer" style="right: 12px !important; position: absolute !important; z-index: 10 !important;" title="<?php esc_attr_e( 'Show/Hide password', 'frontend-dashboard' ); ?>">
													<i class="fas fa-eye text-xs"></i>
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Action Footer -->
						<div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
							<button type="submit" id="fed_save_email_btn" class="fed-btn-primary px-6 py-2.5 rounded-xl font-bold text-xs inline-flex items-center gap-2 cursor-pointer transition-all shadow-xs" style="background-color: #4f46e5 !important; color: #ffffff !important;">
								<i class="fas fa-check" style="color: #ffffff !important;"></i>
								<span><?php esc_html_e( 'Save Email Settings', 'frontend-dashboard' ); ?></span>
							</button>
						</div>
					</div>
				</form>

				<!-- 4. Deliverability Test Email Tool Card -->
				<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-7">
					<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
						<div class="flex items-center gap-3">
							<div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base font-semibold shadow-2xs">
								<i class="fas fa-paper-plane"></i>
							</div>
							<div>
								<h3 class="text-sm font-bold text-slate-900 m-0"><?php esc_html_e( 'Send a Test Email', 'frontend-dashboard' ); ?></h3>
								<p class="text-xs text-slate-500 m-0"><?php esc_html_e( 'Verify that your email routing and SMTP credentials deliver correctly to real mailboxes.', 'frontend-dashboard' ); ?></p>
							</div>
						</div>
					</div>

					<div class="mt-5 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 max-w-xl">
						<div class="relative flex-1 flex items-center">
							<span class="absolute left-3.5 flex items-center pointer-events-none text-slate-400 text-xs" style="left: 14px !important; position: absolute !important; z-index: 10 !important;">
								<i class="fas fa-envelope"></i>
							</span>
							<input type="email" id="fed_test_email_recipient" placeholder="your-email@domain.com" value="<?php echo esc_attr( wp_get_current_user()->user_email ); ?>" class="w-full text-xs text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-emerald-500 transition-all outline-none" style="padding-left: 38px !important; min-height: 42px !important; height: 42px !important;" />
						</div>
						<button type="button" id="fed_send_test_email_btn" data-url="<?php echo $test_action; ?>" class="fed-btn-secondary px-5 py-2.5 rounded-xl font-bold text-xs inline-flex items-center justify-center gap-2 cursor-pointer transition-all border border-slate-200 hover:bg-slate-100" style="min-height: 42px !important; height: 42px !important;">
							<i class="fas fa-paper-plane text-emerald-600"></i>
							<span><?php esc_html_e( 'Send Test Email', 'frontend-dashboard' ); ?></span>
						</button>
					</div>
					<div id="fed_test_email_feedback" class="hidden mt-3 text-xs p-3 rounded-xl font-medium"></div>
				</div>
			</div>

			<!-- Live Toggle & Password Show Script -->
			<script>
				(function($) {
					'use strict';
					$(document).ready(function() {
						// Via Service Radio Cards Toggle
						$(document).on('change', '.fed-email-via-radio', function() {
							var selected = $('input[name="via"]:checked').val();
							$('.fed-mail-method-card').removeClass('border-indigo-600 bg-indigo-50/30').addClass('border-slate-200 bg-white');
							$(this).closest('.fed-mail-method-card').addClass('border-indigo-600 bg-indigo-50/30').removeClass('border-slate-200 bg-white');

							if (selected === 'SMTP') {
								$('#fed_smtp_config_container').slideDown(200);
								$('#fed_email_mode_badge').removeClass('bg-slate-100 text-slate-700 border-slate-200').addClass('bg-indigo-50 text-indigo-700 border-indigo-100');
								$('#fed_email_mode_badge span:first').removeClass('bg-slate-400').addClass('bg-indigo-500');
								$('#fed_email_mode_badge_text').text('SMTP Active');
							} else {
								$('#fed_smtp_config_container').slideUp(200);
								$('#fed_email_mode_badge').removeClass('bg-indigo-50 text-indigo-700 border-indigo-100').addClass('bg-slate-100 text-slate-700 border-slate-200');
								$('#fed_email_mode_badge span:first').removeClass('bg-indigo-500').addClass('bg-slate-400');
								$('#fed_email_mode_badge_text').text('WP Mail Active');
							}
						});

						// Toggle Password Visibility
						$(document).on('click', '#fed_toggle_smtp_pass', function(e) {
							e.preventDefault();
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

						// Send Test Email AJAX
						$(document).on('click', '#fed_send_test_email_btn', function(e) {
							e.preventDefault();
							var $btn = $(this);
							var targetUrl = $btn.data('url');
							var recipient = $('#fed_test_email_recipient').val().trim();
							var $feedback = $('#fed_test_email_feedback');

							if (!recipient) {
								$feedback.removeClass('hidden bg-emerald-50 text-emerald-700 border-emerald-200')
									.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
									.text('Please enter a recipient email address.');
								return;
							}

							$btn.prop('disabled', true).addClass('opacity-75');
							$feedback.removeClass('hidden bg-rose-50 text-rose-700 border-rose-200 bg-emerald-50 text-emerald-800 border-emerald-200')
								.addClass('bg-slate-50 text-slate-600 border border-slate-200 block')
								.text('Sending test email to ' + recipient + '...');

							$.ajax({
								url: targetUrl,
								type: 'POST',
								data: {
									recipient: recipient,
									fed_nonce: $('#fed_email_settings_form input[name="fed_nonce"]').val()
								},
								success: function(response) {
									$btn.prop('disabled', false).removeClass('opacity-75');
									if (response && response.success) {
										$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-rose-50 text-rose-700 border-rose-200')
											.addClass('bg-emerald-50 text-emerald-800 border border-emerald-200 block')
											.html('<i class="fas fa-check-circle mr-1.5 text-emerald-600"></i> ' + (response.data && response.data.message ? response.data.message : 'Test email sent successfully! Check your inbox.'));
									} else {
										var errMsg = (response && response.data && response.data.message) ? response.data.message : 'Failed to send test email.';
										$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-800 border-emerald-200')
											.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
											.html('<i class="fas fa-exclamation-circle mr-1.5 text-rose-500"></i> ' + errMsg);
									}
								},
								error: function() {
									$btn.prop('disabled', false).removeClass('opacity-75');
									$feedback.removeClass('bg-slate-50 text-slate-600 border-slate-200 bg-emerald-50 text-emerald-800 border-emerald-200')
										.addClass('bg-rose-50 text-rose-700 border border-rose-200 block')
										.html('<i class="fas fa-exclamation-circle mr-1.5 text-rose-500"></i> Network error occurred while sending test email.');
								}
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Unified Save for Email & SMTP Settings.
		 */
		public function update() {
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$via        = fed_get_data( 'via', $request, 'WP_MAIL' );
			$from_email = fed_get_data( 'credentials.email', $request, '' );
			$from_name  = fed_get_data( 'credentials.from_name', $request, '' );

			if ( ! empty( $from_email ) && ! is_email( $from_email ) ) {
				wp_send_json_error( array( 'message' => __( 'Please enter a valid "From" email address.', 'frontend-dashboard' ) ) );
			}

			$this->settings['via']                   = in_array( $via, array( 'WP_MAIL', 'SMTP' ), true ) ? $via : 'WP_MAIL';
			$this->settings['credentials']['email']     = sanitize_email( $from_email );
			$this->settings['credentials']['from_name'] = sanitize_text_field( $from_name );

			if ( 'SMTP' === $via ) {
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
					'password'   => $password, // Passwords preserved as entered
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
			$request = isset( $_POST ) ? fed_sanitize_text_field( wp_unslash( $_POST ) ) : array();
			fed_verify_nonce( $request );

			$recipient = isset( $request['recipient'] ) ? sanitize_email( $request['recipient'] ) : '';
			if ( empty( $recipient ) || ! is_email( $recipient ) ) {
				wp_send_json_error( array( 'message' => __( 'Please provide a valid recipient email address.', 'frontend-dashboard' ) ) );
			}

			$subject = sprintf( __( '[%s] Test Email from Frontend Dashboard', 'frontend-dashboard' ), get_bloginfo( 'name' ) );
			$via     = fed_get_data( 'via', $this->settings, 'WP_MAIL' );
			$body    = sprintf(
				__( "Hello!\n\nThis is a confirmation test email sent from Frontend Dashboard on %s.\n\nRouting Method: %s\nSender: %s <%s>\n\nIf you received this, your email configuration is working perfectly!", 'frontend-dashboard' ),
				get_bloginfo( 'name' ),
				esc_html( $via ),
				esc_html( fed_get_data( 'credentials.from_name', $this->settings, get_bloginfo( 'name' ) ) ),
				esc_html( fed_get_data( 'credentials.email', $this->settings, get_option( 'admin_email' ) ) )
			);

			$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

			$mail_error = null;
			$failed_handler = function( $wp_error ) use ( &$mail_error ) {
				if ( is_wp_error( $wp_error ) ) {
					$mail_error = $wp_error->get_error_message();
				}
			};

			add_action( 'wp_mail_failed', $failed_handler );

			$sent = wp_mail( $recipient, $subject, $body, $headers );

			remove_action( 'wp_mail_failed', $failed_handler );

			if ( $sent ) {
				wp_send_json_success( array(
					'message' => sprintf( __( 'Test email sent successfully to %s! Please check your inbox.', 'frontend-dashboard' ), esc_html( $recipient ) ),
				) );
			} else {
				$msg = ! empty( $mail_error ) ? $mail_error : __( 'wp_mail() returned false. Please verify your SMTP host, port, username, password, and encryption protocol.', 'frontend-dashboard' );
				wp_send_json_error( array( 'message' => $msg ) );
			}
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

