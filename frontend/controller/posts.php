<?php
if ( ! defined('ABSPATH')) {
    exit;
}
/**
 * Default Post Options
 *
 * @return array
 */


/**
 * Process Dashboard Display Post
 *
 * @param  string  $post_type
 *
 * @return WP_Query
 */
function fed_process_dashboard_display_post($post_type = 'post')
{
    $user  = get_userdata(get_current_user_id());
    $paged = isset($_REQUEST['page_number']) ? absint($_REQUEST['page_number']) : 1;
    $args  = array(
        'author'         => $user->ID,
        'orderby'        => 'post_date',
        'order'          => 'DESC',
        'posts_per_page' => get_option('posts_per_page', 10),
        'paged'          => $paged,
        'post_status'    => array('publish', 'pending'),
        'post_type'      => $post_type,
    );

    return new WP_Query($args);
}

/**
 * Display Dashboard Post
 *
 * @param  string  $first_element  First Element.
 */
function fed_display_dashboard_post($first_element)
{
    $fed_posts = fed_process_dashboard_display_post();
    $menus     = fed_get_post_menu();
    foreach ($fed_posts as $index => $post) {
        if ($index == $first_element) {
            $active = '';
        } else {
            $active = 'hide';
        }
        ?>
        <div class="panel panel-primary fed_dashboard_item <?php echo $active.' '.$index ?>">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="fa <?php echo $menus[$index]['post_menu_icon'] ?>"></span>
                    <?php echo ucwords($menus[$index]['menu']) ?>
                </h3>
            </div>
            <div class="panel-body fed_dashboard_panel_body">
                <div class="fed_panel_body_container">
                    <?php
                    fed_display_dashboard_view_post_list();
                    ?>

                </div>
            </div>
        </div>
    <?php }
}

/**
 * Post Pagination.
 *
 * @param  WP_Query  $post_object  Post
 *
 */
function fed_get_post_pagination($post_object, $menu)
{
    $pagination_counts = ceil($post_object->found_posts / get_option('posts_per_page', 10));
    $current_page      = isset($_REQUEST['page_number']) ? absint($_REQUEST['page_number']) : 1;

    if ($pagination_counts > 1) {
        ?>
        <ul class="pagination pagination-small fed_post_pagination">
        <?php
        for ($i = 1; $i <= $pagination_counts; $i++) {
            $class = '';
            if ($current_page == $i) {
                $class = 'class="active"';
            }
            ?>
            <li <?php echo $class; ?>>
                <a href="<?php echo esc_url(add_query_arg(
                    array(
                        'page_number' => $i,
                    )
                ), site_url()); ?>">
                    <span><?php echo $i; ?></span>
                </a>
            </li>
            <?php
        }
    }
    ?>
    </ul>
    <?php
}

/**
 * Display dashboard Add New Post
 *
 */
