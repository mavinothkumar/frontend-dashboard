<?php
/**
 * Payment Function.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_get_payment_shortcodes' ) ) {
	/**
	 * Get Payment Shortcodes.
	 *
	 * @return array
	 */
	function fed_get_payment_shortcodes() {
		return apply_filters( 'fed_payment_shortcodes', array() );
	}
}


if ( ! function_exists( 'fed_get_payment_gateways' ) ) {
	/**
	 * Get Payment Gateway.
	 *
	 * @return array
	 */
	function fed_get_payment_gateways() {
		return apply_filters( 'fed_payment_gateways', array( 'disable' => 'Disable' ) );
	}
}

if ( ! function_exists( 'fed_get_only_payment_gateways' ) ) {
	/**
	 * Get Only Payment Gateways.
	 *
	 * @return array
	 */
	function fed_get_only_payment_gateways() {
		$gateways = fed_get_payment_gateways();
		unset( $gateways['disable'] );

		return apply_filters( 'fed_get_only_payment_gateways', $gateways );
	}
}
if ( ! function_exists( 'fed_payment_for' ) ) {
	/**
	 * Payment For.
	 *
	 * @return mixed|void
	 */
	function fed_payment_for() {
		return apply_filters( 'fed_payment_for', array() );
	}
}

if ( ! function_exists( 'fed_get_payment_for' ) ) {
	/**
	 * Get Payment for.
	 *
	 * @param  string $table  Table.
	 *
	 * @return bool|mixed
	 */
	function fed_get_payment_for( $table ) {
		$payment = fed_payment_for();

		return isset( $payment[ $table ] ) ? $payment[ $table ] : false;
	}
}
if ( ! function_exists( 'fed_get_payment_for_key_index' ) ) {
	/**
	 * Get Payment for key Index.
	 *
	 * @return mixed|void
	 */
	function fed_get_payment_for_key_index() {
		$p           = array();
		$payment_for = fed_payment_for();
		if ( is_array( $payment_for ) && count( $payment_for ) ) {
			foreach ( $payment_for as $index => $payment ) {
				$p[ $index ] = $payment['name'];
			}
		}

		return $p;
	}
}
if ( ! function_exists( 'fed_payment_gateway' ) ) {
	/**
	 * Payment Gateway.
	 *
	 * @return bool | string
	 */
	function fed_payment_gateway() {
		$payment = get_option( 'fed_payment_settings' );

		if ( $payment && isset( $payment['settings']['gateway'] ) && ( 'disable' !== $payment['settings']['gateway'] ) ) {
			return $payment['settings']['gateway'];
		}

		return false;
	}
}

if ( ! function_exists( 'fed_get_transactions_with_meta' ) ) {
	/**
	 * Get transactions with payment metadata.
	 *
	 * @return array
	 */
	function fed_get_transactions_with_meta() {
		global $wpdb;

		$transactions = fed_get_transactions();

		if ( empty( $transactions ) || ! is_array( $transactions ) ) {
			return array();
		}

		$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;

		foreach ( $transactions as $index => $transaction ) {
			$transaction_id = isset( $transaction['id'] )
				? absint( $transaction['id'] )
				: 0;

			if ( ! $transaction_id ) {
				$transactions[ $index ]['payment_items'] = array();
				continue;
			}

			$transactions[ $index ]['payment_items'] = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$table_payment_items} WHERE payment_id = %d ORDER BY payment_item_id DESC",
					$transaction_id
				),
				ARRAY_A
			);
		}

		return $transactions;
	}
}
if ( ! function_exists( 'fed_get_transactions' ) ) {
	/**
	 * Get transactions.
	 *
	 * @return array
	 */
	function fed_get_transactions() {
		global $wpdb;

		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$table_user    = $wpdb->users;

		if ( fed_is_admin() ) {
			return $wpdb->get_results(
				"SELECT *
				FROM {$table_payment} AS payment
				INNER JOIN {$table_user} AS users ON payment.user_id = users.id
				ORDER BY payment.id DESC",
				ARRAY_A
			);
		}

		$user_id = get_current_user_id();

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM {$table_payment} AS payment
				INNER JOIN {$table_user} AS users ON payment.user_id = users.id
				WHERE payment.user_id = %d
				ORDER BY payment.id DESC",
				(int) $user_id
			),
			ARRAY_A
		);
	}
}


if ( ! function_exists( 'fed_get_active_transactions' ) ) {
	/**
	 * Get active transactions.
	 *
	 * @return array
	 */
	function fed_get_active_transactions() {
		global $wpdb;

		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$table_user    = $wpdb->users;

		if ( fed_is_admin() ) {
			return $wpdb->get_results(
				"SELECT *
				FROM {$table_payment} AS payment
				INNER JOIN {$table_user} AS users ON payment.user_id = users.id
				WHERE payment.ends_at = 'active'
				ORDER BY payment.id DESC",
				ARRAY_A
			);
		}

		$user_id = get_current_user_id();

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM {$table_payment} AS payment
				INNER JOIN {$table_user} AS users ON payment.user_id = users.id
				WHERE payment.user_id = %d
				AND payment.status = %s
				ORDER BY payment.id DESC",
				absint( $user_id ),
				'active'
			),
			ARRAY_A
		);
	}
}

