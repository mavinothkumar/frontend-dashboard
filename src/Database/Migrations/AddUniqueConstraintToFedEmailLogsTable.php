<?php

namespace FED\Database\Migrations;

use FED\Database\Contracts\MigrationInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AddUniqueConstraintToFedEmailLogsTable
 *
 * Migration to enforce UNIQUE constraint on uuid column in fed_email_logs.
 */
class AddUniqueConstraintToFedEmailLogsTable implements MigrationInterface {

	/**
	 * Unique migration version.
	 *
	 * @return string
	 */
	public function getVersion(): string {
		return '2026_10_29_000008_add_unique_uuid_to_fed_email_logs_table';
	}

	/**
	 * Run migration.
	 */
	public function up(): void {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) !== $table ) {
			return;
		}

		// Clean up any historical duplicate UUID rows before creating UNIQUE key (keep the lowest ID)
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query(
			"DELETE t1 FROM `$table` t1
			INNER JOIN `$table` t2 
			WHERE t1.id > t2.id AND t1.uuid = t2.uuid AND t1.uuid != ''" // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);

		// Check index status
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$indexes = $wpdb->get_results(
			$wpdb->prepare( "SHOW INDEX FROM `$table` WHERE Column_name = %s", 'uuid' ), // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		$has_unique  = false;
		$index_names = array();

		if ( ! empty( $indexes ) ) {
			foreach ( $indexes as $idx ) {
				$index_names[] = $idx['Key_name'];
				if ( 0 === (int) $idx['Non_unique'] ) {
					$has_unique = true;
				}
			}
		}

		if ( ! $has_unique ) {
			// Drop non-unique index if exists
			foreach ( array_unique( $index_names ) as $name ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->query( "ALTER TABLE `$table` DROP INDEX `$name`" );
			}

			// Add unique index
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "ALTER TABLE `$table` ADD UNIQUE KEY `uuid` (`uuid`)" );
		}
	}

	/**
	 * Revert migration.
	 */
	public function down(): void {
		global $wpdb;
		$table = $wpdb->prefix . ( defined( 'BC_FED_TABLE_EMAIL_LOGS' ) ? BC_FED_TABLE_EMAIL_LOGS : 'fed_email_logs' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "ALTER TABLE `$table` DROP INDEX `uuid`, ADD KEY `uuid` (`uuid`)" );
	}
}
