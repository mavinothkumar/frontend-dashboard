<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class HiddenField
 *
 * Hidden input element.
 */
class HiddenField extends BaseField {

	public function render() {
		$attrs = $this->build_attributes(
			array(
				'type'  => 'hidden',
				'value' => $this->value,
			)
		);

		return sprintf( '<input %s />', $attrs );
	}
}
