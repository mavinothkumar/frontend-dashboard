<?php
/**
 * Request.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FED_Requests' ) ) {
	/**
	 * Class FED_Requests
	 */
	class FED_Requests {
		/**
		 * FED_Requests constructor.
		 */
		public function __construct() {
			add_action( 'wp_ajax_fed_ajax_request', array( $this, 'ajax_request' ) );
			// Security: Do not expose unauthenticated nopriv hook for fed_ajax_request
			add_action( 'wp_ajax_fed_api_ajax_request', array( $this, 'ajax_api_request' ) );
			// Security: Do not expose unauthenticated nopriv hook for fed_api_ajax_request
			add_action( 'admin_post_fed_request', array( $this, 'request' ) );
			// Security: Do not expose unauthenticated nopriv hook for fed_request
			add_action( 'admin_post_fed_api_request', array( $this, 'api_request' ) );
			// Security: Do not expose unauthenticated nopriv hook for fed_api_request
		}

		/**
		 * Ajax request.
		 */
		public function ajax_request() {
			if ( ! is_user_logged_in() ) {
				wp_send_json_error( array( 'message' => __( 'Unauthorized Request', 'frontend-dashboard' ) ), 401 );
				exit();
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = fed_sanitize_text_field( $_REQUEST );

			/**
			 * Verify Nonce
			 */
			fed_verify_nonce( $request );

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
			}
			if (
				isset( $request['fed_action_hook_fn'] ) && ! empty( $request['fed_action_hook_fn'] ) && is_string(
					$request['fed_action_hook_fn']
				)
			) {
				fed_ajax_call_function_method(
					array(
						'callable'  => $request['fed_action_hook_fn'],
						'arguments' => $request,
					)
				);
			}

			do_action( 'fed_after_ajax_request_action_hook_call', $request );

			wp_send_json_error( array( 'message' => 'Invalid Request - FED|route|FED_Requests@ajax_request' ) );
		}

		/**
		 * Request.
		 */
		public function request() {
			if ( ! is_user_logged_in() ) {
				wp_die( esc_html__( 'Unauthorized Request', 'frontend-dashboard' ), 401 );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = fed_sanitize_text_field( $_REQUEST );

			/**
			 * Verify Nonce
			 */
			fed_verify_nonce( $request );

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
			}
			if (
				isset( $request['fed_action_hook_fn'] ) && ! empty( $request['fed_action_hook_fn'] ) && is_string(
					$request['fed_action_hook_fn']
				)
			) {
				fed_ajax_call_function_method(
					array(
						'callable'  => $request['fed_action_hook_fn'],
						'arguments' => $request,
					)
				);
			}

			do_action( 'fed_after_ajax_request_action_hook_call', $request );

			wp_send_json_error( array( 'message' => 'Invalid Request - FED|route|FED_Requests@request' ) );
		}

		/**
		 * API Request.
		 */
		public function api_request() {
			if ( ! is_user_logged_in() ) {
				wp_die( esc_html__( 'Unauthorized Request', 'frontend-dashboard' ), 401 );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = fed_sanitize_text_field( $_REQUEST );

			if ( isset( $request['fed_action_hook'] ) && 'FEDInstallAddons@install' === $request['fed_action_hook'] ) {
				check_admin_referer( 'updates' );
			} else {
				fed_verify_nonce( $request );
			}

			do_action( 'fed_before_api_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
				exit();
			}
			if (
				isset( $request['fed_action_hook_fn'] ) && ! empty( $request['fed_action_hook_fn'] ) && is_string(
					$request['fed_action_hook_fn']
				)
			) {
				fed_ajax_call_function_method(
					array(
						'callable'  => $request['fed_action_hook_fn'],
						'arguments' => $request,
					)
				);
				exit();
			}

			do_action( 'fed_after_api_request_action_hook_call', $request );

			wp_send_json_error( array( 'message' => 'Invalid Request - FED|route|FED_Requests@api_request ' ) );
		}

		/**
		 * Ajax API Request.
		 */
		public function ajax_api_request() {
			if ( ! is_user_logged_in() ) {
				wp_send_json_error( array( 'message' => __( 'Unauthorized Request', 'frontend-dashboard' ) ), 401 );
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request = fed_sanitize_text_field( $_REQUEST );

			if ( isset( $request['fed_action_hook'] ) && 'FEDInstallAddons@install' === $request['fed_action_hook'] ) {
				check_ajax_referer( 'updates' );
			} else {
				fed_verify_nonce( $request );
			}

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
				exit();
			}
			if (
				isset( $request['fed_action_hook_fn'] ) && ! empty( $request['fed_action_hook_fn'] ) && is_string(
					$request['fed_action_hook_fn']
				)
			) {
				fed_ajax_call_function_method(
					array(
						'callable'  => $request['fed_action_hook_fn'],
						'arguments' => $request,
					)
				);
				exit();
			}

			do_action( 'fed_after_ajax_request_action_hook_call', $request );

			wp_send_json_error( array( 'message' => 'Invalid Request - FED|route|FED_Requests@ajax_api_request' ) );
		}
	}

	new FED_Requests();
}