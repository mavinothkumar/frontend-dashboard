<?php

namespace FED\Api\Endpoints;

use FED\Http\Validator;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PostEndpoints {

	const NAMESPACE = 'fed/v1';

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/posts',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_posts' ),
					'permission_callback' => array( $this, 'check_read_posts_permission' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'create_post' ),
					'permission_callback' => array( $this, 'check_create_post_permission' ),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/posts/(?P<id>\d+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'get_post' ),
					'permission_callback' => array( $this, 'check_read_single_post_permission' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'update_post' ),
					'permission_callback' => array( $this, 'check_edit_post_permission' ),
				),
				array(
					'methods'             => 'DELETE',
					'callback'            => array( $this, 'delete_post' ),
					'permission_callback' => array( $this, 'check_delete_post_permission' ),
				),
			)
		);
	}

	/**
	 * Permission check: reading author posts list.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_read_posts_permission( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to view posts.', 'frontend-dashboard' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$postType = $request->get_param( 'post_type' ) ?: 'post';
		$postType = sanitize_key( $postType );

		if ( ! post_type_exists( $postType ) ) {
			return new WP_Error(
				'rest_invalid_post_type',
				__( 'Invalid post type.', 'frontend-dashboard' ),
				array( 'status' => 400 )
			);
		}

		$postTypeObj = get_post_type_object( $postType );
		if ( ! $postTypeObj || ( ! current_user_can( $postTypeObj->cap->edit_posts ) && ! current_user_can( 'read' ) ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to view posts for this post type.', 'frontend-dashboard' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Permission check: creating a post.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_create_post_permission( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to create posts.', 'frontend-dashboard' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$params   = $request->get_json_params() ?: $request->get_body_params();
		$postType = isset( $params['post_type'] ) ? sanitize_key( $params['post_type'] ) : 'post';

		if ( ! post_type_exists( $postType ) ) {
			return new WP_Error(
				'rest_invalid_post_type',
				__( 'Invalid post type.', 'frontend-dashboard' ),
				array( 'status' => 400 )
			);
		}

		$postTypeObj = get_post_type_object( $postType );
		if ( ! $postTypeObj ) {
			return new WP_Error(
				'rest_invalid_post_type',
				__( 'Invalid post type.', 'frontend-dashboard' ),
				array( 'status' => 400 )
			);
		}

		// Disallow pages or unprivileged post types for low-privileged users
		$createCap = $postTypeObj->cap->create_posts ?? $postTypeObj->cap->edit_posts;
		if ( ! current_user_can( $createCap ) ) {
			return new WP_Error(
				'rest_cannot_create',
				__( 'You do not have permission to create posts of this type.', 'frontend-dashboard' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Permission check: reading a single post.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_read_single_post_permission( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to view this post.', 'frontend-dashboard' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post ) {
			return new WP_Error(
				'not_found',
				__( 'Post not found.', 'frontend-dashboard' ),
				array( 'status' => 404 )
			);
		}

		if ( (int) $post->post_author !== $userId && ! current_user_can( 'manage_options' ) && ! current_user_can( 'read_post', $postId ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to view this post.', 'frontend-dashboard' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Permission check: editing a post.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_edit_post_permission( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to edit posts.', 'frontend-dashboard' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post ) {
			return new WP_Error(
				'not_found',
				__( 'Post not found.', 'frontend-dashboard' ),
				array( 'status' => 404 )
			);
		}

		$postTypeObj = get_post_type_object( $post->post_type );
		$editCap     = $postTypeObj ? $postTypeObj->cap->edit_post : 'edit_post';

		if ( ! current_user_can( $editCap, $postId ) && ( (int) $post->post_author !== $userId || ! current_user_can( 'edit_posts' ) ) ) {
			return new WP_Error(
				'rest_cannot_edit',
				__( 'You do not have permission to edit this post.', 'frontend-dashboard' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Permission check: deleting a post.
	 *
	 * @param WP_REST_Request $request
	 * @return bool|WP_Error
	 */
	public function check_delete_post_permission( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'You must be logged in to delete posts.', 'frontend-dashboard' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post ) {
			return new WP_Error(
				'not_found',
				__( 'Post not found.', 'frontend-dashboard' ),
				array( 'status' => 404 )
			);
		}

		$postTypeObj = get_post_type_object( $post->post_type );
		$deleteCap   = $postTypeObj ? $postTypeObj->cap->delete_post : 'delete_post';

		if ( ! current_user_can( $deleteCap, $postId ) && ( (int) $post->post_author !== $userId || ! current_user_can( 'delete_posts' ) ) ) {
			return new WP_Error(
				'rest_cannot_delete',
				__( 'You do not have permission to delete this post.', 'frontend-dashboard' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	public function get_posts( WP_REST_Request $request ) {
		$userId   = get_current_user_id();
		$postType = $request->get_param( 'post_type' ) ?: 'post';
		$page     = (int) ( $request->get_param( 'page' ) ?: 1 );
		$perPage  = (int) ( $request->get_param( 'per_page' ) ?: 10 );

		$query = new \WP_Query(
			array(
				'author'         => $userId,
				'post_type'      => sanitize_key( $postType ),
				'post_status'    => array( 'publish', 'draft', 'pending' ),
				'paged'          => $page,
				'posts_per_page' => $perPage,
			)
		);

		$posts = array();
		foreach ( $query->posts as $post ) {
			$posts[] = array(
				'id'             => $post->ID,
				'title'          => $post->post_title,
				'status'         => $post->post_status,
				'date'           => $post->post_date,
				'featured_image' => get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '',
				'edit_url'       => get_edit_post_link( $post->ID, 'raw' ),
			);
		}

		return new WP_REST_Response(
			array(
				'success'     => true,
				'posts'       => $posts,
				'total'       => $query->found_posts,
				'total_pages' => $query->max_num_pages,
			),
			200
		);
	}

	public function get_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post || ( (int) $post->post_author !== $userId && ! current_user_can( 'manage_options' ) ) ) {
			return new WP_Error( 'not_found', __( 'Post not found or unauthorized.', 'frontend-dashboard' ), array( 'status' => 404 ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'post'    => array(
					'id'      => $post->ID,
					'title'   => $post->post_title,
					'content' => $post->post_content,
					'excerpt' => $post->post_excerpt,
					'status'  => $post->post_status,
				),
			),
			200
		);
	}

	public function create_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$params = $request->get_json_params() ?: $request->get_body_params();

		$validator = Validator::make(
			(array) $params,
			array(
				'post_title'   => 'required|string|min:3',
				'post_content' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return new WP_Error(
				'validation_failed',
				$validator->firstError(),
				array(
					'status' => 422,
					'errors' => $validator->errors(),
				)
			);
		}

		$postType = isset( $params['post_type'] ) ? sanitize_key( $params['post_type'] ) : 'post';
		if ( ! post_type_exists( $postType ) ) {
			return new WP_Error( 'invalid_post_type', __( 'Invalid post type.', 'frontend-dashboard' ), array( 'status' => 400 ) );
		}

		$postTypeObj = get_post_type_object( $postType );
		if ( ! $postTypeObj ) {
			return new WP_Error( 'invalid_post_type', __( 'Invalid post type.', 'frontend-dashboard' ), array( 'status' => 400 ) );
		}

		$createCap = $postTypeObj->cap->create_posts ?? $postTypeObj->cap->edit_posts;
		if ( ! current_user_can( $createCap ) ) {
			return new WP_Error( 'forbidden', __( 'You do not have permission to create this post type.', 'frontend-dashboard' ), array( 'status' => 403 ) );
		}

		// Determine safe post status based on user capabilities
		$requestedStatus = isset( $params['post_status'] ) && in_array( $params['post_status'], array( 'publish', 'draft', 'pending' ), true ) ? $params['post_status'] : 'pending';
		$publishCap      = $postTypeObj->cap->publish_posts ?? 'publish_posts';

		if ( 'publish' === $requestedStatus && ! current_user_can( $publishCap ) ) {
			$requestedStatus = 'pending';
		}

		$postData = array(
			'post_title'   => sanitize_text_field( $params['post_title'] ),
			'post_content' => wp_kses_post( function_exists( 'fed_clean_html_tag_attributes' ) ? fed_clean_html_tag_attributes( $params['post_content'] ) : $params['post_content'] ),
			'post_excerpt' => isset( $params['post_excerpt'] ) ? sanitize_textarea_field( $params['post_excerpt'] ) : '',
			'post_status'  => $requestedStatus,
			'post_type'    => $postType,
			'post_author'  => $userId,
		);

		$postId = wp_insert_post( $postData, true );

		if ( is_wp_error( $postId ) ) {
			return new WP_Error( 'post_creation_failed', $postId->get_error_message(), array( 'status' => 400 ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Post submitted successfully', 'frontend-dashboard' ),
				'post_id' => $postId,
			),
			201
		);
	}

	public function update_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post ) {
			return new WP_Error( 'not_found', __( 'Post not found.', 'frontend-dashboard' ), array( 'status' => 404 ) );
		}

		$postTypeObj = get_post_type_object( $post->post_type );
		$editCap     = $postTypeObj ? $postTypeObj->cap->edit_post : 'edit_post';

		if ( ! current_user_can( $editCap, $postId ) && ( (int) $post->post_author !== $userId || ! current_user_can( 'edit_posts' ) ) ) {
			return new WP_Error( 'forbidden', __( 'You do not have permission to edit this post.', 'frontend-dashboard' ), array( 'status' => 403 ) );
		}

		$params = $request->get_json_params() ?: $request->get_body_params();

		$postData = array( 'ID' => $postId );
		if ( isset( $params['post_title'] ) ) {
			$postData['post_title'] = sanitize_text_field( $params['post_title'] );
		}
		if ( isset( $params['post_content'] ) ) {
			$postData['post_content'] = wp_kses_post( function_exists( 'fed_clean_html_tag_attributes' ) ? fed_clean_html_tag_attributes( $params['post_content'] ) : $params['post_content'] );
		}
		if ( isset( $params['post_excerpt'] ) ) {
			$postData['post_excerpt'] = sanitize_textarea_field( $params['post_excerpt'] );
		}
		if ( isset( $params['post_status'] ) && in_array( $params['post_status'], array( 'publish', 'draft', 'pending' ), true ) ) {
			$requestedStatus = sanitize_key( $params['post_status'] );
			$publishCap      = $postTypeObj ? $postTypeObj->cap->publish_posts : 'publish_posts';
			if ( 'publish' === $requestedStatus && ! current_user_can( $publishCap, $postId ) ) {
				$requestedStatus = 'pending';
			}
			$postData['post_status'] = $requestedStatus;
		}

		$updatedId = wp_update_post( $postData, true );

		if ( is_wp_error( $updatedId ) ) {
			return new WP_Error( 'post_update_failed', $updatedId->get_error_message(), array( 'status' => 400 ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Post updated successfully', 'frontend-dashboard' ),
			),
			200
		);
	}

	public function delete_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post ) {
			return new WP_Error( 'not_found', __( 'Post not found.', 'frontend-dashboard' ), array( 'status' => 404 ) );
		}

		$postTypeObj = get_post_type_object( $post->post_type );
		$deleteCap   = $postTypeObj ? $postTypeObj->cap->delete_post : 'delete_post';

		if ( ! current_user_can( $deleteCap, $postId ) && ( (int) $post->post_author !== $userId || ! current_user_can( 'delete_posts' ) ) ) {
			return new WP_Error( 'forbidden', __( 'You do not have permission to delete this post.', 'frontend-dashboard' ), array( 'status' => 403 ) );
		}

		wp_trash_post( $postId );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Post moved to trash', 'frontend-dashboard' ),
			),
			200
		);
	}
}
