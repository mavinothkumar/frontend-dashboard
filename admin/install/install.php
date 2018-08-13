<?php

add_action( 'admin_init', 'fed_upgrade' );
function fed_upgrade() {

	$new_version = BC_FED_PLUGIN_VERSION;
	$old_version = get_option( 'fed_plugin_version', '0' );

	if ( $old_version == $new_version ) {
		return;
	}

	do_action( 'fed_upgrade_action', $new_version, $old_version );

	update_option( 'fed_plugin_version', $new_version );
}

add_action( 'fed_upgrade_action', 'fed_upgrade_actions', 10, 2 );
function fed_upgrade_actions( $new_version, $old_version ) {
	fed_plugin_activation();
	fed_plugin_data();
	fed_plugin_meta_data();
	fed_next_updates();
}

/**
 * Merge post to custom post options to handle everything globally
 */
function fed_next_updates() {
	$cp_admin_settings = get_option( 'fed_cp_admin_settings', array() );
	if ( ! isset( $cp_admin_settings['post'] ) ) {
		$admin_settings = array( 'post' => get_option( 'fed_admin_settings_post', array() ) );
		$merge          = array_merge( $cp_admin_settings, $admin_settings );
		update_option( 'fed_cp_admin_settings', $merge );
	}
}

/**
 * Plugin Activation.
 */
function fed_plugin_activation() {

	global $wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$user_profile_table = $wpdb->prefix . BC_FED_USER_PROFILE_DB;
	$menu_table         = $wpdb->prefix . BC_FED_MENU_DB;
	$post_table         = $wpdb->prefix . BC_FED_POST_DB;


	$charset_collate = $wpdb->get_charset_collate();

	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$user_profile_table}'" ) != $user_profile_table ) {
		$user_profile = "CREATE TABLE `" .$user_profile_table ."` (
		  id BIGINT(20) NOT NULL AUTO_INCREMENT,
		  input_meta char(32) NOT NULL,
		  label_name VARCHAR(255) NOT NULL,
		  input_order BIGINT(20) NOT NULL,
		  show_register VARCHAR(255) NOT NULL DEFAULT 'Disable',
		  show_dashboard VARCHAR(255) NOT NULL DEFAULT 'Disable',
		  show_user_profile VARCHAR(255) NOT NULL DEFAULT 'Enable',
		  is_required VARCHAR(255) NOT NULL DEFAULT 'false',
		  input_type VARCHAR(255) NOT NULL,
		  placeholder VARCHAR(255) NOT NULL,
		  class_name VARCHAR(255) NOT NULL,
		  id_name VARCHAR(255) NOT NULL,
		  input_step VARCHAR(255) NOT NULL,
		  input_min VARCHAR(255) NOT NULL,
		  input_max VARCHAR(255) NOT NULL,
		  input_row VARCHAR(255) NOT NULL,
		  is_tooltip VARCHAR(50) NOT NULL DEFAULT 'no',
		  tooltip_title VARCHAR(255) NOT NULL,
		  tooltip_body VARCHAR(255) NOT NULL,
		  input_value TEXT NOT NULL,
		  user_role TEXT NOT NULL,
		  input_location VARCHAR(255) NOT NULL,
		  extra VARCHAR(255) NOT NULL DEFAULT 'yes',
		  menu VARCHAR(255) NOT NULL DEFAULT 'profile',
		  extended TEXT NOT NULL,
		  PRIMARY KEY  (id),
  		  UNIQUE KEY input_meta (input_meta)
		  ) $charset_collate;";

		dbDelta( $user_profile );
	}
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$post_table}'" ) != $post_table ) {
		$post = "CREATE TABLE `" .$post_table ."` (
		  id BIGINT(20) NOT NULL AUTO_INCREMENT,
		  input_meta char(32) NOT NULL,
		  label_name VARCHAR(255) NOT NULL,
		  input_order BIGINT(20) NOT NULL,
		  is_required VARCHAR(255) NOT NULL DEFAULT 'false',
		  input_type VARCHAR(255) NOT NULL,
		  placeholder VARCHAR(255) NOT NULL,
		  class_name VARCHAR(255) NOT NULL,
		  id_name VARCHAR(255) NOT NULL,
		  input_step VARCHAR(255) NOT NULL,
		  input_min VARCHAR(255) NOT NULL,
		  input_max VARCHAR(255) NOT NULL,
		  input_row VARCHAR(255) NOT NULL,
		  is_tooltip VARCHAR(50) NOT NULL DEFAULT 'no',
		  tooltip_title VARCHAR(255) NOT NULL,
		  tooltip_body VARCHAR(255) NOT NULL,
		  post_type VARCHAR(255) NOT NULL,
		  input_value TEXT NOT NULL,
		  user_role TEXT NOT NULL,
		  input_location VARCHAR(255) NOT NULL,
		  extended TEXT NOT NULL,
		  PRIMARY KEY  (id),
  		  UNIQUE KEY input_meta (input_meta)
		  ) $charset_collate;";
		dbDelta( $post );
	}
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$menu_table}'" ) != $menu_table ) {
		$menu = "CREATE TABLE `".$menu_table ."` (
		  id BIGINT(20) NOT NULL AUTO_INCREMENT,
		  menu_slug char(32) NOT NULL,
		  menu VARCHAR (255) NOT NULL,
		  menu_order BIGINT(20) NOT NULL,
		  menu_image_id VARCHAR (255) NOT NULL,
		  show_user_profile VARCHAR (255) NOT NULL,
		  extra VARCHAR(255) NOT NULL DEFAULT 'yes',
		  user_role TEXT NOT NULL,
		  extended TEXT NOT NULL,
		  PRIMARY KEY  (id),
  		  UNIQUE KEY menu_slug (menu_slug)
		  ) $charset_collate;";

		dbDelta( $menu );
	}

	update_option( 'fed_plugin_version', BC_FED_PLUGIN_VERSION );

}

