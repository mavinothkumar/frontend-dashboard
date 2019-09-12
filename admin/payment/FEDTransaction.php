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

            // Update the Transactions.

            global $wpdb;
            $table = $wpdb->prefix.BC_FED_TABLE_PAYMENT;

            /**
             * Object should have
             * 1. amount
             * 2. currency
             * 3. plan_type (free |  monthly | annual | one_time | if (custom) then plan_days should be
             * available.
             * 4. id
             * 5. name
             * 6. discount
             * 7. discount_value
             * 8. type
             * 9. default_user_role
             * 10. user_role
             */

            $object = array(
                'id'                => 'manual',
                'amount'            => isset($request['amount']) ? fed_sanitize_text_field($request['amount']) : '',
                'currency'          => isset($request['currency']) ? fed_sanitize_text_field($request['currency']) : '',
                'plan_type'         => isset($request['plan_type']) ? fed_sanitize_text_field($request['plan_type']) : '',
                'plan_days'         => isset($request['plan_days']) ? fed_sanitize_text_field($request['plan_days']) : '',
                'name'              => isset($request['name']) ? fed_sanitize_text_field($request['name']) : '',
                'discount'          => isset($request['discount']) ? fed_sanitize_text_field($request['discount']) : '',
                'discount_value'    => isset($request['discount_value']) ? fed_sanitize_text_field($request['discount_value']) : '',
                'type'              => isset($request['type']) ? fed_sanitize_text_field($request['type']) : '',
                'default_user_role' => isset($request['default_user_role']) ? fed_sanitize_text_field($request['default_user_role']) : '',
                'user_role'         => isset($request['user_role']) ? fed_sanitize_text_field($request['user_role']) : '',
            );


            $data = array(
                'user_id'        => (int) $request['user_id'],
                'product_object' => serialize($object),
                'transaction_id' => isset($request['transaction_id']) ? fed_sanitize_text_field($request['transaction_id']) : '',
                'invoice_url'    => 'custom',
                'amount'         => fed_get_exact_amount($object) / 100,
                'currency'       => isset($request['currency']) ? fed_sanitize_text_field($request['currency']) : '',
                'payment_source' => isset($request['payment_source']) ? fed_sanitize_text_field($request['payment_source']) : '',
                'updated'        => current_time('Y-m-d'),
                'created'        => isset($request['created']) ? date('Y-m-d H:i:s',
                    strtotime(fed_sanitize_text_field($request['created']))) : '',
                'ends_at'        => fed_get_membership_expiry_date($object),
            );

            $status      = $wpdb->insert($table, $data);
            $user_update = wp_update_user(array('ID' => (int) $request['user_id'], 'role' => $object['user_role']));

            if ($status && ! $user_update instanceof WP_Error) {
                wp_send_json_success(array(
                    'message' => __('Transaction Added Successfully', 'frontend-dashboard-payment-pro'),
                ));
            }

            FED_Log::writeLog(array('data' => $data, 'status' => $status, 'user_update' => $user_update));
            wp_send_json_error(array(
                'message' => __('OOPs! There is some issue in adding the record, please check the log',
                    'frontend-dashboard-payment-pro'),
            ));

        }

    }

    new FEDTransaction();
}