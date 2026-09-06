<?php
/**
 * Invoice Template Customizer & Management.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FEDInvoiceTemplate' ) ) {
	/**
	 * Class FEDInvoiceTemplate
	 */
	class FEDInvoiceTemplate {

		/**
		 * Template View.
		 */
		public function template() {
			$settings = get_option( 'fed_payment_settings', array() );
			$selected_template = isset( $settings['invoice']['template']['default'] ) ? $settings['invoice']['template']['default'] : 'standard_clean';
			$accent_color = isset( $settings['invoice']['template']['accent_color'] ) ? $settings['invoice']['template']['accent_color'] : '#033333';

			// Get registered invoice templates (Core + Addons)
			$templates = apply_filters(
				'fed_invoice_template',
				array(
					'standard_clean' => array(
						'id'          => 'standard_clean',
						'name'        => __( 'Standard Enterprise Minimalist', 'frontend-dashboard' ),
						'description' => __( 'Clean, crisp typography with itemized breakdown, tax/discount lines, and branded header.', 'frontend-dashboard' ),
						'tag'         => __( 'Default', 'frontend-dashboard' ),
						'is_pro'      => false,
					),
					'modern_gradient' => array(
						'id'          => 'modern_gradient',
						'name'        => __( 'Modern Corporate Statement', 'frontend-dashboard' ),
						'description' => __( 'Bold corporate header with dual-tone status badges and structured summary tables.', 'frontend-dashboard' ),
						'tag'         => __( 'Built-in', 'frontend-dashboard' ),
						'is_pro'      => false,
					),
					'compact_receipt' => array(
						'id'          => 'compact_receipt',
						'name'        => __( 'Compact Digital Receipt', 'frontend-dashboard' ),
						'description' => __( 'Streamlined receipt layout optimized for instant digital transactions and mobile view.', 'frontend-dashboard' ),
						'tag'         => __( 'Built-in', 'frontend-dashboard' ),
						'is_pro'      => false,
					),
				)
			);
			?>
			<div class="bc_fed fed_invoice_template_wrapper" style="font-family: inherit;">
				
				<!-- Template Selector Form -->
				<form method="post" class="fed_ajax" action="<?php echo esc_url( add_query_arg( array( 'fed_action_hook' => 'FEDInvoiceTemplate@update' ), fed_get_ajax_form_action( 'fed_ajax_request' ) ) ); ?>">
					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>

					<div style="margin-bottom: 22px;">
						<h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
							<?php esc_html_e( 'Invoice & Receipt Layout Templates', 'frontend-dashboard' ); ?>
						</h4>
						<p style="font-size: 13.5px; color: #64748b; margin: 0;">
							<?php esc_html_e( 'Choose the default layout design used when generating printable invoices and download PDFs for customers.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<!-- Template Cards Grid -->
					<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 26px;">
						<?php
						foreach ( $templates as $key => $tpl ) {
							$is_selected = ( $selected_template === $key );
							$border_color = $is_selected ? '#033333' : '#e2e8f0';
							$bg_color = $is_selected ? '#f8fafc' : '#ffffff';
							$badge_bg = $is_selected ? '#033333' : '#f1f5f9';
							$badge_color = $is_selected ? '#ffffff' : '#64748b';
							?>
							<label style="cursor: pointer; display: block; margin: 0;">
								<div style="border: 2px solid <?php echo esc_attr( $border_color ); ?>; background: <?php echo esc_attr( $bg_color ); ?>; border-radius: 12px; padding: 18px; position: relative; transition: all 0.2s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.02); height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
									
									<div>
										<!-- Top Header of Card -->
										<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
											<div style="display: flex; align-items: center; gap: 8px;">
												<input type="radio" name="template" value="<?php echo esc_attr( $key ); ?>" <?php checked( $is_selected, true ); ?> style="margin: 0; width: 17px; height: 17px; accent-color: #033333;" />
												<span style="font-weight: 700; font-size: 14.5px; color: #0f172a;"><?php echo esc_html( $tpl['name'] ); ?></span>
											</div>
											<span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px; background: <?php echo esc_attr( $badge_bg ); ?>; color: <?php echo esc_attr( $badge_color ); ?>;">
												<?php echo esc_html( $tpl['tag'] ); ?>
											</span>
										</div>

										<!-- Invoice Skeleton Preview -->
										<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 12px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
											<!-- Header Skeleton -->
											<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; margin-bottom: 8px;">
												<div style="width: 40%; height: 8px; background: #033333; border-radius: 4px;"></div>
												<div style="width: 25%; height: 6px; background: #cbd5e1; border-radius: 4px;"></div>
											</div>
											<!-- Rows Skeleton -->
											<div style="display: flex; flex-direction: column; gap: 5px; margin-bottom: 8px;">
												<div style="display: flex; justify-content: space-between;">
													<div style="width: 50%; height: 6px; background: #e2e8f0; border-radius: 3px;"></div>
													<div style="width: 20%; height: 6px; background: #e2e8f0; border-radius: 3px;"></div>
												</div>
												<div style="display: flex; justify-content: space-between;">
													<div style="width: 40%; height: 6px; background: #f1f5f9; border-radius: 3px;"></div>
													<div style="width: 15%; height: 6px; background: #f1f5f9; border-radius: 3px;"></div>
												</div>
											</div>
											<!-- Total Skeleton -->
											<div style="display: flex; justify-content: flex-end; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
												<div style="width: 30%; height: 7px; background: #10b981; border-radius: 3px;"></div>
											</div>
										</div>

										<!-- Description -->
										<p style="font-size: 12.5px; color: #64748b; margin: 0; line-height: 1.4;">
											<?php echo esc_html( $tpl['description'] ); ?>
										</p>
									</div>

									<div style="margin-top: 14px; font-size: 12px; font-weight: 700; color: <?php echo $is_selected ? '#033333' : '#94a3b8'; ?>; display: flex; align-items: center; gap: 6px;">
										<i class="<?php echo $is_selected ? 'fas fa-check-circle' : 'far fa-circle'; ?>"></i>
										<?php echo $is_selected ? esc_html__( 'Active Default Template', 'frontend-dashboard' ) : esc_html__( 'Click to select', 'frontend-dashboard' ); ?>
									</div>

								</div>
							</label>
						<?php } ?>
					</div>

					<!-- Additional Invoice Customization Options -->
					<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px;">
						<h5 style="font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 14px 0;">
							<i class="fas fa-palette" style="color: #033333; margin-right: 6px;"></i> <?php esc_html_e( 'Invoice Visual Branding', 'frontend-dashboard' ); ?>
						</h5>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" style="margin-bottom: 0;">
									<label style="font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">
										<?php esc_html_e( 'Primary Brand Accent Color', 'frontend-dashboard' ); ?>
									</label>
									<div style="display: flex; align-items: center; gap: 10px;">
										<input type="color" name="accent_color" value="<?php echo esc_attr( $accent_color ); ?>" style="width: 42px; height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 2px; cursor: pointer; background: #ffffff;" />
										<input type="text" name="accent_color_hex" value="<?php echo esc_attr( $accent_color ); ?>" readonly style="width: 110px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-family: monospace; background: #ffffff;" />
										<span style="font-size: 12px; color: #64748b;"><?php esc_html_e( 'Applied to invoice headings, table highlights, and grand totals.', 'frontend-dashboard' ); ?></span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Save Changes Button -->
					<button class="btn btn-primary" type="submit" style="background: #033333; border-color: #033333; font-weight: 700; font-size: 13.5px; padding: 9px 24px; border-radius: 8px; box-shadow: 0 2px 4px rgba(3, 51, 51, 0.15);">
						<i class="fas fa-save" style="margin-right: 6px;"></i> <?php esc_html_e( 'Save Invoice Template', 'frontend-dashboard' ); ?>
					</button>
				</form>

				<!-- Pro Features Banner -->
				<div style="margin-top: 30px; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #a7f3d0; border-radius: 12px; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
					<div style="display: flex; align-items: center; gap: 14px;">
						<div style="width: 44px; height: 44px; border-radius: 10px; background: #10b981; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
							<i class="fas fa-file-pdf"></i>
						</div>
						<div>
							<div style="font-weight: 800; font-size: 15px; color: #065f46;">
								<?php esc_html_e( 'Need Automated PDF Invoices & Email Attachments?', 'frontend-dashboard' ); ?>
							</div>
							<div style="font-size: 13px; color: #047857; margin-top: 2px;">
								<?php esc_html_e( 'Upgrade to Pro to generate downloadable PDF receipts, dynamic QR codes, multi-currency invoices, and automated customer email delivery.', 'frontend-dashboard' ); ?>
							</div>
						</div>
					</div>
					<a href="https://buffercode.com/plugin/frontend-dashboard/" target="_blank" style="background: #059669; color: #ffffff; font-weight: 700; font-size: 13px; padding: 9px 18px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.3);">
						<i class="fas fa-crown"></i> <?php esc_html_e( 'Explore Pro Invoice Engine', 'frontend-dashboard' ); ?>
					</a>
				</div>

			</div>
			<?php
		}

		/**
		 * Update.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {
			$validate = new FED_Validation();
			$validate->name( 'Template' )->value( isset( $request['template'] ) ? $request['template'] : '' )->required();

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}

			$settings = get_option( 'fed_payment_settings', array() );
			
			if ( ! isset( $settings['invoice'] ) || ! is_array( $settings['invoice'] ) ) {
				$settings['invoice'] = array();
			}
			if ( ! isset( $settings['invoice']['template'] ) || ! is_array( $settings['invoice']['template'] ) ) {
				$settings['invoice']['template'] = array();
			}

			$settings['invoice']['template']['default']      = fed_sanitize_text_field( $request['template'] );
			$settings['invoice']['template']['accent_color'] = isset( $request['accent_color'] ) ? fed_sanitize_text_field( $request['accent_color'] ) : '#033333';

			update_option( 'fed_payment_settings', $settings );

			wp_send_json_success( array( 'message' => __( 'Invoice Template Successfully Saved', 'frontend-dashboard' ) ) );
		}
	}

	new FEDInvoiceTemplate();
}