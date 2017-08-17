<?php

/**
 * Default Post Options
 *
 * @return array
 */

/**
 * Process Dashboard Display Post
 *
 * @return array
 */
function fed_process_dashboard_display_post() {
	$user       = get_userdata( get_current_user_id() );
	$paged      = isset( $_REQUEST['question_id'] ) ? absint( $_REQUEST['question_id'] ) : 1;
	$args       = array(
		'author'         => $user->ID,
		'orderby'        => 'post_date',
		'order'          => 'ASC',
		'posts_per_page' => get_option( 'posts_per_page', 10 ),
		'paged'          => $paged,
		'post_status'    => array( 'publish', 'pending' ),
	);
	$query_post = new WP_Query( $args );

//	var_dump($query_post);
	return array( 'post' => $query_post );
}

/**
 * Display Dashboard Post
 *
 * @param string $first_element First Element.
 */
function fed_display_dashboard_post( $first_element ) {
	$fed_posts = fed_process_dashboard_display_post();

	$menus = fed_get_post_menu();
	foreach ( $fed_posts as $index => $post ) {
		if ( $index == $first_element ) {
			$active = '';
		} else {
			$active = 'hide';
		}
		?>
		<div class="panel panel-primary fed_dashboard_item <?php echo $active . ' ' . $index ?>">
			<div class="panel-heading">
				<h3 class="panel-title">
					<span class="fa <?php echo $menus[ $index ]['menu_image_id'] ?>"></span>
					<?php echo ucwords( $menus[ $index ]['menu'] ) ?>
				</h3>
			</div>
			<div class="panel-body fed_dashboard_panel_body">
				<div class="fed_panel_body_container">
					<?php
					echo fed_display_dashboard_view_post_list();
					?>

				</div>
			</div>
		</div>
	<?php }
}

/**
 * Post Pagination.
 *
 * @param WP_Post $post_object Post
 *
 * @return string
 */
function fed_get_post_pagination( $post_object ) {
	$pagination_counts = ceil( $post_object->found_posts / get_option( 'posts_per_page', 10 ) );
	$html              = '';
	$current_page      = isset( $_REQUEST['question_id'] ) ? absint( $_REQUEST['question_id'] ) : 1;

	if ( $pagination_counts > 1 ) {
		$html .= '<ul class="pagination pagination-small fed_post_pagination" data-href="' . admin_url( 'admin-ajax.php?type=pagination&action=fed_dashboard_show_post_list_request&fed_dashboard_show_post_list_request=' . wp_create_nonce( "fed_dashboard_show_post_list_request" ) ) . '">';

		for ( $i = 1; $i <= $pagination_counts; $i ++ ) {
			if ( $current_page == $i ) {
				$class = 'class="active"';
			} else {
				$class = '';
			}
			$html .= '<li ' . $class . ' data-id="' . $i . '"><span>' . $i . '</span></li>';
		}
	}

	return $html;
}

/**
 * Display dashboard Add New Post
 *
 * @return string
 */
