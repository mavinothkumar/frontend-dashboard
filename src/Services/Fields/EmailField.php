<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EmailField
 *
 * Email input element.
 */
class EmailField extends TextField {
	protected $type = 'email';

	public function render() {
		$attrs = $this->build_attributes(
			array(
				'type'    => $this->type,
				'value'   => $this->value,
				'pattern' => '[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$',
			)
		);

		return sprintf( '<input %s />', $attrs );
	}
}
