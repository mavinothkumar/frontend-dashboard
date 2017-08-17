<?php
/**
 * Developer Comment
 * translate => done
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register a custom menu page.
 */
function fed_menu() {
	add_menu_page(
		__( 'Frontend Dashboard', 'fed' ),
		__( 'Frontend Dashboard', 'fed' ),
		'manage_options',
		'fed_settings_menu',
		'fed_common_settings',
		plugins_url('common/assets/images/d.png',BC_FED_PLUGIN),
		2
	);

	add_submenu_page( 'fed_settings_menu', __( 'Dashboard Menu', 'fed' ), __( 'Dashboard Menu', 'fed' ),
		'manage_options', 'fed_dashboard_menu', 'fed_dashboard_menu' );

	add_submenu_page( 'fed_settings_menu', __( 'User Profile', 'fed' ), __( 'User Profile', 'fed' ),
		'manage_options', 'fed_user_profile', 'fed_user_profile' );

	add_submenu_page( 'fed_settings_menu', __( 'Post Fields', 'fed' ), __( 'Post Fields', 'fed' ),
		'manage_options', 'fed_post_fields', 'fed_post_fields' );

	add_submenu_page( 'fed_settings_menu', __( 'Add Profile / Post Fields', 'fed' ), __( 'Add Profile / Post Fields', 'fed' ),
		'manage_options', 'fed_add_user_profile', 'fed_add_user_profile' );

//	add_submenu_page( 'fed_settings_menu', __( 'Plugins', 'fed' ), __( 'Plugins', 'fed' ),
//		'manage_options', 'fed_plugin_pages', 'fed_plugin_pages' );

	add_submenu_page( 'fed_settings_menu', __( 'Status', 'fed' ), __( 'Status', 'fed' ),
		'manage_options', 'fed_status', 'fed_status' );

	add_submenu_page( 'fed_settings_menu', __( 'Help', 'fed' ), __( 'Help', 'fed' ),
		'manage_options', 'fed_help', 'fed_help' );

	do_action( 'fed_add_main_sub_menu' );


}

add_action( 'admin_menu', 'fed_menu' );

/**
 * Show FED Common Settings
 */
function fed_common_settings() {
	$menu            = fed_admin_dashboard_settings_menu_header();
	$menu_counter    = 0;
	$content_counter = 0;
	?>

	<div class="bc_fed container fed_tabs_container">
		<h3 class="fed_header_font_color">Dashboard Settings</h3>
		<div class="row">
			<div class="col-md-12">
				<ul class="nav nav-tabs"
					id="fed_admin_setting_tabs"
					role="tablist">

					<?php foreach ( $menu as $index => $item ) { ?>
						<li role="presentation"
							class="<?php echo ( 0 === $menu_counter ) ? 'active' : ''; ?>">
							<a href="#<?php echo $index; ?>"
							   aria-controls="<?php echo $index; ?>"
							   role="tab"
							   data-toggle="tab">
								<i class="<?php echo $item['icon_class'] ?>"></i>
								<?php _e( $item['name'], 'fed' ) ?>
							</a>
						</li>
						<?php
						$menu_counter ++;
					}
					?>
				</ul>
				<!-- Tab panes -->
				<div class="tab-content">
					<?php foreach ( $menu as $index => $item ) { ?>
						<div role="tabpanel"
							 class="tab-pane <?php echo ( 0 === $content_counter ) ? 'active' : ''; ?>"
							 id="<?php echo $index; ?>">
							<?php
							if ( function_exists( $item['callable'] ) ) {
								call_user_func( $item['callable'] );
							} else {
								echo '<div class="container fed_add_page_profile_container text-center">OOPS! You have not add the callable function, please add ' . $item['callable'] . '() to show the body container</div>';
							}
							?>
						</div>
						<?php
						$content_counter ++;
					}
					?>
				</div>
			</div>
		</div>
		<?php fed_menu_icons_popup(); ?>
	</div>
	<?php
}

/**
 * User Profile
 */
function fed_user_profile() {
	$user_profile = fed_fetch_table_rows_with_key( BC_FED_USER_PROFILE_DB, 'input_meta' );

//	var_dump( fed_fetch_user_profile());
	if ( $user_profile instanceof WP_Error ) {
		?>
		<div class="bc_fed container fed_UP_container">
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger">
						<button type="button"
								class="close"
								data-dismiss="alert"
								aria-hidden="true">&times;
						</button>
						<strong><?php echo $user_profile->get_error_message() ?></strong>
					</div>
				</div>
			</div>
		</div>
		<?php
	} else {
		fed_show_admin_profile_page( $user_profile );
	}
}

/**
 * Add / Edit User Profile
 */
function fed_add_user_profile() {
	/**
	 * Edit Input
	 */
	fed_add_new_user_input();
}

/**
 * Post Fields
 */
function fed_post_fields() {
	$post_fields = fed_fetch_table_rows_with_key( BC_FED_POST_DB, 'input_meta' );

//	var_dump( fed_fetch_user_profile());
	if ( $post_fields instanceof WP_Error ) {
		?>
		<div class="bc_fed container fed_UP_container">
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger">
						<button type="button"
								class="close"
								data-dismiss="alert"
								aria-hidden="true">&times;
						</button>
						<strong><?php echo $post_fields->get_error_message() ?></strong>
					</div>
				</div>
			</div>
		</div>
		<?php
	} else {
		fed_post_fields_layout( $post_fields );
	}
}

/**
 * Dashboard Menu
 */
function fed_dashboard_menu() {
	fed_admin_dashboard_menu();
}


/**
 * Help
 */
function fed_help() {
	fed_admin_help();
}

/**
 * Status
 */
function fed_status() {
	fed_admin_status();
}