function fed_display_dashboard_add_new_post( $post ) {
	$post_table    = fed_fetch_rows_by_table( BC_FED_POST_DB );
	$post_format   = fed_dashboard_get_post_format();
	$post_settings = get_option( 'fed_admin_settings_post' );

	$html = '';
	$html .= '
<div class="row">
    <div class="col-md-5">
        <form method="post"
              class="fed_dashboard_show_post_list_request"
              action=" ' . admin_url( 'admin-ajax.php?action=fed_dashboard_show_post_list_request' ) . '">';
	$html .= wp_nonce_field( 'fed_dashboard_show_post_list_request', 'fed_dashboard_show_post_list_request', '',
		false );

	$html .= '
            <button class="btn btn-primary"
                    type="submit">
                <i class="fa fa-mail-reply"></i> Back to Post List
            </button>
        </form>
    </div>
</div>';

	$html .= '
<form method="post"
      class="fed_dashboard_add_new_post"
      action="' . admin_url( 'admin-ajax.php?action=fed_dashboard_add_new_post' ) . '">';

	$html .= wp_nonce_field( "fed_dashboard_add_new_post", "fed_dashboard_add_new_post", true, false );

	$html .= '<input type="hidden"
                     name="fed_post_type"
                     value="add_new_post">';
	/**
	 * Post Title
	 */
	$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-12">
        <div class="fed_header_font_color">' . __( 'Post Title' ) . '</div>
            ' . fed_input_box( 'post_title', array( 'placeholder' => 'Post Title' ), 'single_line' ) . '
        </div>

    </div>
    ';
	/**
	 * Post Content
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_content'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-12">
        <div class="fed_header_font_color">' . __( 'Post Content' ) . '</div>
            ' . fed_get_wp_editor( '', 'post_content', array( 'quicktags' => true ) ) . '
        </div>

    </div>
    ';
	}
	/**
	 * Categories
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_category'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Category' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_dashboard_display_categories() . '
        </div>
    </div>
    ';
	}
	/**
	 * Tags
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_tag'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Tag' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_dashboard_display_tags() . '
        </div>
    </div>
    ';
	}
	/**
	 * Featured Image
	 * _thumbnail_id
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_featured_image'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">
                ' . __( 'Featured Image' ) . '
            </div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( '_thumbnail_id', array(), 'file' ) . '
        </div>
    </div>
    ';
	}

	/**
	 * Post Format is available based on the Theme.
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_post_format'] != 'Enable' ) {
		if ( is_array( $post_format ) ) {
			$post_format = array_combine( $post_format, $post_format );
			$html        .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Post Format' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( 'post_format', array(
					'options' => $post_format,
				), 'radio' ) . '
        </div>
    </div>
    ';
		}
	}

	/**
	 * Comment Status
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_allow_comments'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Allow Comments' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( 'comment_status', array(
				'default_value' => 'open',
				'value'         => 'open'
			), 'checkbox' ) . '
        </div>
    </div>
    ';
	}
	/**
	 * Extra Fields
	 */
	foreach ( $post_table as $item ) {
		if ( $post['post_type'] === $item['post_type'] ) {
			$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( $item['label_name'] ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_input_details( $item ) . '
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
                Save
            </button>
        </div>
    </div>
    ';

	$html .= '
</form>';

	return $html;

}

/**
 * Display Dashboard View Post List
 *
 */
function fed_display_dashboard_view_post_list() {
	$html      = '';
	$fed_posts = fed_process_dashboard_display_post();
	foreach ( $fed_posts as $index => $post ) {
		$html .= '
<div class="fed_dashboard_post_menu_container">
    <div class="fed_dashboard_post_menu_add_post">
        <form method="post"
              class="fed_dashboard_add_new_post_request"
              action="' . admin_url( 'admin-ajax.php?action=fed_dashboard_add_new_post_request' ) . '">
            ' . wp_nonce_field( 'fed_dashboard_add_new_post_request', 'fed_dashboard_add_new_post_request', true, false
			) . '
            <input type="hidden" name="post_type" value="post" />
            
            <button class="btn btn-primary"
                    type="submit">
                <i class="fa fa-plus"></i>
                Add New Post
            </button>
        </form>
    </div>
</div>';

		$html .= '
<div class="fed_dashboard_item_field_container">';
		foreach ( $post->get_posts() as $single_post ) {
			$html .= '
    <div class="fed_dashboard_item_field_wrapper">
        <div class="row fed_dashboard_item_field">
            <div class="col-md-1"> ' . (int) $single_post->ID . '</div>
            <div class="col-md-9">
                ' . fed_get_post_status_symbol( $single_post->post_status ) . ' ' . esc_attr( $single_post->post_title ) . '
            </div>
            <div class="col-md-2 flex-space-around">
                <div>
                    <form method="post"
                          class="fed_dashboard_edit_post_by_id"
                          action=" ' . admin_url( 'admin-ajax.php?action=fed_dashboard_edit_post_by_id' ) . '">
                        ' . wp_nonce_field( 'fed_dashboard_edit_post_by_id', 'fed_dashboard_edit_post_by_id', '', false
				) . '
                        <input type="hidden"
                               name="post_id"
                               value="' . (int) $single_post->ID . '"/>
                        <button class="btn btn-primary"
                                type="submit">
                            <i class="fa fa-pencil"></i>
                        </button>
                    </form>
                </div>
                <div>
                    <form method="post"
                          class="fed_dashboard_delete_post_by_id"
                          action=" ' . admin_url( 'admin-ajax.php?action=fed_dashboard_delete_post_by_id' ) . '">
                        ' . wp_nonce_field( 'fed_dashboard_delete_post_by_id', 'fed_dashboard_delete_post_by_id', '',
					false ) . '
                        <input type="hidden"
                               name="post_id"
                               value="' . (int) $single_post->ID . '"/>
                        <button class="btn btn-danger"
                                type="submit">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    ';
		}
		$html .= fed_get_post_pagination( $fed_posts['post'] );
		$html .= '</div>';
	}

	return $html;
}

