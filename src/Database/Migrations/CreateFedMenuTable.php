<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedMenuTable implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000001_create_fed_menu_table';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$menu_table      = $wpdb->prefix . 'fed_menu';
		$menu_meta_table = $wpdb->prefix . 'fed_menu_meta';

		$sql_menu = "CREATE TABLE `$menu_table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			menu_slug VARCHAR(191) NOT NULL,
			menu VARCHAR(255) NULL,
			menu_name VARCHAR(255) NULL,
			menu_order INT(10) NOT NULL DEFAULT 0,
			menu_image_id VARCHAR(255) NULL,
			menu_icon VARCHAR(255) NULL,
			show_user_profile VARCHAR(50) NOT NULL DEFAULT 'Enable',
			extra VARCHAR(50) NOT NULL DEFAULT 'yes',
			user_role LONGTEXT NULL,
			extended LONGTEXT NULL,
			parent_id VARCHAR(10) NOT NULL DEFAULT '0',
			menu_type VARCHAR(50) NULL,
			status VARCHAR(50) NOT NULL DEFAULT 'active',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			UNIQUE KEY menu_slug (menu_slug),
			KEY parent_id (parent_id),
			KEY menu_order (menu_order)
		) $charset_collate;";
		dbDelta( $sql_menu );

		$sql_menu_meta = "CREATE TABLE `$menu_meta_table` (
			meta_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			menu_id BIGINT(20) UNSIGNED NOT NULL,
			meta_key VARCHAR(191) NULL,
			meta_value LONGTEXT NULL,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (meta_id),
			KEY menu_meta (menu_id, meta_key)
		) $charset_collate;";
		dbDelta( $sql_menu_meta );
	}

	public function down(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_menu_meta`" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_menu`" );
	}
}
