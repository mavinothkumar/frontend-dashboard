<?php
/**
 * Form Hidden.
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
function fed_form_hidden( $options ) {
	$placeholder = fed_get_data( 'placeholder', $options );
	$name        = fed_get_data( 'input_meta', $options );
	$value       = fed_get_data( 'user_value', $options );
	$class       = 'form-control ' . fed_get_data( 'class_name', $options );
	$required    = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id          = ( isset( $options['id_name'] ) && '' != $options['id_name'] ) ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$readonly    = ( true === fed_get_data( 'readonly', $options ) ) ? 'readonly=readonly' : null;
	$disabled    = ( true === fed_get_data( 'disabled', $options ) ) ? 'disabled=disabled' : null;
	$extra       = isset( $options['extra'] ) ? $options['extra'] : null;

	return sprintf(
		"<input type='hidden' name='%s' value='%s' class='%s' placeholder='%s' %s %s %s %s %s />",
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
