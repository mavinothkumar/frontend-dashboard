<?php
/**
 * Invoice.
 *
 * @package Frontend Dashboard.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDInvoice' ) ) {
	/**
	 * Class FEDInvoice
	 */
	class FEDInvoice {

		/**
		 * Details.
		 */
		public function details() {
			$settings = get_option( 'fed_invoice_settings', array() );
			?>
			<div style="margin-bottom: 20px;">
				<h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
					<?php esc_html_e( 'Company & Organization Billing Profile', 'frontend-dashboard' ); ?>
				</h4>
				<p style="font-size: 13.5px; color: #64748b; margin: 0;">
					<?php esc_html_e( 'These details will appear in the header and issuer section of customer receipts, invoices, and payment statements.', 'frontend-dashboard' ); ?>
				</p>
			</div>
			<?php
			$array    = array(
				'form'  => array(
					'method' => '',
					'class'  => 'fed_admin_menu fed_ajax',
					'attr'   => '',
					'action' => array(
						'url'        => '',
						'action'     => 'fed_ajax_request',
						'parameters' => array(
							'fed_action_hook' => 'FEDInvoice',
						),
					),
					'nonce'  => array(
						'action' => '',
						'name'   => '',
					),
					'loader' => '',
				),
				'input' => array(
					'Company Name' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Company / Business Name', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( 'e.g. Acme Corporation LLC', 'frontend-dashboard' ),
								'input_meta'  => 'company_name',
								'user_value'  => isset( $settings['details']['company_name'] ) ? $settings['details']['company_name'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Official legal business name displayed on all invoices.', 'frontend-dashboard' ),
							)
						),
					),
					'Company Logo' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Company Logo (Image ID / URL)', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_meta' => 'logo',
								'user_value' => isset( $settings['details']['logo'] ) ? $settings['details']['logo'] : '',
								'input_type' => 'file',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Upload your official brand logo for invoices and receipts.', 'frontend-dashboard' ),
							)
						),
					),
					'Logo Width'   => array(
						'col'          => 'col-md-3',
						'name'         => __( 'Logo Width (px)', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => '160',
								'input_meta'  => 'width',
								'user_value'  => isset( $settings['details']['width'] ) ? $settings['details']['width'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Display width in pixels (e.g. 160).', 'frontend-dashboard' ),
							)
						),
					),
					'Logo Height'  => array(
						'col'          => 'col-md-3',
						'name'         => __( 'Logo Height (px)', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => '60',
								'input_meta'  => 'height',
								'user_value'  => isset( $settings['details']['height'] ) ? $settings['details']['height'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Display height in pixels (e.g. 60).', 'frontend-dashboard' ),
							)
						),
					),
					'Door Number'  => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Suite / Door / Building No.', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( 'Suite 400 / Building B', 'frontend-dashboard' ),
								'input_meta'  => 'door_number',
								'user_value'  => isset( $settings['details']['door_number'] ) ? $settings['details']['door_number'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Building, unit, or suite number.', 'frontend-dashboard' ),
							)
						),
					),
					'Street Name'  => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Street Address', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( '123 Enterprise Way', 'frontend-dashboard' ),
								'input_meta'  => 'street_name',
								'user_value'  => isset( $settings['details']['street_name'] ) ? $settings['details']['street_name'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Street address line.', 'frontend-dashboard' ),
							)
						),
					),
					'City'         => array(
						'col'          => 'col-md-4',
						'name'         => __( 'City', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( 'San Francisco', 'frontend-dashboard' ),
								'input_meta'  => 'city',
								'user_value'  => isset( $settings['details']['city'] ) ? $settings['details']['city'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array( 'content' => __( 'City name.', 'frontend-dashboard' ) )
						),
					),
					'State'        => array(
						'col'          => 'col-md-4',
						'name'         => __( 'State / Province', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( 'California', 'frontend-dashboard' ),
								'input_meta'  => 'state',
								'user_value'  => isset( $settings['details']['state'] ) ? $settings['details']['state'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array( 'content' => __( 'State, region, or province.', 'frontend-dashboard' ) )
						),
					),
					'Postal Code'  => array(
						'col'          => 'col-md-4',
						'name'         => __( 'ZIP / Postal Code', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => '94105',
								'input_meta'  => 'postal_code',
								'user_value'  => isset( $settings['details']['postal_code'] ) ? $settings['details']['postal_code'] : '',
								'input_type'  => 'single_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Postal code or ZIP code.', 'frontend-dashboard' ),
							)
						),
					),
					'Country'      => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Country', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => function_exists( 'fed_get_country_code' ) ? fed_get_country_code() : array(),
								'input_meta'  => 'country',
								'user_value'  => isset( $settings['details']['country'] ) ? $settings['details']['country'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Country where your organization is registered.', 'frontend-dashboard' ),
							)
						),
					),
					'Footer Note'  => array(
						'col'          => 'col-md-12',
						'name'         => __( 'Invoice Footer Note & Payment Terms', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'placeholder' => __( 'Thank you for your business! Payment is due within 30 days of issuance. For billing inquiries, contact billing@example.com.', 'frontend-dashboard' ),
								'input_meta'  => 'footer_note',
								'user_value'  => isset( $settings['details']['footer_note'] ) ? $settings['details']['footer_note'] : '',
								'input_type'  => 'multi_line',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Terms of payment, tax disclaimer, bank wiring instructions, or thank-you note printed at the bottom of the invoice.', 'frontend-dashboard' ),
							)
						),
					),
				),
			);
			$array    = apply_filters( 'fed_invoice_template_data', $array );
			fed_common_simple_layout( $array );
		}

		/**
		 * Update.
		 *
		 * @param  array $request  Request.
		 */
		public function update( $request ) {
			$invoice            = get_option( 'fed_invoice_settings' );
			$invoice['details'] = array(
				'logo'         => isset( $request['logo'] ) ? (int) $request['logo'] : '',
				'width'        => isset( $request['width'] ) ? fed_sanitize_text_field( $request['width'] ) : '',
				'height'       => isset( $request['height'] ) ? fed_sanitize_text_field( $request['height'] ) : '',
				'country'      => isset( $request['country'] ) ? fed_sanitize_text_field( $request['country'] ) : '',
				'postal_code'  => isset( $request['postal_code'] ) ? fed_sanitize_text_field(
					$request['postal_code']
				) : '',
				'state'        => isset( $request['state'] ) ? fed_sanitize_text_field( $request['state'] ) : '',
				'city'         => isset( $request['city'] ) ? fed_sanitize_text_field( $request['city'] ) : '',
				'street_name'  => isset( $request['street_name'] ) ? fed_sanitize_text_field(
					$request['street_name']
				) : '',
				'door_number'  => isset( $request['door_number'] ) ? fed_sanitize_text_field(
					$request['door_number']
				) : '',
				'company_name' => isset( $request['company_name'] ) ? fed_sanitize_text_field(
					$request['company_name']
				) : '',
				'footer_note'  => isset( $request['footer_note'] ) ? fed_sanitize_text_field(
					$request['footer_note']
				) : '',
			);

			$new_settings = apply_filters(
				'fed_payment_invoice_template_update', $invoice,
				$request
			);

			update_option( 'fed_invoice_settings', $new_settings );

			wp_send_json_success(
				array(
					'message' => __( 'Invoice Details Updated Successfully ', 'frontend-dashboard' ),
				)
			);
		}

		/**
		 * Download.
		 *
		 * @param  array $request  Request.
		 */
		public function download( $request ) {
			$validate = new FED_Validation();
			$validate->name( 'Transaction ID' )->value( (int) $request['transaction_id'] )->required();

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}

			// Check the Transaction is Valid.
			$payment = fed_get_transaction_with_meta( (int) $request['transaction_id'] );

			if ( $payment instanceof WP_Error ) {
				wp_send_json_error( array( 'message' => $payment->get_error_message() ) );
			}

			$settings = get_option( 'fed_invoice_settings' );

			if ( ! $settings ) {
				wp_send_json_error(
					array(
						'message' => __(
							'Please configure the Invoice Details ( FED | Payments | Invoice | Company Details )',
							'frontend-dashboard'
						),
					)
				);
			}
			global $wpdb;
			$up_table     = $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
			$up           = $wpdb->get_results( "SELECT id, input_meta FROM $up_table" );
			$user_profile = fed_convert_array_object_to_key_value( $up, 'id', 'input_meta' );

			$user = get_userdata( $payment['user_id'] );

			$user_name      = $this->invoice_address( $settings, $user_profile, $user, 'name' );
			$user_name      = empty( $user_name ) ? $payment['display_name'] : $user_name;
			$user_address   = $this->invoice_address( $settings, $user_profile, $user, 'address' );
			$user_city      = $this->invoice_address( $settings, $user_profile, $user, 'city' );
			$user_state     = $this->invoice_address( $settings, $user_profile, $user, 'state' );
			$user_country   = $this->invoice_address( $settings, $user_profile, $user, 'country' );
			$user_postcode  = $this->invoice_address( $settings, $user_profile, $user, 'postcode' );
			$user_telephone = $this->invoice_address( $settings, $user_profile, $user, 'telephone' );

			$logo         = isset( $settings['details']['logo'] ) ? wp_get_attachment_url(
				$settings['details']['logo']
			) : '#';
			$width        = isset( $settings['details']['width'] ) ? sprintf(
				'width=%s',
				$settings['details']['width']
			) : '';
			$height       = isset( $settings['details']['height'] ) ? sprintf(
				'height=%s',
				$settings['details']['height']
			) : '';
			$company_name = isset( $settings['details']['company_name'] ) ? esc_attr(
				$settings['details']['company_name']
			) : '';
			$door_number  = isset( $settings['details']['door_number'] ) ? esc_attr(
				$settings['details']['door_number']
			) : '';
			$street_name  = isset( $settings['details']['street_name'] ) ? esc_attr(
				$settings['details']['street_name']
			) : '';
			$city         = isset( $settings['details']['city'] ) ? esc_attr( $settings['details']['city'] ) : '';
			$state        = isset( $settings['details']['state'] ) ? esc_attr( $settings['details']['state'] ) : '';
			$country      = isset( $settings['details']['country'] ) ? esc_attr( $settings['details']['country'] ) : '';
			$postal_code  = isset( $settings['details']['postal_code'] ) ? esc_attr(
				$settings['details']['postal_code']
			) : '';
			$footer_note  = isset( $settings['details']['footer_note'] ) ? esc_attr(
				$settings['details']['footer_note']
			) : '';

			$transaction_id = isset( $payment['transaction_id'] ) ? $payment['transaction_id'] : '';
			$created        = isset( $payment['created'] ) ? date( 'Y-m-d', strtotime( $payment['created'] ) ) : '';
			$amount         = isset( $payment['amount'] ) ? $payment['amount'] : '';
			$currency       = isset( $payment['currency'] ) ? $payment['currency'] : '';

			$html = '';
			$html .= '<div class="container" id="print">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="padding-top text-left" id="logo">
                            <img src="' . $logo . '" ' . $width . ' ' . $height . ' />
                        </div>
                    </div>
                    <div class="col-sm-6 text-right">
                    <h3>' . __( 'Company Details', 'frontend-dashboard' ) . '</h3>
                        <h4>' . $company_name . '</h4>
                        <p>' . $door_number . ' ' . $street_name . '</p>
                        <p>' . $city . ' ' . $state . '</p>
                        <p>' . $country . ' ' . $postal_code . '</p>
                    </div>
                </div>
                <hr/>
                <div class="row text-uppercase">
                    <div class="col-sm-6 text-left">
                        <h3>' . __( 'Client Details', 'frontend-dashboard' ) . '</h3>
                        <h4 style="display: block;">' . $user_name . '</h4>
                        <p>' . $user_address . '</p>
                        <p>' . $user_city . '</p>
                        <p>' . $user_state . '</p>
                        <p>' . $user_country . '</p>
                        <p>' . $user_postcode . '</p>
                        <p>' . $user_telephone . '</p>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="invoice-color">
                            <strong><h4>Transaction ID: <br>' . $transaction_id . '</h4></strong>
                            <p style="display: block;">Invoice date: ' . $created . '</p>
                            <h1 style="display: block;" class="big-font">' . $amount . ' ' . $currency . '</h1>
                        </div>
                    </div>
                </div>
                <div class="row tablecss">
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr style="display: table-row;" class="success">
                                <th>' . __( 'Name', 'frontend-dashboard' ) . '</th>
                                <th>' . __( 'Amount', 'frontend-dashboard' ) . '</th>
                                <th>' . __( 'Quantity', 'frontend-dashboard' ) . '</th>
                                <th>' . __( 'Tax/Discount', 'frontend-dashboard' ) . '</th>
                                <th>' . __( 'Total', 'frontend-dashboard' ) . '</th>
                            </tr>
                            </thead>
                            <tbody>';

			foreach ( $payment['payment_items'] as $items ) {
				$item          = unserialize( $items['object_items'] );
				$plan_name     = isset( $item['plan_name'] ) ? esc_attr( $item['plan_name'] ) : '';
				$plan_quantity = isset( $item['quantity'] ) ? (int) $item['quantity'] : 1;
				$total         = isset( $payment['amount'] ) ? floatval( $payment['amount'] ) : 0;
				$plan_amount   = isset( $item['amount'] ) ? floatval( $item['amount'] ) : 0;
				$plan_currency = isset( $item['currency'] ) ? esc_attr( $item['currency'] ) : 'USD';
				$discount      = isset( $item['discount_value'] ) && ! empty( $item['discount_value'] ) ? sprintf(
					'Discount : %s %s',
					esc_attr( $item['discount_value'] ), fed_get_discount_type( esc_attr( $item['discount'] ) )
				) : '';
				$tax           = isset( $item['tax_value'] ) && ! empty( $item['tax_value'] ) ?
					sprintf(
						'Tax : %s %s',
						esc_attr( $item['tax_value'] ), fed_get_discount_type( esc_attr( $item['tax'] ) )
					) : '';
				$html          .= '<tr style="display: table-row;">
                                <td>' . $plan_name . '</td>
                                <td>' . $plan_amount . $plan_currency . '</td>
                                <td>' . $plan_quantity . '</td>
                                <td>' . $discount . '<br>' . $tax . '</td>
                                <td>' . $total . $plan_currency . '</td>
                            </tr>';
			}
			$html .= '<tr style="display: table-row;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="display: table-row;" class="info">
                                <td colspan="4" class="text-right">
                                    <b>' . __( 'Total', 'frontend-dashboard' ) . '</b>
                                </td>
                                <td>
                                    <b>' . floatval( $payment['amount'] ) . ' ' . esc_attr( $payment['currency'] ) . '</b>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <p class="invoice-color">  
                        ' . fed_sanitize_text_field( $footer_note ) . '                         
                        </p>
                    </div>
                </div>
            </div>';

			wp_send_json_success(
				array(
					'message' => 'ok',
					'html'    => $html,
				)
			);
		}

		/**
		 * User Address Mapping.
		 */
		public function user() {
			global $wpdb;

			$table         = $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
			$user_profiles = $wpdb->get_results( "SELECT id, label_name FROM $table" );

			$profiles = array( '' => __( '&mdash; Hide this field from invoice &mdash;', 'frontend-dashboard' ) ) + (array) fed_convert_array_object_to_key_value(
					$user_profiles,
					'id', 'label_name'
				);
			$settings = get_option( 'fed_invoice_settings', array() );
			?>
			<div style="margin-bottom: 20px;">
				<h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0;">
					<?php esc_html_e( 'Customer Billing Address Field Mapping', 'frontend-dashboard' ); ?>
				</h4>
				<p style="font-size: 13.5px; color: #64748b; margin: 0;">
					<?php esc_html_e( 'Map the corresponding User Profile fields configured in Frontend Dashboard to extract customer address details automatically on generated invoices and receipts.', 'frontend-dashboard' ); ?>
				</p>
			</div>
			<?php

			$array = array(
				'form'  => array(
					'method' => '',
					'class'  => 'fed_admin_menu fed_ajax',
					'attr'   => '',
					'action' => array(
						'url'        => '',
						'action'     => 'fed_ajax_request',
						'parameters' => array(
							'fed_action_hook' => 'FEDInvoice@store_user',
						),
					),
					'nonce'  => array(
						'action' => '',
						'name'   => '',
					),
					'loader' => '',
				),
				'input' => array(
					'Customer Name' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Customer Full Name', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'name',
								'user_value'  => isset( $settings['user_address']['name'] ) ? $settings['user_address']['name'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field used for the customer/client full name.', 'frontend-dashboard' ),
							)
						),
					),
					'Street Address' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Street Address', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'address',
								'user_value'  => isset( $settings['user_address']['address'] ) ? $settings['user_address']['address'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing customer street address.', 'frontend-dashboard' ),
							)
						),
					),
					'City' => array(
						'col'          => 'col-md-4',
						'name'         => __( 'City', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'city',
								'user_value'  => isset( $settings['user_address']['city'] ) ? $settings['user_address']['city'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing city name.', 'frontend-dashboard' ),
							)
						),
					),
					'State' => array(
						'col'          => 'col-md-4',
						'name'         => __( 'State / Province', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'state',
								'user_value'  => isset( $settings['user_address']['state'] ) ? $settings['user_address']['state'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing state/province.', 'frontend-dashboard' ),
							)
						),
					),
					'Postal Code' => array(
						'col'          => 'col-md-4',
						'name'         => __( 'Postal / ZIP Code', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'postcode',
								'user_value'  => isset( $settings['user_address']['postcode'] ) ? $settings['user_address']['postcode'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing postal code.', 'frontend-dashboard' ),
							)
						),
					),
					'Country' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Country', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'country',
								'user_value'  => isset( $settings['user_address']['country'] ) ? $settings['user_address']['country'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing customer country.', 'frontend-dashboard' ),
							)
						),
					),
					'Telephone' => array(
						'col'          => 'col-md-6',
						'name'         => __( 'Phone / Telephone', 'frontend-dashboard' ),
						'input'        => fed_get_input_details(
							array(
								'input_value' => $profiles,
								'input_meta'  => 'telephone',
								'user_value'  => isset( $settings['user_address']['telephone'] ) ? $settings['user_address']['telephone'] : '',
								'input_type'  => 'select',
								'class_name'  => 'form-control',
							)
						),
						'help_message' => fed_show_help_message(
							array(
								'content' => __( 'Profile field containing customer phone number.', 'frontend-dashboard' ),
							)
						),
					),
				),
			);
			$array = apply_filters( 'fed_invoice_user_address_data', $array );
			fed_common_simple_layout( $array );
		}

		/**
		 * Store User.
		 *
		 * @param  array $request  Request.
		 */
		public function store_user( $request ) {
			$invoice                 = get_option( 'fed_invoice_settings' );
			$invoice['user_address'] = array(
				'name'      => isset( $request['name'] ) ? (int) $request['name'] : '',
				'address'   => isset( $request['address'] ) ? (int) $request['address'] : '',
				'city'      => isset( $request['city'] ) ? (int) $request['city'] : '',
				'state'     => isset( $request['state'] ) ? (int) $request['state'] : '',
				'postcode'  => isset( $request['postcode'] ) ? (int) $request['postcode'] : '',
				'country'   => isset( $request['country'] ) ? (int) $request['country'] : '',
				'telephone' => isset( $request['telephone'] ) ? (int) $request['telephone'] : '',
			);

			$new_settings = apply_filters(
				'fed_invoice_user_address_data_update', $invoice,
				$request
			);

			update_option( 'fed_invoice_settings', $new_settings );

			wp_send_json_success(
				array(
					'message' => __( 'Invoice User Address Updated Successfully ', 'frontend-dashboard' ),
				)
			);
		}

		/**
		 * Invoice Address
		 *
		 * @param  array  $settings  Settings.
		 * @param  array  $user_profile  User Profile.
		 * @param  object $user  User.
		 * @param  string $key  Key.
		 *
		 * @return mixed|string
		 */
		private function invoice_address( $settings, $user_profile, $user, $key ) {
			if ( isset( $settings['user_address'][ $key ] ) && ! empty( $settings['user_address'][ $key ] ) ) {
				$user_meta = $user_profile[ $settings['user_address'][ $key ] ];
				$v         = isset( $user->$user_meta ) && ! empty( $user->$user_meta ) ? esc_attr(
					$user->$user_meta
				) : '';

				return esc_attr( $v );
			}

			return '';
		}
	}
}
