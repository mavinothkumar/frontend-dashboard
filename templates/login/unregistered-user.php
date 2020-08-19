<?php
/**
 * Logged in User form
 *
 * @package Frontend Dashboard.
 */

$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
$menus       = fed_login_form();
if ( isset( $get_payload['page_type'] ) && 'reset_password' === $get_payload['page_type'] ) {
	$menu      = $menus[ $get_payload['page_type'] ]['html'];
	$page_name = 'reset_password';
	unset( $menus['login'], $menus['register'], $menus['forgot_password'] );
} else {
	$page      = fed_get_data( 'page_type', $get_payload, 'login' );
	$page_name = array_key_exists( $page, $menus ) ? $page : 'login';
	$menu      = isset( $menus[ $page_name ]['html'] ) ? $menus[ $page_name ]['html'] : false;
	unset( $menus['reset_password'] );
}

do_action( 'fed_before_login_form' );
if ( $menu ) {
	?>
	<div class="bc_fed container fed_login_container">
		<?php
		//phpcs:ignore
		echo fed_loader();
		?>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="fed_login_menus">
					<div class="fed_login_wrapper">
						<?php
						foreach ( $menus as $key => $menu_item ) {
							$is_active = $page_name === $key ? 'active' : '';
							?>
							<div class="fed_tab_menus <?php echo esc_attr( $is_active ); ?>"
									id="<?php echo esc_attr( $key ); ?>">
								<a href="
								<?php
								echo esc_url(
									add_query_arg( array(
										'page_type' => esc_attr( $key ),
									), fed_get_current_page_url() )
								);
								?>
								">
									<?php
									esc_attr_e( fed_get_data( 'label', $menu_item ), 'frontend-dashboard' );
									?>
								</a>
							</div>
							<?php
						}
						?>
					</div>
				</div>

				<div class="fed_login_content">
					<?php do_action( 'fed_above_login_form' ); ?>

					<div class="fed_tab_content">
						<form method="post" class="fed_form_post">
							<?php
							$contents = $menu['content'];
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
									$content_name = ! empty( $content['name'] ) && ( null === $label ) ? fed_show_form_label( $content ) : '';
									echo wp_kses_post( $content_name );
									?>
									<?php
									//phpcs:ignore
									echo( $content['input'] ); ?>
									<?php
									echo null !== $label ? fed_show_form_label( $content ) : '';
									?>
								</div>
								<?php
							}
							?>
							<div class="form-group">
								<div class="text-center">
									<input type="hidden"
											name="submit"
											value="<?php echo esc_attr( $page_name ); ?>"/>
									<button class="btn btn-primary" type="submit">
										<?php echo wp_kses_post( $menu['button'] ); ?></button>
								</div>
							</div>
						</form>
						<?php do_action( 'fed_below_login_form' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
do_action( 'fed_after_login_form' );
