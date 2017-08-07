<?php

function fed_admin_status() {
	global $wp_version;
	global $wpdb;
	/**
	 * Check all table exists
	 */
	$user_profile = $wpdb->prefix . BC_FED_USER_PROFILE_DB;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$user_profile'" ) != $user_profile ) {
		$user_profile = fed_enable_disable( false );
	} else {
		$user_profile = fed_enable_disable( true );
	}

	$menu = $wpdb->prefix . BC_FED_MENU_DB;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$menu'" ) != $menu ) {
		$menu = fed_enable_disable( false );
	} else {
		$menu = fed_enable_disable( true );
	}

	$post = $wpdb->prefix . BC_FED_POST_DB;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '$post'" ) != $post ) {
		$post = fed_enable_disable( false );
	} else {
		$post = fed_enable_disable( true );
	}

	/**
	 * Login
	 */
	$fed_login           = get_option( 'fed_admin_login', array() );
	$login_settings      = isset( $fed_login['settings']['fed_login_url'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$redirect_login_url  = isset( $fed_login['settings']['fed_redirect_login_url'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$redirect_logout_url = isset( $fed_login['settings']['fed_redirect_logout_url'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$dashboard           = isset( $fed_login['settings']['fed_dashboard_url'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );

	/**
	 * Post
	 */
	$fed_post             = get_option( 'fed_admin_settings_post', array() );
	$fed_post_settings    = isset( $fed_post['settings'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$fed_post_dashboard   = isset( $fed_post['dashboard'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$fed_post_menu        = isset( $fed_post['menu'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );
	$fed_post_permissions = isset( $fed_post['permissions'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );

	/**
	 * User Profile Layout
	 */
	$fed_upl          = get_option( 'fed_admin_settings_upl', array() );
	$fed_upl_settings = isset( $fed_upl['settings'] ) ? fed_enable_disable( true ) : fed_enable_disable( false );


	?>
	<div class="bc_fed container">
		<h3 class="fed_header_font_color">Status</h3>
		<div class="row">

			<div class="col-md-4">
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
									<td class="fed_header_font_color">Settings</td>
									<td><?php echo $fed_upl_settings; ?></td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Versions</h3>
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
									<td><?php echo PHP_VERSION; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">WordPress Version</td>
									<td><?php echo $wp_version; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Plugin Version</td>
									<td><?php echo BC_FED_PLUGIN_VERSION; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Plugin Type</td>
									<td>
										<?php echo fed_plugin_versions() ?>
									</td>
								</tr>
								
								<?php do_action( 'fed_admin_menu_status_version_below') ?>
								
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Frontend Dashboard->Post</h3>
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
									<td class="fed_header_font_color">Settings</td>
									<td><?php echo $fed_post_settings; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Dashboard Settings</td>
									<td><?php echo $fed_post_dashboard; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Menu</td>
									<td><?php echo $fed_post_menu; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Permissions</td>
									<td><?php echo $fed_post_permissions; ?></td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">PHP Extensions</h3>
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
										echo fed_enable_disable( fed_check_extension_loaded( 'cURL' ) );
										?>
									</td>
								</tr>
								<tr>
									<td class="fed_header_font_color">JSON</td>
									<td>
										<?php
										echo fed_enable_disable( fed_check_extension_loaded( 'JSON' ) );
										?>
									</td>
								</tr>
								<tr>
									<td class="fed_header_font_color">OpenSSL</td>
									<td>
										<?php
										echo fed_enable_disable( fed_check_extension_loaded( 'OpenSSL' ) );
										?>
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Database</h3>
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
									<td class="fed_header_font_color">Post</td>
									<td><?php echo $post; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">User Profile</td>
									<td><?php echo $user_profile; ?></td>
								</tr>
								<tr>
									<td class="fed_header_font_color">Menu</td>
									<td><?php echo $menu; ?></td>
								</tr>
								<?php do_action( 'fed_admin_menu_status_database_below' ) ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
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
									<td><?php echo $login_settings; ?></td>
								</tr>
								<tr>
									<td>Redirect After Logged in URL</td>
									<td><?php echo $redirect_login_url; ?></td>
								</tr>
								<tr>
									<td>Redirect After Logged out URL</td>
									<td><?php echo $redirect_logout_url ?></td>
								</tr>
								<tr>
									<td>Dashboard</td>
									<td><?php echo $dashboard; ?></td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<?php do_action( 'fed_admin_menu_status_below' ); ?>

		</div>
	</div>
	<?php
}