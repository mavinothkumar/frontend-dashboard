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