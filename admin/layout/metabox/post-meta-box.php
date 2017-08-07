<?php
/**
 * Post Meta Boxes
 */

add_action( 'admin_init', 'fed_add_meta_boxes', 1 );
/**
 * Add Post Meta Boxes
 */
function fed_add_meta_boxes() {
	add_meta_box( 'fed_meta_boxes', esc_html__( 'Front-end Dashboard Custom Fields' ), 'fed_add_meta_boxes_display', array_keys( fed_get_public_post_types() ), 'normal', 'high' );
}

/**
 * Show Custom Post Meta on Admin Post Page.
 */
function fed_add_meta_boxes_display() {
	wp_nonce_field( 'fed_add_meta_boxes_display', 'fed_add_meta_boxes_display' );

	$extra_fields = fed_fetch_table_rows_with_key( BC_FED_POST_DB, 'input_meta' );
	global $post;
	$post_meta = fed_get_all_post_meta_key( $post->ID );

	foreach ( $extra_fields as $item ) {
		$temp               = $item;
		$temp['user_value'] = isset( $post_meta[ $item['input_meta'] ] ) ? $post_meta[ $item['input_meta'] ]['meta_value'] : '';
		$temp['input_meta'] = 'fed_meta[' . $item['input_meta'] . ']';

		if ( fed_get_current_screen_id() === $item['post_type'] ) {
			echo '<div class="row fed_dashboard_item_field">
                    <div class="col-md-3">
                        <div class="pull-right fed_header_font_color">' . __( $temp['label_name'] ) . '</div>
                    </div>
                    <div class="col-md-9">
                    ' . fed_get_input_details( $temp ) . '
                    </div>
              </div>
             
              ';
		}
	}
	?>
	<?php

}

add_action( 'save_post', 'fed_save_meta_boxes_display', 10, 2 );

function fed_save_meta_boxes_display( $post_id, $post ) {
	/* Verify the nonce before proceeding */
	if ( ! isset( $_POST['fed_add_meta_boxes_display'] ) || ! wp_verify_nonce( $_POST['fed_add_meta_boxes_display'], 'fed_add_meta_boxes_display' ) ) {
		return $post_id;
	}
	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	/**
	 * Get all post meta key
	 */
	$post_meta = fed_fetch_table_rows_with_key( BC_FED_POST_DB, 'input_meta' );

	/**
	 * Check with post meta to save the meta
	 */
	foreach ( $_POST['fed_meta'] as $key => $meta ) {
		if ( in_array( $key, array_keys( $post_meta ) ) ) {
			$meta_value = isset( $_POST['fed_meta'][ $key ] ) ? esc_attr( $_POST['fed_meta'][ $key ] ) : '';
			update_post_meta( $post_id, $key, $meta_value );
		} else {
			/**
			 * Delete the unwanted post metas
			 */
			delete_post_meta( $post_id, $key );
		}
	}
}

/**
 * Remove Post Custom Meta Default Fields
 */
function fed_remove_post_custom_fields() {
	remove_meta_box( 'postcustom', 'post', 'normal' );
}

add_action( 'admin_menu', 'fed_remove_post_custom_fields' );