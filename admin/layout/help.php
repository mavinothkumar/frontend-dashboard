<?php

function fed_admin_help() {
	?>
	<div class="bc_fed container">
		<div class="row">
			<h3 class="fed_header_font_color">We will help you in better ways</h3>
			<div class="col-md-12 padd_top_20">
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="fed_install">
							<h5 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#install" aria-controls="install">
									How to Install and Configure
								</a>
							</h5>
						</div>
						<div id="install" class="collapse" role="tabpanel" aria-labelledby="fed_install">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12">
										<h4>Please follow the below steps to configure the frontend dashboard.</h4>
												<p>Please create the pages for all in one login or for individual [login, register, forgot password and dashboard]</p>
										<p><b>If you want to create all in one login page then </b></p>
										<p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
										<p>2. Give appropriate title</p>
										<p>3. Add shortcode in content area [fed_login]</p>
										<p>4. Change Page Attributes Template to FED Login [In Right Column]</p>
										<p><b>If you are want to create single page for login, register and forgot password then</b></p>
										<p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
										<p>2. Give appropriate title [As we are creating for Login Page]</p>
										<p>3. Add shortcode in content area [fed_login_only]</p>
										<p>4. Change Page Attributes Template to FED Login [In Right Column]</p>
										<p>5. For Register and Forgot Password, create the pages similar to above mentioned instruction and add the shortcode for Register [fed_register_only] and for Forgot password [fed_forgot_password_only] and save.</p>
										<p><b>To create the dashboard page</b></p>
										<p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
										<p>2. Give appropriate title</p>
										<p>3. Add shortcode in content area [fed_dashboard]</p>
										<p>4. Change Page Attributes Template to FED Login [In Right Column]</p>
										<p><b>Then Please go to Frontend Dashboard | Frontend Dashboard | Login (Tab) | Settings (Tab) | Please change the appropriate pages for the settings and do save.</b></p>


									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="headingOne">
							<h5 class="panel-title">
								<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-controls="collapseOne">
									How to Contact Us
								</a>
							</h5>
						</div>
						<div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne">
							<div class="row">
								<div class="col-md-7">
									<div class="flex_between">
										<div class="bc_item">
											<a href="mailto:support@buffercode.com">
												<img src="<?php echo plugins_url( 'admin/assets/images/mail.png', BC_FED_PLUGIN ) ?>"/>
											</a>
											<h5 class="text-center">Mail</h5>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="headingFive">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
									Shortcodes
								</a>
							</h4>
						</div>
						<div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
							<div class="panel-body">
								<h5>1. [fed_login] to generate login, registration, and reset forms</h5>
								<h5>2. [fed_login_only] to show only login page</h5>
								<h5>3. [fed_register_only] to show only register page</h5>
								<h5>4. [fed_forgot_password_only] to generate the forgot password page</h5>
								<h5>5. [fed_dashboard] to generate the dashboard page</h5>
								<h5>6. [fed_user role=user_role] to generate the role based user page</h5>

							</div>
						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="fed_filters">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#filters" aria-expanded="false" aria-controls="filters">
									Filter Hooks [Developers]
								</a>
							</h4>
						</div>
						<div id="filters" class="panel-collapse collapse" role="tabpanel" aria-labelledby="fed_filters">
							<div class="panel-body">
								<b>Note: This is not completely documented</b>
								<ol>
									<li class="col-md-4">fed_admin_input_item_options</li>
									<li class="col-md-4">fed_admin_input_items</li>
									<li class="col-md-4">fed_empty_value_for_user_profile</li>
									<li class="col-md-4">fed_no_update_fields</li>
									<li class="col-md-4">fed_payment_sources</li>
									<li class="col-md-4">fed_plugin_versions</li>
									<li class="col-md-4">fed_input_mandatory_required_fields</li>
									<li class="col-md-4">fed_login_form_filter</li>
									<li class="col-md-4">fed_registration_mandatory_fields</li>
									<li class="col-md-4">fed_login_mandatory_fields</li>
									<li class="col-md-4">fed_admin_dashboard_settings_menu_header</li>
									<li class="col-md-4">fed_frontend_main_menu</li>
									<li class="col-md-4">fed_admin_settings_upl</li>
									<li class="col-md-4">fed_restrictive_menu_names</li>
									<li class="col-md-4">fed_admin_login</li>
									<li class="col-md-4">fed_admin_settings_post</li>
									<li class="col-md-4">fed_register_form_submit</li>
									<li class="col-md-4">fed_user_extra_fields_registration</li>
									<li class="col-md-4">fed_get_date_formats_filter</li>
									<li class="col-md-4">fed_default_extended_fields</li>
									<li class="col-md-4">fed_admin_script_loading_pages</li>
									<li class="col-md-4">fed_update_post_status</li>
									<li class="col-md-4">fed_extend_country_code</li>
									<li class="col-md-4">fed_customize_admin_post_options</li>
									<li class="col-md-4">fed_customize_admin_login_options</li>
									<li class="col-md-4">fed_customize_admin_user_options</li>
									<li class="col-md-4">fed_customize_admin_user_profile_layout_options</li>
								</ol>
							</div>
						</div>
					</div>
					<div class="panel panel-primary">
						<div class="panel-heading" role="tab" id="fed_actions">
							<h4 class="panel-title">
								<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#actions" aria-expanded="false" aria-controls="actions">
									Action Hooks [Developers]
								</a>
							</h4>
						</div>
						<div id="actions" class="panel-collapse collapse" role="tabpanel" aria-labelledby="fed_actions">
							<div class="panel-body">
								<b>Note: This is not completely documented</b>
								<ol>
									<li class="col-md-4">fed_admin_settings_login_action</li>
									<li class="col-md-4">fed_before_dashboard_container</li>
									<li class="col-md-4">fed_after_dashboard_container</li>
									<li class="col-md-4">fed_before_login_only_form</li>
									<li class="col-md-4">fed_after_login_only_form</li>
									<li class="col-md-4">fed_add_main_sub_menu</li>
									<li class="col-md-4">fed_show_support_button_at_user_profile</li>
									<li class="col-md-4">fed_user_profile_below</li>
									<li class="col-md-4">fed_before_login_form</li>
									<li class="col-md-4">fed_after_login_form</li>
									<li class="col-md-4">fed_before_forgot_password_only_form</li>
									<li class="col-md-4">fed_after_forgot_password_only_form</li>
									<li class="col-md-4">fed_login_form_submit_custom</li>
									<li class="col-md-4">fed_before_register_only_form</li>
									<li class="col-md-4">fed_after_register_only_form</li>
									<li class="col-md-4">fed_admin_login_settings_template</li>
									<li class="col-md-4">fed_admin_input_fields_container_extra</li>
									<li class="col-md-4">fed_admin_login_settings_template</li>
									<li class="col-md-4">fed_admin_menu_status_version_below</li>
									<li class="col-md-4">fed_admin_menu_status_database_below</li>
									<li class="col-md-4">fed_admin_menu_status_below</li>
								</ol>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
}