<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FEDPayment')) {
    /**
     * Class FEDPayments
     */
    class FEDPayment
    {
        public function __construct()
        {

        }

        public function settings()
        {
            $settings = $this->settingsData();
            fed_common_simple_layout($settings);
        }

        public function paypal()
        {
            /**
             * Payment Gateways
             */

            echo 'paypal Under Construction';
        }

        public function stripe()
        {
            /**
             * Payment Gateways
             */

            echo 'stripe Under Construction';
        }

        /**
         * @return mixed|void
         */
        public function settingsData()
        {
            /**
            option slug =>  fed_payment_settings
            array(
                'settings' => array(
                    'enable' => '' //default no
                ),
                'gateway'  => array(
                    'stripe' => array(
                        'enable'      => '',
                        'public_key'  => '',
                        'private_key' => '',
                        'success_url' => '',
                        'cancel_url' => '',
                        'notify_url' => '',
                    ),
                ),
            );
             * **/
            $settings = get_option('fed_payment_settings');
            $array    = array(
                'form'  => array(
                    'method' => '',
                    'class'  => 'fed_admin_menu fed_ajax',
                    'attr'   => '',
                    'action' => array('url' => '', 'action' => 'fed_ajax_request'),
                    'nonce'  => array('action' => '', 'name' => ''),
                    'loader' => '',
                ),
                'input' => array(
                    'Enable Payment' => array(
                        'col'          => 'col-md-7',
                        'name'         => __('Enable Payment', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'input_meta' => 'settings[enable]',
                                'user_value' => isset($settings['settings']['enable']) ? $settings['settings']['enable'] : 'no',
                                'input_type' => 'checkbox',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('By Checking this, you are enabling the Payment',
                                'frontend-dashboard'),
                        )),
                    ),
                ),
            );

            return apply_filters('fed_payment_settings', $array, $settings);
        }


    }

    new FEDPayment();
}