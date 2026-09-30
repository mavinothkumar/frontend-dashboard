<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RadioField
 *
 * Radio button group form element.
 */
class RadioField extends BaseField {

	public function render() {
		$options_raw = function_exists( 'fed_get_radio_option_value' )
			? fed_get_radio_option_value( $this->options )
			: (array) $this->options;

		$val = $this->value;
		if ( is_serialized( $val ) ) {
			$val = maybe_unserialize( $val );
		}

		$output = '<div class="fed_radio_group fed-radio-group flex flex-wrap gap-4 py-1">';
		foreach ( $options_raw as $k => $label ) {
			$checked = ( (string) $k === (string) $val ) ? 'checked="checked"' : '';
			$output .= sprintf(
				'<label class="fed_radio_label fed-control-label inline-flex items-center gap-2 cursor-pointer text-sm text-slate-700 font-medium">
					<input type="radio" name="%s" value="%s" %s class="fed-custom-radio %s" />
					<span class="fed-radio-text">%s</span>
				</label>',
				esc_attr( $this->name ),
				esc_attr( $k ),
				$checked,
				esc_attr( $this->class_name ),
				esc_html( $label )
			);
		}
		$output .= '</div>';

		return $output;
	}
}
