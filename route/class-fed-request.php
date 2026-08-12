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

		private $default_methods;
		private $default_functions;

		/**
		 * FED_Requests constructor.
		 */
		public function __construct() {
			$this->get_default_methods();
			$this->get_default_functions();
			add_action( 'wp_ajax_fed_ajax_request', array( $this, 'ajax_request' ) );
			add_action( 'wp_ajax_fed_api_ajax_request', array( $this, 'ajax_api_request' ) );
			add_action( 'admin_post_fed_request', array( $this, 'request' ) );
			add_action( 'admin_post_fed_api_request', array( $this, 'api_request' ) );
		}

		/**
		 * Ajax request.
		 */
		public function ajax_request() {
			$request = fed_sanitize_text_field( $_REQUEST );

			/**
			 * Verify Nonce
			 */
			fed_verify_nonce( $request );

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) && in_array( $request['fed_action_hook'], $this->default_methods, true ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
			}
			if (
				isset( $request['fed_action_hook_fn'] ) &&
				! empty( $request['fed_action_hook_fn'] ) &&
				is_string( $request['fed_action_hook_fn'] ) &&
				in_array( $request['fed_action_hook_fn'], $this->default_functions, true )
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
			$request = fed_sanitize_text_field( $_REQUEST );

			/**
			 * Verify Nonce
			 */
			fed_verify_nonce( $request );

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) && in_array( $request['fed_action_hook'], $this->default_methods, true ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
			}
			if (
				isset( $request['fed_action_hook_fn'] ) &&
				! empty( $request['fed_action_hook_fn'] ) &&
				is_string( $request['fed_action_hook_fn'] ) &&
				in_array( $request['fed_action_hook_fn'], $this->default_functions, true )
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
			$request = fed_sanitize_text_field( $_REQUEST );

			do_action( 'fed_before_api_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) && in_array( $request['fed_action_hook'], $this->default_methods, true ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
				exit();
			}
			if (
				isset( $request['fed_action_hook_fn'] ) &&
				! empty( $request['fed_action_hook_fn'] ) &&
				is_string( $request['fed_action_hook_fn'] ) &&
				in_array( $request['fed_action_hook_fn'], $this->default_functions, true )
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
		 * API Request.
		 */
		public function ajax_api_request() {
			$request = fed_sanitize_text_field( $_REQUEST );

			do_action( 'fed_before_ajax_request_action_hook_call', $request );

			if ( isset( $request['fed_action_hook'] ) && in_array( $request['fed_action_hook'], $this->default_methods, true ) ) {
				fed_execute_method_by_string( urldecode( $request['fed_action_hook'] ), $request );
			}
			if (
				isset( $request['fed_action_hook_fn'] ) &&
				! empty( $request['fed_action_hook_fn'] ) &&
				is_string( $request['fed_action_hook_fn'] ) &&
				in_array( $request['fed_action_hook_fn'], $this->default_functions, true )
			) {
				fed_ajax_call_function_method(
					array(
						'callable'  => $request['fed_action_hook_fn'],
						'arguments' => $request,
					)
				);
			}

			do_action( 'fed_after_ajax_request_action_hook_call', $request );

			wp_send_json_error( array( 'message' => 'Invalid Request - FED|route|FED_Requests@ajax_api_request' ) );
		}

		private function get_default_methods() {
			$default_methods       = array(
				'FEDEmail@update',
				'FEDEmail@update_smtp',
				'FEDInstallAddons@activate',
				'FEDInstallAddons@install',
				'FEDInvoiceTemplate@update',
				'FEDInvoice',
				'FEDInvoice@store_user',
				'FEDPayment',
				'FEDTransaction@add_new_item',
				'FEDTransaction@items',
				'FEDInvoice@download',
				'FEDTransaction@update',
				'FEDTransaction@add_items',
			);
			$this->default_methods = apply_filters( 'fed_request_default_methods', $default_methods );
		}

		private function get_default_functions() {
			$default_functions       = array(
				'fed_admin_frontend_login_menu_save',
			);
			$this->default_functions = apply_filters( 'fed_request_default_functions', $default_functions );
		}

	}

	new FED_Requests();
}
