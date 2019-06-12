<?php
/**
 * Delete and empty the Database Tables and Options
 */


add_action('wp_ajax_fed_status_delete_table', 'fed_status_delete_table');
add_action('wp_ajax_fed_status_empty_table', 'fed_status_empty_table');
add_action('wp_ajax_fed_status_delete_option', 'fed_status_delete_option');
add_action('wp_ajax_fed_status_delete_all_option', 'fed_status_delete_all_option');
add_action('wp_ajax_nopriv_fed_status_delete_table', 'fed_block_the_action');
add_action('wp_ajax_nopriv_fed_status_empty_table', 'fed_block_the_action');
add_action('wp_ajax_nopriv_fed_status_delete_option', 'fed_block_the_action');
add_action('wp_ajax_nopriv_fed_status_delete_all_option', 'fed_block_the_action');

/**
 * Delete Table
 */
function fed_status_delete_table()
{
    if (fed_is_admin()) {
        $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        fed_verify_nonce($request);

        if (isset($request['table_name']) && ! empty($request['table_name'])) {
            global $wpdb;
            $table_name = fed_sanitize_text_field($request['table_name']);
            $status     = $wpdb->query("DROP TABLE IF EXISTS {$table_name}");

            if ($status) {
                wp_send_json_success(
                        [
                                'message' => 'Successfully Deleted',
                                'reload'  => admin_url('admin.php?page=fed_status'),
                        ]
                );
            }
            wp_send_json_error(['message' => 'Something went wrong in deleting the table, please refresh the page and try']);
        }
    }

    wp_send_json_error(['message' => 'You are not admin to do this action']);
}

function fed_status_delete_option()
{
    if (fed_is_admin()) {
        $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        fed_verify_nonce($request);

        if (isset($request['option_id']) && ! empty($request['option_id'])) {
            global $wpdb;
            $option_id = (int) $request['option_id'];
            $status    = $wpdb->delete($wpdb->options, array('option_id' => $option_id), array('%d'));

            if ($status) {
                wp_send_json_success(
                        [
                                'message' => 'Successfully Deleted',
                                'reload'  => admin_url('admin.php?page=fed_status'),
                        ]
                );
            }
            wp_send_json_error(['message' => 'Something went wrong in deleting the table, please refresh the page and try']);
        }
    }

    wp_send_json_error(['message' => 'You are not admin to do this action']);
}

function fed_status_delete_all_option()
{
    if (fed_is_admin()) {
        $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        fed_verify_nonce($request);

        global $wpdb;
        $status = $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'fed%'");

        if ($status) {
            wp_send_json_success(
                    [
                            'message' => 'Successfully Deleted',
                            'reload'  => admin_url('admin.php?page=fed_status'),
                    ]
            );
        }
        wp_send_json_error(['message' => 'Something went wrong in deleting the table, please refresh the page and try']);
    }

    wp_send_json_error(['message' => 'You are not admin to do this action']);
}

function fed_status_empty_table()
{
    if (fed_is_admin()) {
        $request = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        fed_verify_nonce($request);

        if (isset($request['table_name']) && ! empty($request['table_name'])) {
            global $wpdb;
            $table_name = fed_sanitize_text_field($request['table_name']);
            $status     = false;
            if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name) {
                $status = $wpdb->query("TRUNCATE TABLE {$table_name}");
            }


            if ($status) {
                wp_send_json_success(
                        [
                                'message' => 'Successfully Deleted',
                                'reload'  => admin_url('admin.php?page=fed_status'),
                        ]
                );
            }
            wp_send_json_error(['message' => 'Something went wrong in deleting the table, please refresh the page and try']);
        }
    }

    wp_send_json_error(['message' => 'You are not admin to do this action']);
}