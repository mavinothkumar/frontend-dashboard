<?php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! class_exists('FED_Routes')) {
    /**
     * Class FED_Routes
     */
    class FED_Routes
    {

        public $request;

        /**
         * FED_Routes constructor.
         *
         * @param $request
         * @param $error
         */
        public function __construct($request)
        {
            $this->request = $request;
        }

        /**
         * @param $menu
         */
        public function getDashboardContent($menu)
        {
            if ($menu['menu_request']['menu_type'] === 'user') {
                fed_display_dashboard_profile($menu['menu_request']);
            }
            if ($menu['menu_request']['menu_type'] === 'logout') {
                fed_logout_process();
            }

            do_action('fed_frontend_dashboard_menu_container', $this->request, $menu);
        }

        /**
         * @return array|bool|\WP_Error
         */
        public function setDashboardMenuQuery()
        {
            $menu              = fed_get_dashboard_menu_items_sort_data();
            $first_element_key = array_keys($menu);
            $first_element     = $first_element_key[0];

            if (count(array_diff($this->getDefaultMenuQuery(), array_keys($this->request))) !== 0) {
                $menu_items = array(
                        'menu_request' => array(
                                'menu_type' => $menu[$first_element]['menu_type'],
                                'menu_slug' => $menu[$first_element]['menu_slug'],
                                'menu_id'   => $menu[$first_element]['id'],
                                'fed_nonce' => wp_create_nonce('fed_nonce'),
                        ),
                );
            } else {
                $menu_items = array(
                        'menu_request' => array(
                                'menu_type' => $this->request['menu_type'],
                                'menu_slug' => $this->request['menu_slug'],
                                'menu_id'   => $this->request['menu_id'],
                                'fed_nonce' => wp_create_nonce('fed_nonce'),
                        ),
                );
            }

            $menu_items['menu_items'] = $menu;

            /**
             * Check for Nonce
             */
            if ($this->validateNonce($menu_items['menu_request']) instanceof WP_Error) {
                return $this->validateNonce($menu_items['menu_request']);
            }

            /**
             * Check the Menu type is valid
             */
            if ( ! in_array($menu_items['menu_request']['menu_type'], $this->getDefaultMenuType(), true)) {
                return new WP_Error('invalid_menu_type', 'Invalid Menu Type in URL 1');
            }
            /**
             * Check is the menu is allowed for this user
             */

            if (isset($this->request['menu_slug']) && ! in_array($this->request['menu_slug'],
                            fed_get_keys_from_menu($menu))) {
                return new WP_Error('invalid_menu_type', 'Invalid Menu Type in URL 2');
            }

            set_query_var('fed_menu_items', $menu_items);

            return $menu_items;
        }

        /**
         * @return mixed|void
         */
        public function getDefaultMenuQuery()
        {
            return apply_filters('fed_get_default_menu_query', array('menu_type', 'menu_slug', 'fed_nonce'));
        }

        /**
         * @return mixed|void
         */
        public function getDefaultMenuType()
        {
            return fed_get_default_menu_type();
        }

        /**
         * @param $request
         *
         * @return bool|\WP_Error
         */
        private function validateNonce($request)
        {
            if ( ! isset($request['fed_nonce'])) {
                return new WP_Error('invalid_request', 'Invalid Request');
            }
            if ( ! wp_verify_nonce($request['fed_nonce'], 'fed_nonce')) {
                return new WP_Error('invalid_request', 'Invalid Request');
            }

            return true;
        }
    }
}