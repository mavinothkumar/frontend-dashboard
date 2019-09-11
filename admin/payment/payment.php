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
	ORDER BY    payment.id
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

        return sprintf('Name: %s <br> Amount: %s %s<br> Plan Type: %s <br> Discount: %s',
            esc_attr($product['name']),
            esc_attr($product['amount']),
            esc_attr($product['currency']),
            ucfirst(fed_convert_this_to_that(esc_attr($product['plan_type']), '_', ' ')),
            isset($product['discount_value']) ? esc_attr($product['discount_value']).' '.esc_attr($product['discount']) : ''
        );
    }

    return null;
}