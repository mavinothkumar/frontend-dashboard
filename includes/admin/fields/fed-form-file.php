<?php
/**
 * File Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_file' ) ) {
	/**
	 * Form File.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_file( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'file', $options );
	}
}

if ( ! function_exists( 'fed_form_files' ) ) {
	/**
	 * Form Files alias.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_files( $options ) {
		return fed_form_file( $options );
	}
}