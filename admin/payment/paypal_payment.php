<?php
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;


class paypal_payment {
	protected $paypal;

	protected $success_url;

	protected $cancel_url;

	protected $mode;

	protected $options;

	public function __construct() {
		$this->options      = get_option( 'fed_admin_settings_payments' );
		$fed_paypal_options = fed_get_paypal_admin_options( $this->options );
		$url                = fed_paypal_payment_success_cancel_url();

		$this->mode = array(
			'mode'           => $fed_paypal_options['mode'],
			'log.LogEnabled' => true,
			'log.FileName'   => plugin_dir_path( BC_FED_PLUGIN ) . 'admin/payment/log/paypal.log',
			'log.LogLevel'   => 'FINE'
		);

		$this->success_url = $url['success'];

		$this->cancel_url = $url['cancel'];

		$this->paypal = new ApiContext(
			new OAuthTokenCredential(
				$fed_paypal_options['client_id'],
				$fed_paypal_options['secrete_id']
			)
		);

		$this->paypal->setConfig( $this->mode );

	}

	public function paypal_success( $request ) {
		if ( ! isset( $request['paymentId'] ) && ! isset( $request['PayerID'] ) ) {
			$this->paypal_cancel();
			exit();
		}

		$paymentId = $request['paymentId'];
		$payment   = Payment::get( $paymentId, $this->paypal );

		$execution = new PaymentExecution();
		$execution->setPayerId( $request['PayerID'] );

		try {
			$result = $payment->execute( $execution, $this->paypal );
		} catch ( Exception $e ) {
			$this->paypal_cancel();
		}
		$status = $this->addPayment( $result );
		if ( $status['status'] == 'success' ) {
			wp_safe_redirect( $this->success_url . '&tid=' . $status['paypal_id'] );
			exit();
		}

		$this->paypal_cancel();

	}

	public function paypal_cancel() {
		wp_redirect( $this->cancel_url );
		exit();
	}

	private function addPayment( $result ) {
		global $wpdb;
		$table_name = $wpdb->prefix . BC_FED_PAYMENT_DB;
		$user_role  = fed_get_current_user_role();

		$process = array(
			'user_id'           => get_current_user_id(),
			'email'             => $result->payer->payer_info->email,
			'first_name'        => $result->payer->payer_info->first_name,
			'last_name'         => $result->payer->payer_info->last_name,
			'subscription_plan' => strtoupper( $user_role[0] ),
			'recipient_name'    => isset( $result->payer->payer_info->shipping_address->recipient_name ) ? $result->payer->payer_info->shipping_address->recipient_name : '',
			'line1'             => isset( $result->payer->payer_info->shipping_address->line1 ) ? $result->payer->payer_info->shipping_address->line1 : '',
			'city'              => isset( $result->payer->payer_info->shipping_address->city ) ? $result->payer->payer_info->shipping_address->city : '',
			'state'             => isset( $result->payer->payer_info->shipping_address->state ) ? $result->payer->payer_info->shipping_address->state : '',
			'postal_code'       => isset( $result->payer->payer_info->shipping_address->postal_code ) ? $result->payer->payer_info->shipping_address->postal_code : '',
			'country_code'      => isset( $result->payer->payer_info->shipping_address->country_code ) ? $result->payer->payer_info->shipping_address->country_code : '',
			'transaction_id'    => $result->transactions[0]->related_resources[0]->sale->id,
			'payment_source'    => isset( $result->payer->payment_method ) ? $result->payer->payment_method : '',
			'payer_id'          => isset( $result->payer->payer_info->payer_id ) ? $result->payer->payer_info->payer_id : '',
			'amount'            => isset( $result->transactions[0]->related_resources[0]->sale->amount->total ) ? $result->transactions[0]->related_resources[0]->sale->amount->total : '',
			'currency_type'     => isset( $result->transactions[0]->related_resources[0]->sale->amount->currency ) ? $result->transactions[0]->related_resources[0]->sale->amount->currency : '',
			'sku'               => isset( $result->transactions[0]->item_list->items[0]->sku ) ? $result->transactions[0]->item_list->items[0]->sku : '',
			'invoice_number'    => isset( $result->transactions[0]->invoice_number ) ? $result->transactions[0]->invoice_number : '',
			'transaction_fee'   => isset( $result->transactions[0]->related_resources[0]->sale->transaction_fee->value ) ? $result->transactions[0]->related_resources[0]->sale->transaction_fee->value : '',
			'created_at'        => isset( $result->transactions[0]->related_resources[0]->sale->create_time ) ? date( "Y-m-d H:i:s", strtotime( $result->transactions[0]->related_resources[0]->sale->create_time ) ) : date( 'Y-m-d H:i:s' ),
			'updated_at'        => isset( $result->transactions[0]->related_resources[0]->sale->update_time ) ? date( "Y-m-d H:i:s", strtotime( $result->transactions[0]->related_resources[0]->sale->create_time ) ) : date( 'Y-m-d H:i:s' ),
		);

		$wpdb->insert(
			$table_name,
			$process
		);

		return array( 'status' => 'success', 'paypal_id' => $process['transaction_id'] );
	}

	public function process_paypal() {

		$sku                  = current_time( 'YmdHis' ) . '_' . get_current_user_id() . '_' . wp_generate_password( 6, false );
		$purchase_title       = isset( $this->options['settings']['purchase_title'] ) ? $this->options['settings']['purchase_title'] : '';
		$purchase_description = isset( $this->options['settings']['purchase_description'] ) ? $this->options['settings']['purchase_description'] : '';
		$currency_type        = isset( $this->options['settings']['currency_type'] ) ? $this->options['settings']['currency_type'] : 'USD';

		$total = $price = 2; //fed_get_amount_based_on_user_role();

		$payer = new Payer();
		$payer->setPaymentMethod( "PayPal" );

		$item1 = new Item();
		$item1->setName( $purchase_title )
		      ->setCurrency( $currency_type )
		      ->setQuantity( 1 )
		      ->setSku( $sku )
		      ->setPrice( $price );

		$itemList = new ItemList();
		$itemList->setItems( array( $item1 ) );

		$details = new Details();
		$details->setTax( 0 )
		        ->setSubtotal( $price );


		$amount = new Amount();
		$amount->setCurrency( $currency_type )
		       ->setTotal( $total )
		       ->setDetails( $details );

		$transaction = new Transaction();
		$transaction->setAmount( $amount )
		            ->setItemList( $itemList )
		            ->setDescription( $purchase_description )
		            ->setInvoiceNumber( uniqid() );


		$redirectUrls = new RedirectUrls();
		$redirectUrls->setReturnUrl( $this->success_url )
		             ->setCancelUrl( $this->cancel_url );

		$payment = new Payment();
		$payment->setIntent( "sale" )
		        ->setPayer( $payer )
		        ->setRedirectUrls( $redirectUrls )
		        ->setTransactions( array( $transaction ) );

		try {
			$payment->create( $this->paypal );
		} catch ( \PayPal\Exception\PayPalConnectionException $ex ) {
			echo $ex->getCode(); // Prints the Error Code
			echo $ex->getData(); // Prints the detailed error message
			die( $ex );
		} catch ( Exception $ex ) {
			wp_safe_redirect( $this->cancel_url );
			die( $ex );
		}
		$approvalUrl = $payment->getApprovalLink();
		wp_redirect( $approvalUrl );
	}
}
