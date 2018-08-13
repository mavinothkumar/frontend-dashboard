<?php

/**
 * Get all content have registration enables
 *
 * @return array|null|WP_Error
 */
function fed_fetch_user_profile_by_registration() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE show_register LIKE 'Enable'", ARRAY_A );
	if ( count( $result ) <= 0 ) {
		return new WP_Error( 'fed_no_row_found_on_that_id', 'All fields are disabled to show on Registration form ' );
	}
	if ( $result === null ) {
		return new WP_Error( 'fed_invalid_query_string_r_output', 'Invalid query string out output' );
	}

	return $result;

}

/**
 * Fetch Required Fields by Menu Name
 *
 * @param string $menu Menu Name.
 *
 * @return array | WP_Error
 */
function fed_fetch_user_profile_required_by_menu( $menu = 'profile' ) {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE (menu LIKE '{$menu}' AND is_required LIKE 'true') ", ARRAY_A );
	if ( count( $result ) <= 0 ) {
		return array();
	}
	if ( $result == null ) {
		return new WP_Error( 'fed_invalid_query_string_r_output_required', 'Invalid query string out output' );
	}

	return $result;
}

/**
 * Fetch Extra fields
 */
function fed_fetch_user_profile_extra_fields() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE extra LIKE 'yes'", ARRAY_A );

	if ( $result === null || count( $result ) <= 0 ) {
		return false;
	}

	return $result;
}

/**
 * filter extra fields by key value pair
 */
function fed_fetch_user_profile_extra_fields_key_value() {
	$value = fed_fetch_user_profile_extra_fields();
	if ( ! $value ) {
		return array();
	}
	$new_value = array();
	foreach ( $value as $index => $key ) {
		$new_value[ $key['input_meta'] ] = $key['label_name'];
	}

	return $new_value;
}

/**
 * Fetch Not Extra fields
 */
function fed_fetch_user_profile_not_extra_fields() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE extra LIKE 'no'", ARRAY_A );
	if ( null === $result || count( $result ) <= 0 ) {
		return false;
	}

	return $result;
}

/**
 * filter not extra fields by key value pair
 */
function fed_fetch_user_profile_not_extra_fields_key_value() {
	$value = fed_fetch_user_profile_not_extra_fields();
	if ( ! $value ) {
		return false;
	}
	$new_value = array();
	foreach ( $value as $index => $key ) {
		$new_value[ $key['input_meta'] ] = $key['label_name'];
	}

	return $new_value;
}

/**
 * Fetch User profile by Dashboard enabled
 */
function fed_fetch_user_profile_by_dashboard() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE show_dashboard LIKE 'Enable'", ARRAY_A );

	if ( count( $results ) <= 0 ) {
		return false;
	}

	return $results;
}

/**
 * @param string $menu_slug
 *
 * @return array|bool|null|object
 */
function fed_fetch_user_profile_by_menu_slug($menu_slug = '') {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE show_dashboard LIKE 'Enable' AND menu LIKE '{$menu_slug}' ", ARRAY_A );

	if ( count( $results ) <= 0 ) {
		return false;
	}



	return $results;
}

/**
 * Array Group By Key
 *
 * @param array $arr Array Value
 * @param string $key Key
 *
 * @return array|bool
 */
function fed_array_group_by_key( $arr, $key ) {
	if ( ! is_array( $arr ) ) {
		return false;
	}
	if ( ! is_string( $key ) && ! is_int( $key ) && ! is_float( $key ) ) {
		return false;
	}

	$grouped = array();
	foreach ( $arr as $value ) {
		$grouped[ $value[ $key ] ][] = $value;
	}

	if ( func_num_args() > 2 ) {
		$args = func_get_args();
		foreach ( $grouped as $index => $value ) {
			$parms             = array_merge( array( $value ), array_slice( $args, 2, func_num_args() ) );
			$grouped[ $index ] = call_user_func_array( 'array_group_by', $parms );
		}
	}

	return $grouped;
}

/**
 * Is enable role in Registration
 *
 * @param string $fed_admin_login
 *
 * @return array|false
 */
function fed_is_role_in_registration( $fed_admin_login = '' ) {
	if ( $fed_admin_login === '' ) {
		$fed_admin_login = get_option( 'fed_admin_login' );
	}
	if ( isset( $fed_admin_login['register']['role'] ) && count( $fed_admin_login['register']['role'] ) > 0 ) {
		$user_role = fed_get_user_roles();

		return array_intersect_key( $user_role, $fed_admin_login['register']['role'] );
	}

	return false;
}

/**
 * Role with Pricing
 *
 * @param string $fed_admin_login
 *
 * @return array
 */
function fed_role_with_pricing( $fed_admin_login = '' ) {
	if ( $fed_admin_login === '' ) {
		$fed_admin_login = get_option( 'fed_admin_login' );
	}
	$role_pricing = fed_is_role_in_registration( $fed_admin_login );
	$new_array    = array();
	if ( $fed_admin_login ) {
		$fed_payment_options = get_option( 'fed_admin_settings_payments' );
		foreach ( $role_pricing as $index => $role ) {
			$new_array['role'][ $index ] = array(
				'name'   => $role,
				'price'  => $fed_payment_options['role_pricing']['payment_details'][ $index ]['role_pricing'],
				'cycle'  => $fed_payment_options['role_pricing']['payment_details'][ $index ]['payment_cycle'],
				'custom' => $fed_payment_options['role_pricing']['payment_details'][ $index ]['custom_payment_cycle'],
			);
		}
		$new_array['settings'] =$fed_payment_options['settings'];

		return $new_array;
	}

	return $new_array;
}

/**
 * @param string $fed_admin_login
 *
 * @return array
 */
function fed_role_with_pricing_flat( $fed_admin_login = '' ) {
	if ( $fed_admin_login === '' ) {
		$fed_admin_login = get_option( 'fed_admin_login' );
	}
	$role_pricing = fed_is_role_in_registration( $fed_admin_login );

	$new_array    = array();
	if ( $fed_admin_login && $role_pricing ) {
		$fed_payment_options = get_option( 'fed_admin_settings_payments' );
		foreach ( $role_pricing as $index => $role ) {
			$price               = $fed_payment_options['role_pricing']['payment_details'][ $index ]['role_pricing'];
			$cycle               = $fed_payment_options['role_pricing']['payment_details'][ $index ]['payment_cycle'];
			$custom              = $fed_payment_options['role_pricing']['payment_details'][ $index ]['custom_payment_cycle'];
			$currency            = $fed_payment_options['settings']['currency_type'];

			$new_array[ $index ] = (null !== $price && $currency) ? $role . ' ' . $currency . ' ' . $price . fed_convert_to_price( $cycle, $custom ) : $role;
		}

		return $new_array;
	}

	return $new_array;
}

/**
 * @param $value
 *
 * @return array
 */
function fed_fetch_user_profile_columns($value ) {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_USER_PROFILE_DB;

	$columns =  $wpdb->get_results( "SELECT input_meta FROM $table_name WHERE menu = '{$value}' AND show_dashboard = 'Enable' AND extra = 'yes' ", ARRAY_A );
	$new_col = array();

	foreach($columns as $index=>$col) {
		$new_col[] = $col['input_meta'];
	}
	return $new_col;

}
