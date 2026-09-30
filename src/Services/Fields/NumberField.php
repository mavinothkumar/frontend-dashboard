<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NumberField
 *
 * Number input element with min, max, step support.
 */
class NumberField extends TextField {

	protected $type = 'number';

	public function render() {
		$min  = $this->get_data( 'input_min', $this->attributes );
		if ( '' === $min ) {
			$min = $this->get_data( 'min', $this->attributes, null );
		}

		$max  = $this->get_data( 'input_max', $this->attributes );
		if ( '' === $max ) {
			$max = $this->get_data( 'max', $this->attributes, null );
		}

		$step = $this->get_data( 'input_step', $this->attributes );
		if ( '' === $step ) {
			$step = $this->get_data( 'step', $this->attributes, 'any' );
		}

		$extra = [
			'type'  => $this->type,
			'value' => $this->value,
		];

		if ( null !== $min && '' !== $min ) {
			$extra['min'] = $min;
		}
		if ( null !== $max && '' !== $max ) {
			$extra['max'] = $max;
		}
		if ( null !== $step && '' !== $step ) {
			$extra['step'] = $step;
		}

		$attrs = $this->build_attributes( $extra );

		return sprintf( '<input %s />', $attrs );
	}
}
