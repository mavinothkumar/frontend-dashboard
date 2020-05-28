<?php
/**
 * Status.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! function_exists( 'fed_get_status_menu' ) ) {
	/**
	 * Get Table Status
	 */
	function fed_get_status_menu() {
		global $wp_version;
		global $wpdb;

		$table   = $wpdb->prefix . 'options';
		$options = $wpdb->get_results( "SELECT * from {$table} WHERE option_name LIKE 'fed%' " );

		/**
		 * Login
		 */
		$fed_login           = get_option( 'fed_admin_login', array() );
		$login_settings      = fed_enable_disable( isset( $fed_login['settings']['fed_login_url'] ) ? true : false );
		$redirect_login_url  = fed_enable_disable(
			isset( $fed_login['settings']['fed_redirect_login_url'] ) ? true : false
		);
		$redirect_logout_url = fed_enable_disable(
			isset( $fed_login['settings']['fed_redirect_logout_url'] ) ? true : false
		);
		$dashboard           = fed_enable_disable(
			isset( $fed_login['settings']['fed_dashboard_url'] ) ? true : false
		);

		/**
		 * Post
		 */
		$fed_post           = get_option( 'fed_cp_admin_settings', array() );
		$fed_post_settings  = fed_enable_disable( isset( $fed_post['post']['settings'] ) ? true : false );
		$fed_post_dashboard = fed_enable_disable( isset( $fed_post['post']['dashboard'] ) ? true : false );
		$fed_post_menu      = fed_enable_disable( isset( $fed_post['post']['menu'] ) ? true : false );
		$post_permissions   = fed_enable_disable( isset( $fed_post['post']['permissions'] ) ? true : false );

		/**
		 * User Profile Layout
		 */
		$fed_upl          = get_option( 'fed_admin_settings_upl', array() );
		$fed_upl_settings = fed_enable_disable( isset( $fed_upl['settings'] ) ? true : false );

		$sql       = "SHOW TABLES LIKE '{$wpdb->prefix}fed%'";
		$db_tables = $wpdb->get_results( $sql );

		?>
		<div class="bc_fed container">
			<div class="row padd_top_20">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title"><?php esc_attr_e( 'Status', 'frontend-dashboard' ); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													Frontend Dashboard->User Profile Layout
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<tr>
															<td class="fed_header_font_color">
																<?php
																esc_attr_e( 'Settings', 'frontend-dashboard' );
																?>
															</td>
															<td><?php echo wp_kses_post( $fed_upl_settings ); ?></td>
														</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													<?php
													esc_attr_e(
														'Versions',
														'frontend-dashboard'
													)
													?>
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<tr>
															<td class="fed_header_font_color">PHP Version</td>
															<td><?php echo esc_attr( PHP_VERSION ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">WordPress Version</td>
															<td><?php echo esc_attr( $wp_version ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">Plugin Version</td>
															<td><?php echo esc_attr( BC_FED_PLUGIN_VERSION ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">Plugins & Ads-ons</td>
															<td>
																<?php echo esc_attr( fed_plugin_versions() ); ?>
															</td>
														</tr>

														<?php do_action( 'fed_admin_menu_status_version_below' ); ?>

														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">Frontend Dashboard->Post</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>
																<?php
																esc_attr_e(
																	'Name',
																	'frontend-dashboard'
																);
																?>
															</th>
															<th>
																<?php
																esc_attr_e(
																	'Status',
																	'frontend-dashboard'
																);
																?>
															</th>
														</tr>
														</thead>
														<tbody>
														<tr>
															<td class="fed_header_font_color">Settings</td>
															<td><?php echo wp_kses_post( $fed_post_settings ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">Dashboard Settings
															</td>
															<td><?php echo wp_kses_post( $fed_post_dashboard ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">Menu</td>
															<td><?php echo wp_kses_post( $fed_post_menu ); ?></td>
														</tr>
														<tr>
															<td class="fed_header_font_color">Permissions</td>
															<td><?php echo wp_kses_post( $post_permissions ); ?></td>
														</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													<?php
													esc_attr_e(
														'PHP Extensions',
														'frontend-dashboard'
													)
													?>
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>Table Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<tr>
															<td class="fed_header_font_color">cURL</td>
															<td>
																<?php
																echo wp_kses_post(
																	fed_enable_disable(
																		fed_check_extension_loaded( 'cURL' )
																	)
																);
																?>
															</td>
														</tr>
														<tr>
															<td class="fed_header_font_color">JSON</td>
															<td>
																<?php
																echo wp_kses_post(
																	fed_enable_disable(
																		fed_check_extension_loaded( 'JSON' )
																	)
																);
																?>
															</td>
														</tr>
														<tr>
															<td class="fed_header_font_color">OpenSSL</td>
															<td>
																<?php
																echo wp_kses_post(
																	fed_enable_disable(
																		fed_check_extension_loaded( 'OpenSSL' )
																	)
																);
																?>
															</td>
														</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													<?php
													esc_attr_e(
														'Database',
														'frontend-dashboard'
													)
													?>
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>Table Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<?php foreach ( fed_get_table_status() as $table ) { ?>
															<tr>
																<td class="fed_header_font_color">
																	<?php
																	echo esc_attr( $table['title'] ) . ' ( ' . esc_attr( $table['plugin_name'] ) . ' )';
																	?>
																</td>
																<td><?php echo wp_kses_post( $table['status'] ); ?></td>
															</tr>
														<?php } ?>
														<?php do_action( 'fed_admin_menu_status_database_below' ); ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">Frontend Dashboard->Login</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th colspan="2">
																<span class="fed_header_font_color">Settings</span>
															</th>
														</tr>
														<tr>
															<th>Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<tr>
															<td>Login Page URL</td>
															<td><?php echo wp_kses_post( $login_settings ); ?></td>
														</tr>
														<tr>
															<td>Redirect After Logged in URL</td>
															<td><?php echo wp_kses_post( $redirect_login_url ); ?></td>
														</tr>
														<tr>
															<td>Redirect After Logged out URL</td>
															<td><?php echo wp_kses_post( $redirect_logout_url ); ?></td>
														</tr>
														<tr>
															<td>Dashboard</td>
															<td><?php echo wp_kses_post( $dashboard ); ?></td>
														</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php do_action( 'fed_admin_menu_status_below' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>


			<div class="row padd_top_20">
				<div class="col-md-12">
					<div class="panel panel-danger">
						<div class="panel-heading">
							<h3 class="panel-title"><?php esc_attr_e( 'Delete or Empty', 'frontend-dashboard' ); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													<?php esc_attr_e( 'Delete The Table', 'frontend-dashboard' ); ?>
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>
																<?php
																esc_attr_e(
																	'Table Name',
																	'frontend-dashboard'
																);
																?>
															</th>
															<th>
																<?php
																esc_attr_e(
																	'Action',
																	'frontend-dashboard'
																);
																?>
															</th>
														</tr>
														</thead>
														<tbody>
														<?php
														foreach ( $db_tables as $index => $table ) {
															foreach ( $table as $table_name ) {
																?>
																<tr>
																	<td class="fed_header_font_color">
																		<?php echo esc_attr( $table_name ); ?>
																	</td>
																	<td>
																		<form method="post" class="fed_ajax"
																				action="<?php echo esc_url(
																					fed_get_ajax_form_action(
																						'fed_status_delete_table'
																					)
																				); ?>">
																			<?php
																			fed_wp_nonce_field(
																				'fed_nonce',
																				'fed_nonce'
																			);
																			?>
																			<input type="hidden" name="table_name"
																					value="<?php echo esc_attr(
																						$table_name
																					); ?>"/>
																			<button type="submit"
																					class="fed_is_delete btn btn-danger fed_no_background fed_red_color fed_no_p fed_no_m">
																				<i class="fa fa-trash"></i>
																			</button>
																		</form>
																	</td>
																</tr>
																<?php
															}
														}
														?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="col-md-12">
										<div class="panel panel-primary">
											<div class="panel-heading">
												<h3 class="panel-title">
													<?php
													esc_attr_e(
														'Empty The Table',
														'frontend-dashboard'
													)
													?>
												</h3>
											</div>
											<div class="panel-body">
												<div class="table-responsive">
													<table class="table table-hover">
														<thead>
														<tr>
															<th>Table Name</th>
															<th>Status</th>
														</tr>
														</thead>
														<tbody>
														<?php
														foreach ( $db_tables as $index => $table ) {
															foreach ( $table as $table_name ) {
																?>
																<tr>
																	<td class="fed_header_font_color">
																		<?php echo esc_attr( $table_name ); ?>
																	</td>
																	<td>
																		<form method="post" class="fed_ajax"
																				action="<?php echo esc_url(
																					fed_get_ajax_form_action(
																						'fed_status_empty_table'
																					)
																				); ?>">
																			<?php
																			fed_wp_nonce_field(
																				'fed_nonce',
																				'fed_nonce'
																			);
																			?>
																			<input type="hidden" name="table_name"
																					value="<?php echo esc_attr(
																						$table_name
																					); ?>"/>
																			<button type="submit"
																					class="fed_is_delete btn btn-danger fed_no_background fed_red_color fed_no_p fed_no_m">
																				<i class="fas fa-times-circle"></i>
																			</button>
																		</form>
																	</td>
																</tr>
																<?php
															}
														}
														?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>
								</div>
								<?php do_action( 'fed_admin_menu_delete_status_below' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row padd_top_20">
				<div class="col-md-12">
					<div class="panel panel-danger">
						<div class="panel-heading">
							<h3 class="panel-title"><?php esc_attr_e( 'Options', 'frontend-dashboard' ); ?></h3>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<h3 class="panel-title">
												<?php esc_attr_e( 'Delete The Options', 'frontend-dashboard' ); ?>
											</h3>
										</div>
										<div class="panel-body">
											<div class="row m-b-10">
												<div class="col-md-12">
													<div class="text-right">
														<form method="post" class="fed_ajax"
																action="<?php echo esc_url(
																	fed_get_ajax_form_action(
																		'fed_status_delete_all_option'
																	)
																); ?>">
															<?php
															fed_wp_nonce_field(
																'fed_nonce',
																'fed_nonce'
															);
															?>
															<button type="submit"
																	class="fed_is_delete btn btn-danger">
																<i class="fa fa-trash"></i>
																<?php
																esc_attr_e(
																	'Delete all Options',
																	'frontend-dashboard'
																)
																?>
															</button>
														</form>
													</div>
												</div>
											</div>
											<div class="row">
												<?php
												foreach ( $options as $index => $option ) {
													?>
													<div class="col-md-4">
														<div class="fed_flex_space_between bg_secondary fed_white_font padd_5 m-b-10">
															<div class="">
																<?php echo esc_attr( $option->option_name ); ?>
															</div>
															<div class="">
																<form method="post" class="fed_ajax"
																		action="<?php echo esc_url(
																			fed_get_ajax_form_action(
																				'fed_status_delete_option'
																			)
																		); ?>">
																	<?php
																	fed_wp_nonce_field(
																		'fed_nonce',
																		'fed_nonce'
																	);
																	?>
																	<input type="hidden" name="option_id"
																			value="<?php echo esc_attr(
																				$option->option_id
																			); ?>"/>
																	<button type="submit"
																			class="fed_is_delete btn btn-warning fed_no_background fed_red_color fed_no_p fed_no_m">
																		<i class="fa fa-trash"></i>
																	</button>
																</form>
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
					</div>
				</div>
			</div>

			<div class="row padd_top_20">
				<div class="col-md-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">Log File</h3>
						</div>
						<div class="panel-body">
							<object data="<?php echo esc_url( plugins_url( 'log/dashboard.log', BC_FED_PLUGIN ) ); ?>"
									width="1000"
									height="700">
								Not supported
							</object>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
