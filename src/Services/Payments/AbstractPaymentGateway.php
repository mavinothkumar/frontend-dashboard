<?php
/**
 * Abstract Payment Gateway Base
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

abstract class AbstractPaymentGateway implements PaymentGatewayInterface {

	/**
	 * Gateway identifier.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Gateway display name.
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Supported features list.
	 *
	 * @var array
	 */
	protected $supports = [ 'single_payment' ];

	/**
	 * Get Gateway ID.
	 *
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}

	/**
	 * Get Gateway Name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Get Gateway Title.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->getName();
	}

	/**
	 * Check feature support.
	 *
	 * @param string $feature
	 * @return bool
	 */
	public function supports( string $feature ): bool {
		return in_array( $feature, $this->supports, true );
	}

	/**
	 * Default checkout field renderer.
	 *
	 * @param array $orderData
	 * @return string
	 */
	public function renderCheckoutFields( array $orderData = [] ): string {
		return '';
	}

	/**
	 * Default Webhook handler.
	 *
	 * @param Request $request
	 * @return PaymentResponse
	 */
	public function handleWebhook( Request $request ): PaymentResponse {
		return PaymentResponse::success( '', 'Webhook acknowledged.' );
	}

	/**
	 * Default Refund handler.
	 *
	 * @param string $transactionId
	 * @param float  $amount
	 * @return bool
	 */
	public function refund( string $transactionId, float $amount ): bool {
		return false;
	}
}
