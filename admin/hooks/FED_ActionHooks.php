<?php

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
			add_action( 'plugin_action_links_' . BC_FED_PLUGIN_BASENAME, array(
				$this,
				'fed_plugin_action_links',
			), 10, 2 );
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
			$fed_colors = get_option( 'fed_admin_setting_upl_color' );
			if ( false !== $fed_colors ) {
				$pbg_color      = $fed_colors['color']['fed_upl_color_bg_color'];
				$pbg_font_color = $fed_colors['color']['fed_upl_color_bg_font_color'];
				$sbg_color      = $fed_colors['color']['fed_upl_color_sbg_color'];
				$sbg_font_color = $fed_colors['color']['fed_upl_color_sbg_font_color'];
				?>
				<style>
					.bc_fed .fed_header_font_color {
						color: <?php echo $pbg_color; ?> !important;
						font-weight: bolder;
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
						background-color: <?php echo $pbg_color  ?> !important;
						background-image: none !important;
						border-color: <?php echo $pbg_color  ?> !important;
						color: <?php echo $pbg_font_color ?> !important;
					}

					.bc_fed .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover,
					.bc_fed .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
						background-color: <?php echo $pbg_color  ?> !important;
						border-color: <?php echo $pbg_color  ?> !important;
						color: <?php echo $pbg_font_color ?> !important;
					}

					.bc_fed .nav-tabs {
						border-bottom: 1px solid <?php echo $pbg_color  ?> !important;
					}

					.bc_fed .panel-primary {
						border-color: <?php echo $pbg_color  ?> !important;
					}

					.bc_fed .bg-primary-font {
						color: <?php echo $pbg_color; ?>;
					}

					.bc_fed .fed_login_menus {
						background-color: <?php echo $pbg_color; ?> !important;
						color: <?php echo $pbg_font_color ?> !important;
					}

					.bc_fed .fed_login_content {
						border: 1px solid <?php echo $pbg_color; ?> !important;
						padding: 20px 40px;
					}

					.bc_fed .list-group-item {
						background-color: <?php echo $sbg_color?> !important;
						border-color: #ffffff !important;
						color: <?php echo $sbg_font_color?> !important;
					}

					.bc_fed .list-group-item a {
						color: <?php echo $sbg_font_color?> !important;
					}

					.bc_fed .list-group-item.active, .bc_fed .list-group-item.active:hover, .bc_fed .list-group-item.active:focus {
						text-shadow: none !important;
					}

					.bc_fed .btn-default, .bc_fed .btn-primary, .bc_fed .btn-success, .bc_fed .btn-info, .bc_fed .btn-warning, .bc_fed .btn-danger {
						text-shadow: none !important;
					}
				</style>

				<?php
			}
		}

		/**
		 * @param $text
		 *
		 * @return string
		 */
		public function fed_update_footer( $text ) {
			if ( isset( $_GET['page'] ) && in_array( $_GET['page'], fed_get_script_loading_pages(), true ) ) {
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
		 * @param $wp_admin_bar
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
		 *
		 */
		public function fed_wp_before_admin_bar_render() {
			global $wp_admin_bar;
			$dashboard_url = fed_get_dashboard_url();
			if ( $dashboard_url ) {
				$frontend_dashboard = array(
					'parent' => false,
					'id'     => 'frontend-dashboard-main',
					'title'  => __( '<img class="ab-icon" src="' . plugins_url( 'common/assets/images/d.png',
							BC_FED_PLUGIN ) . '" /> <span class="ab-label">Frontend Dashboard</span>',
						'frontend-dashboard' ),
					'href'   => $dashboard_url
				);
				$wp_admin_bar->add_menu( $frontend_dashboard );
			}
		}

		/**
		 * @param $links
		 * @param $file
		 *
		 * @return array
		 */
		public function fed_plugin_row_meta( $links, $file ) {
			if ( BC_FED_PLUGIN_BASENAME == $file ) {
				$row_meta = array(
					'docs/videos' => '<a href="' . esc_url( 'https://buffercode.com/category/name/frontend-dashboard' ) . '">' . esc_html__( 'Docs/Videos',
							'frontend-dashboard' ) . '</a>',
					'donation'    => '<a href="' . esc_url( 'https://www.paypal.me/buffercode' ) . '">' . esc_html__( 'Donation',
							'frontend-dashboard' ) . '</a>',

					'support' => '<a href="mailto:support@buffercode.com">' . esc_html__( 'Support',
							'frontend-dashboard' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		/**
		 * @param $links
		 *
		 * @return array
		 */
		public function fed_plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=fed_settings_menu' ) . '" aria-label="' . esc_attr__( 'Frontend Dashboard Settings',
						'frontend-dashboard' ) . '">' . esc_html__( 'Settings', 'frontend-dashboard' ) . '</a>'
			);

			return array_merge( $action_links, $links );
		}
	}

	new FED_ActionHooks();
}