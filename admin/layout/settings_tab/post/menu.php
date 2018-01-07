<?php
function fed_admin_post_menu_tab( $fed_admin_options ) {
	//var_dump($fed_admin_options);
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
               value="fed_admin_settings_post_menu"/>
        <div class="fed_admin_panel_container">
            <div class="fed_admin_panel_content_wrapper">
                <div class="row">
                    <div class="col-md-4 fed_menu_title">Post Menu Name</div>
                    <div class="col-md-8">
						<?php echo fed_input_box( 'fed_post_menu_name', array(
							'name'        => 'fed_post_options[menu][rename_post]',
							'placeholder' => __( 'Please enter new name for Post' ),
							'value'       => isset( $fed_admin_options['menu']['rename_post'] ) ? $fed_admin_options['menu']['rename_post'] : 'Post'
						), 'single_line' ) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 fed_menu_title">Post Menu Position</div>
                    <div class="col-md-8">
						<?php echo fed_input_box( 'post_menu_position', array(
							'name'        => 'fed_post_options[menu][post_position]',
							'value'       => isset( $fed_admin_options['menu']['post_position'] ) ? $fed_admin_options['menu']['post_position'] : 2,
							'placeholder' => __( 'Post Menu Position' ),
						), 'number' ); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 fed_menu_title">Post Menu Icon</div>
                    <div class="col-md-8">
						<?php echo fed_input_box( 'fed_payment_options[menu][post_menu_icon]', array(
							'name'        => 'fed_post_options[menu][post_menu_icon]',
							'placeholder' => __( 'Please Select Post Menu Icon' ),
							'value'       => isset( $fed_admin_options['menu']['post_menu_icon'] ) ? $fed_admin_options['menu']['post_menu_icon'] : 'fa fa-file-text',
							'class'       => 'post_menu_icon',
							'extra'       => 'data-toggle="modal" data-target=".fed_show_fa_list" placeholder="Menu Icon" data-fed_menu_box_id="post_menu_icon"'
						), 'single_line' ) ?>
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