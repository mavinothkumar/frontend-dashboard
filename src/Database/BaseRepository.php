<?php

namespace FED\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Class BaseRepository
 *
 * Base repository implementing safe, prepared WordPress database operations.
 */
abstract class BaseRepository {

	/**
	 * @var \wpdb
	 */
	protected $db;

	/**
	 * @var string Table name without WP prefix.
	 */
	protected $table;

	/**
	 * BaseRepository constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * Get full table name with WordPress prefix.
	 *
	 * @return string
	 */
	public function getTableName() {
		return $this->db->prefix . $this->table;
	}

	/**
	 * Find a single record by primary ID.
	 *
	 * @param int $id
	 * @return array|null
	 */
	public function find( $id ) {
		$table = $this->getTableName();
		$sql   = $this->db->prepare( "SELECT * FROM `{$table}` WHERE `id` = %d LIMIT 1", (int) $id );
		$row   = $this->db->get_row( $sql, ARRAY_A );

		return $row ?: null;
	}

	/**
	 * Find a single record by a specific column.
	 *
	 * @param string $column
	 * @param mixed  $value
	 * @return array|null
	 */
	public function findBy( $column, $value ) {
		$table = $this->getTableName();
		$sql   = $this->db->prepare( "SELECT * FROM `{$table}` WHERE `{$column}` = %s LIMIT 1", $value );
		$row   = $this->db->get_row( $sql, ARRAY_A );

		return $row ?: null;
	}

	/**
	 * Retrieve all records from the table.
	 *
	 * @param string $orderBy
	 * @return array
	 */
	public function all( $orderBy = 'id ASC' ) {
		$table = $this->getTableName();
		$rows  = $this->db->get_results( "SELECT * FROM `{$table}` ORDER BY {$orderBy}", ARRAY_A );

		return $rows ?: [];
	}

	/**
	 * Find records matching conditions.
	 *
	 * @param array  $conditions Key => value pairs
	 * @param string $orderBy
	 * @param int    $limit
	 * @return array
	 */
	public function where( array $conditions = [], $orderBy = 'id ASC', $limit = 0 ) {
		$table  = $this->getTableName();
		$where  = [];
		$params = [];

		foreach ( $conditions as $col => $val ) {
			$where[]  = "`{$col}` = %s";
			$params[] = $val;
		}

		$whereSql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
		$limitSql = $limit > 0 ? "LIMIT {$limit}" : '';

		$sql = "SELECT * FROM `{$table}` {$whereSql} ORDER BY {$orderBy} {$limitSql}";
		if ( ! empty( $params ) ) {
			$sql = $this->db->prepare( $sql, $params );
		}

		$rows = $this->db->get_results( $sql, ARRAY_A );
		return $rows ?: [];
	}

	/**
	 * Insert a new record.
	 *
	 * @param array $data
	 * @return int|false Inserted ID or false on failure.
	 */
	public function create( array $data ) {
		$table  = $this->getTableName();
		$result = $this->db->insert( $table, $data );

		if ( false === $result ) {
			return false;
		}

		return (int) $this->db->insert_id;
	}

	/**
	 * Update an existing record.
	 *
	 * @param int   $id
	 * @param array $data
	 * @return bool
	 */
	public function update( $id, array $data ) {
		$table  = $this->getTableName();
		$result = $this->db->update( $table, $data, [ 'id' => (int) $id ] );

		return false !== $result;
	}

	/**
	 * Delete a record by ID.
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete( $id ) {
		$table  = $this->getTableName();
		$result = $this->db->delete( $table, [ 'id' => (int) $id ], [ '%d' ] );

		return false !== $result;
	}

	/**
	 * Count records matching optional conditions.
	 *
	 * @param array $conditions
	 * @return int
	 */
	public function count( array $conditions = [] ) {
		$table  = $this->getTableName();
		$where  = [];
		$params = [];

		foreach ( $conditions as $col => $val ) {
			$where[]  = "`{$col}` = %s";
			$params[] = $val;
		}

		$whereSql = ! empty( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '';
		$sql      = "SELECT COUNT(*) FROM `{$table}` {$whereSql}";

		if ( ! empty( $params ) ) {
			$sql = $this->db->prepare( $sql, $params );
		}

		return (int) $this->db->get_var( $sql );
	}
}