/**
 * Plugin Data
 */
function fed_plugin_data() {
	global $wpdb;
	$fed_get_user_roles = array_keys( fed_get_user_roles() );
	$profile_data       = fed_get_user_profile_default_meta_values();

	$profile_table = $wpdb->prefix . BC_FED_USER_PROFILE_DB;
	$menu_table    = $wpdb->prefix . BC_FED_MENU_DB;

	$profile_count = $wpdb->get_var( "SELECT COUNT(*) FROM $profile_table" );
	$menu_count    = $wpdb->get_var( "SELECT COUNT(*) FROM $menu_table" );


	if ( $profile_count <= 0 ) {
		foreach ( $profile_data as $datum ) {
			$wpdb->insert(
				$profile_table,
				$datum
			);
		}
	}

	if ( $menu_count <= 0 ) {
		$wpdb->insert(
			$menu_table,
			array(
				'menu_slug'         => 'profile',
				'menu'              => 'Profile',
				'menu_order'        => 3,
				'menu_image_id'     => 'fa fa-user',
				'show_user_profile' => 'Enable',
				'extra'             => 'no',
				'user_role'         => serialize( $fed_get_user_roles )
			)
		);
	}

}

//register_activation_hook( BC_FED_PLUGIN, 'fed_plugin_activation' );
//register_activation_hook( BC_FED_PLUGIN, 'fed_plugin_data' );
//register_activation_hook( BC_FED_PLUGIN, 'fed_plugin_meta_data' );


/**
 * User Profile Default meta values.
 *
 * @return array
 */
