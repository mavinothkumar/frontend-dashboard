<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TextField
 *
 * Single line text input element.
 */
class TextField extends BaseField {

	protected $type = 'text';

	public function render() {
		$attrs = $this->build_attributes( [
			'type'  => $this->type,
			'value' => $this->value,
		] );

		return sprintf( '<input %s />', $attrs );
	}
}
