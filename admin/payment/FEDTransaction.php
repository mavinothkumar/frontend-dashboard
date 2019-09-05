<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists( 'FEDTransaction' ) ) {
	/**
	 * Class FEDTransaction
	 */
	class FEDTransaction {
		public function __construct() {

		}

		public function transactions() {
			/**
             * Payment Gateways
             */

			echo 'Transactions Under Construction';
		}

	}

	new FEDTransaction();
}