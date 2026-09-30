<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedPostTable implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000003_create_fed_post_table';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$post_table      = $wpdb->prefix . 'fed_post';

		$sql_post = "CREATE TABLE `$post_table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			post_type VARCHAR(191) NOT NULL DEFAULT 'post',
			label VARCHAR(255) NULL,
			input_meta VARCHAR(191) NULL,
			input_type VARCHAR(50) NULL,
			input_order INT(10) NOT NULL DEFAULT 0,
			placeholder VARCHAR(255) NULL,
			class_name VARCHAR(255) NULL,
			id_name VARCHAR(255) NULL,
			options LONGTEXT NULL,
			extra LONGTEXT NULL,
			is_required VARCHAR(50) NOT NULL DEFAULT 'false',
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY post_meta (post_type, input_meta),
			KEY input_order (input_order)
		) $charset_collate;";

		dbDelta( $sql_post );
	}

	public function down(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_post`" );
	}
}
