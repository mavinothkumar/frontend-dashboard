<?php
function fed_admin_user_upload_permission_tab( $fed_admin_options ) {
	$all_roles = fed_get_user_roles();

//	$fed_upload_permission = isset( $fed_admin_options['permissions']['fed_upload_permission'] ) ? array_keys( $fed_admin_options['permissions']['fed_upload_permission'] ) : array();
	$fed_upload_permission = isset( $fed_admin_options['user']['upload_permission'] ) ? array_keys( $fed_admin_options['user']['upload_permission'] ) : array();
	?>

	<form method="post"
		  class="fed_admin_menu fed_ajax"
		  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">

		<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

		<?php echo fed_loader(); ?>

		<input type="hidden"
			   name="fed_admin_unique"
			   value="fed_admin_setting_user"/>
		<input type="hidden"
			   name="fed_admin_unique_user"
			   value="fed_admin_user_upload"/>

		<div class="fed_admin_panel_container">
			<div class="fed_admin_panel_content_wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h3 class="panel-title">
									<i class="fa fa-upload"></i>
									<?php _e( 'User Upload Permission', 'frontend-dashboard' ) ?>
								</h3>
							</div>
							<div class="panel-body">
								<div>
									<label><?php _e( 'Allow User Roles to Upload Files', 'frontend-dashboard' ) ?></label>
								</div>
								<?php foreach ( $all_roles as $key => $role ) {
									$c_value = in_array( $key, $fed_upload_permission, false ) ? 'Enable' : 'Disable';
									?>
									<div class="col-md-6">
										<?php echo fed_input_box( 'user[upload_permission]', array(
											'default_value' => 'Enable',
											'name'          => 'user[upload_permission][' . $key . ']',
											'label'         => $role,
											'value'         => $c_value,
										), 'checkbox' ); ?>
									</div>
									<?php
								} ?>
								<div>
									<input type="submit" class="btn btn-primary" value="Submit"/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


	</form>
	<?php
}