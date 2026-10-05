<?php
/**
 * Select Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Select.
 *
 * @param array $attributes Attributes.
 * @return string
 */
function fed_form_select( $attributes ) {
	return \FED\Services\Fields\FieldFactory::render( 'select', $attributes );
}
