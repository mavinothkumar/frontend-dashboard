<?php
function fed_admin_login_settings_tab( $fed_login_settings ) {
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
			   value="fed_login_settings"/>

		<div class="fed_admin_panel_container">
			<div class="fed_admin_panel_content_wrapper">
				<div class="row">
					<div class="col-md-4 fed_menu_title">Login Page URL</div>
					<div class="col-md-4">
						<?php wp_dropdown_pages( array(
							'name'             => 'fed_admin_login[settings][fed_login_url]',
							'selected'         => isset( $fed_login_settings['settings']['fed_login_url'] ) ? $fed_login_settings['settings']['fed_login_url'] : '',
							'show_option_none' => 'Let it be default',
							'class'            => 'form-control'
						) ); ?>
					</div>
					<div class="col-md-4">
						<?php echo fed_show_help_message( array(
							'title'   => 'Instruction',
							'content' => 'Please add shortcode [fed_login] to show all in one or [fed_login_only] to show only login'
						) ); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title">Register Page URL</div>
					<div class="col-md-4">
						<?php wp_dropdown_pages( array(
							'name'             => 'fed_admin_login[settings][fed_register_url]',
							'selected'         => isset( $fed_login_settings['settings']['fed_register_url'] ) ? $fed_login_settings['settings']['fed_register_url'] : '',
							'show_option_none' => 'Let it be default',
							'class'            => 'form-control'
						) ); ?>
					</div>
					<div class="col-md-4">
						<?php echo fed_show_help_message( array(
							'title'   => 'Instruction',
							'content' => 'Please add shortcode [fed_register_only] to this page'
						) ); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title">Forgot Password Page URL</div>
					<div class="col-md-4">
						<?php wp_dropdown_pages( array(
							'name'             => 'fed_admin_login[settings][fed_forgot_password_url]',
							'selected'         => isset( $fed_login_settings['settings']['fed_forgot_password_url'] ) ? $fed_login_settings['settings']['fed_forgot_password_url'] : '',
							'show_option_none' => 'Let it be default',
							'class'            => 'form-control'
						) ); ?>
					</div>
					<div class="col-md-4">
						<?php echo fed_show_help_message( array(
							'title'   => 'Instruction',
							'content' => 'Please add shortcode [fed_forgot_password_only] to this page'
						) ); ?>
					</div>
				</div>
			<div class="row">
				<div class="col-md-4 fed_menu_title">Redirect After Logged in URL</div>
				<div class="col-md-4">
					<?php wp_dropdown_pages( array(
						'name'             => 'fed_admin_login[settings][fed_redirect_login_url]',
						'selected'         => isset( $fed_login_settings['settings']['fed_redirect_login_url'] ) ? $fed_login_settings['settings']['fed_redirect_login_url'] : '',
						'show_option_none' => 'Let it be default',
						'class'            => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 fed_menu_title">Redirect After Logged out URL</div>
				<div class="col-md-4">
					<?php wp_dropdown_pages( array(
						'name'             => 'fed_admin_login[settings][fed_redirect_logout_url]',
						'selected'         => isset( $fed_login_settings['settings']['fed_redirect_logout_url'] ) ? $fed_login_settings['settings']['fed_redirect_logout_url'] : '',
						'show_option_none' => 'Let it be default',
						'class'            => 'form-control'
					) ); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4 fed_menu_title">Dashboard</div>
				<div class="col-md-4">
					<?php wp_dropdown_pages( array(
						'name'             => 'fed_admin_login[settings][fed_dashboard_url]',
						'selected'         => isset( $fed_login_settings['settings']['fed_dashboard_url'] ) ? $fed_login_settings['settings']['fed_dashboard_url'] : '',
						'show_option_none' => 'Let it be default',
						'class'            => 'form-control'
					) ); ?>
				</div>
				<div class="col-md-4">
					<?php echo fed_show_help_message( array(
						'title'   => 'Instruction',
						'content' => 'Please add shortcode [fed_dashboard] to this page'
					) ); ?>
				</div>
			</div>
		</div>
		</div>

		<?php do_action( 'fed_admin_settings_login_template', $fed_login_settings ) ?>

		<div class="row">
			<div class="col-md-12">
				<input type="submit" class="btn btn-primary" value="Submit"/>
			</div>
		</div>
	</form>
	<?php
}