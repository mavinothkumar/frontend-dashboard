<?php

function fed_admin_user_role_tab( $fed_admin_options ) {
	$user_roles = fed_get_extra_user_roles();
	?>
	<form method="post"
		  class="fed_admin_menu fed_ajax"
		  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">
		<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

		<?php echo fed_loader(); ?>

		<input type="hidden"
			   name="fed_admin_unique"
			   value="fed_admin_setting_user"/>
		<input type="hidden"
			   name="fed_admin_unique_user"
			   value="fed_admin_setting_role"/>

		<div class="fed_admin_panel_container">
			<div class="fed_admin_panel_content_wrapper">
				<div class="panel panel-primary">
					<div class="panel-heading">Add New User Role</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<label>
									Role Name
								</label>
								<?php echo fed_input_box( 'fed_user_add_role_role', array(
									'name'        => 'user[role][role_name]',
									'id'          => 'fed_admin_post_user_role_name',
									'placeholder' => __( 'Role Name' ),
									'required'    => 'true'
								), 'text' ) ?>
							</div>
							<div class="col-md-4">
								<label>
									Role Slug
								</label>
								<?php echo fed_input_box( 'fed_user_add_role_slug', array(
									'name'        => 'user[role][role_slug]',
									'id'          => 'fed_admin_post_user_role_slug',
									'placeholder' => __( 'Role Slug' ),
									'required'    => 'true'
								), 'text' ) ?>
							</div>
							<div class="col-md-2 padd_top_20">
								<button class="btn btn-primary">
									<i class="fa fa-plus"></i>
									Add
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<div class="panel panel-primary fed_admin_user_role_delete_container">
		<div class="panel-heading">Custom User Roles</div>
		<div class="panel-body">
			<?php
			if ( count( $user_roles ) <= 0 ) {
				echo 'Sorry! No custom user role added';
			} else {
				foreach ( $user_roles as $key => $user_role ) {
					?>
					<ul class="list-group col-md-4">
						<li class="list-group-item">
							<form method="post"
								  class="fed_admin_user_role_delete fed_ajax_confirmation"
								  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">
								<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

								<input type="hidden"
									   name="fed_admin_unique"
									   value="fed_admin_setting_user"/>
								<input type="hidden"
									   name="fed_admin_unique_user"
									   value="fed_admin_setting_role_delete"/>
								<input type="hidden"
									   name="user[role][role_slug]"
									   value="<?php echo $key; ?>"/>
								<input type="hidden"
									   name="user[role][role_name]"
									   value="<?php echo $user_role; ?>"/>
								<button type="submit">
									<i class="fa fa-trash"></i>
								</button>
								<?php echo $user_role; ?>
							</form>
						</li>
					</ul>

					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
}