<?php
/**
 * Logged in User form
 *
 * @package Frontend Dashboard.
 */
if ( isset( $_GET['action'], $_GET['key'], $_GET['login'] ) && ( 'fed_reset' === $_GET['action'] ) ) {
	$details = fed_reset_password_only();
	$type    = 'reset_password';
} else {
	$details = fed_login_only();
	$type    = 'login';
}
$registration = fed_get_registration_url();
$forgot       = fed_get_forgot_password_url();
do_action( 'fed_before_login_only_form' );
?>
	<div class="bc_fed container fed_login_container">
		<?php echo fed_loader(); ?>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?php esc_attr_e( $details['menu']['name'], 'frontend-dashboard' ); ?>
						</h3>
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
									$content_name = ! empty( $content['name'] ) ? '<label>' . $content['name'] . '</label>' : '';
									?>
									<div class="form-group">
										<?php echo fed_show_form_label( $content ); ?>
										<?php echo( $content['input'] ); ?>
									</div>
									<?php
								}
								?>

								<div class="form-group">
									<div class="text-center">
										<input type="hidden" name="submit" value="<?php echo esc_attr( $type ); ?>"/>
										<button class="btn btn-primary"
												type="submit">
											<?php
											echo wp_kses_post( __( $details['button'], 'frontend-dashboard' ) );
											?>
										</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12 padd_top_20 text-center">
										<?php if ( $registration ) { ?>
											<a href="<?php echo esc_url( $registration ); ?>">
												<?php
												esc_attr_e(
													'Create an account',
													'frontend-dashboard'
												);
												?>
											</a> |
										<?php } ?>
										<?php
										if ( $forgot ) {
											?>
											<a href="<?php echo esc_url( $forgot ); ?>">
												<?php esc_attr_e( 'Lost Password?', 'frontend-dashboard' ); ?>
											</a>
											<?php
										}
										?>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_login_only_form' );
