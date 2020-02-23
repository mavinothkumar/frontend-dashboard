<?php
/**
 * Form Checkbox.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @param $options
 *
 * @return string
 */
function fed_form_checkbox( $options ) {
	$name          = fed_get_data( 'input_meta', $options );
	$value         = fed_get_data( 'user_value', $options );
	$default_value = fed_get_data( 'default_value', $options ) != '' ? $options['default_value'] : 'yes';
	$class         = fed_get_data( 'class_name', $options );
	$required      = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id            = fed_get_data( 'id_name', $options ) != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$readonly      = fed_get_data( 'readonly', $options ) === true ? 'readonly=readonly' : null;
	$disabled      = fed_get_data( 'disabled', $options ) === true ? 'disabled=disabled' : null;
	$extra         = isset( $options['extra'] ) ? $options['extra'] : null;
	$label         = fed_get_data( 'label', $options );
	$extended      = isset( $options['extended'] ) ? ( is_string( $options['extended'] ) ? unserialize( $options['extended'] ) : $options['extended'] ) : array();

	$is_extended = isset( $extended['label'] ) && count( $extended ) > 0 ? htmlspecialchars_decode( $extended['label'] ) : null;
	$label_value = $is_extended ? $is_extended : $label;
	$checked     = checked( $value, $default_value, false );

	return sprintf(
		"
        <label>
        <input type='checkbox' name='%s' value='%s' class='%s' %s %s %s %s %s %s />
        %s
        </label>",
		$name,
		$default_value,
		$class,
		$checked,
		$disabled,
		$extra,
		$id,
		$readonly,
		$required,
		$label_value
	);
}
