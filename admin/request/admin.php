<?php
if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Get all admin page setting request
 */

add_action('wp_ajax_fed_admin_setting_form', 'fed_admin_setting_form_function');
add_action('wp_ajax_fed_admin_setting_up_form', 'fed_admin_setting_up_form_function');
add_action('wp_ajax_fed_admin_setting_form_dashboard_menu', 'fed_admin_setting_form_dashboard_menu_function');
add_action('wp_ajax_fed_user_profile_delete', 'fed_user_profile_delete_function');
add_action('wp_ajax_fed_message_form', 'fed_message_form_function');
add_action('wp_ajax_fed_is_registered', 'fed_is_registered');

/**
 * Admin Setting Page
 */
function fed_admin_setting_form_function()
{

    $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    /**
     * Check for Nonce
     */
    fed_verify_nonce();

    /**
     * Process the Admin page request.
     */
    if (isset($request['fed_admin_unique']) && 'fed_login_details' == $request['fed_admin_unique']) {
        fed_admin_setting_login_request();
        exit();
    }

    /**
     * Process Admin User Profile Layout Page
     */
    if (isset($request['fed_admin_unique']) && 'fed_admin_setting_upl' == $request['fed_admin_unique']) {
        fed_admin_setting_upl_request();
        exit();
    }

    if (isset($request['fed_admin_unique']) && 'fed_admin_setting_upl_color' == $request['fed_admin_unique']) {
        fed_admin_setting_upl_color_request();
        exit();
    }

    /**
     * Process Post Options
     */
    if (isset($request['fed_admin_unique']) && 'fed_admin_settings_post' == $request['fed_admin_unique']) {
        fed_admin_setting_post_options_request();
        exit();
    }
    /**
     * Process Payment Options
     */

//	if ( isset( $request['fed_admin_unique'] ) && 'fed_payment_options' == $request['fed_admin_unique'] ) {
//		fed_admin_payment_options_request();
//		exit();
//	}
    /**
     * Process User Options
     */
    if (isset($request['fed_admin_unique']) && 'fed_admin_setting_user' == $request['fed_admin_unique']) {
        fed_admin_user_options_request();
        exit();
    }

//	/**
//	 * Process Invoice Options
//	 */
//	if ( isset( $request['fed_admin_unique'] ) && 'fed_invoice_details' == $request['fed_admin_unique'] ) {
//		fed_admin_invoice_options_request();
//		exit();
//	}
//	/**
//	 * Process Support Options
//	 */
//	if ( isset( $request['fed_admin_unique'] ) && 'fed_admin_settings_support' == $request['fed_admin_unique'] ) {
//		fed_admin_support_options_request();
//		exit();
//	}

    /**
     * 3rd Party template redirect handle
     */

    do_action('fed_admin_settings_login_action', $request);
}

/**
 * Admin User Profile Page
 */
function fed_admin_setting_up_form_function()
{
    //$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
    $post = $_REQUEST;

    if ( ! isset($post['fed_action'])) {
        wp_send_json_error(array('message' => 'You are trying some naughty actions, this action has been notified to Admin'));
        exit();
    }
    /**
     * Check for Nonce
     */
    fed_verify_nonce($post);


    if ( ! isset($post['label_name']) || empty($post['label_name'])
            || ! isset($post['input_order']) || empty($post['input_order'])
            || ! isset($post['input_meta']) || empty($post['input_meta'])
            || ! isset($post['input_type'])
            || ! isset($post['input_id'])
    ) {

        wp_send_json_error(array('message' => 'Please fill required fields'));
        exit();

    }

    /**
     * Check for default post value as input meta
     */
    if ('post' === $post['fed_action'] && $post['input_id'] === '' && in_array($post['input_meta'],
                    fed_get_default_post_items(), false)) {
        wp_send_json_error(array('message' => 'Sorry! you cannot add the default post value "'.$post['label_name'].'"'));
    }

    /**
     * Check for default user profile value as input meta
     */
    if ('profile' === $post['fed_action'] && '' === $post['input_id'] && in_array($post['input_meta'],
                    fed_get_default_profile_items(), false)) {
        wp_send_json_error(array('message' => 'Sorry! you cannot add the default profile value "'.$post['label_name'].'"'));
    }

    $values = fed_process_user_profile($post, $post['fed_action'], 'yes');

    $post_id = isset($post['input_id']) && ! empty($post['input_id']) ? (int) $post['input_id'] : '';

    fed_save_profile_post($values, $post['fed_action'], $post_id);
}

/**
 * Edit Dashboard menu
 */
