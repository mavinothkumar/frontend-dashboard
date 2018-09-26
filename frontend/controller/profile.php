<?php

/**
 * Get Display Profile
 *
 * @return array|bool
 */
function fed_process_dashboard_display_profile( $menu ) {
	return fed_fetch_user_profile_by_menu_slug( $menu );

}

/**
 * Get Display Dashboard Profile
 *
 * @param $menu_item
 */
function fed_display_dashboard_profile( $menu_item ) {
	/**
	 * Whether the menu is permitted?
	 * Then Fetch the menu it based on the menu id
	 */

	$profiles = fed_process_dashboard_display_profile( $menu_item['menu_slug'] );
	$user     = get_userdata( get_current_user_id() );
	$menus    = fed_process_dashboard_display_menu();

	$index             = $menu_item['menu_slug'];
	$menu_title        = ucwords( __( $menus[ $index ]['menu'], 'frontend-dashboard' ) );
	$menu_title_value  = apply_filters( 'fed_menu_title', $menu_title, $menus, $index );
	$menu_default_page = apply_filters( 'fed_menu_default_page', true, $menus, $index );
	?>
	<div class="panel panel-primary fed_dashboard_item">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="<?php echo $menus[ $index ]['menu_image_id'] ?>"></span>
				<?php esc_attr_e( $menus[ $index ]['menu'], 'frontend-dashboard' ) ?>
			</h3>
		</div>
		<div class="panel-body">
			<?php
			if ( $menu_default_page ) {
				if ( $profiles ) {
					echo fed_show_alert( 'fed_profile_save_message' );
					?>
					<form method="post"
						  action="">
						<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>
						<input type="hidden"
							   name="tab_id"
							   value="<?php echo $index ?>">
                        <input type="hidden"
							   name="menu_type"
							   value="<?php echo $menu_item['menu_type'] ?>">

                        <input type="hidden"
							   name="menu_slug"
							   value="<?php echo $menu_item['menu_slug'] ?>">
						<?php
						foreach ( $profiles as $single_item ) {

							if ( ! $single_item['input_meta'] !== 'user_pass' || ! $single_item['input_meta'] !== 'confirmation_password' ) {
								$single_item['user_value'] = $user->get( $single_item['input_meta'] );
							}

							if ( in_array( $single_item['input_meta'], fed_no_update_fields() ) ) {
								$single_item['readonly'] = true;
							}

							if ( count( array_intersect( $user->roles, unserialize( $single_item['user_role'] ) ) ) <= 0 ) {
								continue;
							}
							?>
							<div class="row fed_dashboard_item_field">
								<div class="col-md-7 fo">
									<label>
										<?php
										esc_attr_e( $single_item['label_name'], 'frontend-dashboard' );
										?>
									</label>
									<?php
									echo fed_get_input_details( $single_item );
									?>
								</div>
								<div class="col-md-5">
								</div>
							</div>
							<?php
						}
						?>
						<div class="row text-center">
							<button type="submit" class="btn btn-primary">
								<i class="fa fa-floppy-o"></i>
								<?php _e( 'Save', 'frontend-dashboard' ) ?>
							</button>
						</div>

					</form>
					<?php
				} else {
					?>
					<h4><?php _e( 'Sorry! there is no field associated to this menu', 'frontend-dashboard' ) ?></h4>
					<?php
				}
			} else {
				do_action( 'fed_override_default_page', $menus, $index );
			}
			?>
		</div>
	</div>
	<?php
}