<?php
/**
 * Common.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'fed_fetch_rows_by_table' ) ) {
	/**
	 * Fetch all Table Row
	 *
	 * @param  string $table  Table Name.
	 * @param  null   $order  Order.
	 *
	 * @return array|WP_Error
	 */
	function fed_fetch_rows_by_table( $table, $order = null ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$order = $order ? 'ORDER BY id ' . $order : '';

		return $wpdb->get_results( "SELECT * FROM $table_name $order", ARRAY_A );
	}
}

if ( ! function_exists( 'fed_fetch_table_row_by_id' ) ) {
	/**
	 * Fetch Table Row by ID
	 *
	 * @param  string $table  Table Name.
	 * @param  int    $id  Table Row ID.
	 *
	 * @return array|WP_Error
	 */
	function fed_fetch_table_row_by_id( $table, $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d LIMIT 1", (int) $id ),
			ARRAY_A );

		if ( ( is_array( $result ) && count( $result ) <= 0 ) || ! $result ) {
			return new WP_Error( 'fed_no_row_found_on_that_id', __( 'Invalid ID', 'frontend-dashboard' ) );
		}

		return $result;

	}
}

if ( ! function_exists( 'fed_fetch_table_row_by_ids' ) ) {
	/**
	 * Fetch Table Row by IDs
	 *
	 * @param  string $table  Table Name.
	 * @param  array  $ids  Table Row IDs.
	 *
	 * @return array|WP_Error
	 */
	function fed_fetch_table_row_by_ids( $table, $ids ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE id IN (" . implode( ',',
				array_map( 'intval', $ids ) ) . ")", ARRAY_A );

		if ( ( is_array( $result ) && count( $result ) <= 0 ) || ! $result ) {
			return new WP_Error( 'fed_no_row_found_on_that_id', __( 'Invalid ID', 'frontend-dashboard' ) );
		}

		return $result;

	}
}

if ( ! function_exists( 'fed_fetch_table_rows_by_key_value' ) ) {
	/**
	 * Fetch Table Row(s) by Key values
	 *
	 * @param  string $table  Table Name.
	 * @param  string $key  Table Row Name.
	 * @param  string $value  Table Row Value.
	 * @param  string $condition  Condition to apply on Row Name and Row Value.
	 *
	 * @param  string $output
	 *
	 * @param  string $order
	 *
	 * @return array|null
	 */
	function fed_fetch_table_rows_by_key_value(
		$table,
		$key,
		$value,
		$condition = '=',
		$output = ARRAY_A,
		$order = 'ASC'
	) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$order = $order ? 'ORDER BY id ' . $order : '';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE $key $condition %s $order",
				esc_attr( $value )
			), $output );

	}
}

if ( ! function_exists( 'fed_delete_table_row_by_id' ) ) {
	/**
	 * Delete Table Row by ID
	 *
	 * @param  string $table  Table Name.
	 * @param  int    $id  Row ID.
	 *
	 * @return string
	 */
	function fed_delete_table_row_by_id( $table, $id ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$verify = $wpdb->delete( $table_name, array( 'id' => ( $id ) ), array( '%d' ) );

		if ( $verify ) {
			return 'success';
		}

		return 'failed';
	}
}

if ( ! function_exists( 'fed_delete_table_rows_on_condition' ) ) {
	/**
	 * @param         $table
	 * @param         $key
	 * @param         $value
	 * @param  string $condition
	 *
	 * @return string
	 */
	function fed_delete_table_rows_on_condition( $table, $key, $value ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$verify = $wpdb->delete( $table_name, array( $key => $value ) );

		if ( $verify ) {
			return 'success';
		}

		return 'failed';
	}
}

if ( ! function_exists( 'fed_fetch_table_rows_with_key' ) ) {
	/**
	 * Fetch Table Rows with given Key
	 *
	 * @param  string $table  Table Name
	 * @param  string $key  Key
	 *
	 * @return array | object
	 */
	function fed_fetch_table_rows_with_key( $table, $key ) {
		$results = fed_fetch_rows_by_table( $table );

		if ( count( $results ) <= 0 && BC_FED_TABLE_POST !== $table ) {
			return new WP_Error( 'fed_default_value_not_installed',
				__( 'There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on',
					'frontend-dashboard' ) . make_clickable( 'https://buffercode.com/' ) );
		}
		$result_with_key = array();
		foreach ( $results as $result ) {
			$result_with_key[ $result[ $key ] ] = $result;
		}
		uasort( $result_with_key, 'fed_sort_by_order' );

		return $result_with_key;
	}
}

if ( ! function_exists( 'fed_fetch_table_by_is_required' ) ) {
	/**
	 * Fetch Table by Is Required True
	 *
	 * @param  string $table  Table Name
	 *
	 * @return array|WP_Error
	 */
	function fed_fetch_table_by_is_required( $table ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$result = $wpdb->get_results(
			"SELECT * FROM $table_name WHERE is_required LIKE 'true' AND show_register LIKE 'Enable'",
			ARRAY_A
		);
		if ( count( $result ) <= 0 ) {
			return array();
		}
		if ( null == $result ) {
			return new WP_Error(
				'fed_invalid_query_string_r_output_required',
				__( 'Invalid query string out output', 'frontend-dashboard' )
			);
		}

		return $result;
	}
}

if ( ! function_exists( 'fed_insert_new_row' ) ) {
	/**
	 * Create row(s)
	 *
	 * @param  string $table  Table Name.
	 * @param  array  $data  Insert row content.
	 *
	 * @return false|int
	 */
	function fed_insert_new_row( $table, $data ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;

		$status = $wpdb->insert(
			$table_name,
			$data
		);

		return $status ? $wpdb->insert_id : false;
	}
}

if ( ! function_exists( 'fed_fetch_table_rows_by_key_value_column' ) ) {
	/**
	 * Fetch Table Rows by Key Value Column.
	 *
	 * @param  string $table  Table.
	 * @param  array  $conditions  Conditions.
	 *
	 * @return array
	 */
	function fed_fetch_table_rows_by_key_value_column( $table, array $conditions ) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table;
		$key        = isset( $conditions['key'] ) ? $conditions['key'] : false;
		$value      = isset( $conditions['value'] ) ? $conditions['value'] : false;
		$condition  = isset( $conditions['condition'] ) ? $conditions['condition'] : '=';
		$column     = isset( $conditions['column'] ) ? $conditions['column'] : '*';

		$columns = $wpdb->get_results( "SELECT {$column} FROM $table_name WHERE {$key} $condition '{$value}' ",
			ARRAY_A );
		$new_col = array();

		foreach ( $columns as $index => $col ) {
			$new_col[] = $col[ $column ];
		}

		return $new_col;
	}
}