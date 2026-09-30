<?php

namespace FED\Contracts\Payments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PaymentRequest
 *
 * Data Transfer Object for payment processing requests.
 */
class PaymentRequest {

	public $amount;
	public $currency;
	public $userId;
	public $customerEmail;
	public $description;
	public $metadata;

	public function __construct( float $amount, string $currency = 'USD', int $userId = 0, string $customerEmail = '', string $description = '', array $metadata = [] ) {
		$this->amount        = $amount;
		$this->currency      = strtoupper( $currency );
		$this->userId        = $userId;
		$this->customerEmail = $customerEmail;
		$this->description   = $description;
		$this->metadata      = $metadata;
	}
}
