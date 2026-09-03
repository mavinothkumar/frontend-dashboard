<?php
/**
 * Password Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Form Password.
 *
 * @param array $options Options.
 * @return string
 */
function fed_form_password( $options ) {
	return \FED\Services\Fields\FieldFactory::render( 'password', $options );
}
