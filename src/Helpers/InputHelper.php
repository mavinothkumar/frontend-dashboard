<?php

namespace FED\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class InputHelper
 *
 * Modern HTTP request input sanitizer compatible with PHP 8.1+.
 */
class InputHelper {

	/**
	 * Sanitize and return $_GET input parameters.
	 *
	 * @return array
	 */
	public static function get() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$raw = ! empty( $_GET ) ? $_GET : ( filter_input_array( INPUT_GET, FILTER_DEFAULT ) ?: [] );
		return is_array( $raw ) ? map_deep( $raw, 'sanitize_textarea_field' ) : [];
	}

	/**
	 * Sanitize and return $_POST input parameters.
	 *
	 * @return array
	 */
	public static function post() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$raw = ! empty( $_POST ) ? $_POST : ( filter_input_array( INPUT_POST, FILTER_DEFAULT ) ?: [] );
		return is_array( $raw ) ? map_deep( $raw, 'sanitize_textarea_field' ) : [];
	}
}