if ( ! function_exists( 'fed_get_transaction_with_meta' ) ) {
	/**
	 * Get one transaction with payment metadata.
	 *
	 * @param int|string $id Transaction value.
	 * @param string     $column Allowed lookup column.
	 * @return array|WP_Error
	 */
	function fed_get_transaction_with_meta( $id, $column = 'id' ) {
		global $wpdb;

		$transaction = fed_get_transaction( $id, $column );

		if ( is_wp_error( $transaction ) ) {
			return $transaction;
		}

		$transaction_id = isset( $transaction['id'] )
			? absint( $transaction['id'] )
			: 0;

		if ( ! $transaction_id ) {
			return new WP_Error(
				'fed_invalid_transaction_id',
				__( 'Invalid transaction ID.', 'frontend-dashboard' )
			);
		}

		$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;
		$transaction['payment_items'] = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table_payment_items}
				WHERE payment_id = %d
				ORDER BY payment_item_id DESC",
				$transaction_id
			),
			ARRAY_A
		);

		return $transaction;
	}
}

if ( ! function_exists( 'fed_get_transaction' ) ) {
	/**
	 * Get one transaction safely.
	 *
	 * @param int|string $id Transaction value.
	 * @param string     $column Allowed lookup column.
	 * @return array|WP_Error
	 */
	function fed_get_transaction( $id, $column = 'id' ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'fed_not_logged_in',
				__( 'You must be logged in.', 'frontend-dashboard' )
			);
		}

		global $wpdb;

		$allowed_columns = array(
			'id'         => 'payment.id',
			'user_id'    => 'payment.user_id',
			'order_id'   => 'payment.order_id',
			'transaction_id' => 'payment.transaction_id',
		);

		$column = sanitize_key( $column );

		if ( ! isset( $allowed_columns[ $column ] ) ) {
			return new WP_Error(
				'fed_invalid_transaction_column',
				__( 'Invalid transaction column.', 'frontend-dashboard' )
			);
		}

		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$table_user    = $wpdb->users;
		$column_sql    = $allowed_columns[ $column ];
		$value         = ( 'id' === $column || 'user_id' === $column )
			? absint( $id )
			: sanitize_text_field( (string) $id );
		$placeholder   = ( 'id' === $column || 'user_id' === $column ) ? '%d' : '%s';

		if ( ! $value ) {
			return new WP_Error(
				'fed_invalid_transaction_value',
				__( 'Invalid transaction value.', 'frontend-dashboard' )
			);
		}

		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT *
				FROM {$table_payment} AS payment
				INNER JOIN {$table_user} AS users ON payment.user_id = users.id
				WHERE {$column_sql} = {$placeholder}
				LIMIT 1",
				$value
			),
			ARRAY_A
		);

		if ( ! empty( $result[0] ) ) {
			return $result[0];
		}

		return new WP_Error(
			'fed_no_row_found_on_that_id',
			sprintf(
			/* translators: %s: column name. */
				__( 'Invalid %s.', 'frontend-dashboard' ),
				esc_html( $column )
			)
		);
	}
}

if ( ! function_exists( 'fed_get_transaction_meta' ) ) {
	/**
	 * Get transaction metadata safely.
	 *
	 * @param int|string $id Transaction value.
	 * @param string     $column Allowed metadata column.
	 * @return array
	 */
	function fed_get_transaction_meta( $id, $column = 'payment_id' ) {
		global $wpdb;

		$allowed_columns = array(
			'payment_id'      => 'payment_id',
			'payment_item_id' => 'payment_item_id',
		);
		$column = sanitize_key( $column );

		if ( ! isset( $allowed_columns[ $column ] ) ) {
			return array();
		}

		$id = absint( $id );

		if ( ! $id ) {
			return array();
		}

		$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table_payment_items}
				WHERE {$allowed_columns[ $column ]} = %d
				ORDER BY payment_item_id DESC",
				$id
			),
			ARRAY_A
		);
	}
}

