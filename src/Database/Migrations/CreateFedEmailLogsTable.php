<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class CreateFedEmailLogsTable
 *
 * Migration to create the unified email delivery, queue, and audit logs table.
 */
class CreateFedEmailLogsTable implements MigrationInterface {

	/**
	 * Unique migration version.
	 *
	 * @return string
	 */
	public function getVersion(): string {
		return '2026_10_28_000007_create_fed_email_logs_table';
	}

	/**
	 * Run migration.
	 */
	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$table           = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

		$sql = "CREATE TABLE `$table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			uuid VARCHAR(64) NOT NULL DEFAULT '',
			event_name VARCHAR(100) NOT NULL DEFAULT '',
			to_email VARCHAR(255) NOT NULL DEFAULT '',
			subject VARCHAR(255) NOT NULL DEFAULT '',
			body LONGTEXT NULL,
			headers TEXT NULL,
			attachments TEXT NULL,
			provider VARCHAR(50) NOT NULL DEFAULT 'wp_mail',
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			attempts INT(5) UNSIGNED NOT NULL DEFAULT 0,
			max_attempts INT(5) UNSIGNED NOT NULL DEFAULT 3,
			error_message TEXT NULL,
			response_data TEXT NULL,
			retry_after DATETIME NULL,
			sent_at DATETIME NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY uuid (uuid),
			KEY to_email (to_email),
			KEY provider (provider),
			KEY status (status),
			KEY retry_after (retry_after),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql );
	}

	/**
	 * Revert migration.
	 */
	public function down(): void {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
	}
}
