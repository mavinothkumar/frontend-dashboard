<?php
/**
 * Label / HTML Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_label' ) ) {
	/**
	 * Form Label.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_label( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'label', $options );
	}
}
