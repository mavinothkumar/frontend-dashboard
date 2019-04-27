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

        wp_enqueue_script('jquery');

        wp_enqueue_script('fed_sweetalert', plugins_url('/common/assets/js/sweetalert2.js', BC_FED_PLUGIN), array());

        wp_enqueue_script('fed_fontawesome', plugins_url('/common/assets/js/fontawesome.js', BC_FED_PLUGIN), array());

        wp_enqueue_script('fed_fontawesome-shims', plugins_url('/common/assets/js/fontawesome-shims.js', BC_FED_PLUGIN),
                array());

        wp_enqueue_script('fed_admin_script', plugins_url('/admin/assets/fed_admin_script.js', BC_FED_PLUGIN), array());

        wp_enqueue_script('fed_bootstrap_script', plugins_url('/common/assets/js/bootstrap.js', BC_FED_PLUGIN),
                array());

        wp_enqueue_script('fed_jscolor_script', plugins_url('/common/assets/js/jscolor.js', BC_FED_PLUGIN), array());

        wp_enqueue_script('fed_select2_script', plugins_url('/common/assets/js/select2.js', BC_FED_PLUGIN), array());

        wp_enqueue_script('fed_flatpickr', plugins_url('/common/assets/js/flatpickr.js', BC_FED_PLUGIN), array());

        wp_enqueue_style('fed_admin_bootstrap',
                plugins_url('/common/assets/css/bootstrap.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');


        wp_enqueue_style('fed_frontend_sweetalert',
                plugins_url('/common/assets/css/sweetalert2.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');


        wp_enqueue_style('fed_admin_font_awesome',
                plugins_url('/common/assets/css/fontawesome.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');

        wp_enqueue_style('fed_admin_font_awesome-shims',
                plugins_url('/common/assets/css/fontawesome-shims.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');

        wp_enqueue_style('fed_flatpikcer',
                plugins_url('/common/assets/css/flatpickr.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');

        wp_enqueue_style('fed_select2',
                plugins_url('/common/assets/css/select2.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');


        wp_enqueue_style('fed_admin_style',
                plugins_url('admin/assets/fed_admin_style.css', BC_FED_PLUGIN),
                array(), BC_FED_PLUGIN_VERSION, 'all');

        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-sortable');

        do_action('fed_enqueue_script_style_admin');

        wp_localize_script('fed_admin_script', 'frontend_dashboard', fed_js_translation());

        wp_enqueue_media();
    }

    wp_enqueue_style('fed_global_admin_style',
            plugins_url('admin/assets/fed_global_admin_style.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');
}

/**
 * Frontend Script.
 */
function fed_script_front_end()
{

    wp_enqueue_script('fed_script', plugins_url('/common/assets/js/fed_script.js', BC_FED_PLUGIN), array('jquery'));
    wp_enqueue_script('fed_sweetalert', plugins_url('/common/assets/js/sweetalert2.js', BC_FED_PLUGIN), array());

    if ( ! wp_script_is('bootstrap', 'enqueued')) {

        //Enqueue
        wp_enqueue_script('fed_bootstrap_script', plugins_url('/common/assets/js/bootstrap.js', BC_FED_PLUGIN),
                array());

    }

    wp_enqueue_script('fed_fontawesome', plugins_url('/common/assets/js/fontawesome.js', BC_FED_PLUGIN), array());


//	wp_enqueue_script( 'fed_bootstrap_script', plugins_url( '/common/assets/js/bootstrap.js', BC_FED_PLUGIN ), array() );

    wp_enqueue_script('fed_jscolor_script', plugins_url('/common/assets/js/jscolor.js', BC_FED_PLUGIN), array());
    wp_enqueue_script('fed_fontawesome-shims', plugins_url('/common/assets/js/fontawesome-shims.js', BC_FED_PLUGIN),
            array());

    wp_enqueue_script('fed_select2_script', plugins_url('/common/assets/js/select2.js', BC_FED_PLUGIN), array());


    wp_enqueue_script('fed_flatpickr', plugins_url('/common/assets/js/flatpickr.js', BC_FED_PLUGIN), array());

    wp_enqueue_style('fed_frontend_bootstrap',
            plugins_url('/common/assets/css/bootstrap.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_frontend_font_awesome',
            plugins_url('/common/assets/css/fontawesome.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_admin_font_awesome-shims',
            plugins_url('/common/assets/css/fontawesome-shims.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');


    wp_enqueue_style('fed_frontend_sweetalert',
            plugins_url('/common/assets/css/sweetalert2.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_frontend_animate',
            plugins_url('/common/assets/css/animate.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_flatpikcer',
            plugins_url('/common/assets/css/flatpickr.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_fontend_style',
            plugins_url('/common/assets/css/common-style.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_global_admin_style',
            plugins_url('admin/assets/fed_global_admin_style.css', BC_FED_PLUGIN),
            array('dashicons'), BC_FED_PLUGIN_VERSION, 'all');

    wp_enqueue_style('fed_select2',
            plugins_url('/common/assets/css/select2.css', BC_FED_PLUGIN),
            array(), BC_FED_PLUGIN_VERSION, 'all');

    do_action('fed_enqueue_script_style_frontend');

    // Pass PHP value to JavaScript
    $translation_array = apply_filters('fed_convert_php_js_var', fed_js_translation());


    wp_localize_script('fed_script', 'frontend_dashboard', $translation_array);

    wp_enqueue_media();

}