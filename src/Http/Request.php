<?php

namespace FED\Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Request
 *
 * Immutable, safely sanitized HTTP Request object.
 */
class Request {

	/**
	 * @var array Query parameters ($_GET).
	 */
	protected $query;

	/**
	 * @var array Post parameters ($_POST).
	 */
	protected $request;

	/**
	 * @var array Server parameters ($_SERVER).
	 */
	protected $server;

	/**
	 * @var array Uploaded files ($_FILES).
	 */
	protected $files;

	/**
	 * Request constructor.
	 *
	 * @param array $query
	 * @param array $request
	 * @param array $server
	 * @param array $files
	 */
	public function __construct( array $query = [], array $request = [], array $server = [], array $files = [] ) {
		$this->query   = $this->sanitize_array( $query );
		$this->request = $this->sanitize_array( $request );
		$this->server  = $server;
		$this->files   = $files;
	}

	/**
	 * Capture current request from PHP globals.
	 *
	 * @return static
	 */
	public static function capture() {
		return new static(
			$_GET,
			$_POST,
			$_SERVER,
			$_FILES
		);
	}

	/**
	 * Recursively sanitize an array of inputs.
	 *
	 * @param array $data
	 * @return array
	 */
	protected function sanitize_array( array $data ) {
		return map_deep( $data, function( $value ) {
			if ( is_string( $value ) ) {
				return sanitize_text_field( wp_unslash( $value ) );
			}
			return $value;
		} );
	}

	/**
	 * Retrieve a query parameter ($_GET).
	 *
	 * @param string|null $key
	 * @param mixed       $default
	 * @return mixed
	 */
	public function get( $key = null, $default = null ) {
		if ( is_null( $key ) ) {
			return $this->query;
		}
		return isset( $this->query[ $key ] ) ? $this->query[ $key ] : $default;
	}

	/**
	 * Retrieve a post parameter ($_POST).
	 *
	 * @param string|null $key
	 * @param mixed       $default
	 * @return mixed
	 */
	public function post( $key = null, $default = null ) {
		if ( is_null( $key ) ) {
			return $this->request;
		}
		return isset( $this->request[ $key ] ) ? $this->request[ $key ] : $default;
	}

	/**
	 * Retrieve an input from POST or GET.
	 *
	 * @param string|null $key
	 * @param mixed       $default
	 * @return mixed
	 */
	public function input( $key = null, $default = null ) {
		$all = array_merge( $this->query, $this->request );
		if ( is_null( $key ) ) {
			return $all;
		}
		return isset( $all[ $key ] ) ? $all[ $key ] : $default;
	}

	/**
	 * Check if an input exists.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has( $key ) {
		$all = array_merge( $this->query, $this->request );
		return isset( $all[ $key ] );
	}

	/**
	 * Get typed integer.
	 *
	 * @param string $key
	 * @param int    $default
	 * @return int
	 */
	public function getInt( $key, $default = 0 ) {
		return (int) $this->input( $key, $default );
	}

	/**
	 * Get typed string.
	 *
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	public function getString( $key, $default = '' ) {
		return (string) $this->input( $key, $default );
	}

	/**
	 * Get typed boolean.
	 *
	 * @param string $key
	 * @param bool   $default
	 * @return bool
	 */
	public function getBool( $key, $default = false ) {
		$val = $this->input( $key, $default );
		return filter_var( $val, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Retrieve only a subset of inputs.
	 *
	 * @param array $keys
	 * @return array
	 */
	public function only( array $keys ) {
		$results = [];
		$all     = $this->input();
		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $all ) ) {
				$results[ $key ] = $all[ $key ];
			}
		}
		return $results;
	}

	/**
	 * Retrieve all inputs except specified keys.
	 *
	 * @param array $keys
	 * @return array
	 */
	public function except( array $keys ) {
		$all = $this->input();
		foreach ( $keys as $key ) {
			unset( $all[ $key ] );
		}
		return $all;
	}

	/**
	 * Check if HTTP request method matches.
	 *
	 * @param string $method
	 * @return bool
	 */
	public function isMethod( $method ) {
		$current = isset( $this->server['REQUEST_METHOD'] ) ? strtoupper( $this->server['REQUEST_METHOD'] ) : 'GET';
		return $current === strtoupper( $method );
	}

	/**
	 * Check if current request is POST.
	 *
	 * @return bool
	 */
	public function isPost() {
		return $this->isMethod( 'POST' );
	}

	/**
	 * Check if current request is GET.
	 *
	 * @return bool
	 */
	public function isGet() {
		return $this->isMethod( 'GET' );
	}

	/**
	 * Check if current request is an AJAX request.
	 *
	 * @return bool
	 */
	public function isAjax() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX )
			|| ( isset( $this->server['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' === strtolower( $this->server['HTTP_X_REQUESTED_WITH'] ) );
	}

	/**
	 * Validate current request parameters.
	 *
	 * @param array $rules
	 * @param array $messages
	 * @return Validator
	 */
	public function validate( array $rules, array $messages = [] ) {
		return Validator::make( $this->input(), $rules, $messages );
	}
}
