<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo fed_loader();
		?>
		<div class="flex justify-center">
			<div class="w-full max-w-md sm:max-w-xl">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo fed_get_brand_logo_html( array( 'context' => 'register_only' ) );
				?>
				<div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
					
					<div class="bg-blue-600 px-6 py-4">
						<h3 class="text-white text-lg font-semibold tracking-wide">
							<?php echo esc_html( $details['menu']['name'] ); ?>
						</h3>
					</div>
					
					<div class="p-6">
						<div class="fed_tab_content" data-id="<?php echo esc_attr( $details['menu']['id'] ); ?>">
							<form method="post" class="fed_form_post space-y-5">
								<div class="grid grid-cols-12 gap-4">
								<?php
								$contents = $details['content'];
								uasort( $contents, 'fed_sort_by_order' );

								foreach ( $contents as $content ) {
									$width_class = fed_get_field_width_class( $content );
									?>
									<div class="form-group flex flex-col gap-1 <?php echo esc_attr( $width_class ); ?>">
										<?php
										//phpcs:ignore
										echo fed_show_form_label( $content );
										?>
										<?php
										//phpcs:ignore
										echo( $content['input'] );
										?>
										<?php if ( ! empty( $content['tooltip_body'] ) ) : ?>
											<p class="text-xs text-slate-500 mt-0.5 m-0"><?php echo esc_html( $content['tooltip_body'] ); ?></p>
										<?php endif; ?>
										<?php
										do_action( 'fed_register_below_form_field', $content['input_meta'], $content );
										?>
									</div>
									<?php
								}
								?>
								</div>
								
								<div class="pt-4 text-center">
									<input type="hidden" name="submit" value="register"/>
									<button class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" type="submit">
										<?php
										echo esc_html( $details['button'] );
										?>
									</button>
								</div>
								
								<?php if ( $login ) { ?>
									<div class="pt-4 border-t border-gray-100 text-center text-sm text-gray-600">
										<a href="<?php echo esc_url( $login ); ?>" class="text-blue-600 hover:text-blue-500 font-medium">
											<?php
											esc_attr_e(
												'Already have an account?',
												'frontend-dashboard'
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