function fed_display_dashboard_add_new_post($post_type)
{
    $post_table    = fed_fetch_rows_by_table(BC_FED_TABLE_POST);
    $post_settings = fed_get_post_settings_by_type($post_type);
    $menu          = isset($post_settings['menu']['rename_post']) ? $post_settings['menu']['rename_post'] : strtoupper($post_type);
    $menu_item     = get_query_var('fed_menu_items');
    ?>
    <div class="row">
        <div class="col-md-5">
            <a class="btn btn-primary" href="<?php echo esc_url(add_query_arg(
                array(
                    'menu_type' => $menu_item['menu_type'],
                    'menu_slug' => $menu_item['menu_slug'],
                    'fed_nonce' => wp_create_nonce('fed_nonce'),
                )
            ), site_url()); ?>">
                <i class="fa fa-mail-reply"></i>
                Back to <?php echo $menu; ?>
            </a>
        </div>
    </div>
    <form method="post"
          class="fed_dashboard_add_new_post"
          action="<?php echo admin_url('admin-ajax.php?action=fed_dashboard_add_new_post'); ?>">

        <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>

        <input type="hidden"
               name="post_type"
               value="<?php echo $post_type; ?>">

        <input type="hidden"
               name="fed_post_type"
               value="<?php echo $post_type; ?>">

        <div class="row fed_dashboard_item_field">
            <div class="col-md-12">
                <div class="fed_header_font_color"><?php _e('Title') ?></div>
                <?php echo fed_get_input_details(array(
                    'placeholder' => 'Title',
                    'input_meta'  => 'post_title',
                    'input_type'  => 'single_line',
                )); ?>
            </div>

        </div>
        <?php
        if ( ! isset($post_settings['dashboard']['post_content'])) {
            ?>
            <div class="row fed_dashboard_item_field">
                <div class="col-md-12">
                    <div class="fed_header_font_color"><?php _e('Content') ?></div>
                    <?php wp_editor('', 'post_content', array('quicktags' => true)); ?>
                </div>

            </div>
            <?php
        }

        fed_show_category_tag_post_format($post_type, $post_settings);

        /**
         * Featured Image
         * _thumbnail_id
         */
        if ( ! isset($post_settings['dashboard']['featured_image'])) {
            ?>
            <div class="row fed_dashboard_item_field">
                <div class="col-md-12">
                    <div class="fed_header_font_color">
                        <?php _e('Featured Image') ?>
                    </div>
                    <?php echo fed_input_box('_thumbnail_id', array(), 'file') ?>
                    <?php echo fed_get_input_details(array(
                        'input_meta' => '_thumbnail_id',
                        'input_type' => 'file',
                    )) ?>
                </div>
            </div>
            <?php
        }
        /**
         * Comment Status
         */
        if ( ! isset($post_settings['dashboard']['allow_comments'])) {
            ?>
            <div class="row fed_dashboard_item_field">
                <div class="col-md-12">
                    <div class="fed_header_font_color"><?php _e('Allow Comments') ?></div>
                    <?php echo fed_input_box('comment_status', array(
                        'default_value' => 'open',
                        'value'         => 'open',
                    ), 'checkbox'); ?>

                    <?php echo fed_get_input_details(array(
                        'input_meta'    => 'comment_status',
                        'input_type'    => 'checkbox',
                        'default_value' => 'open',
                        'user_value'    => 'open',
                    )); ?>
                </div>
            </div>
            <?php
        }
        /**
         * Extra Fields
         */
        foreach ($post_table as $item) {
            if ($post_type === $item['post_type']) {
                ?>
                <div class="row fed_dashboard_item_field">
                    <div class="col-md-12">
                        <div class="fed_header_font_color"><?php _e($item['label_name']); ?></div>
                        ' . fed_get_input_details( $item ) . '
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <div class="row fed_dashboard_item_field">
            <div class="col-md-3 col-md-offset-4">
                <button class="btn btn-primary"
                        type="submit">
                    <i class="fa fa-floppy-o"></i>
                    <?php _e('Save', 'frontend-dashboard') ?>
                </button>
            </div>
        </div>
    </form>
    <?php
}


/**
 * Process Add New Post
 *
 * @param  array  $post  Post.
 *
 * @return WP_Error | array
 */
function fed_process_dashboard_add_new_post($post)
{
    /**
     * Validate Current user role can add new post type.
     */
    $fed_admin_options = fed_get_post_settings_by_type($post['fed_post_type']);

    $user_role = fed_get_current_user_role();
    if (count(array_intersect($user_role, array_keys($fed_admin_options['permissions']['post_permission']))) > 0) {
        $extras      = fed_fetch_table_rows_with_key(BC_FED_TABLE_POST, 'input_meta');
        $post_status = isset($fed_admin_options['settings']['fed_post_status']) ? sanitize_text_field($fed_admin_options['settings']['fed_post_status']) : 'publish';

        if (empty($post['post_title'])) {
            $error = new WP_Error('fed_dashboard_add_post_title_missing',
                __('Please fill post title', 'frontend-dashboard'));
            wp_send_json_error(array('message' => $error->get_error_messages()));
        }

        $default = array(
            'post_title'     => sanitize_text_field($post['post_title']),
            'post_content'   => isset($post['post_content']) ? wp_kses_post($post['post_content']) : '',
            'post_category'  => isset($post['post_category']) ? sanitize_text_field($post['post_category']) : '',
            'tags_input'     => isset($post['tags_input']) ? implode(',', $post['tags_input']) : '',
            'post_type'      => isset($post['post_type']) ? sanitize_text_field($post['post_type']) : 'post',
            'comment_status' => isset($post['comment_status']) ? sanitize_text_field($post['comment_status']) : 'open',
            'post_status'    => $post_status,
        );

        if (isset($post['ID'])) {
            $default['ID'] = (int) $post['ID'];
        }

        if (isset($post['_thumbnail_id'])) {
            $default['_thumbnail_id'] = $post['_thumbnail_id'] == '' ? -1 : (int) $post['_thumbnail_id'];
        }

//        if ( isset( $post['post_format'] ) ) {
//            $default['post_format'] = sanitize_text_field( $post['post_format'] );
//        }
        if (isset($post['tax_input'])) {
            $default['tax_input'] = $post['tax_input'];
        }

        foreach ($extras as $index => $extra) {
            $default['meta_input'][$index] = isset($post[$index]) ? sanitize_text_field($post[$index]) : '';
        }

        $success = wp_insert_post($default);

        if ($success instanceof WP_Error) {
            wp_send_json_error($success->get_error_messages());
        }

        wp_send_json_success(array('message' => $post['post_title'].__(' Successfully Saved', 'frontend-dashboard')));
    }
    $error = new WP_Error('fed_action_not_allowed',
        __('Sorry! your are not allowed to do this action', 'frontend-dashboard'));

    wp_send_json_error(array('message' => $error->get_error_messages()));
}

/**
 * Display Edit Post by ID
 *
 * @param  array  $post  Post Values.
 *
 * @return string
 */
function fed_display_dashboard_edit_post_by_id($post)
{
    $post_table    = fed_fetch_rows_by_table(BC_FED_TABLE_POST);
    $post_meta     = get_post_meta($post->ID);
    $post_settings = fed_get_post_settings_by_type($post->post_type);

    $html = '';
    $html .= '
<div class="row">
	<div class="col-md-5">
		<form method="post"
			  class="fed_dashboard_show_post_list_request"
			  action=" '.admin_url('admin-ajax.php?action=fed_dashboard_show_post_list_request').'">';
    $html .= fed_wp_nonce_field('fed_dashboard_show_post_list_request', 'fed_dashboard_show_post_list_request', '',
        false);

    $html .= fed_get_input_details(array(
        'input_type' => 'hidden',
        'input_meta' => 'fed_post_type',
        'user_value' => $post->post_type,
    ));

    $html .= '
			<button class="btn btn-primary"
					type="submit">
				<i class="fa fa-mail-reply"></i>
				Back to '.strtoupper($post->post_type).'
			</button>
		</form>
	</div>
</div>';

    $html .= '
<form method="post"
	  class="fed_dashboard_process_edit_post_request"
	  action="'.admin_url('admin-ajax.php?action=fed_dashboard_process_edit_post_request').'">';

    $html .= fed_wp_nonce_field("fed_dashboard_process_edit_post_request", "fed_dashboard_process_edit_post_request",
        true,
        false);

    $html .= fed_input_box('ID', array('value' => (int) $post->ID), 'hidden');

    $html .= '
	<input type="hidden"
		   name="fed_post_type"
		   value="'.$post->post_type.'">
	';

    $html .= '
	<input type="hidden"
		   name="post_type"
		   value="'.$post->post_type.'">
	';
    /**
     * Post Title
     */
    $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-12">
			<div class="fed_header_font_color">'.__('Title').'</div>
			'.fed_input_box('post_title', array(
            'value'       => esc_attr($post->post_title),
            'placeholder' => 'Post Title',
        ), 'single_line').'
		</div>

	</div>
	';
    /**
     * Post Content
     */
    if ( ! isset($post_settings['dashboard']['post_content'])) {
        $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-12">
			<div class="fed_header_font_color">'.__('Content').'</div>
			'.fed_get_wp_editor($post->post_content, 'post_content', array(
                'quicktags' => true,
            )).'
		</div>

	</div>
	';
    }
    $html .= fed_show_category_tag_post_format($post, $post_settings);

    /**
     * Featured Image
     * _thumbnail_id
     */
    if ( ! isset($post_settings['dashboard']['featured_image'])) {
        $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-12">
			<div class="fed_header_font_color">'.__('Featured Image').'</div>
			'.fed_input_box('_thumbnail_id', array('value' => (int) $post_meta['_thumbnail_id'][0]), 'file').
                 '
		</div>
	</div>
	';
    }

    /**
     * Comment Status
     */
    if ( ! isset($post_settings['dashboard']['allow_comments'])) {
        $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-12">
			<div class="fed_header_font_color">'.__('Allow Comments').'</div>
			'.fed_input_box('comment_status', array(
                'default_value' => 'open',
                'value'         => esc_attr($post->comment_status),
            ), 'checkbox').'
		</div>
	</div>
	';
    }
    /**
     * Extra Fields
     */
    foreach ($post_table as $item) {
        $temp               = $item;
        $temp['user_value'] = $post_meta[$item['input_meta']][0];
        if ($post->post_type === $item['post_type']) {
            $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-9">
			<div class="fed_header_font_color">'.__($item['label_name']).'</div>
			'.fed_get_input_details($temp).'
		</div>
	</div>
	';
        }
    }
    $html .= '
	<div class="row fed_dashboard_item_field">
		<div class="col-md-3 col-md-offset-4">
			<button class="btn btn-primary"
					type="submit">
				<i class="fa fa-floppy-o"></i>
				'.__('Save', 'frontend-dashboard').'
			</button>
		</div>
	</div>
	';

    $html .= '
