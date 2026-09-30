<?php

namespace FED\Api\Endpoints;

use FED\Database\Repositories\UserProfileRepository;
use FED\Http\Validator;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProfileEndpoints {

	const NAMESPACE = 'fed/v1';

	/**
	 * @var UserProfileRepository
	 */
	protected $profileRepo;

	public function __construct( UserProfileRepository $profileRepo ) {
		$this->profileRepo = $profileRepo;
	}

	public function register_routes(): void {
		register_rest_route( self::NAMESPACE, '/profile/fields', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_fields' ],
			'permission_callback' => 'is_user_logged_in',
		] );

		register_rest_route( self::NAMESPACE, '/profile', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'update_profile' ],
			'permission_callback' => 'is_user_logged_in',
		] );
	}

	public function get_fields( WP_REST_Request $request ) {
		$fields = $this->profileRepo->getProfileFields();
		$userId = get_current_user_id();

		$data = [];
		foreach ( $fields as $field ) {
			$metaKey = $field['input_meta'];
			$val     = get_user_meta( $userId, $metaKey, true );

			// Core WordPress user properties fallback
			if ( '' === $val || false === $val ) {
				$user = wp_get_current_user();
				if ( isset( $user->{$metaKey} ) ) {
					$val = $user->{$metaKey};
				}
			}

			$field['current_value'] = $val;
			$data[] = $field;
		}

		return new WP_REST_Response( [
			'success' => true,
			'fields'  => $data,
		], 200 );
	}

	public function update_profile( WP_REST_Request $request ) {
		$userId = get_current_user_id();
		$params = $request->get_json_params() ?: $request->get_body_params();

		$fields = $this->profileRepo->getProfileFields();

		// Build dynamic validation rules
		$rules = [];
		foreach ( $fields as $field ) {
			$metaKey = $field['input_meta'];
			$rule    = [];

			if ( 'Enable' === $field['is_required'] || 'true' === $field['is_required'] ) {
				$rule[] = 'required';
			}

			if ( 'email' === $field['input_type'] ) {
				$rule[] = 'email';
			}

			if ( ! empty( $rule ) ) {
				$rules[ $metaKey ] = implode( '|', $rule );
			}
		}

		$validator = Validator::make( (array) $params, $rules );
		if ( $validator->fails() ) {
			return new WP_Error( 'validation_failed', $validator->firstError(), [ 'status' => 422, 'errors' => $validator->errors() ] );
		}

		// Core fields mapping
		$userdata = [ 'ID' => $userId ];
		if ( isset( $params['user_email'] ) ) {
			$userdata['user_email'] = sanitize_email( $params['user_email'] );
		}
		if ( isset( $params['first_name'] ) ) {
			$userdata['first_name'] = sanitize_text_field( $params['first_name'] );
		}
		if ( isset( $params['last_name'] ) ) {
			$userdata['last_name'] = sanitize_text_field( $params['last_name'] );
		}
		if ( isset( $params['display_name'] ) ) {
			$userdata['display_name'] = sanitize_text_field( $params['display_name'] );
		}
		if ( isset( $params['description'] ) ) {
			$userdata['description'] = sanitize_textarea_field( $params['description'] );
		}
		if ( isset( $params['user_url'] ) ) {
			$userdata['user_url'] = esc_url_raw( $params['user_url'] );
		}

		wp_update_user( $userdata );

		// Custom meta fields update
		foreach ( $fields as $field ) {
			$metaKey = $field['input_meta'];
			if ( array_key_exists( $metaKey, $params ) && ! in_array( $metaKey, [ 'user_email', 'first_name', 'last_name', 'display_name', 'description', 'user_url' ], true ) ) {
				update_user_meta( $userId, $metaKey, sanitize_text_field( $params[ $metaKey ] ) );
			}
		}

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Profile updated successfully', 'frontend-dashboard' ),
		], 200 );
	}
}
