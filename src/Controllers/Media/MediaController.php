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
		$loader->add_action( 'wp_ajax_fed_send_media_to_editor', $this, 'send_media_to_editor' );
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

		$query_args = apply_filters( 'fed_media_library_query_args', $query_args );
		$query      = new \WP_Query( $query_args );
		$items      = array();

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

		// Resolve generated image previews for non-image attachments (e.g., PDF previews, documents).
		if ( empty( $thumb_url ) ) {
			if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
				$base_url = str_replace( wp_basename( $full_url ), '', $full_url );
				if ( ! empty( $meta['sizes']['thumbnail']['file'] ) ) {
					$thumb_url = esc_url_raw( $base_url . $meta['sizes']['thumbnail']['file'] );
				} elseif ( ! empty( $meta['sizes']['medium']['file'] ) ) {
					$thumb_url = esc_url_raw( $base_url . $meta['sizes']['medium']['file'] );
				} elseif ( ! empty( $meta['sizes']['full']['file'] ) ) {
					$thumb_url = esc_url_raw( $base_url . $meta['sizes']['full']['file'] );
				} else {
					$first_size = reset( $meta['sizes'] );
					if ( ! empty( $first_size['file'] ) ) {
						$thumb_url = esc_url_raw( $base_url . $first_size['file'] );
					}
				}
			}

			if ( empty( $thumb_url ) && function_exists( 'wp_prepare_attachment_for_js' ) ) {
				$js_data = wp_prepare_attachment_for_js( $id );
				if ( ! empty( $js_data['sizes']['thumbnail']['url'] ) ) {
					$thumb_url = $js_data['sizes']['thumbnail']['url'];
				} elseif ( ! empty( $js_data['sizes']['medium']['url'] ) ) {
					$thumb_url = $js_data['sizes']['medium']['url'];
				} elseif ( ! empty( $js_data['sizes']['full']['url'] ) ) {
					$thumb_url = $js_data['sizes']['full']['url'];
				} elseif ( ! empty( $js_data['image']['src'] ) && ( empty( $js_data['icon'] ) || $js_data['image']['src'] !== $js_data['icon'] ) ) {
					$thumb_url = $js_data['image']['src'];
				}
			}

			if ( empty( $thumb_url ) ) {
				$thumb_id = get_post_thumbnail_id( $id );
				if ( $thumb_id ) {
					$thumb_url = wp_get_attachment_image_url( $thumb_id, 'thumbnail' );
				}
			}
		}

		if ( empty( $med_url ) ) {
			if ( ! empty( $meta['sizes']['medium']['file'] ) ) {
				$base_url = str_replace( wp_basename( $full_url ), '', $full_url );
				$med_url  = esc_url_raw( $base_url . $meta['sizes']['medium']['file'] );
			} elseif ( ! empty( $thumb_url ) ) {
				$med_url = $thumb_url;
			}
		}

		$has_preview    = ! empty( $thumb_url );
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

		$formatted = array(
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
			'has_preview' => (bool) $has_preview,
			'icon'        => $icon,
			'mime'        => $mime,
			'ext'         => strtoupper( $ext ),
			'width'       => $width,
			'height'      => $height,
			'dimensions'  => $dims,
			'filesize'    => $filesize,
			'date'        => get_the_date( 'M j, Y', $id ),
		);

		return apply_filters( 'fed_media_format_attachment', $formatted, (int) $id );
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

		do_action( 'fed_media_file_uploaded', $attachment_id );

		wp_send_json_success( $this->format_attachment( $attachment_id ) );
	}

	/**
	 * AJAX endpoint to format and filter media snippets for insertion into the editor.
	 *
	 * Applies WordPress core 'media_send_to_editor' filter (for third-party plugins like PDF Embedder)
	 * and dedicated 'fed_media_send_to_editor' filter for custom FED integrations.
	 */
	public function send_media_to_editor() {
		check_ajax_referer( 'fed_nonce', 'fed_nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( array( 'message' => __( 'You must be logged in to insert media.', 'frontend-dashboard' ) ), 403 );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Items array is deeply sanitized per-field below.
		$raw_items = isset( $_POST['items'] ) && is_array( $_POST['items'] ) ? wp_unslash( $_POST['items'] ) : array();
		$editor_id = isset( $_POST['editor_id'] ) ? sanitize_text_field( wp_unslash( $_POST['editor_id'] ) ) : 'post_content';

		if ( empty( $raw_items ) ) {
			wp_send_json_error( array( 'message' => __( 'No items provided.', 'frontend-dashboard' ) ), 400 );
		}

		$snippets = array();

		foreach ( $raw_items as $item ) {
			if ( empty( $item ) || ! is_array( $item ) ) {
				continue;
			}

			$id       = isset( $item['id'] ) ? absint( $item['id'] ) : 0;
			$url      = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';
			$title    = isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '';
			$alt      = isset( $item['alt'] ) ? sanitize_text_field( $item['alt'] ) : '';
			$caption  = isset( $item['caption'] ) ? wp_kses_post( $item['caption'] ) : '';
			$align    = isset( $item['align'] ) ? sanitize_key( $item['align'] ) : 'none';
			$size     = isset( $item['size'] ) ? sanitize_key( $item['size'] ) : 'full';
			$link_to  = isset( $item['linkTo'] ) ? sanitize_key( $item['linkTo'] ) : 'none';
			$link_url = isset( $item['linkUrl'] ) ? esc_url_raw( $item['linkUrl'] ) : '';
			$mime     = isset( $item['mime'] ) ? sanitize_text_field( $item['mime'] ) : '';
			$width    = isset( $item['width'] ) ? absint( $item['width'] ) : 0;
			$height   = isset( $item['height'] ) ? absint( $item['height'] ) : 0;
			$is_image = ! empty( $item['is_image'] ) || ( ! empty( $mime ) && 0 === strpos( $mime, 'image/' ) );

			// If ID is valid, enhance with WordPress attachment data.
			$post_obj = $id ? get_post( $id ) : null;
			if ( $post_obj && 'attachment' === $post_obj->post_type ) {
				if ( empty( $title ) ) {
					$title = $post_obj->post_title;
				}
				if ( empty( $caption ) ) {
					$caption = $post_obj->post_excerpt;
				}
				if ( empty( $mime ) ) {
					$mime = $post_obj->post_mime_type;
				}
				if ( empty( $url ) ) {
					$url = wp_get_attachment_url( $id );
				}
			}

			// Generate base HTML representation.
			$html = '';

			if ( $is_image ) {
				$align_class = ( 'none' !== $align && ! empty( $align ) ) ? 'align' . $align : 'alignnone';
				$size_class  = ! empty( $size ) ? 'size-' . $size : 'size-full';
				$id_class    = $id ? ' wp-image-' . $id : '';

				$img_tag = sprintf(
					'<img src="%s" alt="%s" class="%s" />',
					esc_url( $url ),
					esc_attr( $alt ? $alt : $title ),
					esc_attr( trim( "{$align_class} {$size_class}{$id_class}" ) )
				);

				$href = '';
				if ( 'file' === $link_to ) {
					$href = ! empty( $item['full'] ) ? esc_url_raw( $item['full'] ) : $url;
				} elseif ( 'post' === $link_to && $id ) {
					$href = get_attachment_link( $id );
				} elseif ( 'custom' === $link_to && ! empty( $link_url ) ) {
					$href = $link_url;
				}

				if ( ! empty( $href ) ) {
					$img_tag = sprintf( '<a href="%s">%s</a>', esc_url( $href ), $img_tag );
				}

				if ( ! empty( $caption ) ) {
					$width_attr = $width ? ' width="' . $width . '"' : '';
					$html       = sprintf(
						'[caption id="attachment_%d" align="%s"%s]%s %s[/caption]',
						$id,
						esc_attr( $align_class ),
						$width_attr,
						$img_tag,
						$caption
					);
				} else {
					$html = '<p>' . $img_tag . '</p>';
				}
			} elseif ( ! empty( $mime ) && ( 0 === strpos( $mime, 'video/' ) || 0 === strpos( $mime, 'audio/' ) ) ) {
				$tag  = 0 === strpos( $mime, 'video/' ) ? 'video' : 'audio';
				$html = sprintf( '[%s src="%s"][/%s]', $tag, esc_url( $url ), $tag );
			} else {
				// Document / PDF / Other attachment.
				$label = ! empty( $title ) ? $title : ( ! empty( $item['filename'] ) ? sanitize_text_field( $item['filename'] ) : __( 'Download File', 'frontend-dashboard' ) );
				$href  = $url;
				if ( 'custom' === $link_to && ! empty( $link_url ) ) {
					$href = $link_url;
				} elseif ( 'post' === $link_to && $id ) {
					$href = get_attachment_link( $id );
				}

				$html = sprintf(
					'<p><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
					esc_url( $href ),
					esc_html( $label )
				);
			}

			// Prepare standard attachment array for WordPress core 'media_send_to_editor' hook.
			$attachment_meta = array(
				'id'           => $id,
				'url'          => $url,
				'post_title'   => $title,
				'post_excerpt' => $caption,
				'image_alt'    => $alt,
				'align'        => $align,
				'image-size'   => $size,
			);

			/**
			 * 1. Native WordPress Core filter hook:
			 * Allows PDF Embedder and any third-party plugin to convert media into shortcodes or custom embeds.
			 */
			$filtered_html = apply_filters( 'media_send_to_editor', $html, $id, $attachment_meta );

			// Fail-safe PDF Embedder check: if PDF Embedder is active and this is a PDF, but media_send_to_editor didn't modify it.
			$is_pdf = ( ! empty( $mime ) && 'application/pdf' === $mime )
				|| (bool) preg_match( '/\.pdf(\?|$)/i', $url )
				|| ( isset( $item['ext'] ) && 'PDF' === strtoupper( $item['ext'] ) );

			$pdfemb_active = class_exists( '\PDFEmbedder\Plugin' )
				|| function_exists( 'pdf_embedder' )
				|| shortcode_exists( 'pdf-embedder' );

			if ( $is_pdf && $pdfemb_active && $html === $filtered_html ) {
				$title_attr = '';
				if ( ! empty( $title ) && basename( (string) wp_parse_url( $url, PHP_URL_PATH ) ) !== $title ) {
					$title_attr = ' title="' . esc_attr( $title ) . '"';
				}
				$filtered_html = '[pdf-embedder url="' . esc_url( $url ) . '"' . $title_attr . ']';
			}

			/**
			 * 2. Dedicated Frontend Dashboard filter hook:
			 * Allows plugins to customize media HTML specifically for Frontend Dashboard views.
			 */
			$filtered_html = apply_filters( 'fed_media_send_to_editor', $filtered_html, $id, $item, $editor_id );

			$snippets[] = $filtered_html;
		}

		wp_send_json_success( array( 'snippets' => $snippets ) );
	}
}
