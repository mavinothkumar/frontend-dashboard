<?php

namespace FED\Services\Cron;

use FED\Models\AuditLog;
use FED\Models\Notification;
use FED\Hooks\HookLoader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CronManager
 *
 * Background scheduler and periodic maintenance runner.
 */
class CronManager {

	const DAILY_CLEANUP_HOOK = 'fed_daily_maintenance_cron';

	public function register_hooks( HookLoader $loader ): void {
		$loader->add_action( self::DAILY_CLEANUP_HOOK, $this, 'runDailyCleanup' );
		$loader->add_filter( 'cron_schedules', $this, 'registerCustomSchedules' );

		// Schedule cron if not already scheduled
		if ( ! wp_next_scheduled( self::DAILY_CLEANUP_HOOK ) ) {
			wp_schedule_event( time() + 3600, 'daily', self::DAILY_CLEANUP_HOOK );
		}
	}

	/**
	 * Register custom cron intervals.
	 *
	 * @param array $schedules
	 * @return array
	 */
	public function registerCustomSchedules( array $schedules ): array {
		$schedules['every_five_minutes'] = [
			'interval' => 300,
			'display'  => __( 'Every 5 Minutes', 'frontend-dashboard' ),
		];
		$schedules['weekly'] = [
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'frontend-dashboard' ),
		];
		return $schedules;
	}

	/**
	 * Run daily database and file maintenance cleanup.
	 *
	 * @return void
	 */
	public function runDailyCleanup(): void {
		global $wpdb;

		// 1. Delete audit logs older than 30 days
		$logsTable = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );
		$wpdb->query( "DELETE FROM `{$logsTable}` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 30 DAY)" );

		// 2. Delete read notifications older than 60 days
		$notifTable = $wpdb->prefix . 'fed_notifications';
		$wpdb->query( "DELETE FROM `{$notifTable}` WHERE `is_read` = 1 AND `created_at` < DATE_SUB(NOW(), INTERVAL 60 DAY)" );

		do_action( 'fed_daily_cleanup_completed' );
	}
}
