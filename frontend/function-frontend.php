<?php
/**
 * @param        $id_or_email
 * @param string $alt
 * @param string $class
 * @param string $extra
 * @param string $size
 * @param string $attr
 *
 * @return string
 */
function fed_get_avatar($id_or_email, $alt = '', $class = '', $extra = '', $size = '', $attr = '')
{
    $fed_upl   = get_option('fed_admin_settings_upl');
    $user_data = $id_or_email;

    if (filter_var($user_data, FILTER_VALIDATE_EMAIL)) {
        $user_id   = get_user_by('email', $id_or_email);
        $user_data = $user_id->ID;
    }

    //var_dump($fed_upl);
    if ($fed_upl['settings']['fed_upl_change_profile_pic'] != '') {
        //$current_user = wp_get_current_user();
        $user_obj = get_userdata($user_data);

        $gavatar_id = $user_obj->has_prop($fed_upl['settings']['fed_upl_change_profile_pic']) ?
                $user_obj->get($fed_upl['settings']['fed_upl_change_profile_pic']) :
                '';

        if ($gavatar_id != '') {
            return wp_get_attachment_image((int)$gavatar_id, $size, $icon = false, $attr);
        }

        // send the default image
        return sprintf(
                "<img alt='%s' src='%s' class='%s' %s />",
                esc_attr($alt),
                esc_url('https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mm&f=y&s=600'),
                esc_attr($class),
                $extra
        );
    }

    $get_avatar = get_avatar_data($id_or_email, array('size' => 600));
    $avatar     = sprintf(
            "<img alt='%s' src='%s' class='%s' %s />",
            esc_attr($alt),
            esc_url($get_avatar['url']),
            esc_attr($class),
            $extra
    );

    return $avatar;
}

/**
 * @return string
 */
function fed_get_current_page_url()
{
    global $wp;

    return home_url(add_query_arg(array(), $wp->request));
}

/**
 * @return string
 */
function fed_current_page_url()
{
    global $wp;

    return add_query_arg($_SERVER['QUERY_STRING'], '', home_url($wp->request));
}

/**
 * Get Registration content
 *
 * @return array
 */
function fed_get_registration_content_fields()
{
    $details         = fed_fetch_user_profile_by_registration();
    $fed_admin_login = get_option('fed_admin_login');
    $role            = fed_role_with_pricing_flat($fed_admin_login);


    if ($details instanceof WP_Error) {
        wp_die(__('Default Tables not installed, please contact support', 'frontend-dashboard'));
    }

    $registration = array();
    foreach ($details as $detail) {
        $registration[$detail['input_meta']] = array(
                'name'        => $detail['label_name'],
                'input'       => fed_get_input_details(
                        array(
                                'input_meta'  => $detail['input_meta'],
                                'placeholder' => $detail['placeholder'],
                                'class'       => $detail['class_name'],
                                'id'          => $detail['id_name'],
                                'is_required' => $detail['is_required'],
                                'step'        => $detail['input_step'],
                                'min'         => $detail['input_min'],
                                'max'         => $detail['input_max'],
                                'rows'        => $detail['input_row'],
                                'user_value'  => $detail['input_value'],
                                'input_type'  => $detail['input_type'],
                                'input_value'  => $detail['input_value'],
                        )
                ),
                'input_order' => $detail['input_order'],
        );
    }

    if ($role) {
        $registration['role'] = array(
                'name'        => $fed_admin_login['register']['name'],
                'input'       => fed_input_box('role', array('options' => $role), 'select'),
                'input_order' => $fed_admin_login['register']['position'],
        );
    }

    /**
     * Hidden text to make sure its a registration form
     */
    $registration['fed_registration_form'] = array(
            'name'        => '',
            'input'       => fed_get_input_details(
                    array(
                            'input_meta'  => 'fed_registration_form',
                            'placeholder' => '',
                            'class'       => '',
                            'id'          => '',
                            'is_required' => 'true',
                            'step'        => '',
                            'min'         => '',
                            'max'         => '',
                            'rows'        => '',
                            'user_value'  => 'frf',
                            'input_type'  => 'hidden',
                    )
            ),
            'input_order' => 9999,
    );

    return $registration;

}

