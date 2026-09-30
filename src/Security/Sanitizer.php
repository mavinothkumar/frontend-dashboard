<?php

namespace FED\Security;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sanitizer
 *
 * Strict, typed sanitization utility complying with WordPress.org Review Guidelines.
 */
class Sanitizer {

	/**
	 * Sanitize single-line text string.
	 */
	public static function text( $value ): string {
		return is_scalar( $value ) ? sanitize_text_field( wp_unslash( (string) $value ) ) : '';
	}

	/**
	 * Sanitize multi-line textarea string.
	 */
	public static function textarea( $value ): string {
		return is_scalar( $value ) ? sanitize_textarea_field( wp_unslash( (string) $value ) ) : '';
	}

	/**
	 * Sanitize email address.
	 */
	public static function email( $value ): string {
		return is_scalar( $value ) ? sanitize_email( wp_unslash( (string) $value ) ) : '';
	}

	/**
	 * Sanitize URL.
	 */
	public static function url( $value ): string {
		return is_scalar( $value ) ? esc_url_raw( wp_unslash( (string) $value ) ) : '';
	}

	/**
	 * Sanitize slug / key.
	 */
	public static function key( $value ): string {
		return is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
	}

	/**
	 * Cast and validate integer.
	 */
	public static function int( $value, int $default = 0 ): int {
		return is_numeric( $value ) ? (int) $value : $default;
	}

	/**
	 * Cast and validate float.
	 */
	public static function float( $value, float $default = 0.0 ): float {
		return is_numeric( $value ) ? (float) $value : $default;
	}

	/**
	 * Cast and validate boolean.
	 */
	public static function bool( $value, bool $default = false ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_null( $value ) || '' === $value ) {
			return $default;
		}
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Sanitize HTML with allowed tags.
	 */
	public static function html( $value, array $allowedHtml = [] ): string {
		if ( ! is_scalar( $value ) ) {
			return '';
		}
		$tags = ! empty( $allowedHtml ) ? $allowedHtml : wp_kses_allowed_html( 'post' );
		return wp_kses( wp_unslash( (string) $value ), $tags );
	}

	/**
	 * Recursively sanitize an array with a fallback sanitizer.
	 */
	public static function array( array $array, ?callable $sanitizer = null ): array {
		$sanitizer = $sanitizer ?: [ self::class, 'text' ];
		return map_deep( $array, $sanitizer );
	}
}
