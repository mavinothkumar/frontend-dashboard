<?php

namespace FED\Services\Logging;

use FED\Models\AuditLog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AuditLogger
 *
 * Enterprise dual-destination audit logging engine (Disk + Database).
 */
class AuditLogger {

	const LEVEL_DEBUG     = 'debug';
	const LEVEL_INFO      = 'info';
	const LEVEL_WARNING   = 'warning';
	const LEVEL_ERROR     = 'error';
	const LEVEL_CRITICAL  = 'critical';

	/**
	 * Log an event to both database and file storage.
	 *
	 * @param string $level   Severity level.
	 * @param string $message Event description.
	 * @param array  $context Extra metadata.
	 * @param string $channel Category (e.g. 'auth', 'payment', 'system', 'security', 'cron').
	 * @return AuditLog|null
	 */
	public function log( string $level, string $message, array $context = [], string $channel = 'system' ): ?AuditLog {
		$userId    = get_current_user_id() ?: 0;
		$user      = $userId ? get_user_by( 'id', $userId ) : null;
		$ipAddress = $this->getClientIp();
		$userAgent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 255 ) : '';

		$status = 'info';
		if ( in_array( $level, [ 'warning', 'error', 'critical' ], true ) ) {
			$status = ( 'warning' === $level ) ? 'warning' : 'error';
		}

		$description = '';
		if ( ! empty( $context ) ) {
			$description = wp_json_encode( $context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		}

		// 1. Write to unified database audit log
		try {
			$log = AuditLog::create( [
				'user_id'           => $userId,
				'user_login'        => $user ? $user->user_login : ( 0 === $userId ? 'System' : 'Guest' ),
				'user_email'        => $user ? $user->user_email : '',
				'user_display_name' => $user ? $user->display_name : ( 0 === $userId ? 'System / Cron' : 'Guest' ),
				'channel'           => $channel,
				'level'             => $level,
				'action'            => $message,
				'message'           => $message,
				'context'           => $context,
				'action_type'       => $channel,
				'action_title'      => $message,
				'description'       => $description,
				'status'            => $status,
				'ip_address'        => $ipAddress,
				'user_agent'        => $userAgent,
			] );
		} catch ( \Throwable $e ) {
			$log = null;
		}

		// 2. Write to disk log file
		$this->writeToFile( $level, $channel, $message, $context, $userId, $ipAddress );

		return $log;
	}

	public function info( string $message, array $context = [], string $channel = 'system' ) {
		return $this->log( self::LEVEL_INFO, $message, $context, $channel );
	}

	public function warning( string $message, array $context = [], string $channel = 'system' ) {
		return $this->log( self::LEVEL_WARNING, $message, $context, $channel );
	}

	public function error( string $message, array $context = [], string $channel = 'system' ) {
		return $this->log( self::LEVEL_ERROR, $message, $context, $channel );
	}

	public function critical( string $message, array $context = [], string $channel = 'security' ) {
		return $this->log( self::LEVEL_CRITICAL, $message, $context, $channel );
	}

	/**
	 * Write formatted line to disk log file.
	 */
	protected function writeToFile( string $level, string $channel, string $message, array $context, int $userId, string $ipAddress ): void {
		$uploadDir = wp_upload_dir();
		$logDir    = trailingslashit( $uploadDir['basedir'] ) . 'fed-logs';

		if ( ! file_exists( $logDir ) ) {
			wp_mkdir_p( $logDir );
			// Write index.php / .htaccess to prevent directory browsing
			file_put_contents( $logDir . '/index.php', '<?php // Silence is golden' );
			file_put_contents( $logDir . '/.htaccess', 'Deny from all' );
		}

		$logFile = sprintf( '%s/fed-%s-%s.log', $logDir, $channel, gmdate( 'Y-m-d' ) );
		$contextStr = ! empty( $context ) ? ' ' . wp_json_encode( $context ) : '';
		$line = sprintf(
			"[%s UTC] [%s] [%s] User:%d IP:%s - %s%s\n",
			gmdate( 'Y-m-d H:i:s' ),
			strtoupper( $level ),
			strtoupper( $channel ),
			$userId,
			$ipAddress,
			$message,
			$contextStr
		);

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $line, 3, $logFile );
	}

	/**
	 * Get sanitized client IP.
	 */
	protected function getClientIp(): string {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		}
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ips = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			return trim( $ips[0] );
		}
		return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '127.0.0.1';
	}
}