</form>';

    return $html;
}

/**
 * @param $post_type
 *
 * @return mixed|void
 */
function fed_get_post_settings_by_type($post_type)
{

    return apply_filters('fed_get_custom_post_settings_by_type', array(), $post_type);
}


/**
 * @param $post
 * @param $post_settings
 */
function fed_show_category_tag_post_format($post, $post_settings)
{
    $post_type = is_object($post) ? $post->post_type : $post;
    $ctps      = fed_get_category_tag_post_format($post_type);
    $user_role = fed_get_current_user_role_key();

    foreach ($ctps as $index => $ctp) {
        if ($index === 'category') {
            foreach ($ctp as $cindex => $category) {
                if ( ! isset($post_settings['taxonomies'][$cindex][$user_role])) {
                    ?>
                    <div class="row fed_dashboard_item_field">
                        <div class="col-md-12">
                            <div class="fed_header_font_color">
                                <?php echo $category->label ?>
                                <?php  do_action('fed_frontend_dashboard_edit_tag_label', $category, $post) ?>
                            </div>
                            <?php echo fed_get_dashboard_display_categories($post, $category); ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        if ($index === 'tag') {
            foreach ($ctp as $tindex => $tag) {
                if ( ! isset($post_settings['taxonomies'][$tindex][$user_role])) {
                    ?>
                    <div class="row fed_dashboard_item_field">
                        <div class="col-md-12">
                            <div class="fed_header_font_color">
                                <?php echo $tag->label; ?>
                                <?php  do_action('fed_frontend_dashboard_edit_tag_label', $tag, $post) ?>
                            </div>
                            <?php echo fed_get_dashboard_display_tags($post, $tag); ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
        if ($index === 'post_format') {
            if ( ! isset($post_settings['taxonomies']['post_format'][$user_role])) {
                $post_format = fed_dashboard_get_post_format();
                if (is_array($post_format)) {
                    $post_format = array_combine($post_format, $post_format);
                    ?>
                    <div class="row fed_dashboard_item_field">
                        <div class="col-md-12">
                            <div class="fed_header_font_color"><?php _e('Post Format'); ?></div>
                            <?php echo fed_input_box('tax_input[post_format][]', array(
                                'options' => $post_format,
                                'value'   => esc_attr(get_post_format($post->ID)) ?: 'standard',
                            ), 'radio'); ?>
                        </div>
                    </div>
                    <?php
                }
            }
        }
    }
}