<?php
function fed_admin_post_permissions_tab( $fed_admin_options ) {
	$all_roles             = fed_get_user_roles();
	$post_permission   = isset( $fed_admin_options['permissions']['post_permission'] ) ? array_keys( $fed_admin_options['permissions']['post_permission'] ) : array();

	$fed_upload_permission = isset( $fed_admin_options['permissions']['fed_upload_permission'] ) ? array_keys( $fed_admin_options['permissions']['fed_upload_permission'] ) : array();
	?>

    <form method="post"
          class="fed_admin_menu fed_ajax"
          action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

		<?php echo fed_loader(); ?>

        <input type="hidden"
               name="fed_admin_unique"
               value="fed_admin_settings_post"/>
        <input type="hidden"
               name="fed_admin_unique_post"
               value="fed_admin_permission_post"/>

            <div class="fed_admin_panel_container">
                <div class="fed_admin_panel_content_wrapper">
                    <div class="row">
                        <div class="col-md-4 fed_menu_title">Allow User Roles to Add/Edit/Delete Posts</div>
                        <div class="col-md-8">
							<?php foreach ( $all_roles as $key => $role ) {
								$c_value = in_array( $key, $post_permission ) ? 'Enable' : 'Disable';
								?>
                                <div class="col-md-6">
									<?php echo fed_input_box( 'post_permission', array(
										'default_value' => 'Enable',
										'name'          => 'permissions[post_permission][' . $key . ']',
										'label'         => $role,
										'value'         => $c_value,
									), 'checkbox' ); ?>
                                </div>
								<?php
							} ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fed_menu_title">Allow User Roles to Upload Files</div>
                        <div class="col-md-8">
							<?php foreach ( $all_roles as $key => $role ) {
								$c_value = in_array( $key, $fed_upload_permission ) ? 'Enable' : 'Disable';
								?>
                                <div class="col-md-6">
									<?php echo fed_input_box( 'fed_upload_permission', array(
										'default_value' => 'Enable',
										'name'          => 'permissions[fed_upload_permission][' . $key . ']',
										'label'         => $role,
										'value'         => $c_value,
									), 'checkbox' ); ?>
                                </div>
								<?php
							} ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
					<input type="submit" class="btn btn-primary" value="Submit"/>
                </div>
            </div>
    </form>
	<?php
}

/**
 * Get Default Post Options
 *
 * @param array $all_roles User Roles.
 *
 * @return array
 */
function fed_get_default_post_options( $all_roles ) {
	$default = array();
	foreach ( $all_roles as $key => $all_role ) {
		$default['permissions']['post_permission'][ $key ] = 'Enable';
		$default['permissions']['fed_upload_permission'][ $key ] = 'Enable';
	}
	$default['settings']['fed_post_status']   = 'publish';
	$default['menu']['rename_post'] = 'Post';
	$default['menu']['post_position'] = 2;
	$default['menu']['post_menu_icon'] = 'fa fa-file-text';

	return $default;
}