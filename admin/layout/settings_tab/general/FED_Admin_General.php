<?php

/**
 * Class FED_Admin_General
 */

if ( ! class_exists('FED_Admin_General')) {
    class FED_Admin_General
    {

        public function __construct()
        {
            add_action('wp_ajax_fed_admin_script_menu', array($this, 'save_admin_script'));
        }

        public function fed_admin_general_tab()
        {
            $fed_general = get_option('fed_admin_general');
            $tabs        = $this->fed_get_admin_general_options($fed_general);
            ?>
            <div class="row">
                <div class="col-md-3 padd_top_20">
                    <ul class="nav nav-pills nav-stacked"
                        id="fed_admin_setting_login_tabs"
                        role="tablist">
                        <?php
                        $menu_count = 0;
                        foreach ($tabs as $index => $tab) {
                            $active = $menu_count === 0 ? 'active' : '';
                            $menu_count++;
                            ?>
                            <li role="presentation"
                                class="<?php echo $active; ?>">
                                <a href="#<?php echo $index; ?>"
                                   aria-controls="<?php echo $index; ?>"
                                   role="tab"
                                   data-toggle="tab">
                                    <i class="<?php echo $tab['icon']; ?>"></i>
                                    <?php echo $tab['name']; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-md-9">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <?php
                        $content_count = 0;
                        foreach ($tabs as $index => $tab) {
                            $active = $content_count === 0 ? 'active' : '';
                            $content_count++;
                            ?>
                            <div role="tabpanel"
                                 class="tab-pane <?php echo $active; ?>"
                                 id="<?php echo $index; ?>">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <span class="<?php echo $tab['icon']; ?>"></span>
                                            <?php echo $tab['name']; ?>
                                        </h3>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        fed_call_function_method($tab)
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }

        /**
         * @param $options
         *
         * @return mixed|void
         */
        public function fed_get_admin_general_options($options)
        {
            return apply_filters('fed_customize_admin_general_options', array(
                    'fed_admin_scripts'    => array(
                            'icon'      => 'fas fa-code',
                            'name'      => __('Admin Scripts', 'frontend-dashboard-extra'),
                            'callable'  => array('object' => $this, 'method' => 'fed_admin_script_menu_tab'),
                            'arguments' => $options,
                    ),
                    'fed_frontend_scripts' => array(
                            'icon'      => 'fas fa-code',
                            'name'      => __('Frontend Scripts', 'frontend-dashboard-extra'),
                            'callable'  => array('object' => $this, 'method' => 'fed_frontend_script_menu_tab'),
                            'arguments' => $options,
                    ),
            ));
        }

        /**
         * Save Admin Script Menu
         */
        public function save_admin_script()
        {
            $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            fed_verify_nonce($request);
            $db_value = get_option('fed_general_scripts_styles', array());
            $type     = 'admin';
            $default  = $this->default_admin_script();
            if (isset($request['fed_admin_script_type'])) {
                $type    = 'frontend';
                $default = $this->default_frontend_script();
            }
            $admin_script = array();

            foreach ($default as $index => $script) {
                foreach ($script as $key => $value) {
                    if (isset($request[$type][$index][$key])) {
                        $admin_script[$index][$key] = $key;
                    }
                }
            }

            $db_value[$type] = $admin_script;

            update_option('fed_general_scripts_styles', $db_value);

            wp_send_json_success(array('message' => 'Successfully updated'));


        }

        /**
         * @return mixed|void
         */
        public function default_admin_script()
        {
            $scripts = apply_filters('fed_default_admin_scripts_styles', array(
                    'scripts' => array(
                            'jquery'                => array(
                                    'wp_core'     => true,
                                    'name'        => 'JQuery',
                                    'plugin_name' => 'Frontend Dashboard',
                            ),
                            'jquery-ui-core'        => array(
                                    'wp_core'     => true,
                                    'name'        => 'JQuery UI Core',
                                    'plugin_name' => 'Frontend Dashboard',
                            ),
                            'jquery-ui-sortable'    => array(
                                    'wp_core'     => true,
                                    'name'        => 'JQuery UI Sortable',
                                    'plugin_name' => 'Frontend Dashboard',
                            ),
                            'fed_sweetalert'        => array(
                                    'wp_core'      => false,
                                    'name'         => 'SweetAlert',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/sweetalert2.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_fontawesome'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/fontawesome.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_fontawesome-shims' => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome Shims',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/fontawesome-shims.js',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_admin_script'      => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Admin Script',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/admin/assets/fed_admin_script.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_bootstrap_script'  => array(
                                    'wp_core'      => false,
                                    'name'         => 'Bootstrap',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/bootstrap.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_jscolor_script'    => array(
                                    'wp_core'      => false,
                                    'name'         => 'JSColor',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/jscolor.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_select2_script'    => array(
                                    'wp_core'      => false,
                                    'name'         => 'Select2',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/select2.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_flatpickr'         => array(
                                    'wp_core'      => false,
                                    'name'         => 'FlatPickr',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/flatpickr.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                    ),
                    'styles'  => array(
                            'fed_admin_bootstrap'          => array(
                                    'wp_core'      => false,
                                    'name'         => 'Bootstrap',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/bootstrap.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_frontend_sweetalert'      => array(
                                    'wp_core'      => false,
                                    'name'         => 'SweetAlert',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/sweetalert2.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_admin_font_awesome'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/fontawesome.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_admin_font_awesome-shims' => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesomeShims',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/fontawesome-shims.css',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_flatpikcr'                => array(
                                    'wp_core'      => false,
                                    'name'         => 'FlatPikcr',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/flatpickr.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_select2'                  => array(
                                    'wp_core'      => false,
                                    'name'         => 'Select 2',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/select2.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_admin_style'              => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Admin',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('admin/assets/fed_admin_style.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_global_admin_style'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Global',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('admin/assets/fed_global_admin_style.css',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                    ),
            ));
            return $scripts;
        }

        public function fed_admin_script_menu_tab()
        {
            $scripts = $this->admin_scripts_styles();
            $default = $this->default_admin_script();
            ?>
            <form method="post" class="fed_admin_menu fed_ajax"
                  action="<?php echo fed_get_form_action('fed_admin_script_menu') ?>">
                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>

                <?php echo fed_loader(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Note:', 'frontend-dashboard') ?></strong>
                            <?php _e('Select the respective scripts or styles to dequeue from the page',
                                    'frontend-dashboard'); ?>
                        </div>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Caution:', 'frontend-dashboard') ?></strong>
                            <?php _e('If you are not aware, please don\'t select any options, that may crash the Admin Section',
                                    'frontend-dashboard'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Scripts
                            </div>
                            <div class="panel-body">
                                <?php
                                foreach ($default['scripts'] as $index => $script) {
                                    $user_value = in_array($index,
                                            isset($scripts['scripts']) ? $scripts['scripts'] : array()) ? 'yes' : 'no';
                                    ?>
                                    <div class="m-b-10">
                                        <?php
                                        echo fed_get_input_details([
                                                'input_type' => 'checkbox',
                                                'input_meta' => 'admin[scripts]['.$index.']',
                                                'user_value' => $user_value,
                                                'label'      => $script['name'].'('.$script['plugin_name'].')',
                                        ]);
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Styles
                            </div>
                            <div class="panel-body">
                                <?php
                                foreach ($default['styles'] as $index => $script) {
                                    $user_value = in_array($index,
                                            isset($scripts['styles']) ? $scripts['styles'] : array()) ? 'yes' : 'no';
                                    ?>
                                    <div class="m-b-10">
                                        <?php
                                        echo fed_get_input_details([
                                                'input_type' => 'checkbox', 'input_meta' => 'admin[styles]['.$index.']',
                                                'user_value' => $user_value,
                                                'label'      => $script['name'].'('.$script['plugin_name'].')',
                                        ]);
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 m-t-10">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> Save</button>
                    </div>
                </div>
            </form>
            <?php
        }

        /**
         * @param  string  $type
         *
         * @return mixed|void
         */
        public function admin_scripts_styles($type = 'admin')
        {
            $scripts = get_option('fed_general_scripts_styles');

            return isset($scripts[$type]) ? $scripts[$type] : array();

        }

        /**
         * @return mixed|void
         */
        public function default_frontend_script()
        {
            return apply_filters('fed_default_frontend_scripts_styles', array(
                    'scripts' => array(
                            'fed_sweetalert'        => array(
                                    'wp_core'      => false,
                                    'name'         => 'SweetAlert',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/sweetalert2.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_fontawesome'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/fontawesome.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_fontawesome-shims' => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome Shims',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/fontawesome-shims.js',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_script'            => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Frontend Script',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/fed_script.js', BC_FED_PLUGIN),
                                    'dependencies' => array('jquery'),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_bootstrap_script'  => array(
                                    'wp_core'      => false,
                                    'name'         => 'Bootstrap',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/bootstrap.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_jscolor_script'    => array(
                                    'wp_core'      => false,
                                    'name'         => 'JSColor',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/jscolor.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_select2_script'    => array(
                                    'wp_core'      => false,
                                    'name'         => 'Select2',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/select2.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                            'fed_flatpickr'         => array(
                                    'wp_core'      => false,
                                    'name'         => 'FlatPickr',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/js/flatpickr.js', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => false,
                                    'in_footer'    => false,
                            ),
                    ),
                    'styles'  => array(
                            'fed_admin_bootstrap'          => array(
                                    'wp_core'      => false,
                                    'name'         => 'Bootstrap',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/bootstrap.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_frontend_sweetalert'      => array(
                                    'wp_core'      => false,
                                    'name'         => 'SweetAlert',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/sweetalert2.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_admin_font_awesome'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesome',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/fontawesome.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_admin_font_awesome-shims' => array(
                                    'wp_core'      => false,
                                    'name'         => 'FontAwesomeShims',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/fontawesome-shims.css',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_flatpikcer'               => array(
                                    'wp_core'      => false,
                                    'name'         => 'FlatPikcr',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/flatpickr.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_select2'                  => array(
                                    'wp_core'      => false,
                                    'name'         => 'Select 2',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/select2.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_frontend_style'           => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Frontend Style',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/common-style.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_global_admin_style'       => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Global',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('admin/assets/fed_global_admin_style.css',
                                            BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                            'fed_frontend_animate'         => array(
                                    'wp_core'      => false,
                                    'name'         => 'FED Animate',
                                    'plugin_name'  => 'Frontend Dashboard',
                                    'src'          => plugins_url('/common/assets/css/animate.css', BC_FED_PLUGIN),
                                    'dependencies' => array(),
                                    'version'      => BC_FED_PLUGIN_VERSION,
                                    'media'        => 'all',
                            ),
                    ),
            ));
        }

        public function fed_frontend_script_menu_tab()
        {
            $scripts = $this->admin_scripts_styles('frontend');
            $default = $this->default_frontend_script();
            ?>
            <form method="post" class="fed_admin_menu fed_ajax"
                  action="<?php echo fed_get_form_action('fed_admin_script_menu') ?>">
                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                <input type="hidden" name="fed_admin_script_type" value="frontend"/>

                <?php echo fed_loader(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Note:', 'frontend-dashboard') ?></strong>
                            <?php _e('Select the respective scripts or styles to dequeue from the page',
                                    'frontend-dashboard'); ?>
                        </div>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong><?php _e('Caution:', 'frontend-dashboard') ?></strong>
                            <?php _e('If you are not aware, please don\'t select any options, that may crash the Frontend Dashboard',
                                    'frontend-dashboard'); ?>
                        </div>

                    </div>
                    <div class="col-md-6">

                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Scripts
                            </div>
                            <div class="panel-body">
                                <?php
                                foreach ($default['scripts'] as $index => $script) {
                                    $user_value = in_array($index,
                                            isset($scripts['scripts']) ? $scripts['scripts'] : array()) ? 'yes' : 'no';
                                    ?>
                                    <div class="m-b-10">
                                        <?php
                                        echo fed_get_input_details([
                                                'input_type' => 'checkbox',
                                                'input_meta' => 'frontend[scripts]['.$index.']',
                                                'user_value' => $user_value,
                                                'label'      => $script['name'].'('.$script['plugin_name'].')',
                                        ]);
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Styles
                            </div>
                            <div class="panel-body">
                                <?php
                                foreach ($default['styles'] as $index => $script) {
                                    $user_value = in_array($index,
                                            isset($scripts['styles']) ? $scripts['styles'] : array()) ? 'yes' : 'no';
                                    ?>
                                    <div class="m-b-10">
                                        <?php
                                        echo fed_get_input_details([
                                                'input_type' => 'checkbox',
                                                'input_meta' => 'frontend[styles]['.$index.']',
                                                'user_value' => $user_value,
                                                'label'      => $script['name'].'('.$script['plugin_name'].')',
                                        ]);
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 m-t-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-floppy-o"></i>
                            Save</button>
                    </div>
                </div>
            </form>
            <?php
        }

    }

    new FED_Admin_General();
}