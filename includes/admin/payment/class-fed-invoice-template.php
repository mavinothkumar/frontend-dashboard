<?php
/**
 * Invoice Template.
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
		 * Template.
		 */
		public function template() {
			$templates = apply_filters( 'fed_invoice_template', array() );

			if ( $templates && count( $templates ) > 0 ) {
				$settings = get_option( 'fed_payment_settings' );
				?>
				<form method="post" class="fed_ajax"
						action="
						<?php
						echo esc_url(
							add_query_arg(
								array( 'fed_action_hook' => 'FEDInvoiceTemplate@update' ),
								fed_get_ajax_form_action( 'fed_ajax_request' )
							)
						);
						?>
						">
					<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
					<div class="row">
						<?php
						foreach ( $templates as $index => $template ) {
							?>
							<div class="col-md-4">
								<div class="panel panel-secondary-heading">
									<div class="panel-heading">
										<h3 class="panel-title"><?php echo esc_attr( $template['name'] ); ?></h3>
									</div>
									<div class="panel-body">
										<img alt="" class="img-responsive"
												src="<?php echo esc_url( $template['image'] ); ?>">
										<div class="fed_flex_center">
											<label>
												<input class="radio" type="radio" name="template"
														value="template-1"
													<?php echo isset( $settings['invoice']['template']['default'] ) && 'template-1' === $settings['invoice']['template']['default'] ? 'checked' : ''; ?>/>
											</label>
										</div>
										<div class="text-center">
											<strong>
												<?php
												esc_attr_e(
													'Select to make default', 'frontend-dashboard'
												);
												?>
											</strong>
										</div>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
					<button class="btn btn-primary" type="submit">
						<?php esc_attr_e( 'Submit', 'frontend-dashboard' ); ?>
					</button>
				</form>
				<?php
			}
			else {
				$template = new FEDMPPRO();
				$template->pro();
			}
		}

		/**
		 * Update.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {
			$validate = new FED_Validation();
			$validate->name( 'Template' )->value( $request['template'] )->required();

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}
			$settings                                   = get_option( 'fed_payment_settings' );
			$settings['invoice']['template']['default'] = fed_sanitize_text_field( $request['template'] );
			update_option( 'fed_payment_settings', $settings );

			wp_send_json_success( array( 'message' => __( 'Invoice Template Successfully Updated' ) ) );

		}
	}
}