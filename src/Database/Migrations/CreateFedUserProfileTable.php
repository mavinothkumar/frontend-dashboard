<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedUserProfileTable implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000002_create_fed_user_profile_table';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate    = $wpdb->get_charset_collate();
		$user_profile_table = $wpdb->prefix . 'fed_user_profile';

		$sql_user_profile = "CREATE TABLE `$user_profile_table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			input_meta VARCHAR(191) NOT NULL,
			label_name VARCHAR(255) NULL,
			label VARCHAR(255) NULL,
			input_order INT(10) NOT NULL DEFAULT 0,
			show_register VARCHAR(50) NOT NULL DEFAULT 'Disable',
			show_dashboard VARCHAR(50) NOT NULL DEFAULT 'Disable',
			show_user_profile VARCHAR(50) NOT NULL DEFAULT 'Enable',
			is_required VARCHAR(50) NOT NULL DEFAULT 'false',
			is_unique VARCHAR(50) NOT NULL DEFAULT 'false',
			input_type VARCHAR(50) NULL,
			placeholder VARCHAR(255) NULL,
			class_name VARCHAR(255) NULL,
			id_name VARCHAR(255) NULL,
			input_step VARCHAR(50) NULL,
			input_min VARCHAR(50) NULL,
			input_max VARCHAR(50) NULL,
			input_row VARCHAR(50) NULL,
			is_tooltip VARCHAR(50) NOT NULL DEFAULT 'no',
			tooltip_title VARCHAR(255) NULL,
			tooltip_body VARCHAR(255) NULL,
			input_value LONGTEXT NULL,
			user_role LONGTEXT NULL,
			input_location VARCHAR(191) NULL,
			extra VARCHAR(50) NOT NULL DEFAULT 'yes',
			menu VARCHAR(191) NOT NULL DEFAULT 'profile',
			extended LONGTEXT NULL,
			options LONGTEXT NULL,
			user_read_only VARCHAR(50) NOT NULL DEFAULT 'false',
			admin_read_only VARCHAR(50) NOT NULL DEFAULT 'false',
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY input_meta (input_meta),
			KEY input_location (input_location),
			KEY input_order (input_order)
		) $charset_collate;";

		dbDelta( $sql_user_profile );
	}

	public function down(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_user_profile`" );
	}
}
