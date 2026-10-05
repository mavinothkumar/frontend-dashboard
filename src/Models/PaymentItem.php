<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PaymentItem
 *
 * Active Record Model for Payment Line Items.
 */
class PaymentItem extends Model {

	protected $table = 'fed_payment_items';

	protected $fillable = array(
		'payment_id',
		'item_name',
		'item_price',
		'quantity',
	);

	protected $casts = array(
		'id'         => 'int',
		'payment_id' => 'int',
		'item_price' => 'float',
		'quantity'   => 'int',
	);

	/**
	 * Get associated payment.
	 *
	 * @return Payment|null
	 */
	public function payment(): ?Payment {
		return $this->belongsTo( Payment::class, 'payment_id' );
	}
}