function fed_admin_setting_form_dashboard_menu_function()
{
    $post_all = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    parse_str($post_all['data'], $post);
    $action  = $post_all['fed_action'];
    $post_id = (isset($post['menu_id']) && ! empty($post['menu_id'])) ? (int) $post['menu_id'] : '';
    /**
     * Check for Nonce
     */
    fed_verify_nonce($post);

    if ('save' === $action) {
        if (empty($post['fed_menu_name'])
                || empty($post['fed_menu_slug'])
                || empty($post['fed_menu_order']) ||
                ! isset($post['fed_menu_name'], $post['fed_menu_slug'], $post['fed_menu_order'])
        ) {

            wp_send_json_error(array('message' => 'Please fill required fields'));
            exit();
        }

        $values = fed_process_menu($post);

        fed_admin_menu_save($values, $post_id);
    }

    if ('delete' === $action) {
        if ('' == $post_id) {
            wp_send_json_error(array('message' => 'Sorry! please reload the page and try again'));
            exit();
        }

        $is_ID = fed_fetch_table_row_by_id(BC_FED_TABLE_MENU, $post_id);

        if ($is_ID instanceof WP_Error) {
            wp_send_json_error(array('message' => $is_ID->get_error_message('fed_no_row_found_on_that_id')));
        }

        /**
         * Check deleting item is parent item in Sub Menu
         */

        $check_menu = fed_get_dashboard_menu_items_sort_data();

        if (isset($check_menu['user_'.$is_ID['id']]) && isset($check_menu['user_'.$is_ID['id']]['submenu'])) {
            wp_send_json_error(array('message' => __('You are trying to delete a menu, which has Sub Menu(s), Please delete or move it to different Menu')));
        }

        fed_delete_table_rows_on_condition(BC_FED_TABLE_USER_PROFILE, 'menu', $is_ID['menu_slug']);
        $is_delete            = fed_delete_table_row_by_id(BC_FED_TABLE_MENU, $post_id);

        if ('success' === $is_delete) {
            wp_send_json_success(array(
                    'message' => $post['fed_menu_name'].' has been successfully deleted',
                    'reload'  => admin_url().'admin.php?page=fed_dashboard_menu',
            ));
        }

        wp_send_json_success(array(
                'message' => 'Something went wrong, please try again or report us',
        ));
    }


}

/**
 * Admin User Profile Layout Page
 */

function fed_admin_setting_upl_form_function()
{
    /**
     * Check for Nonce
     */
    if ( ! wp_verify_nonce($_REQUEST['fed_admin_setting_upl_nonce'], 'fed_admin_setting_upl_nonce')) {
        wp_send_json_error(array('message' => 'Invalid Request'));
        exit();
    }


}

/**
 * Delete User Profile
 */
function fed_user_profile_delete_function()
{
    $post_all = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    parse_str($post_all['data'], $post);
    $action = $post_all['fed_up_action'];

    /**
     * Check for Nonce
     */
    fed_verify_nonce($post);

    if ('delete' === $action) {
        $check_up_id = $is_delete = $reload = '';
        if (isset($post['profile_id'])) {
            $check_up_id = fed_fetch_table_row_by_id(BC_FED_TABLE_USER_PROFILE, $post['profile_id']);
        }

        if (isset($post['post_id'])) {
            $check_up_id = fed_fetch_table_row_by_id(BC_FED_TABLE_POST, $post['post_id']);
        }

        if ($check_up_id instanceof WP_Error) {
            wp_send_json_error(array('message' => $check_up_id->get_error_message('fed_no_row_found_on_that_id')));
            exit();
        }

        if (isset($post['profile_id'])) {
            $is_delete = fed_delete_table_row_by_id(BC_FED_TABLE_USER_PROFILE, $post['profile_id']);
            $reload    = admin_url().'admin.php?page=fed_user_profile';
        }

        if (isset($post['post_id'])) {
            $is_delete = fed_delete_table_row_by_id(BC_FED_TABLE_POST, $post['post_id']);
            $reload    = admin_url().'admin.php?page=fed_post_fields';
        }


        if ('success' === $is_delete) {
            wp_send_json_success(array(
                    'message' => $post['profile_name'].' has been successfully deleted',
                    'reload'  => $reload,
            ));
            exit();
        }
    }
    wp_send_json_error(array('message' => 'Invalid Request'));
    exit();
}

/**
 * Dismiss admin notice permanently
 */
function fed_message_form_function()
{
    /**
     * Check for Nonce
     */
    if ( ! wp_verify_nonce($_REQUEST['fed_message_nonce'], 'fed_message_nonce')) {
        wp_send_json_error(array('message' => 'Invalid Request'));
        exit();
    }
    update_option('fed_admin_message_notification', 'remove');
    wp_send_json_success(array(
            'message' => 'Successfully updated',
    ));
    exit();
}