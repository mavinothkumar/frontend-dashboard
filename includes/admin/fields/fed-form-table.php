<?php
/**
 * Table Grid Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_table' ) ) {
	/**
	 * Form Table.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_table( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'table', $options );
	}
}
