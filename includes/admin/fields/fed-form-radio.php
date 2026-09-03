<?php
/**
 * Radio Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Radio.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_radio( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'radio', $options );
}
