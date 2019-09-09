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
            add_filter('fed_add_main_sub_menu', array($this, 'main_sub_menu'));
        }

        public function settings()
        {
            $settings = $this->settingsData();
            fed_common_simple_layout($settings);
        }

        /**
         * @return mixed|void
         */
        public function settingsData()
        {

//            option slug =>  fed_payment_settings
//            array(
//                'settings' => array(
//                    'enable' => '' //default no
//                ),
//                'gateway'  => array(
//                    'stripe' => array(
//                        'enable'  => '', //sandbox or live
//                        'sandbox' => array(
//                            'public_key'  => '',
//                            'private_key' => '',
//                        ),
//                        'live'    => array(
//                            'public_key'  => '',
//                            'private_key' => '',
//                        ),
//                        'url'     => array(
//                            'success_url' => '',
//                            'cancel_url'  => '',
//                            'notify_url'  => '',
//                        ),
//                    ),
//                ),
//            );

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

        /**
         * @param $menu
         *
         * @return array
         */
        public function main_sub_menu($menu)
        {
            $menu['fed_payments'] = array(
                'page_title' => __('Payments', 'frontend-dashboard'),
                'menu_title' => __('Payments', 'frontend-dashboard'),
                'capability' => 'manage_options',
                'callback'   => array(new FEDPaymentMenu(), 'index'),
                'position'   => 30,
            );

            return $menu;
        }


    }

    new FEDPayment();
}