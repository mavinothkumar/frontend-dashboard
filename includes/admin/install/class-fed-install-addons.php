<?php
/**
 * Install Add-ons.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
			if ( ! current_user_can( 'install_plugins' ) ) {
				wp_send_json_error(
					array(
						'errorMessage' => __( 'Sorry, you are not allowed to install plugins on this site.', 'frontend-dashboard' ),
					)
				);
			}

			if ( ! function_exists( 'wp_ajax_install_plugin' ) ) {
				require_once ABSPATH . 'wp-admin/includes/ajax-actions.php';
			}

			wp_ajax_install_plugin();
			exit();
		}

		/**
		 * Activate Plugin.
		 *
		 * @param  array $request  Request.
		 */
		public function activate( $request ) {
			if ( ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to activate plugins on this site.', 'frontend-dashboard' ), 403 );
			}

			fed_verify_nonce( $request );

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
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$page = isset( $_GET, $_GET['fed_plugin_custom_activate'] ) && 'on' === $_GET['fed_plugin_custom_activate'] ? true : false;
			if ( $page ) {
				wp_safe_redirect( fed_menu_page_url( 'fed_plugin_pages' ) );
				exit();
			}
		}
	}

	new FEDInstallAddons();
}