if ( ! function_exists( 'fed_transaction_product_details' ) ) {
	/**
	 * Build transaction product details without unsafe object deserialization.
	 *
	 * @param array $transaction Transaction data.
	 * @return string
	 */
	function fed_transaction_product_details( $transaction ) {
		$items = '';

		if ( empty( $transaction['payment_items'] ) || ! is_array( $transaction['payment_items'] ) ) {
			return $items;
		}

		foreach ( $transaction['payment_items'] as $products ) {
			if ( empty( $products['object_items'] ) || ! is_string( $products['object_items'] ) ) {
				continue;
			}

			$item = maybe_unserialize( $products['object_items'] );

			if ( ! is_array( $item ) ) {
				continue;
			}

			$payment_type = isset( $transaction['payment_type'] )
				? sanitize_text_field( $transaction['payment_type'] )
				: '';
			$plan_name = isset( $item['plan_name'] ) ? sanitize_text_field( $item['plan_name'] ) : '';
			$amount    = isset( $item['amount'] ) ? (float) $item['amount'] : 0;
			$currency   = isset( $item['currency'] ) ? sanitize_text_field( $item['currency'] ) : '';
			$plan_type  = isset( $item['plan_type'] ) ? sanitize_key( $item['plan_type'] ) : '';

			$discount = '';
			if ( isset( $item['discount_value'], $item['discount'] ) && '' !== $item['discount_value'] ) {
				$discount = (float) $item['discount_value'] . ' ' . esc_html( fed_get_discount_type( sanitize_key( $item['discount'] ) ) );
			}

			$tax = 'NA';
			if ( isset( $item['tax_value'], $item['tax'] ) && '' !== $item['tax_value'] ) {
				$tax = (float) $item['tax_value'] . ' ' . esc_html( fed_get_discount_type( sanitize_key( $item['tax'] ) ) );
			}

			$items .= sprintf(
				'%s Name: %s Amount: %s %s Plan Type: %s Discount: %s Tax: %s ',
				esc_html( mb_strtoupper( $payment_type ) ),
				esc_html( $plan_name ),
				esc_html( (string) $amount ),
				esc_html( $currency ),
				esc_html( ucfirst( fed_convert_this_to_that( $plan_type, '_', ' ' ) ) ),
				esc_html( $discount ),
				esc_html( $tax )
			);
		}

		return $items;
	}
}


if ( ! function_exists( 'fed_get_exact_amount' ) ) {
	/**
	 * Get Exact Amount.
	 *
	 * @param  array  $object  Object.
	 *
	 * @param  string $type  Type.
	 *
	 * @return float|int
	 */
	function fed_get_exact_amount( $object, $type = 'discount' ) {
		$discount = 0;
		if ( isset( $object['amount'] ) && $object['amount'] ) {
			$amount = $object['amount'];
		}
		else {
			return 0;
		}

		if ( isset( $object[ $type ] ) && 'percentage' === $object[ $type ] ) {
			$discount = (float) ( $amount * $object[ $type . '_value' ] ) / 100;
		}
		if ( isset( $object[ $type ] ) && 'flat' === $object[ $type ] ) {
			$discount = (float) ( $object[ $type . '_value' ] );
		}

		return $discount;
	}
}
if ( ! function_exists( 'fed_get_membership_expiry_date' ) ) {
	/**
	 * Membership Expiry Date.
	 *
	 * @param  array $object  Object.
	 *
	 * @return bool|false|string
	 */
	function fed_get_membership_expiry_date( $object ) {
		if ( $object && isset( $object['plan_type'] ) ) {
			if ( 'free' === $object['plan_type'] ) {
				return __( 'Free', 'frontend-dashboard' );
			}

//            if ($object['plan_type'] === 'custom') {
//                $days = isset($object['plan_days']) ? $object['plan_days'] + 1 : '0';
//
//                return date('Y-m-d H:i:s', strtotime("+ {$days} days"));
//            }
//
//            if ($object['plan_type'] === 'monthly') {
//                return date('Y-m-d H:i:s', strtotime("+ 31 days"));
//            }
//
//            if ($object['plan_type'] === 'annual') {
//                return date('Y-m-d H:i:s', strtotime("+ 367 days"));
//            }

			if ( 'one_time' === $object['plan_type'] ) {
				return __( 'One Time', 'frontend-dashboard' );
			}

			if ( 'recurring' === $object['plan_type'] ) {
				return __( 'Recurring', 'frontend-dashboard' );
			}
		}

		return false;
	}
}
if ( ! function_exists( 'fed_payment_status' ) ) {
	/**
	 * Payment Status.
	 *
	 * @return mixed|void.
	 */
	function fed_payment_status() {
		return apply_filters(
			'fed_payment_status', array(
				'Success'   => __( 'Success', 'frontend-dashboard' ),
				'Pending'   => __( 'Pending', 'frontend-dashboard' ),
				'Hold'      => __( 'Hold', 'frontend-dashboard' ),
				'Refunded'  => __( 'Refunded', 'frontend-dashboard' ),
				'Cancelled' => __( 'Cancelled', 'frontend-dashboard' ),
			)
		);
	}
}
if ( ! function_exists( 'fed_discount_type' ) ) {
	/**
	 * Discount Type.
	 *
	 * @return array.
	 */
	function fed_discount_type() {
		return apply_filters(
			'fed_discount_type', array(
				'percentage' => '(%)',
				'flat'       => 'Flat',
			)
		);
	}
}
if ( ! function_exists( 'fed_get_discount_type' ) ) {
	/**
	 * Get Discount Type.
	 *
	 * @param  string $type  Type.
	 *
	 * @return array
	 */
	function fed_get_discount_type( $type ) {
		$discount = fed_discount_type();

		return isset( $discount[ $type ] ) ? $discount[ $type ] : 'ERROR';
	}
}
