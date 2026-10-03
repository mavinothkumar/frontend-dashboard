<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TextareaField
 *
 * Multi-line textarea form element.
 */
class TextareaField extends BaseField {

	public function render() {
		$rows = $this->get_data( 'input_row', $this->attributes );
		if ( empty( $rows ) ) {
			$rows = $this->get_data( 'rows', $this->attributes );
		}
		if ( empty( $rows ) ) {
			$rows = $this->get_data( 'row', $this->attributes );
		}

		$extra_attrs = array();
		if ( ! empty( $rows ) ) {
			$extra_attrs['rows'] = (int) $rows;
		}

		$attrs = $this->build_attributes( $extra_attrs );
		return sprintf( '<textarea %s>%s</textarea>', $attrs, esc_textarea( $this->value ) );
	}
}
