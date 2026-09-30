<?php
/**
 * Date Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_date' ) ) {
	/**
	 * Form Date.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_date( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'date', $options );
	}
}