<?php
/**
 * Payment Executive Dashboard View & Analytics.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDPaymentDashboard' ) ) {
	/**
	 * Class FEDPaymentDashboard
	 */
	class FEDPaymentDashboard {

		/**
		 * Dashboard Main View.
		 */
		public function dashboard() {
			$this->authorize();

			$metrics = function_exists( 'fed_get_payment_metrics' ) ? fed_get_payment_metrics() : array(
				'total_revenue'        => 0,
				'total_transactions'   => 0,
				'completed_txns'       => 0,
				'pending_txns'         => 0,
				'refunded_txns'        => 0,
				'active_subscriptions' => 0,
				'connected_gateways'   => 1,
				'currency_symbol'      => '$',
			);

			$transactions  = function_exists( 'fed_get_transactions' ) ? fed_get_transactions() : array();
			$recent_txns   = ! empty( $transactions ) ? array_slice( $transactions, 0, 5 ) : array();
			$gateways      = function_exists( 'fed_get_registered_gateways' ) ? fed_get_registered_gateways() : array();
			$settings      = get_option( 'fed_payment_settings', array() );
			$currency_code = isset( $settings['settings']['currency'] ) ? $settings['settings']['currency'] : 'USD';
			$is_sandbox    = isset( $settings['settings']['test_mode'] ) && 'enable' === $settings['settings']['test_mode'];

			$success_rate = 100;
			if ( ! empty( $metrics['total_transactions'] ) ) {
				$success_rate = round( ( $metrics['completed_txns'] / $metrics['total_transactions'] ) * 100, 1 );
			}
			?>
			<div class="bc_fed fed_dashboard_view_wrapper" style="font-family: inherit;">
				
				<!-- Financial KPI Overview (5 Modern Cards) -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; margin-bottom: 24px;">
					
					<!-- Metric 1: Gross Revenue -->
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
						<div>
							<div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
								<?php esc_html_e( 'Gross Revenue', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
								<?php echo esc_html( $metrics['currency_symbol'] . number_format( floatval( $metrics['total_revenue'] ), 2 ) ); ?>
							</div>
							<div style="font-size: 11.5px; color: #10b981; font-weight: 600; margin-top: 2px;">
								<i class="fas fa-arrow-up"></i> <?php esc_html_e( 'Lifetime volume', 'frontend-dashboard' ); ?>
							</div>
						</div>
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px;">
							<i class="fas fa-wallet"></i>
						</div>
					</div>

					<!-- Metric 2: Total Transactions -->
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
						<div>
							<div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
								<?php esc_html_e( 'Total Transactions', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
								<?php echo esc_html( number_format( intval( $metrics['total_transactions'] ) ) ); ?>
							</div>
							<div style="font-size: 11.5px; color: #64748b; font-weight: 500; margin-top: 2px;">
								<?php
								/* translators: %d: Number of completed transactions */
								printf( esc_html__( '%d completed', 'frontend-dashboard' ), intval( $metrics['completed_txns'] ) );
								?>
							</div>
						</div>
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 18px;">
							<i class="fas fa-exchange-alt"></i>
						</div>
					</div>

					<!-- Metric 3: Active Subscriptions -->
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
						<div>
							<div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
								<?php esc_html_e( 'Active Subscriptions', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
								<?php echo esc_html( number_format( intval( $metrics['active_subscriptions'] ) ) ); ?>
							</div>
							<div style="font-size: 11.5px; color: #16a34a; font-weight: 600; margin-top: 2px;">
								<i class="fas fa-sync"></i> <?php esc_html_e( 'Recurring members', 'frontend-dashboard' ); ?>
							</div>
						</div>
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #f0fdf4; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 18px;">
							<i class="fas fa-users"></i>
						</div>
					</div>

					<!-- Metric 4: Success Rate -->
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
						<div>
							<div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
								<?php esc_html_e( 'Success Rate', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
								<?php echo esc_html( $success_rate ); ?>%
							</div>
							<div style="font-size: 11.5px; color: #64748b; font-weight: 500; margin-top: 2px;">
								<?php
								/* translators: %d: Number of pending transactions */
								printf( esc_html__( '%d pending / review', 'frontend-dashboard' ), intval( $metrics['pending_txns'] ) );
								?>
							</div>
						</div>
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #fefce8; color: #ca8a04; display: flex; align-items: center; justify-content: center; font-size: 18px;">
							<i class="fas fa-check-double"></i>
						</div>
					</div>

					<!-- Metric 5: Active Gateways -->
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
						<div>
							<div style="font-size: 11.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">
								<?php esc_html_e( 'Connected Gateways', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 24px; font-weight: 800; color: #0f172a; margin-top: 4px;">
								<?php echo esc_html( intval( $metrics['connected_gateways'] ) ); ?>
							</div>
							<div style="font-size: 11.5px; color: <?php echo $is_sandbox ? '#f59e0b' : '#10b981'; ?>; font-weight: 600; margin-top: 2px;">
								<?php echo $is_sandbox ? esc_html__( 'Sandbox mode', 'frontend-dashboard' ) : esc_html__( 'Live production', 'frontend-dashboard' ); ?>
							</div>
						</div>
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #faf5ff; color: #9333ea; display: flex; align-items: center; justify-content: center; font-size: 18px;">
							<i class="fas fa-shield-alt"></i>
						</div>
					</div>

				</div>

				<!-- 2-Column Responsive Dashboard Layout -->
				<div class="row">
					
					<!-- Left Column: Recent Activity & Subscriptions Snapshot -->
					<div class="col-md-8" style="margin-bottom: 20px;">
						
						<!-- Card: Recent Transactions Table -->
						<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 20px;">
							
							<div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center;">
								<h3 style="margin: 0; font-size: 14.5px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
									<i class="fas fa-receipt" style="color: #033333;"></i>
									<span><?php esc_html_e( 'Recent Transactions', 'frontend-dashboard' ); ?></span>
								</h3>
								<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => 'transactions' ) ) ); ?>" style="font-size: 12.5px; font-weight: 700; color: #033333; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
									<?php esc_html_e( 'View All Transactions', 'frontend-dashboard' ); ?> <i class="fas fa-arrow-right" style="font-size: 10px;"></i>
								</a>
							</div>

							<div style="overflow-x: auto;">
								<table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
									<thead>
										<tr style="background: #ffffff; border-bottom: 1px solid #f1f5f9; color: #64748b; font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
											<th style="padding: 12px 18px;"><?php esc_html_e( 'Customer', 'frontend-dashboard' ); ?></th>
											<th style="padding: 12px 18px;"><?php esc_html_e( 'ID', 'frontend-dashboard' ); ?></th>
											<th style="padding: 12px 18px;"><?php esc_html_e( 'Gateway', 'frontend-dashboard' ); ?></th>
											<th style="padding: 12px 18px;"><?php esc_html_e( 'Amount', 'frontend-dashboard' ); ?></th>
											<th style="padding: 12px 18px;"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
											<th style="padding: 12px 18px;"><?php esc_html_e( 'Date', 'frontend-dashboard' ); ?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										if ( ! empty( $recent_txns ) ) {
											foreach ( $recent_txns as $txn ) {
												$status_raw   = strtolower( fed_get_data( 'status', $txn, 'pending' ) );
												$status_bg    = '#fef3c7';
												$status_color = '#b45309';
												$status_label = __( 'Pending', 'frontend-dashboard' );

												if ( in_array( $status_raw, array( 'completed', 'paid', 'success', 'succeeded', 'active' ), true ) ) {
													$status_bg    = '#dcfce7';
													$status_color = '#15803d';
													$status_label = __( 'Completed', 'frontend-dashboard' );
												} elseif ( in_array( $status_raw, array( 'refunded', 'cancelled', 'failed' ), true ) ) {
													$status_bg    = '#fee2e2';
													$status_color = '#b91c1c';
													$status_label = ucfirst( $status_raw );
												}

												$user_name = fed_get_data( 'display_name', $txn, fed_get_data( 'user_login', $txn, 'User #' . fed_get_data( 'user_id', $txn ) ) );
												$txn_id    = fed_get_data( 'transaction_id', $txn, 'TXN-' . fed_get_data( 'id', $txn ) );
												$gateway   = fed_get_data( 'payment_source', $txn, 'PayPal' );
												$amount    = floatval( fed_get_data( 'amount', $txn, 0 ) );
												$currency  = fed_get_data( 'currency', $txn, 'USD' );
												$created   = fed_get_data( 'created', $txn, '-' );
												?>
												<tr style="border-bottom: 1px solid #f1f5f9;">
													<td style="padding: 12px 18px;">
														<div style="font-weight: 700; color: #0f172a;"><?php echo esc_html( $user_name ); ?></div>
													</td>
													<td style="padding: 12px 18px; font-family: monospace; font-size: 12px; color: #64748b;">
														<?php echo esc_html( $txn_id ); ?>
													</td>
													<td style="padding: 12px 18px;">
														<span style="display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; padding: 3px 8px; border-radius: 5px; font-size: 11.5px; font-weight: 600; color: #475569;">
															<?php echo esc_html( $gateway ); ?>
														</span>
													</td>
													<td style="padding: 12px 18px; font-weight: 800; color: #0f172a;">
														<?php echo esc_html( $currency . ' ' . number_format( $amount, 2 ) ); ?>
													</td>
													<td style="padding: 12px 18px;">
														<span style="display: inline-flex; align-items: center; gap: 5px; background: <?php echo esc_attr( $status_bg ); ?>; color: <?php echo esc_attr( $status_color ); ?>; padding: 3px 8px; border-radius: 9999px; font-size: 11px; font-weight: 700;">
															<span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
															<?php echo esc_html( $status_label ); ?>
														</span>
													</td>
													<td style="padding: 12px 18px; font-size: 12px; color: #64748b;">
														<?php echo esc_html( gmdate( 'M d, Y', strtotime( $created ) ) ); ?>
													</td>
												</tr>
												<?php
											}
										} else {
											?>
											<tr>
												<td colspan="6" style="padding: 28px; text-align: center; color: #64748b;">
													<i class="fas fa-receipt" style="font-size: 24px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
													<div style="font-weight: 600; color: #334155;"><?php esc_html_e( 'No recent transactions recorded.', 'frontend-dashboard' ); ?></div>
												</td>
											</tr>
											<?php
										}
										?>
									</tbody>
								</table>
							</div>

						</div>

						<!-- Card: Recurring Billing & Subscriptions Snapshot -->
						<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
							<div style="display: flex; align-items: center; gap: 14px;">
								<div style="width: 44px; height: 44px; border-radius: 10px; background: #033333; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 18px;">
									<i class="fas fa-sync-alt"></i>
								</div>
								<div>
									<h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">
										<?php esc_html_e( 'Subscriptions & Membership Engine', 'frontend-dashboard' ); ?>
									</h4>
									<p style="margin: 3px 0 0 0; font-size: 12.5px; color: #64748b;">
										<?php esc_html_e( 'Automate recurring renewals, tier upgrades, and customer member access privileges.', 'frontend-dashboard' ); ?>
									</p>
								</div>
							</div>
							<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => 'subscriptions' ) ) ); ?>" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #334155; font-size: 13px; font-weight: 700; padding: 8px 16px; border-radius: 8px; text-decoration: none;">
								<?php esc_html_e( 'Manage Subscriptions', 'frontend-dashboard' ); ?>
							</a>
						</div>

					</div>

					<!-- Right Column: Gateways Status, Quick Actions & Shortcuts -->
					<div class="col-md-4">
						
						<!-- Card: Connected Gateways Status -->
						<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden; margin-bottom: 20px;">
							<div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center;">
								<h4 style="margin: 0; font-size: 14px; font-weight: 700; color: #0f172a;">
									<i class="fas fa-credit-card" style="color: #033333; margin-right: 6px;"></i> <?php esc_html_e( 'Gateways Hub', 'frontend-dashboard' ); ?>
								</h4>
								<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => 'gateways' ) ) ); ?>" style="font-size: 12px; font-weight: 700; color: #033333; text-decoration: none;">
									<?php esc_html_e( 'View Hub', 'frontend-dashboard' ); ?> &rarr;
								</a>
							</div>
							<div style="padding: 14px 18px; display: flex; flex-direction: column; gap: 10px;">
								<?php
								$shown_gateways = array_slice( $gateways, 0, 4 );
								foreach ( $shown_gateways as $g ) :
									$is_act  = ! empty( $g['is_active'] );
									$is_inst = ! empty( $g['is_installed'] );
									?>
									<div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #f8fafc; border-radius: 8px;">
										<div style="display: flex; align-items: center; gap: 8px;">
											<i class="<?php echo esc_attr( $g['icon'] ); ?>" style="color: <?php echo esc_attr( $g['color'] ); ?>; width: 18px; font-size: 14px;"></i>
											<span style="font-size: 13px; font-weight: 600; color: #1e293b;"><?php echo esc_html( $g['name'] ); ?></span>
										</div>
										<span style="font-size: 11px; font-weight: 700; color: <?php echo $is_act ? '#16a34a' : ( $is_inst ? '#64748b' : '#635bff' ); ?>;">
											<?php echo $is_act ? esc_html__( 'Active', 'frontend-dashboard' ) : ( $is_inst ? esc_html__( 'Ready', 'frontend-dashboard' ) : esc_html__( 'Pro', 'frontend-dashboard' ) ); ?>
										</span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>

						<!-- Card: Quick Financial Shortcuts -->
						<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
							<h4 style="margin: 0 0 12px 0; font-size: 13.5px; font-weight: 700; color: #0f172a;">
								<i class="fas fa-bolt" style="color: #f59e0b; margin-right: 6px;"></i> <?php esc_html_e( 'Quick Operations', 'frontend-dashboard' ); ?>
							</h4>
							<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px;">
								<li>
									<a href="
									<?php
									echo esc_url(
										fed_menu_page_url(
											'fed_payments',
											array(
												'menu'    => 'transactions',
												'submenu' => 'FEDTransaction@add_new_transaction',
											)
										)
									);
									?>
												" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: background 0.15s ease;">
										<i class="fas fa-plus-circle" style="color: #10b981;"></i>
										<span><?php esc_html_e( 'Record Manual Transaction', 'frontend-dashboard' ); ?></span>
									</a>
								</li>
								<li>
									<a href="
									<?php
									echo esc_url(
										fed_menu_page_url(
											'fed_payments',
											array(
												'menu'    => 'subscriptions',
												'submenu' => 'FEDSubscription@plans',
											)
										)
									);
									?>
												" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: background 0.15s ease;">
										<i class="fas fa-tags" style="color: #0284c7;"></i>
										<span><?php esc_html_e( 'Configure Subscription Plans', 'frontend-dashboard' ); ?></span>
									</a>
								</li>
								<li>
									<a href="
									<?php
									echo esc_url(
										fed_menu_page_url(
											'fed_payments',
											array(
												'menu'    => 'gateways',
												'submenu' => 'FEDPayment@settings',
											)
										)
									);
									?>
												" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: background 0.15s ease;">
										<i class="fas fa-sliders-h" style="color: #64748b;"></i>
										<span><?php esc_html_e( 'Global Currency & Mode Settings', 'frontend-dashboard' ); ?></span>
									</a>
								</li>
								<li>
									<a href="
									<?php
									echo esc_url(
										fed_menu_page_url(
											'fed_payments',
											array(
												'menu'    => 'invoice',
												'submenu' => 'FEDInvoice@details',
											)
										)
									);
									?>
									" style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: background 0.15s ease;">
										<i class="fas fa-file-invoice" style="color: #8b5cf6;"></i>
										<span><?php esc_html_e( 'Company Invoice & Receipts', 'frontend-dashboard' ); ?></span>
									</a>
								</li>
							</ul>
						</div>

					</div>

				</div>

			</div>
			<?php
		}

		/**
		 * Authorize.
		 */
		public function authorize() {
			if ( ! fed_is_admin() ) {
				wp_die( esc_html__( 'Sorry! You are not allowed to do this action | Error: FEDPaymentDashboard@authorize', 'frontend-dashboard' ) );
			}
		}
	}

	new FEDPaymentDashboard();
}