<?php

namespace FED\Database\Orm;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class QueryBuilder
 *
 * Enterprise fluent, prepared SQL Query Builder.
 */
class QueryBuilder {

	/**
	 * @var \wpdb
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $table;

	/**
	 * @var array
	 */
	protected $select = array( '*' );

	/**
	 * @var array
	 */
	protected $wheres = array();

	/**
	 * @var array
	 */
	protected $bindings = array();

	/**
	 * @var array
	 */
	protected $orders = array();

	/**
	 * @var int|null
	 */
	protected $limit = null;

	/**
	 * @var int|null
	 */
	protected $offset = null;

	/**
	 * QueryBuilder constructor.
	 *
	 * @param string|null $table
	 */
	public function __construct( $table = null ) {
		global $wpdb;
		$this->db = $wpdb;

		if ( $table ) {
			$this->table( $table );
		}
	}

	/**
	 * Set table name (with automatic WP prefix resolution).
	 *
	 * @param string $table
	 * @return $this
	 */
	public function table( string $table ): self {
		$this->table = ( strpos( $table, $this->db->prefix ) === 0 ) ? $table : $this->db->prefix . $table;
		return $this;
	}

	/**
	 * Set select columns.
	 *
	 * @param array|string $columns
	 * @return $this
	 */
	public function select( $columns = array( '*' ) ): self {
		$this->select = is_array( $columns ) ? $columns : func_get_args();
		return $this;
	}

	/**
	 * Add a basic where clause.
	 *
	 * @param string $column
	 * @param mixed  $operator
	 * @param mixed  $value
	 * @return $this
	 */
	public function where( string $column, $operator = null, $value = null ): self {
		if ( is_null( $value ) && ! is_null( $operator ) ) {
			$value    = $operator;
			$operator = '=';
		}

		$column           = sanitize_key( str_replace( '`', '', $column ) );
		$this->wheres[]   = "`{$column}` {$operator} %s";
		$this->bindings[] = $value;

		return $this;
	}

	/**
	 * Add a whereIn clause.
	 *
	 * @param string $column
	 * @param array  $values
	 * @return $this
	 */
	public function whereIn( string $column, array $values ): self {
		if ( empty( $values ) ) {
			$this->wheres[] = '1=0';
			return $this;
		}

		$column         = sanitize_key( str_replace( '`', '', $column ) );
		$placeholders   = implode( ',', array_fill( 0, count( $values ), '%s' ) );
		$this->wheres[] = "`{$column}` IN ({$placeholders})";

		foreach ( $values as $val ) {
			$this->bindings[] = $val;
		}

		return $this;
	}

	/**
	 * Add a whereNull clause.
	 *
	 * @param string $column
	 * @return $this
	 */
	public function whereNull( string $column ): self {
		$column         = sanitize_key( str_replace( '`', '', $column ) );
		$this->wheres[] = "`{$column}` IS NULL";
		return $this;
	}

	/**
	 * Add a whereNotNull clause.
	 *
	 * @param string $column
	 * @return $this
	 */
	public function whereNotNull( string $column ): self {
		$column         = sanitize_key( str_replace( '`', '', $column ) );
		$this->wheres[] = "`{$column}` IS NOT NULL";
		return $this;
	}

	/**
	 * Add order by clause.
	 *
	 * @param string $column
	 * @param string $direction
	 * @return $this
	 */
	public function orderBy( string $column, string $direction = 'ASC' ): self {
		$direction      = strtoupper( $direction ) === 'DESC' ? 'DESC' : 'ASC';
		$column         = sanitize_key( str_replace( '`', '', $column ) );
		$this->orders[] = "`{$column}` {$direction}";
		return $this;
	}

	/**
	 * Order by column descending shorthand.
	 *
	 * @param string $column
	 * @return $this
	 */
	public function latest( string $column = 'created_at' ): self {
		return $this->orderBy( $column, 'DESC' );
	}

	/**
	 * Set query limit.
	 *
	 * @param int $limit
	 * @return $this
	 */
	public function limit( int $limit ): self {
		$this->limit = absint( $limit );
		return $this;
	}