/**
 * Process Add New Post
 *
 * @param array $post Post.
 *
 * @return WP_Error | array
 */
function fed_process_dashboard_add_new_post( $post ) {

	$extras            = fed_fetch_table_rows_with_key( BC_FED_POST_DB, 'input_meta' );
	$fed_admin_options = get_option( 'fed_admin_settings_post' );
	$post_status       = isset( $fed_admin_options['settings']['fed_post_status'] ) ? sanitize_text_field( $fed_admin_options['settings']['fed_post_status'] ) : 'publish';

	if ( $post['post_title'] == '' ) {
		$error = new WP_Error( 'fed_dashboard_add_post_title_missing', 'Please fill post title' );

		wp_send_json_error( array( 'message' => $error->get_error_messages() ) );
		exit();
	}

	$default = array(
		'post_title'     => sanitize_text_field( $post['post_title'] ),
		'post_content'   => isset( $post['post_content'] ) ? wp_kses_post( $post['post_content'] ) : '',
		'post_category'  => isset( $post['post_category'] ) ? sanitize_text_field( $post['post_category'] ) : '',
		'tags_input'     => isset( $post['tags_input'] ) ? implode( ',', $post['tags_input'] ) : '',
		'post_type'      => isset( $post['post_type'] ) ? sanitize_text_field( $post['post_type'] ) : 'post',
		'comment_status' => isset( $post['comment_status'] ) ? sanitize_text_field( $post['comment_status'] ) : 'open',
		'post_status'    => $post_status,
	);

	if ( isset( $post['ID'] ) ) {
		$default['ID'] = (int) $post['ID'];
	}

	if ( isset( $post['_thumbnail_id'] ) ) {
		$default['_thumbnail_id'] = (int) $post['_thumbnail_id'];
	}

	if ( isset( $post['post_format'] ) ) {
		$default['post_format'] = sanitize_text_field( $post['post_format'] );
	}

	foreach ( $extras as $index => $extra ) {
		if ( isset( $post[ $index ] ) ) {
			$default['meta_input'][ $index ] = sanitize_text_field( $post[ $index ] );
		}
	}

	$success = wp_insert_post( $default );

	if ( $success instanceof WP_Error ) {
		wp_send_json_error( $success->get_error_messages() );
	}

	wp_send_json_success( array( 'message' => $post['post_title'] . __( ' Successfully saved' ) ) );
}

/**
 * Display Edit Post by ID
 *
 * @param array $post Post Values.
 *
 * @return string
 */
