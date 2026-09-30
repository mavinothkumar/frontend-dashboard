<?php
/**
 * Color Picker Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_color' ) ) {
	/**
	 * Form Color.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_color( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'color', $options );
	}
}