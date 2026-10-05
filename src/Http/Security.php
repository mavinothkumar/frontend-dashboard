<?php

namespace FED\Http;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Security
 *
 * Security, authentication, and authorization guard.
 */
class Security {

	/**
	 * Verify an incoming WordPress nonce.
	 *
	 * @param string $nonce
	 * @param string $action
	 * @return bool
	 */
	public static function verifyNonce( $nonce, $action = 'fed_nonce' ) {
		return (bool) wp_verify_nonce( $nonce, $action );
	}

	/**
	 * Create a new WordPress nonce.
	 *
	 * @param string $action
	 * @return string
	 */
	public static function createNonce( $action = 'fed_nonce' ) {
		return wp_create_nonce( $action );
	}

	/**
	 * Check if current visitor is authenticated.
	 *
	 * @return bool
	 */
	public static function isAuthenticated() {
		return is_user_logged_in();
	}

	/**
	 * Get current logged in user.
	 *
	 * @return \WP_User|null
	 */
	public static function currentUser() {
		return is_user_logged_in() ? wp_get_current_user() : null;
	}

	/**
	 * Get current logged in user ID.
	 *
	 * @return int
	 */
	public static function currentUserId() {
		return get_current_user_id();
	}

	/**
	 * Check if current user has capability.
	 *
	 * @param string $capability
	 * @return bool
	 */
	public static function can( $capability ) {
		return current_user_can( $capability );
	}

	/**
	 * Check if current user is an administrator.
	 *
	 * @return bool
	 */
	public static function isAdmin() {
		return current_user_can( 'administrator' );
	}

	/**
	 * Enforce authentication and capability or throw an error.
	 *
	 * @param string $capability
	 * @throws \Exception
	 */
	public static function authorize( $capability = 'read' ) {
		if ( ! self::isAuthenticated() ) {
			Response::error( 'Unauthorized. Please log in.', 401 );
		}

		if ( ! self::can( $capability ) ) {
			Response::error( 'Forbidden. Insufficient permissions.', 403 );
		}
	}
}
