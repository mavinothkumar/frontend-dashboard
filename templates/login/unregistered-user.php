<?php
/**
 * Unregistered User Authentication Card (Login, Register, Forgot Password Tabs)
 *
 * @package Frontend Dashboard.
 */

$get_payload = isset( $_GET ) ? fed_sanitize_text_field( wp_unslash( $_GET ) ) : array();
$menus       = fed_login_form();
if ( isset( $get_payload['page_type'] ) && 'reset_password' === $get_payload['page_type'] ) {
	$menu      = isset( $menus[ $get_payload['page_type'] ]['html'] ) ? $menus[ $get_payload['page_type'] ]['html'] : false;
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
	<div class="bc_fed fed_login_container max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans text-slate-800">
		<style>
			.fed_login_container .form-group label {
				font-size: 1.15rem;
				font-weight: 300;
				color: #334155;
				margin-bottom: 0.15rem;
			}
			.fed_login_container input.form-control:not([type="checkbox"]) {
				background-color: #eef2fa;
				border: 1px solid #e2e8f0;
				border-radius: 0.15rem;
				padding: 0.75rem 1rem;
				font-size: 1rem;
				color: #1e293b;
				width: 100%;
				box-shadow: none;
			}
			.fed_login_container input.form-control:not([type="checkbox"]):focus {
				outline: none;
				border-color: #3b82f6;
				box-shadow: 0 0 0 1px #3b82f6;
			}
			.fed_login_container .form-group:has(input[type="checkbox"]) {
				flex-direction: row;
				align-items: center;
				gap: 0.75rem;
				margin-top: 1rem;
			}
			.fed_login_container input[type="checkbox"] {
				width: 1.25rem;
				height: 1.25rem;
				border: 1px solid #94a3b8;
				border-radius: 0.15rem;
				cursor: pointer;
			}
			.fed_login_container .form-group:has(input[type="checkbox"]) label {
				font-size: 0.95rem;
				font-weight: 500;
				color: #475569;
				margin-bottom: 0;
				cursor: pointer;
			}
		</style>
		<?php echo fed_loader(); ?>
		
		<div class="flex justify-center">
			<div class="w-full max-w-md">
				<div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200/80">
					
					<!-- Tab Navigation -->
					<div class="bg-slate-50 p-3 border-b border-slate-100 flex items-center justify-around gap-1">
						<?php
						foreach ( $menus as $key => $menu_item ) {
							$is_active = $page_name === $key;
							$tab_class = $is_active
								? 'bg-[#2563eb] text-white shadow font-bold'
								: 'text-slate-500 hover:text-slate-800 hover:bg-slate-100/50 font-medium';
							?>
							<a href="<?php echo esc_url( add_query_arg( array( 'page_type' => esc_attr( $key ) ), fed_get_current_page_url() ) ); ?>"
								class="flex-1 text-center py-2.5 px-3 rounded-xl text-sm transition-all duration-200 <?php echo esc_attr( $tab_class ); ?>"
								id="<?php echo esc_attr( $key ); ?>">
								<?php esc_html_e( fed_get_data( 'label', $menu_item ), 'frontend-dashboard' ); ?>
							</a>
							<?php
						}
						?>
					</div>

					<!-- Form Content -->
					<div class="p-6 sm:p-8">
						<?php do_action( 'fed_above_login_form' ); ?>

						<div class="fed_tab_content">
							<form method="post" class="fed_form_post space-y-4">
								<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
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
									<div class="form-group flex flex-col gap-1">
										<?php
										$content_name = ! empty( $content['name'] ) && ( null === $label ) ? fed_show_form_label( $content ) : '';
										echo wp_kses_post( $content_name );
										?>
										<?php echo( $content['input'] ); ?>
										<?php echo null !== $label ? fed_show_form_label( $content ) : ''; ?>
									</div>
									<?php
								}
								?>
								<div class="pt-2 text-center">
									<input type="hidden" name="submit" value="<?php echo esc_attr( $page_name ); ?>"/>
									<button class="w-full inline-flex justify-center items-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-150" type="submit">
										<?php echo wp_kses_post( $menu['button'] ); ?>
									</button>
								</div>
							</form>
							<?php do_action( 'fed_below_login_form' ); ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<?php
}
do_action( 'fed_after_login_form' );
