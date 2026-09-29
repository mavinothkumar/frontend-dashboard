<?php

namespace FED\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicenseManager
 *
 * Core coordinator managing stored license keys, activation state, periodic cron checks, and AJAX handlers.
 */
class LicenseManager {

	const OPTION_KEY = 'fed_addon_licenses';
	const CRON_HOOK  = 'fed_cron_license_weekly_check';

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Private singleton constructor
	}

	/**
	 * Register hooks with HookLoader or WordPress.
	 *
	 * @param \FED\Hooks\HookLoader|null $loader
	 */
	public function register_hooks( $loader = null ) {
		// Initialize auto-updater
		PluginUpdater::instance()->register_hooks();

		// Schedule weekly background check cron if not scheduled
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', self::CRON_HOOK );
		}

		add_action( self::CRON_HOOK, array( $this, 'cron_refresh_all_licenses' ) );

		// Register AJAX endpoints
		add_action( 'wp_ajax_fed_activate_addon_license', array( $this, 'ajax_activate_license' ) );
		add_action( 'wp_ajax_fed_deactivate_addon_license', array( $this, 'ajax_deactivate_license' ) );
		add_action( 'wp_ajax_fed_refresh_addon_license', array( $this, 'ajax_refresh_license' ) );
		add_action( 'wp_ajax_fed_check_all_updates', array( $this, 'ajax_check_all_updates' ) );

		// Register Dynamic Submenu
		LicenseAdminController::register_menu();
	}

	/**
	 * Get all stored license records.
	 *
	 * @return array
	 */
	public function get_licenses() {
		$licenses = get_option( self::OPTION_KEY, array() );
		return is_array( $licenses ) ? $licenses : array();
	}

	/**
	 * Get license record for a specific addon slug.
	 *
	 * @param string $slug
	 * @return array|null
	 */
	public function get_license( $slug ) {
		$licenses = $this->get_licenses();
		return isset( $licenses[ $slug ] ) ? $licenses[ $slug ] : null;
	}

	/**
	 * Check if a specific addon has an active license.
	 *
	 * @param string $slug
	 * @return bool
	 */
	public function is_active( $slug ) {
		$license = $this->get_license( $slug );
		return ! empty( $license['key'] ) && 'active' === ( ! empty( $license['status'] ) ? $license['status'] : '' );
	}

	/**
	 * Activate an addon license key.
	 *
	 * @param string $slug
	 * @param string $key
	 * @return array
	 */
	public function activate_license( $slug, $key ) {
		$slug = sanitize_key( $slug );
		$key  = sanitize_text_field( trim( $key ) );

		$addon = LicenseRegistry::get_addon( $slug );
		if ( ! $addon ) {
			return array(
				'success' => false,
				'message' => __( 'Unregistered or invalid addon requested.', 'frontend-dashboard' ),
			);
		}

		if ( empty( $key ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please enter a valid license key or transaction ID.', 'frontend-dashboard' ),
			);
		}

		$response = LicenseApiClient::activate( $key, $slug );
		$licenses = $this->get_licenses();

		if ( ! empty( $response['success'] ) ) {
			$licenses[ $slug ] = array(
				'key'          => $key,
				'status'       => 'active',
				'message'      => ! empty( $response['message'] ) ? $response['message'] : __( 'License activated successfully.', 'frontend-dashboard' ),
				'activated_at' => time(),
				'last_checked' => time(),
				'domain'       => LicenseApiClient::get_site_domain(),
			);
			update_option( self::OPTION_KEY, $licenses );

			// Clear update transients
			delete_site_transient( 'update_plugins' );

			return array(
				'success' => true,
				'message' => $licenses[ $slug ]['message'],
				'license' => $licenses[ $slug ],
			);
		}

		// Failed activation
		$licenses[ $slug ] = array(
			'key'          => $key,
			'status'       => 'invalid',
			'message'      => ! empty( $response['message'] ) ? $response['message'] : __( 'License validation failed.', 'frontend-dashboard' ),
			'last_checked' => time(),
			'domain'       => LicenseApiClient::get_site_domain(),
		);
		update_option( self::OPTION_KEY, $licenses );

		return array(
			'success' => false,
			'message' => $licenses[ $slug ]['message'],
			'license' => $licenses[ $slug ],
		);
	}

	/**
	 * Deactivate an addon license key.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function deactivate_license( $slug ) {
		$slug    = sanitize_key( $slug );
		$license = $this->get_license( $slug );
		$key     = ! empty( $license['key'] ) ? $license['key'] : '';

		if ( ! empty( $key ) ) {
			LicenseApiClient::deactivate( $key, $slug );
		}

		$licenses = $this->get_licenses();
		unset( $licenses[ $slug ] );
		update_option( self::OPTION_KEY, $licenses );

		// Clear update transients
		delete_site_transient( 'update_plugins' );

		return array(
			'success' => true,
			'message' => __( 'License deactivated successfully.', 'frontend-dashboard' ),
		);
	}

	/**
	 * Refresh verification status of an addon.
	 *
	 * @param string $slug
	 * @return array
	 */
	public function refresh_license( $slug ) {
		$license = $this->get_license( $slug );
		if ( empty( $license['key'] ) ) {
			return array(
				'success' => false,
				'message' => __( 'No license key configured for this addon.', 'frontend-dashboard' ),
			);
		}

		return $this->activate_license( $slug, $license['key'] );
	}

	/**
	 * Weekly cron worker silently refreshing all active licenses.
	 */
	public function cron_refresh_all_licenses() {
		$addons   = LicenseRegistry::get_registered_addons();
		$licenses = $this->get_licenses();

		foreach ( $addons as $slug => $addon ) {
			if ( ! empty( $licenses[ $slug ]['key'] ) && 'active' === ( ! empty( $licenses[ $slug ]['status'] ) ? $licenses[ $slug ]['status'] : '' ) ) {
				$this->activate_license( $slug, $licenses[ $slug ]['key'] );
			}
		}
	}

	/**
	 * AJAX Handler: Activate License
	 */
	public function ajax_activate_license() {
		check_ajax_referer( 'fed_license_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ), 403 );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';
		$key  = isset( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : '';

		$result = $this->activate_license( $slug, $key );

		if ( ! empty( $result['success'] ) ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * AJAX Handler: Deactivate License
	 */
	public function ajax_deactivate_license() {
		check_ajax_referer( 'fed_license_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ), 403 );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';

		$result = $this->deactivate_license( $slug );
		wp_send_json_success( $result );
	}

	/**
	 * AJAX Handler: Refresh Single License
	 */
	public function ajax_refresh_license() {
		check_ajax_referer( 'fed_license_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ), 403 );
		}

		$slug   = isset( $_POST['slug'] ) ? sanitize_key( $_POST['slug'] ) : '';
		$result = $this->refresh_license( $slug );

		if ( ! empty( $result['success'] ) ) {
			wp_send_json_success( $result );
		} else {
			wp_send_json_error( $result );
		}
	}

	/**
	 * AJAX Handler: Check All Updates
	 */
	public function ajax_check_all_updates() {
		check_ajax_referer( 'fed_license_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ), 403 );
		}

		delete_site_transient( 'update_plugins' );
		wp_update_plugins();

		wp_send_json_success( array(
			'message' => __( 'Plugin update check completed.', 'frontend-dashboard' ),
		) );
	}
}
