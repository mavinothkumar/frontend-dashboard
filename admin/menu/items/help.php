<?php

function fed_get_help_menu()
{
    ?>
    <div class="bc_fed container">
        <div class="row">
            <div class="col-md-12 padd_top_20">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?php _e('We will help you in better ways',
                                    'frontend-dashboard') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="panel-group" id="fed_menu_help" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="fed_videos">
                                    <h5 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#fed_menu_help"
                                           href="#videos"
                                           aria-controls="install">
                                            <?php _e('How to Videos', 'frontend-dashboard') ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="videos" class="collapse" role="tabpanel"
                                     aria-labelledby="fed_videos_heading">
                                    <div class="panel-body">
                                        <div class="row">
                                            <?php
                                            $items = fed_get_help_video_items();
                                            foreach ($items as $item) {
                                                ?>
                                                <div class="col-md-12">
                                                    <h4>
                                                        <i class="<?php echo $item['icon']; ?>"
                                                           aria-hidden="true"></i>
                                                        <a target="_blank" href="<?php echo $item['url']; ?>">
                                                            <?php echo $item['name']; ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="fed_install">
                                    <h5 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#fed_menu_help"
                                           href="#install"
                                           aria-controls="install"><?php _e('How to Install and Configure',
                                                    'frontend-dashboard') ?></a>
                                    </h5>
                                </div>
                                <div id="install" class="collapse" role="tabpanel"
                                     aria-labelledby="fed_install">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>Please follow the below steps to configure the frontend
                                                    dashboard.
                                                </h4>
                                                <p>Please create the pages for all in one login or for
                                                    individual
                                                    [login,
                                                    register, forgot password and dashboard]
                                                </p>
                                                <p>
                                                    <b>If you want to create all in one login page then</b>
                                                </p>
                                                <p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
                                                <p>2. Give appropriate title</p>
                                                <p>3. Add shortcode in content area [fed_login]</p>
                                                <p>4. Change Page Attributes Template to FED Login [In Right
                                                    Column]</p>
                                                <p>
                                                    <b>If you are want to create single page for login, register
                                                        and
                                                        forgot
                                                        password then
                                                    </b>
                                                </p>
                                                <p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
                                                <p>2. Give appropriate title [As we are creating for Login
                                                    Page]</p>
                                                <p>3. Add shortcode in content area [fed_login_only]</p>
                                                <p>4. Change Page Attributes Template to FED Login [In Right
                                                    Column]</p>
                                                <p>5. For Register and Forgot Password, create the pages similar
                                                    to
                                                    above
                                                    mentioned instruction and add the shortcode for Register
                                                    [fed_register_only]
                                                    and for Forgot password [fed_forgot_password_only] and save.
                                                </p>
                                                <p>
                                                    <b>To create the dashboard page</b>
                                                </p>
                                                <p>1. Please go to Admin Dashboard | Pages | Add New Pages</p>
                                                <p>2. Give appropriate title</p>
                                                <p>3. Add shortcode in content area [fed_dashboard]</p>
                                                <p>4. Change Page Attributes Template to FED Login [In Right
                                                    Column]</p>
                                                <p>
                                                    <b>Then Please go to Frontend Dashboard | Frontend Dashboard
                                                        | Login
                                                        (Tab) |
                                                        Settings (Tab) | Please change the appropriate pages for
                                                        the
                                                        settings
                                                        and do save.
                                                    </b>
                                                </p>


                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="contact_us">
                                    <h5 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#fed_menu_help"
                                           href="#fed_contact_us" aria-controls="fed_contact_us">
                                            <?php _e('How to Contact Us', 'frontend-dashboard') ?>
                                        </a>
                                    </h5>
                                </div>
                                <div id="fed_contact_us" class="collapse" role="tabpanel"
                                     aria-labelledby="contact_us">
                                    <div class="row">
                                        <div class="col-md-7">
                                            <div class="flex_between">
                                                <div class="bc_item">
                                                    <a href="https://buffercode.com/plugin/frontend-dashboard">
                                                        <img src="<?php echo plugins_url('admin/assets/images/chat.png',
                                                                BC_FED_PLUGIN) ?>"/>
                                                    </a>
                                                    <h5 class="text-center"><?php _e('Chat',
                                                                'frontend-dashboard') ?></h5>
                                                </div>
                                                <div class="bc_item">
                                                    <a href="mailto:support@buffercode.com">
                                                        <img src="<?php echo plugins_url('admin/assets/images/mail.png',
                                                                BC_FED_PLUGIN) ?>"/>
                                                    </a>
                                                    <h5 class="text-center"><?php _e('Mail',
                                                                'frontend-dashboard') ?></h5>
                                                </div>
                                                <div class="bc_item">
                                                    <a href="https://wordpress.org/support/plugin/frontend-dashboard/reviews/?filter=5#new-post">
                                                        <img src="<?php echo plugins_url('admin/assets/images/rate.png',
                                                                BC_FED_PLUGIN) ?>"/>
                                                    </a>
                                                    <h5 class="text-center"><?php _e('Rate us',
                                                                'frontend-dashboard') ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>


                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="shortcodes">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse"
                                           data-parent="#fed_menu_help" href="#fed_shortcodes" aria-expanded="false"
                                           aria-controls="fed_shortcodes">
                                            <?php _e('Shortcodes', 'frontend-dashboard') ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="fed_shortcodes" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="shortcodes">
                                    <div class="panel-body">
                                        <h5>1. [fed_login] to generate login, registration, and reset forms</h5>
                                        <h5>2. [fed_login_only] to show only login page</h5>
                                        <h5>3. [fed_register_only] to show only register page</h5>
                                        <h5>4. [fed_forgot_password_only] to generate the forgot password
                                            page</h5>
                                        <h5>5. [fed_dashboard] to generate the dashboard page</h5>
                                        <h5>6. [fed_user role=user_role] to generate the role based user
                                            page</h5>

                                    </div>
                                </div>
                            </div>


                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="fed_filters">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse"
                                           data-parent="#fed_menu_help" href="#filters" aria-expanded="false"
                                           aria-controls="filters">
                                            <?php _e('Filter Hooks [Developers]', 'frontend-dashboard') ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="filters" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="fed_filters">
                                    <div class="panel-body">
                                        <b><?php _e('Note: This is not completely documented',
                                                    'frontend-dashboard') ?></b>
                                        <br>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.8</b>
                                            </div>
                                            <div class="col-md-4">fed_admin_login_wp_restrict_template</div>
                                            <div class="col-md-4">fed_admin_login_register_template</div>
                                            <div class="col-md-4">fed_admin_login_settings_template</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.6</b>
                                            </div>
                                            <div class="col-md-4">fed_add_main_sub_menu</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.4</b>
                                            </div>
                                            <div class="col-md-4">fed_config</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.3</b>
                                            </div>
                                            <div class="col-md-4">fed_admin_settings_upl_color</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4</b>
                                            </div>
                                            <div class="col-md-4">fed_menu_title</div>
                                            <div class="col-md-4">fed_process_menu</div>
                                            <div class="col-md-4">fed_menu_default_page</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.3</b>
                                            </div>
                                            <div class="col-md-4">fed_login_only_filter</div>
                                            <div class="col-md-4">fed_register_only_filter</div>
                                            <div class="col-md-4">fed_convert_php_js_var</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.2</b>
                                            </div>
                                            <div class="col-md-4">fed_process_author_custom_details</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1</b>
                                            </div>
                                            <div class="col-md-4">fed_get_date_formats_filter</div>
                                            <div class="col-md-4">fed_default_extended_fields</div>
                                            <div class="col-md-4">fed_admin_script_loading_pages</div>
                                            <div class="col-md-4">fed_update_post_status</div>
                                            <div class="col-md-4">fed_extend_country_code</div>
                                            <div class="col-md-4">fed_customize_admin_post_options</div>
                                            <div class="col-md-4">fed_customize_admin_login_options</div>
                                            <div class="col-md-4">fed_customize_admin_user_options</div>
                                            <div class="col-md-4">
                                                fed_customize_admin_user_profile_layout_options
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.0</b>
                                            </div>
                                            <div class="col-md-4">fed_admin_input_item_options</div>
                                            <div class="col-md-4">fed_admin_input_items</div>
                                            <div class="col-md-4">fed_empty_value_for_user_profile</div>
                                            <div class="col-md-4">fed_no_update_fields</div>
                                            <div class="col-md-4">fed_payment_sources</div>
                                            <div class="col-md-4">fed_plugin_versions</div>
                                            <div class="col-md-4">fed_input_mandatory_required_fields</div>
                                            <div class="col-md-4">fed_login_form_filter</div>
                                            <div class="col-md-4">fed_registration_mandatory_fields</div>
                                            <div class="col-md-4">fed_login_mandatory_fields</div>
                                            <div class="col-md-4">fed_admin_dashboard_settings_menu_header</div>
                                            <div class="col-md-4">fed_frontend_main_menu</div>
                                            <div class="col-md-4">fed_admin_settings_upl</div>
                                            <div class="col-md-4">fed_restrictive_menu_names</div>
                                            <div class="col-md-4">fed_admin_login</div>
                                            <div class="col-md-4">fed_admin_settings_post</div>
                                            <div class="col-md-4">fed_register_form_submit</div>
                                            <div class="col-md-4">fed_user_extra_fields_registration</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="panel panel-secondary-heading">
                                <div class="panel-heading" role="tab" id="fed_actions">
                                    <h4 class="panel-title">
                                        <a class="collapsed" role="button" data-toggle="collapse"
                                           data-parent="#fed_menu_help" href="#actions" aria-expanded="false"
                                           aria-controls="actions">
                                            <?php _e('Action Hooks [Developers]', 'frontend-dashboard') ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="actions" class="panel-collapse collapse" role="tabpanel"
                                     aria-labelledby="fed_actions">
                                    <div class="panel-body">
                                        <b><?php _e('Note: This is not completely documented',
                                                    'frontend-dashboard') ?></b>
                                        <br>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.7</b>
                                            </div>
                                            <div class="col-md-4">
                                                <del>fed_add_main_sub_menu</del>
                                                to
                                                fed_add_main_sub_menu_action
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.4</b>
                                            </div>
                                            <div class="col-md-4">fed_frontend_dashboard_menu_container</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4.3</b>
                                            </div>
                                            <div class="col-md-4">fed_edit_main_menu_item_no_extra</div>
                                            <div class="col-md-4">fed_edit_main_menu_item_for_extra</div>
                                            <div class="col-md-4">fed_add_inline_css_at_head</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.4</b>
                                            </div>
                                            <div class="col-md-4">fed_override_default_page</div>
                                            <div class="col-md-4">fed_edit_main_menu_item_bottom</div>
                                            <div class="col-md-4">fed_add_main_menu_item_bottom</div>
                                            <div class="col-md-4">fed_enqueue_script_style_admin</div>
                                        </div>
                                        <hr>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.1.3</b>
                                            </div>
                                            <div class="col-md-4">fed_login_before_validation</div>
                                            <div class="col-md-4">fed_register_before_validation</div>
                                            <div class="col-md-4">fed_enqueue_script_style_frontend</div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <b>From version 1.0</b>
                                            </div>
                                            <div class="col-md-4">fed_admin_settings_login_action</div>
                                            <div class="col-md-4">fed_before_dashboard_container</div>
                                            <div class="col-md-4">fed_after_dashboard_container</div>
                                            <div class="col-md-4">fed_before_login_only_form</div>
                                            <div class="col-md-4">fed_after_login_only_form</div>
                                            <div class="col-md-4">
                                                <del>fed_add_main_sub_menu</del>
                                                (in 1.1.4.7)
                                            </div>
                                            <div class="col-md-4">fed_show_support_button_at_user_profile</div>
                                            <div class="col-md-4">fed_user_profile_below</div>
                                            <div class="col-md-4">fed_before_login_form</div>
                                            <div class="col-md-4">fed_after_login_form</div>
                                            <div class="col-md-4">fed_before_forgot_password_only_form</div>
                                            <div class="col-md-4">fed_after_forgot_password_only_form</div>
                                            <div class="col-md-4">fed_login_form_submit_custom</div>
                                            <div class="col-md-4">fed_before_register_only_form</div>
                                            <div class="col-md-4">fed_after_register_only_form</div>
                                            <div class="col-md-4">fed_admin_input_fields_container_extra</div>
                                            <div class="col-md-4">
                                                <del>fed_admin_login_settings_template</del>
                                                (in 1.1
                                                .4.8)
                                            </div>
                                            <div class="col-md-4">fed_admin_menu_status_version_below</div>
                                            <div class="col-md-4">fed_admin_menu_status_database_below</div>
                                            <div class="col-md-4">fed_admin_menu_status_below</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}