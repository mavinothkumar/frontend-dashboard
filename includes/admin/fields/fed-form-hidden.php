<?php
/**
 * Hidden Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Hidden.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_hidden( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'hidden', $options );
}
