<?php

/**
 * Get Display Profile
 *
 * @return array|bool
 */
function fed_process_dashboard_display_profile() {
	$menus                    = array_keys( fed_process_dashboard_display_menu() );
	$profiles                 = fed_array_group_by_key( fed_fetch_user_profile_by_dashboard(), 'menu' );
	$merge_menus_with_profile = array();

	/**
	 * Append Post Menu to Profile to show in Dashboard
	 */
	foreach ( $menus as $menu ) {
		if ( array_key_exists( $menu, $profiles ) ) {
			$merge_menus_with_profile[ $menu ] = $profiles[ $menu ];
		} else {
			$merge_menus_with_profile[ $menu ] = array();
		}
	}
	uasort( $merge_menus_with_profile, 'fed_sort_by_order' );

	return $merge_menus_with_profile;
}

/**
 * Get Display Dashboard Profile
 *
 * @param array $profiles Profile Details.
 * @param array $menus Menu Details
 * @param object $user User Details
 * @param string $first_element Check First Element
 */
function fed_display_dashboard_profile( $first_element ) {
	//var_dump( $profiles );
	$profiles  = fed_process_dashboard_display_profile();
	$user  = get_userdata( get_current_user_id() );
	$menus = fed_process_dashboard_display_menu();
	foreach ( $profiles as $index => $item ) {
		if ( $index == $first_element ) {
			$active = '';
		} else {
			$active = 'hide';
		}
		?>
        <div class="panel panel-primary fed_dashboard_item <?php echo $active . ' ' . $index ?>">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="<?php echo $menus[ $index ]['menu_image_id'] ?>"></span>
					<?php echo ucwords( $menus[ $index ]['menu_slug'] ) ?>
                </h3>
            </div>
            <div class="panel-body">
				<?php if ( count( $item ) > 0 ) { ?>
                    <form method="post"
                          class="fed_user_profile_save"
                          action="<?php echo admin_url( 'admin-ajax.php?action=fed_user_profile_save' ) ?>">
						<?php wp_nonce_field( 'fed_user_profile_save', 'fed_user_profile_save' ) ?>
                        <input type="hidden"
                               name="tab_id"
                               value="<?php echo $index ?>">
						<?php
						foreach ( $item as $single_item ) {

							if ( ! $single_item['input_meta'] !== 'user_pass' || ! $single_item['input_meta'] !== 'confirmation_password' ) {
								$single_item['user_value'] = $user->get( $single_item['input_meta'] );
							}

							if ( in_array( $single_item['input_meta'], fed_no_update_fields() ) ) {
								$single_item['readonly'] = 'readonly';
							}

							if ( count( array_intersect( $user->roles, unserialize( $single_item['user_role'] ) ) ) <= 0 ) {
								continue;
							}

							?>
                            <div class="row fed_dashboard_item_field">
                                <div class="col-md-4">
                                    <div class="pull-right">
										<?php echo esc_attr( $single_item['label_name']) ?>
                                    </div>
                                </div>
                                <div class="col-md-5">
									<?php
									echo fed_get_input_details( $single_item );
									?>
                                </div>
                                <div class="col-md-3">

                                </div>
                            </div>

							<?php
						}
						?>
                        <div class="row text-center">
                            <button class="btn btn-primary">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
						<?php

						?>

                    </form>
				<?php } else {
					echo 'Sorry! there is no field associated to this menu';
				} ?>
            </div>
        </div>
	<?php }
}