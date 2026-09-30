<?php

namespace FED\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FormHelper
 *
 * Handles HTML form generation and input fields.
 */
class FormHelper {

	/**
	 * Generate an input box array configuration.
	 *
	 * @param string $meta_key Meta Key.
	 * @param array  $attr     Input Attributes.
	 * @param string $type     Input Format.
	 *
	 * @return array|string
	 */
	public static function input_box( $meta_key, $attr = [], $type = 'text' ) {
		if ( empty( $meta_key ) ) {
			wp_die( 'Please add input meta key' );
		}

		$values = [];
		$values['placeholder'] = !empty( $attr['placeholder'] ) ? esc_attr( $attr['placeholder'] ) : '';
		$values['label']       = !empty( $attr['label'] ) ? strip_tags( $attr['label'], '<i><b>' ) : '';
		$values['class_name']  = !empty( $attr['class'] ) ? esc_attr( $attr['class'] ) : '';

		$values['user_value'] = !empty( $attr['value'] ) ? esc_attr( $attr['value'] ) : '';
		$values['input_min']  = !empty( $attr['min'] ) ? esc_attr( $attr['min'] ) : 0;
		$values['input_max']  = !empty( $attr['max'] ) ? esc_attr( $attr['max'] ) : 99999999999999999999999999999999999999999999999999;
		$values['input_step'] = !empty( $attr['step'] ) ? esc_attr( $attr['step'] ) : 'any';

		$values['is_required']   = ( isset( $attr['required'] ) && 'true' == $attr['required'] ) ? 'required="required"' : '';
		$values['id_name']       = !empty( $attr['id'] ) ? esc_attr( $attr['id'] ) : '';
		$values['readonly']      = ( isset( $attr['readonly'] ) && true === $attr['readonly'] ) ? true : '';
		
		$values['input_value']   = !empty( $attr['options'] ) ? $attr['options'] : '';
		$values['disabled']      = ( isset( $attr['disabled'] ) && true === $attr['disabled'] ) ? true : '';
		$values['default_value'] = !empty( $attr['default_value'] ) ? esc_attr( $attr['default_value'] ) : 'yes';
		$values['extra']         = isset( $attr['extra'] ) ? $attr['extra'] : '';
		$values['extended']      = !empty( $attr['extended'] ) ? esc_attr( $attr['extended'] ) : [];
		$values['input_type']    = $type;
		$values['input_meta']    = isset( $attr['name'] ) ? $attr['name'] : $meta_key;
		$values['content']       = isset( $attr['content'] ) ? $attr['content'] : '';

		// If fed_get_input_details still exists procedurally, call it, otherwise this needs to be extracted too.
		if ( function_exists('fed_get_input_details') ) {
			return fed_get_input_details( $values );
		}
		
		return $values;
	}
}
