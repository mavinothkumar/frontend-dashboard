<?php
/**
 * Profile.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Display Profile.
 *
 * @param  string $menu  Menu.
 *
 * @return array|bool
 */
function fed_process_dashboard_display_profile( $menu ) {
	return fed_fetch_user_profile_by_menu_slug( $menu );

}

/**
 * Get Display Dashboard Profile.
 *
 * @param  array $menu_item  Menu Item.
 */
function fed_display_dashboard_profile( $menu_item ) {
	/**
	 * Is menu permitted?
	 * Then Fetch the menu and its menu id
	 */
	$profiles = fed_process_dashboard_display_profile( $menu_item['menu_slug'] );
	$user     = get_userdata( get_current_user_id() );
	$menus    = fed_process_dashboard_display_menu();

	$index     = $menu_item['menu_slug'];
	$menu_name = esc_attr( $menus[ $index ]['menu'] );
	/* translators: %s : Menu. */
	$menu_title        = sprintf( ucwords( __( '%s ', 'frontend-dashboard' ) ), $menu_name );
	$menu_title_value  = apply_filters( 'fed_menu_title', $menu_title, $menus, $index );
	$menu_default_page = apply_filters( 'fed_menu_default_page', true, $menus, $index );
	?>
	<div class="panel panel-primary fed_dashboard_item">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="<?php echo esc_attr( $menus[ $index ]['menu_image_id'] ); ?>"></span>
				<?php
				/* translators: %s : Menu */
				esc_attr_e( apply_filters( 'fed_user_profile_menu_container_title', $menu_name, $menus[ $index ] ) );
				?>
			</h3>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<?php
					do_action( 'fed_dashboard_panel_inside_top' );
					do_action( 'fed_dashboard_panel_inside_top_' . fed_get_data( 'menu_slug', $menu_item ) );
					// phpcs:ignore
					echo fed_show_alert( 'fed_profile_save_message' );
					if ( $menu_default_page ) {
						if ( $profiles ) {
							usort( $profiles, 'fed_sort_by_order' );
							?>
							<form method="post"
									action="<?php echo esc_url( add_query_arg( array( 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ),
										fed_get_form_action( 'fed_save_user_profile' ) ) ); ?>">
								<?php wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
								<input type="hidden"
										name="tab_id"
										value="<?php echo esc_attr( $index ); ?>"/>
								<input type="hidden"
										name="menu_type"
										value="<?php echo esc_attr( $menu_item['menu_type'] ); ?>"/>

								<input type="hidden"
										name="menu_slug"
										value="<?php echo esc_attr( $menu_item['menu_slug'] ); ?>"/>
								<?php

								do_action( 'fed_dashboard_profile_form_top' );

								foreach ( $profiles as $single_item ) {
									if ( 'user_pass' !== $single_item['input_meta'] || 'confirmation_password' !== $single_item['input_meta'] ) {
										$single_item['user_value'] = $user->get( $single_item['input_meta'] );
									}

									if ( in_array( $single_item['input_meta'], fed_no_update_fields() ) ) {
										$single_item['readonly'] = true;
									}

									if (
										count(
											array_intersect( $user->roles, unserialize( $single_item['user_role'] ) )
										) <= 0
									) {
										continue;
									}
									?>
									<div class="row fed_dashboard_item_field">
										<div class="col-md-12 fo">
											<label>
												<?php
												echo wp_kses_post( $single_item['label_name'] );
												?>
											</label>
											<?php
											// phpcs:ignore
											echo fed_get_input_details( $single_item );
											?>
										</div>
									</div>
									<?php
								}

								do_action( 'fed_dashboard_profile_form_bottom' );
								?>
								<div class="row text-center">
									<button type="submit" class="btn btn-primary">
										<i class="fa fa-floppy-o"></i>
										<?php esc_attr_e( 'Save', 'frontend-dashboard' ); ?>
									</button>
								</div>
							</form>
							<?php
						} else {
							?>
							<h4>
								<?php
								esc_attr_e( 'Sorry! there is no field associated to this menu', 'frontend-dashboard' );
								?>
							</h4>
							<?php
						}
					} else {
						do_action( 'fed_override_default_page', $menus, $index );
					}
					do_action( 'fed_dashboard_panel_inside_bottom' );
					do_action( 'fed_dashboard_panel_inside_bottom_' . fed_get_data( 'menu_slug', $menu_item ) );
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}
