<?php

namespace FED\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicenseRegistry
 *
 * Decoupled registry that aggregates registered Pro addons via WordPress filters.
 * Ensures free core plugin contains ZERO hardcoded Pro plugin details.
 */
class LicenseRegistry {

	/**
	 * Cached list of registered addons
	 *
	 * @var array|null
	 */
	private static $addons = null;

	/**
	 * Get all registered Pro addons.
	 *
	 * @param bool $force_refresh
	 * @return array
	 */
	public static function get_registered_addons( $force_refresh = false ) {
		if ( null === self::$addons || $force_refresh ) {
			/**
			 * Filter: fed_registered_pro_addons
			 *
			 * Allows Pro extensions to register their metadata dynamically.
			 *
			 * @param array $addons Array of registered addon configurations.
			 */
			$raw_addons = apply_filters( 'fed_registered_pro_addons', array() );
			
			self::$addons = array();

			if ( is_array( $raw_addons ) ) {
				foreach ( $raw_addons as $key => $addon ) {
					$normalized = self::normalize_addon( $addon, $key );
					if ( $normalized ) {
						self::$addons[ $normalized['slug'] ] = $normalized;
					}
				}
			}
		}

		return self::$addons;
	}

	/**
	 * Check if any Pro addons are currently active and registered.
	 *
	 * @return bool
	 */
	public static function has_pro_addons() {
		$addons = self::get_registered_addons();
		return ! empty( $addons );
	}

	/**
	 * Get a specific registered Pro addon by slug.
	 *
	 * @param string $slug
	 * @return array|null
	 */
	public static function get_addon( $slug ) {
		$addons = self::get_registered_addons();
		return isset( $addons[ $slug ] ) ? $addons[ $slug ] : null;
	}

	/**
	 * Normalize an addon definition to ensure all required keys exist.
	 *
	 * @param array|mixed $addon
	 * @param string|int $key
	 * @return array|null
	 */
	private static function normalize_addon( $addon, $key ) {
		if ( ! is_array( $addon ) ) {
			return null;
		}

		$slug = isset( $addon['slug'] ) ? sanitize_key( $addon['slug'] ) : ( is_string( $key ) ? sanitize_key( $key ) : '' );
		if ( empty( $slug ) ) {
			return null;
		}

		$plugin_file = isset( $addon['plugin_file'] ) ? $addon['plugin_file'] : '';
		$basename    = isset( $addon['basename'] ) ? $addon['basename'] : ( $plugin_file ? plugin_basename( $plugin_file ) : '' );

		return array(
			'slug'         => $slug,
			'name'         => isset( $addon['name'] ) ? sanitize_text_field( $addon['name'] ) : $slug,
			'short_name'   => isset( $addon['short_name'] ) ? sanitize_text_field( $addon['short_name'] ) : ( isset( $addon['name'] ) ? sanitize_text_field( $addon['name'] ) : $slug ),
			'version'      => isset( $addon['version'] ) ? sanitize_text_field( $addon['version'] ) : '1.0.0',
			'icon'         => isset( $addon['icon'] ) ? $addon['icon'] : '📦',
			'plugin_file'  => $plugin_file,
			'basename'     => $basename,
			'doc_url'      => isset( $addon['doc_url'] ) ? esc_url_raw( $addon['doc_url'] ) : 'https://faq.frontenddashboard.com',
			'purchase_url' => isset( $addon['purchase_url'] ) ? esc_url_raw( $addon['purchase_url'] ) : 'https://buffercode.com',
			'renew_url'    => isset( $addon['renew_url'] ) ? esc_url_raw( $addon['renew_url'] ) : 'https://buffercode.com',
			'settings_url' => isset( $addon['settings_url'] ) ? esc_url_raw( $addon['settings_url'] ) : '',
		);
	}
}
