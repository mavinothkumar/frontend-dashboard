<?php

namespace FED\Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Response
 *
 * Standardized HTTP / AJAX response builder.
 */
class Response {

	/**
	 * Send a JSON response and exit.
	 *
	 * @param mixed $data
	 * @param int   $status
	 */
	public static function json( $data, $status = 200 ) {
		status_header( $status );
		wp_send_json( $data, $status );
	}

	/**
	 * Send a success response.
	 *
	 * @param mixed  $data
	 * @param string $message
	 * @param int    $status
	 */
	public static function success( $data = null, $message = '', $status = 200 ) {
		status_header( $status );
		wp_send_json_success( [
			'message' => $message,
			'data'    => $data,
		], $status );
	}

	/**
	 * Send an error response.
	 *
	 * @param string $message
	 * @param int    $status
	 * @param array  $errors
	 */
	public static function error( $message = 'Something went wrong', $status = 400, array $errors = [] ) {
		status_header( $status );
		wp_send_json_error( [
			'message' => $message,
			'errors'  => $errors,
		], $status );
	}

	/**
	 * Redirect to another URL.
	 *
	 * @param string $url
	 * @param int    $status
	 */
	public static function redirect( $url, $status = 302 ) {
		wp_safe_redirect( $url, $status );
		exit;
	}
}
