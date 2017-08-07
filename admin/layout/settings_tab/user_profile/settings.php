<?php
function fed_admin_user_profile_settings_tab( $fed_admin_options ) {
	$fed_upef = array_merge( fed_fetch_user_profile_extra_fields_key_value(), array( '' => 'Let it be default' ) );
	//var_dump( $fed_admin_options );
	?>
    <form method="post"
          class="fed_admin_menu fed_ajax"
          action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">
		<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

		<?php echo fed_loader(); ?>

        <input type="hidden"
               name="fed_admin_unique"
               value="fed_admin_setting_upl"/>


        <div class="fed_admin_panel_container">
            <div class="fed_admin_panel_content_wrapper">
                <div class="row">
                    <div class="col-md-4 fed_menu_title">
                        <?php _e('Change Profile Picture','fed') ?>
                        <span
                           class="fa fa-info-circle"
                           data-toggle="popover"
                           data-trigger="hover"
                           data-original-title="Note"
                           data-content="Image size should be min 600x600 px">
						</span>

                    </div>

                    <div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_change_profile_pic', array(
							'name'    => 'settings[fed_upl_change_profile_pic]',
							'options' => $fed_upef,
							'value'   => isset( $fed_admin_options['settings']['fed_upl_change_profile_pic'] ) ? $fed_admin_options['settings']['fed_upl_change_profile_pic'] : '',
						), 'select' ) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 fed_menu_title"><?php _e('Disable Description','fed') ?></div>
                    <div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_disable_desc', array(
							'name'    => 'settings[fed_upl_disable_desc]',
							'options' => fed_yes_no( 'ASC' ),
							'value'   => isset( $fed_admin_options['settings']['fed_upl_disable_desc'] ) ? $fed_admin_options['settings']['fed_upl_disable_desc'] : '',
						), 'select' ) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 fed_menu_title"><?php _e('Number of Recent Post to show','fed') ?></div>
                    <div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_no_recent_post', array(
							'name'        => 'settings[fed_upl_no_recent_post]',
							'placeholder' => __( 'Number of Recent Post to show on User Profile' ),
							'value'       => isset( $fed_admin_options['settings']['fed_upl_no_recent_post'] ) ? $fed_admin_options['settings']['fed_upl_no_recent_post'] : '5',
						), 'number' ) ?>
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