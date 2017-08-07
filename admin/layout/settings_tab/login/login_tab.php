<?php

function fed_admin_login_tab() {
	$fed_login = get_option( 'fed_admin_login' );
	?>
    <div class="row">
        <div class="col-md-3 padd_top_20">
            <ul class="nav nav-pills nav-stacked"
                id="fed_admin_setting_login_tabs"
                role="tablist">
                <li role="presentation"
                    class="active">
                    <a href="#fed_admin_login_settings"
                       aria-controls="fed_admin_login_settings"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-cogs"></i>
                        Settings
                    </a>
                </li>
				<li>
                    <a href="#fed_admin_register_settings"
                       aria-controls="fed_admin_register_settings"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-sign-in"></i>
                        Register
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel"
                     class="tab-pane active"
                     id="fed_admin_login_settings">
					<?php
					fed_admin_login_settings_tab($fed_login);
					?>
                </div>
				<div role="tabpanel"
                     class="tab-pane"
                     id="fed_admin_register_settings">
					<?php
					fed_admin_register_settings_tab($fed_login);
					?>
                </div>
            </div>
        </div>
    </div>

	<?php
}