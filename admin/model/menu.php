<?php

/**
 * Fetch Menu with Key and Value Pair
 *
 * @return array
 */
function fed_fetch_menu() {
	$results = fed_fetch_rows_by_table( BC_FED_MENU_DB );

	if ( count( $results ) <= 0 ) {
		new WP_Error( 'fed_default_value_not_installed', __( 'There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on ', 'frontend-dashboard' ) . make_clickable( 'https://buffercode.com/' ) );
	}

	return $results;
}

/**
 * Fetch Table Rows with Key for Front End.
 *
 * @param string $table Table Name
 * @param String $key Row Key
 *
 * @return array|WP_Error
 */
function fed_fetch_table_rows_with_key_front_end( $table, $key ) {
	$results = apply_filters( 'fed_add_custom_menu', fed_fetch_rows_by_table( $table ) );
	$user_role = fed_get_current_user_role_key();

	if ( count( $results ) <= 0 && BC_FED_POST_DB !== $table ) {
		return new WP_Error( 'fed_default_value_not_installed', __( 'There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on ', 'frontend-dashboard' ) . make_clickable( 'https://buffercode.com/' ) );
	}
	$result_with_key = array();
	foreach ( $results as $result ) {
		$res = isset( $result['user_role'] ) && ! empty( $result['user_role'] ) ? $result['user_role'] : false;
		/**
		 * Lets compare the user role with the admin saved user role
		 */
		if ( ! $res ) {
			continue;
		}
		if ( ! in_array( $user_role, unserialize( $res ), true ) ) {
			continue;
		}
		$result['menu_type']                = isset( $result['menu_type'] ) ? $result['menu_type'] : 'user';
		$result_with_key[ $result[ $key ] ] = $result;
	}

	return $result_with_key;
}