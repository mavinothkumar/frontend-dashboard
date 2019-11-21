<?php

if ( ! class_exists('FEDInstallAddons')) {
    /**
     * Class FEDInstallAddons
     */
    class FEDInstallAddons
    {
        public function __construct()
        {
            add_action('activated_plugin', array($this, 'activated_plugin'), 10, 2);
        }

        public function install()
        {
            wp_ajax_install_plugin();
        }

        /**
         * @param $request
         *
         * @return bool
         */
        public function activate($request)
        {
            $plugin_name = fed_get_data('plugin_name', $request, false);
            $location    = $_SERVER['HTTP_REFERER'];
            if ($plugin_name) {
                $status = activate_plugin($plugin_name);
                if ($status instanceof WP_Error) {
                    fed_set_alert('fed_activation_message',
                        __('OOPs! Something went wrong while activating the plugin', 'frontend-dashboard'));
                    wp_safe_redirect($location);
                }
                fed_set_alert('fed_activation_message',
                    __('Plugin activated successfully', 'frontend-dashboard'));
                wp_safe_redirect($location);
            }
        }

        /**
         * @param $plugin
         * @param $network_wide
         */
        public function activated_plugin($plugin, $network_wide)
        {
            $page = isset($_GET, $_GET['fed_plugin_custom_activate']) && $_GET['fed_plugin_custom_activate'] === 'on' ? true : false;
            if ($page) {
                wp_redirect(fed_menu_page_url('fed_plugin_pages'));
                exit();
            }
        }
    }

    new FEDInstallAddons();
}