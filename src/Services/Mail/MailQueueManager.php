<?php

namespace FED\Services\Mail;

use FED\Core\Logger;
use FED\Services\Mail\Contracts\MailDriverInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MailQueueManager
 *
 * Enterprise Queue, Retries, Failure Exposure, and Delivery Audit Logging Engine.
 */
class MailQueueManager {

	const CRON_HOOK        = 'fed_process_email_queue';
	const SINGLE_CRON_HOOK = 'fed_process_email_queue_single';

	protected MailDriverManager $driverManager;

	public function __construct( ?MailDriverManager $driverManager = null ) {
		$this->driverManager = $driverManager ?? new MailDriverManager();
	}

	/**
	 * Register background cron runner.
	 */
	public function registerHooks(): void {
		add_action( self::CRON_HOOK, array( $this, 'processQueue' ) );
		add_action( self::SINGLE_CRON_HOOK, array( $this, 'processQueue' ) );

		add_filter(
			'cron_schedules',
			function ( $schedules ) {
				if ( ! isset( $schedules['fed_every_five_minutes'] ) ) {
					$schedules['fed_every_five_minutes'] = array(
						'interval' => 300,
						'display'  => __( 'Every 5 Minutes (Frontend Dashboard)', 'frontend-dashboard' ),
					);
				}
				return $schedules;
			}
		);

		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'fed_every_five_minutes', self::CRON_HOOK );
		}
	}

	/**
	 * Dispatch an email message with queueing, idempotency, and audit logging.
	 *
	 * @param MailMessage $message
	 * @param bool        $forceSync
	 * @return MailResult
	 */
	public function dispatch( MailMessage $message, bool $forceSync = false ): MailResult {
		global $wpdb;
		$table    = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
		$settings = get_option( 'fed_settings_email', array() );
		$driver   = $this->driverManager->getActiveDriver();

		// Idempotency: Check if this exact UUID was already processed, queued, or in flight
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$existing = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, status FROM `$table` WHERE uuid = %s LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$message->getUuid()
			),
			ARRAY_A
		);

		if ( $existing ) {
			$existing_status = $existing['status'];

			// If already sent, queued, or actively processing, short-circuit to prevent duplicate dispatches
			if ( in_array( $existing_status, array( 'sent', 'queued', 'processing' ), true ) ) {
				return MailResult::success(
					$message->getUuid(),
					array(
						'idempotent_duplicate' => true,
						'status'               => $existing_status,
						'log_id'               => (int) $existing['id'],
					)
				);
			}

			// If explicitly cancelled previously, maintain cancellation
			if ( 'cancelled' === $existing_status ) {
				return MailResult::failure( __( 'Email delivery was cancelled by filter.', 'frontend-dashboard' ) );
			}
		}

		/**
		 * Pre-send filter to allow cancelling or withholding email delivery.
		 *
		 * Return false to cancel email dispatch before transport.
		 *
		 * @param bool                $allow_send Whether to proceed with dispatch.
		 * @param MailMessage         $message    The email message payload.
		 * @param MailDriverInterface $driver     The active mail driver.
		 */
		$allow_send = apply_filters( 'fed_pre_send_mail', true, $message, $driver );

		if ( ! $allow_send ) {
			$this->recordLog(
				$message,
				$driver->getId(),
				'cancelled',
				0,
				__( 'Email delivery was cancelled by fed_pre_send_mail filter hook.', 'frontend-dashboard' )
			);

			return MailResult::failure( __( 'Email delivery was cancelled by filter.', 'frontend-dashboard' ) );
		}

		$queue_enabled = 'yes' === fed_get_data( 'queue.enabled', $settings, 'no' );
		$should_queue  = $queue_enabled && ! $forceSync;

		if ( $should_queue ) {
			$log_id = $this->recordLog( $message, $driver->getId(), 'queued', 0 );

			// Spawn prompt background runner if not already scheduled
			if ( ! wp_next_scheduled( self::SINGLE_CRON_HOOK ) ) {
				wp_schedule_single_event( time() + 2, self::SINGLE_CRON_HOOK );
			}

			return MailResult::success(
				$message->getUuid(),
				array(
					'queued' => true,
					'log_id' => $log_id,
				)
			);
		}

		// Synchronous delivery
		$result = $driver->send( $message );
		$status = $result->isSuccess() ? 'sent' : 'failed';
		$error  = $result->getErrorMessage();

		$log_id = $this->recordLog(
			$message,
			$driver->getId(),
			$status,
			1,
			$error,
			$result->getResponseData(),
			$result->isSuccess() ? current_time( 'mysql' ) : null
		);

		if ( $result->isSuccess() ) {
			do_action( 'fed_mail_sent', $log_id, $message, $result );
		} else {
			do_action( 'fed_mail_failed', $log_id, $message, $result );
		}

		return $result;
	}

	/**
	 * Process queued and backoff-retry items from the database.
	 *
	 * @param int $limit
	 * @return int Number of processed emails.
	 */
	public function processQueue( int $limit = 10 ): int {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
		$now   = current_time( 'mysql' );

		// Stale in-flight recovery: recover items left in 'processing' for > 15 minutes (e.g. killed process or server restart)
		$stale_time = gmdate( 'Y-m-d H:i:s', time() - ( 15 * MINUTE_IN_SECONDS ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE `$table` 
				SET status = 'queued', updated_at = %s 
				WHERE status = 'processing' AND updated_at < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$now,
				$stale_time
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM `$table` 
				WHERE (status = 'queued') 
				   OR (status = 'failed' AND attempts < max_attempts AND retry_after IS NOT NULL AND retry_after <= %s)
				ORDER BY id ASC LIMIT %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$now,
				$limit
			),
			ARRAY_A
		);

		if ( empty( $items ) ) {
			return 0;
		}

		$processed     = 0;
		$driver        = $this->driverManager->getActiveDriver();
		$claimed_uuids = array();

		foreach ( $items as $row ) {
			$uuid = $row['uuid'];

			// In-batch deduplication guard
			if ( ! empty( $uuid ) && isset( $claimed_uuids[ $uuid ] ) ) {
				continue;
			}

			// Atomic claim guard: transition status to 'processing' before dispatching.
			// Guarantees that concurrent workers or simultaneous processQueue calls do not double-process rows.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$claimed = $wpdb->query(
				$wpdb->prepare(
					"UPDATE `$table` 
					SET status = 'processing', updated_at = %s 
					WHERE id = %d AND status IN ('queued', 'failed')", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$now,
					(int) $row['id']
				)
			);

			if ( ! $claimed ) {
				// Record was already claimed or processed by another concurrent process
				continue;
			}

			if ( ! empty( $uuid ) ) {
				$claimed_uuids[ $uuid ] = true;
			}

			$headers     = ! empty( $row['headers'] ) ? (array) json_decode( $row['headers'], true ) : array();
			$attachments = ! empty( $row['attachments'] ) ? (array) json_decode( $row['attachments'], true ) : array();

			$message = new MailMessage(
				$row['to_email'],
				$row['subject'],
				$row['body'],
				$headers,
				$attachments,
				$row['event_name'],
				true,
				array(),
				$uuid
			);

			$attempts = (int) $row['attempts'] + 1;
			$result   = $driver->send( $message );

			if ( $result->isSuccess() ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update(
					$table,
					array(
						'status'        => 'sent',
						'attempts'      => $attempts,
						'error_message' => null,
						'sent_at'       => current_time( 'mysql' ),
						'updated_at'    => current_time( 'mysql' ),
					),
					array( 'id' => $row['id'] )
				);

				do_action( 'fed_mail_sent', (int) $row['id'], $message, $result );
			} else {
				$max_attempts = (int) $row['max_attempts'];
				$retry_after  = null;

				if ( $attempts < $max_attempts ) {
					// Exponential backoff: attempt 1 -> 2m, attempt 2 -> 10m, attempt 3 -> 30m
					$delay_minutes   = ( 1 === $attempts ) ? 2 : ( ( 2 === $attempts ) ? 10 : 30 );
					$retry_timestamp = time() + ( $delay_minutes * MINUTE_IN_SECONDS );
					$retry_after     = gmdate( 'Y-m-d H:i:s', $retry_timestamp );

					// Precisely schedule wake-up cron single event at the retry backoff timestamp
					wp_schedule_single_event( $retry_timestamp, self::SINGLE_CRON_HOOK );
				}

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$wpdb->update(
					$table,
					array(
						'status'        => 'failed',
						'attempts'      => $attempts,
						'error_message' => $result->getErrorMessage(),
						'retry_after'   => $retry_after,
						'updated_at'    => current_time( 'mysql' ),
					),
					array( 'id' => $row['id'] )
				);

				do_action( 'fed_mail_failed', (int) $row['id'], $message, $result );
			}

			++$processed;
		}

		// If batch limit was reached, schedule another single runner in 2 seconds to drain remaining queue
		if ( count( $items ) >= $limit && ! wp_next_scheduled( self::SINGLE_CRON_HOOK ) ) {
			wp_schedule_single_event( time() + 2, self::SINGLE_CRON_HOOK );
		}

		return $processed;
	}

	/**
	 * Manually resend an email by log ID.
	 *
	 * @param int $logId
	 * @return MailResult
	 */
	public function resend( int $logId ): MailResult {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM `$table` WHERE id = %d LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$logId
			),
			ARRAY_A
		);

		if ( ! $row ) {
			return MailResult::failure( __( 'Email record not found.', 'frontend-dashboard' ) );
		}

		$headers     = ! empty( $row['headers'] ) ? (array) json_decode( $row['headers'], true ) : array();
		$attachments = ! empty( $row['attachments'] ) ? (array) json_decode( $row['attachments'], true ) : array();

		$message = new MailMessage(
			$row['to_email'],
			$row['subject'],
			$row['body'],
			$headers,
			$attachments,
			$row['event_name'],
			true,
			array(),
			wp_generate_uuid4() // Generate fresh UUID for the resend attempt
		);

		$driver   = $this->driverManager->getActiveDriver();
		$result   = $driver->send( $message );
		$attempts = (int) $row['attempts'] + 1;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->update(
			$table,
			array(
				'provider'      => $driver->getId(),
				'status'        => $result->isSuccess() ? 'sent' : 'failed',
				'attempts'      => $attempts,
				'error_message' => $result->isSuccess() ? null : $result->getErrorMessage(),
				'sent_at'       => $result->isSuccess() ? current_time( 'mysql' ) : $row['sent_at'],
				'updated_at'    => current_time( 'mysql' ),
			),
			array( 'id' => $logId )
		);

		return $result;
	}

	/**
	 * Record or update delivery log in the database.
	 *
	 * @param MailMessage $message
	 * @param string      $provider
	 * @param string      $status
	 * @param int         $attempts
	 * @param string|null $errorMessage
	 * @param mixed       $responseData
	 * @param string|null $sentAt
	 * @return int Log ID
	 */
	protected function recordLog(
		MailMessage $message,
		string $provider,
		string $status,
		int $attempts = 0,
		?string $errorMessage = null,
		mixed $responseData = null,
		?string $sentAt = null
	): int {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

		// Fast lookup: if a record with this UUID already exists, return its ID
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$existing_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `$table` WHERE uuid = %s LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$message->getUuid()
			)
		);

		if ( ! empty( $existing_id ) ) {
			return (int) $existing_id;
		}

		$data = array(
			'uuid'          => $message->getUuid(),
			'event_name'    => $message->getEventName(),
			'to_email'      => $message->getTo(),
			'subject'       => $message->getSubject(),
			'body'          => $message->getBody(),
			'headers'       => ! empty( $message->getHeaders() ) ? wp_json_encode( $message->getHeaders() ) : null,
			'attachments'   => ! empty( $message->getAttachments() ) ? wp_json_encode( $message->getAttachments() ) : null,
			'provider'      => sanitize_key( $provider ),
			'status'        => sanitize_key( $status ),
			'attempts'      => $attempts,
			'max_attempts'  => 3,
			'error_message' => $errorMessage,
			'response_data' => ! empty( $responseData ) ? wp_json_encode( $responseData ) : null,
			'sent_at'       => $sentAt,
			'created_at'    => current_time( 'mysql' ),
			'updated_at'    => current_time( 'mysql' ),
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$inserted = $wpdb->insert( $table, $data );

		if ( false === $inserted || empty( $wpdb->insert_id ) ) {
			// Catch race condition: if another process just inserted this UUID, return existing ID
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$collided_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM `$table` WHERE uuid = %s LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$message->getUuid()
				)
			);
			if ( ! empty( $collided_id ) ) {
				return (int) $collided_id;
			}
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Delete logs older than retention period (days).
	 *
	 * @param int $days
	 * @return int Deleted rows
	 */
	public function cleanupLogs( int $days = 30 ): int {
		global $wpdb;
		$table     = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
		$threshold = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM `$table` WHERE created_at < %s", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$threshold
			)
		);
	}
}
