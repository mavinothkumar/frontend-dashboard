<?php
/**
 * Single Line Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Single Line.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_single_line( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'single_line', $options );
}
