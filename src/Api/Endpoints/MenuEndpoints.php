<?php

namespace FED\Api\Endpoints;

use FED\Database\Repositories\MenuRepository;
use FED\Http\Validator;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MenuEndpoints {

	const NAMESPACE = 'fed/v1';

	/**
	 * @var MenuRepository
	 */
	protected $menuRepo;

	public function __construct( MenuRepository $menuRepo ) {
		$this->menuRepo = $menuRepo;
	}

	public function register_routes(): void {
		register_rest_route( self::NAMESPACE, '/menus', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_menus' ],
			'permission_callback' => 'is_user_logged_in',
		] );

		register_rest_route( self::NAMESPACE, '/menus/reorder', [
			'methods'             => 'POST',
			'callback'            => [ $this, 'reorder_menus' ],
			'permission_callback' => function() {
				return current_user_can( 'manage_options' );
			},
		] );
	}

	public function get_menus( WP_REST_Request $request ) {
		$user  = wp_get_current_user();
		$roles = (array) $user->roles;
		$role  = ! empty( $roles[0] ) ? $roles[0] : 'subscriber';

		$menus = $this->menuRepo->getMenusForRole( $role );

		return new WP_REST_Response( [
			'success' => true,
			'menus'   => $menus,
		], 200 );
	}

	public function reorder_menus( WP_REST_Request $request ) {
		$params    = $request->get_json_params() ?: $request->get_body_params();
		$validator = Validator::make( (array) $params, [
			'items' => 'required',
		] );

		if ( $validator->fails() ) {
			return new WP_Error( 'validation_failed', $validator->firstError(), [ 'status' => 422 ] );
		}

		$items = (array) $params['items'];
		foreach ( $items as $order => $item ) {
			if ( isset( $item['id'] ) ) {
				$this->menuRepo->update( (int) $item['id'], [
					'menu_order' => (int) $order,
					'parent_id'  => isset( $item['parent_id'] ) ? (string) $item['parent_id'] : '0',
				] );
			}
		}

		return new WP_REST_Response( [
			'success' => true,
			'message' => __( 'Menu order updated successfully', 'frontend-dashboard' ),
		], 200 );
	}
}
