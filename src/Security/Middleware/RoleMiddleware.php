<?php

namespace FED\Security\Middleware;

use FED\Http\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RoleMiddleware {

	public function handle( array $allowedRoles ): void {
		( new AuthMiddleware() )->handle();

		$user  = wp_get_current_user();
		$roles = (array) $user->roles;

		if ( in_array( 'administrator', $roles, true ) ) {
			return;
		}

		if ( count( array_intersect( $roles, $allowedRoles ) ) === 0 ) {
			Response::error( 'Forbidden. Insufficient role permissions.', 403 );
		}
	}
}
