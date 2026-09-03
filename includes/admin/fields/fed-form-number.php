<?php
/**
 * Number Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Number.
 *
 * @param array $attributes Attributes.
 * @return string
 */
function fed_form_number( $attributes ) {
	return \FED\Services\Fields\FieldFactory::render( 'number', $attributes );
}
