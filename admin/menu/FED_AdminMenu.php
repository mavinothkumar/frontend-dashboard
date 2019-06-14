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
                        $menu['page_title'],
                        $menu['menu_title'],
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
                                        <?php echo $item['name'] ?>
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
            fed_get_dashboard_menu_items();
        }

        public function user_profile()
        {
            fed_get_user_profile_menu();
        }

        public function post_fields()
        {
            fed_get_post_fields_menu();
        }

        public function add_user_profile()
        {
            fed_get_add_profile_post_fields();
        }

        public function plugin_pages()
        {
            fed_get_plugin_pages_menu();
        }

        public function help()
        {
            fed_get_help_menu();
        }

        public function status()
        {
            fed_get_status_menu();
        }

        /**
         * @return mixed|void
         */
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
                    'general'             => array(
                            'icon_class' => 'fas fa-tachometer-alt',
                            'name'       => __('Common', 'frontend-dashboard'),
                            'callable'   => array(
                                    'object' => new FED_Admin_General(),
                                    'method' => 'fed_admin_general_tab',
                            ),
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

        /**
         * @param $item
         */
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
                    <?php _e('OOPS! You have not add the callable function, please add', 'frontend-dashboard');
                    echo $item['callable'];
                    _e('to show the body container', 'frontend-dashboard') ?>
                </div>
                <?php
            }
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