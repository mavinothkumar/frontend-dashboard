<?php

namespace FED\Controllers\Media;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MediaController
 *
 * Provides dedicated, secure AJAX endpoints for the Frontend Dashboard Media Modal.
 */
class MediaController {

	/**
	 * Register actions with plugin loader.
	 *
	 * @param \FED\Core\Loader $loader
	 */
	public function register_hooks( $loader ) {
		$loader->add_action( 'wp_ajax_fed_get_media_library', $this, 'get_media_library' );
		$loader->add_action( 'wp_ajax_fed_upload_media_file', $this, 'upload_media_file' );
	}

	/**
	 * Fetch WordPress media items for the current user.
	 */
	public function get_media_library() {
		check_ajax_referer( 'fed_nonce', 'fed_nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to view media.', 'frontend-dashboard' ) ), 403 );
		}

		$search = isset( $_REQUEST['search'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search'] ) ) : '';
		$page   = isset( $_REQUEST['page'] ) ? max( 1, (int) $_REQUEST['page'] ) : 1;
		$limit  = isset( $_REQUEST['limit'] ) ? max( 1, min( 60, (int) $_REQUEST['limit'] ) ) : 36;
		$mime   = isset( $_REQUEST['mime'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mime'] ) ) : 'image';

		$query_args = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => $limit,
			'paged'          => $page,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( 'image' === $mime || empty( $mime ) ) {
			$query_args['post_mime_type'] = 'image';
		}

		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		// Restrict authors/subscribers to their own uploads unless admin or editor
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			$query_args['author'] = get_current_user_id();
		}

		$query = new \WP_Query( $query_args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$id        = $post->ID;
			$meta      = wp_get_attachment_metadata( $id );
			$thumb_url = wp_get_attachment_image_url( $id, 'thumbnail' );
			$med_url   = wp_get_attachment_image_url( $id, 'medium' );
			$large_url = wp_get_attachment_image_url( $id, 'large' );
			$full_url  = wp_get_attachment_url( $id );

			$file_path = get_attached_file( $id );
			$filename  = $file_path ? basename( $file_path ) : get_the_title( $id );
			$filesize  = ( $file_path && file_exists( $file_path ) ) ? size_format( filesize( $file_path ) ) : '';

			$items[] = array(
				'id'        => $id,
				'title'     => get_the_title( $id ) ?: $filename,
				'filename'  => $filename,
				'url'       => $full_url ?: '',
				'thumbnail' => $thumb_url ?: $full_url,
				'medium'    => $med_url ?: $full_url,
				'large'     => $large_url ?: $full_url,
				'full'      => $full_url ?: '',
				'width'     => ! empty( $meta['width'] ) ? $meta['width'] : '',
				'height'    => ! empty( $meta['height'] ) ? $meta['height'] : '',
				'filesize'  => $filesize,
				'mime'      => get_post_mime_type( $id ),
				'date'      => get_the_date( 'M j, Y', $id ),
			);
		}

		wp_send_json_success(
			array(
				'items'       => $items,
				'total'       => (int) $query->found_posts,
				'total_pages' => (int) $query->max_num_pages,
				'page'        => $page,
			)
		);
	}

	/**
	 * Handle image file upload to WordPress Media Library.
	 */
	public function upload_media_file() {
		check_ajax_referer( 'fed_nonce', 'fed_nonce' );

		if ( ! is_user_logged_in() || ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to upload files.', 'frontend-dashboard' ) ), 403 );
		}

		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No file was provided.', 'frontend-dashboard' ) ), 400 );
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'file', 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( array( 'message' => $attachment_id->get_error_message() ), 400 );
		}

		$meta      = wp_get_attachment_metadata( $attachment_id );
		$thumb_url = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );
		$med_url   = wp_get_attachment_image_url( $attachment_id, 'medium' );
		$large_url = wp_get_attachment_image_url( $attachment_id, 'large' );
		$full_url  = wp_get_attachment_url( $attachment_id );

		$file_path = get_attached_file( $attachment_id );
		$filename  = $file_path ? basename( $file_path ) : get_the_title( $attachment_id );
		$filesize  = ( $file_path && file_exists( $file_path ) ) ? size_format( filesize( $file_path ) ) : '';

		wp_send_json_success(
			array(
				'id'        => $attachment_id,
				'title'     => get_the_title( $attachment_id ) ?: $filename,
				'filename'  => $filename,
				'url'       => $full_url ?: '',
				'thumbnail' => $thumb_url ?: $full_url,
				'medium'    => $med_url ?: $full_url,
				'large'     => $large_url ?: $full_url,
				'full'      => $full_url ?: '',
				'width'     => ! empty( $meta['width'] ) ? $meta['width'] : '',
				'height'    => ! empty( $meta['height'] ) ? $meta['height'] : '',
				'filesize'  => $filesize,
				'mime'      => get_post_mime_type( $attachment_id ),
			)
		);
	}
}
