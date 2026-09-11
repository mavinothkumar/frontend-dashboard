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
			// add_action( 'init', array( $this, 'fed_load_text_domain' ) );
			add_action( 'fed_add_inline_css_at_head', array( $this, 'fed_add_inline_css_at_head_color' ) );
			add_action( 'wp_head', array( $this, 'fed_add_inline_css_at_head_color' ), 99 );
			add_action( 'fed_before_dashboard_container', array( $this, 'fed_add_inline_css_at_head_color' ), 1 );
			add_action( 'fed_inside_dashboard_container_top', array( $this, 'fed_add_inline_css_at_head_color' ), 1 );
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
					$mailer->isSMTP();
					$auth               = fed_get_data( 'smtp.auth', $settings, 'yes' );
					$mailer->SMTPAuth   = ( 'no' === $auth || false === $auth || '0' === $auth ) ? false : true;
					$mailer->Host       = (string) fed_get_data( 'smtp.host_name', $settings, '' );
					$mailer->Username   = (string) fed_get_data( 'smtp.user_name', $settings, '' );
					$mailer->Password   = (string) fed_get_data( 'smtp.password', $settings, '' );
					$encryption         = strtolower( (string) fed_get_data( 'smtp.encryption', $settings, 'tls' ) );
					if ( 'none' === $encryption ) {
						$mailer->SMTPSecure  = '';
						$mailer->SMTPAutoTLS = false;
					} elseif ( 'starttls' === $encryption ) {
						$mailer->SMTPSecure = 'tls';
					} else {
						$mailer->SMTPSecure = $encryption;
					}
					$port = fed_get_data( 'smtp.port', $settings, 587 );
					$mailer->Port = ! empty( $port ) ? (int) $port : 587;
				}
			}
		}

		/**
		 * Loading Text Domain
		 */
		public function fed_load_text_domain() {
			// load_plugin_textdomain( 'frontend-dashboard', false, BC_FED_PLUGIN_NAME . '/languages' );
		}

		/**
		 * Adding inline Css at Head
		 */
		public function fed_add_inline_css_at_head_color() {
			static $fed_theme_css_rendered = false;
			if ( $fed_theme_css_rendered ) {
				return;
			}

			$fed_colors = get_option( 'fed_admin_setting_upl_color' );
			if ( ! $fed_colors || ! is_array( $fed_colors ) ) {
				return;
			}

			$fed_theme_css_rendered = true;

			$pbg_color      = fed_get_data( 'color.fed_upl_color_bg_color', $fed_colors, '#4F46E5' );
			$pbg_font_color = fed_get_data( 'color.fed_upl_color_bg_font_color', $fed_colors, '#FFFFFF' );
			$sbg_color      = fed_get_data( 'color.fed_upl_color_sbg_color', $fed_colors, '#06B6D4' );
			$sbg_font_color = fed_get_data( 'color.fed_upl_color_sbg_font_color', $fed_colors, '#FFFFFF' );
			$sidebar_bg     = fed_get_data( 'color.fed_upl_color_sidebar_bg', $fed_colors, '#FFFFFF' );
			$sidebar_text   = fed_get_data( 'color.fed_upl_color_sidebar_text', $fed_colors, '#64748B' );
			$active_bg      = fed_get_data( 'color.fed_upl_color_active_bg', $fed_colors, '#EEF2FF' );
			$active_text    = fed_get_data( 'color.fed_upl_color_active_text', $fed_colors, '#4F46E5' );
			$body_bg        = fed_get_data( 'color.fed_upl_color_body_bg', $fed_colors, '#F8FAFC' );
			$card_bg        = fed_get_data( 'color.fed_upl_color_card_bg', $fed_colors, '#FFFFFF' );
			$text_main      = fed_get_data( 'color.fed_upl_color_text_main', $fed_colors, '#0F172A' );
			$border_color   = fed_get_data( 'color.fed_upl_color_border', $fed_colors, '#E2E8F0' );
			?>
			<style id="fed-enterprise-dashboard-theme">
				:root, .bc_fed {
					--fed-primary: <?php echo esc_attr( $pbg_color ); ?>;
					--fed-primary-font: <?php echo esc_attr( $pbg_font_color ); ?>;
					--fed-secondary: <?php echo esc_attr( $sbg_color ); ?>;
					--fed-secondary-font: <?php echo esc_attr( $sbg_font_color ); ?>;
					--fed-sidebar-bg: <?php echo esc_attr( $sidebar_bg ); ?>;
					--fed-sidebar-text: <?php echo esc_attr( $sidebar_text ); ?>;
					--fed-sidebar-active-bg: <?php echo esc_attr( $active_bg ); ?>;
					--fed-sidebar-active-text: <?php echo esc_attr( $active_text ); ?>;
					--fed-body-bg: <?php echo esc_attr( $body_bg ); ?>;
					--fed-card-bg: <?php echo esc_attr( $card_bg ); ?>;
					--fed-text-main: <?php echo esc_attr( $text_main ); ?>;
					--fed-border: <?php echo esc_attr( $border_color ); ?>;
				}

				/* Overall Page Canvas Backdrop */
				body.bc_fed,
				.bc_fed.fed_dashboard_container,
				.bc_fed.min-h-screen,
				.bc_fed .fed_dashboard_wrapper,
				.bc_fed main.fed_dashboard_items {
					background-color: <?php echo esc_attr( $body_bg ); ?> !important;
				}

				/* Sidebar Column & Surfaces */
				.bc_fed aside.fed_dashboard_menus,
				.bc_fed .fed_sidebar_unified_shell,
				.bc_fed .fed_frontend_dashboard_menu,
				.bc_fed .fed-dashboard-sidebar {
					background-color: <?php echo esc_attr( $sidebar_bg ); ?> !important;
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}

				/* Sidebar Typography */
				.bc_fed .fed_sidebar_user_name,
				.bc_fed .fed_dashboard_menus h3,
				.bc_fed .fed_dashboard_menus .text-slate-900,
				.bc_fed .fed_dashboard_menus .text-slate-800 {
					color: <?php echo esc_attr( $sidebar_text ); ?> !important;
				}
				.bc_fed .fed_sidebar_user_email,
				.bc_fed .fed_sidebar_nav_title,
				.bc_fed .fed_dashboard_menus p,
				.bc_fed .fed_dashboard_menus .text-slate-500,
				.bc_fed .fed_dashboard_menus .text-slate-400,
				.bc_fed .fed_dashboard_menus .text-xs.uppercase {
					color: <?php echo esc_attr( $sidebar_text ); ?> !important;
					opacity: 0.85;
				}
				.bc_fed .fed_sidebar_role_badge,
				.bc_fed .fed_dashboard_menus .bg-indigo-50.text-indigo-700,
				.bc_fed .fed_dashboard_menus .inline-flex.bg-indigo-50 {
					background-color: <?php echo esc_attr( $active_bg ); ?> !important;
					color: <?php echo esc_attr( $active_text ); ?> !important;
				}
				.bc_fed .fed_sidebar_user_section {
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}

				/* Sidebar Active Nav Item */
				.bc_fed .fed_dashboard_menus .fed_menu_item a.bg-indigo-50,
				.bc_fed .fed_dashboard_menus .fed_menu_item button.bg-indigo-50,
				.bc_fed .fed_dashboard_menus .bg-indigo-50,
				.bc_fed .fed-tab-active,
				.bc_fed .fed_menu_item.active {
					background-color: <?php echo esc_attr( $active_bg ); ?> !important;
					color: <?php echo esc_attr( $active_text ); ?> !important;
					box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
				}
				.bc_fed .fed_dashboard_menus .fed_menu_item a.bg-indigo-50 span,
				.bc_fed .fed_dashboard_menus .fed_menu_item button.bg-indigo-50 span,
				.bc_fed .fed_dashboard_menus .bg-indigo-50 .text-indigo-600,
				.bc_fed .fed_dashboard_menus .bg-indigo-50 .text-indigo-700,
				.bc_fed .fed_dashboard_menus .bg-indigo-50 svg,
				.bc_fed .fed_dashboard_menus .bg-indigo-50 i {
					color: <?php echo esc_attr( $active_text ); ?> !important;
				}

				/* Sidebar Inactive Nav Items */
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50),
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50) {
					color: <?php echo esc_attr( $sidebar_text ); ?> !important;
				}
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50) span,
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50) svg,
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50) i,
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50) span,
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50) svg,
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50) i {
					color: <?php echo esc_attr( $sidebar_text ); ?> !important;
				}
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50):hover,
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50):hover {
					background-color: <?php echo esc_attr( $active_bg ); ?> !important;
					color: <?php echo esc_attr( $active_text ); ?> !important;
					opacity: 0.95;
				}
				.bc_fed .fed_dashboard_menus .fed_menu_item a:not(.bg-indigo-50):hover *,
				.bc_fed .fed_dashboard_menus .fed_menu_item button:not(.bg-indigo-50):hover * {
					color: <?php echo esc_attr( $active_text ); ?> !important;
				}

				/* Main Content Cards & Surfaces */
				.bc_fed .fed_dashboard_items > div.bg-white,
				.bc_fed .fed_dashboard_items .bg-white,
				.bc_fed .fed_dashboard_item > .bg-white,
				.bc_fed .fed_dashboard_item .bg-white,
				.bc_fed .fed_dashboard_main_card,
				.bc_fed .fed_dashboard_panel_body,
				.bc_fed .fed_dashboard_site {
					background-color: <?php echo esc_attr( $card_bg ); ?> !important;
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}

				/* Secondary Canvas Panels inside Main Card */
				.bc_fed .fed_dashboard_item .bg-slate-50\/50,
				.bc_fed .fed_dashboard_item .bg-slate-50,
				.bc_fed .fed_dashboard_items .bg-slate-50\/50,
				.bc_fed .fed_dashboard_items .bg-slate-50 {
					background-color: <?php echo esc_attr( $body_bg ); ?> !important;
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}

				/* Primary Action Buttons */
				.bc_fed button.fed_submit,
				.bc_fed input[type="submit"].btn-primary,
				.bc_fed a.bg-indigo-600,
				.bc_fed button.bg-indigo-600,
				.bc_fed .btn-primary,
				.bc_fed .fed_btn_primary,
				.bc_fed .fed-submit-btn {
					background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					background-image: none !important;
					color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
					border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
				}
				.bc_fed button.fed_submit:hover,
				.bc_fed input[type="submit"].btn-primary:hover,
				.bc_fed a.bg-indigo-600:hover,
				.bc_fed button.bg-indigo-600:hover,
				.bc_fed .btn-primary:hover {
					filter: brightness(0.92);
				}

				/* Secondary Badges & Accents */
				.bc_fed .bg-indigo-500\/20 {
					background-color: <?php echo esc_attr( $sbg_color ); ?>33 !important;
					color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
					border-color: <?php echo esc_attr( $sbg_color ); ?>66 !important;
				}

				/* Typography & Headings */
				.bc_fed .fed_dashboard_items h1,
				.bc_fed .fed_dashboard_items h2,
				.bc_fed .fed_dashboard_items h3,
				.bc_fed .fed_dashboard_items h4,
				.bc_fed .fed_dashboard_items h5,
				.bc_fed .fed_dashboard_items h6,
				.bc_fed .fed_dashboard_items .text-slate-900,
				.bc_fed .fed_dashboard_items .text-slate-800,
				.bc_fed .fed_dashboard_items .text-gray-900 {
					color: <?php echo esc_attr( $text_main ); ?> !important;
				}

				/* Borders & Dividers */
				.bc_fed .border-slate-200,
				.bc_fed .border-slate-200\/80,
				.bc_fed .border-gray-200,
				.bc_fed .border-gray-100 {
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}

				/* Form Inputs */
				.bc_fed input[type="text"],
				.bc_fed input[type="email"],
				.bc_fed input[type="password"],
				.bc_fed input[type="url"],
				.bc_fed input[type="number"],
				.bc_fed select,
				.bc_fed textarea {
					border-color: <?php echo esc_attr( $border_color ); ?> !important;
				}
				.bc_fed input:focus, .bc_fed select:focus, .bc_fed textarea:focus {
					border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					box-shadow: 0 0 0 3px <?php echo esc_attr( $pbg_color ); ?>26 !important;
				}

				/* Legacy Classes */
				.bc_fed .fed_header_font_color {
					color: <?php echo esc_attr( $pbg_color ); ?> !important;
					font-weight: bolder;
				}
				.bc_fed .fed_menu_title, .bc_fed .fed_menu_icon {
					color: <?php echo esc_attr( $sbg_font_color ); ?> !important;
				}
				.bcd_fed .fed_bg_primary,
				.bc_fed .fed_bg_primary {
					background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
				}
				.bc_fed .nav-tabs > li.active > a,
				.bc_fed .list-group-item.active {
					background-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					border-color: <?php echo esc_attr( $pbg_color ); ?> !important;
					color: <?php echo esc_attr( $pbg_font_color ); ?> !important;
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
				'fed_head_css',
				array(
					'pbg_color'      => $pbg_color,
					'pbg_font_color' => $pbg_font_color,
					'sbg_color'      => $sbg_color,
					'sbg_font_color' => $sbg_font_color,
				)
			);
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
