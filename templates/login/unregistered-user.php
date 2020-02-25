<?php
/**
 * Logged in User form
 *
 * @package Frontend Dashboard.
 */

$details = fed_login_form();
do_action( 'fed_before_login_form' );
$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
?>
	<div class="bc_fed container fed_login_container">
		<?php echo fed_loader(); ?>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="fed_login_menus">
					<div class="fed_login_wrapper">
						<?php
						$hide = '';
						foreach ( $details as $key => $detail ) {
							if ( isset( $_GET['action'] ) ) {
								if ( ( 'fed_reset' == $get_payload['action'] ) && ( 'Reset Password' == $key ) ) {
									$detail['selected'] = true;
									$hide               = '';
								} elseif ( ( 'fed_forgot' == $get_payload['action'] ) && ( 'Forgot Password' == $key ) ) {
									$detail['selected'] = true;
									$hide               = '';
								} else {
									$detail['selected'] = false;
									$hide               = 'hide';
								}
							} else {
								if ( 'Reset Password' == $key ) {
									$hide = 'hide';
								}
							}
							?>
							<div class="fed_tab_menus
							<?php
							echo ( true == $detail['selected'] ) ? esc_attr( 'fed_selected' ) : '';
							echo esc_attr( $hide );
							?>
							" id="<?php echo esc_attr( $detail['menu']['id'] ); ?>">
								<?php echo esc_attr( $detail['menu']['name'] ); ?>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div class="fed_login_content">
					<?php do_action( 'fed_above_login_form' ); ?>
					<?php
					foreach ( $details as $key => $detail ) {
						if ( isset( $get_payload['action'] ) ) {
							if ( 'Reset Password' == $key && 'fed_reset' == $get_payload['action'] ) {
								$detail['selected'] = true;
							} elseif ( 'fed_forgot' == $get_payload['action'] && 'Forgot Password' == $key ) {
								$detail['selected'] = true;
							} else {
								$detail['selected'] = false;
							}
						}
						?>

						<div class="fed_tab_content 
						<?php
						echo false == $detail['selected'] ? 'hide' : '';
						?>
						"
								data-id="<?php echo esc_attr( $detail['menu']['id'] ); ?>">
							<form method="post"
									class="fed_form_post"
							>

								<?php
								fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' );

								$contents = $detail['content'];
								uasort( $contents, 'fed_sort_by_order' );
								foreach ( $contents as $content ) {
									$label = null;
									if ( ! empty( $content['extended'] ) ) {
										$extended = maybe_unserialize( $content['extended'] );
										if ( isset( $extended['label'] ) ) {
											$label = $extended['label'];
										}
									}
									?>
									<div class="form-group">
										<?php
										$content_name = ! empty( $content['name'] ) && ( null === $label ) ? '<label>' . $content['name'] . '</label>' : '';
										echo wp_kses_post( $content_name );
										?>
										<?php echo ( $content['input'] ); ?>
										<?php
										echo null !== $label ? '<label>' . esc_attr(
												$content['name']
											) . '</label>' : '';
										?>
									</div>
									<?php
								}
								?>
								<div class="form-group">
									<div class="text-center">
										<input type="hidden"
												name="submit"
												value="<?php echo esc_attr( $key ); ?>"/>
										<button class="btn btn-primary" type="submit">
											<?php echo wp_kses_post( $detail['button'] ); ?></button>
									</div>
								</div>
							</form>
							<?php do_action( 'fed_below_login_form' ); ?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_login_form' );
