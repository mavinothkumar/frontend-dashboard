<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedLogsTable implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000005_create_fed_logs_table';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$table           = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );

		$sql = "CREATE TABLE `$table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			user_login VARCHAR(60) NOT NULL DEFAULT '',
			user_email VARCHAR(100) NOT NULL DEFAULT '',
			user_display_name VARCHAR(250) NOT NULL DEFAULT '',
			channel VARCHAR(50) NOT NULL DEFAULT 'system',
			level VARCHAR(20) NOT NULL DEFAULT 'info',
			action VARCHAR(255) NOT NULL DEFAULT '',
			message TEXT NULL,
			context LONGTEXT NULL,
			action_type VARCHAR(100) NOT NULL DEFAULT 'system',
			action_title VARCHAR(255) NOT NULL DEFAULT '',
			description TEXT NULL,
			status VARCHAR(20) NOT NULL DEFAULT 'info',
			ip_address VARCHAR(45) NOT NULL DEFAULT '',
			user_agent VARCHAR(255) NULL DEFAULT '',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY channel (channel),
			KEY level (level),
			KEY action_type (action_type),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql );

		// Clean up legacy redundant fed_logs table
		$legacy_logs_table = $wpdb->prefix . 'fed_logs';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$legacy_logs_table}'" ) === $legacy_logs_table ) {
			// phpcs:ignore PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
			$wpdb->query( "DROP TABLE IF EXISTS `{$legacy_logs_table}`" );
		}
	}

	public function down(): void {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_ACTIVITY_LOG' ) ? BC_FED_TABLE_ACTIVITY_LOG : 'fed_activity_log' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_logs`" );
	}
}
