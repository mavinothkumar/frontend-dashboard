<?php

/**
 * Display Dashboard Payment.
 *
 * @param string $first_element Position
 */
function fed_display_dashboard_payment( $first_element ) {
	$index        = 'payment';
	$payment_menu = fed_get_payment_menu();
	$payment_rbp  = new FEDPaymentRBP();


	if ( $index == $first_element ) {
		$active = '';
	} else {
		$active = 'hide';
	}
	?>
	<div class="panel panel-primary fed_dashboard_item <?php echo $active . ' ' . $index ?>">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="fa <?php echo $payment_menu[ $index ]['menu_image_id'] ?>"></span>
				<?php echo ucwords( $payment_menu[ $index ]['menu'] ) ?>
			</h3>
		</div>
		<div class="panel-body fed_dashboard_panel_body">
			<div class="fed_panel_body_container">
				<?php
				if ( $payment_rbp->isUserPaid() ) {
					if ( ! $payment_rbp->isUserUnderSubscription() ) {
						fed_display_user_not_paid( $payment_rbp );
					}
					fed_display_user_paid( $payment_rbp->isUserPaid() );
				} else {
					fed_display_user_not_paid( $payment_rbp );
				}
				?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Display User Paid.
 *
 * @param $payment_rbp
 */
function fed_display_user_paid( $payment_rbp ) {
	?>
	<div class="table-responsive">
		<table class="table table-striped">
			<thead>
			<tr class="success">
				<td>Date</td>
				<td>Transaction ID</td>
				<td>Invoice Number</td>
				<td>Amount</td>
				<td>Payment Source</td>
				<td>Download</td>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $payment_rbp as $payment_detail ) {
				?>
				<tr>
					<td><?php echo esc_attr( $payment_detail['created_at'] ) ?></td>
					<td><?php echo esc_attr( $payment_detail['transaction_id'] ) ?></td>
					<td><?php echo esc_attr( $payment_detail['invoice_number'] ) ?></td>
					<td><?php echo esc_attr( $payment_detail['currency_type'] ) . ' ' . (float) $payment_detail['amount'] ?></td>
					<td><?php echo esc_attr( $payment_detail['payment_source'] ) ?></td>
					<td>
						<a target="_blank"
						   href="<?php echo fed_get_invoice_url() . '?invoice_id=' . $payment_detail['sku'] ?>">
							<i class="fa fa-download"></i>
						</a>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * User Not Paid
 *
 * @param object $payment_rbp FEDPaymentRBP
 */
function fed_display_user_not_paid( $payment_rbp ) {
	$payment_settings = new FEDPaymentSettings();
	$current_user_role = fed_get_current_user_role();
	if ( $payment_settings->getSettings() !== null ) {

		?>
		<div class="padd_bot_20">
			<form method="post"
				  role="form"
				  class="fed_user_not_paid_form"
			>
				<?php fed_wp_nonce_field( 'fed_display_user_not_paid', 'fed_display_user_not_paid' ) ?>
				<input type="hidden"
					   name="fed_paypal"
					   value=""/>
				<?php echo do_shortcode( '[fed_show_roles roles=subscriber type=submit]'); ?>
			</form>
		</div>
		<?php
	} else {
		echo 'Admin has not configured the Payment Settings';
	}
}