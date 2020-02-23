<?php
/**
 * Transactions.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDTransaction' ) ) {
	/**
	 * Class FEDTransaction
	 */
	class FEDTransaction {

		/**
		 * Transactions.
		 */
		public function transactions() {
			/**
			 * Payment Gateways
			 */
			$this->authorize();

			echo do_shortcode( '[fed_transactions]' );
		}

		/**
		 * Authorize.
		 */
		public function authorize() {
			if ( ! is_user_logged_in() ) {
				$error_message = __( 'Error 403: Please login to view this page', 'frontend-dashboard' );
				wp_die( $error_message );
			}
		}

		/**
		 * Update.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {
			$this->authorize();

			if ( ! is_admin() ) {
				wp_die( __( 'Error 403: You are not allowed to view this page', 'frontend-dashboard' ) );
			}

			/**
			 * TODO: Update manual adding Transaction Payments.
			 * Validation.
			 */
			$validate = new FED_Validation();
			$validate->name( __( 'User Name', 'frontend-dashboard' ) )->value( fed_get_data( 'user_id' ) )->required();
			$validate->name(
				__(
					'Transaction ID',
					'frontend-dashboard'
				)
			)->value( fed_get_data( 'transaction_id' ) )->required();
			$validate->name(
				__(
					'Purchase Date',
					'frontend-dashboard'
				)
			)->value( fed_get_data( 'created' ) )->required();
			$validate->name(
				__(
					'Payment Source',
					'frontend-dashboard'
				)
			)->value( fed_get_data( 'payment_source' ) )->required();
			$validate->name(
				__(
					'Payment Type',
					'frontend-dashboard'
				)
			)->value( fed_get_data( 'fed_pp_object_type' ) )->required();
			$validate->name(
				__(
					'Product Name',
					'frontend-dashboard'
				)
			)->value( fed_get_data( 'fed_pp_object_id' ) )->is_array( 1 );

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}

			$table = fed_get_payment_for( esc_attr( $request['fed_pp_object_type'] ) );

			if ( isset( $table['object_table'] ) && ! empty( $table['object_table'] ) ) {
				$values           = fed_fetch_table_row_by_ids( $table['object_table'], $request['fed_pp_object_id'] );
				$request['items'] = $values;
				$status           = $this->add_transaction( $request );

				if ( $status ) {
					wp_send_json_success(
						array(
							'message' => __( 'Transaction Added Successfully', 'frontend-dashboard' ),
						)
					);
				}
			}
			// add the Transactions.
			// // FED_Log::writeLog( array( '$request' => $request ) );.
			wp_send_json_error(
				array(
					'message' => __(
						'OOPs! There is some issue in adding the record, please check the log',
						'frontend-dashboard'
					),
				)
			);

		}

		/**
		 * Add Transaction.
		 *
		 * @param  array $request  Request.
		 *
		 * @return bool.
		 */
		public function add_transaction( $request ) {
			if ( isset( $request['items'] ) && count( $request['items'] ) > 0 ) {
				$user_update = true;
				$type        = isset( $request['fed_pp_object_type'] ) && ! empty( $request['fed_pp_object_type'] ) ? $request['fed_pp_object_type'] : '';
				$data        = $this->format_transaction( $request, $type );
				if ( isset( $data['transaction'] ) ) {
					$user_role = fed_get_data( 'user_role', $data['transaction'] );

					unset( $data['transaction']['user_role'] );

					// // FED_Log::writeLog( [ '$data' => $data ] );.
					$payment = fed_insert_new_row( BC_FED_TABLE_PAYMENT, $data['transaction'] );

					// // FED_Log::writeLog( [ '$payment' => $payment ] );.
					$status = fed_mp_add_payment_meta( $data, $payment, $request );

					if ( $payment && $status ) {
						if ( $user_role ) {
							$user_update = wp_update_user(
								array(
									'ID'   => (int) $request['user_id'],
									'role' => $user_role,
								)
							);
							if ( $user_update instanceof WP_Error ) {
								/**
								 * // FED_Log::writeLog(
								 * array(
								 * '$status'      => $status,
								 * '$user_role'   => $user_role,
								 * '$user_update' => $user_update,
								 * )
								 * );
								 */
								return false;
							}
						}

						return true;
					}
				}

				/**
				 * // FED_Log::writeLog(
				 * array(
				 * '$status'      => $status,
				 * '$user_role'   => $user_role,
				 * '$user_update' => $user_update,
				 * )
				 * );
				 */
				return false;
			}
		}

		/**
		 * Format Transaction.
		 *
		 * @param  array  $request  Request.
		 *
		 * @param  string $payment_type  Payment Type.
		 *
		 * @return array
		 */
		public function format_transaction( $request, $payment_type = '' ) {
			$total     = 0;
			$items     = array();
			$user_role = null;
			$currency  = 'USD';
			$ends_at   = '';
			foreach ( $request['items'] as $index => $item ) {
				// finding the Total.
				$user_role     = isset( $item['user_role'] ) && ( 'fed_null' !== $item['user_role'] ) ? $item['user_role'] : null;
				$ends_at       = fed_get_membership_expiry_date( $item );
				$amount        = isset( $item['amount'] ) ? (float) $item['amount'] : 0;
				$discount      = isset( $item['discount_value'] ) ? (float) $item['discount_value'] : 0;
				$tax           = isset( $item['tax_value'] ) ? (float) $item['tax_value'] : 0;
				$shipping      = isset( $item['shipping_value'] ) ? (float) $item['shipping_value'] : 0;
				$discount_cost = 0;
				$tax_cost      = 0;
				$shipping_cost = 0;
				$quantity      = isset( $item['quantity'] ) && ! empty( $item['quantity'] ) ? (int) $item['quantity'] : 1;
				$currency      = isset( $item['currency'] ) ? fed_sanitize_text_field( $item['currency'] ) : '';
				if ( ! empty( $payment_type ) ) {
					$type = $payment_type;
				}
				else {
					$type = ( isset( $item['type'] ) && ! empty( $item['type'] ) ) ? $item['type'] : '';
				}

				if ( $discount ) {
					$discount_cost = fed_get_exact_amount( $item, 'discount' );
				}
				if ( $tax ) {
					$tax_cost = fed_get_exact_amount( $item, 'tax' );
				}
				if ( $shipping ) {
					$shipping_cost = fed_get_exact_amount( $item, 'shipping' );
				}

				$discounted_amount = ( $amount + $tax_cost + $shipping_cost ) - ( $discount_cost ) * $quantity;

				$total = $total + $discounted_amount;

				$id           = isset( $item['id'] ) ? (int) $item['id'] : fed_get_random_string( 7 );
				$items[ $id ] = array(
					'id'                => $id,
					'amount'            => $amount,
					'total'             => $discounted_amount,
					'currency'          => $currency,
					'plan_type'         => fed_sanitize_text_field( fed_get_data( 'plan_type', $item ) ),
					'plan_id'           => fed_sanitize_text_field( fed_get_data( 'plan_id', $item ) ),
					'gateway'           => fed_sanitize_text_field( fed_get_data( 'gateway', $item ) ),
					'plan_days'         => fed_sanitize_text_field( fed_get_data( 'plan_days', $item ) ),
					'plan_name'         => fed_sanitize_text_field( fed_get_data( 'plan_name', $item ) ),
					'default_user_role' => fed_sanitize_text_field( fed_get_data( 'default_user_role', $item ) ),
					'user_role'         => fed_sanitize_text_field( fed_get_data( 'user_role', $item ) ),
					'quantity'          => $quantity,
					'discount'          => fed_sanitize_text_field( fed_get_data( 'discount', $item ) ),
					'discount_value'    => fed_sanitize_text_field( fed_get_data( 'discount_value', $item ) ),
					'tax'               => fed_sanitize_text_field( fed_get_data( 'tax', $item ) ),
					'tax_value'         => fed_sanitize_text_field( fed_get_data( 'tax_value', $item ) ),
					'shipping'          => fed_sanitize_text_field( fed_get_data( 'shipping', $item ) ),
					'shipping_value'    => fed_sanitize_text_field( fed_get_data( 'shipping_value', $item ) ),
					'note_to_payee'     => fed_sanitize_text_field( fed_get_data( 'note_to_payee', $item ) ),
					'description'       => fed_sanitize_text_field( fed_get_data( 'description', $item ) ),
				);
			}

			$transaction = array(
				'user_id'        => (int) fed_get_data( 'user_id', $request, get_current_user_id() ),
				'transaction_id' => fed_sanitize_text_field( fed_get_data( 'transaction_id', $request ) ),
				'amount'         => $total,
				'currency'       => $currency,
				'payment_type'   => ! empty( $type ) ? $type : 'NA',
				'payment_source' => fed_sanitize_text_field( fed_get_data( 'payment_source', $request ) ),
				'updated'        => current_time( 'Y-m-d' ),
				'created'        => isset( $request['created'] ) ? date(
					'Y-m-d H:i:s',
					strtotime( fed_sanitize_text_field( $request['created'] ) )
				) : '',
				'status'         => fed_sanitize_text_field( fed_get_data( 'status', $request, 'Pending' ) ),
				'ends_at'        => $ends_at,
				'user_role'      => $user_role,
			);

			return array(
				'transaction' => $transaction,
				'items'       => $items,
			);
		}


		/**
		 * Items.
		 *
		 * @param  array $request  Request.
		 */
		public function items( $request ) {
			fed_verify_nonce( $request );

			if ( isset( $request['transaction_id'] ) ) {
				$items = fed_get_transaction_with_meta( $request['transaction_id'] );
				$html  = fed_transaction_product_details( $items );
				wp_send_json_success( array( 'html' => $html ) );
			}
			wp_send_json_error( array( 'html' => __( 'Something went wrong', 'frontend-dashboard' ) ) );
		}

		/**
		 * Add items.
		 *
		 * @param  array $request  Request.
		 */
		public function add_items( $request ) {
			if ( isset( $request['type'] ) ) {
				global $wpdb;
				$table      = fed_get_payment_for( esc_attr( $request['type'] ) );
				$table_name = $wpdb->prefix . $table['object_table'];
				if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}' " ) === $table_name ) {
					$records = $wpdb->get_results( "SELECT * FROM `{$table_name}` ", ARRAY_A );
					if ( $records && count( $records ) > 0 ) {
						$formatted = fed_get_key_value_array( $records, 'id', 'plan_name' );
						$data_url  = add_query_arg(
							array(
								'fed_action_hook' => 'FEDTransaction@add_new_item',
								'fed_nonce'       => wp_create_nonce( 'fed_nonce' ),
							), fed_get_ajax_form_action( 'fed_ajax_request' )
						);
						$html      = '<div class="fed_flex_start_center fed_transaction_item"><div>';
						$html      .= '<label>' . __( 'Please select your product', 'frontend-dashboard' ) . '</label>';
						$html      .= '<select name="fed_pp_object_id[]" class="form-control">';
						foreach ( $formatted as $key => $format ) {
							$html .= '<option value="' . $key . '">' . $format . '</option>';
						}
						$html .= '</select></div>';
						$html .= '<div class="m-t-20 fed_m_l_10"><button type="button" class="btn btn-danger m-r-10 fed_delete_transaction_item"><i class="fa fa-trash"></i></button><button  type="button" class="btn btn-primary fed_add_transaction_item" data-url="' . $data_url . '"><i class="fa fa-plus"></i></button></div></div>';

						wp_send_json_success( array( 'html' => $html ) );
					}
				}
				wp_send_json_error( array( 'message' => __( 'Table not exist', 'frontend-dashboard' ) ) );
			}
		}

		/**
		 * Add New Item.
		 *
		 * @param  array $request  Request.
		 */
		public function add_new_item( $request ) {

		}

		/**
		 * Add New Transaction.
		 *
		 * @param  string $request  Request.
		 */
		public function add_new_transaction( $request ) {
			?>
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label>
							<?php esc_attr_e( 'Gateway', 'frontend-dashboard' ); ?>
						</label>
						<?php echo fed_form_select(
							array(
								'input_meta'  => 'gateway',
								'user_value'  => '',
								'input_value' => array_merge(
									array(
										'' => __( 'Please select Gateway', 'frontend-dashboard-membership' ),
									), fed_get_only_payment_gateways()
								),
							)
						) ?>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label>
							<?php esc_attr_e( 'Gateway', 'frontend-dashboard' ); ?>
						</label>
						<?php echo fed_form_select(
							array(
								'input_meta'  => 'gateway',
								'user_value'  => '',
								'input_value' => array_merge(
									array(
										'' => __( 'Please select Gateway', 'frontend-dashboard-membership' ),
									), fed_get_payment_for_key_index()
								),
							)
						) ?>
					</div>
				</div>

				<div class="col-md-3">
					<div class="form-group">
						<label>
							<?php esc_attr_e( 'User', 'frontend-dashboard' ); ?>
						</label>
						<?php
						wp_dropdown_users( array( 'class' => 'fed_multi_select' ) );
						?>
					</div>
				</div>
			</div>
			<?php
		}

	}

	new FEDTransaction();
}
