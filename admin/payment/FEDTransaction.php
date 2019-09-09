<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FEDTransaction')) {
    /**
     * Class FEDTransaction
     */
    class FEDTransaction
    {
        public function __construct()
        {
            add_action('plugins_loaded', array($this, 'authorize'));
        }

        public function transactions()
        {
            /**
             * Payment Gateways
             */

            echo do_shortcode('[fed_transactions]');
        }

        public function authorize()
        {
            if ( ! is_user_logged_in()) {
                wp_die(__('Error 403: Please login to view this page'));
            }
        }

    }

    new FEDTransaction();
}