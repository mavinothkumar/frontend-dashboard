<?php

if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * @param $options
 *
 * @return string
 */
function fed_form_radio($options)
{
    $name          = fed_get_data('input_meta', $options);
    $value         = fed_get_data('user_value', $options);
    $class         = fed_get_data('class_name', $options);
    $required      = fed_get_data('is_required', $options) == 'true' ? 'required="required"' : null;
    $id            = fed_get_data('id_name', $options) != '' ? 'id="'.esc_attr($options['id_name']).'"' : null;
    $readonly      = fed_get_data('readonly', $options) === true ? 'readonly=readonly' : null;
    $disabled      = fed_get_data('disabled', $options) === true ? 'disabled=disabled' : null;
    $extra         = isset($options['extra']) ? $options['extra'] : null;

    $html = '';

    $options = fed_get_select_option_value($options['input_value']);
    $html    .= '<br>';
    foreach ($options as $key => $label) {
        $checked = checked($value, $key, false);
        $html    .= sprintf(
            "
            <div class='radio %s'>
        <label for='%s'><input type='radio' name='%s' value='%s' class='%s' %s %s %s %s %s %s />%s</label>
        </div>
        ",
            $disabled,
            $key,
            $name,
            $key,
            $class,
            $checked,
            $extra,
            $disabled,
            $id,
            $readonly,
            $required,
            $label
        );
    }

    return $html;
}