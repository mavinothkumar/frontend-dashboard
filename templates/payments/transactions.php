<?php
/**
 * Transaction Template
 *
 * @package frontend-dashboard
 */

$transactions = fed_get_transactions();
?>
<div class="bc_fed max-w-7xl mx-auto py-6 font-sans text-gray-800">
	<?php
	if ( ! $transactions instanceof WP_Error && is_user_logged_in() ) {
		$random = fed_get_random_string( 5 );
		?>
		<div class="space-y-4">
			<?php if ( fed_is_admin() ) { ?>
				<div class="flex justify-between items-center">
					<h2 class="text-xl font-bold text-gray-900"><?php esc_html_e( 'Transactions', 'frontend-dashboard' ); ?></h2>
					<a href="<?php
						echo esc_url(
							fed_menu_page_url(
								'fed_payments',
								array(
									'menu'  => 'transactions',
									'route' => 'FEDTransaction@add_new_transaction',
								)
							)
						);
						?>"
						class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors fed_frontend_add_new_transaction">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
						<?php esc_attr_e( 'Add New Transaction', 'frontend-dashboard' ); ?>
					</a>
				</div>
			<?php } ?>

			<div class="bg-white shadow rounded-xl border border-gray-200 overflow-hidden">
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 fed_datatable">
						<thead class="bg-gray-50">
						<tr>
							<?php if ( fed_is_admin() ) { ?>
								<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'User Details', 'frontend-dashboard' ); ?></th>
							<?php } ?>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Source', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Transaction', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Product', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Amount', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Status', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Purchase Date', 'frontend-dashboard' ); ?></th>
							<th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider"><?php esc_attr_e( 'Action', 'frontend-dashboard' ); ?></th>
						</tr>
						</thead>
						<tbody class="bg-white divide-y divide-gray-100 text-sm">
						<?php
						if ( count( $transactions ) ) {
							foreach ( $transactions as $transaction ) {
								$status = strtolower( $transaction['status'] );
								$badge_class = 'bg-gray-100 text-gray-800';
								if ( in_array( $status, [ 'completed', 'paid', 'success', 'succeeded' ], true ) ) {
									$badge_class = 'bg-green-100 text-green-800';
								} elseif ( in_array( $status, [ 'pending', 'processing' ], true ) ) {
									$badge_class = 'bg-yellow-100 text-yellow-800';
								} elseif ( in_array( $status, [ 'failed', 'cancelled', 'refunded' ], true ) ) {
									$badge_class = 'bg-red-100 text-red-800';
								}
								?>
								<tr class="hover:bg-gray-50/80 transition-colors">
									<?php if ( fed_is_admin() ) { ?>
										<td class="px-6 py-4 whitespace-nowrap">
											<div class="text-xs font-medium text-gray-900">ID: <?php echo esc_html( $transaction['user_id'] ); ?></div>
											<div class="text-xs text-gray-700 font-semibold"><?php echo esc_html( $transaction['user_nicename'] ); ?></div>
											<div class="text-xs text-gray-500"><?php echo esc_html( $transaction['user_email'] ); ?></div>
										</td>
									<?php } ?>
									<td class="px-6 py-4 whitespace-nowrap">
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
											<?php echo esc_html( mb_strtoupper( $transaction['payment_source'] ) ); ?>
										</span>
									</td>
									<td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-700"><?php echo esc_html( $transaction['transaction_id'] ); ?></td>
									<td class="px-6 py-4 whitespace-nowrap">
										<div class="fed_transaction_items_container">
											<span class="font-medium text-gray-900"><?php echo esc_html( mb_strtoupper( $transaction['payment_type'] ) ); ?></span>
											<form class="fed_transaction_items inline-block ml-2"
													action="<?php
													echo esc_url(
														add_query_arg(
															array(
																'fed_action_hook' => 'FEDTransaction@items',
															), fed_get_ajax_form_action(
																'fed_ajax_request'
															)
														)
													);
													?>" method="post">
												<?php fed_wp_nonce_field(); ?>
												<input type="hidden" name="transaction_id" value="<?php echo esc_attr( $transaction['id'] ); ?>"/>
												<button type="submit" class="text-xs text-blue-600 hover:text-blue-800 underline">
													<?php esc_attr_e( 'More Info', 'frontend-dashboard' ); ?>
												</button>
											</form>
										</div>
									</td>
									<td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
										<?php echo esc_html( $transaction['amount'] . ' ' . mb_strtoupper( $transaction['currency'] ) ); ?>
									</td>
									<td class="px-6 py-4 whitespace-nowrap">
										<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold <?php echo esc_attr( $badge_class ); ?>">
											<?php echo esc_html( strtoupper( $transaction['status'] ) ); ?>
										</span>
									</td>
									<td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs"><?php echo esc_html( $transaction['created'] ); ?></td>
									<td class="px-6 py-4 whitespace-nowrap text-center">
										<form method="post" class="fed_ajax_print_invoice inline-block"
												action="<?php
												echo esc_url(
													add_query_arg(
														array( 'fed_action_hook' => 'FEDInvoice@download' ),
														fed_get_ajax_form_action(
															'fed_ajax_request'
														)
													)
												);
												?>">
											<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
											<input type="hidden" name="transaction_id" value="<?php echo esc_attr( $transaction['id'] ); ?>"/>
											<button type="submit" class="inline-flex items-center p-1.5 border border-gray-200 rounded-lg text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition-colors shadow-sm" title="<?php esc_attr_e( 'Download Invoice', 'frontend-dashboard' ); ?>">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
											</button>
										</form>
									</td>
								</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<td colspan="8" class="px-6 py-12 text-center text-gray-500">
									<?php esc_html_e( 'No transactions found.', 'frontend-dashboard' ); ?>
								</td>
							</tr>
							<?php
						}
						?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="modal fade" id="fed_invoice_popup" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title"><?php esc_attr_e( 'Invoice', 'frontend-dashboard' ); ?></h4>
						</div>
						<div class="modal-body"></div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="button" class="btn btn-default fed_invoice_print">
								<i class="fa fa-print"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	} else {
		?>
		<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
			<p class="text-sm text-yellow-700"><?php esc_attr_e( 'Sorry, you should login to view this page', 'frontend-dashboard' ); ?></p>
		</div>
		<?php
	}
	?>
	<?php
	if ( fed_is_admin() ) {
		?>
		<div class="modal fade" id="fed_transaction_modal" tabindex="-1" role="dialog">
			<form class="fed_ajax"
					action="
					<?php
					echo esc_url(
						add_query_arg(
							array( 'fed_action_hook' => 'FEDTransaction@update' ), fed_get_ajax_form_action(
								'fed_ajax_request'
							)
						)
					);
					?>
									   ">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ); ?>
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title">
								<?php
								esc_attr_e(
									'Add New Transaction', 'frontend-dashboard'
								);
								?>
							</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label><?php esc_attr_e( 'User Name', 'frontend-dashboard' ); ?></label>
												<?php
												wp_dropdown_users(
													array(
														'name'             => 'user_id',
														'show_option_none' => 'Please Select the User',
														'class'            => 'form-control',
														'role__not_in'     => array( 'administrator' ),
													)
												)
												?>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label>
													<?php
													esc_attr_e(
														'Transaction ID', 'frontend-dashboard'
													);
													?>
												</label>
												<input type="text"
														value="
														<?php
														echo esc_attr(
															strtoupper( fed_get_random_string( 15 ) )
														);
														?>
														"
														placeholder="
														<?php
														esc_attr_e(
															'Transaction ID',
															'frontend-dashboard'
														)
														?>
														"
														name="transaction_id" class="form-control"/>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>
												<?php
												esc_attr_e(
													'Purchase Date', 'frontend-dashboard'
												);
													?>
													</label>
												<input type="date"
														placeholder="
														<?php
														esc_attr_e(
															'Purchase Date - dd/mm/yyyy',
															'frontend-dashboard'
														)
														?>
														"
														name="created" class="form-control"/>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label>
												<?php
												esc_attr_e(
													'Payment Source', 'frontend-dashboard'
												);
													?>
													</label>
												<?php
												echo fed_get_input_details(
													array(
														'input_value' => array(
															'' => __(
																'Please select',
																'frontend-dashboard'
															),
														) + fed_get_payment_sources(),
														'input_meta'  => 'payment_source',
														'input_type'  => 'select',
													)
												);
												?>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?php esc_attr_e( 'Status', 'frontend-dashboard' ); ?></label>
												<?php
												echo fed_get_input_details(
													array(
														'input_value' => fed_payment_status(),
														'input_meta'  => 'status',
														'input_type'  => 'select',
													)
												);
												?>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-12 p-10 fed_transaction_items_wrapper">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>
												<?php
												esc_attr_e(
													'Payment for', 'frontend-dashboard'
												);
													?>
													</label>
												<select class="form-control" name='fed_pp_object_type'
														id="fed_add_transaction_item"
														data-url="
														<?php
														echo esc_url(
															add_query_arg(
																array(
																	'fed_action_hook' => 'FEDTransaction@add_items',
																	'fed_nonce'       => wp_create_nonce(
																		'fed_nonce'
																	),
																), fed_get_ajax_form_action(
																	'fed_ajax_request'
																)
															)
														);
														?>
														 ">
													<?php
													foreach (
														array(
															'' => __( 'Please Select', 'frontend-dashboard' ),
														) + fed_get_payment_for_key_index() as $key => $for
													) {
														?>
														<option value="<?php echo esc_attr( $key ); ?>">
															<?php echo esc_attr( $for ); ?></option>
														<?php

													}
													?>
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
							<button type="button" class="btn btn-default" data-dismiss="modal">
								<?php
								esc_attr_e(
									'Close',
									'frontend-dashboard'
								)
								?>
							</button>
							<button type="submit" class="btn btn-primary">
								<?php
								esc_attr_e(
									'Add New Transaction',
									'frontend-dashboard'
								)
								?>
							</button>
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
