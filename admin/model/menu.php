<?php

/**
 * Fetch Menu with Key and Value Pair
 *
 * @return array
 */
function fed_fetch_menu() {
	$results         = fed_fetch_rows_by_table( BC_FED_MENU_DB );

	if ( count( $results ) <= 0 ) {
		new WP_Error( 'fed_default_value_not_installed', __('There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on ','fed') . make_clickable( 'https://buffercode.com/' ) );
	}

//	$result_with_key = array();
//	foreach ( $results as $result ) {
//		$result_with_key[ $result['menu_slug'] ] = $result;
//	}

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
	$results   = fed_fetch_rows_by_table( $table );
	$user      = get_userdata( get_current_user_id() );

	if ( count( $results ) <= 0 && BC_FED_POST_DB !== $table ) {
		return new WP_Error( 'fed_default_value_not_installed', __('There is some trouble in installing the default value, please try to deactivate and activate the plugin or contact us on ','fed') . make_clickable( 'https://ifecho.com/' ) );
	}
	$result_with_key = array();
	foreach ( $results as $result ) {
		/**
		 * Lets compare the user role with the admin saved user role
		 */
		if ( count( array_intersect( $user->roles, unserialize( $result['user_role'] ) ) ) <= 0 ) {
			continue;
		}
		$result_with_key[ $result[ $key ] ] = $result;
	}

	return $result_with_key;
}