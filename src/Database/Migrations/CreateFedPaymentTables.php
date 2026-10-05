<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CreateFedPaymentTables implements MigrationInterface {

	public function getVersion(): string {
		return '2026_09_01_000004_create_fed_payment_tables';
	}

	public function up(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate     = $wpdb->get_charset_collate();
		$payment_table       = $wpdb->prefix . 'fed_payment';
		$payment_items_table = $wpdb->prefix . 'fed_payment_items';

		$sql_payment = "CREATE TABLE `$payment_table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			payment_type VARCHAR(50) NOT NULL DEFAULT 'onetime',
			payment_source VARCHAR(50) NOT NULL DEFAULT 'manual',
			transaction_id VARCHAR(191) NULL,
			amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			currency VARCHAR(10) NOT NULL DEFAULT 'USD',
			status VARCHAR(50) NOT NULL DEFAULT 'pending',
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_id (user_id),
			KEY transaction_id (transaction_id),
			KEY status (status)
		) $charset_collate;";
		dbDelta( $sql_payment );

		$sql_payment_items = "CREATE TABLE `$payment_items_table` (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			payment_id BIGINT(20) UNSIGNED NOT NULL,
			item_name VARCHAR(255) NOT NULL,
			item_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
			quantity INT(10) NOT NULL DEFAULT 1,
			created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY payment_id (payment_id)
		) $charset_collate;";
		dbDelta( $sql_payment_items );
	}

	public function down(): void {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_payment_items`" );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}fed_payment`" );
	}
}
