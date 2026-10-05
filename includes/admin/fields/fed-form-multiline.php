<?php
/**
 * Multi Line Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Multi Line.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_multi_line( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'multi_line', $options );
}
