<?php
/**
 * Menu.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get all dashboard menus.
 *
 * @return array
 */
function fed_get_all_dashboard_display_menus() {
	$profile_menu = fed_process_dashboard_display_menu();
	$settings     = get_option( 'fed_admin_settings_upl' );
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
	return fed_fetch_table_rows_with_key_front_end( BC_FED_TABLE_MENU, 'menu_slug' );
}

/**
 * Get Post Menu.
 *
 * @return array
 */
function fed_get_post_menu() {
	$all_roles          = fed_get_user_roles();
	$admin_post_options = get_option(
		'fed_cp_admin_settings', fed_get_default_post_options(
			$all_roles
		)
	);

	$default = array();
	$user    = get_userdata( get_current_user_id() );
	foreach ( $admin_post_options as $key => $options ) {
		$menu_position = ( isset( $options['menu']['post_position'] ) && '' != $options['menu']['post_position'] ) ? (int) $options['menu']['post_position'] : 99;

		$menu_name = ( isset( $options['menu']['rename_post'] ) && '' != $options['menu']['rename_post'] ) ? esc_attr(
			$options['menu']['rename_post']
		) : 'Post';
		$menu_icon = ( isset( $options['menu']['post_menu_icon'] ) && '' != $options['menu']['post_menu_icon'] ) ? esc_attr(
			$options['menu']['post_menu_icon']
		) : 'fa fa-file-text';

		if (
			isset( $options['permissions']['post_permission'] ) &&
			count(
				array_intersect(
					$user->roles,
					array_keys(
						$options['permissions']['post_permission']
					)
				)
			) > 0
		) {
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
 *
 * @return array
 */
function fed_get_logout_menu() {
	return array(
		'logout' =>
			array(
				'id'                => 'logout',
				'menu_slug'         => 'logout',
				'menu'              => __( 'Logout', 'frontend-dashboard' ),
				'menu_order'        => '900000',
				'menu_image_id'     => 'fa fa-sign-out',
				'show_user_profile' => 'disable',
				'menu_type'         => 'logout',
			),
	);
}


/**
 * Dashboard Menu
 *
 * @param  array $menus  Menus.
 */
function fed_display_dashboard_menu( $menus ) {
	$first_element_key = array_keys( $menus['menu_items'] );
	$first_element     = $first_element_key[0];
	$dashboard_url     = fed_get_dashboard_url();
	$get_payload       = \FED\Helpers\InputHelper::get();

	foreach ( $menus['menu_items'] as $index => $menu ) {
		$menu_format  = fed_format_menu_items( $menu, $index, $first_element, $dashboard_url, $index );
		$is_submenu   = false;
		$parent_id    = isset( $get_payload['parent_id'] ) ? sanitize_text_field( $get_payload['parent_id'] ) : '';
		
		if ( isset( $menu['submenu'] ) && ! empty( $menu['submenu'] ) ) {
			$is_submenu = true;
			$submenus   = $menu['submenu'];
			uasort( $submenus, 'fed_sort_by_order' );
		}

		$isActive     = ! empty( $menu_format['active'] );
		$random_number = fed_get_random_string( 5 );

		if ( $is_submenu ) {
			$isParentActive = $index === $parent_id || $isActive;
			?>
			<div class="fed_menu_item mb-1">
				<button type="button"
						class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 <?php echo $isParentActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>"
						data-toggle="collapse"
						data-target="#sub_<?php echo esc_attr( $index . $random_number ); ?>"
						aria-expanded="<?php echo $isParentActive ? 'true' : 'false'; ?>">
					<div class="flex items-center gap-3">
						<span class="w-5 text-center text-base <?php echo esc_attr( $menu['menu_image_id'] ); ?> <?php echo $isParentActive ? 'text-indigo-600' : 'text-gray-400'; ?>"></span>
						<span><?php echo esc_html( $menu_format['menu_name'] ); ?></span>
					</div>
					<svg class="w-4 h-4 transform transition-transform duration-200 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
				</button>
				<div id="sub_<?php echo esc_attr( $index . $random_number ); ?>"
						class="pl-7 pr-2 py-1 space-y-1 <?php echo $isParentActive ? 'block' : 'hidden'; ?>">
					<a href="<?php echo esc_url( $menu_format['menu_url'] ); ?>"
							class="block px-3 py-1.5 rounded-md text-xs font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors">
						<?php echo esc_html( $menu_format['menu_name'] ); ?> (Overview)
					</a>
					<?php
					foreach ( $submenus as $sub_index => $sub_menu ) {
						$sub_menu_format = fed_format_menu_items( $sub_menu, $sub_index, $first_element, $dashboard_url, $index );
						$isSubActive     = ! empty( $sub_menu_format['active'] );
						?>
						<a href="<?php echo esc_url( $sub_menu_format['menu_url'] ); ?>"
								class="block px-3 py-1.5 rounded-md text-xs font-medium transition-colors <?php echo $isSubActive ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'; ?>">
							<?php echo esc_html( $sub_menu_format['menu_name'] ); ?>
						</a>
					<?php } ?>
				</div>
			</div>
			<?php
		} else {
			if ( 'logout_logout' === $index || 'logout' === $menu['menu_slug'] ) {
				?>
				<div class="fed_menu_item mt-3 pt-3 border-t border-gray-100">
					<a href="<?php echo wp_logout_url( fed_get_logout_redirect_url() ); ?>"
							class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-150">
						<span class="w-5 text-center text-base <?php echo esc_attr( $menu['menu_image_id'] ); ?> text-red-500"></span>
						<span><?php echo esc_html( $menu_format['menu_name'] ); ?></span>
					</a>
				</div>
				<?php
			} else {
				?>
				<div class="fed_menu_item mb-1">
					<a href="<?php echo esc_url( $menu_format['menu_url'] ); ?>"
							class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150 <?php echo $isActive ? 'bg-indigo-50 text-indigo-700 font-semibold shadow-xs' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'; ?>">
						<span class="w-5 text-center text-base <?php echo esc_attr( $menu['menu_image_id'] ); ?> <?php echo $isActive ? 'text-indigo-600' : 'text-gray-400'; ?>"></span>
						<span><?php echo esc_html( $menu_format['menu_name'] ); ?></span>
					</a>
				</div>
				<?php
			}
		}
	}
}

/**
 * Format Menu Items.
 *
 * @param  array        $menu  Menu.
 * @param  string       $index  Index.
 * @param  string       $first_element  First Element.
 * @param  string       $dashboard_url  Dashboard URL.
 * @param  string | int $parent_id  Parent ID.
 *
 * @return array
 */
function fed_format_menu_items( $menu, $index, $first_element, $dashboard_url, $parent_id ) {
	$get_payload = \FED\Helpers\InputHelper::get();
	$active      = null;
	$menu_type   = isset( $menu['menu_type'] ) ? $menu['menu_type'] : 'custom';
	$menu_slug   = isset( $menu['menu_slug'] ) ? $menu['menu_slug'] : 'fed_slug_error';
	$menu_id     = isset( $menu['id'] ) ? $menu['id'] : 0;
	$menu_name   = isset( $menu['menu'] ) ? $menu['menu'] : ( isset( $menu['menu_name'] ) ? $menu['menu_name'] : 'MISSING' );
	$menu_url    = add_query_arg(
		array(
			'menu_type' => $menu_type,
			'menu_slug' => $menu_slug,
			'menu_id'   => $menu_id,
			'parent_id' => $parent_id,
			'fed_nonce' => wp_create_nonce(
				'fed_nonce'
			),
		), $dashboard_url
	);
	$menu_url    = apply_filters( 'fed_convert_dashboard_menu_url', $menu_url, $menu );
	$target      = '_self';

	if ( is_array( $menu_url ) && isset( $menu_url['url'] ) ) {
		$target   = isset( $menu_url['target'] ) ? $menu_url['target'] : $target;
		$menu_url = $menu_url['url'];
	}

	if ( isset( $get_payload['menu_type'], $get_payload['menu_id'] ) ) {
		if ( $index === $get_payload['menu_type'] . '_' . $get_payload['menu_id'] || $menu_slug === ( $get_payload['menu_slug'] ?? '' ) ) {
			$active = 'active';
		}
	} else {
		if ( $index === $first_element ) {
			$active = 'active';
		}
	}

	return array(
		'menu_name' => $menu_name,
		'menu_url'  => $menu_url,
		'menu_id'   => $menu_id,
		'active'    => $active,
		'target'    => $target,
	);
}

/**
 * Collapse Menu.
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
              $('.fed_collapse_menu').trigger('click')
            }
          })
		</script>
		<?php
	}
	$collapse = fed_get_collapse_menu_content();
	?>
	<div class="panel panel-secondary fed_menu_item">
		<div class="panel-heading" role="tab">
			<h4 class="panel-title fed_collapse_menu">
				<a role="button" data-toggle="collapse" data-parent="#fed_default_template"
						href="#">
					<div class="fed_flex_left">
						<div class="fed_menu_icon fed_collapse_menu_icon menu_open">
							<span class="open <?php echo esc_attr( $collapse['open_icon'] ); ?>"></span>
							<span class="closed hide <?php echo esc_attr( $collapse['close_icon'] ); ?>"></span>
						</div>
						<div class="fed_menu_title fed_collapse_menu_item">
							<?php echo esc_attr( $collapse['name'] ); ?>
						</div>
					</div>
				</a>
			</h4>
		</div>
	<?php
}

/**
 * Get Collapse Menu Content.
 *
 * @return mixed|void
 */
function fed_get_collapse_menu_content() {
	return apply_filters(
		'fed_collapse_menu_content', array(
			'open_icon'  => 'fa fa-arrow-right',
			'close_icon' => 'fa fa-arrow-left',
			'name'       => __( 'Collapse Menu', 'frontend-dashboard' ),
		)
	);
}

