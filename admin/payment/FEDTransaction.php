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

        public function transactions()
        {
            /**
             * Payment Gateways
             */
            $this->authorize();

            echo do_shortcode('[fed_transactions]');
        }

        public function authorize()
        {
            if ( ! is_user_logged_in()) {
                wp_die(__('Error 403: Please login to view this page', 'frontend-dashboard'));
            }
        }

        /**
         * @param $request
         */
        public function update($request)
        {
            $this->authorize();

            if ( ! is_admin()) {
                wp_die(__('Error 403: You are not allowed to view this page', 'frontend-dashboard'));
            }

            //Validation
            $validate = new FED_Validation();
            $validate->name('User Name')->value($request['user_id'])->required();
            $validate->name('Transaction ID')->value($request['transaction_id'])->required();
            $validate->name('Purchase Date')->value($request['created'])->required();
            $validate->name('Payment Source')->value($request['payment_source'])->required();

            if ( ! $validate->isSuccess()) {
                $errors = implode('<br>', $validate->getErrors());
                wp_send_json_error(array('message' => $errors));
            }

            // add the Transactions.
            $status = $this->addTransaction($request);

            if ($status) {
                wp_send_json_success(array(
                    'message' => __('Transaction Added Successfully', 'frontend-dashboard-payment-pro'),
                ));
            }


            FED_Log::writeLog(array('$request' => $request));
            wp_send_json_error(array(
                'message' => __('OOPs! There is some issue in adding the record, please check the log',
                    'frontend-dashboard-payment-pro'),
            ));

        }

        /**
         * @param $request
         */
        public function addTransaction($request)
        {
            if (isset($request['items']) && count($request['items']) > 0) {
                $user_update = true;
                global $wpdb;
                $table = $wpdb->prefix.BC_FED_TABLE_PAYMENT;

                $data = $this->formatTransaction($request);


                $status = $wpdb->insert($table, $data['data']);

                if ($status && ! $user_update instanceof WP_Error) {
                    if ($data['user_role']) {
                        $user_update = wp_update_user(
                            array(
                                'ID'   => (int) $request['user_id'],
                                'role' => $data['user_role'],
                            ));
                    }

                    return true;
                }

                FED_Log::writeLog(array(
                    '$status'      => $status,
                    '$user_role'   => $data['user_role'],
                    '$user_update' => $user_update,
                ));
            }
        }

        /**
         * @param $request
         *
         * @return array
         */
        public function formatTransaction($request)
        {
            $total     = 0;
            $items     = array();
            $user_role = null;
            $currency  = 'USD';
            $ends_at   = '';
            foreach ($request['items'] as $index => $item) {
                // finding the Total
                $user_role     = isset($item['user_role']) && $item['user_role'] !== 'fed_null' ? $item['user_role'] : null;
                $ends_at       = fed_get_membership_expiry_date($item);
                $amount        = isset($item['amount']) ? floatval($item['amount']) : 0;
                $discount      = isset($item['discount_value']) ? floatval($item['discount_value']) : 0;
                $tax           = isset($item['tax_value']) ? floatval($item['tax_value']) : 0;
                $shipping      = isset($item['shipping_value']) ? floatval($item['shipping_value']) : 0;
                $discount_cost = 0;
                $tax_cost      = 0;
                $shipping_cost = 0;
                $quantity      = isset($item['quantity']) && ! empty($item['quantity']) ? $item['quantity'] : 1;
                $currency      = isset($item['currency']) ? fed_sanitize_text_field($item['currency']) : '';

                if ($discount) {
                    $discount_cost = fed_get_exact_amount($item, 'discount');
                }
                if ($tax) {
                    $tax_cost = fed_get_exact_amount($item, 'tax');
                }
                if ($shipping) {
                    $shipping_cost = fed_get_exact_amount($item, 'shipping');
                }
                $discounted_amount = ($amount - ($discount_cost + $tax_cost + $shipping_cost)) * $quantity;

                $total = $total + $discounted_amount;

                $items[] = array(
                    'id'                => 'manual',
                    'amount'            => $amount,
                    'total'             => $discounted_amount,
                    'currency'          => $currency,
                    'plan_type'         => isset($item['plan_type']) ? fed_sanitize_text_field($item['plan_type']) : '',
                    'plan_days'         => isset($item['plan_days']) ? fed_sanitize_text_field($item['plan_days']) : '',
                    'plan_name'         => isset($item['plan_name']) ? fed_sanitize_text_field($item['plan_name']) : '',
                    'type'              => isset($item['type']) ? fed_sanitize_text_field($item['type']) : '',
                    'default_user_role' => isset($item['default_user_role']) ? fed_sanitize_text_field($item['default_user_role']) : '',
                    'user_role'         => isset($item['user_role']) ? fed_sanitize_text_field($item['user_role']) : '',
                    'quantity'          => $quantity,
                    'discount'          => isset($item['discount']) ? fed_sanitize_text_field($item['discount']) : '',
                    'discount_value'    => isset($item['discount_value']) ? fed_sanitize_text_field($item['discount_value']) : '',
                    'tax'               => isset($item['tax']) ? fed_sanitize_text_field($item['tax']) : '',
                    'tax_value'         => isset($item['tax_value']) ? fed_sanitize_text_field($item['tax_value']) : '',
                    'shipping'          => isset($item['shipping']) ? fed_sanitize_text_field($item['shipping']) : '',
                    'shipping_value'    => isset($item['shipping_value']) ? fed_sanitize_text_field($item['shipping_value']) : '',
                );


            }

            $data = array(
                'user_id'        => (int) $request['user_id'],
                'items'          => serialize($items),
                'transaction_id' => isset($request['transaction_id']) ? fed_sanitize_text_field($request['transaction_id']) : '',
                'invoice_url'    => 'custom',
                'amount'         => $total,
                'currency'       => $currency,
                'payment_source' => isset($request['payment_source']) ? fed_sanitize_text_field($request['payment_source']) : '',
                'updated'        => current_time('Y-m-d'),
                'created'        => isset($request['created']) ? date('Y-m-d H:i:s',
                    strtotime(fed_sanitize_text_field($request['created']))) : '',
                'status'         => isset($request['status']) ? fed_sanitize_text_field($request['status']) : 'Pending',
                'ends_at'        => $ends_at,
            );

            return array('data' => $data, 'user_role' => $user_role);
        }

    }

    new FEDTransaction();
}