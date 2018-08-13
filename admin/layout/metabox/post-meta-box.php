<?php
/**
 * Post Meta Boxes
 */

add_action( 'admin_init', 'fed_add_meta_boxes', 1 );
/**
 * Add Post Meta Boxes
 */
function fed_add_meta_boxes() {
	add_meta_box( 'fed_meta_boxes', esc_html__( 'Frontend Dashboard Custom Fields', 'frontend-dashboard' ), 'fed_add_meta_boxes_display', array_keys( fed_get_public_post_types() ), 'normal', 'high' );
}

/**
 * Show Custom Post Meta on Admin Post Page.
 */
function fed_add_meta_boxes_display() {
	fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' );

	$extra_fields = fed_fetch_table_rows_with_key( BC_FED_POST_DB, 'input_meta' );
	global $post;
	$post_meta = fed_get_all_post_meta_key( $post->ID );
	?>
	<div class="bc_fed">
		<?php
		foreach ( $extra_fields as $item ) {
			$temp               = $item;
			$temp['user_value'] = isset( $post_meta[ $item['input_meta'] ] ) ? $post_meta[ $item['input_meta'] ]['meta_value'] : '';
			$temp['input_meta'] = 'fed_meta[' . $item['input_meta'] . ']';

			if ( fed_get_current_screen_id() === $item['post_type'] ) {
				?>
				<div class="row fed_dashboard_item_field p-b-20">
					<div class="col-md-6">
						<div class="fed_header_font_color"><?php esc_attr_e( $temp['label_name'], 'fed' ); ?></div>
						<?php echo fed_get_input_details( $temp ); ?>
					</div>
				</div>
				<?php
			}
		}
		?>
	</div>
	<?php

}

add_action( 'save_post', 'fed_save_meta_boxes_display', 10, 2 );

function fed_save_meta_boxes_display( $post_id, $post ) {
	/* Verify the nonce before proceeding */
	if ( ! isset( $_POST['fed_nonce'], $_POST['fed_meta'] ) || ! wp_verify_nonce( $_POST['fed_nonce'], 'fed_nonce' ) ) {
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
	if ( count( $post_meta ) > 0 ) {
		foreach ( $post_meta as $index => $extra ) {
			if ( isset( $_POST['fed_meta'] ) ) {
				if ( array_key_exists( $index, $_POST['fed_meta'] ) ) {
					$meta_value = isset( $_POST['fed_meta'][ $index ] ) ? sanitize_text_field( $_POST['fed_meta'][ $index ] ) : '';
					update_post_meta( $post_id, $index, $meta_value );
				} else {
					/**
					 * Delete the unwanted post metas
					 */
					delete_post_meta( $post_id, $index );
				}
			}
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