<?php
/**
 * User Profile Layout
 */

function fed_user_profile_layout_design() {
	$fed_admin_options = get_option( 'fed_admin_settings_upl' );
	?>

        <div class="bc_fed row">
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
                            <i class="fa fa-cogs"></i>
                            <?php _e('Settings','fed') ?>
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
						fed_admin_user_profile_settings_tab( $fed_admin_options );
						?>
                    </div>
                </div>
            </div>
        </div>


	<?php
}