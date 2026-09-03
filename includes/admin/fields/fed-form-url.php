<?php
/**
 * URL Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form URL.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_url( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'url', $options );
}
