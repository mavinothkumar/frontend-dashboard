<?php
/**
 * Checkbox Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Checkbox.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_checkbox( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'checkbox', $options );
}
