<?php

namespace FED\Contracts\Payments;

use FED\Http\Request;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface PaymentGatewayInterface
 *
 * Contract for commercial and third-party payment gateway extensions.
 */
interface PaymentGatewayInterface {

	/**
	 * Unique identifier for the gateway (e.g. 'stripe', 'paypal', 'razorpay').
	 *
	 * @return string
	 */
	public function getId(): string;

	/**
	 * User-friendly display name of the gateway.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Alias for getName() to retrieve the gateway title.
	 *
	 * @return string
	 */
	public function getTitle(): string;

	/**
	 * Check whether the gateway supports a specific capability (e.g., 'subscriptions', 'refunds', 'single_payment').
	 *
	 * @param string $feature
	 * @return bool
	 */
	public function supports( string $feature ): bool;

	/**
	 * Render checkout input / button elements for the gateway on the frontend.
	 *
	 * @param array $orderData
	 * @return string
	 */
	public function renderCheckoutFields( array $orderData = [] ): string;

	/**
	 * Process a payment request.
	 *
	 * @param PaymentRequest $request
	 * @return PaymentResponse
	 */
	public function processPayment( PaymentRequest $request ): PaymentResponse;

	/**
	 * Handle an incoming webhook request from the payment gateway.
	 *
	 * @param Request $request
	 * @return PaymentResponse
	 */
	public function handleWebhook( Request $request ): PaymentResponse;

	/**
	 * Refund a completed transaction.
	 *
	 * @param string $transactionId
	 * @param float  $amount
	 * @return bool
	 */
	public function refund( string $transactionId, float $amount ): bool;
}
