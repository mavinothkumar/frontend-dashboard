<?php
/**
 * Subscriptions Management Controller & Views.
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDSubscription' ) ) {
	/**
	 * Class FEDSubscription
	 */
	class FEDSubscription {

		/**
		 * Subscriptions list view (Dedicated Page).
		 */
		public function subscriptions() {
			$this->authorize();
			$subscriptions = fed_get_subscriptions();
			?>
			<div class="bc_fed fed_subscriptions_container" style="font-family: inherit;">
				
				<!-- Heading with Action Buttons -->
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px; flex-wrap: wrap; gap: 14px;">
					<div>
						<h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
							<i class="fas fa-sync-alt" style="color: #033333;"></i>
							<span><?php esc_html_e( 'All Subscriptions', 'frontend-dashboard' ); ?></span>
						</h3>
						<p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
							<?php esc_html_e( 'Complete ledger of customer recurring memberships, billing cycles, subscriber statuses, and renewal dates.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
						<!-- Navigate to Subscription Plans Page -->
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
									" 
							style="display: inline-flex; align-items: center; gap: 7px; background: #ffffff; color: #033333; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.04); transition: all 0.15s ease;">
							<i class="fas fa-cubes"></i> 
							<span><?php esc_html_e( 'Subscription Plans', 'frontend-dashboard' ); ?></span>
						</a>

						<!-- Navigate to Add New Plan on Plans Page -->
						<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'menu'    => 'subscriptions',
									'submenu' => 'FEDSubscription@plans',
									'open'    => 'new',
								),
								fed_menu_page_url( 'fed_payments' )
							)
						);
						?>
									" 
							style="display: inline-flex; align-items: center; gap: 7px; background: #033333; color: #ffffff; border: 1px solid #033333; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(3,51,51,0.2);">
							<i class="fas fa-plus"></i> <?php esc_html_e( 'New Subscription Plan', 'frontend-dashboard' ); ?>
						</a>
					</div>
				</div>

				<!-- Action Toolbar: Search, Status Filter Pills & Export -->
				<div style="display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 20px; flex-wrap: wrap;">
					
					<!-- Left: Search Box -->
					<div style="position: relative; flex: 1; min-width: 240px; max-width: 380px;">
						<i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px; pointer-events: none;"></i>
						<input type="text" id="fed_subscription_search" placeholder="<?php esc_attr_e( 'Search subscribers, plans, IDs...', 'frontend-dashboard' ); ?>" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 14px 8px 36px; font-size: 13.5px; color: #1e293b; background: #ffffff; outline: none; box-shadow: 0 1px 2px rgba(0,0,0,0.02);" />
					</div>

					<!-- Right: Filter Pills & Export -->
					<div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
						<div class="fed_sub_filter_group" style="display: inline-flex; background: #f1f5f9; padding: 3px; border-radius: 8px; border: 1px solid #e2e8f0;">
							<button type="button" class="fed_sub_filter_btn active" data-filter="all" style="background: #ffffff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; color: #0f172a; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
								<?php esc_html_e( 'All', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_sub_filter_btn" data-filter="active" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Active', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_sub_filter_btn" data-filter="past_due" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Past Due', 'frontend-dashboard' ); ?>
							</button>
							<button type="button" class="fed_sub_filter_btn" data-filter="cancelled" style="background: transparent; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #64748b; cursor: pointer;">
								<?php esc_html_e( 'Cancelled', 'frontend-dashboard' ); ?>
							</button>
						</div>

						<button type="button" onclick="alert('Exporting recurring subscription registry...');" style="display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer;">
							<i class="fas fa-file-export" style="color: #64748b;"></i> <?php esc_html_e( 'Export CSV', 'frontend-dashboard' ); ?>
						</button>
					</div>

				</div>

				<!-- Enterprise Subscriptions Data Table (100% Full Width) -->
				<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); width: 100%;">
					<div style="overflow-x: auto;">
						<table class="fed_enterprise_table" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13.5px;">
							<thead>
								<tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;">
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Subscriber', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Plan & Tier', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Amount / Cycle', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Payment Gateway', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Status', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px;"><?php esc_html_e( 'Next Renewal', 'frontend-dashboard' ); ?></th>
									<th style="padding: 14px 18px; text-align: right;"><?php esc_html_e( 'Actions', 'frontend-dashboard' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( ! empty( $subscriptions ) ) {
									foreach ( $subscriptions as $sub ) {
										$status       = strtolower( fed_get_data( 'status', $sub, 'active' ) );
										$status_bg    = '#dcfce7';
										$status_color = '#15803d';
										$status_label = __( 'Active', 'frontend-dashboard' );

										if ( 'past_due' === $status ) {
											$status_bg    = '#fef3c7';
											$status_color = '#b45309';
											$status_label = __( 'Past Due', 'frontend-dashboard' );
										} elseif ( in_array( $status, array( 'cancelled', 'expired', 'failed' ), true ) ) {
											$status_bg    = '#fee2e2';
											$status_color = '#b91c1c';
											$status_label = __( 'Cancelled', 'frontend-dashboard' );
										}
										?>
										<tr class="fed_sub_row" data-status="<?php echo esc_attr( $status ); ?>" style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;">
											<!-- Subscriber Column -->
											<td style="padding: 14px 18px;">
												<div style="display: flex; align-items: center; gap: 10px;">
													<div style="width: 34px; height: 34px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
														<?php echo esc_html( strtoupper( substr( fed_get_data( 'user_name', $sub, 'U' ), 0, 1 ) ) ); ?>
													</div>
													<div>
														<div style="font-weight: 700; color: #0f172a;"><?php echo esc_html( fed_get_data( 'user_name', $sub, '' ) ); ?></div>
														<div style="font-size: 12px; color: #64748b;"><?php echo esc_html( fed_get_data( 'user_email', $sub, '' ) ); ?></div>
													</div>
												</div>
											</td>

											<!-- Plan Column -->
											<td style="padding: 14px 18px;">
												<div style="font-weight: 600; color: #1e293b;"><?php echo esc_html( fed_get_data( 'plan_name', $sub, 'Standard Plan' ) ); ?></div>
												<div style="font-size: 11.5px; font-family: monospace; color: #94a3b8;"><?php echo esc_html( fed_get_data( 'id', $sub, '' ) ); ?></div>
											</td>

											<!-- Amount Column -->
											<td style="padding: 14px 18px;">
												<div style="font-weight: 700; color: #0f172a;">
													$<?php echo esc_html( number_format( floatval( fed_get_data( 'amount', $sub, 0 ) ), 2 ) ); ?>
												</div>
												<div style="font-size: 11.5px; color: #64748b;">
													<?php
													/* translators: %s: Billing cycle interval */
													printf( esc_html__( 'per %s', 'frontend-dashboard' ), esc_html( strtolower( fed_get_data( 'billing_cycle', $sub, 'Month' ) ) ) );
													?>
												</div>
											</td>

											<!-- Gateway Column -->
											<td style="padding: 14px 18px;">
												<span style="display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; color: #334155;">
													<i class="fas fa-credit-card" style="color: #64748b; font-size: 11px;"></i>
													<?php echo esc_html( fed_get_data( 'gateway', $sub, 'PayPal' ) ); ?>
												</span>
											</td>

											<!-- Status Column -->
											<td style="padding: 14px 18px;">
												<span style="display: inline-flex; align-items: center; gap: 6px; background: <?php echo esc_attr( $status_bg ); ?>; color: <?php echo esc_attr( $status_color ); ?>; padding: 4px 10px; border-radius: 9999px; font-size: 11.5px; font-weight: 700;">
													<span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
													<?php echo esc_html( $status_label ); ?>
												</span>
											</td>

											<!-- Renewal Date Column -->
											<td style="padding: 14px 18px;">
												<div style="font-weight: 600; color: #1e293b;"><?php echo esc_html( fed_get_data( 'renewal_date', $sub, '-' ) ); ?></div>
												<div style="font-size: 11.5px; color: #94a3b8;">
													<?php
													/* translators: %s: Subscription start date */
													printf( esc_html__( 'Started %s', 'frontend-dashboard' ), esc_html( fed_get_data( 'start_date', $sub, '-' ) ) );
													?>
												</div>
											</td>

											<!-- Actions Column -->
											<td style="padding: 14px 18px; text-align: right;">
												<div style="display: inline-flex; align-items: center; gap: 6px;">
													<button type="button" title="<?php esc_attr_e( 'View Details', 'frontend-dashboard' ); ?>" style="background: #ffffff; border: 1px solid #cbd5e1; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: #475569; cursor: pointer;">
														<i class="fas fa-eye" style="font-size: 12px;"></i>
													</button>
													<button type="button" title="<?php esc_attr_e( 'Cancel Subscription', 'frontend-dashboard' ); ?>" style="background: #fee2e2; border: 1px solid #fecaca; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; color: #ef4444; cursor: pointer;">
														<i class="fas fa-ban" style="font-size: 12px;"></i>
													</button>
												</div>
											</td>
										</tr>
										<?php
									}
								} else {
									?>
									<tr>
										<td colspan="7" style="padding: 36px; text-align: center; color: #64748b;">
											<i class="fas fa-sync-alt" style="font-size: 32px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
											<div style="font-size: 15px; font-weight: 700; color: #334155;"><?php esc_html_e( 'No Recurring Subscriptions Found', 'frontend-dashboard' ); ?></div>
											<div style="font-size: 13px; color: #64748b; margin-top: 4px;"><?php esc_html_e( 'Recurring memberships and subscriptions will appear here automatically when created.', 'frontend-dashboard' ); ?></div>
										</td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Live Search and Filter Script -->
				<script type="text/javascript">
				jQuery(document).ready(function($){
					// Instant Search
					$('#fed_subscription_search').on('input', function(){
						var q = $(this).val().toLowerCase().trim();
						$('.fed_sub_row').each(function(){
							var text = $(this).text().toLowerCase();
							$(this).toggle( !q || text.indexOf(q) > -1 );
						});
					});

					// Status Filter
					$('.fed_sub_filter_btn').on('click', function(e){
						e.preventDefault();
						$('.fed_sub_filter_btn').removeClass('active').css({'background':'transparent', 'color':'#64748b', 'font-weight':'600', 'box-shadow':'none'});
						$(this).addClass('active').css({'background':'#ffffff', 'color':'#0f172a', 'font-weight':'700', 'box-shadow':'0 1px 2px rgba(0,0,0,0.05)'});
						
						var filter = $(this).data('filter');
						$('.fed_sub_row').each(function(){
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
		 * Subscription Plans & Packages View.
		 */
		public function plans() {
			$this->authorize();

			// Load custom saved plans or default directory
			$custom_plans = get_option( 'fed_subscription_plans_custom', null );
			if ( ! is_array( $custom_plans ) || empty( $custom_plans ) ) {
				$custom_plans = array(
					'starter'    => array(
						'id'        => 'starter',
						'name'      => __( 'Starter Membership', 'frontend-dashboard' ),
						'price'     => '19.00',
						'cycle'     => 'Monthly',
						'subs'      => 12,
						'user_role' => 'subscriber',
						'popular'   => false,
						'features'  => "Standard User Dashboard\nCustom Profile Fields\nCommunity Access",
						'color'     => '#0284c7',
					),
					'pro'        => array(
						'id'        => 'pro',
						'name'      => __( 'Professional Plan', 'frontend-dashboard' ),
						'price'     => '49.00',
						'cycle'     => 'Monthly',
						'subs'      => 28,
						'user_role' => 'subscriber',
						'popular'   => true,
						'features'  => "All Starter Features\nPost Submissions & Taxonomies\nCustom Post Types\nPriority Support",
						'color'     => '#16a34a',
					),
					'enterprise' => array(
						'id'        => 'enterprise',
						'name'      => __( 'Enterprise Annual', 'frontend-dashboard' ),
						'price'     => '299.00',
						'cycle'     => 'Annual',
						'subs'      => 8,
						'user_role' => 'administrator',
						'popular'   => false,
						'features'  => "Full Platform Access\nUnlimited Post Types\nAdvanced Payments & Invoicing\nDedicated Account Manager",
						'color'     => '#7c3aed',
					),
				);
			}

			$roles = function_exists( 'fed_get_user_roles' ) ? fed_get_user_roles() : array();
			?>
			<div class="bc_fed fed_plans_container" style="font-family: inherit; position: relative;">
				
				<!-- Plans Header & Actions -->
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
					<div>
						<h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
							<i class="fas fa-cubes" style="color: #033333;"></i>
							<span><?php esc_html_e( 'Subscription Plans', 'frontend-dashboard' ); ?></span>
						</h3>
						<p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
							<?php esc_html_e( 'Configure membership packages, pricing tiers, recurring billing intervals, and user role assignments.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
						<!-- Back to Subscribers Button -->
						<a href="
						<?php
						echo esc_url(
							fed_menu_page_url(
								'fed_payments',
								array(
									'menu'    => 'subscriptions',
									'submenu' => 'FEDSubscription@subscriptions',
								)
							)
						);
						?>
									" 
							style="display: inline-flex; align-items: center; gap: 7px; background: #ffffff; color: #033333; border: 1px solid #cbd5e1; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.04); transition: all 0.15s ease;">
							<i class="fas fa-users"></i> 
							<span><?php esc_html_e( 'All Subscriptions', 'frontend-dashboard' ); ?></span>
						</a>

						<!-- Add Subscription Plan Button -->
						<button type="button" id="fed_open_add_plan_btn" style="display: inline-flex; align-items: center; gap: 7px; background: #033333; color: #ffffff; border: 1px solid #033333; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; box-shadow: 0 2px 6px rgba(3,51,51,0.2);">
							<i class="fas fa-plus"></i> <?php esc_html_e( 'New Subscription Plan', 'frontend-dashboard' ); ?>
						</button>
					</div>
				</div>

				<!-- Plans Grid -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
					<?php
					foreach ( $custom_plans as $key => $plan ) :
						$features_arr = is_array( $plan['features'] ) ? $plan['features'] : explode( "\n", trim( $plan['features'] ) );
						$plan_color   = ! empty( $plan['color'] ) ? $plan['color'] : '#0284c7';
						$plan_price   = isset( $plan['price'] ) ? $plan['price'] : '0.00';
						$plan_cycle   = isset( $plan['cycle'] ) ? $plan['cycle'] : 'Monthly';
						?>
						<div class="fed_plan_card" data-plan-id="<?php echo esc_attr( $key ); ?>" style="background: #ffffff; border: 1px solid <?php echo ! empty( $plan['popular'] ) ? '#16a34a' : '#e2e8f0'; ?>; border-radius: 14px; padding: 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); position: relative; display: flex; flex-direction: column; justify-content: space-between;">
							
							<?php if ( ! empty( $plan['popular'] ) ) : ?>
								<span style="position: absolute; top: -11px; right: 20px; background: #16a34a; color: #ffffff; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 2px 10px; border-radius: 9999px; letter-spacing: 0.04em;">
									<?php esc_html_e( 'Most Popular', 'frontend-dashboard' ); ?>
								</span>
							<?php endif; ?>

							<div>
								<h4 style="margin: 0 0 8px 0; font-size: 17px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $plan['name'] ); ?></h4>
								<div style="display: flex; align-items: baseline; gap: 6px; margin-bottom: 16px;">
									<span style="font-size: 28px; font-weight: 800; color: #0f172a;">$<?php echo esc_html( $plan_price ); ?></span>
									<span style="font-size: 13px; color: #64748b;">/ <?php echo esc_html( $plan_cycle ); ?></span>
								</div>

								<div style="background: #f8fafc; border: 1px solid #f1f5f9; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; color: #334155; font-weight: 600; margin-bottom: 20px; display: inline-flex; align-items: center; gap: 6px;">
									<i class="fas fa-users" style="color: <?php echo esc_attr( $plan_color ); ?>;"></i>
									<?php
									/* translators: %d: Number of active subscribers */
									printf( esc_html__( '%d Active Subscribers', 'frontend-dashboard' ), intval( isset( $plan['subs'] ) ? $plan['subs'] : 0 ) );
									?>
								</div>

								<ul style="list-style: none; padding: 0; margin: 0 0 24px 0; display: flex; flex-direction: column; gap: 10px;">
									<?php foreach ( $features_arr as $feature ) : ?>
										<?php if ( ! empty( trim( $feature ) ) ) : ?>
											<li style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #475569;">
												<i class="fas fa-check" style="color: #16a34a; font-size: 12px; flex-shrink: 0;"></i>
												<span><?php echo esc_html( trim( $feature ) ); ?></span>
											</li>
										<?php endif; ?>
									<?php endforeach; ?>
								</ul>
							</div>

							<!-- Action Buttons & 3-dots Menu Container -->
							<div style="display: flex; gap: 10px; position: relative;">
								<button type="button" class="fed_edit_plan_btn" 
										data-id="<?php echo esc_attr( $key ); ?>"
										data-name="<?php echo esc_attr( $plan['name'] ); ?>"
										data-price="<?php echo esc_attr( $plan_price ); ?>"
										data-cycle="<?php echo esc_attr( $plan_cycle ); ?>"
										data-role="<?php echo esc_attr( isset( $plan['user_role'] ) ? $plan['user_role'] : '' ); ?>"
										data-popular="<?php echo ! empty( $plan['popular'] ) ? '1' : '0'; ?>"
										data-features="<?php echo esc_attr( is_array( $plan['features'] ) ? implode( "\n", $plan['features'] ) : $plan['features'] ); ?>"
										style="flex: 1; background: #f8fafc; border: 1px solid #cbd5e1; padding: 9px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; transition: background 0.15s ease;">
									<i class="fas fa-pencil-alt" style="margin-right: 5px; font-size: 11px;"></i> <?php esc_html_e( 'Edit Plan', 'frontend-dashboard' ); ?>
								</button>
								
								<div style="position: relative;">
									<button type="button" class="fed_plan_dots_btn" style="background: #ffffff; border: 1px solid #cbd5e1; padding: 9px 12px; border-radius: 8px; color: #64748b; cursor: pointer;">
										<i class="fas fa-ellipsis-v"></i>
									</button>
									
									<!-- Dropdown Actions Menu -->
									<div class="fed_plan_dropdown_menu" style="display: none; position: absolute; right: 0; bottom: calc(100% + 6px); background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); width: 170px; z-index: 50; overflow: hidden; padding: 4px;">
										<a href="#" class="fed_dropdown_edit" data-id="<?php echo esc_attr( $key ); ?>" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; font-size: 12.5px; color: #334155; text-decoration: none; border-radius: 6px; font-weight: 500;">
											<i class="fas fa-edit" style="width: 14px; color: #0284c7;"></i> <?php esc_html_e( 'Edit Plan', 'frontend-dashboard' ); ?>
										</a>
										<a href="#" class="fed_dropdown_duplicate" data-id="<?php echo esc_attr( $key ); ?>" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; font-size: 12.5px; color: #334155; text-decoration: none; border-radius: 6px; font-weight: 500;">
											<i class="fas fa-copy" style="width: 14px; color: #16a34a;"></i> <?php esc_html_e( 'Duplicate', 'frontend-dashboard' ); ?>
										</a>
										<a href="
										<?php
										echo esc_url(
											fed_menu_page_url(
												'fed_payments',
												array(
													'menu' => 'subscriptions',
													'submenu' => 'FEDSubscription@subscriptions',
												)
											)
										);
										?>
													" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; font-size: 12.5px; color: #334155; text-decoration: none; border-radius: 6px; font-weight: 500;">
											<i class="fas fa-users" style="width: 14px; color: #64748b;"></i> <?php esc_html_e( 'Subscribers', 'frontend-dashboard' ); ?>
										</a>
										<div style="height: 1px; background: #f1f5f9; margin: 4px 0;"></div>
										<a href="#" class="fed_dropdown_delete" data-id="<?php echo esc_attr( $key ); ?>" style="display: flex; align-items: center; gap: 8px; padding: 8px 12px; font-size: 12.5px; color: #ef4444; text-decoration: none; border-radius: 6px; font-weight: 600;">
											<i class="fas fa-trash" style="width: 14px; color: #ef4444;"></i> <?php esc_html_e( 'Delete Plan', 'frontend-dashboard' ); ?>
										</a>
									</div>
								</div>
							</div>

						</div>
					<?php endforeach; ?>
				</div>

				<!-- Add/Edit Subscription Plan Modal -->
				<div id="fed_plan_modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
					<div style="background: #ffffff; border-radius: 14px; max-width: 520px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); overflow: hidden; animation: fedFadeIn 0.2s ease;">
						
						<!-- Modal Header -->
						<div style="background: #033333; color: #ffffff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
							<h4 id="fed_plan_modal_title" style="margin: 0; font-size: 16px; font-weight: 700; color: #ffffff;">
								<?php esc_html_e( 'Configure Subscription Plan', 'frontend-dashboard' ); ?>
							</h4>
							<button type="button" id="fed_close_plan_modal" style="background: none; border: none; color: #ffffff; font-size: 18px; cursor: pointer; line-height: 1;">&times;</button>
						</div>

						<!-- Modal Form -->
						<form method="post" class="fed_ajax" action="<?php echo esc_url( add_query_arg( array( 'fed_action_hook' => 'FEDSubscription@save_plan' ), fed_get_ajax_form_action( 'fed_ajax_request' ) ) ); ?>" style="padding: 24px;">
							<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
							<input type="hidden" name="plan_id" id="fed_input_plan_id" value="" />

							<div style="display: flex; flex-direction: column; gap: 16px;">
								<div>
									<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Plan Name', 'frontend-dashboard' ); ?> *</label>
									<input type="text" name="plan_name" id="fed_input_plan_name" required placeholder="e.g. Pro Membership Suite" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;" />
								</div>

								<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Price ($ USD)', 'frontend-dashboard' ); ?> *</label>
										<input type="number" step="0.01" name="plan_price" id="fed_input_plan_price" required placeholder="49.00" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;" />
									</div>
									<div>
										<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Billing Cycle', 'frontend-dashboard' ); ?></label>
										<select name="plan_cycle" id="fed_input_plan_cycle" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
											<option value="Monthly"><?php esc_html_e( 'Monthly', 'frontend-dashboard' ); ?></option>
											<option value="Quarterly"><?php esc_html_e( 'Quarterly', 'frontend-dashboard' ); ?></option>
											<option value="Annual"><?php esc_html_e( 'Annual', 'frontend-dashboard' ); ?></option>
											<option value="Lifetime"><?php esc_html_e( 'Lifetime / One-time', 'frontend-dashboard' ); ?></option>
										</select>
									</div>
								</div>

								<div>
									<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Assigned User Role Upon Subscription', 'frontend-dashboard' ); ?></label>
									<select name="user_role" id="fed_input_user_role" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;">
										<?php foreach ( $roles as $role_key => $role_name ) : ?>
											<option value="<?php echo esc_attr( $role_key ); ?>"><?php echo esc_html( $role_name ); ?></option>
										<?php endforeach; ?>
									</select>
								</div>

								<div>
									<label style="font-size: 13px; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;"><?php esc_html_e( 'Features List (One feature per line)', 'frontend-dashboard' ); ?></label>
									<textarea name="plan_features" id="fed_input_plan_features" rows="4" placeholder="Access to user dashboard&#10;Custom post submissions&#10;Priority support" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px;"></textarea>
								</div>

								<div style="display: flex; align-items: center; gap: 8px;">
									<input type="checkbox" name="popular" id="fed_input_popular" value="1" style="width: 16px; height: 16px; accent-color: #16a34a;" />
									<label for="fed_input_popular" style="font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; margin: 0;"><?php esc_html_e( 'Highlight as "Most Popular" Plan', 'frontend-dashboard' ); ?></label>
								</div>
							</div>

							<div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
								<button type="button" id="fed_cancel_plan_modal" style="background: #f1f5f9; border: 1px solid #cbd5e1; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;"><?php esc_html_e( 'Cancel', 'frontend-dashboard' ); ?></button>
								<button type="submit" style="background: #033333; border: 1px solid #033333; padding: 9px 22px; border-radius: 8px; font-size: 13px; font-weight: 700; color: #ffffff; cursor: pointer; box-shadow: 0 2px 6px rgba(3,51,51,0.2);"><?php esc_html_e( 'Save Plan', 'frontend-dashboard' ); ?></button>
							</div>

						</form>
					</div>
				</div>

				<!-- Interactive 3-dots Dropdown & Modal Script -->
				<script type="text/javascript">
				jQuery(document).ready(function($){
					// Auto-open new modal if query param has open=new
					var urlParams = new URLSearchParams(window.location.search);
					if (urlParams.get('open') === 'new') {
						setTimeout(function(){
							$('#fed_open_add_plan_btn').trigger('click');
						}, 100);
					}

					// Toggle 3-dots dropdown
					$('.fed_plan_dots_btn').on('click', function(e){
						e.stopPropagation();
						var menu = $(this).siblings('.fed_plan_dropdown_menu');
						$('.fed_plan_dropdown_menu').not(menu).hide();
						menu.toggle();
					});

					// Hide dropdown when clicking outside
					$(document).on('click', function(){
						$('.fed_plan_dropdown_menu').hide();
					});

					// Open Add Plan Modal
					$('#fed_open_add_plan_btn').on('click', function(){
						$('#fed_plan_modal_title').text('<?php echo esc_js( __( 'Create New Subscription Plan', 'frontend-dashboard' ) ); ?>');
						$('#fed_input_plan_id').val('');
						$('#fed_input_plan_name').val('');
						$('#fed_input_plan_price').val('');
						$('#fed_input_plan_cycle').val('Monthly');
						$('#fed_input_plan_features').val('');
						$('#fed_input_popular').prop('checked', false);
						$('#fed_plan_modal').css('display', 'flex');
					});

					// Open Edit Plan Modal
					$(document).on('click', '.fed_edit_plan_btn, .fed_dropdown_edit', function(e){
						e.preventDefault();
						var card = $(this).closest('.fed_plan_card');
						var btn = card.find('.fed_edit_plan_btn');
						
						$('#fed_plan_modal_title').text('<?php echo esc_js( __( 'Edit Subscription Plan', 'frontend-dashboard' ) ); ?>');
						$('#fed_input_plan_id').val(btn.data('id'));
						$('#fed_input_plan_name').val(btn.data('name'));
						$('#fed_input_plan_price').val(btn.data('price'));
						$('#fed_input_plan_cycle').val(btn.data('cycle'));
						$('#fed_input_user_role').val(btn.data('role'));
						$('#fed_input_popular').prop('checked', btn.data('popular') == '1');
						$('#fed_input_plan_features').val(btn.data('features'));
						$('#fed_plan_modal').css('display', 'flex');
					});

					// Close Modal
					$('#fed_close_plan_modal, #fed_cancel_plan_modal').on('click', function(){
						$('#fed_plan_modal').hide();
					});

					// Duplicate Plan
					$(document).on('click', '.fed_dropdown_duplicate', function(e){
						e.preventDefault();
						var id = $(this).data('id');
						if (confirm('<?php echo esc_js( __( 'Duplicate this subscription plan?', 'frontend-dashboard' ) ); ?>')) {
							$.post('<?php echo esc_url( fed_get_ajax_form_action( 'fed_ajax_request' ) ); ?>', {
								fed_action_hook: 'FEDSubscription@duplicate_plan',
								fed_nonce: '<?php echo esc_js( wp_create_nonce( 'fed_nonce' ) ); ?>',
								plan_id: id
							}, function(res){
								location.reload();
							});
						}
					});

					// Delete Plan
					$(document).on('click', '.fed_dropdown_delete', function(e){
						e.preventDefault();
						var id = $(this).data('id');
						if (confirm('<?php echo esc_js( __( 'Are you sure you want to delete this subscription plan?', 'frontend-dashboard' ) ); ?>')) {
							$.post('<?php echo esc_url( fed_get_ajax_form_action( 'fed_ajax_request' ) ); ?>', {
								fed_action_hook: 'FEDSubscription@delete_plan',
								fed_nonce: '<?php echo esc_js( wp_create_nonce( 'fed_nonce' ) ); ?>',
								plan_id: id
							}, function(res){
								location.reload();
							});
						}
					});
				});
				</script>

			</div>
			<?php
		}

		/**
		 * Save Subscription Plan (AJAX Hook).
		 *
		 * @param array $request
		 */
		public function save_plan( $request ) {
			$this->authorize();

			$plan_id  = ! empty( $request['plan_id'] ) ? sanitize_key( $request['plan_id'] ) : 'plan_' . time();
			$name     = isset( $request['plan_name'] ) ? fed_sanitize_text_field( $request['plan_name'] ) : '';
			$price    = isset( $request['plan_price'] ) ? fed_sanitize_text_field( $request['plan_price'] ) : '0.00';
			$cycle    = isset( $request['plan_cycle'] ) ? fed_sanitize_text_field( $request['plan_cycle'] ) : 'Monthly';
			$role     = isset( $request['user_role'] ) ? fed_sanitize_text_field( $request['user_role'] ) : 'subscriber';
			$features = isset( $request['plan_features'] ) ? sanitize_textarea_field( $request['plan_features'] ) : '';
			$popular  = ! empty( $request['popular'] );

			$plans = get_option( 'fed_subscription_plans_custom', array() );
			if ( empty( $plans ) ) {
				$plans = array(
					'starter' => array(
						'id'       => 'starter',
						'name'     => 'Starter Membership',
						'price'    => '19.00',
						'cycle'    => 'Monthly',
						'subs'     => 12,
						'features' => "Standard User Dashboard\nCustom Profile Fields\nCommunity Access",
						'color'    => '#0284c7',
					),
					'pro'     => array(
						'id'       => 'pro',
						'name'     => 'Professional Plan',
						'price'    => '49.00',
						'cycle'    => 'Monthly',
						'subs'     => 28,
						'popular'  => true,
						'features' => "All Starter Features\nPost Submissions\nPriority Support",
						'color'    => '#16a34a',
					),
				);
			}

			$plans[ $plan_id ] = array(
				'id'        => $plan_id,
				'name'      => $name,
				'price'     => $price,
				'cycle'     => $cycle,
				'user_role' => $role,
				'popular'   => $popular,
				'features'  => $features,
				'subs'      => isset( $plans[ $plan_id ]['subs'] ) ? $plans[ $plan_id ]['subs'] : 0,
				'color'     => '#0284c7',
			);

			update_option( 'fed_subscription_plans_custom', $plans );
			wp_send_json_success( array( 'message' => __( 'Subscription plan saved successfully', 'frontend-dashboard' ) ) );
		}

		/**
		 * Duplicate Plan (AJAX Hook).
		 *
		 * @param array $request
		 */
		public function duplicate_plan( $request ) {
			$this->authorize();
			$plan_id = isset( $request['plan_id'] ) ? sanitize_key( $request['plan_id'] ) : '';
			$plans   = get_option( 'fed_subscription_plans_custom', array() );

			if ( isset( $plans[ $plan_id ] ) ) {
				$new_id           = 'plan_' . time();
				$new_plan         = $plans[ $plan_id ];
				$new_plan['id']   = $new_id;
				$new_plan['name'] = $new_plan['name'] . ' (Copy)';
				$new_plan['subs'] = 0;
				$plans[ $new_id ] = $new_plan;
				update_option( 'fed_subscription_plans_custom', $plans );
				wp_send_json_success( array( 'message' => __( 'Plan duplicated successfully', 'frontend-dashboard' ) ) );
			}
			wp_send_json_error( array( 'message' => __( 'Plan not found', 'frontend-dashboard' ) ) );
		}

		/**
		 * Delete Plan (AJAX Hook).
		 *
		 * @param array $request
		 */
		public function delete_plan( $request ) {
			$this->authorize();
			$plan_id = isset( $request['plan_id'] ) ? sanitize_key( $request['plan_id'] ) : '';
			$plans   = get_option( 'fed_subscription_plans_custom', array() );

			if ( isset( $plans[ $plan_id ] ) ) {
				unset( $plans[ $plan_id ] );
				update_option( 'fed_subscription_plans_custom', $plans );
				wp_send_json_success( array( 'message' => __( 'Plan deleted successfully', 'frontend-dashboard' ) ) );
			}
			wp_send_json_error( array( 'message' => __( 'Plan not found', 'frontend-dashboard' ) ) );
		}

		/**
		 * Authorize.
		 */
		public function authorize() {
			if ( ! current_user_can( 'manage_options' ) ) {
				if ( wp_doing_ajax() ) {
					wp_send_json_error( array( 'message' => __( 'Permission denied', 'frontend-dashboard' ) ), 403 );
					exit();
				}
				wp_die( esc_html__( 'Sorry! You are not allowed to do this action | Error: FEDSubscription@authorize', 'frontend-dashboard' ), 403 );
			}
		}
	}

	new FEDSubscription();
}