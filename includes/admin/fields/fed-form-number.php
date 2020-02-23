<?php
/**
 * Form Number.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Number.
 *
 * @param  array  $attributes  Attributes.
 *
 * @return string
 */
function fed_form_number( $attributes ) {
	$placeholder = fed_get_data( 'placeholder', $attributes );
	$name        = fed_get_data( 'input_meta', $attributes );
	$value       = fed_get_data( 'user_value', $attributes );
	$class       = 'form-control ' . fed_get_data( 'class_name', $attributes );
	$required    = fed_get_data( 'is_required', $attributes ) == 'true' ? 'required="required"' : null;
	$id          = isset( $attributes['id_name'] ) && '' != $attributes['id_name'] ? 'id="' . esc_attr( $attributes['id_name'] ) . '"' : null;
	$readonly    = ( true === fed_get_data( 'readonly', $attributes ) ) ? 'readonly=readonly' : null;
	$disabled    = ( true === fed_get_data( 'disabled', $attributes ) ) ? 'disabled=disabled' : null;
	$extra       = isset( $attributes['extra'] ) ? $attributes['extra'] : null;
	$min         = fed_get_data( 'input_min', $attributes, 0 );
	$max         = fed_get_data( 'input_max', $attributes, 99999999999999999999999999999999999999999999 );
	$step        = fed_get_data( 'input_step', $attributes, 'any' );

	return sprintf(
		"<input type='number' name='%s' value='%s' class='%s' placeholder='%s' min='%s' max='%s' step='%s' %s %s %s %s %s />",
		$name,
		$value,
		$class,
		$placeholder,
		$min,
		$max,
		$step,
		$disabled,
		$extra,
		$id,
		$readonly,
		$required
	);
}
