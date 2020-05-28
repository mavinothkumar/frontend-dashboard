<?php
/**
 * Add-ons
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Plugin Pages Menu.
 */
function fed_get_plugin_pages_menu() {
	$api = get_transient( 'fed_plugin_list_api' );
	if ( ( $api ) === false ) {
		$api = get_plugin_list();
	}
	if ( $api ) {
		set_transient( 'fed_plugin_list_api', $api, 12 * HOUR_IN_SECONDS );
		$plugins = json_decode( $api );
		?>
		<div class="bc_fed container fed_plugins">
			<div class="row  padd_top_20">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">
								<?php esc_attr_e( 'Add-Ons', 'frontend-dashboard' ); ?>
							</h3>
						</div>
						<div class="panel-body">
							<?php
							//phpcs:ignore
							echo fed_loader( 'hide', 'Please wait, its working' ) ?>
							<div class="row m-b-10">
								<div class="col-md-12">
									<a target="_blank" class="btn btn-secondary btn-lg"
											href="https://demo.frontenddashboard.com">
										<i class="fa fa-eye"></i>
										Frontend Dashboard Demo
									</a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php
									//phpcs:ignore
									echo fed_show_alert( 'fed_activation_message' ); ?>
								</div>
							</div>
							<div class="row">
								<?php
								foreach ( $plugins->plugins as $slug => $single ) {
									if ( 'Free' === $single->pricing->type ) {
										$type     = '<i class="fas fa-lock-open"></i>';
										$bg_color = '';
									} else {
										$type     = '<i class="fas fa-lock"></i>';
										$bg_color = 'bg_pro_color';
									}
									?>
									<div class="col-md-12">
										<div class="padd_5 margin_bottom_20 fed_border_2px fed_font_size_20">
											<div class="row  <?php echo esc_attr( $bg_color ); ?>">
												<div class="col-md-1">
													<img class="img-responsive" width="100px"
															src="<?php echo esc_url( $single->thumbnail ); ?>"
															alt="">
												</div>
												<div class="col-md-6">
													<div class="fed_addons_title fed_flex_space_between">
														<div class="fed_p_b_10">
															<?php
															echo esc_attr( $single->title );
															?>
															<small>
																(
																v
																<?php
																if ( is_plugin_active( $single->directory ) ) {
																	echo esc_attr( constant( $single->id . '_VERSION' ) );
																} else {
																	echo esc_attr( $single->version );
																}
																?>
																)
															</small>
														</div>
														<div class="">
															<div class="fed_p_l_10">
																<?php echo wp_kses_post( $type ); ?>
															</div>
														</div>
													</div>
													<div class="fed_addons_description">
														<p>
															<?php
															echo esc_attr( wp_trim_words( $single->description, 100 ) );
															?>
														</p>
													</div>
												</div>
												<div class="col-md-5">
													<div class="fed_plugin_link">
														<div class="fed_flex_space_between">
															<?php
															if ( is_plugin_active( $single->directory ) ) {
																?>
																<button class="btn btn-info">
																	<i class="fa fa-check"
																			aria-hidden="true"></i>
																	Installed
																</button>
																<?php
																if ( $single->version > constant( $single->id . '_VERSION' ) ) {
																	?>
																	<button class="btn btn-danger">
																		<i class="fa fa-refresh"
																				aria-hidden="true"></i>
																		Update
																	</button>
																	<?php
																}
															} else {
																if ( 'Free' === $single->pricing->type ) {
																	$path = WP_PLUGIN_DIR . '/' . $single->install_slug;
																	if ( is_dir( $path ) ) {
																		?>
																		<form method="post"
																				action="<?php echo esc_url( fed_get_form_action( 'fed_request' ) . '&fed_action_hook=FEDInstallAddons@activate' ); ?>">
																			<?php fed_wp_nonce_field(); ?>
																			<input type="hidden" name="plugin_name"
																					value="<?php echo esc_attr( $single->install_slug . '/' . $single->install_slug . '.php' ); ?>"/>
																			<button type="submit"
																					class="btn btn-primary">
																				Activate
																			</button>
																		</form>
																		<?php
																	} else {
																		?>
																		<form method="post"
																				class="fed_ajax_plugin_install"
																				action="<?php echo esc_url( fed_get_ajax_form_action( 'fed_api_ajax_request' ) . '&fed_action_hook=FEDInstallAddons@install' ); ?>">
																			<?php
																			wp_nonce_field( 'updates' );
																			?>
																			<input type="hidden" name="slug"
																					value="<?php echo esc_attr( isset( $single->install_slug ) ? $single->install_slug : $slug ); ?>">
																			<button type="submit"
																					class="btn btn-primary">
																				<i class="fa fa-download"
																						aria-hidden="true"></i>
																				<?php
																				esc_attr_e( 'Install & Activate',
																					'frontend-dashboard' )
																				?>
																			</button>
																		</form>
																		<?php
																	}
																}
																if ( 'Pro' === $single->pricing->type ) {
																	?>
																	<?php
																	foreach ( $single->pricing->amount as $atype => $amount ) {
																		?>
																		<form method="post"
																				action="<?php echo esc_url( $single->pricing->purchase_url ); ?>">
																			<input type='hidden' name='redirect_url'
																					value="<?php echo esc_url( fed_current_page_url() ); ?>"/>
																			<input type='hidden' name='domain'
																					value="<?php echo esc_attr( fed_get_domain_name() ); ?>"/>
																			<input type='hidden'
																					name='contact_email'
																					value="<?php echo esc_attr( fed_get_admin_email() ); ?>"/>
																			<input type='hidden' name='plugin_name'
																					value='<?php echo esc_attr( $slug ); ?>'/>
																			<input type='hidden' name='amount'
																					value='<?php echo esc_attr( $amount->amount ); ?>'/>
																			<input type='hidden' name='plan_type'
																					value=<?php echo esc_attr( $atype ); ?>/>
																			<button type="submit"
																					class="btn btn-primary">
																				<i class="fa fa-shopping-cart"
																						aria-hidden="true"></i>
																				<?php echo esc_attr( 'Buy ' . $amount->name . ' ' . $single->pricing->currency . $amount->amount ); ?>
																			</button>
																		</form>
																		<?php
																	}
																	?>
																	<?php
																}
															}
															?>
															<a href="<?php echo esc_url( $single->download_url ); ?>">
																<button class="btn btn-warning">
																	<i class="fa fa-eye"
																			aria-hidden="true"></i>
																	View
																</button>
															</a>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<?php
	} else {
		?>
		<div class="bc_fed container fed_plugins">
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>
					<?php
					esc_attr_e( 'Sorry there is some issue in internet connectivity.', 'frontend-dashboard' );
					?>
				</strong>
			</div>
			<?php
			//phpcs:ignore
			echo fed_loader( '' );
			?>
		</div>
		<?php
	}
}