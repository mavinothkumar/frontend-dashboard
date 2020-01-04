<?php
/**
 * Transaction Template
 *
 * @package frontend-dashboard
 */

$transactions = fed_get_transactions();
?>
<div class="bc_fed">
	<?php
	if ( ! $transactions instanceof WP_Error && is_user_logged_in() ) {
		$random = fed_get_random_string( 5 );
		?>
        <div class="table-responsive">
			<?php if ( fed_is_admin() ) { ?>
                <a href="<?php echo fed_menu_page_url( 'fed_payments', array(
					'menu'  => 'transactions',
					'route' => 'FEDTransaction@add_new_transaction',
				) ); ?>"
                   class="btn btn-primary m-b-10 fed_frontend_add_new_transaction"><?php _e( 'Add New Transaction',
						'frontend-dashboard' ); ?></a>
			<?php } ?>
            <table class="table table-hover table-striped fed_datatable">
                <thead>
                <tr>
					<?php if ( fed_is_admin() ) { ?>
                        <th><?php _e( 'User Details', 'frontend-dashboard' ); ?></th>
					<?php } ?>
                    <th><?php _e( 'Source', 'frontend-dashboard' ); ?></th>
                    <th><?php _e( 'Transaction', 'frontend-dashboard' ); ?></th>
                    <th><?php _e( 'Product', 'frontend-dashboard' ); ?></th>
                    <th><?php _e( 'Amount', 'frontend-dashboard' ); ?></th>
                    <th><?php _e( 'Status', 'frontend-dashboard' ); ?></th>
                    <th><?php _e( 'Purchase Date', 'frontend-dashboard' ); ?></th>
                    <th>
                        <i class="fa fa-print"></i>
                    </th>
                </tr>
                </thead>
                <tbody>
				<?php
				if ( count( $transactions ) ) {
					foreach ( $transactions as $transaction ) { ?>
                        <tr>
							<?php if ( fed_is_admin() ) { ?>
                                <td><?php printf( '<strong>ID:</strong> %s <br> <strong>Name:</strong> %s <br> <strong>Email:</strong> %s',
										esc_attr( $transaction['user_id'] ),
										esc_attr( $transaction['user_nicename'] ),
										esc_attr( $transaction['user_email'] ) ) ?></td>
							<?php } ?>
                            <td><?php esc_attr_e( mb_strtoupper( $transaction['payment_source'] ) ) ?></td>
                            <td><?php esc_attr_e( $transaction['transaction_id'] ) ?></td>
                            <td>
                                <div class="fed_transaction_items_container">
									<?php
									esc_attr_e( mb_strtoupper( $transaction['payment_type'] ) )
									?>
                                    <form class="fed_transaction_items"
                                          action="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDTransaction@items'; ?>"
                                          method="post">
										<?php fed_wp_nonce_field() ?>
                                        <input type="hidden" name="transaction_id"
                                               value="<?php echo $transaction['id']; ?>"/>
                                        <button type="submit" class="btn btn-link"><?php _e( 'More Info',
												'frontend-dashboard' ) ?></button>
                                    </form>
                                </div>
                            </td>
                            <td><?php echo esc_attr( $transaction['amount'] ) . ' ' . mb_strtoupper( esc_attr( $transaction['currency'] ) ) ?></td>
                            <td><?php echo esc_attr( strtoupper( $transaction['status'] ) ) ?></td>
                            <td><?php echo esc_attr( $transaction['created'] ) ?></td>
                            <td>
                                <form method="post" class="fed_ajax_print_invoice"
                                      action="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDInvoice@download'; ?>">
									<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>
                                    <input type="hidden" name="transaction_id"
                                           value="<?php echo $transaction['id']; ?>"/>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-download"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
					<?php }
				} ?>
                </tbody>
            </table>
            <div class="modal fade" id="fed_invoice_popup" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"><?php _e( 'Invoice', 'frontend-dashboard' ) ?></h4>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-default fed_invoice_print">
                                <i class="fa fa-print"></i>
                            </button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
		<?php
	} else {
		_e( 'Sorry, you should login to view this page', 'frontend-dashboard' );
	}
	?>
	<?php
	if ( fed_is_admin() ) {
		?>
        <div class="modal fade" id="fed_transaction_modal" tabindex="-1" role="dialog">
            <form class="fed_ajax"
                  action="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDTransaction@update'; ?>">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
						<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title"><?php _e( 'Add New Transaction', 'frontend-dashboard' ); ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><?php _e( 'User Name', 'frontend-dashboard' ) ?></label>
												<?php wp_dropdown_users( array(
													'name'             => 'user_id',
													'show_option_none' => 'Please Select the User',
													'class'            => 'form-control',
													'role__not_in'     => array( 'administrator' ),
												) ) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><?php _e( 'Transaction ID', 'frontend-dashboard' ) ?></label>
                                                <input type="text"
                                                       value="<?php echo strtoupper( fed_get_random_string( 15 ) ); ?>"
                                                       placeholder="<?php _e( 'Transaction ID',
													       'frontend-dashboard' ) ?>"
                                                       name="transaction_id" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><?php _e( 'Purchase Date', 'frontend-dashboard' ) ?></label>
                                                <input type="date"
                                                       placeholder="<?php _e( 'Purchase Date - dd/mm/yyyy',
													       'frontend-dashboard' ) ?>"
                                                       name="created" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><?php _e( 'Payment Source', 'frontend-dashboard' ) ?></label>
												<?php echo fed_get_input_details( array(
													'input_value' => array(
														                 '' => __( 'Please select',
															                 'frontend-dashboard' ),
													                 ) + fed_get_payment_sources(),
													'input_meta'  => 'payment_source',
													'input_type'  => 'select',
												) ); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label><?php _e( 'Status', 'frontend-dashboard' ) ?></label>
												<?php echo fed_get_input_details( array(
													'input_value' => fed_payment_status(),
													'input_meta'  => 'status',
													'input_type'  => 'select',
												) ); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 p-10 fed_transaction_items_wrapper">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><?php _e( 'Payment for', 'frontend-dashboard' ) ?></label>
                                                <select class="form-control" name='fed_pp_object_type'
                                                        id="fed_add_transaction_item"
                                                        data-url="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDTransaction@add_items&fed_nonce=' . wp_create_nonce( 'fed_nonce' ); ?>">
													<?php foreach (
														array(
															'' => __( 'Please Select', 'frontend-dashboard' ),
														) + fed_get_payment_for_key_index() as $key => $for
													) {
														?>
                                                        <option value="<?php echo $key; ?>"><?php echo $for ?></option>
														<?php

													} ?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8" id="fed_transaction_items_container">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e( 'Close',
									'frontend-dashboard' ) ?></button>
                            <button type="submit" class="btn btn-primary"><?php _e( 'Add New Transaction',
									'frontend-dashboard' ) ?></button>
                        </div>

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </form>
        </div><!-- /.modal -->
	<?php } ?>
    <style>
        @media screen {
            #fed_invoice_popup {
                display: none;
            }
        }

        @media print {
            html, body * {
                visibility: hidden;
                background: white !important;
            }

            #fed_invoice_popup .modal-body, #fed_invoice_popup .modal-body * {
                visibility: visible;
            }

            #fed_invoice_popup {
                border: 2px solid #033333 !important;
                width: auto;
            }

            #fed_invoice_popup .container {
                background: white !important;
                padding: 20px !important;
            }
        }
    </style>
</div>