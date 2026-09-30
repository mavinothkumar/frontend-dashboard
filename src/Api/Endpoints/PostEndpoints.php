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
		register_rest_route( self::NAMESPACE, '/posts', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_posts' ],
				'permission_callback' => 'is_user_logged_in',
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'create_post' ],
				'permission_callback' => 'is_user_logged_in',
			],
		] );

		register_rest_route( self::NAMESPACE, '/posts/(?P<id>\d+)', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_post' ],
				'permission_callback' => 'is_user_logged_in',
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'update_post' ],
				'permission_callback' => 'is_user_logged_in',
			],
			[
				'methods'             => 'DELETE',
				'callback'            => [ $this, 'delete_post' ],
				'permission_callback' => 'is_user_logged_in',
			],
		] );
	}

	public function get_posts( WP_REST_Request $request ) {
		$userId   = get_current_user_id();
		$postType = $request->get_param( 'post_type' ) ?: 'post';
		$page     = (int) ( $request->get_param( 'page' ) ?: 1 );
		$perPage  = (int) ( $request->get_param( 'per_page' ) ?: 10 );

		$query = new \WP_Query( [
			'author'         => $userId,
			'post_type'      => sanitize_key( $postType ),
			'post_status'    => [ 'publish', 'draft', 'pending' ],
			'paged'          => $page,
			'posts_per_page' => $perPage,
		] );

		$posts = [];
		foreach ( $query->posts as $post ) {
			$posts[] = [
				'id'            => $post->ID,
				'title'         => $post->post_title,
				'status'        => $post->post_status,
				'date'          => $post->post_date,
				'featured_image'=> get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '',
				'edit_url'      => get_edit_post_link( $post->ID, 'raw' ),
			];
		}

		return new WP_REST_Response( [
			'success'     => true,
			'posts'       => $posts,
			'total'       => $query->found_posts,
			'total_pages' => $query->max_num_pages,
		], 200 );
	}

	public function get_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post || ( (int) $post->post_author !== $userId && ! current_user_can( 'manage_options' ) ) ) {
			return new WP_Error( 'not_found', __( 'Post not found or unauthorized.', 'frontend-dashboard' ), [ 'status' => 404 ] );
		}

		return new WP_REST_Response( [
			'success' => true,
			'post'    => [
				'id'      => $post->ID,
				'title'   => $post->post_title,
				'content' => $post->post_content,
				'excerpt' => $post->post_excerpt,
				'status'  => $post->post_status,
			],
		], 200 );
	}

	public function create_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$params = $request->get_json_params() ?: $request->get_body_params();

		$validator = Validator::make( (array) $params, [
			'post_title'   => 'required|string|min:3',
			'post_content' => 'required|string',
		] );

		if ( $validator->fails() ) {
			return new WP_Error( 'validation_failed', $validator->firstError(), [ 'status' => 422, 'errors' => $validator->errors() ] );
		}

		$postData = [
			'post_title'   => sanitize_text_field( $params['post_title'] ),
			'post_content' => wp_kses_post( $params['post_content'] ),
			'post_excerpt' => isset( $params['post_excerpt'] ) ? sanitize_textarea_field( $params['post_excerpt'] ) : '',
			'post_status'  => isset( $params['post_status'] ) && in_array( $params['post_status'], [ 'publish', 'draft', 'pending' ], true ) ? $params['post_status'] : 'pending',
			'post_type'    => isset( $params['post_type'] ) ? sanitize_key( $params['post_type'] ) : 'post',
			'post_author'  => $userId,
		];

		$postId = wp_insert_post( $postData, true );

		if ( is_wp_error( $postId ) ) {
			return new WP_Error( 'post_creation_failed', $postId->get_error_message(), [ 'status' => 400 ] );
		}

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Post submitted successfully', 'frontend-dashboard' ),
			'post_id' => $postId,
		], 201 );
	}

	public function update_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post || ( (int) $post->post_author !== $userId && ! current_user_can( 'manage_options' ) ) ) {
			return new WP_Error( 'not_found', __( 'Post not found or unauthorized.', 'frontend-dashboard' ), [ 'status' => 404 ] );
		}

		$params = $request->get_json_params() ?: $request->get_body_params();

		$postData = [ 'ID' => $postId ];
		if ( isset( $params['post_title'] ) ) {
			$postData['post_title'] = sanitize_text_field( $params['post_title'] );
		}
		if ( isset( $params['post_content'] ) ) {
			$postData['post_content'] = wp_kses_post( $params['post_content'] );
		}
		if ( isset( $params['post_excerpt'] ) ) {
			$postData['post_excerpt'] = sanitize_textarea_field( $params['post_excerpt'] );
		}
		if ( isset( $params['post_status'] ) && in_array( $params['post_status'], [ 'publish', 'draft', 'pending' ], true ) ) {
			$postData['post_status'] = $params['post_status'];
		}

		$updatedId = wp_update_post( $postData, true );

		if ( is_wp_error( $updatedId ) ) {
			return new WP_Error( 'post_update_failed', $updatedId->get_error_message(), [ 'status' => 400 ] );
		}

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Post updated successfully', 'frontend-dashboard' ),
		], 200 );
	}

	public function delete_post( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$postId = (int) $request->get_param( 'id' );
		$post   = get_post( $postId );

		if ( ! $post || ( (int) $post->post_author !== $userId && ! current_user_can( 'manage_options' ) ) ) {
			return new WP_Error( 'not_found', __( 'Post not found or unauthorized.', 'frontend-dashboard' ), [ 'status' => 404 ] );
		}

		wp_trash_post( $postId );

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Post moved to trash', 'frontend-dashboard' ),
		], 200 );
	}
}
