<?php
/**
 * Developer Comment
 * translate => done
 */

/**
 * Fetch all Table Row
 *
 * @param string $table Table Name
 *
 * @return array|WP_Error
 */
function fed_fetch_rows_by_table( $table ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;

	return  $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );
}

/**
 * Fetch Table Row by ID
 *
 * @param string $table Table Name.
 * @param int $id Table Row ID.
 *
 * @return array|WP_Error
 */
function fed_fetch_table_row_by_id( $table, $id ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;

	$result = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $id", ARRAY_A );

	if ( count( $result ) <= 0 ) {
		return new WP_Error( 'fed_no_row_found_on_that_id', 'There is no row associated with that ID' );
	}

	return $result;

}


/**
 * Fetch Table Row(s) by Key values
 *
 * @param string $table Table Name.
 * @param string $key Table Row Name.
 * @param string $value Table Row Value.
 * @param string $condition Condition to apply on Row Name and Row Value.
 *
 * @return array|null
 */
function fed_fetch_table_rows_by_key_value( $table, $key, $value, $condition = '=' ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;

	return $wpdb->get_results( "SELECT * FROM $table_name WHERE {$key} $condition '{$value}' ", ARRAY_A );

}

/**
 * Delete Table Row by ID
 *
 * @param string $table Table Name.
 * @param int $id Row ID.
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

/**
 * Fetch Table Rows with given Key
 *
 * @param string $table Table Name
 * @param string $key Key
 *
 * @return array | object
 */
function fed_fetch_table_rows_with_key( $table, $key ) {
	$results = fed_fetch_rows_by_table( $table );

	if ( count( $results ) <= 0 && BC_FED_POST_DB !== $table ) {
		return new WP_Error( 'fed_default_value_not_installed', __('There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on','frontend-dashboard') . make_clickable( 'https://ifecho.com/' ) );
	}
	$result_with_key = array();
	foreach ( $results as $result ) {
		$result_with_key[ $result[ $key ] ] = $result;
	}
	uasort( $result_with_key, 'fed_sort_by_order' );

	return $result_with_key;
}

/**
 * Fetch Table by Is Required True
 *
 * @param string $table Table Name
 *
 * @return array|WP_Error
 */
function fed_fetch_table_by_is_required( $table ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;

	$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE is_required LIKE 'true'", ARRAY_A );
	if ( count( $result ) <= 0 ) {
		return array();
	}
	if ( $result == null ) {
		return new WP_Error( 'fed_invalid_query_string_r_output_required', 'Invalid query string out output' );
	}

	return $result;
}


/**
 * Create row(s)
 *
 * @param string $table Table Name.
 * @param array $data Insert row content
 *
 * @return false|int
 */
function fed_insert_new_row( $table, $data ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;

	return $wpdb->insert(
		$table_name,
		$data
	);
}

/**
 * @param $table
 * @param array $conditions
 *
 * @return array
 */
function fed_fetch_table_rows_by_key_value_column( $table, array $conditions ) {
	global $wpdb;
	$table_name = $wpdb->prefix . $table;
	$key = isset( $conditions['key']) ? $conditions['key'] : false;
	$value = isset( $conditions['value']) ? $conditions['value'] : false;
	$condition = isset( $conditions['condition']) ? $conditions['condition'] : '=';
	$column = isset( $conditions['column']) ? $conditions['column'] : '*';

	$columns =  $wpdb->get_results( "SELECT {$column} FROM $table_name WHERE {$key} $condition '{$value}' ", ARRAY_A );
	$new_col = array();

	foreach($columns as $index=>$col) {
		$new_col[] = $col[$column];
	}
	return $new_col;

}