<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FEDInvoice')) {
    /**
     * Class FEDInvoice
     */
    class FEDInvoice
    {
        public function settings()
        {
            echo 'success';
        }

        public function details()
        {
            $settings = get_option('fed_invoice_settings');
            $array    = array(
                'form'  => array(
                    'method' => '',
                    'class'  => 'fed_admin_menu fed_ajax',
                    'attr'   => '',
                    'action' => array(
                        'url' => '', 'action' => 'fed_ajax_request', 'parameters' => array(
                            'fed_action_hook' => 'FEDInvoice',
                        ),
                    ),
                    'nonce'  => array('action' => '', 'name' => ''),
                    'loader' => '',
                ),
                'input' => array(
                    'Company Logo' => array(
                        'col'          => 'col-md-12',
                        'name'         => __('Company Logo', 'frontend-dashboard-payment'),
                        'input'        => fed_get_input_details(array(
                            'input_meta' => 'logo',
                            'user_value' => isset($settings['details']['logo']) ? $settings['details']['logo'] : '',
                            'input_type' => 'file',
                        )),
                        'help_message' => fed_show_help_message(array('content' => "Company Logo")),
                    ),
                    'Logo Width'   => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Logo Width (px)', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Logo Width in Pixel',
                                    'frontend-dashboard-payment'),
                                'input_meta'  => 'width',
                                'user_value'  => isset($settings['details']['width']) ? $settings['details']['width'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Logo Width in Pixel")),
                    ),
                    'Logo Height'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Logo Height (px)', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Logo Height in Pixel',
                                    'frontend-dashboard-payment'),
                                'input_meta'  => 'height',
                                'user_value'  => isset($settings['details']['height']) ? $settings['details']['height'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Logo Height in Pixel")),
                    ),
                    'Company Name' => array(
                        'col'          => 'col-md-12',
                        'name'         => __('Company Name', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Company Name', 'frontend-dashboard-payment'),
                                'input_meta'  => 'company_name',
                                'user_value'  => isset($settings['details']['company_name']) ? $settings['details']['company_name'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Company Name")),
                    ),
                    'Door Number'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Door Number', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Door Number', 'frontend-dashboard-payment'),
                                'input_meta'  => 'door_number',
                                'user_value'  => isset($settings['details']['door_number']) ? $settings['details']['door_number'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Door Number")),
                    ),
                    'Street Name'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Street Name', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Street Name', 'frontend-dashboard-payment'),
                                'input_meta'  => 'street_name',
                                'user_value'  => isset($settings['details']['street_name']) ? $settings['details']['street_name'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Street Name")),
                    ),
                    'City'         => array(
                        'col'          => 'col-md-6',
                        'name'         => __('City', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('City', 'frontend-dashboard-payment'),
                                'input_meta'  => 'city',
                                'user_value'  => isset($settings['details']['city']) ? $settings['details']['city'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "City")),
                    ),
                    'State'        => array(
                        'col'          => 'col-md-6',
                        'name'         => __('State', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('State', 'frontend-dashboard-payment'),
                                'input_meta'  => 'state',
                                'user_value'  => isset($settings['details']['state']) ? $settings['details']['state'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "State")),
                    ),
                    'Postal Code'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Postal Code', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Postal Code', 'frontend-dashboard-payment'),
                                'input_meta'  => 'postal_code',
                                'user_value'  => isset($settings['details']['postal_code']) ? $settings['details']['postal_code'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Postal Code")),
                    ),
                    'Country'      => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Country', 'frontend-dashboard-payment'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Country', 'frontend-dashboard-payment'),
                                'input_value' => fed_get_country_code(),
                                'input_meta'  => 'country',
                                'user_value'  => isset($settings['details']['country']) ? $settings['details']['country'] : '',
                                'input_type'  => 'select',
                            )),
                        'help_message' => fed_show_help_message(array('content' => "Country")),
                    ),

                ),
            );
            $array    = apply_filters('fed_invoice_template_data', $array);
            fed_common_simple_layout($array);
        }

        /**
         * @param $request
         */
        public function update($request)
        {
            $invoice                        = get_option('fed_invoice_settings');
            $invoice['details'] = array(
                'logo'         => isset($request['logo']) ? (int) $request['logo'] : '',
                'width'        => isset($request['width']) ? fed_sanitize_text_field($request['width']) : '',
                'height'       => isset($request['height']) ? fed_sanitize_text_field($request['height']) : '',
                'country'      => isset($request['country']) ? fed_sanitize_text_field($request['country']) : '',
                'postal_code'  => isset($request['postal_code']) ? fed_sanitize_text_field($request['postal_code']) : '',
                'state'        => isset($request['state']) ? fed_sanitize_text_field($request['state']) : '',
                'city'         => isset($request['city']) ? fed_sanitize_text_field($request['city']) : '',
                'street_name'  => isset($request['street_name']) ? fed_sanitize_text_field($request['street_name']) : '',
                'door_number'  => isset($request['door_number']) ? fed_sanitize_text_field($request['door_number']) : '',
                'company_name' => isset($request['company_name']) ? fed_sanitize_text_field($request['company_name']) : '',
            );

            $new_settings = apply_filters('fed_payment_invoice_template_update', $invoice,
                $request);

            update_option('fed_invoice_settings', $new_settings);

            wp_send_json_success(array(
                'message' => __('Invoice Details Updated Successfully '),
            ));
        }
    }
}