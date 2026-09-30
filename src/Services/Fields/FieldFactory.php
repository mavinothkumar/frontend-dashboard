<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FieldFactory
 *
 * Factory and registry for creating and rendering extensible OOP form elements.
 */
class FieldFactory {

	/**
	 * @var array Registry mapping field types to class names.
	 */
	protected static $registry = [
		'text'        => TextField::class,
		'single_line' => TextField::class,
		'textarea'    => TextareaField::class,
		'multi_line'  => TextareaField::class,
		'select'      => SelectField::class,
		'checkbox'    => CheckboxField::class,
		'radio'       => RadioField::class,
		'number'      => NumberField::class,
		'email'       => EmailField::class,
		'password'    => PasswordField::class,
		'url'         => UrlField::class,
		'hidden'      => HiddenField::class,
		'file'        => FileField::class,
		'files'       => FileField::class,
		'date'        => DateField::class,
		'color'       => ColorField::class,
		'wp_editor'   => EditorField::class,
		'editor'      => EditorField::class,
		'label'       => LabelField::class,
		'table'       => TableField::class,
	];

	/**
	 * Register a custom or third-party field class.
	 *
	 * @param string $type Field type identifier.
	 * @param string $class_name Fully qualified class name extending BaseField.
	 */
	public static function register( $type, $class_name ) {
		if ( is_subclass_of( $class_name, BaseField::class ) ) {
			self::$registry[ strtolower( $type ) ] = $class_name;
		}
	}

	/**
	 * Create a field instance.
	 *
	 * @param string $type
	 * @param array  $attributes
	 * @return BaseField
	 */
	public static function create( $type, array $attributes = [] ) {
		$key = strtolower( $type );
		$class = isset( self::$registry[ $key ] ) ? self::$registry[ $key ] : TextField::class;

		return new $class( $attributes );
	}

	/**
	 * Render a field by type and attributes.
	 *
	 * @param string $type
	 * @param array  $attributes
	 * @return string
	 */
	public static function render( $type, array $attributes = [] ) {
		$field = self::create( $type, $attributes );
		$html  = $field->render();

		// Extensible filters for developers
		$html = apply_filters( 'fed_render_field', $html, $type, $attributes, $field );
		$html = apply_filters( 'fed_render_field_' . strtolower( $type ), $html, $attributes, $field );

		return $html;
	}

	/**
	 * Get the entire registered types list.
	 *
	 * @return array
	 */
	public static function get_registered_types() {
		return array_keys( self::$registry );
	}
}