function fed_get_user_profile_default_meta_values() {
	$fed_get_user_roles = array_keys( fed_get_user_roles() );
	$default_values     = array(
		'user_login'            => array(
			'input_order'       => 5,
			'show_register'     => 'Enable',
			'show_dashboard'    => 'Enable',
			'show_user_profile' => 'Disable',
			'is_required'       => 'true',
			'label_name'        => 'User Name',
			'input_type'        => 'single_line',
			'input_meta'        => 'user_login',
			'placeholder'       => 'User Name',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',

		),
		'user_pass'             => array(
			'input_order'       => 9,
			'show_register'     => 'Enable',
			'show_dashboard'    => 'Enable',
			'show_user_profile' => 'Disable',
			'is_required'       => 'true',
			'label_name'        => 'Password',
			'input_type'        => 'password',
			'input_meta'        => 'user_pass',
			'placeholder'       => 'Password',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'confirmation_password' => array(
			'input_order'       => 11,
			'show_register'     => 'Enable',
			'show_dashboard'    => 'Enable',
			'show_user_profile' => 'Disable',
			'is_required'       => 'true',
			'label_name'        => 'Confirmation Password',
			'input_type'        => 'password',
			'input_meta'        => 'confirmation_password',
			'placeholder'       => 'Password',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'user_email'            => array(
			'input_order'       => 12,
			'show_register'     => 'Enable',
			'show_dashboard'    => 'Enable',
			'show_user_profile' => 'Disable',
			'is_required'       => 'true',
			'label_name'        => 'Email Address',
			'input_type'        => 'email',
			'input_meta'        => 'user_email',
			'placeholder'       => 'Email Address',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'user_nicename'         => array(
			'input_order'       => 14,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'Nicename',
			'input_type'        => 'single_line',
			'input_meta'        => 'user_nicename',
			'placeholder'       => 'Nicename',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'display_name'          => array(
			'input_order'       => 17,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'Display Name',
			'input_type'        => 'single_line',
			'input_meta'        => 'display_name',
			'placeholder'       => 'Display Name',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'first_name'            => array(
			'input_order'       => 20,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'First Name',
			'input_type'        => 'single_line',
			'input_meta'        => 'first_name',
			'placeholder'       => 'First Name',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'last_name'             => array(
			'input_order'       => 23,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'Last Name',
			'input_type'        => 'single_line',
			'input_meta'        => 'last_name',
			'placeholder'       => 'Last Name',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'nickname'              => array(
			'input_order'       => 17,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'Nickname',
			'input_type'        => 'single_line',
			'input_meta'        => 'nickname',
			'placeholder'       => 'Nickname',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'description'           => array(
			'input_order'       => 17,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Enable',
			'is_required'       => 'false',
			'label_name'        => 'Description',
			'input_type'        => 'multi_line',
			'input_meta'        => 'description',
			'placeholder'       => 'Description',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
		'show_admin_bar_front'  => array(
			'input_order'       => 17,
			'show_register'     => 'Disable',
			'show_dashboard'    => 'Disable',
			'show_user_profile' => 'Disable',
			'is_required'       => 'false',
			'label_name'        => 'Show admin bar on front page',
			'input_type'        => 'checkbox',
			'input_meta'        => 'show_admin_bar_front',
			'placeholder'       => '',
			'user_role'         => serialize( $fed_get_user_roles ),
			'extra'             => 'no',
		),
	);

	return $default_values;
}

/**
 * Plugin meta data.
 */
function fed_plugin_meta_data() {
	/**
	 * Admin Settings Tab ==> Login
	 */
	if ( ! get_option( 'fed_admin_login' ) ) {
		$value = array( 'register' => array( 'name' => 'User Role', 'position' => 999, 'role' => array() ) );
		update_option( 'fed_admin_login', $value );
	}
	/**
	 * Admin Settings Tab ==> Post
	 */
	if ( ! get_option( 'fed_admin_settings_post' ) ) {
		$all_roles = fed_get_user_roles();
		$value     = fed_get_default_post_options( $all_roles );
		update_option( 'fed_admin_settings_post', $value );
	}

	/**
	 * Admin Settings Tab ==> User Profile Layout
	 */
	if ( ! get_option( 'fed_admin_settings_upl' ) ) {
		$value = array(
			'settings' => array(
				'fed_upl_change_profile_pic' => '',
				'fed_upl_disable_desc'       => 'no',
				'fed_upl_no_recent_post'     => '5',
			)
		);
		update_option( 'fed_admin_settings_upl', $value );
	}
}

/**
 * Admin Notice
 */
function fed_admin_notice() {
	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], fed_get_script_loading_pages(), false ) ) {
		$get_notification = get_option( 'fed_admin_message_notification' );
		/**
		 * Plugin update notifications
		 */
		if ( false === ( $api = get_transient( 'fed_plugin_list_api' ) ) ) {
			$api = get_plugin_list();
			set_transient( 'fed_plugin_list_api', $api, 12 * HOUR_IN_SECONDS );
		}
		if ( $api ) {
			$plugins = json_decode( $api );
			foreach ( $plugins->plugins as $plugin ) {
				if ( defined( $plugin->id ) ) {
					if ( constant( $plugin->id . '_VERSION' ) < $plugin->version ) {
						?>
						<div class="notice notice-info">
							<div class="fed_flex_start_center">
								<img width="50px" src="<?php echo $plugin->thumbnail; ?>"/>
								<h2 class="fed_p_l_20">
									<?php echo $plugin->title . ' has been updated to newer version ' . $plugin->version . ' kindly <a href="' . $plugin->download_url . '">Update</a>'; ?>
								</h2>
							</div>
						</div>
						<?php
					}
				}
			}
		}
		/**
		 * Common Notification to watch videos
		 */
		if ( ! $get_notification ) {
			//fed_initial_setup();
			?>
			<div class="notice notice-success">
				<p>
					<b><?php _e( 'If you need help in configuring the frontend dashboard, please watch the videos here', 'frontend-dashboard' );
						?>
					</b>
					<a href="https://buffercode.com/category/name/frontend-dashboard"><?php _e( 'Frontend Dashboard Instructions', 'frontend-dashboard' ) ?></a>
				</p>
				<p>
					<?php _e( 'Notice: Please check your default settings', 'frontend-dashboard' );
					?>
					<a href="<?php menu_page_url( 'fed_status' ) ?>"><?php _e( 'Here', 'frontend-dashboard' ) ?></a>
					<?php
					_e( 'if everything is good, please  click Don\'t show again button', 'frontend-dashboard' ); ?>

					<span class="fed_message_hide">Hide</span>
					<span class="fed_message_delete" data-url="<?php echo admin_url( 'admin-ajax.php?action=fed_message_form&fed_message_nonce=' . wp_create_nonce( 'fed_message_nonce' ) ) ?>">Don't show again</span>
				</p>
			</div>
			<?php
		}
		do_action( 'fed_admin_notice' );
	}
}

//add_action( 'all_admin_notices', 'fed_admin_notice' );


function get_plugin_list() {
	$config     = fed_config();
	$plugin_api = wp_remote_get( $config['plugin_api'], array( 'timeout' => 120, 'httpversion' => '1.1' ) );

	if ( is_array( $plugin_api ) && isset( $plugin_api['body'] ) ) {
		return $plugin_api['body'];
	}

	return false;
}

/**
 * Auto update the Frontend Dashboard dependent plugins
 *
 * @param bool $update
 * @param object $item
 *
 * @return bool
 */
function fed_update_all_dependent_plugins( $update, $item ) {

	if ( in_array( $item->slug, fed_get_dependent_plugins(), true ) ) {
		return true;
	}

	return $update;
}

add_filter( 'auto_update_plugin', 'fed_update_all_dependent_plugins', 10, 2 );
