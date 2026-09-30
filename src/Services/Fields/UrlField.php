<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UrlField
 *
 * URL input element.
 */
class UrlField extends TextField {
	protected $type = 'url';

	public function render() {
		if ( empty( $this->placeholder ) ) {
			$this->placeholder = 'https://example.com';
		}

		$attrs = $this->build_attributes( [
			'type'  => $this->type,
			'value' => $this->value,
		] );

		return sprintf( '<input %s />', $attrs );
	}
}
