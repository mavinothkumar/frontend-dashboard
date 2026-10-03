<?php

namespace FED\Database;

use FED\Core\Logger;
use FED\Database\Contracts\MigrationInterface;
use FED\Database\Migrations\CreateFedMenuTable;
use FED\Database\Migrations\CreateFedUserProfileTable;
use FED\Database\Migrations\CreateFedPostTable;
use FED\Database\Migrations\CreateFedPaymentTables;
use FED\Database\Migrations\CreateFedLogsTable;
use FED\Database\Migrations\CreateFedNotificationsTable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MigrationManager
 *
 * Enterprise versioned database migration runner.
 */
class MigrationManager {

	/**
	 * @var string Option name storing applied migration keys.
	 */
	const MIGRATIONS_OPTION = 'fed_applied_migrations';

	/**
	 * @var array List of registered migration classes.
	 */
	protected $migrations = array(
		CreateFedMenuTable::class,
		CreateFedUserProfileTable::class,
		CreateFedPostTable::class,
		CreateFedPaymentTables::class,
		CreateFedLogsTable::class,
		CreateFedNotificationsTable::class,
	);

	/**
	 * Run all pending migrations.
	 *
	 * @return array Array of executed migration versions.
	 */
	public function run(): array {
		$applied  = $this->getAppliedMigrations();
		$executed = array();

		foreach ( $this->migrations as $migrationClass ) {
			/** @var MigrationInterface $migration */
			$migration = new $migrationClass();
			$version   = $migration->getVersion();

			if ( in_array( $version, $applied, true ) ) {
				continue;
			}

			try {
				Logger::info( "Running migration: {$version}" );
				$migration->up();
				$applied[]  = $version;
				$executed[] = $version;
				$this->saveAppliedMigrations( $applied );
			} catch ( \Throwable $e ) {
				Logger::error( "Migration {$version} failed: " . $e->getMessage() );
				break;
			}
		}

		return $executed;
	}

	/**
	 * Get list of already applied migrations.
	 *
	 * @return array
	 */
	public function getAppliedMigrations(): array {
		$applied = get_option( self::MIGRATIONS_OPTION, array() );
		return is_array( $applied ) ? $applied : array();
	}

	/**
	 * Save applied migrations to options table.
	 *
	 * @param array $applied
	 * @return void
	 */
	protected function saveAppliedMigrations( array $applied ): void {
		update_option( self::MIGRATIONS_OPTION, array_values( array_unique( $applied ) ) );
	}
}
