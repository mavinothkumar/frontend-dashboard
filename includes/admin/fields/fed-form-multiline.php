<?php
/**
 * Form Multiline.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Multiline.
 *
 * @param  array  $options  Options.
 *
 * @return string
 */
function fed_form_multi_line( $options ) {
	$placeholder = fed_get_data( 'placeholder', $options );
	$name        = fed_get_data( 'input_meta', $options );
	$value       = fed_get_data( 'user_value', $options, '', false );
	$class       = 'form-control ' . fed_get_data( 'class_name', $options );
	$required    = 'true' == fed_get_data( 'is_required', $options ) ? 'required="required"' : null;
	$id          = ( isset( $options['id_name'] ) && '' != $options['id_name'] ) ? 'id="' . esc_attr( $options['id_name'] ) . '"' : null;
	$readonly    = ( true === fed_get_data( 'readonly', $options ) ) ? 'readonly=readonly' : null;
	$disabled    = ( true === fed_get_data( 'disabled', $options ) ) ? 'disabled=disabled' : null;
	$extra       = isset( $options['extra'] ) ? $options['extra'] : null;
	$rows        = isset( $options['rows'] ) ? absint( $options['rows'] ) : 5;
	$cols        = isset( $options['cols'] ) ? absint( $options['cols'] ) : 30;

	$extended            = fed_get_data( 'extended', $options );
	$unseralize          = $extended ? maybe_unserialize( $extended ) : null;
	$disable_user_access = $unseralize ? fed_get_data( 'disable_user_access', $unseralize ) : null;

	if ( 'Disable' === $disable_user_access && ! fed_is_admin() ) {
		$name     = '';
		$readonly = 'readonly=readonly';
		$disabled = 'disabled=disabled';
	}

	return sprintf(
		"<textarea name='%s' rows='%s' cols='%s' class='%s' placeholder='%s' %s %s %s %s %s >%s</textarea>",
		$name,
		$rows,
		$cols,
		$class,
		$placeholder,
		$disabled,
		$extra,
		$id,
		$readonly,
		$required,
		wp_kses_post( $value )
	);
}
