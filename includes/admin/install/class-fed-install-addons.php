<?php
/**
 * Install Add-ons.
 *
 * @package Frontend Dashboard.
 */

if ( ! class_exists( 'FEDInstallAddons' ) ) {
	/**
	 * Class FEDInstallAddons
	 */
	class FEDInstallAddons {

		/**
		 * FEDInstallAddons constructor.
		 */
		public function __construct() {
			add_action( 'activated_plugin', array( $this, 'activated_plugin' ), 10, 2 );
		}

		/**
		 * Install.
		 */
		public function install() {
			wp_ajax_install_plugin();
		}

		/**
		 * Activate Plugin.
		 *
		 * @param  array $request  Request.
		 */
		public function activate( $request ) {
			$server_payload = filter_input_array( INPUT_SERVER, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
			$plugin_name    = fed_get_data( 'plugin_name', $request, false );
			$location       = $server_payload['HTTP_REFERER'];
			if ( $plugin_name ) {
				$status = activate_plugin( $plugin_name );
				if ( $status instanceof WP_Error ) {
					fed_set_alert(
						'fed_activation_message',
						__( 'OOPs! Something went wrong while activating the plugin', 'frontend-dashboard' )
					);
					wp_safe_redirect( $location );
				}
				fed_set_alert(
					'fed_activation_message',
					__( 'Plugin activated successfully', 'frontend-dashboard' )
				);
				wp_safe_redirect( $location );
			}
		}

		/**
		 * Activate Plugin.
		 *
		 * @param  string $plugin  Plugin.
		 * @param  bool   $network_wide  Network Wide.
		 */
		public function activated_plugin( $plugin, $network_wide ) {
			$page = isset( $_GET, $_GET['fed_plugin_custom_activate'] ) && 'on' === $_GET['fed_plugin_custom_activate'] ? true : false;
			if ( $page ) {
				wp_redirect( fed_menu_page_url( 'fed_plugin_pages' ) );
				exit();
			}
		}
	}

	new FEDInstallAddons();
}
