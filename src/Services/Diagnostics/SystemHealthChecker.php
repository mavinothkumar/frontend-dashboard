<?php

namespace FED\Services\Diagnostics;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class SystemHealthChecker
 *
 * Automated diagnostic and health auditor for Frontend Dashboard.
 */
class SystemHealthChecker {

	/**
	 * Static runner shortcut.
	 *
	 * @return array
	 */
	public static function run(): array {
		$checker = new self();
		$audit   = $checker->runAudit();

		// Flattened summary checks for easy UI display
		$summary_checks = [];
		if ( isset( $audit['environment']['php_version'] ) ) {
			$summary_checks[] = [
				'name'   => 'PHP Version',
				'value'  => $audit['environment']['php_version']['value'],
				'status' => $audit['environment']['php_version']['status'],
			];
		}
		if ( isset( $audit['environment']['memory_limit'] ) ) {
			$summary_checks[] = [
				'name'   => 'Memory Limit',
				'value'  => $audit['environment']['memory_limit']['value'],
				'status' => $audit['environment']['memory_limit']['status'],
			];
		}
		if ( isset( $audit['filesystem']['uploads_writable'] ) ) {
			$summary_checks[] = [
				'name'   => 'Uploads Directory',
				'value'  => $audit['filesystem']['uploads_writable']['status'] === 'pass' ? 'Writable' : 'Read-Only',
				'status' => $audit['filesystem']['uploads_writable']['status'],
			];
		}
		if ( isset( $audit['security']['https_enabled'] ) ) {
			$summary_checks[] = [
				'name'   => 'HTTPS / SSL',
				'value'  => $audit['security']['https_enabled']['value'],
				'status' => $audit['security']['https_enabled']['status'],
			];
		}

		// Database status
		$db_all_good = true;
		if ( isset( $audit['database'] ) ) {
			foreach ( $audit['database'] as $tbl ) {
				if ( ( $tbl['status'] ?? 'pass' ) !== 'pass' ) {
					$db_all_good = false;
					break;
				}
			}
		}
		$summary_checks[] = [
			'name'   => 'Database Tables',
			'value'  => $db_all_good ? 'All 8 Tables OK' : 'Tables Missing',
			'status' => $db_all_good ? 'pass' : 'fail',
		];

		return [
			'overall_status' => ( $audit['status'] === 'healthy' ) ? 'pass' : ( $audit['status'] === 'warning' ? 'warning' : 'fail' ),
			'score'          => $audit['score'],
			'checks'         => $summary_checks,
			'raw'            => $audit,
		];
	}

	/**
	 * Run full system health diagnostic audit.
	 *
	 * @return array
	 */
	public function runAudit(): array {
		global $wpdb;

		$results = [
			'status'      => 'healthy',
			'score'       => 100,
			'environment' => [],
			'database'    => [],
			'filesystem'  => [],
			'security'    => [],
		];

		// 1. PHP Environment
		$phpVersion = PHP_VERSION;
		$results['environment']['php_version'] = [
			'label'   => 'PHP Version',
			'value'   => $phpVersion,
			'status'  => version_compare( $phpVersion, '8.0.0', '>=' ) ? 'pass' : 'warning',
			'message' => version_compare( $phpVersion, '8.0.0', '>=' ) ? 'Optimal (>= 8.0)' : 'Recommend PHP 8.0+',
		];

		$extensions = [ 'curl', 'json', 'mbstring', 'openssl', 'gd' ];
		foreach ( $extensions as $ext ) {
			$loaded = extension_loaded( $ext );
			$results['environment'][ 'ext_' . $ext ] = [
				'label'   => "PHP Extension: {$ext}",
				'value'   => $loaded ? 'Enabled' : 'Missing',
				'status'  => $loaded ? 'pass' : 'fail',
			];
			if ( ! $loaded ) {
				$results['score'] -= 10;
			}
		}

		$results['environment']['memory_limit'] = [
			'label'  => 'WP Memory Limit',
			'value'  => WP_MEMORY_LIMIT,
			'status' => ( (int) WP_MEMORY_LIMIT >= 128 ) ? 'pass' : 'warning',
		];

		// 2. Database Health
		$tables = [
			'fed_menu',
			'fed_menu_meta',
			'fed_user_profile',
			'fed_post',
			'fed_payment',
			'fed_payment_items',
			'fed_activity_log',
			'fed_notifications',
		];

		foreach ( $tables as $tbl ) {
			$fullName = $wpdb->prefix . $tbl;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$exists   = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $fullName ) ) === $fullName;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$count    = $exists ? (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$fullName}`" ) : 0;

			$results['database'][ $tbl ] = [
				'label'     => $fullName,
				'installed' => $exists,
				'rows'      => $count,
				'status'    => $exists ? 'pass' : 'fail',
			];

			if ( ! $exists ) {
				$results['score'] -= 15;
			}
		}

		// 3. File System
		$uploadDir = wp_upload_dir();
		$writable  = wp_is_writable( $uploadDir['basedir'] );
		$results['filesystem']['uploads_writable'] = [
			'label'  => 'Uploads Directory Writable',
			'value'  => $uploadDir['basedir'],
			'status' => $writable ? 'pass' : 'fail',
		];

		// 4. Security & Cron
		$isSsl = is_ssl();
		$results['security']['https_enabled'] = [
			'label'  => 'HTTPS / SSL Active',
			'value'  => $isSsl ? 'Active' : 'Insecure (HTTP)',
			'status' => $isSsl ? 'pass' : 'warning',
		];

		if ( $results['score'] < 70 ) {
			$results['status'] = 'critical';
		} elseif ( $results['score'] < 90 ) {
			$results['status'] = 'warning';
		}

		return $results;
	}
}
