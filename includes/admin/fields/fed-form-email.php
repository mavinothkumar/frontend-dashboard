<?php
/**
 * Form Email.
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
function fed_form_email( $options ) {
	$placeholder = fed_get_data( 'placeholder', $options );
	$name        = fed_get_data( 'input_meta', $options );
	$value       = fed_get_data( 'user_value', $options );
	$class       = 'form-control ' . fed_get_data( 'class_name', $options );
	$required    = fed_get_data( 'is_required', $options ) == 'true' ? 'required="required"' : null;
	$id          = isset( $options['id_name'] ) && $options['id_name'] != '' ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$readonly    = fed_get_data( 'readonly', $options ) === true ? 'readonly=readonly' : null;
	$disabled    = fed_get_data( 'disabled', $options ) === true ? 'disabled=disabled' : null;
	$extra       = isset( $options['extra'] ) ? $options['extra'] : null;

	$extended            = fed_get_data( 'extended', $options );
	$unseralize          = $extended ? maybe_unserialize( $extended ) : null;
	$disable_user_access = $unseralize ? fed_get_data( 'disable_user_access', $unseralize ) : null;

	if ( 'Disable' === $disable_user_access && ! fed_is_admin() ) {
		$name     = '';
		$readonly = 'readonly=readonly';
		$disabled = 'disabled=disabled';
	}

	return sprintf(
		"<input type='email' name='%s' value='%s' class='%s' placeholder='%s' %s %s %s %s %s />",
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
