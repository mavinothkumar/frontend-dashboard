<?php

namespace FED\Controllers\Auth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AuthController
 *
 * Handles login, registration, and password reset requests.
 */
class AuthController {

	public function register_hooks( $loader ) {
		$loader->add_action( 'wp_ajax_fed_login_form_post', $this, 'handle_auth_request' );
		$loader->add_action( 'wp_ajax_nopriv_fed_login_form_post', $this, 'handle_auth_request' );
	}

	public function handle_auth_request() {
		$post_payload = \FED\Helpers\InputHelper::post();

		if ( function_exists('fed_verify_nonce') ) {
			fed_verify_nonce();
		}

		if ( isset( $post_payload['submit'] ) ) {
			switch ( $post_payload['submit'] ) {
				case 'login':
					if ( function_exists('fed_login_form_submit') ) fed_login_form_submit( $post_payload );
					break;
				case 'register':
					if ( function_exists('fed_register_form_submit') ) fed_register_form_submit( $post_payload );
					break;
				case 'forgot_password':
					if ( function_exists('fed_forgot_form_submit') ) fed_forgot_form_submit( $post_payload );
					break;
				case 'reset_password':
					if ( function_exists('fed_reset_form_submit') ) fed_reset_form_submit( $post_payload );
					break;
				default:
					do_action( 'fed_login_form_submit_custom' );
					break;
			}
		}
		
		wp_die();
	}
}
