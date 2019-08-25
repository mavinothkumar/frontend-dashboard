<?php
if ( ! class_exists( 'FEDPayment' ) ) {
	/**
	 * Class FEDPayments
	 */
	class FEDPayment {
		public function __construct() {

		}

		public function gateway() {
			/**
             * Payment Gateways
             */

			echo 'Gateway Under Construction';
		}
		public function paypal() {
			/**
             * Payment Gateways
             */

			echo 'paypal Under Construction';
		}
		public function stripe() {
			/**
             * Payment Gateways
             */

			echo 'stripe Under Construction';
		}


	}

	new FEDPayment();
}