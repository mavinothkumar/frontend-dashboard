<?php
/**
 * Actions Hooks
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FED_ActionHooks' ) ) {
	/**
	 * Class FED_ActionHooks
	 */
	class FED_ActionHooks {

		/**
		 * FED_ActionHooks constructor.
		 */
		public function __construct() {
			add_action( 'admin_bar_menu', array( $this, 'fed_admin_bar_menu' ) );
			add_action( 'init', array( $this, 'fed_load_text_domain' ) );
			add_action( 'fed_add_inline_css_at_head', array( $this, 'fed_add_inline_css_at_head_color' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'fed_wp_before_admin_bar_render' ) );
			add_action( 'plugin_row_meta', array( $this, 'fed_plugin_row_meta' ), 10, 2 );
			add_action( 'admin_footer_text', array( $this, 'fed_update_footer' ) );
			add_action(
				'plugin_action_links_' . BC_FED_PLUGIN_BASENAME,
				array(
					$this,
					'fed_plugin_action_links',
				), 10, 2
			);
			add_action( 'phpmailer_init', array( $this, 'send_email_via_smtp' ) );
		}

		/**
		 * Send Email via SMPT.
		 *
		 * @param  object $mailer  Mailer.
		 */
		public function send_email_via_smtp( $mailer ) {
			$settings  = get_option( 'fed_settings_email' );
			$is_enable = fed_get_data( 'via', $settings, false );
			if ( $settings ) {
				$email     = fed_get_data( 'credentials.email', $settings, false );
				$from_name = fed_get_data( 'credentials.from_name', $settings, false );
				$fed_email = new FEDEmail();
				if ( $email && ! empty( $email ) && is_email( $email ) ) {
					add_filter( 'wp_mail_from', array( $fed_email, 'sender_email' ) );
				}
				if ( $from_name && ! empty( $from_name ) ) {
					add_filter( 'wp_mail_from_name', array( $fed_email, 'sender_name' ) );
				}
				if ( 'SMTP' === $is_enable ) {
					$mailer->IsSMTP();
					$mailer->SMTPAuth   = fed_get_data( 'smtp.auth', $settings );
					$mailer->Host       = fed_get_data( 'smtp.host_name', $settings );
					$mailer->Username   = fed_get_data( 'smtp.user_name', $settings );
					$mailer->Password   = fed_get_data( 'smtp.password', $settings );
					$mailer->SMTPSecure = fed_get_data( 'smtp.encryption', $settings );
					$mailer->Port       = fed_get_data( 'smtp.port', $settings );
				}
			}
		}

		/**
		 * Loading Text Domain
		 */
		public function fed_load_text_domain() {
			load_plugin_textdomain( 'frontend-dashboard', false, BC_FED_PLUGIN_NAME . '/languages' );
		}

		/**
		 * Adding inline Css at Head
		 */
		public function fed_add_inline_css_at_head_color() {

			if ( fed_is_shortcode_in_content() ) {
				$fed_colors = get_option( 'fed_admin_setting_upl_color' );

				$pbg_color      = fed_get_data( 'color.fed_upl_color_bg_color', $fed_colors, '#0AAAAA' );
				$pbg_font_color = fed_get_data( 'color.fed_upl_color_bg_font_color', $fed_colors, '#FFFFFF' );
				$sbg_color      = fed_get_data( 'color.fed_upl_color_sbg_color', $fed_colors, '#033333' );
				$sbg_font_color = fed_get_data( 'color.fed_upl_color_sbg_font_color', $fed_colors, '#FFFFFF' );
				?>
				<style>
					.bc_fed .fed_header_font_color {
						color: <?php echo esc_attr( $pbg_color ); ?> !important;
						font-weight: bolder;
					}

					.bc_fed .fed_menu_title, .bc_fed .fed_menu_icon {
						color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
					}

					.bcd_fed .fed_bg_primary {
						background: <?php echo esc_attr( $pbg_color ); ?> !important;
						color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
					}

					.bc_fed .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover,
					.bc_fed .btn-primary,
					.bc_fed .bg-primary,
					.bc_fed .nav-pills > li.active > a,
					.bc_fed .nav-pills > li.active > a:focus,
					.bc_fed .nav-pills > li.active > a:hover,
					.bc_fed .list-group-item.active,
					.bc_fed .list-group-item.active:focus,
					.bc_fed .list-group-item.active:hover,
					.bc_fed .panel-primary > .panel-heading,
					.bc_fed .btn-primary.focus, .btn-primary:focus,
					.bc_fed .btn-primary:hover,
					.bc_fed .btn.active, .btn:active,
					.bc_fed input[type="button"]:hover,
					.bc_fed input[type="button"]:focus,
					.bc_fed input[type="submit"]:hover,
					.bc_fed input[type="submit"]:focus,
					.bc_fed .popover-title {
						background-color: <?php echo esc_attr( $pbg_color ); ?>;
						background-image: none !important;
						border-color: <?php echo esc_attr( $pbg_color ); ?>;
						color: <?php echo esc_attr( $pbg_font_color ); ?>;
					}

					.bc_fed .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover,
					.bc_fed .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
						background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
						border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
						color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
					}

					.fed_frontend_dashboard_menu .fed_menu_item {
						background: <?php echo esc_attr( $sbg_color ); ?> !important;
						color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
					}

					.fed_frontend_dashboard_menu .panel-body .panel-title {
						padding: 10px;
						margin: 5px;
						background: <?php echo esc_attr( $sbg_color ); ?>;
						color: <?php echo esc_attr( $sbg_font_color ); ?>;
					}

					.bc_fed .fed_frontend_dashboard_menu .panel-heading.active,
					.bc_fed .fed_frontend_dashboard_menu .panel-body .panel-title.active {
						color: <?php echo esc_attr( $pbg_font_color ); ?>;
						background: <?php echo esc_attr( $pbg_color ); ?>;
					}

					.bc_fed .nav-tabs {
						border-bottom: 1px solid <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.bc_fed .panel-primary {
						border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.bc_fed .bg-primary-font {
						color: <?php echo esc_attr( $pbg_color ); ?>;
					}

					.bc_fed .fed_login_menus {
						background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
						color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
					}

					.bc_fed .fed_login_content {
						border: 1px solid <?php echo esc_attr( $pbg_color ); ?> !important;
						padding: 20px 40px;
					}

					.bc_fed .list-group-item {
						background-color: <?php echo esc_attr( $sbg_color ); ?> !important;
						border-color: #ffffff !important;
						color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
					}

					.bc_fed .swal2-icon.swal2-success [class^='swal2-success-line'] {
						background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.bc_fed .swal2-icon.swal2-success .swal2-success-ring {
						border: 4px solid <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.bc_fed .list-group-item a {
						color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
					}

					.bc_fed .list-group-item.active, .bc_fed .list-group-item.active:hover, .bc_fed .list-group-item.active:focus {
						text-shadow: none !important;
					}

					.bc_fed .btn-default, .bc_fed .btn-primary, .bc_fed .btn-success, .bc_fed .btn-info, .bc_fed .btn-warning, .bc_fed .btn-danger {
						text-shadow: none !important;
					}

					.swal2-icon.swal2-success {
						border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.swal2-icon.swal2-success [class^='swal2-success-line'] {
						background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.swal2-icon.swal2-success .swal2-success-ring {
						width: 80px;
						height: 80px;
						border: 4px solid <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.fed_primary_font_color {
						color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.swal2-confirm.swal2-styled {
						background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
						border-left-color: <?php echo esc_attr( $pbg_color ); ?> !important;
						border-right-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					}

					.fed_tab_menus.active {
						font-weight: 700;
						text-decoration: underline;
					}
				</style>

				<?php
				do_action(
					'fed_head_css', array(
						'pbg_color'      => $pbg_color,
						'pbg_font_color' => $pbg_font_color,
						'sbg_color'      => $sbg_color,
						'sbg_font_color' => $sbg_font_color,
					)
				);
			}
		}

		/**
		 * Update Footer.
		 *
		 * @param  string $text  Text.
		 *
		 * @return string
		 */
		public function fed_update_footer( $text ) {
			if (
				isset( $_GET['page_type'] ) && in_array(
					wp_unslash( $_GET['page_type'] ), fed_get_script_loading_pages(),
					true
				)
			) {
				$text = '<span id="footer-thankyou">If you like <strong>Frontend Dashboard (v' . BC_FED_PLUGIN_VERSION . ')</strong>, Please leave us a rating <a 
href="https://wordpress.org/support/plugin/frontend-dashboard/reviews/?filter=5#new-post">
<i class="fa fa-star fa-2x" aria-hidden="true"></i>
<i class="fa fa-star fa-2x" aria-hidden="true"></i>
<i class="fa fa-star fa-2x" aria-hidden="true"></i>
<i class="fa fa-star fa-2x" aria-hidden="true"></i>
<i class="fa fa-star fa-2x" aria-hidden="true"></i>
</a>. A huge thanks in advance <i class="fa fa-smile-o" aria-hidden="true"></i>';
			}

			return $text;
		}

		/**
		 * Admin Bar Menu.
		 *
		 * @param  object $wp_admin_bar  Admin bar.
		 */
		public function fed_admin_bar_menu( $wp_admin_bar ) {
			$dashboard_url = fed_get_dashboard_url();
			if ( $dashboard_url ) {
				$under_dashboard = array(
					'parent' => 'site-name',
					'id'     => 'frontend-dashboard',
					'title'  => __( 'Frontend Dashboard', 'frontend-dashboard' ),
					'href'   => fed_get_dashboard_url(),
				);

				$wp_admin_bar->add_node( $under_dashboard );
			}
		}

		/**
		 * Admin bar render.
		 */
		public function fed_wp_before_admin_bar_render() {
			global $wp_admin_bar;
			$dashboard_url = fed_get_dashboard_url();
			if ( $dashboard_url ) {
				$frontend_dashboard = array(
					'parent' => false,
					'id'     => 'frontend-dashboard-main',
					'meta'   => array(
						'class' => 'menupop',
					),
					'title'  =>
						'<span class="ab-icon">
                               <img style="margin-top:-4px;" class="" src="' .
						esc_url(
							plugins_url(
								'/assets/frontend/images/d.png',
								BC_FED_PLUGIN
							)
						) . '" />
                               </span><span class="ab-label">Frontend Dashboard</span>',
					'href'   => $dashboard_url,
				);
				$wp_admin_bar->add_menu( $frontend_dashboard );
			}
		}

		/**
		 * Plugin Row Meta.
		 *
		 * @param  array  $links  Links.
		 * @param  string $file  File.
		 *
		 * @return array
		 */
		public function fed_plugin_row_meta( $links, $file ) {
			if ( BC_FED_PLUGIN_BASENAME == $file ) {
				$row_meta = array(
					'demo'        => '<a href="' . esc_url( 'https://demo.frontenddashboard.com/' ) . '">' . esc_html__(
							'Demo',
							'frontend-dashboard'
						) . '</a>',
					'docs/videos' => '<a href="' . esc_url(
							'https://buffercode.com/category/name/frontend-dashboard'
						) . '">' . esc_html__(
						                 'Docs/Videos',
						                 'frontend-dashboard'
					                 ) . '</a>',
					'donation'    => '<a href="' . esc_url( 'https://www.paypal.me/buffercode' ) . '">' . esc_html__(
							'Donation',
							'frontend-dashboard'
						) . '</a>',

					'support' => '<a href="mailto:support@buffercode.com">' . esc_html__(
							'Support',
							'frontend-dashboard'
						) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * Plugin Action Links.
		 *
		 * @param  array $links  Links.
		 *
		 * @return array
		 */
		public function fed_plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url(
						'admin.php?page=fed_settings_menu'
					) . '" aria-label="' . esc_attr__(
					              'Frontend Dashboard Settings',
					              'frontend-dashboard'
				              ) . '">' . esc_html__( 'Settings', 'frontend-dashboard' ) . '</a>',
			);

			return array_merge( $action_links, $links );
		}
	}

	new FED_ActionHooks();
}
