<?php
/**
 * Payment Gateways Directory & Hub.
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDPaymentGatewayHub' ) ) {
	/**
	 * Class FEDPaymentGatewayHub
	 */
	class FEDPaymentGatewayHub {

		/**
		 * Gateways Hub View.
		 */
		public function hub() {
			$this->authorize();
			$gateways        = fed_get_registered_gateways();
			$current_gateway = fed_payment_gateway();
			?>
			<div class="bc_fed fed_gateway_hub_container" style="font-family: inherit;">
				
				<!-- Hub Header -->
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
					<div>
						<h3 style="margin: 0; font-size: 17px; font-weight: 700; color: #0f172a;">
							<?php esc_html_e( 'Payment Gateways & Processors Hub', 'frontend-dashboard' ); ?>
						</h3>
						<p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
							<?php esc_html_e( 'Manage connected merchant gateways, configure checkout options, and extend capabilities with Pro processors.', 'frontend-dashboard' ); ?>
						</p>
					</div>
					<div style="display: flex; gap: 10px; align-items: center;">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_payments&menu=gateways&submenu=FEDPayment@settings' ) ); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; border: 1px solid #cbd5e1; color: #334155; font-weight: 600; font-size: 13px; padding: 9px 16px; border-radius: 8px; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
							<i class="fas fa-sliders-h" style="color: #64748b;"></i> <?php esc_html_e( 'General Gateway Settings', 'frontend-dashboard' ); ?>
						</a>
					</div>
				</div>

				<!-- Gateways Card Directory Grid -->
				<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 20px;">
					<?php
					foreach ( $gateways as $gateway_id => $gateway ) {
						$is_active    = ! empty( $gateway['is_active'] );
						$is_installed = ! empty( $gateway['is_installed'] );
						$is_pro       = ( 'pro' === $gateway['type'] );
						$badge_color  = isset( $gateway['badge_color'] ) ? $gateway['badge_color'] : '#16a34a';
						$brand_color  = isset( $gateway['color'] ) ? $gateway['color'] : '#0f172a';
						?>
						<div class="fed_gateway_card" style="background: #ffffff; border: 1px solid <?php echo $is_active ? '#86efac' : '#e2e8f0'; ?>; border-radius: 14px; padding: 22px 24px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between; position: relative; transition: all 0.2s ease;">
							
							<div>
								<!-- Header: Icon, Name & Type Badge -->
								<div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 14px;">
									<div style="display: flex; align-items: center; gap: 14px; min-width: 0; flex: 1;">
										<div style="width: 48px; height: 48px; border-radius: 12px; background: <?php echo esc_attr( $brand_color ); ?>15; color: <?php echo esc_attr( $brand_color ); ?>; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;">
											<i class="<?php echo esc_attr( $gateway['icon'] ); ?>"></i>
										</div>
										<div style="min-width: 0; flex: 1;">
											<h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; line-height: 1.3;">
												<?php echo esc_html( $gateway['name'] ); ?>
											</h4>
											<span style="font-size: 12px; color: #64748b; font-weight: 500; display: block; margin-top: 2px;">
												<?php echo esc_html( $gateway['tagline'] ); ?>
											</span>
										</div>
									</div>

									<span style="background: <?php echo esc_attr( $badge_color ); ?>18; color: <?php echo esc_attr( $badge_color ); ?>; border: 1px solid <?php echo esc_attr( $badge_color ); ?>35; font-size: 11px; font-weight: 800; padding: 3px 9px; border-radius: 6px; text-transform: uppercase; white-space: nowrap; flex-shrink: 0; letter-spacing: 0.03em;">
										<?php echo esc_html( $gateway['badge'] ); ?>
									</span>
								</div>

								<!-- Description -->
								<p style="margin: 0 0 18px 0; font-size: 13px; color: #475569; line-height: 1.45;">
									<?php echo esc_html( $gateway['description'] ); ?>
								</p>
							</div>

							<!-- Footer Action Bar -->
							<div style="border-top: 1px solid #f1f5f9; padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
								<div style="display: flex; align-items: center; gap: 6px;">
									<?php if ( $is_active ) : ?>
										<span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #16a34a;">
											<span style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></span>
											<?php esc_html_e( 'Active / In Use', 'frontend-dashboard' ); ?>
										</span>
									<?php elseif ( $is_installed ) : ?>
										<span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #64748b;">
											<span style="width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1;"></span>
											<?php esc_html_e( 'Ready to Configure', 'frontend-dashboard' ); ?>
										</span>
									<?php else : ?>
										<span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; color: #635bff;">
											<i class="fas fa-lock" style="font-size: 11px;"></i>
											<?php esc_html_e( 'Pro Feature', 'frontend-dashboard' ); ?>
										</span>
									<?php endif; ?>
								</div>

								<div>
									<?php if ( $is_installed ) : ?>
										<a href="<?php echo esc_url( $gateway['settings_url'] ); ?>" style="display: inline-flex; align-items: center; gap: 6px; background: <?php echo $is_active ? '#033333' : '#f8fafc'; ?>; border: 1px solid <?php echo $is_active ? '#033333' : '#cbd5e1'; ?>; color: <?php echo $is_active ? '#ffffff' : '#334155'; ?>; padding: 7px 14px; border-radius: 6px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all 0.15s ease;">
											<i class="fas fa-cog" style="font-size: 11px;"></i> <?php esc_html_e( 'Configure', 'frontend-dashboard' ); ?>
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( $gateway['settings_url'] ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 6px; background: #635bff; border: 1px solid #635bff; color: #ffffff; padding: 7px 14px; border-radius: 6px; font-size: 12.5px; font-weight: 600; text-decoration: none; transition: all 0.15s ease; box-shadow: 0 2px 6px rgba(99,91,255,0.25);">
											<i class="fas fa-external-link-alt" style="font-size: 11px;"></i> <?php esc_html_e( 'Get Pro Connector', 'frontend-dashboard' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>

						</div>
					<?php } ?>
				</div>

			</div>
			<?php
		}

		/**
		 * Authorize.
		 */
		public function authorize() {
			if ( ! fed_is_admin() ) {
				wp_die( esc_html__( 'Sorry! You are not allowed to do this action | Error: FEDPaymentGatewayHub@authorize', 'frontend-dashboard' ) );
			}
		}
	}

	new FEDPaymentGatewayHub();
}