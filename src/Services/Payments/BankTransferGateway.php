<?php
/**
 * Offline Bank Transfer Gateway Driver
 *
 * @package Frontend Dashboard
 */

namespace FED\Services\Payments;

use FED\Contracts\Payments\PaymentRequest;
use FED\Contracts\Payments\PaymentResponse;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BankTransferGateway extends AbstractPaymentGateway {

	protected $id       = 'bank_transfer';
	protected $name     = 'Direct Bank Transfer';
	protected $supports = array( 'single_payment' );

	public function renderCheckoutFields( array $orderData = array() ): string {
		$settings = get_option( 'fed_payment_settings', array() );
		$bankInfo = $settings['bank_details'] ?? __( 'Please transfer the payment to our official bank account and provide transaction ID as reference.', 'frontend-dashboard' );

		return '<div class="fed_bank_transfer_info bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-700 leading-relaxed mb-4">' .
			'<h4 class="font-bold text-slate-800 mb-1">' . esc_html__( 'Bank Transfer Instructions', 'frontend-dashboard' ) . '</h4>' .
			'<p class="m-0">' . nl2br( esc_html( $bankInfo ) ) . '</p>' .
		'</div>';
	}

	public function processPayment( PaymentRequest $request ): PaymentResponse {
		$txId = 'BACS-' . strtoupper( wp_generate_password( 8, false ) );

		return PaymentResponse::success(
			$txId,
			__( 'Order placed successfully. Please complete the bank transfer.', 'frontend-dashboard' )
		);
	}
}
