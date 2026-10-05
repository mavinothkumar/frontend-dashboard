<?php

namespace FED\Api\Endpoints;

use FED\Http\Validator;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthEndpoints {

	const NAMESPACE = 'fed/v1';

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/auth/login',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'login' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/auth/register',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'register' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/auth/reset-password',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'reset_password' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/auth/me',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'me' ),
				'permission_callback' => 'is_user_logged_in',
			)
		);
	}

	public function login( WP_REST_Request $request ) {
		$params    = $request->get_json_params() ?: $request->get_body_params();
		$validator = Validator::make(
			(array) $params,
			array(
				'user_login'    => 'required|string',
				'user_password' => 'required|string',
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

		$credentials = array(
			'user_login'    => sanitize_text_field( $params['user_login'] ),
			'user_password' => $params['user_password'],
			'remember'      => ! empty( $params['remember'] ),
		);

		$user = wp_signon( $credentials, is_ssl() );

		if ( is_wp_error( $user ) ) {
			return new WP_Error( 'invalid_credentials', $user->get_error_message(), array( 'status' => 401 ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Login successful', 'frontend-dashboard' ),
				'user'    => array(
					'id'           => $user->ID,
					'username'     => $user->user_login,
					'email'        => $user->user_email,
					'display_name' => $user->display_name,
					'roles'        => $user->roles,
				),
			),
			200
		);
	}

	public function register( WP_REST_Request $request ) {
		if ( ! get_option( 'users_can_register' ) ) {
			return new WP_Error( 'registration_disabled', __( 'User registration is currently closed.', 'frontend-dashboard' ), array( 'status' => 403 ) );
		}

		$params    = $request->get_json_params() ?: $request->get_body_params();
		$validator = Validator::make(
			(array) $params,
			array(
				'user_login' => 'required|string|min:3',
				'user_email' => 'required|email|unique:users,user_email',
				'password'   => 'required|string|min:6',
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

		$username = sanitize_user( $params['user_login'], true );
		$email    = sanitize_email( $params['user_email'] );
		$password = $params['password'];

		$userId = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $userId ) ) {
			return new WP_Error( 'registration_error', $userId->get_error_message(), array( 'status' => 400 ) );
		}

		$user = get_user_by( 'id', $userId );

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Registration successful', 'frontend-dashboard' ),
				'user_id' => $userId,
			),
			201
		);
	}

	public function reset_password( WP_REST_Request $request ) {
		$params    = $request->get_json_params() ?: $request->get_body_params();
		$validator = Validator::make(
			(array) $params,
			array(
				'user_login' => 'required|string',
			)
		);

		if ( $validator->fails() ) {
			return new WP_Error( 'validation_failed', $validator->firstError(), array( 'status' => 422 ) );
		}

		$login = sanitize_text_field( $params['user_login'] );
		$user  = strpos( $login, '@' ) ? get_user_by( 'email', $login ) : get_user_by( 'login', $login );

		if ( ! $user ) {
			return new WP_Error( 'invalid_user', __( 'User not found.', 'frontend-dashboard' ), array( 'status' => 404 ) );
		}

		$status = retrieve_password( $user->user_login );

		if ( is_wp_error( $status ) ) {
			return new WP_Error( 'reset_failed', $status->get_error_message(), array( 'status' => 400 ) );
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => __( 'Password reset link has been sent to your email.', 'frontend-dashboard' ),
			),
			200
		);
	}

	public function me( WP_REST_Request $request ) {
		$user = wp_get_current_user();

		return new WP_REST_Response(
			array(
				'id'           => $user->ID,
				'username'     => $user->user_login,
				'email'        => $user->user_email,
				'first_name'   => $user->first_name,
				'last_name'    => $user->last_name,
				'display_name' => $user->display_name,
				'roles'        => $user->roles,
				'avatar_url'   => get_avatar_url( $user->ID ),
			),
			200
		);
	}
}
