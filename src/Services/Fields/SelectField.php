<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SelectField
 *
 * Dropdown select form element (supporting single and multi-select).
 */
class SelectField extends BaseField {

	public function render() {
		$is_multiple = isset( $this->extended['multiple'] ) && 'Enable' === $this->extended['multiple'];
		$name        = $is_multiple ? $this->name . '[]' : $this->name;
		$options_raw = function_exists( 'fed_get_select_option_value' )
			? fed_get_select_option_value( $this->options )
			: (array) $this->options;

		$val = $this->value;
		if ( is_serialized( $val ) ) {
			$val = maybe_unserialize( $val );
		}

		$options_html = '';
		foreach ( $options_raw as $key => $label ) {
			$display_label = $label;
			if ( is_string( $display_label ) ) {
				$trimmed = trim( $display_label );
				if ( ( str_starts_with( $trimmed, '{' ) && str_ends_with( $trimmed, '}' ) ) || ( str_starts_with( $trimmed, '[' ) && str_ends_with( $trimmed, ']' ) ) ) {
					$decoded = json_decode( $trimmed, true );
					if ( is_array( $decoded ) ) {
						$display_label = $decoded['name'] ?? $decoded['title'] ?? $decoded['label'] ?? $display_label;
					}
				}
			} elseif ( is_array( $display_label ) ) {
				$display_label = $display_label['name'] ?? $display_label['title'] ?? $display_label['label'] ?? (string) reset( $display_label );
			} elseif ( is_object( $display_label ) ) {
				$display_label = $display_label->name ?? $display_label->title ?? $display_label->label ?? (string) $display_label;
			}

			$selected = '';
			if ( is_array( $val ) && in_array( $key, $val, true ) ) {
				$selected = 'selected="selected"';
			} elseif ( ! is_array( $val ) && (string) $key === (string) $val ) {
				$selected = 'selected="selected"';
			}
			$options_html .= sprintf(
				'<option value="%s" %s>%s</option>',
				esc_attr( $key ),
				$selected,
				esc_html( (string) $display_label )
			);
		}

		$extra_attrs = [];
		if ( $is_multiple ) {
			$extra_attrs['name']     = $this->name . '[]';
			$extra_attrs['multiple'] = 'multiple';
			if ( strpos( $this->class_name, 'fed_multi_select' ) === false ) {
				$this->class_name = trim( $this->class_name . ' fed_multi_select' );
			}
		}

		$attrs = $this->build_attributes( $extra_attrs );

		return sprintf( '<select %s>%s</select>', $attrs, $options_html );
	}
}
