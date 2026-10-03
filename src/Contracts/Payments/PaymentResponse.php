<?php

namespace FED\Contracts\Payments;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PaymentResponse
 *
 * Data Transfer Object for payment processing outcomes.
 */
class PaymentResponse {

	public $success;
	public $transactionId;
	public $message;
	public $redirectUrl;
	public $rawResponse;

	public function __construct( bool $success, string $transactionId = '', string $message = '', string $redirectUrl = '', array $rawResponse = array() ) {
		$this->success       = $success;
		$this->transactionId = $transactionId;
		$this->message       = $message;
		$this->redirectUrl   = $redirectUrl;
		$this->rawResponse   = $rawResponse;
	}

	public static function success( string $transactionId, string $message = 'Payment successful', array $raw = array() ) {
		return new self( true, $transactionId, $message, '', $raw );
	}

	public static function failed( string $message = 'Payment failed', array $raw = array() ) {
		return new self( false, '', $message, '', $raw );
	}

	public static function redirect( string $url, string $transactionId = '' ) {
		return new self( true, $transactionId, 'Redirecting...', $url );
	}
}
