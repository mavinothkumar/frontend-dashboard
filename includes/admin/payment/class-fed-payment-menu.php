<?php
/**
 * Payment Menu Controller & Enterprise Dashboard Layout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDPaymentMenu' ) ) {
	/**
	 * Class FEDPaymentMenu
	 */
	class FEDPaymentMenu {

		/**
		 * Index.
		 */
		public function index() {
			$this->layout();
		}

		/**
		 * Payment Menu Layout.
		 */
		public function layout() {
			$menus = apply_filters(
				'fed_payment_menu', array(
					'dashboard'    => array(
						'icon'    => 'fas fa-chart-pie',
						'name'    => __( 'Dashboard', 'frontend-dashboard' ),
						'submenu' => 'FEDPaymentDashboard@dashboard',
					),
					'transactions' => array(
						'icon'    => 'fas fa-receipt',
						'name'    => __( 'Transactions', 'frontend-dashboard' ),
						'submenu' => 'FEDTransaction@transactions',
					),
					'subscriptions' => array(
						'icon'    => 'fas fa-sync-alt',
						'name'    => __( 'Subscriptions', 'frontend-dashboard' ),
						'submenu' => 'FEDSubscription@subscriptions',
					),
					'gateways'      => array(
						'icon'    => 'fas fa-credit-card',
						'name'    => __( 'Payment Gateways', 'frontend-dashboard' ),
						'submenu' => array(
							'FEDPaymentGatewayHub@hub' => array(
								'icon' => 'fas fa-th-large',
								'name' => __( 'Gateways Hub', 'frontend-dashboard' ),
								'menu' => array( 'FEDPaymentGatewayHub@hub' ),
							),
							'FEDPayment@settings' => array(
								'icon' => 'fas fa-sliders-h',
								'name' => __( 'Gateway Settings', 'frontend-dashboard' ),
								'menu' => array( 'FEDPayment@settings' ),
							),
						),
					),
					'invoice'      => array(
						'icon'    => 'fas fa-file-invoice-dollar',
						'name'    => __( 'Invoices', 'frontend-dashboard' ),
						'submenu' => array(
							'FEDInvoice@details'          => array(
								'icon' => 'fas fa-building',
								'name' => __( 'Company Details', 'frontend-dashboard' ),
								'menu' => array( 'FEDInvoice@details' ),
							),
							'FEDInvoiceTemplate@template' => array(
								'icon' => 'fas fa-paint-brush',
								'name' => __( 'Invoice Template', 'frontend-dashboard' ),
								'menu' => array( 'FEDInvoiceTemplate@template' ),
							),
							'FEDInvoice@user'             => array(
								'icon' => 'fas fa-map-marker-alt',
								'name' => __( 'User Billing Address', 'frontend-dashboard' ),
								'menu' => array( 'FEDInvoice@user' ),
							),
						),
					),
				)
			);
			?>
			<div class="bc_fed fed_payments_app_wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; width: 100%; max-width: 100%; margin: 15px 0 40px 0; padding: 0 20px 0 10px; box-sizing: border-box;">
				
				<!-- Enterprise Hero Header Banner -->
				<div style="background: linear-gradient(135deg, #033333 0%, #064e4e 50%, #0a6565 100%); border-radius: 14px; padding: 22px 28px; color: #ffffff; margin-bottom: 22px; box-shadow: 0 4px 14px rgba(3, 51, 51, 0.18);">
					<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
						<div>
							<div style="display: flex; align-items: center; gap: 10px;">
								<span style="background: rgba(255, 255, 255, 0.15); width: 34px; height: 34px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 16px;">
									<i class="fas fa-wallet"></i>
								</span>
								<h2 style="font-size: 21px; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.01em;">
									<?php esc_html_e( 'Payments & Billing Engine', 'frontend-dashboard' ); ?>
								</h2>
							</div>
						</div>

						<div style="display: flex; align-items: center; gap: 10px;">
							<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => 'gateways', 'submenu' => 'FEDPaymentGatewayHub@hub' ) ) ); ?>" style="display: inline-flex; align-items: center; gap: 7px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.22); padding: 8px 14px; border-radius: 8px; color: #ffffff; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease;">
								<i class="fas fa-plug" style="color: #6ee7b7;"></i> <?php esc_html_e( 'Gateways Hub', 'frontend-dashboard' ); ?>
							</a>
							<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => 'transactions', 'submenu' => 'FEDTransaction@add_new_transaction' ) ) ); ?>" style="display: inline-flex; align-items: center; gap: 7px; background: #10b981; border: 1px solid #059669; padding: 8px 16px; border-radius: 8px; color: #ffffff; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);">
								<i class="fas fa-plus"></i> <?php esc_html_e( 'Record Transaction', 'frontend-dashboard' ); ?>
							</a>
						</div>
					</div>
				</div>

				<?php if ( ! empty( $menus ) ) { ?>
					<!-- Modern 5-Tab Navigation Bar -->
					<div style="margin-bottom: 20px;">
						<?php $this->header_menu( $menus ); ?>
					</div>

					<!-- Body Content -->
					<div>
						<?php $this->body_content( $menus ); ?>
					</div>
				<?php } else { ?>
					<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 30px; text-align: center; color: #64748b;">
						<?php esc_html_e( 'Something went wrong loading the payment configuration.', 'frontend-dashboard' ); ?>
					</div>
				<?php } ?>

			</div>
			<?php
		}

		/**
		 * Header Tabs Menu.
		 *
		 * @param  array $menus  Menus.
		 */
		public function header_menu( $menus ) {
			$get_payload = isset( $_GET ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET ) ) : array();
			$current_menu = isset( $get_payload['menu'] ) ? $get_payload['menu'] : fed_get_first_key_in_array( $menus );
			?>
			<div class="fed_nav_pills_container" style="display: flex; gap: 8px; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; overflow-x: auto;">
				<?php
				foreach ( $menus as $index => $item ) {
					$is_active = ( $current_menu === $index );
					$bg_style   = $is_active ? '#033333' : '#f8fafc';
					$text_style = $is_active ? '#ffffff' : '#475569';
					$border     = $is_active ? '1px solid #033333' : '1px solid #e2e8f0';
					$icon_color = $is_active ? '#34d399' : '#64748b';
					$shadow     = $is_active ? 'box-shadow: 0 4px 6px -1px rgba(3, 51, 51, 0.2);' : '';
					?>
					<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => esc_attr( $index ) ) ) ); ?>" 
					   style="display: inline-flex; align-items: center; gap: 8px; background: <?php echo esc_attr( $bg_style ); ?>; color: <?php echo esc_attr( $text_style ); ?>; border: <?php echo esc_attr( $border ); ?>; padding: 10px 18px; border-radius: 8px; font-size: 13.5px; font-weight: 700; text-decoration: none; transition: all 0.2s ease; <?php echo esc_attr( $shadow ); ?>">
						<i class="<?php echo esc_attr( fed_get_data( 'icon', $item ) ); ?>" style="color: <?php echo esc_attr( $icon_color ); ?>; font-size: 14px;"></i>
						<span><?php echo esc_html( fed_get_data( 'name', $item ) ); ?></span>
					</a>
				<?php } ?>
			</div>
			<?php
		}

		/**
		 * Body Content.
		 *
		 * @param  array $menus  Menus.
		 */
		public function body_content( $menus ) {
			$_menu    = fed_get_data( 'menu' );
			$_submenu = fed_get_data( 'submenu' );
			$menu     = ! empty( $_menu ) && isset( $menus[ $_menu ] ) ? esc_html( $_menu ) : fed_get_first_key_in_array( $menus );
			$submenu  = ! empty( $_submenu ) ? esc_html( $_submenu ) : false;

			if ( $menu && isset( $menus[ $menu ] ) ) {
				$menu_config = $menus[ $menu ];

				// Submenu items array
				if ( isset( $menu_config['submenu'] ) && is_array( $menu_config['submenu'] ) && count( $menu_config['submenu'] ) ) {
					if ( ! $submenu || ! isset( $menu_config['submenu'][ $submenu ] ) ) {
						$submenu = fed_get_first_key_in_array( $menu_config['submenu'] );
					}
					?>
					<div class="row" style="margin-top: 10px;">
						<!-- Left Submenu Pills -->
						<div class="col-md-3" style="margin-bottom: 16px;">
							<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
								<div style="font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; padding: 6px 12px 10px 12px; border-bottom: 1px solid #f1f5f9; margin-bottom: 8px;">
									<?php echo esc_html( fed_get_data( 'name', $menu_config ) ); ?> <?php esc_html_e( 'Sections', 'frontend-dashboard' ); ?>
								</div>
								<ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 4px;">
									<?php
									foreach ( $menu_config['submenu'] as $index => $sub_item ) {
										$is_sub_active = ( $submenu === $index );
										$sub_bg   = $is_sub_active ? '#033333' : 'transparent';
										$sub_text = $is_sub_active ? '#ffffff' : '#334155';
										$sub_icon = $is_sub_active ? '#34d399' : '#64748b';
										$sub_weight = $is_sub_active ? '700' : '600';
										?>
										<li>
											<a href="<?php echo esc_url( fed_menu_page_url( 'fed_payments', array( 'menu' => $menu, 'submenu' => $index ) ) ); ?>" 
											   style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 7px; background: <?php echo esc_attr( $sub_bg ); ?>; color: <?php echo esc_attr( $sub_text ); ?>; font-size: 13px; font-weight: <?php echo esc_attr( $sub_weight ); ?>; text-decoration: none; transition: background 0.15s ease;">
												<i class="<?php echo esc_attr( $sub_item['icon'] ); ?>" style="color: <?php echo esc_attr( $sub_icon ); ?>; width: 16px; text-align: center;"></i>
												<span><?php echo esc_html( $sub_item['name'] ); ?></span>
											</a>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div>

						<!-- Right Content Card -->
						<div class="col-md-9">
							<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden;">
								<div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between;">
									<h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
										<i class="<?php echo esc_attr( $menu_config['submenu'][ $submenu ]['icon'] ); ?>" style="color: #033333;"></i>
										<span><?php echo esc_html( $menu_config['submenu'][ $submenu ]['name'] ); ?></span>
									</h3>
								</div>
								<div style="padding: 20px;">
									<?php
									if ( is_string( $submenu ) ) {
										fed_execute_method_by_string( $submenu, $_GET );
									}
									?>
								</div>
							</div>
						</div>
					</div>
					<?php
				} elseif ( isset( $menu_config['submenu'] ) && is_string( $menu_config['submenu'] ) ) {
					// Single full-width controller layout
					$sub_menu_action = $menu_config['submenu'];
					if ( ! empty( $_submenu ) ) {
						if ( strpos( $_submenu, '@' ) !== false ) {
							$sub_menu_action = $_submenu;
						} elseif ( 'subscriptions' === $menu && in_array( $_submenu, array( 'plans', 'subscriptions' ), true ) ) {
							$sub_menu_action = 'FEDSubscription@' . $_submenu;
						} elseif ( 'transactions' === $menu && in_array( $_submenu, array( 'transactions', 'add_new_transaction' ), true ) ) {
							$sub_menu_action = 'FEDTransaction@' . $_submenu;
						}
					}
					?>
					<div style="margin-top: 10px;">
						<?php fed_execute_method_by_string( $sub_menu_action, $_GET ); ?>
					</div>
					<?php
				}
			} else {
				?>
				<div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 16px 20px; border-radius: 10px; margin-top: 15px;">
					<strong><?php esc_html_e( 'Selected payment menu section could not be found.', 'frontend-dashboard' ); ?></strong>
				</div>
				<?php
			}
		}
	}

	new FEDPaymentMenu();
}

