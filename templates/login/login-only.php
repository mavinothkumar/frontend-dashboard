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
										<?php echo fed_show_form_label( $content ); // Needs to output labels with 'block text-sm font-medium text-gray-700' ?>
										<?php echo( $content['input'] ); // Inputs should ideally have 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm' ?>
									</div>
									<?php
								}
								?>

								<div class="pt-4 text-center">
									<input type="hidden" name="submit" value="<?php echo esc_attr( $type ); ?>"/>
									<button class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
											type="submit">
										<?php
										echo wp_kses_post( __( $details['button'], 'frontend-dashboard' ) );
										?>
									</button>
								</div>
								
								<div class="pt-4 border-t border-gray-100 text-center text-sm text-gray-600">
									<?php if ( $registration ) { ?>
										<a href="<?php echo esc_url( $registration ); ?>" class="text-blue-600 hover:text-blue-500 font-medium">
											<?php
											esc_attr_e(
												'Create an account',
												'frontend-dashboard'
											);
											?>
										</a>
										<span class="mx-2 text-gray-300">|</span>
									<?php } ?>
									
									<?php if ( $forgot ) { ?>
										<a href="<?php echo esc_url( $forgot ); ?>" class="text-blue-600 hover:text-blue-500 font-medium">
											<?php esc_attr_e( 'Lost Password?', 'frontend-dashboard' ); ?>
										</a>
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
do_action( 'fed_after_login_only_form' );
