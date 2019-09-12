<?php

/**
 * @return array
 */
function fed_get_payment_shortcodes()
{
    return apply_filters('fed_payment_shortcodes', array());
}

/**
 * @return array
 */
function fed_get_payment_gateways()
{
    return apply_filters('fed_payment_gateways', array('disable' => 'Disable'));
}

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

/**
 * @return array|object|null
 */
function fed_get_transactions()
{
    if (fed_is_admin()) {
        global $wpdb;
        $table_payment = $wpdb->prefix.BC_FED_TABLE_PAYMENT;
        $table_user    = $wpdb->prefix.'users';

        return $wpdb->get_results(
            "
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
	ORDER BY    payment.id DESC
	", ARRAY_A);
    } else {
        return fed_fetch_table_rows_by_key_value(BC_FED_TABLE_PAYMENT, 'user_id', get_current_user_id());
    }
}

/**
 * @param $transaction
 *
 * @return mixed
 */
function fed_transaction_product_details($transaction)
{
    if (isset($transaction['product_object'])) {
        $product = unserialize($transaction['product_object']);

        return sprintf('<strong>%s</strong> <br> <strong>Name:</strong> %s <br> <strong>Amount:</strong> %s %s<br> <strong>Plan Type:</strong> %s <br> <strong>Discount:</strong> %s',
            esc_attr(mb_strtoupper($product['type'])),
            esc_attr($product['name']),
            esc_attr($product['amount']),
            esc_attr($product['currency']),
            ucfirst(fed_convert_this_to_that(esc_attr($product['plan_type']), '_', ' ')),
            isset($product['discount_value']) ? esc_attr($product['discount_value']).' '.esc_attr($product['discount']) : ''
        );
    }

    return null;
}

/**
 * @param $object
 *
 * @return float|int
 */
function fed_get_exact_amount($object)
{
    $amount = isset($object['amount']) ? $object['amount'] : 0;
    if (isset($object['discount']) && $object['discount'] === 'percentage') {
        $amount = ($amount * $object['discount_value']) / 100;
    }
    if (isset($object['discount']) && $object['discount'] === 'flat') {
        $amount = ($amount - $object['discount_value']);
    }

    return $amount * 100;
}

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

            return date('Y-m-d', strtotime("+'.$days.' days"));
        }

        if ($object['plan_type'] === 'monthly') {
            return date('Y-m-d', strtotime("+ 31 days"));
        }

        if ($object['plan_type'] === 'annual') {
            return date('Y-m-d', strtotime("+ 367 days"));
        }

        if ($object['plan_type'] === 'one_time') {
            return __('One Time', 'frontend-dashboard');
        }
    }

    return false;
}