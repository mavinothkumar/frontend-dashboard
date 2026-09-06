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
		 * Transactions list view.
		 */
		public function transactions() {
			$this->authorize();
			$transactions = function_exists( 'fed_get_transactions' ) ? fed_get_transactions() : array();
			$gateways     = function_exists( 'fed_get_only_payment_gateways' ) ? fed_get_only_payment_gateways() : array( 'paypal' => 'PayPal' );
			$currencies   = function_exists( 'fed_get_payment_currencies' ) ? fed_get_payment_currencies() : array( 'USD' => 'USD ($)' );
			?>
			<div class="bc_fed fed_transactions_container" style="font-family: inherit;">
				
				<!-- Heading with Action Button -->
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 14px;">
					<div>
						<h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
							<i class="fas fa-receipt" style="color: #033333;"></i>
							<span><?php esc_html_e( 'All Transactions', 'frontend-dashboard' ); ?></span>
						</h3>
						<p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
							<?php esc_html_e( 'Complete ledger of customer checkouts, membership renewals, and manual financial entries.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<div>
						<button type="button" id="fed_open_add_txn_btn" style="display: inline-flex; align-items: center; gap: 7px; background: #033333; color: #ffffff; border: 1px solid #033333; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 6px rgba(3,51,51,0.2);">
							<i class="fas fa-plus"></i> <?php esc_html_e( 'Add Transaction', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>

				<!-- Action Toolbar: Search, Status Filter Pills & Quick Actions -->
				<div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
					
					<!-- Left: Search Box -->
					<div style="position: relative; flex: 1; min-width: 240px; max-width: 400px;">
						<i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
						<input type="text" id="fed_transaction_search" placeholder="<?php esc_attr_e( 'Search customer, email, txn ID, gateway...', 'frontend-dashboard' ); ?>" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 14px 8px 36px; font-size: 13.5px; color: #1e293b; background: #ffffff; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" />
					</div>

					<!-- Right: Filter Pills -->
					<div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
						<div class="fed_txn_filter_group" style="display: inline-flex; background: #f1f5f9; padding: 3px; border-radius: 8px; border: 1px solid #e2e8f0;">
							<button type="button" class="fed_txn_filter_btn active" data-filter="all" style="background: #ffffff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #0f172a; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
								<?php esc_html_e( 'All', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_txn_filter_btn" data-filter="completed" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Completed', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_txn_filter_btn" data-filter="pending" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Pending', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_txn_filter_btn" data-filter="refunded" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Refunded / Failed', 'frontend-dashboard' ); ?>
							</button>
						</div>
					</div>

				</div>

				<!-- Enterprise Transactions Data Table (100% Full Width) -->
				<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); width: 100%;">
					<div style="overflow-x: auto;">
						<table class="fed_enterprise_table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
							<thead>
								<tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Customer', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Transaction ID', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Gateway / Source', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Amount', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Date', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px; text-align: right;"><?php esc_html_e( 'Actions', 'frontend-dashboard' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( ! empty( $transactions ) ) {
									foreach ( $transactions as $txn ) {
										$status_raw = strtolower( fed_get_data( 'status', $txn, 'pending' ) );
										$status_filter_group = 'pending';
										$status_bg = '#fef3c7';
										$status_color = '#b45309';
										$status_label = __( 'Pending', 'frontend-dashboard' );

										if ( in_array( $status_raw, array( 'completed', 'paid', 'success', 'succeeded', 'active' ), true ) ) {
											$status_filter_group = 'completed';
											$status_bg = '#dcfce7';
											$status_color = '#15803d';
											$status_label = __( 'Completed', 'frontend-dashboard' );
										} elseif ( in_array( $status_raw, array( 'refunded', 'cancelled', 'failed', 'declined' ), true ) ) {
											$status_filter_group = 'refunded';
											$status_bg = '#fee2e2';
											$status_color = '#b91c1c';
											$status_label = ucfirst( $status_raw );
										}

										$user_name = fed_get_data( 'display_name', $txn, fed_get_data( 'user_login', $txn, 'User #' . fed_get_data( 'user_id', $txn ) ) );
										$user_email = fed_get_data( 'user_email', $txn, '' );
										$txn_id = fed_get_data( 'transaction_id', $txn, 'TXN-' . fed_get_data( 'id', $txn ) );
										$gateway = fed_get_data( 'payment_source', $txn, 'PayPal' );
										$amount = floatval( fed_get_data( 'amount', $txn, 0 ) );
										$currency = fed_get_data( 'currency', $txn, 'USD' );
										$created = fed_get_data( 'created', $txn, '-' );
										$db_id = fed_get_data( 'id', $txn );
										?>
										<tr class="fed_txn_row" data-status="<?php echo esc_attr( $status_filter_group ); ?>" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
											
											<!-- Customer Column -->
											<td style="padding: 14px 18px;">
												<div style="display: flex; align-items: center; gap: 10px;">
													<div style="width: 34px; height: 34px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
														<?php echo esc_html( strtoupper( substr( $user_name, 0, 1 ) ) ); ?>
													</div>
													<div>
														<div style="font-weight: 700; color: #0f172a;"><?php echo esc_html( $user_name ); ?></div>
														<div style="font-size: 12px; color: #64748b;"><?php echo esc_html( $user_email ); ?></div>
													</div>
												</div>
											</td>

											<!-- Transaction ID Column -->
											<td style="padding: 14px 18px;">
												<div style="font-family: monospace; font-size: 13px; font-weight: 600; color: #1e293b;">
													<?php echo esc_html( $txn_id ); ?>
												</div>
												<div style="font-size: 11.5px; color: #94a3b8;">
													<?php echo esc_html( fed_get_data( 'payment_type', $txn, 'Standard Checkout' ) ); ?>
												</div>
											</td>

											<!-- Gateway Column -->
											<td style="padding: 14px 18px;">
												<span style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #334155;">
													<i class="fas fa-credit-card" style="color: #64748b; font-size: 11px;"></i>
													<?php echo esc_html( $gateway ); ?>
												</span>
											</td>

											<!-- Amount Column -->
											<td style="padding: 14px 18px;">
												<div style="font-weight: 800; font-size: 14.5px; color: #0f172a;">
													<?php echo esc_html( $currency . ' ' . number_format( $amount, 2 ) ); ?>
												</div>
											</td>

											<!-- Status Column -->
											<td style="padding: 14px 18px;">
												<span style="display: inline-flex; align-items: center; gap: 6px; background: <?php echo esc_attr( $status_bg ); ?>; color: <?php echo esc_attr( $status_color ); ?>; padding: 4px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
													<span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
													<?php echo esc_html( $status_label ); ?>
												</span>
											</td>

											<!-- Date Column -->
											<td style="padding: 14px 18px;">
												<div style="font-weight: 600; color: #1e293b; font-size: 13px;">
													<?php echo esc_html( date( 'M d, Y', strtotime( $created ) ) ); ?>
												</div>
												<div style="font-size: 11.5px; color: #94a3b8;">
													<?php echo esc_html( date( 'h:i A', strtotime( $created ) ) ); ?>
												</div>
											</td>

											<!-- Actions Column -->
											<td style="padding: 14px 18px; text-align: right;">
												<div style="display: inline-flex; align-items: center; gap: 6px;">
													<button type="button" class="fed_view_txn_btn" data-id="<?php echo esc_attr( $db_id ); ?>" title="<?php esc_attr_e( 'View Details', 'frontend-dashboard' ); ?>" style="background: #ffffff; border: 1px solid #cbd5e1; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: #475569; cursor: pointer; transition: all 0.15s ease;">
														<i class="fas fa-eye" style="font-size: 12px;"></i>
													</button>
												</div>
											</td>

										</tr>
										<?php
									}
								} else {
									?>
									<tr>
										<td colspan="7" style="padding: 40px; text-align: center; color: #64748b;">
											<i class="fas fa-receipt" style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
											<div style="font-size: 15px; font-weight: 700; color: #334155;"><?php esc_html_e( 'No Transactions Recorded Yet', 'frontend-dashboard' ); ?></div>
											<div style="font-size: 13px; color: #64748b; margin-top: 4px;"><?php esc_html_e( 'Transactions processed through connected gateways or recorded manually will appear here.', 'frontend-dashboard' ); ?></div>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Add Manual Transaction Modal -->
				<div id="fed_add_txn_modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
					<div style="background: #ffffff; border-radius: 14px; max-width: 560px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: fedFadeIn 0.2s ease;">
						
						<!-- Modal Header -->
						<div style="background: #033333; color: #ffffff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
							<h4 style="margin: 0; font-size: 16px; font-weight: 700; color: #ffffff; display: flex; align-items: center; gap: 8px;">
								<i class="fas fa-receipt" style="color: #34d399;"></i>
								<span><?php esc_html_e( 'Record Manual Transaction', 'frontend-dashboard' ); ?></span>
							</h4>
							<button type="button" id="fed_close_add_txn_modal" style="background: none; border: none; color: #ffffff; font-size: 20px; cursor: pointer; line-height: 1;">&times;</button>
						</div>

						<!-- Modal Form -->
						<form method="post" class="fed_ajax" action="<?php echo esc_url( add_query_arg( array( 'fed_action_hook' => 'FEDTransaction@save_manual_transaction' ), fed_get_ajax_form_action( 'fed_ajax_request' ) ) ); ?>" style="padding: 24px;">
							<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

							<div style="display: flex; flex-direction: column; gap: 16px;">
								
								<!-- Customer Selection -->
								<div>
									<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Customer / User', 'frontend-dashboard' ); ?> *</label>
									<select name="user_id" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px; background: #ffffff;">
										<?php
										$users = get_users( array( 'number' => 100, 'orderby' => 'display_name' ) );
										if ( ! empty( $users ) ) {
											foreach ( $users as $u ) {
												$selected = ( $u->ID === get_current_user_id() ) ? 'selected' : '';
												echo '<option value="' . esc_attr( $u->ID ) . '" ' . $selected . '>' . esc_html( $u->display_name . ' (' . $u->user_email . ')' ) . '</option>';
											}
										}
										?>
									</select>
								</div>

								<!-- Transaction ID & Gateway -->
								<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Transaction ID', 'frontend-dashboard' ); ?> *</label>
										<input type="text" name="transaction_id" required value="TXN-<?php echo esc_attr( strtoupper( wp_generate_password( 8, false ) ) ); ?>" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px; font-family: monospace;" />
									</div>
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Payment Gateway / Source', 'frontend-dashboard' ); ?> *</label>
										<select name="payment_source" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
											<option value="PayPal">PayPal</option>
											<option value="Stripe">Stripe</option>
											<option value="Bank Transfer"><?php esc_html_e( 'Direct Bank Transfer', 'frontend-dashboard' ); ?></option>
											<option value="Cash"><?php esc_html_e( 'Cash / Offline', 'frontend-dashboard' ); ?></option>
											<option value="Manual"><?php esc_html_e( 'Manual Entry', 'frontend-dashboard' ); ?></option>
										</select>
									</div>
								</div>

								<!-- Amount & Currency -->
								<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 14px;">
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Amount', 'frontend-dashboard' ); ?> *</label>
										<input type="number" step="0.01" name="amount" required placeholder="49.00" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;" />
									</div>
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Currency', 'frontend-dashboard' ); ?></label>
										<select name="currency" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
											<option value="USD">USD ($)</option>
											<option value="EUR">EUR (€)</option>
											<option value="GBP">GBP (£)</option>
											<option value="CAD">CAD ($)</option>
											<option value="AUD">AUD ($)</option>
											<option value="INR">INR (₹)</option>
										</select>
									</div>
								</div>

								<!-- Payment Type & Status -->
								<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Payment Type', 'frontend-dashboard' ); ?></label>
										<select name="payment_type" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
											<option value="Standard Checkout"><?php esc_html_e( 'Standard Checkout', 'frontend-dashboard' ); ?></option>
											<option value="Subscription"><?php esc_html_e( 'Subscription / Membership', 'frontend-dashboard' ); ?></option>
											<option value="Digital Product"><?php esc_html_e( 'Digital Product', 'frontend-dashboard' ); ?></option>
											<option value="Invoice Settlement"><?php esc_html_e( 'Invoice Settlement', 'frontend-dashboard' ); ?></option>
										</select>
									</div>
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></label>
										<select name="status" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
											<option value="completed"><?php esc_html_e( 'Completed', 'frontend-dashboard' ); ?></option>
											<option value="pending"><?php esc_html_e( 'Pending', 'frontend-dashboard' ); ?></option>
											<option value="refunded"><?php esc_html_e( 'Refunded', 'frontend-dashboard' ); ?></option>
										</select>
									</div>
								</div>

							</div>

							<div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
								<button type="button" id="fed_cancel_add_txn_modal" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;"><?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?></button>
								<button type="submit" style="background: #033333; border: 1px solid #033333; padding: 9px 22px; border-radius: 8px; font-size: 13px; font-weight: 700; color: #ffffff; cursor: pointer; box-shadow: 0 2px 6px rgba(3,51,51,0.2);"><?php esc_html_e( 'Save Transaction', 'frontend-dashboard' ); ?></button>
							</div>

						</form>
					</div>
				</div>

				<!-- Live Search and Status Filter Script -->
				<script type="text/javascript">
				jQuery(document).ready(function($){
					// Open Add Transaction Modal
					$('#fed_open_add_txn_btn').on('click', function(){
						$('#fed_add_txn_modal').css('display', 'flex');
					});

					// Close Modal
					$('#fed_close_add_txn_modal, #fed_cancel_add_txn_modal').on('click', function(){
						$('#fed_add_txn_modal').hide();
					});

					// Instant Search
					$('#fed_transaction_search').on('input', function(){
						var q = $(this).val().toLowerCase().trim();
						$('.fed_txn_row').each(function(){
							var text = $(this).text().toLowerCase();
							$(this).toggle( !q || text.indexOf(q) > -1 );
						});
					});

					// Status Filter Pills
					$('.fed_txn_filter_btn').on('click', function(e){
						e.preventDefault();
						$('.fed_txn_filter_btn').removeClass('active').css({'background':'transparent', 'color':'#64748b', 'font-weight':'600', 'box-shadow':'none'});
						$(this).addClass('active').css({'background':'#ffffff', 'color':'#0f172a', 'font-weight':'700', 'box-shadow':'0 1px 2px rgba(0,0,0,0.05)'});
						
						var filter = $(this).data('filter');
						$('.fed_txn_row').each(function(){
							if ( filter === 'all' || $(this).data('status') === filter ) {
								$(this).show();
							} else {
								$(this).hide();
							}
						});
					});
				});
				</script>

			</div>
			<?php
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
		 * Save Manual Transaction via AJAX.
		 *
		 * @param array $request
		 */
		public function save_manual_transaction( $request ) {
			$this->authorize();

			if ( ! fed_is_admin() ) {
				wp_send_json_error( array( 'message' => __( 'Permission denied.', 'frontend-dashboard' ) ) );
			}

			$user_id        = isset( $request['user_id'] ) ? intval( $request['user_id'] ) : get_current_user_id();
			$transaction_id = isset( $request['transaction_id'] ) ? fed_sanitize_text_field( $request['transaction_id'] ) : 'TXN-' . time();
			$payment_source = isset( $request['payment_source'] ) ? fed_sanitize_text_field( $request['payment_source'] ) : 'Manual';
			$payment_type   = isset( $request['payment_type'] ) ? fed_sanitize_text_field( $request['payment_type'] ) : 'Standard Checkout';
			$amount         = isset( $request['amount'] ) ? floatval( $request['amount'] ) : 0.00;
			$currency       = isset( $request['currency'] ) ? fed_sanitize_text_field( $request['currency'] ) : 'USD';
			$status         = isset( $request['status'] ) ? fed_sanitize_text_field( $request['status'] ) : 'completed';

			if ( empty( $transaction_id ) || $amount <= 0 ) {
				wp_send_json_error( array( 'message' => __( 'Please provide a valid Transaction ID and positive Amount.', 'frontend-dashboard' ) ) );
			}

			$data = array(
				'user_id'        => $user_id,
				'payment_type'   => $payment_type,
				'payment_source' => $payment_source,
				'transaction_id' => $transaction_id,
				'amount'         => $amount,
				'currency'       => $currency,
				'status'         => $status,
				'created'        => current_time( 'mysql' ),
			);

			$inserted = fed_insert_new_row( BC_FED_TABLE_PAYMENT, $data );

			if ( $inserted ) {
				wp_send_json_success( array( 'message' => __( 'Transaction recorded successfully!', 'frontend-dashboard' ) ) );
			}

			wp_send_json_error( array( 'message' => __( 'Failed to record transaction. Please try again.', 'frontend-dashboard' ) ) );
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