/**
 * Process required field to match the requirement
 */
function fed_process_user_profile_required_field()
{
    $fields = fed_fetch_table_by_is_required(BC_FED_USER_PROFILE_DB);

    $values = array_reduce($fields, function ($result, $item) {
        $result[$item['input_meta']] = 'Please enter '.$item['label_name'];

        return $result;
    }, array());

    return $values;
}

/**
 * @param $menu
 *
 * @return mixed
 */
function fed_process_user_profile_required_by_menu($menu)
{
    $fields = fed_fetch_user_profile_required_by_menu($menu);

    $values = array_reduce($fields, function ($result, $item) {
        $result[$item['input_meta']] = 'Please enter '.$item['label_name'];

        return $result;
    }, array());

    return $values;
}


/**
 * Process Author Input Record
 *
 * @param object $user        User Object
 * @param string $single_item Single Item
 *
 * @return string
 */
function fed_process_author_details($user, array $single_item)
{
    /**
     * 'text'     => 'Text Box',
     * 'number'   => 'Number Box',
     * 'textarea' => 'Textarea',
     * 'email'    => 'Email',
     * 'checkbox' => 'Checkbox',
     * 'select'   => 'Select',
     * 'radio'    => 'Radio',
     * 'password' => 'Password',
     * 'url'      => 'URL',
     * 'date'     => 'Date',
     * 'file'     => 'File',
     * 'color'    => 'Color'
     */
    if ($single_item['input_type'] === 'file') {
        return wp_get_attachment_image((int)$user->get($single_item['input_meta']), 'thumbnail');
    }

    if ($single_item['input_type'] === 'url') {
        return make_clickable($user->get($single_item['input_meta']));
    }

    if ($single_item['input_type'] === 'color') {
        return fed_input_box($single_item['input_meta'], array(
                'value'    => $user->get($single_item['input_meta']),
                'disabled' => true,
        ), 'color');
    }

    if ($single_item['input_type'] === 'checkbox') {
        return fed_input_box($single_item['input_meta'], array(
                'value'    => $user->get($single_item['input_meta']),
                'disabled' => true,
        ), 'checkbox');
    }

    if ($single_item['input_type'] === 'radio') {
        $input_value = fed_convert_comma_separated_key_value($single_item['input_value']);

        return $input_value[$user->get($single_item['input_meta'])];
    }

    if ($single_item['input_type'] === 'date') {
        $user_date = $user->get($single_item['input_meta']);
        $extended  = is_string($single_item['extended']) ? unserialize($single_item['extended']) : $single_item['extended'];
        $format    = $extended['enable_time'] == 'true' ? '%e %B %Y -  %I:%M %p' : '%e %B %Y';

        if (strpos($user_date, 'to') !== false) {
            $range = explode('to', $user_date);

            return ucfirst(strftime($format, strtotime($range[0]))).' to '.ucfirst(strftime($format,
                            strtotime($range[1])));

        }

        if (strpos($user_date, ';') !== false) {
            $multiple      = explode(';', $user_date);
            $multiple_item = '';
            foreach ($multiple as $item) {
                $multiple_item .= ucfirst(strftime($format, strtotime($item))).'<br>';
            }

            return $multiple_item;
        }

        return ucfirst(strftime($format, strtotime($user_date)));
    }

    $value = $user->get($single_item['input_meta']);

    return apply_filters('fed_process_author_custom_details', $value, $user, $single_item);
}

/**
 * User Input Mandatory Required Fields
 */
function fed_input_mandatory_required_fields()
{
    return apply_filters('fed_input_mandatory_required_fields', array(
            'user_login',
            'user_pass',
            'confirmation_password',
            'user_email',
    ));
}

/**
 * @param string $content
 * @param        $id
 * @param array  $options
 *
 * @return string
 */
function fed_get_wp_editor($content = '', $id, array $options = array())
{
    ob_start();

    wp_editor($content, $id, $options);

    $temp = ob_get_clean();
    $temp .= \_WP_Editors::enqueue_scripts();
    print_footer_scripts();
    $temp .= \_WP_Editors::editor_js();

    return $temp;
}

