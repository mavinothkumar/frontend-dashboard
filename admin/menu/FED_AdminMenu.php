<?php

if ( ! class_exists('FED_AdminMenu')) {
    /**
     * Class FED_AdminMenu
     */
    class FED_AdminMenu
    {
        public function __construct()
        {
            add_action('admin_menu', array($this, 'menu'));
        }

        public function menu()
        {
            add_menu_page(
                    __('Frontend Dashboard', 'frontend-dashboard'),
                    __('Frontend Dashboard', 'frontend-dashboard'),
                    'manage_options',
                    'fed_settings_menu',
                    array($this, 'common_settings'),
                    plugins_url('common/assets/images/d.png', BC_FED_PLUGIN),
                    2
            );

            $main_menu = $this->fed_get_main_sub_menu();
            foreach ($main_menu as $index => $menu) {
                add_submenu_page('fed_settings_menu',
                        sprintf(__('%s', 'frontend-dashboard'), $menu['page_title']),
                        sprintf(__('%s', 'frontend-dashboard'), $menu['menu_title']),
                        $menu['capability'],
                        $index,
                        $menu['callback']);
            }

            do_action('fed_add_main_sub_menu_action');

        }

        public function common_settings()
        {
            $menu            = $this->admin_dashboard_settings_menu_header();
            $menu_counter    = 0;
            $content_counter = 0;
            ?>

            <div class="bc_fed container fed_tabs_container">
                <h3 class="fed_header_font_color"><?php _e('Dashboard Settings', 'frontend-dashboard') ?></h3>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs"
                            id="fed_admin_setting_tabs"
                            role="tablist">

                            <?php foreach ($menu as $index => $item) { ?>
                                <li role="presentation"
                                    class="<?php echo (0 === $menu_counter) ? 'active' : ''; ?>">
                                    <a href="#<?php echo $index; ?>"
                                       aria-controls="<?php echo $index; ?>"
                                       role="tab"
                                       data-toggle="tab">
                                        <i class="<?php echo $item['icon_class'] ?>"></i>
                                        <?php printf(__('%s', 'frontend-dashboard'), $item['name']) ?>
                                    </a>
                                </li>
                                <?php
                                $menu_counter++;
                            }
                            ?>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <?php foreach ($menu as $index => $item) { ?>
                                <div role="tabpanel"
                                     class="tab-pane <?php echo (0 === $content_counter) ? 'active' : ''; ?>"
                                     id="<?php echo $index; ?>">
                                    <?php
                                    $this->call_function_method($item);
                                    ?>
                                </div>
                                <?php
                                $content_counter++;
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php fed_menu_icons_popup(); ?>
            </div>
            <?php
        }

        public function dashboard_menu()
        {
            $menus      = fed_fetch_table_rows_with_key(BC_FED_MENU_DB, 'menu_slug');
            $user_roles = fed_get_user_roles();
            ?>
            <div class="bc_fed container">
                <!-- Show Empty form to add Dashboard Menu-->
                <div class="row padd_top_20 hide"
                     id="fed_add_new_menu_container">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <b><?php _e('Add New Menu', 'frontend-dashboard') ?></b>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <form method="post"
                                      class="fed_admin_menu fed_menu_ajax"
                                      action="<?php echo admin_url('admin-ajax.php?action=fed_admin_setting_form_dashboard_menu') ?>">
                                    <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>

                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label><?php _e('Menu Name', 'frontend-dashboard') ?></label>
                                                        <input type="text"
                                                               title="Menu Name"
                                                               name="fed_menu_name"
                                                               class="form-control fed_menu_name"
                                                               value=""
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>
                                                            <?php _e('Menu Slug', 'frontend-dashboard') ?>
                                                            <?php echo fed_show_help_message(array(
                                                                    'title'   => 'Info',
                                                                    'content' => 'Please do not change the Slug until its mandatory',
                                                            ));
                                                            ?>
                                                        </label>
                                                        <input type="text"
                                                               title="Menu Slug"
                                                               name="fed_menu_slug"
                                                               class="form-control fed_menu_slug"
                                                               value=""
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label><?php _e('Menu Icon', 'frontend-dashboard') ?></label>
                                                        <input type="text"
                                                               name="menu_image_id"
                                                               class="form-control menu_image_id"
                                                               data-toggle="modal"
                                                               data-target=".fed_show_fa_list"
                                                               placeholder=""
                                                               data-fed_menu_box_id="menu_image_id"

                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label><?php _e('Menu Order', 'frontend-dashboard') ?></label>
                                                        <input type="number"
                                                               name="fed_menu_order"
                                                               class="form-control fed_menu_order"
                                                               placeholder=""
                                                        />
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label><?php _e('Disable User Profile',
                                                                    'frontend-dashboard') ?></label>
                                                        <br>
                                                        <input title="Disable User Profile" type="checkbox"
                                                               name="show_user_profile"
                                                               value="Disable"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row padd_top_20">
                                                <div class="col-md-12">
                                                    <label><?php _e('Select user role to show this input field',
                                                                'frontend-dashboard') ?></label>
                                                </div>
                                                <div class="col-md-12 ">
                                                    <?php
                                                    foreach ($user_roles as $key => $user_role) {
                                                        ?>
                                                        <div class="col-md-2">
                                                            <?php echo fed_input_box('user_role', array(
                                                                    'default_value' => 'Enable',
                                                                    'name'          => 'user_role['.$key.']',
                                                                    'label'         => esc_attr(sprintf(__('%s',
                                                                            'frontend-dashboard'), $user_role)),
                                                                    'value'         => 'Enable',
                                                            ), 'checkbox'); ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="col-md-12">
                                                <div class="form-group fed_menu_save_button">
                                                    <button type="submit"
                                                            class="btn btn-primary fed_menu_save">
                                                        <i class="fa fa-plus"></i>
                                                        <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php do_action('fed_add_main_menu_item_bottom') ?>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!--List / Edit Dashboard Menus-->
                <div class="row padd_top_20">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    <b><?php _e('Menu Lists', 'frontend-dashboard') ?></b>
                                </h3>
                            </div>
                            <div class="panel-body">
                                <div class="fed_dashboard_menu_items_container">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="button"
                                                        role="link"
                                                        class="btn btn-primary fed_menu_save fed_menu_save_button_toggle">
                                                    <i class="fa fa-plus"></i>
                                                    <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group fed_search_box  col-md-10">
                                                <input id="fed_menu_search" type="text" class="form-control
												fed_menu_search"
                                                       placeholder="<?php _e('Search Menu Name...',
                                                               'frontend-dashboard') ?>">
                                                <span class="input-group-btn">
										        <button class="btn btn-danger fed_menu_search_clear hide" type="button">
													<i class="fa fa-times-circle" aria-hidden="true"></i>
												</button>
										      </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-group" id="fedmenu" role="tablist" aria-multiselectable="true">
                                        <?php
                                        $collapse = 0;
                                        foreach ($menus as $index => $menu) {
                                            if ($collapse === 0) {
                                                $collapsed = '';
                                                $in        = 'in';
                                            } else {
                                                $collapsed = 'collapsed';
                                                $in        = '';
                                            }

                                            $collapse++;
                                            ?>
                                            <div class="fed_dashboard_menu_single_item <?php echo $index ?>">
                                                <div class="panel panel-secondary-heading">
                                                    <div class="panel-heading <?php echo $collapsed; ?>"
                                                         role="tab" id="<?php echo $index ?>" data-toggle="collapse"
                                                         data-parent="#fedmenu" href="#collapse<?php echo $index ?>"
                                                         aria-expanded="true" aria-controls="collapse<?php echo $index
                                                    ?>">
                                                        <h4 class="panel-title">
                                                            <a>
                                                                <?php
                                                                echo '<span class="'.$menu['menu_image_id'].'"></span>'; ?>
                                                                <?php printf(__('%s', 'frontend-dashboard'),
                                                                        $menu['menu']) ?>
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="collapse<?php echo $index ?>"
                                                         class="panel-collapse collapse <?php echo $in ?>"
                                                         role="tabpanel" aria-labelledby="<?php echo $index ?>">
                                                        <div class="panel-body">
                                                            <form method="post"
                                                                  class="fed_admin_menu fed_menu_ajax"
                                                                  action="<?php echo admin_url('admin-ajax.php?action=fed_admin_setting_form_dashboard_menu') ?>">

                                                                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                                                <input type="hidden"
                                                                       name="menu_id"
                                                                       value="<?php echo $menu['id'] ?>"/>
                                                                <input type="hidden"
                                                                       name="fed_menu_slug"
                                                                       value="<?php echo esc_attr($menu['menu_slug']) ?>"
                                                                />
                                                                <div class="row">
                                                                    <div class="col-md-10">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label><?php _e('Menu Name',
                                                                                                    'frontend-dashboard') ?></label>
                                                                                        <input type="text"
                                                                                               name="fed_menu_name"
                                                                                               class="form-control fed_menu_name"
                                                                                               value="<?php echo esc_attr($menu['menu']) ?>"
                                                                                               required="required"
                                                                                               placeholder="Menu Name"
                                                                                        />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group">
                                                                                        <label><?php _e('Menu Icon',
                                                                                                    'frontend-dashboard') ?></label>
                                                                                        <input type="text"
                                                                                               name="menu_image_id"
                                                                                               class="form-control <?php echo esc_attr($menu['menu_slug']) ?>"
                                                                                               value="<?php echo esc_attr($menu['menu_image_id']) ?>"
                                                                                               data-toggle="modal"
                                                                                               data-target=".fed_show_fa_list"
                                                                                               placeholder="Menu Icon"
                                                                                               data-fed_menu_box_id="<?php echo esc_attr($menu['menu_slug']) ?>"
                                                                                        />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group">
                                                                                        <label><?php _e('Menu Order',
                                                                                                    'frontend-dashboard') ?></label>
                                                                                        <input type="number"
                                                                                               name="fed_menu_order"
                                                                                               class="form-control fed_menu_order"
                                                                                               value="<?php echo esc_attr(
                                                                                                       $menu['menu_order']) ?>"
                                                                                               required="required"
                                                                                               placeholder="Menu Order"
                                                                                        />
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group text-center">
                                                                                        <?php
                                                                                        echo fed_input_box('show_user_profile',
                                                                                                array(
                                                                                                        'default_value' => 'Disable',
                                                                                                        'label'         => __('Disable User Profile',
                                                                                                                'frontend-dashboard'),
                                                                                                        'value'         => esc_attr($menu['show_user_profile']),
                                                                                                ), 'checkbox');
                                                                                        ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <button type="submit"
                                                                                        class="btn btn-primary fed_menu_save"
                                                                                >
                                                                                    <i class="fa fa-save"></i>
                                                                                </button>
                                                                                <?php if ($menu['extra'] !== 'no') { ?>
                                                                                    <button type="submit"
                                                                                            class="btn btn-danger fed_menu_delete">
                                                                                        <i class="fa fa-trash"></i>
                                                                                    </button>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="row">
                                                                            <div class="col-md-12 padd_top_10">
                                                                                <div class="col-md-2">
                                                                                    <label><?php _e('Select User Role(s)') ?></label>
                                                                                </div>
                                                                                <?php
                                                                                foreach ($user_roles as $key => $user_role) {
                                                                                    $res     = isset($menu['user_role']) ? $menu['user_role'] : false;
                                                                                    $c_value = 'Disable';
                                                                                    if ($res) {
                                                                                        $c_value = in_array($key,
                                                                                                unserialize($res),
                                                                                                false) ? 'Enable' : 'Disable';
                                                                                    }

                                                                                    ?>
                                                                                    <div class="col-md-2">
                                                                                        <?php echo fed_input_box('user_role',
                                                                                                array(
                                                                                                        'default_value' => 'Enable',
                                                                                                        'name'          => 'user_role['.$key.']',
                                                                                                        'label'         => esc_attr($user_role),
                                                                                                        'value'         => $c_value,
                                                                                                ), 'checkbox'); ?>
                                                                                    </div>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <?php do_action('fed_edit_main_menu_item_bottom',
                                                                        $menu) ?>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php fed_menu_icons_popup(); ?>
            </div>
            <?php
        }

        public function user_profile()
        {
            $user_profile = fed_fetch_table_rows_with_key(BC_FED_USER_PROFILE_DB, 'input_meta');

            if ($user_profile instanceof WP_Error) {
                ?>
                <div class="bc_fed container fed_UP_container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">&times;
                                </button>
                                <strong><?php echo $user_profile->get_error_message() ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                $this->show_admin_profile_page($user_profile);
            }
        }

        public function post_fields()
        {
            $post_fields = fed_fetch_table_rows_with_key(BC_FED_POST_DB, 'input_meta');
            if ($post_fields instanceof WP_Error) {
                ?>
                <div class="bc_fed container fed_UP_container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">&times;
                                </button>
                                <strong><?php echo $post_fields->get_error_message() ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                $this->post_fields_layout($post_fields);
            }
        }

        public function add_user_profile()
        {
            $id              = '';
            $add_edit_action = __('Add New ', 'frontend-dashboard');
            $selected        = '';
            $action          = isset($_GET['fed_action']) ? esc_attr($_GET['fed_action']) : '';
            if (isset($_GET['fed_input_id'])) {
                $id              = (int)$_GET['fed_input_id'];
                $add_edit_action = __('Edit ', 'frontend-dashboard');
            }

            if ($action !== 'profile' && $action !== 'post') {
                ?>
                <div class="bc_fed container fed_add_page_profile_container text-center">
                    <a class="btn btn-primary"
                       href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=post' ?>">
                        <i class="fa fa-envelope"></i>
                        <?php _e('Add Extra Post Field', 'frontend-dashboard') ?>
                    </a>
                    <a class="btn btn-primary"
                       href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=profile' ?>">
                        <i class="fa fa-user-plus"></i>
                        <?php _e('Add Extra User Profile Field', 'frontend-dashboard') ?>
                    </a>
                </div>
                <?php
                return;
            }

            if ($action === 'profile') {
                $page = __('Profile', 'frontend-dashboard');
                $url  = menu_page_url('fed_user_profile', false);
                if (is_int($id)) {
                    $rows = fed_fetch_table_row_by_id(BC_FED_USER_PROFILE_DB, $id);
                    if ($rows instanceof WP_Error) {
                        ?>
                        <div class="container fed_UP_container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <button type="button"
                                                class="close"
                                                data-dismiss="alert"
                                                aria-hidden="true">&times;
                                        </button>
                                        <strong><?php echo $rows->get_error_message() ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        return;
                    }
                    $row      = fed_process_user_profile($rows, $action);
                    $selected = $row['input_type'];
                } else {
                    $row = fed_get_empty_value_for_user_profile($action);
                }
            }
            if ($action === 'post') {
                $page = __('Post', 'frontend-dashboard');
                $url  = menu_page_url('fed_post_fields', false);
                if (is_int($id)) {
                    $rows = fed_fetch_table_row_by_id(BC_FED_POST_DB, $id);
                    if ($rows instanceof WP_Error) {
                        ?>
                        <div class="container fed_UP_container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-danger">
                                        <button type="button"
                                                class="close"
                                                data-dismiss="alert"
                                                aria-hidden="true">&times;
                                        </button>
                                        <strong><?php echo $rows->get_error_message() ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        return;
                    }
                    $row      = fed_process_user_profile($rows, $action);
                    $selected = $row['input_type'];
                } else {
                    $row = fed_get_empty_value_for_user_profile($action);
                }
            }
            $menu_options = fed_fetch_menu();
            $buttons      = fed_admin_user_profile_select($selected);
            ?>
            <div class="bc_fed container fed_add_edit_input_container">
                <div class="row fed_admin_up_select">
                    <div class="col-md-3">
                        <a class="btn btn-primary"
                           href="<?php echo $url; ?>">
                            <i class="fa fa-mail-reply"></i>
                            <?php _e('Back to', 'frontend-dashboard') ?> <?php echo $page ?>
                        </a>
                    </div>
                    <div class="col-md-3 col-lg-offset-1 text-center">
                        <h4 class="fed_header_font_color text-uppercase">
                            <?php echo $add_edit_action; ?><?php echo $page ?><?php _e('field', 'frontend-dashboard') ?>
                        </h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="fed_buttons_container <?php echo $buttons['class']; ?>">
                            <?php
                            foreach ($buttons['options'] as $index => $button) {
                                $active = $buttons['value'] === $index ? 'active' : '';
                                ?>
                                <div class="fed_button <?php echo $active; ?>" data-button="<?php echo $index; ?>">
                                    <div class="fed_button_image">
                                        <img src="<?php echo $button['image'] ?>"/>
                                    </div>
                                    <div class="fed_button_text"><?php echo $button['name'] ?></div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="fed_all_input_fields_container">

                    <?php
                    //			Input Type
                    fed_admin_input_fields_single_line($row, $action, $menu_options);
                    //            Email Type
                    fed_admin_input_fields_mail($row, $action, $menu_options);
                    //            Number Type
                    fed_admin_input_fields_number($row, $action, $menu_options);
                    //            Password
                    fed_admin_input_fields_password($row, $action, $menu_options);
                    //            TextArea
                    fed_admin_input_fields_multi_line($row, $action, $menu_options);
                    //            Checkbox
                    fed_admin_input_fields_checkbox($row, $action, $menu_options);
                    //            Radio
                    fed_admin_input_fields_radio($row, $action, $menu_options);
                    //            Select / Dropdown
                    fed_admin_input_fields_select($row, $action, $menu_options);
                    // URL
                    fed_admin_input_fields_url($row, $action, $menu_options);

                    do_action('fed_admin_input_fields_container_extra', $row, $action, $menu_options);
                    ?>

                </div>
            </div>
            <?php
        }

        public function plugin_pages()
        {
            $plugins = array(
                    'plugins' => array(
                            'fed_extra'   => array(
                                    'id'           => 'BC_FED_EXTRA_PLUGIN',
                                    'version'      => '1.1',
                                    'directory'    => 'frontend-dashboard-extra/frontend-dashboard-extra.php',
                                    'title'        => 'Frontend Dashboard Extra',
                                    'description'  => 'Frontend Dashboard Extra WordPress plugin is a supportive plugin for Frontend Dashboard with supportive additional features likes extra Calendar for selecting date and time, Colors and File Upload for images.',
                                    'thumbnail'    => plugins_url('admin/assets/images/plugins/frontend_dashboard_extra.jpg',
                                            BC_FED_PLUGIN),
                                    'download_url' => 'http://buffercode.com/plugin/frontend-dashboard-extra',
                                    'upload_url'   => 'http://buffercode.com/plugin/frontend-dashboard-extra',
                                    'pricing'      => array(
                                            'type'          => 'Free',
                                            'amount'        => '0',
                                            'currency'      => '$',
                                            'currency_code' => 'USD',
                                            'purchase_url'  => '',
                                    ),
                            ),
                            'fed_captcha' => array(
                                    'id'           => 'BC_FED_CAPTCHA_PLUGIN',
                                    'version'      => '1.0',
                                    'directory'    => 'frontend-dashboard-captcha/frontend-dashboard-captcha.php',
                                    'title'        => 'Frontend Dashboard Captcha',
                                    'description'  => 'Frontend Dashboard Captcha WordPress plugin is a supportive plugin for Frontend Dashboard to protect against spam in Login and Register form.',
                                    'thumbnail'    => plugins_url('admin/assets/images/plugins/frontend_dashboard_captcha.jpg',
                                            BC_FED_PLUGIN),
                                    'download_url' => 'http://buffercode.com/plugin/frontend-dashboard-captcha',
                                    'upload_url'   => 'http://buffercode.com/plugin/frontend-dashboard-captcha',
                                    'pricing'      => array(
                                            'type'          => 'Free',
                                            'amount'        => '0',
                                            'currency'      => '$',
                                            'currency_code' => 'USD',
                                            'purchase_url'  => '',
                                    ),
                            ),
                            'fed_pages'   => array(
                                    'id'           => 'FED_PAGES_PLUGIN',
                                    'version'      => '1.2',
                                    'directory'    => 'frontend-dashboard-pages/frontend-dashboard-pages.php',
                                    'title'        => 'Frontend Dashboard Pages',
                                    'description'  => 'Frontend Dashboard Pages is a plugin to show pages inside the Frontend Dashboard menu. The assigning page may contain content, images and even shortcodes',
                                    'thumbnail'    => plugins_url('admin/assets/images/plugins/frontend_dashboard_pages.jpg',
                                            BC_FED_PLUGIN),
                                    'download_url' => 'https://buffercode.com/plugin/frontend-dashboard-pages',
                                    'upload_url'   => 'https://buffercode.com/plugin/frontend-dashboard-pages',
                                    'pricing'      => array(
                                            'type'          => 'Free',
                                            'amount'        => '0',
                                            'currency'      => '$',
                                            'currency_code' => 'USD',
                                            'purchase_url'  => '',
                                    ),
                            ),
                    ),
                    'date'    => date('Y-m-d H:i:s'),
            );
            if (false === ($api = get_transient('fed_plugin_list_api'))) {
                $api = get_plugin_list();
                set_transient('fed_plugin_list_api', $api, 12 * HOUR_IN_SECONDS);
            }
            if ($api) {
                $plugins = json_decode($api);
                ?>
                <div class="bc_fed container fed_plugins">
                    <div class="row  padd_top_20">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h3 class="panel-title">
                                        <?php _e('Add-Ons', 'frontend-dashboard') ?>
                                    </h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php foreach ($plugins->plugins as $single) { ?>
                                            <div class="col-md-6 col-xs-12 col-sm-12">
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title">
                                                            <?php echo $single->title; ?>
                                                            <span class="pull-right">
											<i class="fa fa-code-fork" aria-hidden="true"></i>
                                                                <?php
                                                                if (is_plugin_active($single->directory)) {
                                                                    echo constant($single->id.'_VERSION');
                                                                } else {
                                                                    echo $single->version;
                                                                } ?>
										</span>
                                                        </h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <img class="img-responsive"
                                                                     src="<?php echo $single->thumbnail; ?>"
                                                                     alt="">
                                                            </div>
                                                            <div class="col-md-8">
                                                                <p class="fed_plugin_description">
                                                                    <?php echo wp_trim_words($single->description,
                                                                            100); ?>
                                                                </p>
                                                                <div class="fed_plugin_link">
                                                                    <a href="<?php echo $single->download_url ?>">
                                                                        <button class="btn btn-warning">
                                                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                                                            View
                                                                        </button>
                                                                    </a>
                                                                    <?php
                                                                    if (is_plugin_active($single->directory)) {
                                                                        ?>
                                                                        <button class="btn btn-info">
                                                                            <i class="fa fa-check"
                                                                               aria-hidden="true"></i>
                                                                            Installed
                                                                        </button>
                                                                        <?php
                                                                        if ($single->version > constant($single->id.'_VERSION'
                                                                                )) {
                                                                            ?>
                                                                            <button class="btn btn-danger">
                                                                                <i class="fa fa-refresh"
                                                                                   aria-hidden="true"></i>
                                                                                Update
                                                                            </button>
                                                                            <?php
                                                                        }

                                                                    } else {
                                                                        if ($single->pricing->type === 'Free') { ?>
                                                                            <a href="<?php echo $single->download_url; ?>"
                                                                               class="btn btn-primary"
                                                                               role="button">
                                                                                <i class="fa fa-download"
                                                                                   aria-hidden="true"></i>
                                                                                <?php _e('Download',
                                                                                        'frontend-dashboard') ?>
                                                                            </a>
                                                                        <?php }
                                                                        if ($single->pricing->type === 'Pro') {
                                                                            ?>
                                                                            <a href="#" class="btn btn-primary"
                                                                               role="button">
                                                                                <i class="fa fa-shopping-cart"
                                                                                   aria-hidden="true"></i>
                                                                                <?php echo $single->pricing->currency.$single->pricing->amount; ?>
                                                                            </a>
                                                                            <?php
                                                                        }

                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php
            } else {
                ?>
                <div class="bc_fed container fed_plugins">
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong><?php _e('Sorry there is some issue in internet connectivity.',
                                    'frontend-dashboard') ?></strong>
                    </div>
                    <?php echo fed_loader(''); ?>
                </div>
                <?php
            }
        }

        public function help()
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

                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-secondary-heading">
                                        <div class="panel-heading" role="tab" id="fed_install">
                                            <h5 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
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
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <h5 class="panel-title">
                                                <a role="button" data-toggle="collapse" data-parent="#accordion"
                                                   href="#collapseOne" aria-controls="collapseOne">
                                                    <?php _e('How to Contact Us', 'frontend-dashboard') ?>
                                                </a>
                                            </h5>
                                        </div>
                                        <div id="collapseOne" class="collapse" role="tabpanel"
                                             aria-labelledby="headingOne">
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
                                        <div class="panel-heading" role="tab" id="headingFive">
                                            <h4 class="panel-title">
                                                <a class="collapsed" role="button" data-toggle="collapse"
                                                   data-parent="#accordion" href="#collapseFive" aria-expanded="false"
                                                   aria-controls="collapseFive">
                                                    <?php _e('Shortcodes', 'frontend-dashboard') ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseFive" class="panel-collapse collapse" role="tabpanel"
                                             aria-labelledby="headingFive">
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
                                                   data-parent="#accordion" href="#filters" aria-expanded="false"
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
                                                   data-parent="#accordion" href="#actions" aria-expanded="false"
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

        public function status()
        {
            global $wp_version;

            /**
             * Login
             */
            $fed_login           = get_option('fed_admin_login', array());
            $login_settings      = fed_enable_disable(isset($fed_login['settings']['fed_login_url']) ? true : false);
            $redirect_login_url  = fed_enable_disable(isset($fed_login['settings']['fed_redirect_login_url']) ? true : false);
            $redirect_logout_url = fed_enable_disable(isset($fed_login['settings']['fed_redirect_logout_url']) ? true : false);
            $dashboard           = fed_enable_disable(isset($fed_login['settings']['fed_dashboard_url']) ? true : false);

            /**
             * Post
             */
            $fed_post           = get_option('fed_cp_admin_settings', array());
            $fed_post_settings  = fed_enable_disable(isset($fed_post['post']['settings']) ? true : false);
            $fed_post_dashboard = fed_enable_disable(isset($fed_post['post']['dashboard']) ? true : false);
            $fed_post_menu      = fed_enable_disable(isset($fed_post['post']['menu']) ? true : false);
            $post_permissions   = fed_enable_disable(isset($fed_post['post']['permissions']) ? true : false);

            /**
             * User Profile Layout
             */
            $fed_upl          = get_option('fed_admin_settings_upl', array());
            $fed_upl_settings = fed_enable_disable(isset($fed_upl['settings']) ? true : false)


            ?>
            <div class="bc_fed container">
                <div class="row padd_top_20">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><?php _e('Status', 'frontend-dashboard') ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">
                                                        Frontend Dashboard->User Profile Layout
                                                    </h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td class="fed_header_font_color">Settings</td>
                                                                <td><?php echo $fed_upl_settings; ?></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?php _e('Versions',
                                                                'frontend-dashboard') ?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td class="fed_header_font_color">PHP Version</td>
                                                                <td><?php echo PHP_VERSION; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">WordPress Version</td>
                                                                <td><?php echo $wp_version; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">Plugin Version</td>
                                                                <td><?php echo BC_FED_PLUGIN_VERSION; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">Plugins & Ads-ons</td>
                                                                <td>
                                                                    <?php echo fed_plugin_versions() ?>
                                                                </td>
                                                            </tr>

                                                            <?php do_action('fed_admin_menu_status_version_below') ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Frontend Dashboard->Post</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td class="fed_header_font_color">Settings</td>
                                                                <td><?php echo $fed_post_settings; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">Dashboard Settings
                                                                </td>
                                                                <td><?php echo $fed_post_dashboard; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">Menu</td>
                                                                <td><?php echo $fed_post_menu; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">Permissions</td>
                                                                <td><?php echo $post_permissions; ?></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?php _e('PHP Extensions',
                                                                'frontend-dashboard') ?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Table Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td class="fed_header_font_color">cURL</td>
                                                                <td>
                                                                    <?php
                                                                    echo fed_enable_disable(fed_check_extension_loaded('cURL'));
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">JSON</td>
                                                                <td>
                                                                    <?php
                                                                    echo fed_enable_disable(fed_check_extension_loaded('JSON'));
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fed_header_font_color">OpenSSL</td>
                                                                <td>
                                                                    <?php
                                                                    echo fed_enable_disable(fed_check_extension_loaded('OpenSSL'));
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?php _e('Database',
                                                                'frontend-dashboard') ?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th>Table Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php foreach (fed_get_table_status() as $table) { ?>
                                                                <tr>
                                                                    <td class="fed_header_font_color">
                                                                        <?php echo $table['title'].' ( '.$table['plugin_name'].' )' ?>
                                                                    </td>
                                                                    <td><?php echo $table['status']; ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                            <?php do_action('fed_admin_menu_status_database_below') ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">Frontend Dashboard->Login</h3>
                                                </div>
                                                <div class="panel-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover">
                                                            <thead>
                                                            <tr>
                                                                <th colspan="2">
                                                                    <span class="fed_header_font_color">Settings</span>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Name</th>
                                                                <th>Status</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr>
                                                                <td>Login Page URL</td>
                                                                <td><?php echo $login_settings; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Redirect After Logged in URL</td>
                                                                <td><?php echo $redirect_login_url; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Redirect After Logged out URL</td>
                                                                <td><?php echo $redirect_logout_url ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Dashboard</td>
                                                                <td><?php echo $dashboard; ?></td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php do_action('fed_admin_menu_status_below'); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        private function admin_dashboard_settings_menu_header()
        {
            $menu = array(
                    'login'               => array(
                            'icon_class' => 'fas fa-sign-in-alt',
                            'name'       => __('Login', 'frontend-dashboard'),
                            'callable'   => 'fed_admin_login_tab',
                    ),
                    'user'                => array(
                            'icon_class' => 'fa fa-user',
                            'name'       => __('User', 'frontend-dashboard'),
                            'callable'   => 'fed_admin_user_options_tab',
                    ),
                    'user_profile_layout' => array(
                            'icon_class' => 'fa fa-user-secret',
                            'name'       => __('User Profile Layout', 'frontend-dashboard'),
                            'callable'   => 'fed_user_profile_layout_design',
                    ),
            );

            if ( ! defined('FED_CP_PLUGIN_VERSION')) {
                $menu['post_options'] = array(
                        'icon_class' => 'fa fa-envelope',
                        'name'       => __('Post', 'frontend-dashboard'),
                        'callable'   => 'fed_admin_post_options_tab',
                );
            }

            return apply_filters('fed_admin_dashboard_settings_menu_header', $menu);
        }

        private function call_function_method($item)
        {
            $parameter = '';
            if (isset($item['callable']['parameters'])) {
                $parameter = $item['callable']['parameters'];
            }


            if (is_string($item['callable']) && function_exists($item['callable'])) {
                call_user_func($item['callable'], $parameter);
            } elseif (is_array($item['callable']) && method_exists($item['callable']['object'],
                            $item['callable']['method'])) {
                call_user_func(array($item['callable']['object'], $item['callable']['method']), $parameter);
            } else {
                ?>
                <div class="container fed_add_page_profile_container text-center">
                    <?php printf(__('OOPS! You have not add the callable function, please add %s() to show the
					body container', 'frontend-dashboard'), $item['callable']) ?>
                </div>
                <?php
            }
        }

        private function post_fields_layout($profiles)
        {
            ?>
            <div class="bc_fed container fed_post_container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="fed_UP_page_header">
                            <h3 class="fed_header_font_color"><?php _e('Post Fields', 'frontend-dashboard') ?></h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fed_UP_select_container">
                            <div class="fed_UP_input_select">
                                <a class="btn btn-primary"
                                   href="<?php echo menu_page_url('fed_add_user_profile', false).'&fed_action=post' ?>">
                                    <i class="fa fa-plus"></i>
                                    <?php _e('Add New Custom Post Field', 'frontend-dashboard') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (count($profiles) <= 0) {
                    ?>
                    <div class="row fed_alert_danger">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <button type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-hidden="true">&times;
                                </button>
                                <strong>
                                    <?php _e('Sorry! there are no custom post fields added!', 'frontend-dashboard') ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    usort($profiles, 'fed_sort_by_order');
                    $menu = fed_get_public_post_types();
                    ?>
                    <div class="row">
                        <div class="col-md-12 fed_admin_profile_container">
                            <div class="row">
                                <div class="col-md-3">
                                    <ul class="nav nav-pills nav-stacked p-t-10" role="tablist">
                                        <?php
                                        $profilesValue = fed_array_group_by_key($profiles, 'post_type');
                                        $groupBy       = fed_compare_two_arrays_get_second_value($menu, $profilesValue);
                                        $count         = 0;
                                        foreach ($groupBy as $index => $group) {
                                            $isActive = $count === 0 ? 'active' : '';
                                            ?>
                                            <li class="<?php echo $isActive; ?>">
                                                <a href="#<?php echo $index ?>" role="tab"
                                                   data-toggle="tab"><?php echo $menu[$index] ?></a>
                                            </li>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div class="col-md-7">
                                    <div class="tab-content">
                                        <?php
                                        $count = 0;
                                        foreach ($groupBy as $index => $group) {
                                            $isActive = $count === 0 ? 'active' : '';
                                            ?>
                                            <div class="<?php echo $isActive; ?> tab-pane fade in"
                                                 id="<?php echo $index; ?>">
                                                <div class="panel panel-primary">
                                                    <div class="panel-heading">
                                                        <h3 class="panel-title"><?php echo $menu[$index]; ?></h3>
                                                    </div>
                                                    <div class="panel-body">
                                                        <?php

                                                        foreach ($group as $profile) {
                                                            ?>
                                                            <form method="post"
                                                                  class="fed_user_profile_delete fed_profile_ajax"
                                                                  action="<?php echo admin_url('admin-ajax.php?action=fed_user_profile_delete') ?>">
                                                                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                                                <input type="hidden"
                                                                       name="post_id"
                                                                       value="<?php echo $profile['id'] ?>">

                                                                <input type="hidden"
                                                                       name="profile_name"
                                                                       value="<?php echo $profile['label_name'] ?>">

                                                                <div class="row fed_single_profile ">
                                                                    <div class="col-md-6">
                                                                        <label class="control-label">
                                                                            <?php
                                                                            echo $profile['label_name'].fed_is_required($profile['is_required']); ?>
                                                                        </label>
                                                                        <?php echo fed_get_input_details($profile) ?>
                                                                    </div>

                                                                    <div class="col-md-6 p-t-20">
                                                                        <a class="btn btn-primary"
                                                                           href="<?php echo menu_page_url('fed_add_user_profile',
                                                                                           false).'&fed_input_id='.$profile['id'].'&fed_action=post' ?>">
                                                                            <i class="fa fa-pencil"
                                                                               aria-hidden="true"></i>
                                                                        </a>
                                                                        <?php if ( ! fed_check_field_is_belongs_to_extra($profile['input_meta'])) { ?>
                                                                            <button class="btn btn-danger fed_profile_delete">
                                                                                <i class="fa fa-trash"
                                                                                   aria-hidden="true"></i>
                                                                            </button>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        <?php }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                            $count++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        private function show_admin_profile_page($profiles)
        {
            usort($profiles, 'fed_sort_by_order');
            $menu     = fed_get_key_value_array(fed_fetch_menu(), 'menu_slug');
            $menu_key = fed_get_key_value_array($menu, 'menu_slug', 'menu');
            ?>
            <div class="bc_fed container fed_tabs_container fed_UP_container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="fed_UP_page_header">
                            <h3 class="fed_header_font_color">
                                <?php _e('User Profile', 'frontend-dashboard') ?>
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="fed_UP_select_container">
                            <div class="fed_UP_input_select">
                                <a class="btn btn-primary"
                                   href="<?php echo menu_page_url('fed_add_user_profile',
                                                   false).'&fed_action=profile' ?>">
                                    <i class="fa fa-plus"></i>
                                    <?php _e('Add New Extra User Profile Field', 'frontend-dashboard') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 fed_admin_profile_container">
                        <div class="row">
                            <div class="col-md-3">
                                <ul class="nav nav-pills nav-stacked p-t-10" role="tablist">
                                    <?php
                                    $profilesValue = fed_array_group_by_key($profiles, 'menu');
                                    $groupBy       = fed_compare_two_arrays_get_second_value($menu_key, $profilesValue);
                                    $count         = 0;
                                    foreach ($groupBy as $index => $group) {
                                        $isActive = $count === 0 ? 'active' : '';
                                        ?>
                                        <li class="<?php echo $isActive; ?>">
                                            <a href="#<?php echo $index ?>" role="tab" data-toggle="tab">
                                                <?php echo '<span class="'.$menu[$index]['menu_image_id'].'"></span>' ?>
                                                <?php printf(__('%s', 'frontend-dashboard'), $menu[$index]['menu']
                                                ); ?>
                                            </a>
                                        </li>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                    <li class="t-m-20">
                                        <a href="<?php menu_page_url('fed_dashboard_menu'); ?>">
                                            <i class="fa fa-plus"></i>
                                            <?php _e('Add New Menu', 'frontend-dashboard') ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-7">
                                <div class="tab-content">
                                    <?php
                                    $count = 0;
                                    foreach ($groupBy as $index => $group) {
                                        $isActive = $count === 0 ? 'active' : '';
                                        ?>
                                        <div class="<?php echo $isActive; ?> tab-pane fade in"
                                             id="<?php echo $index; ?>">
                                            <div class="panel panel-primary">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title">
                                                        <?php echo '<span class="'.$menu[$index]['menu_image_id'].'"></span>' ?>
                                                        <?php printf(__('%s', 'frontend-dashboard'),
                                                                $menu[$index]['menu']); ?>
                                                    </h3>
                                                </div>
                                                <div class="panel-body">
                                                    <?php

                                                    foreach ($group as $profile) {
                                                        $register     = fed_profile_enable_disable($profile['show_register'],
                                                                'register');
                                                        $dashboard    = fed_profile_enable_disable($profile['show_dashboard'],
                                                                'dashboard');
                                                        $user_profile = fed_profile_enable_disable($profile['show_user_profile'],
                                                                'user_profile');
                                                        ?>
                                                        <form method="post"
                                                              class="fed_user_profile_delete fed_profile_ajax"
                                                              action="<?php echo admin_url('admin-ajax.php?action=fed_user_profile_delete') ?>">
                                                            <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                                            <input type="hidden"
                                                                   name="profile_id"
                                                                   value="<?php echo $profile['id'] ?>">

                                                            <input type="hidden"
                                                                   name="profile_name"
                                                                   value="<?php echo $profile['label_name'] ?>">

                                                            <div class="row fed_single_profile ">
                                                                <div class="col-md-6">
                                                                    <label class="control-label">
                                                                        <?php
                                                                        printf(__($profile['label_name'],
                                                                                'frontend-dashboard'),
                                                                                $profile['label_name']);
                                                                        echo fed_is_required($profile['is_required']); ?>
                                                                    </label>
                                                                    <?php echo fed_get_input_details($profile) ?>
                                                                </div>

                                                                <div class="col-md-6 p-t-20">
                                                                    <?php echo fed_show_help_message(array(
                                                                            'icon'    => 'fa fa-sign-in',
                                                                            'class'   => 'btn '.$register['button'],
                                                                            'title'   => sprintf(__('%s',
                                                                                    'frontend-dashboard'),
                                                                                    $register['title']),
                                                                            'content' => $register['content'],
                                                                    )) ?>

                                                                    <?php echo fed_show_help_message(array(
                                                                            'icon'    => 'fas fa-tachometer-alt',
                                                                            'class'   => 'btn '.$dashboard['button'],
                                                                            'title'   => sprintf(__('%s',
                                                                                    'frontend-dashboard'),
                                                                                    $dashboard['title']),
                                                                            'content' => $dashboard['content'],
                                                                    )) ?>

                                                                    <?php echo fed_show_help_message(array(
                                                                            'icon'    => 'fa fa-user',
                                                                            'class'   => 'btn '.$user_profile['button'],
                                                                            'title'   => sprintf(__('%s',
                                                                                    'frontend-dashboard'),
                                                                                    $user_profile['title']),
                                                                            'content' => $user_profile['content'],
                                                                    )) ?>

                                                                    <span class="p-l-40">
													<a class="btn btn-primary"
                                                       href="<?php echo menu_page_url('fed_add_user_profile',
                                                                       false).'&fed_input_id='.$profile['id'].'&fed_action=profile' ?>">
														<i class="fa fa-pencil" aria-hidden="true"></i>
													</a>
                                                                        <?php if ( ! fed_check_field_is_belongs_to_extra($profile['input_meta'])) { ?>
                                                                            <button class="btn btn-danger fed_profile_delete">
															<i class="fa fa-trash" aria-hidden="true"></i>
														</button>
                                                                        <?php } ?>
													</span>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    <?php }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }


        /**
         * @return array
         */
        protected function fed_get_main_sub_menu()
        {
            $menu = array(
                    'fed_dashboard_menu'   => array(
                            'page_title' => __('Dashboard Menu', 'frontend-dashboard'),
                            'menu_title' => __('Dashboard Menu', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'dashboard_menu'),
                            'position'   => 7,
                    ),
                    'fed_user_profile'     => array(
                            'page_title' => __('User Profile', 'frontend-dashboard'),
                            'menu_title' => __('User Profile', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'user_profile'),
                            'position'   => 20,
                    ),
                    'fed_post_fields'      => array(
                            'page_title' => __('Post Fields', 'frontend-dashboard'),
                            'menu_title' => __('Post Fields', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'post_fields'),
                            'position'   => 25,
                    ),
                    'fed_add_user_profile' => array(
                            'page_title' => __('Add Profile / Post Fields', 'frontend-dashboard'),
                            'menu_title' => __('Add Profile / Post Fields', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'add_user_profile'),
                            'position'   => 30,

                    ),
                    'fed_plugin_pages'     => array(
                            'page_title' => __('Add-Ons', 'frontend-dashboard'),
                            'menu_title' => __('Add-Ons', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'plugin_pages'),
                            'position'   => 50,
                    ),
                    'fed_status'           => array(
                            'page_title' => __('Status', 'frontend-dashboard'),
                            'menu_title' => __('Status', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'status'),
                            'position'   => 70,
                    ),
                    'fed_help'             => array(
                            'page_title' => __('Help', 'frontend-dashboard'),
                            'menu_title' => __('Help', 'frontend-dashboard'),
                            'capability' => 'manage_options',
                            'callback'   => array($this, 'help'),
                            'position'   => 100,
                    ),
            );

            $main_menu = apply_filters('fed_add_main_sub_menu', $menu);

            return fed_array_sort($main_menu, 'position');
        }

    }

    new FED_AdminMenu();
}