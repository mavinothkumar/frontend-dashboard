<?php

namespace FED\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Logger
 *
 * Enterprise structured logging for Frontend Dashboard.
 */
class Logger {

	/**
	 * Log an informational message.
	 *
	 * @param string $message
	 * @param array  $context
	 */
	public static function info( $message, array $context = array() ) {
		self::log( 'INFO', $message, $context );
	}

	/**
	 * Log a warning message.
	 *
	 * @param string $message
	 * @param array  $context
	 */
	public static function warning( $message, array $context = array() ) {
		self::log( 'WARNING', $message, $context );
	}

	/**
	 * Log an error message or exception.
	 *
	 * @param string|\Throwable $message
	 * @param array             $context
	 */
	public static function error( $message, array $context = array() ) {
		if ( $message instanceof \Throwable ) {
			$context['file']  = $message->getFile();
			$context['line']  = $message->getLine();
			$context['trace'] = $message->getTraceAsString();
			$message          = $message->getMessage();
		}
		self::log( 'ERROR', $message, $context );
	}

	/**
	 * Internal log handler.
	 *
	 * @param string $level
	 * @param string $message
	 * @param array  $context
	 */
	protected static function log( $level, $message, array $context = array() ) {
		$context_str = ! empty( $context ) ? ' ' . wp_json_encode( $context ) : '';
		$formatted   = sprintf( '[FED %s] %s%s', $level, $message, $context_str );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $formatted );
		}

		do_action( 'fed_logged_message', $level, $message, $context );
	}
}
