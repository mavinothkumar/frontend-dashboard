<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CheckboxField
 *
 * Checkbox form element (supporting single checkbox with label/default_value and multi-options).
 */
class CheckboxField extends BaseField {

	public function render() {
		$default_value = $this->get_data( 'default_value', $this->attributes, 'yes' );
		if ( empty( $default_value ) ) {
			$default_value = 'yes';
		}

		$label = $this->get_data( 'label', $this->attributes );
		if ( ! empty( $this->extended['label'] ) ) {
			$label = htmlspecialchars_decode( $this->extended['label'] );
		}

		$val = $this->value;
		if ( is_serialized( $val ) ) {
			$val = maybe_unserialize( $val );
		}

		// Check if multi-options are provided as non-empty array
		if ( is_array( $this->options ) && ! empty( $this->options ) ) {
			$options_raw = function_exists( 'fed_get_checkbox_option_value' )
				? fed_get_checkbox_option_value( $this->options )
				: $this->options;

			$output = '<div class="fed_checkbox_group fed-checkbox-group flex flex-wrap gap-4 py-1">';
			foreach ( $options_raw as $k => $opt_label ) {
				$checked = ( is_array( $val ) && in_array( $k, $val, true ) ) ? 'checked="checked"' : '';
				$output .= sprintf(
					'<label class="fed_checkbox_label fed-control-label inline-flex items-center gap-2 cursor-pointer text-sm text-slate-700 font-medium">
						<input type="checkbox" name="%s[]" value="%s" %s class="fed-custom-checkbox %s" />
						<span class="fed-checkbox-text">%s</span>
					</label>',
					esc_attr( $this->name ),
					esc_attr( $k ),
					$checked,
					esc_attr( $this->class_name ),
					esc_html( $opt_label )
				);
			}
			$output .= '</div>';
			return $output;
		}

		// Single checkbox
		$checked = checked( $val, $default_value, false );
		$attrs   = $this->build_attributes(
			array(
				'type'  => 'checkbox',
				'value' => $default_value,
				'class' => trim( 'fed-custom-checkbox ' . $this->class_name ),
			)
		);

		return sprintf(
			'<label class="fed_checkbox_label fed-control-label inline-flex items-center gap-2 cursor-pointer text-sm text-slate-700 font-medium">
				<input %s %s />
				<span class="fed-checkbox-text">%s</span>
			</label>',
			$attrs,
			$checked,
			$label
		);
	}
}
