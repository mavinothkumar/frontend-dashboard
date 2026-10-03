<?php
/**
 * WP Editor Field.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_form_wp_editor' ) ) {
	/**
	 * Form WP Editor.
	 *
	 * @param array $options Options.
	 * @return string
	 */
	function fed_form_wp_editor( $options ) {
		return \FED\Services\Fields\FieldFactory::render( 'wp_editor', $options );
	}
}

if ( ! function_exists( 'fed_e_form_wpeditor' ) ) {
	/**
	 * Backward compatibility alias for Extra plugin.
	 */
	function fed_e_form_wpeditor( $options ) {
		return fed_form_wp_editor( $options );
	}
}