	/**
	 * Set query offset.
	 *
	 * @param int $offset
	 * @return $this
	 */
	public function offset( int $offset ): self {
		$this->offset = absint( $offset );
		return $this;
	}

	/**
	 * Build SQL query.
	 *
	 * @return string
	 */
	protected function toSql(): string {
		$columns = empty( $this->select ) ? '*' : implode(
			', ',
			array_map(
				function ( $col ) {
					return '*' === $col ? '*' : '`' . sanitize_key( str_replace( '`', '', $col ) ) . '`';
				},
				$this->select
			)
		);

		$sql = "SELECT {$columns} FROM `{$this->table}`";

		if ( ! empty( $this->wheres ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $this->wheres );
		}

		if ( ! empty( $this->orders ) ) {
			$sql .= ' ORDER BY ' . implode( ', ', $this->orders );
		}

		if ( ! is_null( $this->limit ) ) {
			$sql .= ' LIMIT ' . (int) $this->limit;
		}

		if ( ! is_null( $this->offset ) ) {
			$sql .= ' OFFSET ' . (int) $this->offset;
		}

		return $sql;
	}

	/**
	 * Execute prepared query and fetch all results.
	 *
	 * @return array
	 */
	public function get(): array {
		$sql = $this->toSql();

		if ( ! empty( $this->bindings ) ) {
			$sql = $this->db->prepare( $sql, $this->bindings );
		}

		$results = $this->db->get_results( $sql, ARRAY_A );
		return $results ?: array();
	}

	/**
	 * Execute prepared query and fetch single row.
	 *
	 * @return array|null
	 */
	public function first(): ?array {
		$this->limit( 1 );
		$results = $this->get();
		return ! empty( $results[0] ) ? $results[0] : null;
	}

	/**
	 * Count matching records.
	 *
	 * @return int
	 */
	public function count(): int {
		$sql = "SELECT COUNT(*) FROM `{$this->table}`";

		if ( ! empty( $this->wheres ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $this->wheres );
		}

		if ( ! empty( $this->bindings ) ) {
			$sql = $this->db->prepare( $sql, $this->bindings );
		}

		return (int) $this->db->get_var( $sql );
	}

	/**
	 * Insert a record.
	 *
	 * @param array $data
	 * @return int|false
	 */
	public function insert( array $data ) {
		$result = $this->db->insert( $this->table, $data );
		return false !== $result ? (int) $this->db->insert_id : false;
	}

	/**
	 * Update records matching current query constraints.
	 *
	 * @param array $data
	 * @return int|false
	 */
	public function update( array $data ) {
		$sets     = array();
		$bindings = array();

		foreach ( $data as $col => $val ) {
			$col        = sanitize_key( str_replace( '`', '', $col ) );
			$sets[]     = "`{$col}` = %s";
			$bindings[] = $val;
		}

		$sql = "UPDATE `{$this->table}` SET " . implode( ', ', $sets );

		if ( ! empty( $this->wheres ) ) {
			$sql     .= ' WHERE ' . implode( ' AND ', $this->wheres );
			$bindings = array_merge( $bindings, $this->bindings );
		}

		return $this->db->query( $this->db->prepare( $sql, $bindings ) );
	}

	/**
	 * Delete records matching current constraints.
	 *
	 * @return int|false
	 */
	public function delete() {
		$sql = "DELETE FROM `{$this->table}`";

		if ( ! empty( $this->wheres ) ) {
			$sql .= ' WHERE ' . implode( ' AND ', $this->wheres );
		}

		if ( ! empty( $this->bindings ) ) {
			$sql = $this->db->prepare( $sql, $this->bindings );
		}

		return $this->db->query( $sql );
	}

	/**
	 * Paginate query results.
	 *
	 * @param int $perPage
	 * @param int $page
	 * @return array
	 */
	public function paginate( int $perPage = 15, int $page = 1 ): array {
		$page       = max( 1, $page );
		$total      = $this->count();
		$totalPages = (int) ceil( $total / $perPage );

		$this->limit( $perPage )->offset( ( $page - 1 ) * $perPage );
		$items = $this->get();

		return array(
			'data'         => $items,
			'total'        => $total,
			'per_page'     => $perPage,
			'current_page' => $page,
			'last_page'    => $totalPages,
		);
	}
}
