<?php

/**
 * Input Fields.
 *
 * @param string $meta_key Meta Key
 * @param array  $attr     Input Attributes
 * @param string $type     Input Format.
 *
 * @return string
 */
function fed_input_box($meta_key, $attr = array(), $type = 'text')
{
    /**
     * Break it, if the input meta doesn't exist
     */
    if (empty($meta_key)) {
        wp_die('Please add input meta key');
    }

    $values                = array();
    $values['placeholder'] = isset($attr['placeholder']) && ! empty($attr['placeholder']) ? esc_attr($attr['placeholder']) : '';
    $values['label']       = isset($attr['label']) && ! empty($attr['label']) ? strip_tags($attr['label'],
            '<i><b>') : '';
    $values['class_name']  = isset($attr['class']) && ! empty($attr['class']) ? esc_attr($attr['class']) : '';

    $values['user_value'] = isset($attr['value']) && ! empty($attr['value']) ? esc_attr($attr['value']) : '';
    $values['input_min']  = isset($attr['min']) && ! empty($attr['min']) ? esc_attr($attr['min']) : 0;
    $values['input_max']  = isset($attr['max']) && ! empty($attr['max']) ? esc_attr($attr['max']) : 99999999999999999999999999999999999999999999999999;
    $values['input_step'] = isset($attr['step']) && ! empty($attr['step']) ? esc_attr($attr['step']) : 'any';

    $values['is_required']   = isset($attr['is_required']) && $attr['required'] == 'true' ? 'required="required"' : '';
    $values['id_name']       = isset($attr['id']) && ! empty($attr['id']) ? esc_attr($attr['id']) : '';
    $values['readonly']      = isset($attr['readonly']) && $attr['readonly'] === true ? true : '';
    $values['user_value']    = isset($attr['value']) && ! empty($attr['value']) ? esc_attr($attr['value']) : '';
    $values['input_value']   = isset($attr['options']) && ! empty($attr['options']) ? $attr['options'] : '';
    $values['disabled']      = isset($attr['disabled']) && $attr['disabled'] === true ? true : '';
    $values['default_value'] = isset($attr['default_value']) && ! empty($attr['default_value']) ? esc_attr($attr['default_value']) : 'yes';
    $values['extra']         = isset($attr['extra']) ? $attr['extra'] : '';
    $values['extended']      = isset($attr['extended']) && ! empty($attr['extended']) ? esc_attr($attr['extended']) : array();
    $values['input_type']    = $type;
    $values['input_meta']    = isset($attr['name']) ? $attr['name'] : $meta_key;
    $values['content']       = isset($attr['content']) ? $attr['content'] : '';

    return fed_get_input_details($values);
}

/**
 * Loader.
 *
 * @param string $hide
 *
 * @return string
 */
function fed_loader($hide = 'hide', $message = null)
{
    $html = '<div class="preview-area '.$hide.'">
        <div class="spinner_circle">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>';

    if ($message) {
        $html .= '<div class="fed_loader_message hide">'.$message.'</div>';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Sort given array by order key
 */
function fed_sort_by_order($a, $b)
{
    if (isset($a['input_order'], $b['input_order'])) {
        return $a['input_order'] - $b['input_order'];
    }
    if (isset($a['menu_order'], $b['menu_order'])) {
        return $a['menu_order'] - $b['menu_order'];
    }

    return 199;
}

/**
 * Sort by Desc
 *
 * @param array  $a First Element.
 * @param array  $b Second Element.
 *
 * @param string $key
 *
 * @return int
 */
function fed_sort_by_desc($a, $b)
{
    if (isset($a['id'], $b['id'])) {
        return (int)$b['id'] - (int)$a['id'];
    }

    return 199;
}

function fed_wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true, $echo = true)
{
    $name        = esc_attr($name);
    $nonce_field = '<input type="hidden" name="'.$name.'" value="'.wp_create_nonce($action).'" />';

    if ($referer) {
        $nonce_field .= wp_referer_field(false);
    }

    if ($echo) {
        echo $nonce_field;
    }

    return $nonce_field;
}

/**
 * Generate Random String
 *
 * @param int $length
 *
 * @return string
 */
function fed_get_random_string($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

