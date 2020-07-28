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
	$get_payload       = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );

	foreach ( $menus['menu_items'] as $index => $menu ) {
		$menu_format  = fed_format_menu_items( $menu, $index, $first_element, $dashboard_url, $index );
		$is_submenu   = '';
		$submenu_icon = '';
		$parent_id    = isset( $get_payload, $get_payload['parent_id'] ) ? fed_sanitize_text_field(
			$get_payload['parent_id']
		) : '';
		if ( isset( $menu['submenu'] ) ) {
			$is_submenu   = true;
			$submenu_icon = '<span class="fed_float_right"><i class="fas fa-chevron-right"></i></span>';
			$submenus     = $menu['submenu'];
			uasort( $submenus, 'fed_sort_by_order' );
		}

		$random_number = fed_get_random_string( 5 );
		?>
		<div class="panel panel-secondary fed_menu_item">
			<?php
			if ( $is_submenu ) {
				$top_class = $index === $parent_id ? 'active' : '';
				?>
				<div class="panel-heading <?php echo esc_attr( $top_class . ' ' . $menu_format['active'] ); ?>"
						role="tab" id="<?php echo esc_attr( $index ); ?>">
					<h4 class="panel-title">
						<a role="button" data-toggle="collapse" data-parent="#fed_default_template"
								href="#<?php echo esc_attr( $index . $random_number ); ?>"
								aria-expanded="true" aria-controls="<?php echo esc_attr( $index ); ?>">
							<div class="fed_display_inline">
								<div>
									<div class="fed_menu_icon">
										<span class="<?php echo esc_attr( $menu['menu_image_id'] ); ?>"></span>
									</div>
									<div class="fed_menu_title">
										<?php
										echo esc_attr( $menu_format['menu_name'] );
										?>
									</div>
								</div>
								<div>
									<?php echo wp_kses_post( $submenu_icon ); ?>
								</div>
							</div>
						</a>

					</h4>
				</div>
				<div id="<?php echo esc_attr( $index . $random_number ); ?>"
						class="panel-collapse collapse <?php echo $index === $parent_id ? 'in' : ''; ?>"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $index ); ?>">
					<div class="panel-body">
						<h4 class="panel-title">
							<a href="<?php echo esc_attr( $menu_format['menu_url'] ); ?>">
								<div class="flex">
									<div class="fed_menu_icon">
										<span class="<?php echo esc_attr( $menu['menu_image_id'] ); ?>"></span>
									</div>
									<div class="fed_menu_title">
										<?php echo esc_attr( $menu_format['menu_name'] ); ?>
									</div>
								</div>
							</a>

						</h4>
						<?php
						foreach ( $submenus as $sub_index => $sub_menu ) {
							$sub_menu_format = fed_format_menu_items(
								$sub_menu, $sub_index, $first_element,
								$dashboard_url, $index
							);
							?>
							<h4 class="panel-title <?php echo esc_attr( $sub_menu_format['active'] ); ?>">
								<a href="<?php echo esc_url( $sub_menu_format['menu_url'] ); ?>">
									<div class="flex">
										<div class="fed_menu_icon">
											<span class="<?php echo esc_attr( $sub_menu['menu_image_id'] ); ?>"></span>
										</div>
										<div class="fed_menu_title">
											<?php echo esc_attr( $sub_menu_format['menu_name'] ); ?>
										</div>
									</div>
								</a>
							</h4>
						<?php } ?>
					</div>
				</div>
				<?php
			} else {

				/**
				 * Make logout to work on click (not to redirect to the respective page)
				 */
				if ( 'logout_logout' === $index ) {
					?>
					<div class="panel-heading  <?php echo $index === $parent_id ? 'active' : ''; ?>"
							id="<?php echo esc_attr( $index ); ?>">
						<h4 class="panel-title">
							<a href="<?php echo wp_logout_url( fed_get_logout_redirect_url() ); ?>">
								<div class="fed_display_inline">
									<div>
										<div class="fed_menu_icon">
											<span class="<?php echo esc_attr( $menu['menu_image_id'] ); ?>"></span>
										</div>
										<div class="fed_menu_title">
											<?php echo esc_attr( $menu_format['menu_name'] ); ?>
										</div>
									</div>
									<div>
										<?php echo esc_attr( $submenu_icon ); ?>
									</div>
								</div>
							</a>
						</h4>
					</div>
					<?php
				} else {
					?>
					<div class="panel-heading  <?php echo $index === $parent_id ? 'active' : ''; ?>" role="tab"
							id="<?php echo esc_attr( $index ); ?>">
						<h4 class="panel-title">
							<a href="<?php echo esc_attr( $menu_format['menu_url'] ); ?>">
								<div class="fed_display_inline">
									<div>
										<div class="fed_menu_icon">
											<span class="<?php echo esc_attr( $menu['menu_image_id'] ); ?>"></span>
										</div>
										<div class="fed_menu_title">
											<?php echo esc_attr( $menu_format['menu_name'] ); ?>
										</div>
									</div>
									<div>
										<?php echo esc_attr( $submenu_icon ); ?>
									</div>
								</div>
							</a>
						</h4>
					</div>
				<?php }
			}
			?>
		</div>
		<?php
	}
}

/**
 * Format Menu Items.
 *
 * @param  array        $menu  Menu.
 * @param  string       $index  Index.
 * @param  string       $first_element  First Element.
 * @param  string       $dashboard_url  Dashboard URL.
 *
 * @param  string | int $parent_id  Parent ID.
 *
 * @return array
 */
function fed_format_menu_items( $menu, $index, $first_element, $dashboard_url, $parent_id ) {
	$get_payload = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
	$active      = null;
	$menu_type   = isset( $menu['menu_type'] ) ? $menu['menu_type'] : 'custom';
	$menu_slug   = isset( $menu['menu_slug'] ) ? $menu['menu_slug'] : 'fed_slug_error';
	$menu_id     = isset( $menu['id'] ) ? $menu['id'] : 0;
	$menu_name   = isset( $menu['menu'] ) ? $menu['menu'] : 'MISSING';
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

	/*
	 * This check for menu to be open in new or same window.
	 */
	if ( is_array( $menu_url ) && isset( $menu_url['url'] ) ) {
		$target   = isset( $menu_url['target'] ) ? $menu_url['target'] : $target;
		$menu_url = $menu_url['url'];
	}

	if ( isset( $get_payload['menu_type'], $get_payload['menu_id'] ) ) {
		if ( $index === $get_payload['menu_type'] . '_' . $get_payload['menu_id'] ) {
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

