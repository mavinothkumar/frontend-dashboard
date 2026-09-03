<?php
/**
 * Logged in User form
 *
 * @package Frontend Dashboard.
 */

$details = fed_register_only();
$login   = fed_get_login_url();

do_action( 'fed_before_register_only_form' );
?>
	<div class="bc_fed fed_login_container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 font-sans text-gray-800">
		<?php echo fed_loader(); ?>
		<div class="flex justify-center">
			<div class="w-full max-w-md">
				<div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
					
					<div class="bg-blue-600 px-6 py-4">
						<h3 class="text-white text-lg font-semibold tracking-wide">
							<?php esc_attr_e( $details['menu']['name'], 'frontend-dashboard' ); ?>
						</h3>
					</div>
					
					<div class="p-6">
						<div class="fed_tab_content" data-id="<?php echo esc_attr( $details['menu']['id'] ); ?>">
							<form method="post" class="fed_form_post space-y-5">
								<?php
								$contents = $details['content'];
								uasort( $contents, 'fed_sort_by_order' );

								foreach ( $contents as $content ) {
									?>
									<div class="form-group flex flex-col gap-1">
										<?php
										//phpcs:ignore
										echo fed_show_form_label( $content );
										?>
										<?php
										//phpcs:ignore
										echo( $content['input'] );

										do_action( 'fed_register_below_form_field', $content['input_meta'], $content );
										?>
									</div>
									<?php
								}
								?>
								
								<div class="pt-4 text-center">
									<input type="hidden" name="submit" value="register"/>
									<button class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" type="submit">
										<?php
										echo wp_kses_post( __( $details['button'], 'frontend-dashboard' ) );
										?>
									</button>
								</div>
								
								<?php if ( $login ) { ?>
									<div class="pt-4 border-t border-gray-100 text-center text-sm text-gray-600">
										<a href="<?php echo esc_url( $login ); ?>" class="text-blue-600 hover:text-blue-500 font-medium">
											<?php
											esc_attr_e(
												'Already have an account?', 'frontend-dashboard'
											);
											?>
										</a>
									</div>
								<?php } ?>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_register_only_form' );
