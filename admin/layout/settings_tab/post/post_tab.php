<?php
/**
 * Post Options
 */
function fed_admin_post_options_tab() {
	$fed_admin_options = get_option( 'fed_admin_settings_post', array() );

//	var_dump( $fed_admin_options );
	?>
    <div class="row">
        <div class="col-md-3 padd_top_20">
            <ul class="nav nav-pills nav-stacked"
                id="fed_admin_setting_post_tabs"
                role="tablist">
                <li role="presentation"
                    class="active">
                    <a href="#fed_admin_post_settings"
                       aria-controls="fed_admin_post_settings"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-cogs"></i>
                        Settings
                    </a>
                </li>
                <li role="presentation">
                    <a href="#fed_admin_post_dashboard"
                       aria-controls="fed_admin_post_dashboard"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-cog"></i>
                        Dashboard Settings
                    </a>
                </li>
                <li role="presentation">
                    <a href="#fed_admin_post_menu"
                       aria-controls="fed_admin_post_menu"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-list"></i>
                        Menu
                    </a>
                </li>
                <li role="presentation">
                    <a href="#fed_admin_post_permissions"
                       aria-controls="fed_admin_post_permissions"
                       role="tab"
                       data-toggle="tab">
                        <i class="fa fa-universal-access"></i>
                        Permissions
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel"
                     class="tab-pane active"
                     id="fed_admin_post_settings">
					<?php
					fed_admin_post_settings_tab( $fed_admin_options );
					?>
                </div>
                <div role="tabpanel"
                     class="tab-pane"
                     id="fed_admin_post_dashboard">
					<?php
					fed_admin_post_dashboard_tab( $fed_admin_options );
					?>
                </div>
                <div role="tabpanel"
                     class="tab-pane"
                     id="fed_admin_post_menu">
					<?php
					fed_admin_post_menu_tab( $fed_admin_options );
					?>
                </div>
                <div role="tabpanel"
                     class="tab-pane"
                     id="fed_admin_post_permissions">
					<?php
					fed_admin_post_permissions_tab( $fed_admin_options );
					?>
                </div>
            </div>
        </div>
    </div>


	<?php
}