function fed_display_dashboard_edit_post_by_id( $post ) {
	$post_table    = fed_fetch_rows_by_table( BC_FED_POST_DB );
	$post_format   = fed_dashboard_get_post_format();
	$post_meta     = get_post_meta( $post->ID );
	$post_settings = get_option( 'fed_admin_settings_post' );

	$html = '';
	$html .= '
<div class="row">
    <div class="col-md-5">
        <form method="post"
              class="fed_dashboard_show_post_list_request"
              action=" ' . admin_url( 'admin-ajax.php?action=fed_dashboard_show_post_list_request' ) . '">';
	$html .= wp_nonce_field( 'fed_dashboard_show_post_list_request', 'fed_dashboard_show_post_list_request', '',
		false );

	$html .= '
            <button class="btn btn-primary"
                    type="submit">
                <i class="fa fa-mail-reply"></i> Back to Post List
            </button>
        </form>
    </div>
</div>';

	$html .= '
<form method="post"
      class="fed_dashboard_process_edit_post_request"
      action="' . admin_url( 'admin-ajax.php?action=fed_dashboard_process_edit_post_request' ) . '">';

	$html .= wp_nonce_field( "fed_dashboard_process_edit_post_request", "fed_dashboard_process_edit_post_request", true,
		false );

	$html .= fed_input_box( 'ID', array( 'value' => (int) $post->ID ), 'hidden' );

	$html .= '<input type="hidden"
                     name="fed_post_type"
                     value="add_new_post">';
	/**
	 * Post Title
	 */
	$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-12">
        <div class="fed_header_font_color">' . __( 'Post Title' ) . '</div>
            ' . fed_input_box( 'post_title', array(
			'value'       => esc_attr( $post->post_title ),
			'placeholder' => 'Post Title'
		), 'single_line' ) . '
        </div>

    </div>
    ';
	/**
	 * Post Content
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_content'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-12">
        <div class="fed_header_font_color">' . __( 'Post Content' ) . '</div>
            ' . fed_get_wp_editor( $post->post_content, 'post_content', array(
				'quicktags' => true
			) ) . '
        </div>

    </div>
    ';
	}
	/**
	 * Categories
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_category'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Category' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_dashboard_display_categories( $post->ID ) . '
        </div>
    </div>
    ';
	}
	/**
	 * Tags
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_tag'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Tag' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_dashboard_display_tags( $post->ID ) . '
        </div>
    </div>
    ';
	}
	/**
	 * Featured Image
	 * _thumbnail_id
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_featured_image'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">
                ' . __( 'Featured Image' ) . '
            </div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( '_thumbnail_id', array( 'value' => (int) $post_meta['_thumbnail_id'][0] ), 'file' ) .
		         '
        </div>
    </div>
    ';
	}

	/**
	 * Post Format is available based on the Theme.
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_post_format'] != 'Enable' ) {
		if ( is_array( $post_format ) ) {
			$post_format = array_combine( $post_format, $post_format );
			$html        .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Post Format' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( 'post_format', array(
					'options' => $post_format,
					'value'   => esc_attr( get_post_format( $post->ID ) ) ?: 'standard'
				), 'radio' ) . '
        </div>
    </div>
    ';
		}
	}
	/**
	 * Comment Status
	 */
	if ( $post_settings['dashboard']['fed_post_dashboard_allow_comments'] != 'Enable' ) {
		$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( 'Allow Comments' ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_input_box( 'comment_status', array(
				'default_value' => 'open',
				'value'         => esc_attr( $post->comment_status ),
			), 'checkbox' ) . '
        </div>
    </div>
    ';
	}
	/**
	 * Extra Fields
	 */
	foreach ( $post_table as $item ) {
		$temp               = $item;
		$temp['user_value'] = $post_meta[ $item['input_meta'] ][0];
		if ( $post->post_type === $item['post_type'] ) {
			$html .= '
    <div class="row fed_dashboard_item_field">
        <div class="col-md-3">
            <div class="pull-right fed_header_font_color">' . __( $item['label_name'] ) . '</div>
        </div>
        <div class="col-md-9">
            ' . fed_get_input_details( $temp ) . '
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
                Save
            </button>
        </div>
    </div>
    ';

	$html .= '
</form>';

	return $html;
}
