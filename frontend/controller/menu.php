<?php

/**
 * Get all dashboard menus.
 *
 * @return array
 */
function fed_get_all_dashboard_display_menus() {
	$profile_menu = fed_process_dashboard_display_menu();

	$settings = get_option( 'fed_admin_settings_upl' );
	if ( isset( $settings['settings']['fed_upl_disable_logout'] ) && 'yes' === $settings['settings']['fed_upl_disable_logout']
	) {
		$logout = array();
	} else {
		$logout = fed_get_logout_menu();
	}

	$all_menus = apply_filters( 'fed_frontend_main_menu', array_merge( $profile_menu, $logout ) );
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
	$admin_post_options = get_option( 'fed_cp_admin_settings', fed_get_default_post_options(
		$all_roles
	) );

	$default = array();
	$user    = get_userdata( get_current_user_id() );
	foreach ( $admin_post_options as $key => $options ) {
		$menu_position = ( isset( $options['menu']['post_position'] ) && $options['menu']['post_position'] != '' ) ? (int) $options['menu']['post_position'] : 99;

		$menu_name = ( isset( $options['menu']['rename_post'] ) && $options['menu']['rename_post'] != '' ) ? esc_attr( $options['menu']['rename_post'] ) : 'Post';
		$menu_icon = ( isset( $options['menu']['post_menu_icon'] ) && $options['menu']['post_menu_icon'] != '' ) ? esc_attr( $options['menu']['post_menu_icon'] ) : 'fa fa-file-text';

		if ( isset( $options['permissions']['post_permission'] ) && count( array_intersect( $user->roles, array_keys( $options['permissions']['post_permission'] ) ) ) > 0 ) {
			$default[ $key ] = array(
				'id'                => '20',
				'menu_slug'         => 'post',
				'menu'              => $menu_name,
				'menu_order'        => $menu_position,
				'menu_image_id'     => $menu_icon,
				'show_user_profile' => 'disable',
			);
		}
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
				'menu_type'         => 'logout',
			)
	);
}


/**
 * Dashboard Menu
 *
 * @param $first_element
 */
function fed_display_dashboard_menu( $menus ) {
	$first_element_key = array_keys( $menus['menu_items'] );
	$first_element     = $first_element_key[0];
	$dashboard_url     = fed_get_dashboard_url();
	foreach ( $menus['menu_items'] as $index => $menu ) {
		$active    = '';
		$menu_type = isset( $menu['menu_type'] ) ? $menu['menu_type'] : 'custom';
		$menu_slug = is_string( $index ) ? $index : 'fed_slug_error';
		$menu_url  = $dashboard_url . '?menu_type=' . $menu_type . '&' . 'menu_slug=' . $menu_slug . '&fed_nonce=' . wp_create_nonce( 'fed_nonce' );
		$menu_url  = apply_filters( 'fed_convert_dashboard_menu_url', $menu_url, $menu );
		$target    = '_self';
		/*
		 * This check for menu to be open in new or same window
		 */
		if ( is_array( $menu_url ) && isset( $menu_url['url'] ) ) {
			$target   = isset( $menu_url['target'] ) ? $menu_url['target'] : '_self';
			$menu_url = $menu_url['url'];
		}

		if ( isset( $_GET['menu_slug'] ) ) {
			if ( $index === $_GET['menu_slug'] ) {
				$active = 'active';
			}
		} else {
			if ( $index === $first_element ) {
				$active = 'active';
			}
		}
		?>
		<li class=" <?php echo $active ?> list-group-item" data-menu="<?php echo $index; ?>">
			<a href="<?php echo $menu_url; ?>" target="<?php echo $target; ?>">
				<div class="flex">
					<div class="fed_menu_icon">
						<span class="<?php echo $menu['menu_image_id'] ?>"></span>
					</div>
					<div class="fed_menu_title"><?php printf( esc_attr__( '%s', 'frontend-dashboard' ), ucwords( $menu['menu'] ) ) ?></div>
				</div>
			</a>
		</li>
		<?php
	}
	?>

	<?php
}

/**
 * Collapse Menu
 */
function fed_get_collapse_menu() {
	$settings = get_option( 'fed_admin_settings_upl' );
	if ( isset( $settings['settings']['fed_upl_disable_collapse_menu'] ) && 'yes' === $settings['settings']['fed_upl_disable_collapse_menu']
	) {
		return true;
	}

	if ( isset( $settings['settings']['fed_upl_collapse_menu'] ) && 'yes' === $settings['settings']['fed_upl_collapse_menu']
	) {
		?>
		<script>
            jQuery(document).ready(function ($) {
                if ($('.fed_dashboard_menus').length) {
                    $('.fed_collapse_menu').trigger('click');
                }
            })
		</script>
		<?php
	}
	$collapse = fed_get_collapse_menu_content();
	?>
	<li class="fed_collapse_menu list-group-item visible-lg visibile-md">
		<div class="flex">
			<div class="fed_menu_icon fed_collapse_menu_icon menu_open">
				<span class="open <?php echo $collapse['open_icon'] ?>"></span>
				<span class="closed hide <?php echo $collapse['close_icon'] ?>"></span>
			</div>
			<div class="fed_menu_title fed_collapse_menu_item">
				<?php printf( __( '%s', 'frontend-dashboard' ), $collapse['name'] ) ?>
			</div>
		</div>
	</li>
	<?php
}

function fed_get_collapse_menu_content() {
	return apply_filters( 'fed_collapse_menu_content', array(
		'open_icon'  => 'fa fa-arrow-right',
		'close_icon' => 'fa fa-arrow-left',
		'name'       => __( 'Collapse Menu', 'frontend-dashboard' ),
	) );
}