/**
 * @param string $post
 * @param string $cpt
 *
 * @return string
 */
function fed_get_dashboard_display_categories($post = '', $cpt = '')
{
    $categories         = array();
    $fed_get_categories = get_terms(array(
            'taxonomy'   => $cpt->name,
            'hide_empty' => false,
    ));
    if (isset($post->ID)) {
        $categories = wp_get_post_terms($post->ID, $cpt->name, array('fields' => 'ids'));
    }

    return fed_convert_array_to_id_name($fed_get_categories, 'term_id', 'tax_input['.$cpt->name.']', $categories);
}

/**
 * @param string $pos_id
 *
 * @return string
 */
function fed_get_dashboard_display_tags($post = '', $cpt = '')
{
    $tags         = array();
    $fed_get_tags = get_terms(array(
            'taxonomy'   => $cpt->name,
            'hide_empty' => false,
    ));
    if (isset($post->ID)) {
        $tags = wp_get_post_terms($post->ID, $cpt->name, array('fields' => 'slugs'));
    }

    return fed_convert_array_to_id_name($fed_get_tags, 'slug', 'tax_input['.$cpt->name.']', $tags);
}

/**
 * @param        $array
 * @param string $key
 * @param string $type
 * @param array  $compare
 *
 * @return string
 */
function fed_convert_array_to_id_name(array $array, $key = 'term_id', $type = '', $compare = array())
{
    $new_category = array();
    $html         = '';

    if ($array) {
        foreach ($array as $value) {
            $new_category[$value->$key] = $value->name;
        }

        foreach ($new_category as $index => $new_value) {
            $actual_value = '';
            if (count($compare) > 0 && in_array($index, $compare, false)) {
                $actual_value = $index;
            }

            $html .= fed_input_box($type,
                    array(
                            'name'          => $type.'[]',
                            'default_value' => $index,
                            'label'         => $new_value,
                            'value'         => $actual_value,
                    ), 'checkbox');
        }

        return $html;
    }
    $temp = $key === 'term_id' ? 'Category' : 'Tag';

    return 'Sorry! there is no field associated to this '.$temp;
}

/**
 * Get post format
 */
function fed_dashboard_get_post_format()
{
    if (current_theme_supports('post-formats')) {
        $post_formats = get_theme_support('post-formats');
        if (is_array($post_formats[0])) {
            return $post_formats[0];
        }

        return false;
    }

    return false;
}

/**
 * Get post meta 0th element
 *
 * @param int    $id  post ID.
 * @param string $key Key.
 *
 * @return string.
 */
function fed_get_post_meta($id)
{
    $post_meta = get_post_meta($id);
    $temp      = array();
    foreach ($post_meta as $index => $items) {
        foreach ($items as $item) {
            $temp[$index] = $item;
        }
    }

    return $temp;
}

/**
 * @param $post_id
 *
 * @return array
 */
function fed_get_categories_id_by_post_id($post_id)
{
    $categories = get_the_category($post_id);

    return fed_convert_array_object_to_key_value($categories);
}

/**
 * @param $status
 *
 * @return string
 */
function fed_get_post_status_symbol($status)
{
    if ($status == 'publish') {
        return ' <i class="fa fa-check bg-primary-font" data-toggle="popover" data-trigger="hover" title="Post Status" 
 data-content="Awesome! This post as been published"></i>';
    }
    if ($status == 'pending') {
        return ' <i class="fa fa-pause bg-info-font" data-toggle="popover" data-trigger="hover" title="Post Status" 
 data-content="Please wait, your post is in pending status, editor or admin will approve your post."></i>';
    }

    return '<i class="fa fa-exclamation bg-danger-font" data-toggle="popover" data-trigger="hover" title="Post Status" 
 data-content="Oops! Something went wrong, please contact admin"></i>';

}

/**
 * @param $pos_id
 *
 * @return array
 */
function fed_get_tags_id_by_post_id($pos_id)
{
    $tags = get_the_tags($pos_id);

    return fed_convert_array_object_to_key_value($tags);
}

