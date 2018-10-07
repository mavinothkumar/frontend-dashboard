<?php


function fed_login_form_submit($post)
{
    do_action('fed_login_before_validation', $post);

    $credentials = array(
            'user_login'    => $post['user_login'],
            'user_password' => $post['user_password'],
            'remember'      => isset($post['remember']) ? true : false,
    );

    $redirect_url = fed_get_login_redirect_url();

    $errors = fed_validate_login_form($post);

    if ($errors instanceof WP_Error) {
        wp_send_json_error(array('user' => $errors->get_error_messages()));
    }

    $result = wp_signon($credentials);

    //var_dump( $result);

    if ($result instanceof WP_Error) {
        wp_send_json_error(array('user' => $result->get_error_messages()));
    }

    $redirect_url = $redirect_url == false ? home_url() : $redirect_url;

    wp_send_json_success(array(
            'user'    => $result,
            'message' => __('Successfully Logged in', 'frontend-dashboard'),
            'url'     => $redirect_url,
            'reload'  => $redirect_url,
    ));

}





