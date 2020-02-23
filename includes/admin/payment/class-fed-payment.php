<?php
/**
 * Payment.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDPayment' ) ) {
	/**
	 * Class FEDPayments
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
			$settings = get_option( 'fed_payment_settings' );
			$array    = array(
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
					'Enable Payment' => array(
						'col'          => 'col-md-7',
						'name'         => __( 'Gateway', 'frontend-dashboard' ),
						'input'        =>
							fed_get_input_details(
								array(
									'input_meta'  => 'settings[gateway]',
									'user_value'  => isset( $settings['settings']['gateway'] ) ? esc_attr(
										$settings['settings']['gateway']
									) : 'disable',
									'input_type'  => 'radio',
									'class_name'  => 'm-r-10',
									'input_value' => fed_get_payment_gateways(),
								)
							),
						'help_message' => fed_show_help_message(
							array(
								'content' => __(
									'By Checking this, you are enabling the Payment',
									'frontend-dashboard'
								),
							)
						),
					),
				),
			);

			return apply_filters( 'fed_payment_settings', $array, $settings );
		}

		/**
		 * @param $menu
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
		 * Update.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {

			$this->authorize();

			$this->validation();

			$settings = get_option( 'fed_payment_settings' );

			$settings['settings']['gateway'] = isset( $request['settings']['gateway'] ) ? fed_sanitize_text_field(
				$request['settings']['gateway']
			) : 'disable';

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
						'Sorry! You are not allowed to do this action | Error: FED|Admin|Payment|FEDPayment@authorize'
					)
				);
			}

		}
	}

	new FEDPayment();
}