/**
 * @param        $array
 * @param string $key
 * @param string $value
 *
 * @return array
 */
function fed_convert_array_object_to_key_value($array, $key = 'slug', $value = 'term_id')
{
    $ids = array();
    foreach ($array as $object) {
        $ids[$object->$key] = $object->$value;
    }

    return $ids;
}

/**
 *
 */
function fed_get_payment_notification()
{
    if (isset($_REQUEST['success']) && $_REQUEST['success'] == 'no') {
        ?>
        <div class="alert alert-danger">
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;
            </button>
            <strong>Cancelled!</strong>
            Sorry your transaction has been cancelled
        </div>
        <?php
    }
    if (isset($_REQUEST['success']) && $_REQUEST['success'] == 'yes' && isset($_REQUEST['tid'])) {
        ?>
        <div class="alert alert-success">
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;
            </button>
            <strong>Payment Success!</strong>
            Thanks for your payment - You transaction ID
            : <?php echo $_REQUEST['tid']; ?>
        </div>
        <?php
    }
}

/**
 * @param $fed_user_attr
 */
function fed_show_users_by_role($fed_user_attr)
{
    $user_roles    = fed_get_user_roles();
    $get_user_role = $fed_user_attr->role;
    $current_url   = get_site_url().'/'.$get_user_role.'/';
    ?>
<div class="bc_fed fed_user_roles_container <?php echo $get_user_role; ?>">

    <?php
    if ( ! array_key_exists($get_user_role, $user_roles)) {
        ?>
        <div class="alert alert-danger">
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;
            </button>
            <strong>Sorry!</strong>
            The User Role " <?php echo $get_user_role; ?>" is not available on your domain,
            please
            check the spelling assigned to the
            <strong>"role"</strong>
            short-code
        </div>
        <?php
    } else {
        $get_all_users = new WP_User_Query(array('role' => $get_user_role));
        if ( ! $get_all_users->get_total()) {
            ?>
            <div class="alert alert-info">
                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-hidden="true">&times;
                </button>
                <strong>Sorry!</strong>
                There are no User assigned to this User Role " <?php echo $get_user_role; ?>"
            </div>
            <?php
        } else {
            $chunks = array_chunk($get_all_users->get_results(), 4);
            foreach ($chunks as $chunk) {
                ?>
                <div class="row fed_user_role_single">
                    <?php
                    foreach ($chunk as $get_all_user) {
                        $name  = $get_all_user->get('display_name');
                        $email = $get_all_user->get('user_email');
                        ?>
                        <div class="col-md-3">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <?php echo fed_get_avatar($email, $name); ?>
                                </div>
                                <div class="panel-footer bg-primary">
                                    <h3 class="panel-title">
                                        <a target="_blank"
                                           href="<?php echo get_permalink().'?fed_user_profile='.$get_all_user->ID; ?>">
                                            <?php echo $name; ?>
                                        </a>
                                    </h3>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }
        ?>
        </div>
        <?php
    }
}

/**
 * @param $fed_user_attr
 * @param $user_id
 */
function fed_show_user_by_role($fed_user_attr, $user_id)
{
    $user = new WP_User_Query(array(
            'include' => (int)$user_id,
            'role'    => $fed_user_attr->role,
    ));
    if ( ! $user->get_total()) {
        ?>
        <div class="alert alert-info text-center">
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;
            </button>
            <strong><?php _e('Sorry!', 'frontend-dashboard') ?></strong>
            <?php _e('No user found...', 'frontend-dashboard') ?>
        </div>
        <?php
    } else {
        $results = $user->get_results();
        fed_show_user_profile_page($results[0]);
    }
    ?>

    <?php
}

/**
 * Show user profile page by user ID
 *
 * @param object $user User Data
 */
function fed_show_user_profile_page($user)
{
    /**
     * Collect Menu, User Information and Menu Items
     */
    $profiles    = fed_array_group_by_key(fed_fetch_user_profile_by_dashboard(), 'menu');
    $menus       = fed_fetch_table_rows_with_key(BC_FED_MENU_DB, 'menu_slug');
    $upl_options = get_option('fed_admin_settings_upl');

    /**
     * Get author recent Posts
     */
    $post_count   = isset($upl_options['settings']['fed_upl_no_recent_post']) ? $upl_options['settings']['fed_upl_no_recent_post'] : 5;
    $author_query = array('posts_per_page' => $post_count, 'author' => $user->ID, 'order' => 'DESC');
    $author_posts = new WP_Query($author_query);

    ?>
    <div id="primary fed_user_profile"
         class="bc_fed fed-profile-area container">

        <div class="row fed_profile_container">
            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-12 fed_profile_picture">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="fed_profile_full_name text-center">
                                    <h3 class="panel-title">
                                        <?php esc_attr_e($user->get('display_name'), 'frontend-dashboard') ?>
                                    </h3>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo fed_get_avatar($user->ID, $user->display_name, 'img-responsive'); ?>
                            </div>

                            <div class="panel-footer">

                                <?php
                                do_action('fed_show_support_button_at_user_profile', $user);

                                if ($upl_options['settings']['fed_upl_disable_desc'] === 'no') { ?>
                                    <div class="row">
                                        <div class="col-md-12 fed_profile_description">
                                            <?php esc_attr_e($user->get('description'), 'frontend-dashboard') ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 fed_dashboard_container">
                <div class="row fed_dashboard_wrapper">
                    <div class="col-md-12 fed_dashboard_menus fed_collapse">
                        <ul class="nav nav-pills nav-justified list-group ">
                            <?php
                            $first = true;
                            foreach ($profiles as $index => $profile) {
                                if ($menus[$index]['show_user_profile'] !== 'Enable') {
                                    continue;
                                }
                                if ($first) {
                                    $first  = false;
                                    $active = 'active';
                                } else {
                                    $active = '';
                                }
                                ?>
                                <li class="fed_menu_slug <?php echo $active ?>"
                                    data-menu="<?php echo $menus[$index]['menu_slug']; ?>"
                                >
                                    <a href="#<?php echo $menus[$index]['menu_slug']; ?>">
                                        <span class="<?php echo $menus[$index]['menu_image_id'] ?>"></span>
                                        <?php esc_attr_e(ucwords($menus[$index]['menu']), 'frontend-dashboard') ?>
                                    </a>
                                </li>
                                <?php
                            } ?>
                        </ul>
                    </div>
                    <div class="col-md-12 fed_dashboard_items">
                        <?php
                        $first = true;
                        foreach ($profiles as $index => $item) {
                            if ($menus[$index]['show_user_profile'] !== 'Enable') {
                                continue;
                            }
                            if ($first) {
                                $first  = false;
                                $active = '';
                            } else {
                                $active = 'hide';
                            }
                            ?>
                            <div class="panel panel-primary fed_dashboard_item <?php echo $active.' '.$index ?>">
                                <div class="panel-body">
                                    <?php
                                    foreach ($item as $single_item) {
                                        if ($single_item['show_user_profile'] !== 'Enable') {
                                            continue;
                                        }

                                        if ($single_item['input_meta'] === 'user_pass' || $single_item['input_meta'] === 'confirmation_password') {
                                            continue;
                                        }
                                        if (in_array($single_item['input_meta'], fed_no_update_fields(), false)) {
                                            $single_item['readonly'] = 'readonly';
                                        }
                                        if (count(array_intersect($user->roles,
                                                        unserialize($single_item['user_role']))) <= 0) {
                                            continue;
                                        }

                                        ?>
                                        <div class="row fed_dashboard_item_field">
                                            <div class="fed_dashboard_label_name fed_header_font_color col-md-4 text-right-md text-right-not-sm text-right-not-xs">
                                                <?php esc_attr_e($single_item['label_name'], 'frontend-dashboard') ?>
                                            </div>
                                            <div class="col-md-8">
                                                <?php echo fed_process_author_details($user, $single_item) ?>
                                            </div>

                                        </div>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
        <div class="row fed_user_posts_container">
            <div class="col-md-12">
                <h2><?php _e('Recent Post by', 'frontend-dashboard') ?><?php echo $user->get('display_name') ?></h2>
            </div>
            <div class="col-md-8 fed_posts">
                <?php
                while ($author_posts->have_posts()) :
                    $author_posts->the_post();
                    ?>
                    <div class="fed_post_title">
                        <a href="<?php the_permalink(); ?>"
                           title="<?php the_title_attribute(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </div>
                    <div class="fed_post_excerpt">
                        <?php the_excerpt() ?>
                    </div>
                <?php
                endwhile;
                ?>

            </div>
            <div class="col-md-4 fed_sidebar">
                <?php get_sidebar() ?>
            </div>
        </div>
    </div>
    <?php do_action('fed_user_profile_below');

}

function fed_get_403_error_page()
{
    $url = explode('?', esc_url_raw(add_query_arg(array())));
    ?>
    <div class="panel panel-primary fed_dashboard_item active">
        <div class="panel-heading">
            <h3 class="panel-title">
                <span class="fa fa-exclamation-triangle"></span>
                <?php _e('Error (403)', 'frontend-dashboard') ?>
            </h3>
        </div>
        <div class="panel-body">
            <h2><?php _e('Unauthorised Access', 'frontend-dashboard') ?></h2>
            <a class="btn btn-primary" href="<?php echo $url[0]; ?>"><?php _e('Click here to visit Dashboard',
                        'frontend-dashboard') ?></a>
        </div>
    </div>
    <?php
}

/**
 * @param $key
 * @param $message
 */
function fed_set_alert($key, $message)
{
    set_transient($key, $message, MINUTE_IN_SECONDS);
}

/**
 * @param $key
 *
 * @return string
 */
function fed_show_alert($key)
{
    $value = get_transient($key);
    $html  = '';
    if ($value) {
        if (is_array($value)) {
            $value = $value[0];
        }
        $html .= '<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<strong>'.$value.'</strong>
						</div>';
        delete_transient($key);
    }

    return $html;
}

/**
 * Disabled for future release
 */
//function fed_dashboard_posts_nav($postsss) {
//
////	if( is_singular() )
////		return;
//
//	/** Stop execution if there's only 1 page */
//	if( $postsss->max_num_pages <= 1 )
//		return;
//
//	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
//	$max   = intval( $postsss->max_num_pages );
//
//	/**	Add current page to the array */
//	if ( $paged >= 1 )
//		$links[] = $paged;
//
//	/**	Add the pages around the current page to the array */
//	if ( $paged >= 3 ) {
//		$links[] = $paged - 1;
//		$links[] = $paged - 2;
//	}
//
//	if ( ( $paged + 2 ) <= $max ) {
//		$links[] = $paged + 2;
//		$links[] = $paged + 1;
//	}
//
//	echo '<div class="navigation"><ul>' . "\n";
//
//	/**	Previous Post Link */
//	if ( get_previous_posts_link() )
//		printf( '<li>%s</li>' . "\n", get_previous_posts_link() );
//
//	/**	Link to first page, plus ellipses if necessary */
//	if ( ! in_array( 1, $links ) ) {
//		$class = 1 == $paged ? ' class="active"' : '';
//
//		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
//
//		if ( ! in_array( 2, $links ) )
//			echo '<li>…</li>';
//	}
//
//	/**	Link to current page, plus 2 pages in either direction if necessary */
//	sort( $links );
//	foreach ( (array) $links as $link ) {
//		$class = $paged == $link ? ' class="active"' : '';
//		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
//	}
//
//	/**	Link to last page, plus ellipses if necessary */
//	if ( ! in_array( $max, $links ) ) {
//		if ( ! in_array( $max - 1, $links ) )
//			echo '<li>…</li>' . "\n";
//
//		$class = $paged == $max ? ' class="active"' : '';
//		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
//	}
//
//	/**	Next Post Link */
//	if ( get_next_posts_link() )
//		printf( '<li>%s</li>' . "\n", get_next_posts_link() );
//
//	echo '</ul></div>' . "\n";
//
//}
//add_filter( 'dynamic_sidebar_params', 'fed_dynamic_sidebar_params' );
//function fed_dynamic_sidebar_params( $params ) {
//	var_dump( $params );
//}