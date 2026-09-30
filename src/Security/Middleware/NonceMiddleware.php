<?php

namespace FED\Security\Middleware;

use FED\Http\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NonceMiddleware {

	public function handle( string $action = 'fed_nonce', string $queryKey = 'fed_nonce' ): void {
		$nonce = isset( $_REQUEST[ $queryKey ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $queryKey ] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, $action ) ) {
			Response::error( 'Invalid or expired security token (nonce). Please refresh.', 403 );
		}
	}
}
