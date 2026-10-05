<?php

namespace FED\Database\Contracts;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface MigrationInterface
 *
 * Contract for versioned database migrations.
 */
interface MigrationInterface {

	/**
	 * Get the unique migration version/identifier.
	 *
	 * @return string
	 */
	public function getVersion(): string;

	/**
	 * Run the migration (create/alter tables).
	 *
	 * @return void
	 */
	public function up(): void;

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down(): void;
}
