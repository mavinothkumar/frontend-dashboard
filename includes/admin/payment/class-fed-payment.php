<?php
/**
 * Payment Gateway Configuration & Core Settings.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDPayment' ) ) {
	/**
	 * Class FEDPayment
	 */
	class FEDPayment {

		/**
		 * FEDPayment constructor.
		 */
		public function __construct() {
			add_filter( 'fed_add_main_sub_menu', array( $this, 'main_sub_menu' ) );
		}

		/**
		 * Settings.
		 */
		public function settings() {
			$settings = $this->settings_data();
			fed_common_simple_layout( $settings );
		}

		/**
		 * Setting Data
		 *
		 * @return mixed|void.
		 *
		 * Options => fed_payment_settings
		 */
		public function settings_data() {
			$settings = get_option( 'fed_payment_settings', array() );
			$currencies = function_exists( 'fed_get_payment_currencies' ) ? fed_get_payment_currencies() : array( 'USD' => 'USD - US Dollar ($)' );
			$gateways = function_exists( 'fed_get_payment_gateways' ) ? fed_get_payment_gateways() : array( 'disable' => __( 'Disable', 'frontend-dashboard' ) );

			$array = array(
				'form'  => array(
					'method' => '',
					'class'  => 'fed_admin_menu fed_ajax',
					'attr'   => '',
					'action' => array(
						'url'        => '',
						'action'     => 'fed_ajax_request',
						'parameters' => array(
							'fed_action_hook' => 'FEDPayment',
						),
					),
					'nonce'  => array(
						'action' => '',
						'name'   => '',
					),
					'loader' => '',
				),
				'input' => array(
					'Default Payment Gateway' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Default Payment Gateway', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_meta'  => 'settings[gateway]',
								'user_value'  => isset( $settings['settings']['gateway'] ) ? esc_attr( $settings['settings']['gateway'] ) : 'disable',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
								'input_value' => $gateways,
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Select the primary payment gateway for checkout processing.', 'frontend-dashboard' ),
							)
						),
					),
					'Environment Mode' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Environment Mode', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_meta'  => 'settings[test_mode]',
								'user_value'  => isset( $settings['settings']['test_mode'] ) ? esc_attr( $settings['settings']['test_mode'] ) : 'enable',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
								'input_value' => array(
									'disable' => __( 'Live Mode (Production)', 'frontend-dashboard' ),
									'enable'  => __( 'Sandbox / Test Mode (Simulation)', 'frontend-dashboard' ),
								),
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Enable Sandbox mode while testing payment flows before going live.', 'frontend-dashboard' ),
							)
						),
					),
					'Currency Code' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Payment Currency', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_meta'  => 'settings[currency]',
								'user_value'  => isset( $settings['settings']['currency'] ) ? esc_attr( $settings['settings']['currency'] ) : 'USD',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
								'input_value' => $currencies,
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Currency used for all billing plans, checkouts, and invoices.', 'frontend-dashboard' ),
							)
						),
					),
					'Currency Position' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Currency Symbol Position', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_meta'  => 'settings[currency_position]',
								'user_value'  => isset( $settings['settings']['currency_position'] ) ? esc_attr( $settings['settings']['currency_position'] ) : 'left',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
								'input_value' => array(
									'left'        => __( 'Left ($99.00)', 'frontend-dashboard' ),
									'right'       => __( 'Right (99.00$)', 'frontend-dashboard' ),
									'left_space'  => __( 'Left with space ($ 99.00)', 'frontend-dashboard' ),
									'right_space' => __( 'Right with space (99.00 $)', 'frontend-dashboard' ),
								),
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Position of the currency symbol next to the price amounts.', 'frontend-dashboard' ),
							)
						),
					),
					'Payment Success Page' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Payment Success Page', 'frontend-dashboard' ),
						'input'        => wp_dropdown_pages(
							array(
								'name'             => 'settings[success_page]',
								'selected'         => isset( $settings['settings']['success_page'] ) ? $settings['settings']['success_page'] : '',
								'show_option_none' => __( '&mdash; Default Dashboard Page &mdash;', 'frontend-dashboard' ),
								'class'            => 'form-control',
								'echo'             => false,
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'The page customers are redirected to after a successful transaction.', 'frontend-dashboard' ),
							)
						),
					),
					'Payment Cancelled Page' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Payment Cancelled Page', 'frontend-dashboard' ),
						'input'        => wp_dropdown_pages(
							array(
								'name'             => 'settings[failed_page]',
								'selected'         => isset( $settings['settings']['failed_page'] ) ? $settings['settings']['failed_page'] : '',
								'show_option_none' => __( '&mdash; Default Dashboard Page &mdash;', 'frontend-dashboard' ),
								'class'            => 'form-control',
								'echo'             => false,
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'The page customers are redirected to if they cancel the checkout.', 'frontend-dashboard' ),
							)
						),
					),
				),
			);

			return apply_filters( 'fed_payment_settings', $array, $settings );
		}

		/**
		 * @param array $menu
		 *
		 * @return array
		 */
		public function main_sub_menu( $menu ) {
			$menu['fed_payments'] = array(
				'page_title' => __( 'Payments', 'frontend-dashboard' ),
				'menu_title' => __( 'Payments', 'frontend-dashboard' ),
				'capability' => 'manage_options',
				'callback'   => array( new FEDPaymentMenu(), 'index' ),
				'position'   => 30,
			);

			return $menu;
		}

		/**
		 * Update Settings.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {
			$this->authorize();
			$this->validation();

			$settings = get_option( 'fed_payment_settings', array() );

			if ( ! isset( $settings['settings'] ) || ! is_array( $settings['settings'] ) ) {
				$settings['settings'] = array();
			}

			$settings['settings']['gateway']           = isset( $request['settings']['gateway'] ) ? fed_sanitize_text_field( $request['settings']['gateway'] ) : 'disable';
			$settings['settings']['test_mode']         = isset( $request['settings']['test_mode'] ) ? fed_sanitize_text_field( $request['settings']['test_mode'] ) : 'enable';
			$settings['settings']['currency']          = isset( $request['settings']['currency'] ) ? fed_sanitize_text_field( $request['settings']['currency'] ) : 'USD';
			$settings['settings']['currency_position'] = isset( $request['settings']['currency_position'] ) ? fed_sanitize_text_field( $request['settings']['currency_position'] ) : 'left';
			$settings['settings']['success_page']      = isset( $request['settings']['success_page'] ) ? fed_sanitize_text_field( $request['settings']['success_page'] ) : '';
			$settings['settings']['failed_page']       = isset( $request['settings']['failed_page'] ) ? fed_sanitize_text_field( $request['settings']['failed_page'] ) : '';

			update_option( 'fed_payment_settings', $settings );

			wp_send_json_success(
				array( 'message' => __( 'Payment Settings Successfully Saved', 'frontend-dashboard' ) )
			);
		}

		/**
		 * Validation.
		 */
		private function validation() {
			$validate = new FED_Validation();

			$validate->name( 'Payment Gateway' )->value( fed_get_data( 'settings.gateway' ) )->required();

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}
		}

		/**
		 * Authorize.
		 */
		public function authorize() {
			if ( ! fed_is_admin() ) {
				wp_die(
					__(
						'Sorry! You are not allowed to do this action | Error: FED|Admin|Payment|FEDPayment@authorize',
						'frontend-dashboard'
					)
				);
			}
		}
	}

	new FEDPayment();
}

