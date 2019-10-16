<?php
if ( ! defined('ABSPATH')) {
    exit;
}

if ( ! function_exists('fed_get_payment_shortcodes')) {
    /**
     * @return array
     */
    function fed_get_payment_shortcodes()
    {
        return apply_filters('fed_payment_shortcodes', array());
    }
}


if ( ! function_exists('fed_get_payment_gateways')) {
    /**
     * @return array
     */
    function fed_get_payment_gateways()
    {
        return apply_filters('fed_payment_gateways', array('disable' => 'Disable'));
    }
}
if ( ! function_exists('fed_payment_for')) {
    /**
     * @return mixed|void
     */
    function fed_payment_for()
    {
        return apply_filters('fed_payment_for', array());
    }
}

if ( ! function_exists('fed_get_payment_for')) {
    /**
     * @param $table
     *
     * @return bool|mixed
     */
    function fed_get_payment_for($table)
    {
        $payment = fed_payment_for();

        return isset($payment[$table]) ? $payment[$table] : false;
    }
}
if ( ! function_exists('fed_get_payment_for_key_index')) {
    /**
     *
     *
     * @return mixed|void
     */
    function fed_get_payment_for_key_index()
    {
        $p           = array();
        $payment_for = fed_payment_for();
        if (is_array($payment_for) && count($payment_for)) {
            foreach ($payment_for as $index => $payment) {
                $p[$index] = $payment['name'];
            }
        }

        return $p;
    }
}
if ( ! function_exists('fed_payment_gateway')) {
    /**
     * @return bool | string
     */
    function fed_payment_gateway()
    {
        $payment = get_option('fed_payment_settings');

        if ($payment && isset($payment['settings']['gateway']) && $payment['settings']['gateway'] !== 'disable') {
            return $payment['settings']['gateway'];
        }

        return false;
    }
}

