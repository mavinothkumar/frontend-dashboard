<?php

function fed_admin_post_dashboard_tab($fed_admin_options) {
	//var_dump($fed_admin_options);
	?>
	<form method="post"
	      class="fed_admin_menu fed_ajax"
	      action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">

		<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

		<?php echo fed_loader(); ?>

		<input type="hidden"
		       name="fed_admin_unique"
		       value="fed_admin_settings_post"/>

		<input type="hidden"
		       name="fed_admin_unique_post"
		       value="fed_admin_settings_dashboard"/>


			<div class="fed_admin_panel_container">
				<div class="fed_admin_panel_content_wrapper">
					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Post Content</div>
						<div class="col-md-4">
								<?php echo fed_input_box( 'fed_post_dashboard_content', array(
									'name'    => 'dashboard[fed_post_dashboard_content]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_content'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_content'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Post Category</div>
						<div class="col-md-4">
								<?php echo fed_input_box( 'fed_post_dashboard_category', array(
									'name'    => 'dashboard[fed_post_dashboard_category]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_category'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_category'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Post Tag</div>
						<div class="col-md-4">

								<?php echo fed_input_box( 'fed_post_dashboard_tag', array(
									'name'    => 'dashboard[fed_post_dashboard_tag]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_tag'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_tag'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
							</div>
					</div>
					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Featured Image</div>
						<div class="col-md-4">

								<?php echo fed_input_box( 'fed_post_dashboard_featured_image', array(
									'name'    => 'dashboard[fed_post_dashboard_featured_image]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_featured_image'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_featured_image'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
							</div>
						</div>

					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Post Format</div>
						<div class="col-md-4">

								<?php echo fed_input_box( 'fed_post_dashboard_post_format', array(
									'name'    => 'dashboard[fed_post_dashboard_post_format]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_post_format'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_post_format'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
							</div>
						</div>

					<div class="row">
						<div class="col-md-3 fed_menu_title">Disable Allow Comments</div>
						<div class="col-md-4">

								<?php echo fed_input_box( 'fed_post_dashboard_allow_comments', array(
									'name'    => 'dashboard[fed_post_dashboard_allow_comments]',
									'value'   => isset( $fed_admin_options['dashboard']['fed_post_dashboard_allow_comments'] ) ? $fed_admin_options['dashboard']['fed_post_dashboard_allow_comments'] : '',
									'default_value' => 'Enable',
								), 'checkbox' ); ?>
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