<?php
/**
 * Global FED Helper Functions
 *
 * @package Frontend Dashboard
 */

namespace FED;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use FED\Core\Application;
use FED\Services\View;
use FED\Http\Request;
use FED\Http\Response;

if ( ! function_exists( 'FED\app' ) ) {
	/**
	 * Resolve a service from the FED Application container.
	 *
	 * @param string|null $abstract
	 * @param array       $parameters
	 * @return mixed|Application|\FED\Core\Container
	 */
	function app( $abstract = null, array $parameters = [] ) {
		$app = Application::getInstance();

		if ( is_null( $abstract ) ) {
			return $app->getContainer();
		}

		return $app->make( $abstract, $parameters );
	}
}

if ( ! function_exists( 'FED\view' ) ) {
	/**
	 * Render a scoped view template.
	 *
	 * @param string $template
	 * @param array  $data
	 * @return string
	 */
	function view( $template, array $data = [] ) {
		return app( View::class )->render( $template, $data );
	}
}

if ( ! function_exists( 'FED\request' ) ) {
	/**
	 * Get the current HTTP Request instance or parameter.
	 *
	 * @param string|null $key
	 * @param mixed       $default
	 * @return Request|mixed
	 */
	function request( $key = null, $default = null ) {
		$req = app( Request::class );
		if ( is_null( $key ) ) {
			return $req;
		}
		return $req->input( $key, $default );
	}
}

if ( ! function_exists( 'FED\response' ) ) {
	/**
	 * Get the HTTP Response builder.
	 *
	 * @return Response
	 */
	function response() {
		return app( Response::class );
	}
}

if ( ! function_exists( 'FED\field' ) ) {
	/**
	 * Render a form field via FieldFactory.
	 *
	 * @param string $type
	 * @param array  $attributes
	 * @return string
	 */
	function field( string $type, array $attributes = [] ): string {
		return \FED\Services\Fields\FieldFactory::render( $type, $attributes );
	}
}

if ( ! function_exists( 'FED\gateways' ) ) {
	/**
	 * Get the Payment Gateway Manager instance.
	 *
	 * @return \FED\Services\Payments\PaymentGatewayManager
	 */
	function gateways(): \FED\Services\Payments\PaymentGatewayManager {
		return \FED\Services\Payments\PaymentGatewayManager::getInstance();
	}
}

