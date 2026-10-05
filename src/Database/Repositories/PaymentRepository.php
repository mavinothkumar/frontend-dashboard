<?php

namespace FED\Database\Repositories;

use FED\Database\BaseRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PaymentRepository
 *
 * Repository handling payment transactions and invoice line items.
 */
class PaymentRepository extends BaseRepository {

	protected $table = 'fed_payment';

	/**
	 * Retrieve all transactions for a specific user ID.
	 *
	 * @param int $userId
	 * @return array
	 */
	public function getTransactionsForUser( $userId ) {
		return $this->where( array( 'user_id' => (int) $userId ), 'created_at DESC, id DESC' );
	}

	/**
	 * Find a payment by its gateway transaction ID.
	 *
	 * @param string $transactionId
	 * @return array|null
	 */
	public function findByTransactionId( $transactionId ) {
		return $this->findBy( 'transaction_id', $transactionId );
	}

	/**
	 * Retrieve all line items for a specific payment ID.
	 *
	 * @param int $paymentId
	 * @return array
	 */
	public function getItemsForPayment( $paymentId ) {
		$table = $this->db->prefix . 'fed_payment_items';
		$sql   = $this->db->prepare( "SELECT * FROM `{$table}` WHERE `payment_id` = %d", (int) $paymentId );
		$rows  = $this->db->get_results( $sql, ARRAY_A );

		return $rows ?: array();
	}

	/**
	 * Create a payment transaction along with its line items.
	 *
	 * @param array $paymentData
	 * @param array $items
	 * @return int|false Payment ID on success, false on failure.
	 */
	public function createWithItems( array $paymentData, array $items = array() ) {
		$paymentId = $this->create( $paymentData );

		if ( ! $paymentId ) {
			return false;
		}

		if ( ! empty( $items ) ) {
			$itemsTable = $this->db->prefix . 'fed_payment_items';
			foreach ( $items as $item ) {
				$item['payment_id'] = $paymentId;
				$this->db->insert( $itemsTable, $item );
			}
		}

		return $paymentId;
	}
}
