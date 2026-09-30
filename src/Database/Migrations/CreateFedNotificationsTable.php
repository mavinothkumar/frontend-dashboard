<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedNotificationsTable implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000006_create_fed_notifications_table';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$table           = $wpdb->prefix . 'fed_notifications';

		$sql = "CREATE TABLE `$table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			title VARCHAR(255) NOT NULL,
			message TEXT NOT NULL,
			type VARCHAR(50) NOT NULL DEFAULT 'info',
			action_url VARCHAR(255) NULL,
			is_read TINYINT(1) NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY is_read (is_read),
			KEY created_at (created_at)
		) $charset_collate;";

		dbDelta( $sql );
	}

	public function down(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_notifications`" );
	}
}
