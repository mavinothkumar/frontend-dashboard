<?php
/**
 * Media Controller.
 *
 * @package Frontend_Dashboard
 */

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
	 * @param \FED\Core\Loader $loader The plugin loader instance.
	 */
	public function register_hooks( $loader ) {
		$loader->add_action( 'wp_ajax_fed_get_media_library', $this, 'get_media_library' );
		$loader->add_action( 'wp_ajax_fed_upload_media_file', $this, 'upload_media_file' );
		$loader->add_action( 'wp_ajax_fed_save_attachment_details', $this, 'save_attachment_details' );
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
		$mime   = isset( $_REQUEST['mime'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mime'] ) ) : '';

		$query_args = array(
			'post_type'              => 'attachment',
			'post_status'            => 'inherit',
			'posts_per_page'         => $limit,
			'paged'                  => $page,
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
			'no_found_rows'          => false,
		);

		if ( ! empty( $mime ) && 'all' !== $mime ) {
			if ( 'document' === $mime ) {
				$query_args['post_mime_type'] = array(
					'application/pdf',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'application/vnd.ms-excel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'application/vnd.ms-powerpoint',
					'application/vnd.openxmlformats-officedocument.presentationml.presentation',
					'text/plain',
					'text/csv',
					'application/zip',
					'application/x-zip-compressed',
					'application/x-rar-compressed',
					'application/x-tar',
					'application/gzip',
				);
			} elseif ( 'image' === $mime ) {
				$query_args['post_mime_type'] = array(
					'image/jpeg',
					'image/png',
					'image/gif',
					'image/webp',
					'image/svg+xml',
					'image/bmp',
					'image/tiff',
					'image/x-icon',
				);
			} elseif ( 'audio' === $mime ) {
				$query_args['post_mime_type'] = array(
					'audio/mpeg',
					'audio/wav',
					'audio/ogg',
					'audio/midi',
					'audio/x-ms-wma',
					'audio/x-m4a',
					'audio/mp3',
					'audio/aac',
					'audio/flac',
				);
			} elseif ( 'video' === $mime ) {
				$query_args['post_mime_type'] = array(
					'video/mp4',
					'video/webm',
					'video/ogg',
					'video/quicktime',
					'video/x-msvideo',
					'video/x-flv',
					'video/x-matroska',
				);
			} else {
				$query_args['post_mime_type'] = $mime;
			}
		}

		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		// Restrict authors/subscribers to their own uploads unless admin or editor.
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			$query_args['author'] = get_current_user_id();
		}

		$query = new \WP_Query( $query_args );
		$items = array();

		foreach ( $query->posts as $post ) {
			$items[] = $this->format_attachment( $post->ID );
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
	 * Save updated attachment details (alt text, title, caption, description).
	 */
	public function save_attachment_details() {
		check_ajax_referer( 'fed_nonce', 'fed_nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'frontend-dashboard' ) ), 403 );
		}

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! $id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid attachment ID.', 'frontend-dashboard' ) ), 400 );
		}

		if ( ! current_user_can( 'edit_post', $id ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to edit this attachment.', 'frontend-dashboard' ) ), 403 );
		}

		if ( isset( $_POST['alt'] ) ) {
			$alt = sanitize_text_field( wp_unslash( $_POST['alt'] ) );
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		}

		$update_args  = array( 'ID' => $id );
		$needs_update = false;

		if ( isset( $_POST['title'] ) ) {
			$update_args['post_title'] = sanitize_text_field( wp_unslash( $_POST['title'] ) );
			$needs_update              = true;
		}

		if ( isset( $_POST['caption'] ) ) {
			$update_args['post_excerpt'] = wp_kses_post( wp_unslash( $_POST['caption'] ) );
			$needs_update                = true;
		}

		if ( isset( $_POST['description'] ) ) {
			$update_args['post_content'] = wp_kses_post( wp_unslash( $_POST['description'] ) );
			$needs_update                = true;
		}

		if ( $needs_update ) {
			wp_update_post( $update_args );
		}

		wp_send_json_success( $this->format_attachment( $id ) );
	}

	/**
	 * Format an attachment post into a structured array for JSON responses.
	 *
	 * @param int $id Attachment ID.
	 * @return array
	 */
	public function format_attachment( $id ) {
		$meta      = wp_get_attachment_metadata( $id );
		$is_image  = wp_attachment_is_image( $id );
		$thumb_url = wp_get_attachment_image_url( $id, 'thumbnail' );
		$med_url   = wp_get_attachment_image_url( $id, 'medium' );
		$large_url = wp_get_attachment_image_url( $id, 'large' );
		$full_url  = wp_get_attachment_url( $id );
		$icon_url  = wp_mime_type_icon( $id );

		$file_path = get_attached_file( $id );
		$post_obj  = get_post( $id );
		$the_title = $post_obj ? $post_obj->post_title : '';
		$filename  = $file_path ? basename( $file_path ) : $the_title;
		$title     = ! empty( $the_title ) ? $the_title : $filename;

		$filesize = '';
		if ( ! empty( $meta['filesize'] ) ) {
			$filesize = size_format( (int) $meta['filesize'] );
		} elseif ( $file_path && file_exists( $file_path ) && is_readable( $file_path ) ) {
			$size     = filesize( $file_path );
			$filesize = false !== $size ? size_format( $size ) : '';
		}

		$mime = get_post_mime_type( $id );
		$mime = ! empty( $mime ) ? $mime : '';
		$ext  = $file_path ? pathinfo( $file_path, PATHINFO_EXTENSION ) : '';

		$fallback_image = $is_image ? $full_url : '';
		$thumbnail      = ! empty( $thumb_url ) ? $thumb_url : $fallback_image;
		$medium         = ! empty( $med_url ) ? $med_url : $fallback_image;
		$large          = ! empty( $large_url ) ? $large_url : $fallback_image;
		$full           = ! empty( $full_url ) ? $full_url : '';
		$icon           = ! empty( $icon_url ) ? $icon_url : '';
		$alt_text       = get_post_meta( $id, '_wp_attachment_image_alt', true );
		$alt            = ! empty( $alt_text ) ? $alt_text : $title;

		$caption     = $post_obj ? $post_obj->post_excerpt : '';
		$description = $post_obj ? $post_obj->post_content : '';
		$permalink   = get_attachment_link( $id );

		$width  = ! empty( $meta['width'] ) ? (int) $meta['width'] : '';
		$height = ! empty( $meta['height'] ) ? (int) $meta['height'] : '';
		$dims   = ( $is_image && $width && $height ) ? "{$width} × {$height}" : '';

		return array(
			'id'          => (int) $id,
			'title'       => html_entity_decode( $title, ENT_QUOTES, 'UTF-8' ),
			'filename'    => html_entity_decode( $filename, ENT_QUOTES, 'UTF-8' ),
			'alt'         => html_entity_decode( $alt, ENT_QUOTES, 'UTF-8' ),
			'caption'     => html_entity_decode( $caption, ENT_QUOTES, 'UTF-8' ),
			'description' => html_entity_decode( $description, ENT_QUOTES, 'UTF-8' ),
			'url'         => $full,
			'link'        => $permalink ? $permalink : $full,
			'thumbnail'   => $thumbnail,
			'medium'      => $medium,
			'large'       => $large,
			'full'        => $full,
			'is_image'    => (bool) $is_image,
			'icon'        => $icon,
			'mime'        => $mime,
			'ext'         => strtoupper( $ext ),
			'width'       => $width,
			'height'      => $height,
			'dimensions'  => $dims,
			'filesize'    => $filesize,
			'date'        => get_the_date( 'M j, Y', $id ),
		);
	}

	/**
	 * Handle file upload to WordPress Media Library.
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

		wp_send_json_success( $this->format_attachment( $attachment_id ) );
	}
}
