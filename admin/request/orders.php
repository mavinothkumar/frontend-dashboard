<?php
add_action( 'wp_ajax_fed_admin_orders', 'fed_admin_orders_function' );
add_action( 'wp_ajax_fed_admin_order_delete', 'fed_admin_order_delete_function' );
add_action( 'wp_ajax_fed_order_search_add', 'fed_order_search_add_function' );
add_action( 'wp_ajax_fed_admin_add_orders', 'fed_admin_add_orders_function' );
/**
 * Admin Orders
 */
function fed_admin_orders_function() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_PAYMENT_DB;
	$request    = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	$response = fed_admin_order_id_validation( $request );
	$order    = $response['order'];
	$id       = $response['id'];

	$orders = array(
		'email'          => isset( $request['email'] ) ? sanitize_email( $request['email'] ) : $order['email'],
		'first_name'     => isset( $request['first_name'] ) ? sanitize_text_field( $request['first_name'] ) : $order['first_name'],
		'last_name'      => isset( $request['last_name'] ) ? sanitize_text_field( $request['last_name'] ) : $order['last_name'],
		'recipient_name' => isset( $request['recipient_name'] ) ? sanitize_text_field( $request['recipient_name'] ) : $order['recipient_name'],
		'amount'         => isset( $request['amount'] ) ? (float) $request['amount'] : $order['amount'],
		'line1'          => isset( $request['line1'] ) ? sanitize_text_field( $request['line1'] ) : $order['line1'],
		'city'           => isset( $request['city'] ) ? sanitize_text_field( $request['city'] ) : $order['city'],
		'state'          => isset( $request['state'] ) ? sanitize_text_field( $request['state'] ) : $order['state'],
		'postal_code'    => isset( $request['postal_code'] ) ? sanitize_text_field( $request['postal_code'] ) : $order['postal_code'],
		'country_code'   => isset( $request['country_code'] ) ? sanitize_text_field( $request['country_code'] ) : $order['country_code'],
		'updated_at'     => date( 'Y-m-d H:i:s' ),
	);

	$status = $wpdb->update( $table_name, $orders, array( 'id' => $id ) );

	if ( $status === false ) {
		wp_send_json_error( array( 'message' => __( 'Sorry no record found to update your details', 'frontend-dashboard' ) ) );
		exit();
	}

	wp_send_json_success( array( 'message' => __( 'Orders has been successfully updated', 'frontend-dashboard' ) ) );
	exit();

}

/**
 * Admin Order Delete
 */
function fed_admin_order_delete_function() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_PAYMENT_DB;
	$request    = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	parse_str( $request['data'], $request );
	$response = fed_admin_order_id_validation( $request );
	$order    = $response['order'];
	$id       = $response['id'];

	$verify = $wpdb->delete( $table_name, array( 'id' => $id ), array( '%d' ) );

	if ( $verify ) {
		wp_send_json_success( array(
			'message' => __( 'Transaction ID: ' . $order['transaction_id'] . ' has been deleted successfully', 'frontend-dashboard' ),
			'reload'  => admin_url() . 'admin.php?page=fed_orders',
		) );
		exit();
	}

	wp_send_json_error( array( 'message' => __( 'Something went wrong, please refresh the page and delete it again.', 'frontend-dashboard' ) ) );
	exit();
}

/**
 * Admin Order ID Validation
 *
 * @param array $request Request
 *
 * @return array
 */
