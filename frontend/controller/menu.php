<?php

/**
 * Get all dashboard menus.
 *
 * @return array
 */
function fed_get_all_dashboard_display_menus() {
	$profile_menu = fed_process_dashboard_display_menu();
	$post_menu    = fed_get_post_menu();
	$logout       = fed_get_logout_menu();

	$all_menus = apply_filters( 'fed_frontend_main_menu', array_merge( $profile_menu, $post_menu, $logout ) );

	uasort( $all_menus, 'fed_sort_by_order' );

	return $all_menus;
}

/**
 * Process Dashboard Menu
 *
 * @return array|WP_Error
 */
function fed_process_dashboard_display_menu() {
	return fed_fetch_table_rows_with_key_front_end( BC_FED_MENU_DB, 'menu_slug' );
}

/**
 * Get Post Menu.
 *
 * @return array
 */
function fed_get_post_menu() {
	$all_roles          = fed_get_user_roles();
	$admin_post_options = get_option( 'fed_admin_settings_post', fed_get_default_post_options( $all_roles ) );
	$user               = get_userdata( get_current_user_id() );
	$menu_position      = ( isset( $admin_post_options['menu']['post_position'] ) && $admin_post_options['menu']['post_position'] != '' ) ? (int) $admin_post_options['menu']['post_position'] : 2;

	$menu_name = ( isset( $admin_post_options['menu']['rename_post'] ) && $admin_post_options['menu']['rename_post'] != '' ) ? esc_attr( $admin_post_options['menu']['rename_post'] ) : 'Post';
	$menu_icon = ( isset( $admin_post_options['menu']['post_menu_icon'] ) && $admin_post_options['menu']['post_menu_icon'] != '' ) ? esc_attr( $admin_post_options['menu']['post_menu_icon'] ) : 'fa fa-file-text';

	$default = array();

	if ( count( array_intersect( $user->roles, array_keys( $admin_post_options['permissions']['fed_post_permission'] ) ) ) > 0 ) {
		$default = array(
			'post' =>
				array(
					'id'                => '20',
					'menu_slug'         => 'post',
					'menu'              => $menu_name,
					'menu_order'        => $menu_position,
					'menu_image_id'     => $menu_icon,
					'show_user_profile' => 'disable',
				)
		);
	}

	return $default;
}


/**
 * Logout Menu
 * @return array
 */
function fed_get_logout_menu() {
	return array(
		'logout' =>
			array(
				'id'                => 'logout',
				'menu_slug'         => 'logout',
				'menu'              => 'Logout',
				'menu_order'        => '900000',
				'menu_image_id'     => 'fa fa-sign-out',
				'show_user_profile' => 'disable',
			)
	);
}


/**
 * Dashboard Menu
 *
 * @param $first_element
 */
function fed_display_dashboard_menu( $first_element ) {
	$menus = fed_get_all_dashboard_display_menus();
	foreach ( $menus as $index => $menu ) {
		if ( $index == $first_element ) {
			$active = 'active';
		} else {
			$active = '';
		}
		?>
		<a href="#<?php echo $menu['menu_slug']; ?>"
		   class="list-group-item fed_menu_slug <?php echo $active ?>"
		   data-menu="<?php echo $menu['menu_slug']; ?>">
			<div class="flex">
				<div class="fed_menu_icon">
					<span class="<?php echo $menu['menu_image_id'] ?>"></span>
				</div>
				<div class="fed_menu_title"><?php echo ucwords( $menu['menu'] ) ?></div>
			</div>

		</a>
		<?php
	}
	?>

	<?php
}

/**
 * Collapse Menu
 */
function fed_get_collapse_menu() {
	?>
	<div class="list-group-item fed_collapse_menu">
		<div class="flex">
			<div class="fed_menu_icon fed_collapse_menu_icon">
				<span class="fa fa-arrow-circle-left"></span>
			</div>
			<div class="fed_menu_title fed_collapse_menu_item"><?php _e( 'Collapse Menu', 'fed' ) ?></div>
		</div>

	</div>
	<?php
}
