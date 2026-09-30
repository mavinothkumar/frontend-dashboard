<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LabelField
 *
 * Custom formatted HTML or instructional label element.
 */
class LabelField extends BaseField {

	protected $type = 'label';

	public function render() {
		$content = ! empty( $this->options ) ? $this->options : ( ! empty( $this->value ) ? $this->value : '' );
		if ( is_array( $content ) ) {
			$content = implode( "\n", $content );
		}

		$id_attr = ! empty( $this->id_name ) ? sprintf( ' id="%s"', esc_attr( $this->id_name ) ) : '';
		$class   = ! empty( $this->class_name ) ? esc_attr( $this->class_name ) : 'fed-custom-label';

		return sprintf(
			'<div class="%s"%s>%s</div>',
			$class,
			$id_attr,
			wp_kses_post( $content )
		);
	}
}