<?php

namespace FED\Services\Ui;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AlertManager
 *
 * Flash alerts, banner messages, and SweetAlert confirmation builder.
 */
class AlertManager {

	const FLASH_KEY = 'fed_flash_messages';

	/**
	 * Set a flash message for next page render.
	 *
	 * @param string $type ('success', 'error', 'warning', 'info')
	 * @param string $message
	 * @return void
	 */
	public static function flash( string $type, string $message ): void {
		if ( ! session_id() && ! headers_sent() ) {
			@session_start();
		}
		$_SESSION[ self::FLASH_KEY ][] = array(
			'type'    => sanitize_key( $type ),
			'message' => sanitize_text_field( $message ),
		);
	}

	/**
	 * Get and clear all flash messages.
	 *
	 * @return array
	 */
	public static function getFlashes(): array {
		if ( ! session_id() && ! headers_sent() ) {
			@session_start();
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$messages = isset( $_SESSION[ self::FLASH_KEY ] ) ? (array) $_SESSION[ self::FLASH_KEY ] : array();
		unset( $_SESSION[ self::FLASH_KEY ] );
		return $messages;
	}

	/**
	 * Render a Tailwind banner alert.
	 *
	 * @param string $type
	 * @param string $message
	 * @param bool   $dismissible
	 * @return string
	 */
	public static function renderBanner( string $type, string $message, bool $dismissible = true ): string {
		$colors = array(
			'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
			'error'   => 'bg-rose-50 text-rose-800 border-rose-300',
			'warning' => 'bg-amber-50 text-amber-800 border-amber-300',
			'info'    => 'bg-sky-50 text-sky-800 border-sky-300',
		);

		$colorClass = $colors[ $type ] ?? $colors['info'];
		$msgHtml    = esc_html( $message );

		return '<div class="flex items-center justify-between p-4 mb-4 text-sm rounded-xl border shadow-xs ' . esc_attr( $colorClass ) . '" role="alert">' .
			'<div class="flex items-center gap-2">' .
			'<span>' . $msgHtml . '</span>' .
			'</div>' .
			'</div>';
	}
}
