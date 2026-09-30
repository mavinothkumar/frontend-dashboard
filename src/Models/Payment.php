<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Payment
 *
 * Active Record Model for Transactions.
 */
class Payment extends Model {

	protected $table = 'fed_payment';

	protected $fillable = [
		'user_id',
		'payment_type',
		'payment_source',
		'transaction_id',
		'amount',
		'currency',
		'status',
	];

	protected $casts = [
		'id'      => 'int',
		'user_id' => 'int',
		'amount'  => 'float',
	];

	/**
	 * Get payment line items.
	 *
	 * @return PaymentItem[]
	 */
	public function items(): array {
		return $this->hasMany( PaymentItem::class, 'payment_id' );
	}
}
