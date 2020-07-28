<?php
/**
 * Forgot Password only
 *
 * @package Frontend Dashboard.
 */

$details  = fed_forgot_password_only();
$register = fed_get_registration_url();
$login    = fed_get_login_url();

do_action( 'fed_before_forgot_password_only_form' );
?>
	<div class="bc_fed container fed_login_container">
		<?php
		//phpcs:ignore
		echo fed_loader();
		?>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?php esc_attr_e( $details['menu']['name'],
								'frontend-dashboard' ); ?></h3>
					</div>
					<div class="panel-body">
						<div class="fed_tab_content"
								data-id="<?php echo esc_attr( $details['menu']['id'] ); ?>">
							<form method="post"
									class="fed_form_post"
							>
								<?php
								$contents = $details['content'];
								uasort( $contents, 'fed_sort_by_order' );
								foreach ( $contents as $content ) {
									?>
									<div class="form-group">
										<label>
											<?php
											//phpcs:ignore
											echo wp_kses_post( __( $content['name'], 'frontend-dashboard' ) );
											?>
										</label>
										<?php
										//phpcs:ignore
										echo( $content['input'] );
										?>
									</div>
									<?php
								}
								?>
								<div class="row">
									<div class="col-md-3"></div>
									<div class="col-md-9">
										<input type="hidden"
												name="submit"
												value="forgot_password"/>
										<button class="btn btn-primary" type="submit">
											<?php esc_attr_e( $details['button'], 'frontend-dashboard' ); ?>
										</button>
									</div>

									<?php if ( $login ) { ?>
										<div class="col-md-6 padd_top_20 text-center">
											<a href="<?php echo esc_url( $login ); ?>">
												<?php
												esc_attr_e(
													'Already have an account?', 'frontend-dashboard'
												);
												?>
											</a>
										</div>
									<?php } ?>

									<?php if ( $register ) { ?>
										<div class="col-md-6 padd_top_20 text-center">
											<a href="<?php echo esc_url( $register ); ?>">
												<?php
												esc_attr_e(
													'Create an account?', 'frontend-dashboard'
												);
												?>
											</a>
										</div>
									<?php } ?>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_forgot_password_only_form' );
