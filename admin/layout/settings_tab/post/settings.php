<?php
function fed_admin_post_settings_tab( $fed_admin_options ) {
	$post_status = fed_get_post_status();
	$fed_post_status       = isset( $fed_admin_options['settings']['fed_post_status'] ) ? $fed_admin_options['settings']['fed_post_status'] : '';
	$fed_post_position     = isset( $fed_admin_options['settings']['fed_post_position'] ) ? $fed_admin_options['settings']['fed_post_position'] : 3;
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
               value="fed_admin_settings_post"/>


            <div class="fed_admin_panel_container">
                <div class="fed_admin_panel_content_wrapper">


                    <div class="row">
                        <div class="col-md-4 fed_menu_title">New Post Status</div>
                        <div class="col-md-4">
                            <div class="col-md-6">
								<?php echo fed_input_box( 'fed_post_status', array(
									'name'    => 'settings[fed_post_status]',
									'value'   => $fed_post_status,
									'options' => $post_status
								), 'select' ); ?>
                            </div>
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