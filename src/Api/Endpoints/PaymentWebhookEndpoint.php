<?php
/**
 * Payment Webhook REST Endpoint
 *
 * @package Frontend Dashboard
 */

namespace FED\Api\Endpoints;

use FED\Http\Request;
use FED\Services\Payments\PaymentGatewayManager;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentWebhookEndpoint {

	const NAMESPACE = 'fed/v1';

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/payments/webhook/(?P<gateway>[a-zA-Z0-9_-]+)',
			array(
				'methods'             => array( 'POST', 'GET' ),
				'callback'            => array( $this, 'handle_webhook' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function handle_webhook( WP_REST_Request $request ) {
		$gatewayId = sanitize_key( $request->get_param( 'gateway' ) );
		$payload   = $request->get_json_params() ?: $request->get_params();

		$httpRequest = new Request( $payload, $request->get_headers() );
		$manager     = PaymentGatewayManager::getInstance();
		$response    = $manager->handleWebhook( $gatewayId, $httpRequest );

		if ( ! $response->success ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'message' => $response->message,
				),
				400
			);
		}

		return new WP_REST_Response(
			array(
				'success' => true,
				'message' => $response->message,
				'data'    => $response->rawResponse,
			),
			200
		);
	}
}