function fed_admin_order_id_validation( $request ) {
	if ( ! wp_verify_nonce( $request['fed_admin_order_delete'], 'fed_admin_order_delete' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		exit();
	}

	$id = isset( $request['order_id'] ) ? (int) $request['order_id'] : 0;
	if ( ! $id ) {
		wp_send_json_error( array( 'message' => 'The Order ID is missing, please refresh the page and try again' ) );
		exit();
	}
	$order = fed_fetch_table_row_by_id( BC_FED_PAYMENT_DB, $id );
	if ( $order instanceof WP_Error ) {
		wp_send_json_error( array( 'message' => __( 'The Order ID not available now, please refresh the page and try again.', 'frontend-dashboard' ) ) );
		exit();
	}

	return array( 'id' => $id, 'order' => $order );
}

/**
 * Order Search User to Add
 */
function fed_order_search_add_function() {
	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
	if ( ! isset( $request['fed_order_search'] ) || '' == $request['fed_order_search'] ) {
		wp_send_json_error( array( 'message' => __( 'Please fill the search field', 'frontend-dashboard' ) ) );
		exit();
	}
	$user = get_user_by( sanitize_text_field( $request['order_search_key'] ), sanitize_text_field( $request['fed_order_search'] ) );

	if ( ! $user ) {
		wp_send_json_error( array( 'message' => __( 'Sorry! No user found', 'frontend-dashboard' ) ) );
		exit();
	}

	wp_send_json_success( array(
		'message' => __( 'Great! You have found an user', 'frontend-dashboard' ),
		'extra'   => array(
			'user_id'    => $user->ID,
			'email'      => $user->user_email,
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
		)
	) );

}

/**
 * Admin Add Order
 */
function fed_admin_add_orders_function() {
	global $wpdb;
	$table_name = $wpdb->prefix . BC_FED_PAYMENT_DB;
	$request    = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	if ( ! wp_verify_nonce( $request['fed_admin_add_orders'], 'fed_admin_add_orders' ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid Request' ) ) );
		exit();
	}
	$validation = fed_order_add_validation( $request );

	if ( $validation instanceof WP_Error ) {
		wp_send_json_error( array( 'message' => $validation->get_error_messages() ) );
		exit();
	}

	$orders = array(
		'transaction_id' => wp_generate_password( 17, false ),
		'payer_id'       => wp_generate_password( 13, false ),
		'invoice_number' => wp_generate_password( 13, false ),
		'sku'            => current_time( 'YmdHis' ) . '_' . $request['user_id'] . '_' . wp_generate_password( 6, false ),
		'user_id'        => isset( $request['user_id'] ) ? (int) $request['user_id'] : '',
		'email'          => isset( $request['email'] ) ? sanitize_email( $request['email'] ) : '',
		'first_name'     => isset( $request['first_name'] ) ? sanitize_text_field( $request['first_name'] ) : '',
		'last_name'      => isset( $request['last_name'] ) ? sanitize_text_field( $request['last_name'] ) : '',
		'recipient_name' => isset( $request['recipient_name'] ) ? sanitize_text_field( $request['recipient_name'] ) : '',
		'amount'         => isset( $request['amount'] ) ? (float) $request['amount'] : '',
		'line1'          => isset( $request['line1'] ) ? sanitize_text_field( $request['line1'] ) : '',
		'city'           => isset( $request['city'] ) ? sanitize_text_field( $request['city'] ) : '',
		'state'          => isset( $request['state'] ) ? sanitize_text_field( $request['state'] ) : '',
		'postal_code'    => isset( $request['postal_code'] ) ? sanitize_text_field( $request['postal_code'] ) : '',
		'country_code'   => isset( $request['country_code'] ) ? sanitize_text_field( $request['country_code'] ) : '',
		'payment_source' => isset( $request['payment_source'] ) ? sanitize_text_field( $request['payment_source'] ) : '',
		'currency_type'  => isset( $request['currency_type'] ) ? sanitize_text_field( $request['currency_type'] ) : 'paypal',
		'created_at'     => date( 'Y-m-d H:i:s' ),
		'updated_at'     => date( 'Y-m-d H:i:s' ),
	);

	$status = $wpdb->insert(
		$table_name,
		$orders
	);

	if ( $status === false ) {
		wp_send_json_error( array( 'message' => __( 'Sorry, Something went wrong in storing values in DB, please try again later or contact support', 'frontend-dashboard' ) ) );
		exit();
	}

	wp_send_json_success( array( 'message' => __( 'New Transaction ID ' . $orders['transaction_id'] . ' successfully added', 'frontend-dashboard' ) ) );
	exit();
}

