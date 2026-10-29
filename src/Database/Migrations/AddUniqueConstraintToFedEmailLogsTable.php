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

		// Check all existing indexes on the table
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$indexes = $wpdb->get_results(
			"SHOW INDEX FROM `$table`", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		);

		$has_unique_uuid      = false;
		$uuid_indexes_to_drop = array();

		if ( ! empty( $indexes ) ) {
			foreach ( $indexes as $idx ) {
				if ( 'uuid' === $idx['Column_name'] ) {
					if ( 0 === (int) $idx['Non_unique'] ) {
						$has_unique_uuid = true;
					} else {
						$uuid_indexes_to_drop[] = $idx['Key_name'];
					}
				}
			}
		}

		if ( $has_unique_uuid ) {
			return;
		}

		// Drop non-unique indexes on uuid
		foreach ( array_unique( $uuid_indexes_to_drop ) as $index_name ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "ALTER TABLE `$table` DROP INDEX `$index_name`" );
		}

		// Double-check if key name 'uuid' still exists before adding
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$key_exists = $wpdb->get_var(
			$wpdb->prepare( "SHOW INDEX FROM `$table` WHERE Key_name = %s", 'uuid' ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		);

		if ( ! empty( $key_exists ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$wpdb->query( "ALTER TABLE `$table` DROP INDEX `uuid`" );
		}

		// Add unique index
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "ALTER TABLE `$table` ADD UNIQUE KEY `uuid` (`uuid`)" );
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
