<?php

/**
 * Input Fields.
 *
 * @param  string  $meta_key  Meta Key
 * @param  array  $attr  Input Attributes
 * @param  string  $type  Input Format.
 *
 * @return string
 */
function fed_input_box($meta_key, $attr = array(), $type = 'text')
{
    /**
     * Break it, if the input meta doesn't exist
     */
    if (empty($meta_key)) {
        wp_die('Please add input meta key');
    }

    $values                = array();
    $values['placeholder'] = isset($attr['placeholder']) && ! empty($attr['placeholder']) ? esc_attr($attr['placeholder']) : '';
    $values['label']       = isset($attr['label']) && ! empty($attr['label']) ? strip_tags($attr['label'],
            '<i><b>') : '';
    $values['class_name']  = isset($attr['class']) && ! empty($attr['class']) ? esc_attr($attr['class']) : '';

    $values['user_value'] = isset($attr['value']) && ! empty($attr['value']) ? esc_attr($attr['value']) : '';
    $values['input_min']  = isset($attr['min']) && ! empty($attr['min']) ? esc_attr($attr['min']) : 0;
    $values['input_max']  = isset($attr['max']) && ! empty($attr['max']) ? esc_attr($attr['max']) : 99999999999999999999999999999999999999999999999999;
    $values['input_step'] = isset($attr['step']) && ! empty($attr['step']) ? esc_attr($attr['step']) : 'any';

    $values['is_required']   = isset($attr['is_required']) && $attr['required'] == 'true' ? 'required="required"' : '';
    $values['id_name']       = isset($attr['id']) && ! empty($attr['id']) ? esc_attr($attr['id']) : '';
    $values['readonly']      = isset($attr['readonly']) && $attr['readonly'] === true ? true : '';
    $values['user_value']    = isset($attr['value']) && ! empty($attr['value']) ? esc_attr($attr['value']) : '';
    $values['input_value']   = isset($attr['options']) && ! empty($attr['options']) ? $attr['options'] : '';
    $values['disabled']      = isset($attr['disabled']) && $attr['disabled'] === true ? true : '';
    $values['default_value'] = isset($attr['default_value']) && ! empty($attr['default_value']) ? esc_attr($attr['default_value']) : 'yes';
    $values['extra']         = isset($attr['extra']) ? $attr['extra'] : '';
    $values['extended']      = isset($attr['extended']) && ! empty($attr['extended']) ? esc_attr($attr['extended']) : array();
    $values['input_type']    = $type;
    $values['input_meta']    = isset($attr['name']) ? $attr['name'] : $meta_key;
    $values['content']       = isset($attr['content']) ? $attr['content'] : '';

    return fed_get_input_details($values);
}

/**
 * Loader.
 *
 * @param  string  $hide
 *
 * @return string
 */
