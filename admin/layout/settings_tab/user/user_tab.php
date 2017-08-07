<?php
function fed_admin_user_options_tab() {
	$fed_admin_options = get_option( 'fed_admin_settings_user' );
	?>
	<div class="row">
		<div class="col-md-3 padd_top_20">
			<ul class="nav nav-pills nav-stacked"
			    id="fed_admin_setting_user_profile_tabs"
			    role="tablist">
				<li role="presentation"
				    class="active">
					<a href="#fed_admin_user_profile_settings"
					   aria-controls="fed_admin_user_profile_settings"
					   role="tab"
					   data-toggle="tab">
						<i class="fa fa-user-plus"></i>
						Add/Delete Custom Role
					</a>
				</li>
			</ul>
		</div>
		<div class="col-md-9">
			<!-- Tab panes -->
			<div class="tab-content">
				<div role="tabpanel"
				     class="tab-pane active"
				     id="fed_admin_user_profile_settings">
					<?php
					fed_admin_user_role_tab($fed_admin_options);
					?>
				</div>
			</div>
		</div>
	</div>


	<?php
}