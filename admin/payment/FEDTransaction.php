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
            $validate->name(__('User Name', 'frontend-dashboard'))->value(fed_get_data('user_id'))->required();
            $validate->name(__('Transaction ID',
                'frontend-dashboard'))->value(fed_get_data('transaction_id'))->required();
            $validate->name(__('Purchase Date', 'frontend-dashboard'))->value(fed_get_data('created'))->required();
            $validate->name(__('Payment Source',
                'frontend-dashboard'))->value(fed_get_data('payment_source'))->required();

            if ( ! $validate->isSuccess()) {
                $errors = implode('<br>', $validate->getErrors());
                wp_send_json_error(array('message' => $errors));
            }

            // add the Transactions.
            $status = $this->addTransaction($request);

            if ($status) {
                wp_send_json_success(array(
                    'message' => __('Transaction Added Successfully', 'frontend-dashboard'),
                ));
            }

            FED_Log::writeLog(array('$request' => $request));
            wp_send_json_error(array(
                'message' => __('OOPs! There is some issue in adding the record, please check the log',
                    'frontend-dashboard'),
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

                $data      = $this->formatTransaction($request);
                $user_role = fed_get_data('user_role', $data);

                unset($data['user_role']);


                $status = $wpdb->insert($table, $data['data']);

                if ($status) {
                    if ($user_role) {
                        $user_update = wp_update_user(
                            array(
                                'ID'   => (int) $request['user_id'],
                                'role' => $user_role,
                            ));
                        if ($user_update instanceof WP_Error) {
                            FED_Log::writeLog(array(
                                '$status'      => $status,
                                '$user_role'   => $user_role,
                                '$user_update' => $user_update,
                            ));

                            return false;
                        }
                    }

                    return true;
                }

                FED_Log::writeLog(array(
                    '$status'      => $status,
                    '$user_role'   => $user_role,
                    '$user_update' => $user_update,
                ));

                return false;
            }
        }

        /**
         * @param $request
         *
         * @param  string  $type
         *
         * @return array
         */
        public function formatTransaction($request, $type = '')
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
                $amount        = isset($item['amount']) ? (float) $item['amount'] : 0;
                $discount      = isset($item['discount_value']) ? (float) $item['discount_value'] : 0;
                $tax           = isset($item['tax_value']) ? (float) $item['tax_value'] : 0;
                $shipping      = isset($item['shipping_value']) ? (float) $item['shipping_value'] : 0;
                $discount_cost = 0;
                $tax_cost      = 0;
                $shipping_cost = 0;
                $quantity      = isset($item['quantity']) && ! empty($item['quantity']) ? (int) $item['quantity'] : 1;
                $currency      = isset($item['currency']) ? fed_sanitize_text_field($item['currency']) : '';
                $type          = ! empty($type) ? $type : isset($item['type']) && ! empty($item['type']) ? $item['type'] : '';

                if ($discount) {
                    $discount_cost = fed_get_exact_amount($item, 'discount');
                }
                if ($tax) {
                    $tax_cost = fed_get_exact_amount($item, 'tax');
                }
                if ($shipping) {
                    $shipping_cost = fed_get_exact_amount($item, 'shipping');
                }

                $discounted_amount = ($amount + $tax_cost + $shipping_cost) - ($discount_cost) * $quantity;

                $total = $total + $discounted_amount;

                $items[] = array(
                    'id'                => isset($item['id']) ? intval($item['id']) : 'manual',
                    'amount'            => $amount,
                    'total'             => $discounted_amount,
                    'currency'          => $currency,
                    'plan_type'         => fed_sanitize_text_field(fed_get_data('plan_type', $item)),
                    'plan_days'         => fed_sanitize_text_field(fed_get_data('plan_days', $item)),
                    'plan_name'         => fed_sanitize_text_field(fed_get_data('plan_name', $item)),
                    'default_user_role' => fed_sanitize_text_field(fed_get_data('default_user_role', $item)),
                    'user_role'         => fed_sanitize_text_field(fed_get_data('user_role', $item)),
                    'quantity'          => $quantity,
                    'discount'          => fed_sanitize_text_field(fed_get_data('discount', $item)),
                    'discount_value'    => fed_sanitize_text_field(fed_get_data('discount_value', $item)),
                    'tax'               => fed_sanitize_text_field(fed_get_data('tax', $item)),
                    'tax_value'         => fed_sanitize_text_field(fed_get_data('tax_value', $item)),
                    'shipping'          => fed_sanitize_text_field(fed_get_data('shipping', $item)),
                    'shipping_value'    => fed_sanitize_text_field(fed_get_data('shipping_value', $item)),
                    'note_to_payee'     => fed_sanitize_text_field(fed_get_data('note_to_payee', $item)),
                    'description'       => fed_sanitize_text_field(fed_get_data('description', $item)),
                );
            }

            $data = array(
                'user_id'        => (int) fed_get_data('id', $request, get_current_user_id()),
                'items'          => serialize($items),
                'transaction_id' => fed_sanitize_text_field(fed_get_data('transaction_id', $request)),
                'invoice_url'    => 'custom',
                'amount'         => $total,
                'currency'       => $currency,
                'payment_type'   => ! empty($type) ? $type : 'NA',
                'payment_source' => fed_sanitize_text_field(fed_get_data('payment_source', $request)),
                'updated'        => current_time('Y-m-d'),
                'created'        => isset($request['created']) ? date('Y-m-d H:i:s',
                    strtotime(fed_sanitize_text_field($request['created']))) : '',
                'status'         => fed_sanitize_text_field(fed_get_data('status', $request, 'Pending')),
                'ends_at'        => $ends_at,
                'user_role'      => $user_role,
            );

            return $data;
        }

    }

    new FEDTransaction();
}