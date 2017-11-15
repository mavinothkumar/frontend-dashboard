<?php

if ( ! class_exists( 'FED_ActionHooks' ) ) {

	class FED_ActionHooks {
		public function __construct() {
			add_action( 'admin_bar_menu', array( $this, 'fed_admin_bar_menu' ) );
			add_action( 'wp_before_admin_bar_render', array( $this, 'fed_wp_before_admin_bar_render' ) );
			add_action( 'plugin_row_meta', array( $this, 'fed_plugin_row_meta' ), 10, 2 );
			add_action( 'plugin_action_links_' . BC_FED_PLUGIN_BASENAME, array(
				$this,
				'fed_plugin_action_links'
			), 10, 2 );
		}

		public function fed_admin_bar_menu( $wp_admin_bar ) {
			$dashboard_url = fed_get_dashboard_url();
			if ( $dashboard_url ) {
				$under_dashboard = array(
					'parent' => 'site-name',
					'id'     => 'frontend-dashboard',
					'title'  => __( 'Frontend Dashboard', 'fed' ),
					'href'   => fed_get_dashboard_url(),
				);

				$wp_admin_bar->add_node( $under_dashboard );
			}
		}

		public function fed_wp_before_admin_bar_render() {
			global $wp_admin_bar;
			$dashboard_url = fed_get_dashboard_url();
			if ( $dashboard_url ) {
				$frontend_dashboard = array(
					'parent' => false,
					'id'     => 'frontend-dashboard-main',
					'title'  => __( '<img class="ab-icon" src="' . plugins_url( 'common/assets/images/d.png', BC_FED_PLUGIN ) . '" /> <span class="ab-label">Frontend Dashboard</span>', 'fed' ),
					'href'   => $dashboard_url
				);
				$wp_admin_bar->add_menu( $frontend_dashboard );
			}
		}

		public function fed_plugin_row_meta( $links, $file ) {
			if ( BC_FED_PLUGIN_BASENAME == $file ) {
				$row_meta = array(
					'docs/videos' => '<a href="' . esc_url( 'https://buffercode.com/category/name/frontend-dashboard' ) . '">' . esc_html__( 'Docs/Videos', 'fed' ) . '</a>',
					'donation'    => '<a href="' . esc_url( 'https://www.paypal.me/buffercode' ) . '">' . esc_html__( 'Donation', 'fed' ) . '</a>',

					'support' => '<a href="mailto:support@buffercode.com">' . esc_html__( 'Support', 'fed' ) . '</a>',
				);

				return array_merge( $links, $row_meta );
			}

			return (array) $links;
		}

		public function fed_plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=fed_settings_menu' ) . '" aria-label="' . esc_attr__( 'Frontend Dashboard Settings', 'fed' ) . '">' . esc_html__( 'Settings', 'fed' ) . '</a>'
			);

			return array_merge( $action_links, $links );
		}
	}

	new FED_ActionHooks();
}