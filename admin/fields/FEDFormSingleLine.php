<?php

/**
 * @param $options
 *
 * @return string
 */
function fed_single_line($options)
{
    $placeholder = fed_get_data('placeholder', $options);
    $name        = fed_get_data('input_meta', $options);
    $value       = fed_get_data('user_value', $options);
    $class       = 'form-control '.fed_get_data('class_name', $options);
    $required    = isset($options['is_required']) && $options['is_required'] == 'true' ? 'required="required"' : null;
    $id          = isset($options['id_name']) && $options['id_name'] != '' ? 'id="'.esc_attr($options['id_name']).'"' : null;
    $readonly    = isset($options['readonly']) && $options['readonly'] === true ? 'readonly=readonly' : null;
    $disabled    = isset($options['disabled']) && $options['disabled'] === true ? 'disabled=disabled' : null;
    $extra       = isset($options['extra']) ? $options['extra'] : null;

    return sprintf(
        "<input type='text' name='%s' value='%s' class='%s' placeholder='%s' %s %s %s %s %s />",
        $name,
        $value,
        $class,
        $placeholder,
        $disabled,
        $extra,
        $id,
        $readonly,
        $required
    );
}