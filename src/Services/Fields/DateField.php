<?php

namespace FED\Services\Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DateField
 *
 * Flatpickr date & time picker form element.
 */
class DateField extends BaseField {

	protected $type = 'date';

	public function render() {
		$extended = $this->extended;

		$date_format    = isset( $extended['date_format'] ) && ! empty( $extended['date_format'] ) ? esc_attr( $extended['date_format'] ) : 'd-m-Y';
		$mode           = isset( $extended['date_mode'] ) && ! empty( $extended['date_mode'] ) ? esc_attr( $extended['date_mode'] ) : 'single';
		$enable_time    = isset( $extended['enable_time'] ) && 'true' === (string) $extended['enable_time'] ? 'true' : 'false';
		$time_24hr      = isset( $extended['time_24hr'] ) && 'true' === (string) $extended['time_24hr'] ? 'true' : 'false';
		$enable_seconds = isset( $extended['enable_seconds'] ) && 'true' === (string) $extended['enable_seconds'] ? 'true' : 'false';
		$min_date       = isset( $extended['min_date'] ) && ! empty( $extended['min_date'] ) ? esc_attr( $extended['min_date'] ) : '';
		$max_date       = isset( $extended['max_date'] ) && ! empty( $extended['max_date'] ) ? esc_attr( $extended['max_date'] ) : '';

		if ( 'true' === $enable_time ) {
			if ( 'true' === $time_24hr ) {
				$time_part = 'true' === $enable_seconds ? ' H:i:S' : ' H:i';
			} else {
				$time_part = 'true' === $enable_seconds ? ' h:i:S K' : ' h:i K';
			}
			$full_format = $date_format . $time_part;
		} else {
			$full_format = $date_format;
		}

		$placeholder = ! empty( $this->placeholder ) ? $this->placeholder : $full_format;

		$extra_attrs = array(
			'type'             => 'text',
			'data-date-format' => $full_format,
			'data-alt-format'  => $full_format,
			'data-alt-input'   => 'true',
			'data-mode'        => $mode,
			'placeholder'      => $placeholder,
			'data-enable-time' => $enable_time,
			'data-time_24hr'   => $time_24hr,
			'value'            => $this->value,
		);

		if ( '' !== $min_date ) {
			$extra_attrs['data-min-date'] = $min_date;
		}
		if ( '' !== $max_date ) {
			$extra_attrs['data-max-date'] = $max_date;
		}
		if ( 'true' === $enable_seconds ) {
			$extra_attrs['data-enable-seconds'] = 'true';
		}

		if ( strpos( $this->class_name, 'flatpickr' ) === false ) {
			$this->class_name = trim( $this->class_name . ' flatpickr' );
		}

		return sprintf( '<input %s />', $this->build_attributes( $extra_attrs ) );
	}
}
