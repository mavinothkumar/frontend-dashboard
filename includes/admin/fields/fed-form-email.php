<?php
/**
 * Email Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Email.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_email( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'email', $options );
}
