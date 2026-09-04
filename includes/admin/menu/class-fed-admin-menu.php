<?php
/**
 * Admin Menu.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FED_AdminMenu' ) ) {
	/**
	 * Class FED_AdminMenu
	 */
	class FED_AdminMenu {

		/**
		 * FED_AdminMenu constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_action( 'admin_init', array( $this, 'handle_legacy_redirects' ) );
			add_action( 'admin_head', array( $this, 'ensure_admin_page_title' ), 1 );
		}

		/**
		 * Handle legacy URL redirects for bookmarks
		 */
		public function handle_legacy_redirects() {
			if ( isset( $_GET['page'] ) && 'fed_settings_menu' === $_GET['page'] ) {
				wp_safe_redirect( admin_url( 'admin.php?page=fed_settings' ) );
				exit;
			}
			if ( isset( $_GET['page'] ) && 'fed_add_user_profile' === $_GET['page'] ) {
				$action = isset( $_GET['fed_action'] ) && 'post' === $_GET['fed_action'] ? 'post' : 'profile';
				$target = ( 'post' === $action ) ? 'fed_post_fields' : 'fed_user_profile';
				$params = $_GET;
				$params['page'] = $target;
				wp_safe_redirect( add_query_arg( $params, admin_url( 'admin.php' ) ) );
				exit;
			}
		}

		/**
		 * Ensure global $title is always a non-null string for FED admin pages to avoid PHP 8.1+ strip_tags(null) notice.
		 */
		public function ensure_admin_page_title() {
			global $title;
			if ( null === $title || '' === $title ) {
				if ( isset( $_GET['page'] ) && 0 === strpos( (string) $_GET['page'], 'fed_' ) ) {
					$title = __( 'Frontend Dashboard', 'frontend-dashboard' );
				}
			}
		}

		/**
		 * Menu
		 */
		public function menu() {
			add_menu_page(
				__( 'Frontend Dashboard', 'frontend-dashboard' ),
				__( 'Frontend Dashboard', 'frontend-dashboard' ),
				'manage_options',
				'fed_dashboard',
				array( $this, 'dashboard_overview' ),
				plugins_url( '/assets/frontend/images/d.png', BC_FED_PLUGIN ),
				2
			);

			$main_menu = $this->fed_get_main_sub_menu();
			foreach ( $main_menu as $index => $menu ) {
				add_submenu_page(
					'fed_dashboard',
					$menu['page_title'],
					$menu['menu_title'],
					$menu['capability'],
					$index,
					$menu['callback']
				);
			}

			do_action( 'fed_add_main_sub_menu_action' );

		}

		/**
		 * Executive Overview Dashboard
		 */
		public function dashboard_overview() {
			if ( class_exists( '\FED\Controllers\Admin\DashboardOverviewController' ) ) {
				( new \FED\Controllers\Admin\DashboardOverviewController() )->render();
			}
		}

		/**
		 * Payments & Invoices
		 */
		public function payments() {
			if ( class_exists( 'FEDPaymentMenu' ) ) {
				( new FEDPaymentMenu() )->index();
			}
		}

		/**
		 * Common Settings.
		 */
		/**
		 * Common Settings.
		 */
		public function common_settings() {
			$menu            = $this->admin_dashboard_settings_menu_header();
			$menu_counter    = 0;
			$content_counter = 0;
			$total_tabs      = is_array( $menu ) ? count( $menu ) : 0;
			?>
			<!-- Scoped Styles for Settings Dashboard -->
			<style>
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
				.fed-btn-delete,
				button.fed-btn-delete {
					color: #475569 !important;
					background-color: #f8fafc !important;
					border: 1px solid #e2e8f0 !important;
				}
				.fed-btn-delete:hover,
				button.fed-btn-delete:hover {
					color: #e11d48 !important;
					background-color: #fff1f2 !important;
					border-color: #fecdd3 !important;
				}

				/* Top Main Tabs Navigation Styling */
				.fed-main-tab-btn,
				a.fed-main-tab-btn {
					display: inline-flex !important;
					align-items: center !important;
					gap: 8px !important;
					padding: 9px 18px !important;
					border-radius: 12px !important;
					font-size: 12px !important;
					font-weight: 600 !important;
					color: #64748b !important;
					background-color: transparent !important;
					border: 1px solid transparent !important;
					text-decoration: none !important;
					transition: all 0.15s ease !important;
					box-shadow: none !important;
					cursor: pointer !important;
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
					box-shadow: 0 2px 6px -1px rgba(79, 70, 229, 0.35) !important;
				}
				.fed-main-tab-btn.fed-tab-active i,
				.fed-main-tab-btn.fed-tab-active span,
				a.fed-main-tab-btn.fed-tab-active i,
				a.fed-main-tab-btn.fed-tab-active span {
					color: #ffffff !important;
				}

				/* Sidebar Subtabs Navigation Styling */
				.fed-subtab-link,
				a.fed-subtab-link {
					display: flex !important;
					align-items: center !important;
					gap: 12px !important;
					padding: 10px 14px !important;
					border-radius: 14px !important;
					font-size: 12px !important;
					font-weight: 600 !important;
					color: #475569 !important;
					background-color: transparent !important;
					border: 1px solid transparent !important;
					text-decoration: none !important;
					transition: all 0.15s ease !important;
					cursor: pointer !important;
				}
				.fed-subtab-link:hover,
				a.fed-subtab-link:hover {
					background-color: #f8fafc !important;
					color: #0f172a !important;
				}
				.fed-subtab-link.fed-subtab-active,
				a.fed-subtab-link.fed-subtab-active {
					background-color: #eef2ff !important;
					border-color: #c7d2fe !important;
					color: #4338ca !important;
					font-weight: 700 !important;
					box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
				}
				.fed-subtab-link .fed-subtab-icon-box {
					width: 32px !important;
					height: 32px !important;
					border-radius: 10px !important;
					display: flex !important;
					align-items: center !important;
					justify-content: center !important;
					background-color: #f1f5f9 !important;
					color: #64748b !important;
					flex-shrink: 0 !important;
					transition: all 0.15s ease !important;
				}
				.fed-subtab-link.fed-subtab-active .fed-subtab-icon-box {
					background-color: #4f46e5 !important;
					color: #ffffff !important;
				}
				.fed-subtab-link.fed-subtab-active .fed-subtab-icon-box i {
					color: #ffffff !important;
				}

				.bc_fed label:not(.fed-role-chip):not(.role-checkbox-label):not(.cursor-pointer) {
					display: block !important;
					width: 100% !important;
					float: none !important;
					margin: 0 0 6px 0 !important;
					padding: 0 !important;
					font-size: 12px !important;
					font-weight: 700 !important;
					color: #334155 !important;
					text-align: left !important;
				}
				.bc_fed label.fed-role-chip,
				.bc_fed .role-checkbox-label,
				.bc_fed label.cursor-pointer,
				.bc_fed .fed-role-chips-wrap label {
					display: flex !important;
					width: auto !important;
					margin: 0 !important;
				}

				.bc_fed input[type="text"],
				.bc_fed input[type="number"],
				.bc_fed input[type="email"],
				.bc_fed input[type="password"],
				.bc_fed input[type="url"],
				.bc_fed textarea {
					display: block !important;
					border: 1px solid #e2e8f0 !important;
					border-radius: 14px !important;
					background-color: #f8fafc !important;
					padding: 10px 14px !important;
					font-size: 12px !important;
					font-weight: 500 !important;
					line-height: 1.5 !important;
					color: #1e293b !important;
					width: 100% !important;
					min-height: 42px !important;
					box-sizing: border-box !important;
					box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
					transition: all 0.15s ease !important;
				}

				.bc_fed select,
				.bc_fed select.form-control,
				.bc_fed select.fed_multi_select {
					display: block !important;
					border: 1px solid #e2e8f0 !important;
					border-radius: 14px !important;
					background-color: #f8fafc !important;
					padding: 10px 38px 10px 14px !important;
					font-size: 12px !important;
					font-weight: 500 !important;
					line-height: 1.5 !important;
					color: #1e293b !important;
					width: 100% !important;
					max-width: 100% !important;
					min-height: 42px !important;
					height: 42px !important;
					box-sizing: border-box !important;
					box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
					-webkit-appearance: none !important;
					-moz-appearance: none !important;
					appearance: none !important;
					background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
					background-position: right 14px center !important;
					background-repeat: no-repeat !important;
					background-size: 16px 16px !important;
					transition: all 0.15s ease !important;
					cursor: pointer !important;
				}

				.bc_fed input[type="text"]:hover,
				.bc_fed input[type="number"]:hover,
				.bc_fed input[type="email"]:hover,
				.bc_fed input[type="password"]:hover,
				.bc_fed input[type="url"]:hover,
				.bc_fed select:hover,
				.bc_fed textarea:hover {
					border-color: #cbd5e1 !important;
					background-color: #ffffff !important;
				}

				.bc_fed input[type="text"]:focus,
				.bc_fed input[type="number"]:focus,
				.bc_fed input[type="email"]:focus,
				.bc_fed input[type="password"]:focus,
				.bc_fed input[type="url"]:focus,
				.bc_fed select:focus,
				.bc_fed textarea:focus {
					border-color: #6366f1 !important;
					background-color: #ffffff !important;
					box-shadow: 0 0 0 1px #6366f1 !important;
					outline: none !important;
				}

				.bc_fed input[type="color"] {
					height: 42px !important;
					padding: 4px !important;
					cursor: pointer;
					border-radius: 12px !important;
					border: 1px solid #e2e8f0 !important;
				}
			</style>

			<div class="bc_fed fed-admin-wrap w-full max-w-none px-4 sm:px-8 py-6 sm:py-8 font-sans text-slate-800">
				<?php echo fed_loader(); ?>

				<!-- Toast Notification Element -->
				<div id="fed_toast_notification" class="fixed bottom-6 right-6 transform translate-y-16 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700" style="z-index: 99999999 !important;">
					<span id="fed_toast_icon" class="text-emerald-400 text-base"><i class="fas fa-check-circle"></i></span>
					<span id="fed_toast_message" class="text-xs font-semibold tracking-wide">Settings saved successfully.</span>
				</div>

				<!-- Page Header -->
				<div class="bg-white rounded-2xl p-5 sm:p-6 shadow-xs border border-slate-200/80 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
					<div class="flex items-center gap-3.5">
						<div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-xs shrink-0" style="background-color: #4f46e5 !important; color: #ffffff !important;">
							<i class="fas fa-sliders-h text-sm" style="color: #ffffff !important;"></i>
						</div>
						<div>
							<div class="flex items-center gap-2.5">
								<h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight m-0 p-0">
									<?php esc_html_e( 'Dashboard Settings', 'frontend-dashboard' ); ?>
								</h1>
								<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
									<?php echo (int) $total_tabs; ?> <?php esc_html_e( 'Modules', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<p class="text-xs text-slate-500 m-0 mt-0.5 font-medium">
								<?php esc_html_e( 'Configure frontend dashboard login, user permissions, layout design, email notifications, and general settings.', 'frontend-dashboard' ); ?>
							</p>
						</div>
					</div>

					<div class="flex items-center gap-2.5 shrink-0">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_dashboard_menu' ) ); ?>" class="fed-btn-secondary h-10 inline-flex items-center justify-center gap-2 px-4 rounded-xl font-semibold text-xs transition-all cursor-pointer shadow-2xs no-underline">
							<i class="fas fa-bars text-xs"></i>
							<span><?php esc_html_e( 'Dashboard Menus', 'frontend-dashboard' ); ?></span>
						</a>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_user_profile' ) ); ?>" class="fed-btn-secondary h-10 inline-flex items-center justify-center gap-2 px-4 rounded-xl font-semibold text-xs transition-all cursor-pointer shadow-2xs no-underline">
							<i class="fas fa-id-card text-xs"></i>
							<span><?php esc_html_e( 'User Profile', 'frontend-dashboard' ); ?></span>
						</a>
					</div>
				</div>

				<!-- Main Navigation Tabs Bar -->
				<div class="bg-white rounded-2xl p-1.5 shadow-xs border border-slate-200/80 mb-6 flex flex-wrap gap-1.5" id="fed_main_settings_tabs_bar" role="tablist">
					<?php 
					$btn_counter = 0;
					foreach ( $menu as $index => $item ) : 
						$is_first = ( 0 === $btn_counter );
						$btn_counter ++;
					?>
						<a href="#<?php echo esc_attr( $index ); ?>"
						   data-tab="<?php echo esc_attr( $index ); ?>"
						   role="tab"
						   class="fed-main-tab-btn <?php echo $is_first ? 'fed-tab-active' : ''; ?>">
							<i class="<?php echo esc_attr( $item['icon_class'] ); ?>"></i>
							<span><?php echo esc_html( $item['name'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>

				<!-- Main Tab Content Panes -->
				<div class="fed-main-tabs-content-wrap">
					<?php 
					$pane_counter = 0;
					foreach ( $menu as $index => $item ) : 
						$is_first = ( 0 === $pane_counter );
						$pane_counter ++;
					?>
						<div role="tabpanel"
							 class="fed-main-tab-pane <?php echo $is_first ? 'block' : 'hidden'; ?>"
							 id="tab_pane_<?php echo esc_attr( $index ); ?>"
							 data-pane="<?php echo esc_attr( $index ); ?>">
							<?php $this->call_function_method( $item ); ?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php fed_menu_icons_popup(); ?>
			</div>

			<!-- Dynamic Tab Switching & AJAX Toast Script -->
			<script>
				(function($) {
					'use strict';

					function showToast(message, isSuccess = true) {
						var $toast = $('#fed_toast_notification');
						var $icon = $('#fed_toast_icon');
						var $msg = $('#fed_toast_message');

						$msg.text(message || (isSuccess ? 'Changes saved successfully.' : 'An error occurred.'));
						if (isSuccess) {
							$icon.html('<i class="fas fa-check-circle"></i>').removeClass('text-rose-400').addClass('text-emerald-400');
						} else {
							$icon.html('<i class="fas fa-exclamation-circle"></i>').removeClass('text-emerald-400').addClass('text-rose-400');
						}

						$toast.removeClass('opacity-0 translate-y-16 pointer-events-none').addClass('opacity-100 translate-y-0');
						setTimeout(function() {
							$toast.removeClass('opacity-100 translate-y-0').addClass('opacity-0 translate-y-16 pointer-events-none');
						}, 3000);
					}

					$(document).ready(function() {
						// Primary Main Tab Switcher
						function switchMainTab(tabKey) {
							if (!tabKey) return;
							$('.fed-main-tab-btn').removeClass('fed-tab-active');
							$('.fed-main-tab-btn[data-tab="' + tabKey + '"]').addClass('fed-tab-active');

							$('.fed-main-tab-pane').addClass('hidden').removeClass('block');
							$('.fed-main-tab-pane[data-pane="' + tabKey + '"]').removeClass('hidden').addClass('block');
						}

						$(document).on('click', '.fed-main-tab-btn', function(e) {
							e.preventDefault();
							var tabKey = $(this).data('tab');
							switchMainTab(tabKey);
							if (history.pushState) {
								history.pushState(null, null, '#' + tabKey);
							} else {
								location.hash = '#' + tabKey;
							}
						});

						// Handle hash on initial load
						var initialHash = window.location.hash ? window.location.hash.replace('#', '') : '';
						if (initialHash && $('.fed-main-tab-btn[data-tab="' + initialHash + '"]').length) {
							switchMainTab(initialHash);
						}

						// Subtab Switcher
						$(document).on('click', '.fed-subtab-link', function(e) {
							e.preventDefault();
							var $this = $(this);
							var $wrap = $this.closest('.fed-settings-subtab-container');
							var targetSelector = $this.data('target') || $this.attr('href');

							$wrap.find('.fed-subtab-link').removeClass('fed-subtab-active');
							$this.addClass('fed-subtab-active');

							$wrap.find('.tab-pane').addClass('hidden').removeClass('active block');
							
							var $target = $wrap.find(targetSelector);
							if (!$target.length && targetSelector && targetSelector.indexOf('#') === 0) {
								var rawKey = targetSelector.replace('#', '');
								$target = $wrap.find('#' + rawKey + ', [data-pane="' + rawKey + '"]');
							}
							if ($target.length) {
								$target.removeClass('hidden').addClass('active block');
							}
						});
					});
				})(jQuery);
			</script>
			<?php
		}

		/**
		 * Dashboard Menu
		 */
		public function dashboard_menu() {
			fed_get_dashboard_menu_items();
		}

		/**
		 * User Profile
		 */
		public function user_profile() {
			fed_get_user_profile_menu();
		}

		/**
		 * Post Fields.
		 */
		public function post_fields() {
			fed_get_post_fields_menu();
		}

		/**
		 * Add user Profile.
		 */
		public function add_user_profile() {
			fed_get_add_profile_post_fields();
		}

		/**
		 * Plugin Pages.
		 */
		public function plugin_pages() {
			fed_get_plugin_pages_menu();
		}

		/**
		 * Help.
		 */
		public function help() {
			fed_get_help_menu();
		}

		/**
		 * Status.
		 */
		public function status() {
			fed_get_status_menu();
		}

		/**
		 * Admin Dashboard settings Menu Header.
		 *
		 * @return mixed|void
		 */
		private function admin_dashboard_settings_menu_header() {
			$menu = array(
				'login'               => array(
					'icon_class' => 'fas fa-sign-in-alt',
					'name'       => __( 'Login', 'frontend-dashboard' ),
					'callable'   => 'fed_admin_login_tab',
				),
				'user'                => array(
					'icon_class' => 'fa fa-user',
					'name'       => __( 'User', 'frontend-dashboard' ),
					'callable'   => 'fed_admin_user_options_tab',
				),
				'user_profile_layout' => array(
					'icon_class' => 'fa fa-dashboard',
					'name'       => __( 'Dashboard', 'frontend-dashboard' ),
					'callable'   => 'fed_user_profile_layout_design',
				),
				'general'             => array(
					'icon_class' => 'fas fa-tachometer-alt',
					'name'       => __( 'Common', 'frontend-dashboard' ),
					'callable'   => array(
						'object' => new FED_Admin_General(),
						'method' => 'fed_admin_general_tab',
					),
				),
				'email'               => array(
					'icon_class' => 'fas fa-envelope',
					'name'       => __( 'Email', 'frontend-dashboard' ),
					'callable'   => array(
						'object' => new FEDEmail(),
						'method' => 'show',
					),
				),
			);

			if ( ! defined( 'FED_CP_PLUGIN_VERSION' ) ) {
				$menu['post_options'] = array(
					'icon_class' => 'fa fa-envelope',
					'name'       => __( 'Post', 'frontend-dashboard' ),
					'callable'   => 'fed_admin_post_options_tab',
				);
			}

			return apply_filters( 'fed_admin_dashboard_settings_menu_header', $menu );
		}

		/**
		 * Call function method.
		 *
		 * @param  array $item  Item.
		 */
		private function call_function_method( $item ) {
			$parameter = '';
			if ( isset( $item['callable']['parameters'] ) ) {
				$parameter = $item['callable']['parameters'];
			}

			if ( is_string( $item['callable'] ) && function_exists( $item['callable'] ) ) {
				call_user_func( $item['callable'], $parameter );
			} elseif ( is_array( $item['callable'] ) && method_exists( $item['callable']['object'],
					$item['callable']['method'] ) ) {
				call_user_func( array( $item['callable']['object'], $item['callable']['method'] ), $parameter );
			} else {
				?>
				<div class="bc_fed fed_add_page_profile_container text-center">
					<?php
					esc_attr_e( 'OOPS! You have not add the callable function, please add ', 'frontend-dashboard' );
					echo esc_attr( $item['callable'] );
					esc_attr_e( ' to show the body container', 'frontend-dashboard' )
					?>
				</div>
				<?php
			}
		}

		/**
		 * Get Main Sub Menu.
		 *
		 * @return array
		 */
		public function fed_get_main_sub_menu() {
			$menu = array(
				'fed_dashboard'        => array(
					'page_title' => __( 'Overview', 'frontend-dashboard' ),
					'menu_title' => __( 'Overview', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'dashboard_overview' ),
					'position'   => 1,
				),
				'fed_dashboard_menu'   => array(
					'page_title' => __( 'Dashboard Menu', 'frontend-dashboard' ),
					'menu_title' => __( 'Dashboard Menu', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'dashboard_menu' ),
					'position'   => 10,
				),
				'fed_user_profile'     => array(
					'page_title' => __( 'User Profile', 'frontend-dashboard' ),
					'menu_title' => __( 'User Profile', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'user_profile' ),
					'position'   => 20,
				),
				'fed_post_fields'      => array(
					'page_title' => __( 'Post Fields', 'frontend-dashboard' ),
					'menu_title' => __( 'Post Fields', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'post_fields' ),
					'position'   => 25,
				),
				'fed_payments'         => array(
					'page_title' => __( 'Payments', 'frontend-dashboard' ),
					'menu_title' => __( 'Payments', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'payments' ),
					'position'   => 40,
				),
				'fed_settings'         => array(
					'page_title' => __( 'Settings', 'frontend-dashboard' ),
					'menu_title' => __( 'Settings', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'common_settings' ),
					'position'   => 50,
				),
				'fed_status'           => array(
					'page_title' => __( 'Status', 'frontend-dashboard' ),
					'menu_title' => __( 'Status', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'status' ),
					'position'   => 70,
				),
				'fed_plugin_pages'     => array(
					'page_title' => __( 'Add-Ons', 'frontend-dashboard' ),
					'menu_title' => __( 'Add-Ons', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'plugin_pages' ),
					'position'   => 80,
				),
				'fed_help'             => array(
					'page_title' => __( 'Help', 'frontend-dashboard' ),
					'menu_title' => __( 'Help', 'frontend-dashboard' ),
					'capability' => 'manage_options',
					'callback'   => array( $this, 'help' ),
					'position'   => 100,
				),
			);

			$main_menu = apply_filters( 'fed_add_main_sub_menu', $menu );

			return fed_array_sort( $main_menu, 'position' );
		}

//		public function test() {
//
//		}

	}

	new FED_AdminMenu();
}
