<?php
/**
 * Enqueue Script at Admin and Front End
 */

add_action('admin_enqueue_scripts', 'fed_script_admin');
add_action('wp_enqueue_scripts', 'fed_script_front_end', 99);
/**
 * Admin Scripts.
 *
 * @param $hook
 */
function fed_script_admin($hook)
{

    if (($hook === 'post.php' || $hook === 'user-edit.php' || $hook === 'post-new.php' || $hook === 'profile.php') || (isset($_GET['page']) && in_array($_GET['page'],
                            fed_get_script_loading_pages(), false))) {

        $db_scripts      = get_option('fed_general_scripts_styles', array());
        $default_scripts = new FED_Admin_General();

        foreach ($default_scripts->default_admin_script() as $index => $scripts) {
            foreach ($scripts as $key => $script) {
                if ( ! isset($db_scripts['admin'][$index][$key])) {
                    if ($index === 'scripts') {
                        if ($script['wp_core'] === true) {
                            wp_enqueue_script($key);
                        } else {
                            wp_enqueue_script($key, $script['src'], $script['dependencies'], $script['version'],
                                    $script['in_footer']);
                        }
                    }
                    if ($index === 'styles') {
                        if ($script['wp_core'] === true) {
                            wp_enqueue_style($key);
                        } else {
                            wp_enqueue_style($key, $script['src'], $script['dependencies'], $script['version'],
                                    $script['media']);
                        }
                    }
                }
            }
        }

        do_action('fed_enqueue_script_style_admin');

        wp_localize_script('fed_admin_script', 'frontend_dashboard', fed_js_translation());

        wp_enqueue_media();
    }
}

/**
 * Frontend Script.
 */
function fed_script_front_end()
{
    if (fed_is_shortcode_in_content()) {
        $db_scripts      = get_option('fed_general_scripts_styles', array());
        $default_scripts = new FED_Admin_General();

        foreach ($default_scripts->default_frontend_script() as $index => $scripts) {
            foreach ($scripts as $key => $script) {
                if ( ! isset($db_scripts['frontend'][$index][$key])) {
                    if ($index === 'scripts') {
                        if ($script['wp_core'] === true) {
                            wp_enqueue_script($key);
                        } else {
                            wp_enqueue_script($key, $script['src'], $script['dependencies'], $script['version'],
                                    $script['in_footer']);
                        }
                    }
                    if ($index === 'styles') {
                        if ($script['wp_core'] === true) {
                            wp_enqueue_style($key);
                        } else {
                            wp_enqueue_style($key, $script['src'], $script['dependencies'], $script['version'],
                                    $script['media']);
                        }
                    }
                }
            }
        }

        do_action('fed_enqueue_script_style_frontend');

        // Pass PHP value to JavaScript
        $translation_array = apply_filters('fed_convert_php_js_var', fed_js_translation());


        wp_localize_script('fed_script', 'frontend_dashboard', $translation_array);

        wp_enqueue_media();
    }
}