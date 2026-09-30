<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Class BaseField
 *
 * Base class for all Frontend Dashboard form elements.
 * Provides unified attribute parsing, sanitization, and access control.
 */
abstract class BaseField {

	protected $attributes = [];
	protected $name = '';
	protected $value = '';
	protected $placeholder = '';
	protected $class_name = '';
	protected $id_name = '';
	protected $is_required = false;
	protected $is_readonly = false;
	protected $is_disabled = false;
	protected $extra = '';
	protected $options = [];
	protected $extended = [];

	/**
	 * BaseField constructor.
	 *
	 * @param array $attributes Raw options array from menu or form definitions.
	 */
	public function __construct( array $attributes = [] ) {
		$this->attributes = $attributes;
		$this->parse_attributes( $attributes );
	}

	/**
	 * Parse and extract standard field properties.
	 *
	 * @param array $attributes
	 */
	protected function parse_attributes( array $attributes ) {
		$this->name        = $this->get_data( 'input_meta', $attributes );
		$this->value       = $this->get_data( 'user_value', $attributes );
		$this->placeholder = $this->get_data( 'placeholder', $attributes );
		$class             = $this->get_data( 'class_name', $attributes );
		$this->class_name  = ( ! empty( $class ) && strpos( $class, 'form-control' ) !== false ) ? $class : trim( 'form-control ' . $class );
		$this->id_name     = $this->get_data( 'id_name', $attributes );
		$this->extra       = isset( $attributes['extra'] ) ? $attributes['extra'] : '';
		$this->options     = isset( $attributes['input_value'] ) ? $attributes['input_value'] : [];

		$required          = $this->get_data( 'is_required', $attributes );
		$this->is_required = ( 'true' === $required || true === $required || 'Enable' === $required || 'yes' === $required );
		$this->is_readonly = ( true === $this->get_data( 'readonly', $attributes ) || 'readonly' === $this->get_data( 'readonly', $attributes ) );
		$this->is_disabled = ( true === $this->get_data( 'disabled', $attributes ) || 'disabled' === $this->get_data( 'disabled', $attributes ) );

		$extended = $this->get_data( 'extended', $attributes );
		if ( is_string( $extended ) ) {
			$extended = maybe_unserialize( $extended );
		}
		$this->extended = is_array( $extended ) ? $extended : [];

		// Handle user permission access check
		$disable_user_access = isset( $this->extended['disable_user_access'] ) ? $this->extended['disable_user_access'] : null;
		if ( 'Disable' === $disable_user_access && ! $this->is_admin() ) {
			$this->name        = '';
			$this->is_readonly = true;
			$this->is_disabled = true;
		}
	}

	/**
	 * Safely retrieve data from an array.
	 *
	 * @param string $key
	 * @param array  $array
	 * @param mixed  $default
	 * @return mixed
	 */
	protected function get_data( $key, array $array, $default = '' ) {
		if ( function_exists( 'fed_get_data' ) ) {
			return fed_get_data( $key, $array, $default );
		}
		return isset( $array[ $key ] ) ? $array[ $key ] : $default;
	}

	/**
	 * Check if current user is an administrator.
	 *
	 * @return bool
	 */
	protected function is_admin() {
		if ( function_exists( 'fed_is_admin' ) ) {
			return fed_is_admin();
		}
		return current_user_can( 'administrator' );
	}

	/**
	 * Compile standard HTML attribute string.
	 *
	 * @param array $extra_attrs
	 * @return string
	 */
	protected function build_attributes( array $extra_attrs = [] ) {
		$attrs = [];

		if ( ! empty( $this->name ) ) {
			$attrs[] = sprintf( 'name="%s"', esc_attr( $this->name ) );
		}
		if ( ! empty( $this->id_name ) ) {
			$attrs[] = sprintf( 'id="%s"', esc_attr( $this->id_name ) );
		}
		if ( ! empty( $this->class_name ) ) {
			$attrs[] = sprintf( 'class="%s"', esc_attr( $this->class_name ) );
		}
		if ( ! empty( $this->placeholder ) ) {
			$attrs[] = sprintf( 'placeholder="%s"', esc_attr( $this->placeholder ) );
		}
		if ( $this->is_required ) {
			$attrs[] = 'required="required"';
		}
		if ( $this->is_readonly ) {
			$attrs[] = 'readonly="readonly"';
		}
		if ( $this->is_disabled ) {
			$attrs[] = 'disabled="disabled"';
		}
		if ( ! empty( $this->extra ) && 'no' !== $this->extra ) {
			$attrs[] = $this->extra;
		}

		foreach ( $extra_attrs as $key => $val ) {
			if ( is_null( $val ) || false === $val ) {
				continue;
			}
			if ( true === $val ) {
				$attrs[] = esc_attr( $key );
			} else {
				$attrs[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $val ) );
			}
		}

		return implode( ' ', array_filter( $attrs ) );
	}

	/**
	 * Render the field HTML.
	 *
	 * @return string
	 */
	abstract public function render();

	/**
	 * String conversion magic method.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}
