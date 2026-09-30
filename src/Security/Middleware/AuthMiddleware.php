<?php

namespace FED\Security\Middleware;

use FED\Http\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AuthMiddleware {

	public function handle(): void {
		if ( ! is_user_logged_in() ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				Response::error( 'Unauthorized. Please log in.', 401 );
			}
			$req_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
			wp_safe_redirect( wp_login_url( $req_uri ) );
			exit;
		}
	}
}
