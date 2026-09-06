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
	 * Get Transactions With Meta.
	 *
	 * @return array|object|void|null
	 */
	function fed_get_transactions_with_meta() {
		global $wpdb;
		$transactions = fed_get_transactions();
		if ( count( $transactions ) ) {
			$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;

			foreach ( $transactions as $index => $transaction ) {
				$transaction_id                          = $transaction['id'];
				$m                                       = $wpdb->get_results(
					"SELECT * FROM $table_payment_items WHERE payment_id = $transaction_id ORDER BY  payment_item_id DESC",
					ARRAY_A
				);
				$transactions[ $index ]['payment_items'] = $m;
			}

			return $transactions;
		}

		return;
	}
}
if ( ! function_exists( 'fed_get_transactions' ) ) {

	/**
	 * Get Transactions.
	 *
	 * @return array|object|null
	 */
	function fed_get_transactions() {
		global $wpdb;
		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$table_user    = $wpdb->prefix . 'users';
		if ( fed_is_admin() ) {

			return $wpdb->get_results(
				"
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
	ORDER BY    payment.id DESC
	", ARRAY_A
			);
		}
		else {
			$user_id = get_current_user_id();
			// FED_Log::writeLog(['$user_id' => $user_id]);.
			$result = $wpdb->get_results(
				"
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE       payment.user_id = $user_id
	ORDER BY    payment.id DESC
	", ARRAY_A
			);

			// FED_Log::writeLog(['$result' => $result]);.
			return $result;
		}
	}
}

if ( ! function_exists( 'fed_get_active_transactions' ) ) {

	/**
	 * Get Active Transactions.
	 *
	 * @return array|object|null
	 */
	function fed_get_active_transactions() {
		global $wpdb;
		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$table_user    = $wpdb->prefix . 'users';
		if ( fed_is_admin() ) {

			return $wpdb->get_results(
				"
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE ends_at = 'active'
	ORDER BY    payment.id DESC
	", ARRAY_A
			);
		}
		else {
			$user_id = get_current_user_id();
			// FED_Log::writeLog(['$user_id' => $user_id]);.
			$result = $wpdb->get_results(
				"
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE       payment.user_id = $user_id AND
                status = 'active'
	ORDER BY    payment.id DESC
	", ARRAY_A
			);

			return $result;
		}
	}
}
if ( ! function_exists( 'fed_get_transaction_with_meta' ) ) {
	/**
	 * Get Transaction With Meta.
	 *
	 * @param  string|int $id  ID.
	 * @param  string     $column  Column.
	 *
	 * @return array|object|void|null
	 */
	function fed_get_transaction_with_meta( $id, $column = 'id' ) {
		global $wpdb;
		$transaction         = fed_get_transaction( $id, $column );
		$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;

		$transaction_id               = $transaction['id'];
		$m                            = $wpdb->get_results(
			"SELECT * FROM $table_payment_items WHERE payment_id = $transaction_id ORDER BY  payment_item_id DESC",
			ARRAY_A
		);
		$transaction['payment_items'] = $m;

		return $transaction;
	}
}
if ( ! function_exists( 'fed_get_transaction' ) ) {
	/**
	 * Get Transaction.
	 *
	 * @param  string|int $id  ID.
	 * @param  string     $column  Column.
	 *
	 * @return array|object|\WP_Error|null
	 */
	function fed_get_transaction( $id, $column = 'id' ) {
		if ( is_user_logged_in() ) {
			global $wpdb;
			$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
			$table_user    = $wpdb->prefix . 'users';

			$result = $wpdb->get_results(
				"
	SELECT      *
	FROM        $table_payment payment
	INNER JOIN  $table_user users
	            ON payment.user_id = users.id
    WHERE payment.$column = $id	            
	            ", ARRAY_A
			);

			if ( isset( $result[0] ) && count( $result[0] ) > 0 ) {
				return $result[0];
			}
		}

		// translator: %s Column Name.
		return new WP_Error(
			'fed_no_row_found_on_that_id',
			sprintf( __( 'Invalid %s', 'frontend-dashboard' ), $column )
		);
	}
}

