<?php
function fed_admin_register_settings_tab( $fed_login_register ) {
	$user_role  = isset( $fed_login_register['register']['role'] ) ? array_keys( $fed_login_register['register']['role'] ) : array();
	$name       = isset( $fed_login_register['register']['name'] ) ? $fed_login_register['register']['name'] : 'User Role';
	$position   = isset( $fed_login_register['register']['position'] ) ? $fed_login_register['register']['position'] : 999;
	$user_roles = fed_get_user_roles();
	?>
	<form method="post"
		  class="fed_admin_menu fed_ajax"
		  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">

		<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

		<?php echo fed_loader(); ?>

		<input type="hidden"
			   name="fed_admin_unique"
			   value="fed_login_details"/>

		<input type="hidden"
			   name="fed_admin_unique_login"
			   value="fed_register_settings"/>

		<div class="fed_admin_panel_container">
			<div class="fed_admin_panel_content_wrapper">
				<div class="row">
					<div class="col-md-4 fed_menu_title"><?php _e( 'Menu Name', 'fed' ) ?></div>
					<div class="col-md-8">
						<div class="col-md-6">
							<?php echo fed_input_box( 'fed_login_register_name', array(
								'name'  => 'fed_admin_login[name]',
								'value' => $name,
							), 'single_line' ); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title"><?php _e( 'Menu Name Order', 'fed' ) ?></div>
					<div class="col-md-8">
						<div class="col-md-6">
							<?php echo fed_input_box( 'fed_login_register_position', array(
								'name'  => 'fed_admin_login[position]',
								'value' => $position,
							), 'number' ); ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title"><?php _e( 'Show User Role(s) in Register Form', 'fed' ) ?></div>
					<div class="col-md-8">
						<?php foreach ( $user_roles as $key => $role ) {
							$c_value = in_array( $key, $user_role,false ) ? 'Enable' : 'Disable';
							?>
							<div class="col-md-6">
								<?php echo fed_input_box( 'fed_login_register', array(
									'default_value' => 'Enable',
									'name'          => 'fed_admin_login[role][' . $key . ']',
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

		<?php do_action( 'fed_admin_login_register_template', $fed_login_register ) ?>

		<div class="row">
			<div class="col-md-12">
				<input type="submit" class="btn btn-primary" value="Submit"/>
			</div>
		</div>
	</form>
	<?php
}