function fed_loader($hide = 'hide', $message = null)
{
    $html = '<div class="preview-area '.$hide.'">
        <div class="spinner_circle">
            <div class="double-bounce1"></div>
            <div class="double-bounce2"></div>
        </div>';

    if ($message) {
        $html .= '<div class="fed_loader_message hide">'.$message.'</div>';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Sort given array by order key
 */
function fed_sort_by_order($a, $b)
{
    if (isset($a['order'], $b['order'])) {
        return $a['order'] - $b['order'];
    }
    if (isset($a['input_order'], $b['input_order'])) {
        return $a['input_order'] - $b['input_order'];
    }
    if (isset($a['menu_order'], $b['menu_order'])) {
        return $a['menu_order'] - $b['menu_order'];
    }

    return 199;
}

/**
 * Sort by Desc
 *
 * @param  array  $a  First Element.
 * @param  array  $b  Second Element.
 *
 * @param  string  $key
 *
 * @return int
 */
function fed_sort_by_desc($a, $b)
{
    if (isset($a['id'], $b['id'])) {
        return (int) $b['id'] - (int) $a['id'];
    }

    return 199;
}

/**
 * @param  int  $action
 * @param  string  $name
 * @param  bool  $referer
 * @param  bool  $echo
 *
 * @return string
 */
function fed_wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true, $echo = true)
{
    $name        = esc_attr($name);
    $nonce_field = '<input type="hidden" name="'.$name.'" value="'.wp_create_nonce($action).'" />';

    if ($referer) {
        $nonce_field .= wp_referer_field(false);
    }

    if ($echo) {
        echo $nonce_field;
    }

    return $nonce_field;
}

/**
 * Generate Random String
 *
 * @param  int  $length
 *
 * @return string
 */
function fed_get_random_string($length = 10)
{
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}


/**
 * @param $content
 *
 * @return bool
 */
function fed_is_shortcode_in_content()
{
    global $post;
    $shortcodes = fed_shortcode_lists();

    if (is_a($post, 'WP_Post')) {
        foreach ($shortcodes as $shortcode) {
            if (has_shortcode($post->post_content, $shortcode)) {
                return true;
            }
        }
    }

    return false;
}

/**
 * @return mixed|void
 */
function fed_shortcode_lists()
{
    return apply_filters('fed_shortcode_lists', array(
            'fed_login',
            'fed_login_only',
            'fed_register_only',
            'fed_forgot_password_only',
            'fed_dashboard',
            'fed_user',
    ));
}

/**
 * @return array
 */
function fed_js_translation()
{
    return array(
            'plugin_url'          => plugins_url(BC_FED_PLUGIN_NAME),
            'payment_info'        => 'no',
            'fed_admin_form_post' => admin_url('admin-ajax.php?action=fed_admin_form_post&nonce='.wp_create_nonce("fed_admin_form_post")),
            'fed_login_form_post' => admin_url('admin-ajax.php?action=fed_login_form_post&nonce='.wp_create_nonce("fed_login_form_post")),
            'alert'               => array(
                    'confirmation'            => array(
                            'title'   => __('Are you sure?', 'frontend-dashboard'),
                            'text'    => __('You want to do this action?', 'frontend-dashboard'),
                            'confirm' => __('Yes, Please Proceed', 'frontend-dashboard'),
                            'cancel'  => __('No, Cancel it', 'frontend-dashboard'),

                    ),
                    'redirecting'             => __('Please wait, you are redirecting..', 'frontend-dashboard'),
                    'title_cancelled'         => __('Cancelled', 'frontend-dashboard'),
                    'something_went_wrong'    => __('Something Went Wrong', 'frontend-dashboard'),
                    'invalid_form_submission' => __('Invalid form submission', 'frontend-dashboard'),
                    'please_try_again'        => __('Please try again', 'frontend-dashboard'),
            ),
            'common'              => array(
                    'hide_add_new_menu' => __('Hide Add New Menu', 'frontend-dashboard'),
                    'add_new_menu'      => __('Add New Menu', 'frontend-dashboard'),
            ),
    );
}

/**
 * @param  null  $action
 *
 * @return string|void
 */
function fed_get_form_action($action = null)
{
    if ($action) {
        return admin_url('admin-ajax.php?action='.$action);
    }

    return '#';
}


/**
 * @param $user_role
 *
 * @return bool
 */
function fed_is_current_user_role($user_role)
{
    $user          = wp_get_current_user();
    $allowed_roles = fed_get_user_roles();
    if (array_intersect(array_keys($allowed_roles), $user->roles)) {
        return true;
    }

    return false;
}

/**
 * @return bool
 */
function fed_is_admin()
{
    return fed_is_current_user_role('administrator');
}

/**
 * @return mixed|void
 */
function fed_get_default_menu_type()
{
    return apply_filters('fed_get_default_menu_type', array(
            'post',
            'user',
            'logout',
            'collapse',
            'custom',
    ));
}

/**
 * @param $string
 *
 * @return array
 */
function fed_menu_split($string)
{
    return explode('_', $string, 2);
}

/**
 * @return array
 */
function fed_get_menu_mobile_attributes()
{
    $collapse = array();
    if (wp_is_mobile()) {
        $collapse['in']     = '';
        $collapse['d']      = ' collapsed';
        $collapse['expand'] = 'false';
    } else {
        $collapse['in']     = 'in';
        $collapse['d']      = ' ';
        $collapse['expand'] = 'true';
    }

    return $collapse;
}

/**
 * @param $var
 * @param  bool  $exit
 */
function bcdump($var, $exit = false)
{
    echo '<pre style="font-size:11px;">';

    if (is_array($var) || is_object($var)) {
        echo htmlentities(print_r($var, true));
    } elseif (is_string($var)) {
        echo "string(".strlen($var).") \"".htmlentities($var)."\"\n";
    } else {
        var_dump($var);
    }

    echo "\n</pre>";

    if ($exit) {
        exit;
    }
}


/**
 * @param $className
 * @param  array  $arguments
 *
 * @return bool|mixed
 */
function fed_create_new_instance($className, array $arguments = array())
{
    if (class_exists($className)) {
        try {
            return call_user_func_array(array(
                    new ReflectionClass($className), 'newInstance',
            ),
                    $arguments);
        } catch (ReflectionException $e) {
            wp_die('Class Name '.$className.' Doesnt exist');
        }
    }

    wp_die('Class Name '.$className.' Doesnt exist');
}

/**
 * @param $page_slug
 *
 * @param  string  $parameters
 *
 * @return string|void
 */
function fed_menu_page_url($page_slug, $parameters = null)
{
    if (is_array($parameters) && count($parameters)) {
        return admin_url('/admin.php?page='.$page_slug).'&'.http_build_query($parameters);
    }

    return admin_url('/admin.php?page='.$page_slug);
}