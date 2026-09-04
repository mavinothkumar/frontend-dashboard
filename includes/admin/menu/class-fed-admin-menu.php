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
		public function common_settings() {
			$menu            = $this->admin_dashboard_settings_menu_header();
			$menu_counter    = 0;
			$content_counter = 0;
			?>

			<div class="bc_fed container fed_tabs_container">
				<h3 class="fed_header_font_color">
					<?php esc_attr_e( 'Dashboard Settings', 'frontend-dashboard' ); ?>
				</h3>
				<div class="row">
					<div class="col-md-12">
						<ul class="nav nav-tabs"
								id="fed_admin_setting_tabs"
								role="tablist">
							<?php foreach ( $menu as $index => $item ) { ?>
								<li role="presentation"
										class="<?php echo ( 0 === $menu_counter ) ? 'active' : ''; ?>">
									<a href="#<?php echo esc_attr( $index ); ?>"
											aria-controls="<?php echo esc_attr( $index ); ?>"
											role="tab"
											data-toggle="tab">
										<i class="<?php echo esc_attr( $item['icon_class'] ); ?>"></i>
										<?php echo esc_attr( $item['name'] ); ?>
									</a>
								</li>
								<?php
								$menu_counter ++;
							}
							?>
						</ul>
						<!-- Tab panes -->
						<div class="tab-content">
							<?php foreach ( $menu as $index => $item ) { ?>
								<div role="tabpanel"
										class="tab-pane <?php echo ( 0 === $content_counter ) ? 'active' : ''; ?>"
										id="<?php echo esc_attr( $index ); ?>">
									<?php
									$this->call_function_method( $item );
									?>
								</div>
								<?php
								$content_counter ++;
							}
							?>
						</div>
					</div>
				</div>
				<?php fed_menu_icons_popup(); ?>
			</div>
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