if ( ! function_exists('fed_get_transactions_with_meta')) {
    /**
     * @return array|object|void|null
     */
    function fed_get_transactions_with_meta()
    {
        global $wpdb;
        $transactions = fed_get_transactions();
        if (count($transactions)) {
            $table_payment_items = $wpdb->prefix.BC_FED_TABLE_PAYMENT_ITEMS;

            foreach ($transactions as $index => $transaction) {
                $transaction_id                        = $transaction['id'];
                $m                                     = $wpdb->get_results("SELECT * FROM $table_payment_items WHERE payment_id = $transaction_id ORDER BY  payment_item_id DESC",
                    ARRAY_A);
                $transactions[$index]['payment_items'] = $m;
            }

            return $transactions;
        }

        return;
    }
}
if ( ! function_exists('fed_get_transactions')) {

    /**
     * @param  null  $payment_type
     *
     * @return array|object|null
     */
    function fed_get_transactions()
    {
        global $wpdb;
        $table_payment = $wpdb->prefix.BC_FED_TABLE_PAYMENT;
        $table_user    = $wpdb->prefix.'users';
        if (fed_is_admin()) {

            return $wpdb->get_results(
                "
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
	ORDER BY    payment.id DESC
	", ARRAY_A);
        } else {
            $user_id = get_current_user_id();
            FED_Log::writeLog(['$user_id' => $user_id]);

            $result = $wpdb->get_results(
                "
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE       payment.user_id = $user_id
	ORDER BY    payment.id DESC
	", ARRAY_A);

//            FED_Log::writeLog(['$result' => $result]);

            return $result;
        }
    }
}
if ( ! function_exists('fed_get_transaction_with_meta')) {
    /**
     * @param $id
     * @param  string  $column
     *
     * @return array|object|void|null
     */
    function fed_get_transaction_with_meta($id, $column = 'id')
    {
        global $wpdb;
        $transaction         = fed_get_transaction($id, $column = 'id');
        $table_payment_items = $wpdb->prefix.BC_FED_TABLE_PAYMENT_ITEMS;

        $transaction_id               = $transaction['id'];
        $m                            = $wpdb->get_results("SELECT * FROM $table_payment_items WHERE payment_id = $transaction_id ORDER BY  payment_item_id DESC",
            ARRAY_A);
        $transaction['payment_items'] = $m;

        return $transaction;

    }
}
if ( ! function_exists('fed_get_transaction')) {
    /**
     * @param $id
     * @param  string  $column
     *
     * @return array|object|\WP_Error|null
     */
    function fed_get_transaction($id, $column = 'id')
    {
        if (is_user_logged_in()) {
            global $wpdb;
            $table_payment = $wpdb->prefix.BC_FED_TABLE_PAYMENT;
            $table_user    = $wpdb->prefix.'users';

            $result = $wpdb->get_results(
                "
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE payment.$column = $id	            
	            ", ARRAY_A);

            if (isset($result[0]) && count($result[0]) > 0) {
                return $result[0];
            }
        }

        // %s Column Name
        return new WP_Error('fed_no_row_found_on_that_id', sprintf(__('Invalid %s', 'frontend-dashboard'), $column));
    }
}

if ( ! function_exists('fed_get_transaction_meta')) {
    /**
     * @param $id
     * @param  string  $column
     *
     * @return array|object|void|null
     */
    function fed_get_transaction_meta($id, $column = 'id')
    {
        global $wpdb;
        $table_payment_items = $wpdb->prefix.BC_FED_TABLE_PAYMENT_ITEMS;
        $transaction         = $wpdb->get_results("SELECT * FROM $table_payment_items WHERE $column = $id ORDER BY  payment_item_id DESC",
            ARRAY_A);

        return $transaction;

    }
}

if ( ! function_exists('fed_transaction_product_details')) {
    /**
     * @param $transaction
     *
     * @return mixed
     */
    function fed_transaction_product_details($transaction)
    {
        $items = '';
        foreach ($transaction['payment_items'] as $products) {
            $item  = unserialize($products['object_items']);
            $items .= sprintf('<strong>%s</strong> <br> <strong>Name:</strong> %s <br> <strong>Amount:</strong> %s %s<br> <strong>Plan Type:</strong> %s <br> <strong>Discount:</strong> %s <br> <strong>Tax:</strong> %s <br> <br>',
                esc_attr(mb_strtoupper($transaction['payment_type'])),
                esc_attr($item['plan_name']),
                esc_attr($item['amount']),
                esc_attr($item['currency']),
                ucfirst(fed_convert_this_to_that(esc_attr($item['plan_type']), '_', ' ')),
                isset($item['discount_value']) && ! empty($item['discount_value']) ? esc_attr($item['discount_value']).' '.esc_attr(fed_get_discount_type($item['discount'])) : '',
                isset($item['tax_value']) && ! empty($item['tax_value']) ? esc_attr($item['tax_value']).' '.esc_attr(fed_get_discount_type($item['tax'])) : 'NA'
            );
        }

        return $items;
    }
}
if ( ! function_exists('fed_get_exact_amount')) {
    /**
     * @param $object
     *
     * @param  string  $type
     *
     * @return float|int
     */
    function fed_get_exact_amount($object, $type = 'discount')
    {
        $discount = 0;
        if (isset($object['amount']) && $object['amount']) {
            $amount = $object['amount'];
        } else {
            return 0;
        }

        if (isset($object[$type]) && $object[$type] === 'percentage') {
            $discount = (float) ($amount * $object[$type.'_value']) / 100;
        }
        if (isset($object[$type]) && $object[$type] === 'flat') {
            $discount = (float) ($object[$type.'_value']);
        }

        return $discount;
    }
}
if ( ! function_exists('fed_get_membership_expiry_date')) {
    /**
     * @param $object
     *
     * @return bool|false|string
     */
    function fed_get_membership_expiry_date($object)
    {
        if ($object && isset($object['plan_type'])) {
            if ($object['plan_type'] === 'free') {
                return __('Free', 'frontend-dashboard');
            }

            if ($object['plan_type'] === 'custom') {
                $days = isset($object['plan_days']) ? $object['plan_days'] + 1 : '0';

                return date('Y-m-d H:i:s', strtotime("+ {$days} days"));
            }

            if ($object['plan_type'] === 'monthly') {
                return date('Y-m-d H:i:s', strtotime("+ 31 days"));
            }

            if ($object['plan_type'] === 'annual') {
                return date('Y-m-d H:i:s', strtotime("+ 367 days"));
            }

            if ($object['plan_type'] === 'one_time') {
                return __('One Time', 'frontend-dashboard');
            }
        }

        return false;
    }
}
if ( ! function_exists('fed_payment_status')) {
    /**
     * @return mixed|void
     */
    function fed_payment_status()
    {
        return apply_filters('fed_payment_status', array(
            'Success'   => __('Success', 'frontend-dashboard'),
            'Pending'   => __('Pending', 'frontend-dashboard'),
            'Hold'      => __('Hold', 'frontend-dashboard'),
            'Refunded'  => __('Refunded', 'frontend-dashboard'),
            'Cancelled' => __('Cancelled', 'frontend-dashboard'),
        ));
    }
}
if ( ! function_exists('fed_discount_type')) {
    /**
     * @return array
     */
    function fed_discount_type()
    {
        return apply_filters('fed_discount_type', array(
            'percentage' => '(%)',
            'flat'       => 'Flat',
        ));
    }
}
if ( ! function_exists('fed_get_discount_type')) {
    /**
     * @param $type
     *
     * @return array
     */
    function fed_get_discount_type($type)
    {
        $discount = fed_discount_type();

        return isset($discount[$type]) ? $discount[$type] : 'ERROR';
    }
}