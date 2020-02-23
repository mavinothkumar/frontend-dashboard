<?php
/**
 * Form Select.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Select.
 *
 * @param  array $attributes  Attributes.
 *
 * @return string
 */
function fed_form_select( $attributes ) {
	$name     = fed_get_data( 'input_meta', $attributes );
	$value    = fed_get_data( 'user_value', $attributes );
	$class    = 'form-control ' . fed_get_data( 'class_name', $attributes );
	$required = fed_get_data( 'is_required', $attributes ) == 'true' ? 'required="required"' : null;
	$id       = ( '' != fed_get_data(
			'id_name',
			$attributes
		) ) ? ( 'id="' . esc_attr( $attributes['id_name'] ) . '"' ) : null;
	$disabled = ( true === fed_get_data( 'disabled', $attributes ) ) ? 'disabled=disabled' : null;
	$extra    = isset( $attributes['extra'] ) ? $attributes['extra'] : null;
	$extended = isset( $attributes['extended'] ) ? ( is_string( $attributes['extended'] ) ? unserialize( $attributes['extended'] ) : $attributes['extended'] ) : array();

	$is_multiple = isset( $extended['multiple'] ) ? $extended['multiple'] : null;
	$options     = fed_get_select_option_value( $attributes['input_value'] );

	$multi_select = '';
	$option       = '';
	$select_name  = $name;

	// If Multi-Select.
	if ( 'Enable' === $is_multiple ) {
		$multi_select = ' multiple=multiple ';
		$class        .= ' fed_multi_select ';
		$select_name  = $name . '[]';
	}

	foreach ( $options as $key => $label ) {
		if ( is_array( $value ) ) {
			$checked = in_array( $key, $value ) ? 'selected' : '';
			$option  .= '<option value="' . esc_attr( $key ) . '" ' . $checked . '>' . $label . '</option>';
		} elseif ( is_serialized( $value ) ) {
			$checked = in_array( $key, unserialize( $value ) ) ? 'selected' : '';
			$option  .= '<option value="' . esc_attr( $key ) . '" ' . $checked . '>' . $label . '</option>';
		} else {
			$option .= '<option
						value="' . esc_attr( $key ) . '" ' . selected(
					$value, $key,
					false
				) . '>' . $label . '</option>';
		}
	}

	return sprintf(
		"
        <select name='%s' class='%s' %s %s %s %s %s>
        %s
        </select>",
		$select_name,
		$class,
		$multi_select,
		$disabled,
		$extra,
		$id,
		$required,
		$option
	);
}
