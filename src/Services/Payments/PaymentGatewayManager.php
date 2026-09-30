<?php
/**
 * Payment Gateway Manager
 *
 * @package Frontend Dashboard
 */

namespace FED\Services\Payments;

use FED\Contracts\Payments\PaymentGatewayInterface;
use FED\Contracts\Payments\PaymentRequest;
use FED\Contracts\Payments\PaymentResponse;
use FED\Http\Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaymentGatewayManager {

	/**
	 * @var array<string, PaymentGatewayInterface>
	 */
	protected $gateways = [];

	/**
	 * @var self|null
	 */
	protected static $instance = null;

	public static function getInstance(): self {
		if ( null === static::$instance ) {
			static::$instance = new self();
		}
		return static::$instance;
	}

	public function __construct() {
		$this->initDefaultGateways();
	}

	/**
	 * Initialize default built-in gateways.
	 */
	protected function initDefaultGateways(): void {
		$this->registerGateway( new BankTransferGateway() );
		
		// Allow third-party and Pro addons to register gateways
		do_action( 'fed_register_payment_gateways', $this );
	}

	/**
	 * Register a gateway instance.
	 *
	 * @param PaymentGatewayInterface $gateway
	 */
	public function registerGateway( PaymentGatewayInterface $gateway ): void {
		$this->gateways[ $gateway->getId() ] = $gateway;
	}

	/**
	 * Get a registered gateway by ID.
	 *
	 * @param string $gatewayId
	 * @return PaymentGatewayInterface|null
	 */
	public function getGateway( string $gatewayId ): ?PaymentGatewayInterface {
		return $this->gateways[ $gatewayId ] ?? null;
	}

	/**
	 * Get all registered gateways.
	 *
	 * @return array<string, PaymentGatewayInterface>
	 */
	public function getGateways(): array {
		return $this->gateways;
	}

	/**
	 * Get all enabled gateways.
	 *
	 * @return array<string, PaymentGatewayInterface>
	 */
	public function getEnabledGateways(): array {
		$current_gateway = function_exists( 'fed_payment_gateway' ) ? fed_payment_gateway() : false;
		if ( ! $current_gateway ) {
			return $this->gateways;
		}

		if ( isset( $this->gateways[ $current_gateway ] ) ) {
			return [ $current_gateway => $this->gateways[ $current_gateway ] ];
		}

		return $this->gateways;
	}

	/**
	 * Process payment through a specific gateway.
	 *
	 * @param string         $gatewayId
	 * @param PaymentRequest $request
	 * @return PaymentResponse
	 */
	public function process( string $gatewayId, PaymentRequest $request ): PaymentResponse {
		$gateway = $this->getGateway( $gatewayId );
		if ( ! $gateway ) {
			/* translators: %s: Gateway ID */
			return PaymentResponse::failed( sprintf( __( 'Payment Gateway "%s" not found.', 'frontend-dashboard' ), $gatewayId ) );
		}
		return $gateway->processPayment( $request );
	}

	/**
	 * Route incoming webhook to designated gateway.
	 *
	 * @param string  $gatewayId
	 * @param Request $request
	 * @return PaymentResponse
	 */
	public function handleWebhook( string $gatewayId, Request $request ): PaymentResponse {
		$gateway = $this->getGateway( $gatewayId );
		if ( ! $gateway ) {
			/* translators: %s: Gateway ID */
			return PaymentResponse::failed( sprintf( __( 'Payment Gateway "%s" not configured for webhooks.', 'frontend-dashboard' ), $gatewayId ) );
		}
		return $gateway->handleWebhook( $request );
	}
}
