<?php

namespace FED\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PluginUpdater
 *
 * Integrates licensed Pro addons with WordPress native plugin update checks and information popups.
 */
class PluginUpdater {

	/**
	 * @var PluginUpdater|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return PluginUpdater
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register updater hooks with WordPress.
	 */
	public function register_hooks() {
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_updates' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info_popup' ), 20, 3 );
	}

	/**
	 * Hook: pre_set_site_transient_update_plugins
	 *
	 * Checks BufferCode API for updates on all registered Pro addons with active licenses.
	 *
	 * @param object $transient
	 * @return object
	 */
	public function check_for_updates( $transient ) {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		$addons = LicenseRegistry::get_registered_addons();
		if ( empty( $addons ) ) {
			return $transient;
		}

		$licenses = LicenseManager::instance()->get_licenses();

		foreach ( $addons as $slug => $addon ) {
			$basename = ! empty( $addon['basename'] ) ? $addon['basename'] : '';
			if ( empty( $basename ) ) {
				continue;
			}

			$license = isset( $licenses[ $slug ] ) ? $licenses[ $slug ] : null;
			$tx_id   = ! empty( $license['key'] ) ? $license['key'] : '';
			$status  = ! empty( $license['status'] ) ? $license['status'] : 'inactive';

			if ( empty( $tx_id ) || 'active' !== $status ) {
				continue;
			}

			$current_version = isset( $transient->checked[ $basename ] ) ? $transient->checked[ $basename ] : $addon['version'];

			// Cache key for update check per addon
			$cache_key = 'fed_update_check_' . md5( $slug . $current_version . $tx_id );
			$cached_info = get_transient( $cache_key );

			if ( false === $cached_info ) {
				$response = LicenseApiClient::check_version( $tx_id, $slug, $current_version );
				if ( ! empty( $response['success'] ) && ! empty( $response['data'] ) ) {
					$cached_info = $response['data'];
					set_transient( $cache_key, $cached_info, 12 * HOUR_IN_SECONDS );
				} else {
					set_transient( $cache_key, 'no_update', 4 * HOUR_IN_SECONDS );
					continue;
				}
			}

			if ( is_array( $cached_info ) && ! empty( $cached_info['new_version'] ) ) {
				if ( version_compare( $current_version, $cached_info['new_version'], '<' ) ) {
					$item = (object) array(
						'id'            => 'buffercode/' . $slug,
						'slug'          => $slug,
						'plugin'        => $basename,
						'new_version'   => $cached_info['new_version'],
						'url'           => isset( $cached_info['url'] ) ? $cached_info['url'] : $addon['purchase_url'],
						'package'       => isset( $cached_info['package'] ) ? $cached_info['package'] : '',
						'tested'        => isset( $cached_info['tested'] ) ? $cached_info['tested'] : '',
						'requires_php'  => '7.4',
						'compatibility' => new \stdClass(),
					);

					$transient->response[ $basename ] = $item;
				}
			}
		}

		return $transient;
	}

	/**
	 * Hook: plugins_api
	 *
	 * Intercepts plugin information modal queries for registered Pro addons.
	 *
	 * @param false|object|array $result
	 * @param string $action
	 * @param object $args
	 * @return false|object
	 */
	public function plugin_info_popup( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || empty( $args->slug ) ) {
			return $result;
		}

		$addons = LicenseRegistry::get_registered_addons();
		if ( ! isset( $addons[ $args->slug ] ) ) {
			return $result;
		}

		$addon    = $addons[ $args->slug ];
		$licenses = LicenseManager::instance()->get_licenses();
		$license  = isset( $licenses[ $args->slug ] ) ? $licenses[ $args->slug ] : null;
		$tx_id    = ! empty( $license['key'] ) ? $license['key'] : '';

		$api_res = LicenseApiClient::get_plugin_info( $tx_id, $args->slug );

		if ( ! empty( $api_res['success'] ) && ! empty( $api_res['data'] ) ) {
			$data = $api_res['data'];

			$info = new \stdClass();
			$info->name           = isset( $data['name'] ) ? $data['name'] : $addon['name'];
			$info->slug           = $args->slug;
			$info->version        = isset( $data['new_version'] ) ? $data['new_version'] : $addon['version'];
			$info->author         = '<a href="https://buffercode.com">BufferCode</a>';
			$info->homepage       = isset( $data['url'] ) ? $data['url'] : $addon['purchase_url'];
			$info->requires       = isset( $data['requires'] ) ? $data['requires'] : '5.8';
			$info->tested         = isset( $data['tested'] ) ? $data['tested'] : '6.7';
			$info->last_updated   = isset( $data['last_updated'] ) ? $data['last_updated'] : gmdate( 'Y-m-d' );
			$info->download_link  = isset( $data['download_link'] ) ? $data['download_link'] : ( isset( $data['package'] ) ? $data['package'] : '' );
			$info->sections       = isset( $data['sections'] ) && is_array( $data['sections'] ) ? $data['sections'] : array(
				'description' => isset( $addon['name'] ) ? '<p>' . esc_html( $addon['name'] ) . '</p>' : '',
				'changelog'   => '<p>' . __( 'Visit BufferCode for changelog details.', 'frontend-dashboard' ) . '</p>',
			);

			return $info;
		}

		return $result;
	}
}
