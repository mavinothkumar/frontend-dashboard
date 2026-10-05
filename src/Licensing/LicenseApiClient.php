<?php

namespace FED\Licensing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LicenseApiClient
 *
 * Handles HTTP client communication with BufferCode.com licensing and update endpoints.
 */
class LicenseApiClient {

	/**
	 * Base API URL
	 */
	const API_BASE_URL = 'https://buffercode.com';

	/**
	 * Request timeout in seconds
	 */
	const REQUEST_TIMEOUT = 15;

	/**
	 * Get normalized site domain for licensing API requests.
	 *
	 * @return string
	 */
	public static function get_site_domain() {
		$url  = home_url();
		$host = wp_parse_url( $url, PHP_URL_HOST );

		if ( ! empty( $host ) ) {
			return strtolower( trim( $host ) );
		}

		// Fallback clean regex
		$clean = preg_replace( '#^https?://#i', '', $url );
		$clean = untrailingslashit( explode( '/', $clean )[0] );

		return ! empty( $clean ) ? strtolower( trim( $clean ) ) : 'localhost';
	}

	/**
	 * Activate a plugin license key.
	 *
	 * Endpoint: GET https://buffercode.com/api/plugin/activate/{transaction_id}/{domain}/{plugin_name}
	 *
	 * @param string      $transaction_id License key / Transaction ID
	 * @param string      $plugin_name    Plugin slug
	 * @param string|null $domain    Custom domain override
	 * @return array
	 */
	public static function activate( $transaction_id, $plugin_name, $domain = null ) {
		$tx_id    = trim( $transaction_id );
		$slug     = trim( $plugin_name );
		$site_dom = $domain ? trim( $domain ) : self::get_site_domain();

		if ( empty( $tx_id ) || empty( $slug ) ) {
			return array(
				'success' => false,
				'message' => __( 'License key and plugin name are required.', 'frontend-dashboard' ),
			);
		}

		$endpoint = sprintf(
			'%s/api/plugin/activate/%s/%s/%s',
			self::API_BASE_URL,
			rawurlencode( $tx_id ),
			rawurlencode( $site_dom ),
			rawurlencode( $slug )
		);

		$response = wp_remote_get(
			$endpoint,
			array(
				'timeout'   => self::REQUEST_TIMEOUT,
				'sslverify' => true,
				'headers'   => array(
					'Accept'     => 'application/json',
					'User-Agent' => 'FrontendDashboard/' . ( defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.0.2' ) . '; ' . home_url(),
				),
			)
		);

		return self::parse_response( $response, __( 'Activation failed. Please check your license key and network connection.', 'frontend-dashboard' ) );
	}

	/**
	 * Deactivate a plugin license key.
	 *
	 * Endpoint: GET https://buffercode.com/api/plugin/deactivate/{transaction_id}/{domain}/{plugin_name}
	 *
	 * @param string      $transaction_id License key / Transaction ID
	 * @param string      $plugin_name    Plugin slug
	 * @param string|null $domain    Custom domain override
	 * @return array
	 */
	public static function deactivate( $transaction_id, $plugin_name, $domain = null ) {
		$tx_id    = trim( $transaction_id );
		$slug     = trim( $plugin_name );
		$site_dom = $domain ? trim( $domain ) : self::get_site_domain();

		if ( empty( $tx_id ) || empty( $slug ) ) {
			return array(
				'success' => false,
				'message' => __( 'License key and plugin name are required.', 'frontend-dashboard' ),
			);
		}

		$endpoint = sprintf(
			'%s/api/plugin/deactivate/%s/%s/%s',
			self::API_BASE_URL,
			rawurlencode( $tx_id ),
			rawurlencode( $site_dom ),
			rawurlencode( $slug )
		);

		$response = wp_remote_get(
			$endpoint,
			array(
				'timeout'   => self::REQUEST_TIMEOUT,
				'sslverify' => true,
				'headers'   => array(
					'Accept'     => 'application/json',
					'User-Agent' => 'FrontendDashboard/' . ( defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.0.2' ) . '; ' . home_url(),
				),
			)
		);

		return self::parse_response( $response, __( 'Deactivation request could not be completed.', 'frontend-dashboard' ) );
	}

	/**
	 * Check for new plugin version via V2 Enterprise API.
	 *
	 * Endpoint: POST https://buffercode.com/api/v2/plugin/update/{transaction_id}/{domain}/{plugin_name}
	 * Payload: {"action": "version", "version": "1.0.0"}
	 *
	 * @param string      $transaction_id
	 * @param string      $plugin_name
	 * @param string      $current_version
	 * @param string|null $domain
	 * @return array
	 */
	public static function check_version( $transaction_id, $plugin_name, $current_version, $domain = null ) {
		$tx_id    = trim( $transaction_id );
		$slug     = trim( $plugin_name );
		$site_dom = $domain ? trim( $domain ) : self::get_site_domain();

		$endpoint = sprintf(
			'%s/api/v2/plugin/update/%s/%s/%s',
			self::API_BASE_URL,
			rawurlencode( $tx_id ),
			rawurlencode( $site_dom ),
			rawurlencode( $slug )
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout'   => self::REQUEST_TIMEOUT,
				'sslverify' => true,
				'headers'   => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
					'User-Agent'   => 'FrontendDashboard/' . ( defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.0.2' ) . '; ' . home_url(),
				),
				'body'      => wp_json_encode(
					array(
						'action'  => 'version',
						'version' => $current_version,
					)
				),
			)
		);

		return self::parse_response( $response, __( 'Version check failed.', 'frontend-dashboard' ) );
	}

	/**
	 * Fetch detailed plugin information popup data via V2 Enterprise API.
	 *
	 * Endpoint: POST https://buffercode.com/api/v2/plugin/update/{transaction_id}/{domain}/{plugin_name}
	 * Payload: {"action": "info"}
	 *
	 * @param string      $transaction_id
	 * @param string      $plugin_name
	 * @param string|null $domain
	 * @return array
	 */
	public static function get_plugin_info( $transaction_id, $plugin_name, $domain = null ) {
		$tx_id    = trim( $transaction_id );
		$slug     = trim( $plugin_name );
		$site_dom = $domain ? trim( $domain ) : self::get_site_domain();

		$endpoint = sprintf(
			'%s/api/v2/plugin/update/%s/%s/%s',
			self::API_BASE_URL,
			rawurlencode( $tx_id ),
			rawurlencode( $site_dom ),
			rawurlencode( $slug )
		);

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout'   => self::REQUEST_TIMEOUT,
				'sslverify' => true,
				'headers'   => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
					'User-Agent'   => 'FrontendDashboard/' . ( defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.0.2' ) . '; ' . home_url(),
				),
				'body'      => wp_json_encode(
					array(
						'action' => 'info',
					)
				),
			)
		);

		return self::parse_response( $response, __( 'Could not fetch plugin information.', 'frontend-dashboard' ) );
	}

	/**
	 * Parse and standardize HTTP response from BufferCode API.
	 *
	 * @param array|\WP_Error $response
	 * @param string          $default_error
	 * @return array
	 */
	private static function parse_response( $response, $default_error ) {
		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'status'  => false,
				'message' => $response->get_error_message(),
			);
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( ! is_array( $data ) ) {
			return array(
				'success' => false,
				'status'  => false,
				'message' => $default_error . ' (HTTP ' . $code . ')',
			);
		}

		// Ensure boolean success field is established
		$is_success      = ( 200 === $code || 201 === $code ) && ( ! isset( $data['success'] ) || true === $data['success'] || ( isset( $data['status'] ) && true === $data['status'] ) );
		$data['success'] = $is_success;

		if ( ! isset( $data['message'] ) ) {
			$data['message'] = $is_success ? __( 'Request completed successfully.', 'frontend-dashboard' ) : $default_error;
		}

		return $data;
	}
}
