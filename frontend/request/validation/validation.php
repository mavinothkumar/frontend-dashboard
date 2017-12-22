<?php
/**
 * Validate User Profile Form.
 *
 * @param array $post Request
 *
 * @return bool|WP_Error
 */
function fed_validate_user_profile_form( $post ) {
	/**
	 * Both Password Match.
	 */
	$fed_error        = new WP_Error();
	$mandatory_fields = fed_process_user_profile_required_by_menu( $post['tab_id'] );
	if ( isset( $mandatory_fields['user_pass'] ) ) {
		unset( $mandatory_fields['user_pass'] );
		unset( $mandatory_fields['confirmation_password'] );
	}

	if ( isset( $post['user_pass'] ) && $post['user_pass'] != '' && $post['user_pass'] !== $post['confirmation_password'] ) {
		$fed_error->add( 'password_not_match', __( 'Password not match' ) );
	}

	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( $post[ $key ] == '' ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}

/**
 * Validate New Manual Order
 * @param array $request Request
 *
 * @return bool|WP_Error
 */
function fed_order_add_validation( $request ) {
	$fed_error = new WP_Error();

	if ( empty( $request['user_id'] ) || ! isset( $request['user_id'] ) ) {
		wp_send_json_error( array( 'message' => __( 'You can able to add only the registered user, So please use the search functionality to find the user', 'frontend-dashboard' ) ));
		exit();
	}

	$mandatory_fields = array(
		'email'          => 'Email',
		'first_name'     => 'First Name',
		'last_name'      => 'Last Name',
		'amount'         => 'Amount',
		'user_id'        => 'User ID',
		'currency_type'  => 'Currency Type',
		'payment_source' => 'Payment Source'
	);
	foreach ( $mandatory_fields as $key => $mandatory_field ) {
		if ( $request[ $key ] == '' ) {
			$fed_error->add( $key, $mandatory_field );
		}
	}

	if ( $fed_error->get_error_codes() ) {
		return $fed_error;
	}

	return true;
}