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
                        'name'         => __('Company Logo', 'frontend-dashboard'),
                        'input'        => fed_get_input_details(array(
                            'input_meta' => 'logo',
                            'user_value' => isset($settings['details']['logo']) ? $settings['details']['logo'] : '',
                            'input_type' => 'file',
                        )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Company Logo', 'frontend-dashboard'),
                        )),
                    ),
                    'Logo Width'   => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Logo Width (px)', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Logo Width in Pixel',
                                    'frontend-dashboard'),
                                'input_meta'  => 'width',
                                'user_value'  => isset($settings['details']['width']) ? $settings['details']['width'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Logo Width in Pixel', 'frontend-dashboard'),
                        )),
                    ),
                    'Logo Height'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Logo Height (px)', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Logo Height in Pixel',
                                    'frontend-dashboard'),
                                'input_meta'  => 'height',
                                'user_value'  => isset($settings['details']['height']) ? $settings['details']['height'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Logo Height in Pixel', 'frontend-dashboard'),
                        )),
                    ),
                    'Company Name' => array(
                        'col'          => 'col-md-12',
                        'name'         => __('Company Name', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Company Name', 'frontend-dashboard'),
                                'input_meta'  => 'company_name',
                                'user_value'  => isset($settings['details']['company_name']) ? $settings['details']['company_name'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Company Name', 'frontend-dashboard'),
                        )),
                    ),
                    'Door Number'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Door Number', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Door Number', 'frontend-dashboard'),
                                'input_meta'  => 'door_number',
                                'user_value'  => isset($settings['details']['door_number']) ? $settings['details']['door_number'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Door Number', 'frontend-dashboard'),
                        )),
                    ),
                    'Street Name'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Street Name', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Street Name', 'frontend-dashboard'),
                                'input_meta'  => 'street_name',
                                'user_value'  => isset($settings['details']['street_name']) ? $settings['details']['street_name'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Street Name', 'frontend-dashboard'),
                        )),
                    ),
                    'City'         => array(
                        'col'          => 'col-md-6',
                        'name'         => __('City', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('City', 'frontend-dashboard'),
                                'input_meta'  => 'city',
                                'user_value'  => isset($settings['details']['city']) ? $settings['details']['city'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => __('City', 'frontend-dashboard'))),
                    ),
                    'State'        => array(
                        'col'          => 'col-md-6',
                        'name'         => __('State', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('State', 'frontend-dashboard'),
                                'input_meta'  => 'state',
                                'user_value'  => isset($settings['details']['state']) ? $settings['details']['state'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array('content' => __('State', 'frontend-dashboard'))),
                    ),
                    'Postal Code'  => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Postal Code', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Postal Code', 'frontend-dashboard'),
                                'input_meta'  => 'postal_code',
                                'user_value'  => isset($settings['details']['postal_code']) ? $settings['details']['postal_code'] : '',
                                'input_type'  => 'single_line',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Postal Code', 'frontend-dashboard'),
                        )),
                    ),
                    'Country'      => array(
                        'col'          => 'col-md-6',
                        'name'         => __('Country', 'frontend-dashboard'),
                        'input'        =>
                            fed_get_input_details(array(
                                'placeholder' => __('Country', 'frontend-dashboard'),
                                'input_value' => fed_get_country_code(),
                                'input_meta'  => 'country',
                                'user_value'  => isset($settings['details']['country']) ? $settings['details']['country'] : '',
                                'input_type'  => 'select',
                            )),
                        'help_message' => fed_show_help_message(array(
                            'content' => __('Country', 'frontend-dashboard'),
                        )),
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
            $invoice            = get_option('fed_invoice_settings');
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

        /**
         * @param $request
         */
        public function download($request)
        {
            $validate = new FED_Validation();
            $validate->name('Transaction ID')->value($request['transaction_id'])->required();

            if ( ! $validate->isSuccess()) {
                $errors = implode('<br>', $validate->getErrors());
                wp_send_json_error(array('message' => $errors));
            }

            // Check the Transaction is Valid
            $payment = fed_get_transaction((int) $request['transaction_id']);

            if ($payment instanceof WP_Error) {
                wp_send_json_error(array('message' => $payment->get_error_message()));
            }

            $settings = get_option('fed_invoice_settings');

//            bcdump($payment);
//            bcdump($settings);
            bcdump(unserialize($payment['product_object']));
            exit();


            if ( ! $settings) {
                wp_send_json_error(array(
                    'message' => __('Please configure the Invoice Details ( FED | Payments | Invoice | Company Details )',
                        'frontend-dashboard'),
                ));
            }

            $html   = '';
            $object = unserialize($payment['product_object']);
            $html   .= '<div class="container" id="print">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="padding-top text-left" id="logo">
                            <img src="'.wp_get_attachment_url($settings['logo']).'" '.$settings['width'].' '.$settings['height'].' />
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h4>'.$settings['company_name'].'</h4>
                        <p>'.$settings['door_number'].' '.$settings['street_name'].'</p>
                        <p>'.$settings['city'].' '.$settings['state'].'</p>
                        <p>'.$settings['country'].' '.$settings['postal_code'].'</p>
                    </div>
                </div>
                <hr/>
                <div class="row text-uppercase">
                    <div class="col-sm-6 text-left">
                        <h3>Client Details</h3>
                        <h4 style="display: block;">'.$payment['display_name'].'</h4>
                        <p>'.$payment['user_email'].'</p>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="invoice-color">
                            <h3>Transaction ID: '.$payment['transaction_id'].'</h3>
                            <h4 style="display: block;">Invoice date: '.$payment['created'].'</h4>
                            <h1 style="display: block;" class="big-font">'.$payment['amount'] / 100 .' '.$settings['currency'].'</h1>
                        </div>
                    </div>
                </div>
                <div class="row tablecss">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr style="display: table-row;" class="success">
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>';


            $html .= '<tr style="display: table-row;">
                                <td>'.$object['name'].'</td>
                                <td>'.isset($object['quantity']) ? $object['quantity'] : '1'.'</td>
                                <td>'.$object['price'].' '.$list['currency'].'</td>
                            </tr>';
            $html .= '<tr style="display: table-row;">
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>SubTotal</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['subtotal'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Tax</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['tax'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Shipping</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['shipping'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Insurance</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['insurance'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Handling Fee</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['handling_fee'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Shipping Discount</b>
                                </td>
                                <td>
                                    <b>-'.$settings['amount']['shipping_discount'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            
                            <tr style="display: table-row;" class="info">
                                <td colspan="2" class="text-right">
                                    <b>Total</b>
                                </td>
                                <td>
                                    <b>'.$settings['amount']['total'].' '.$settings['amount']['currency'].'</b>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="invoice-color">                           
                        </p>
                    </div>
                </div>
            </div>';

            wp_send_json_success(array('message' => 'ok', 'html' => $html));
        }

        public function user()
        {
            echo 'success';
        }
    }
}