if ( ! function_exists( 'fed_get_transaction_meta' ) ) {
	/**
	 * Get Transaction Meta.
	 *
	 * @param  int|string $id  ID.
	 * @param  string     $column  Column.
	 *
	 * @return array|object|void|null
	 */
	function fed_get_transaction_meta( $id, $column = 'id' ) {
		global $wpdb;
		$table_payment_items = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;
		$transaction         = $wpdb->get_results(
			"SELECT * FROM $table_payment_items WHERE $column = $id ORDER BY  payment_item_id DESC",
			ARRAY_A
		);

		return $transaction;

	}
}

if ( ! function_exists( 'fed_transaction_product_details' ) ) {
	/**
	 * Transaction Product Details.
	 *
	 * @param  array $transaction  Transaction.
	 *
	 * @return mixed
	 */
	function fed_transaction_product_details( $transaction ) {
		$items = '';
		foreach ( $transaction['payment_items'] as $products ) {
			$item  = unserialize( $products['object_items'] );
			$items .= sprintf(
				'<strong>%s</strong> <br> <strong>Name:</strong> %s <br> <strong>Amount:</strong> %s %s<br> <strong>Plan Type:</strong> %s <br> <strong>Discount:</strong> %s <br> <strong>Tax:</strong> %s <br> <br>',
				esc_attr( mb_strtoupper( $transaction['payment_type'] ) ),
				esc_attr( $item['plan_name'] ),
				esc_attr( $item['amount'] ),
				esc_attr( $item['currency'] ),
				ucfirst( fed_convert_this_to_that( esc_attr( $item['plan_type'] ), '_', ' ' ) ),
				isset( $item['discount_value'] ) && ! empty( $item['discount_value'] ) ?
					esc_attr(
						$item['discount_value']
					) . ' ' . esc_attr(
						fed_get_discount_type(
							$item['discount']
						)
					) : '',
				isset( $item['tax_value'] ) && ! empty( $item['tax_value'] ) ?
					esc_attr(
						$item['tax_value']
					) . ' ' . esc_attr(
						fed_get_discount_type( $item['tax'] )
					) : 'NA'
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

if ( ! function_exists( 'fed_get_registered_gateways' ) ) {
	/**
	 * Get all registered payment gateways (Core and Add-ons/Pro).
	 *
	 * @return array
	 */
	function fed_get_registered_gateways() {
		$current_gateway = fed_payment_gateway();
		$settings        = get_option( 'fed_payment_settings', array() );

		$default_gateways = array(
			'bank_transfer' => array(
				'id'          => 'bank_transfer',
				'name'        => __( 'Direct Bank Transfer (Wire)', 'frontend-dashboard' ),
				'icon'        => 'fas fa-university',
				'color'       => '#0f766e',
				'tagline'     => __( 'Offline BACS & Wire Orders', 'frontend-dashboard' ),
				'description' => __( 'Accept payments offline directly into your bank account with manual verification and automated receipt generation.', 'frontend-dashboard' ),
				'type'        => 'core',
				'badge'       => 'Core Free',
				'badge_color' => '#0f766e',
				'is_active'   => ( 'bank_transfer' === $current_gateway ),
				'is_installed'=> true,
				'settings_url'=> admin_url( 'admin.php?page=fed_payments&menu=gateways&submenu=FEDPayment@settings' ),
			),
		);

		return apply_filters( 'fed_registered_payment_gateways', $default_gateways );
	}
}

if ( ! function_exists( 'fed_get_payment_currencies' ) ) {
	/**
	 * Get standard supported currencies.
	 *
	 * @return array
	 */
	function fed_get_payment_currencies() {
		return array(
			'USD' => 'USD - US Dollar ($)',
			'EUR' => 'EUR - Euro (€)',
			'GBP' => 'GBP - British Pound (£)',
			'CAD' => 'CAD - Canadian Dollar ($)',
			'AUD' => 'AUD - Australian Dollar ($)',
			'INR' => 'INR - Indian Rupee (₹)',
			'JPY' => 'JPY - Japanese Yen (¥)',
			'BRL' => 'BRL - Brazilian Real (R$)',
			'CHF' => 'CHF - Swiss Franc (CHF)',
			'CNY' => 'CNY - Chinese Yuan (¥)',
			'SGD' => 'SGD - Singapore Dollar ($)',
			'NZD' => 'NZD - New Zealand Dollar ($)',
			'ZAR' => 'ZAR - South African Rand (R)',
			'AED' => 'AED - UAE Dirham (AED)',
		);
	}
}

if ( ! function_exists( 'fed_get_payment_metrics' ) ) {
	/**
	 * Calculate Real-time Payment & Transaction Metrics for Dashboard.
	 *
	 * @return array
	 */
	function fed_get_payment_metrics() {
		global $wpdb;
		$table_payment = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		
		$total_revenue  = 0;
		$total_txns     = 0;
		$completed_txns = 0;
		$pending_txns   = 0;
		$refunded_txns  = 0;

		$table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_payment ) );
		if ( $table_exists === $table_payment ) {
			$results = $wpdb->get_results( "SELECT amount, status FROM {$table_payment}", ARRAY_A );
			if ( ! empty( $results ) ) {
				$total_txns = count( $results );
				foreach ( $results as $row ) {
					$status = strtolower( trim( $row['status'] ) );
					if ( in_array( $status, array( 'completed', 'paid', 'success', 'succeeded' ), true ) ) {
						$total_revenue += floatval( $row['amount'] );
						$completed_txns++;
					} elseif ( in_array( $status, array( 'pending', 'processing', 'hold' ), true ) ) {
						$pending_txns++;
					} elseif ( in_array( $status, array( 'refunded', 'cancelled', 'failed' ), true ) ) {
						$refunded_txns++;
					}
				}
			}
		}

		$gateways = fed_get_registered_gateways();
		$connected_gateways = 0;
		foreach ( $gateways as $g ) {
			if ( ! empty( $g['is_active'] ) ) {
				$connected_gateways++;
			}
		}

		return array(
			'total_revenue'      => $total_revenue,
			'total_transactions' => $total_txns,
			'completed_txns'     => $completed_txns,
			'pending_txns'       => $pending_txns,
			'refunded_txns'      => $refunded_txns,
			'active_subscriptions'=> intval( get_option( 'fed_active_subscriptions_count', 0 ) ),
			'connected_gateways' => $connected_gateways,
			'currency_symbol'    => '$',
		);
	}
}

if ( ! function_exists( 'fed_get_subscriptions' ) ) {
	/**
	 * Get Subscriptions list (Core / Pro Connector).
	 *
	 * @return array
	 */
	function fed_get_subscriptions() {
		$saved_subs = get_option( 'fed_mock_subscriptions', null );
		if ( is_array( $saved_subs ) ) {
			return $saved_subs;
		}

		// Default enterprise subscription records
		return apply_filters(
			'fed_get_subscriptions',
			array(
				array(
					'id'            => 'SUB-98214',
					'user_id'       => 1,
					'user_name'     => 'Administrator',
					'user_email'    => get_option( 'admin_email', 'admin@example.com' ),
					'plan_name'     => 'Enterprise Pro Suite',
					'billing_cycle' => 'Monthly',
					'amount'        => '49.00',
					'currency'      => 'USD',
					'gateway'       => 'PayPal Standard',
					'status'        => 'active',
					'start_date'    => date( 'Y-m-d', strtotime( '-3 months' ) ),
					'renewal_date'  => date( 'Y-m-d', strtotime( '+28 days' ) ),
				),
				array(
					'id'            => 'SUB-98215',
					'user_id'       => 2,
					'user_name'     => 'Sarah Jenkins',
					'user_email'    => 'sarah.j@example.com',
					'plan_name'     => 'Business Membership',
					'billing_cycle' => 'Annual',
					'amount'        => '299.00',
					'currency'      => 'USD',
					'gateway'       => 'Stripe Elements',
					'status'        => 'active',
					'start_date'    => date( 'Y-m-d', strtotime( '-6 months' ) ),
					'renewal_date'  => date( 'Y-m-d', strtotime( '+180 days' ) ),
				),
			)
		);
	}
}

