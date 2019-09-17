<?php
/**
 * Transaction Template
 *
 * @package frontend-dashboard
 */

$transactions = fed_get_transactions();

if ( ! $transactions instanceof WP_Error) {
    $random = fed_get_random_string(5);
    ?>
    <div class="table-responsive">
        <?php if (fed_is_admin()) { ?>
            <button type="button" data-toggle="modal" data-target="#fed_transaction_modal"
                    class="btn btn-primary m-b-10 fed_frontend_add_new_transaction"><?php _e('Add New Transaction',
                    'frontend-dashboard'); ?></button>
        <?php } ?>
        <table class="table table-hover table-striped fed_datatable">
            <thead>
            <tr>
                <?php if (fed_is_admin()) { ?>
                    <th><?php _e('User Details', 'frontend-dashboard'); ?></th>
                <?php } ?>
                <th><?php _e('Transaction', 'frontend-dashboard'); ?></th>
                <th><?php _e('Product', 'frontend-dashboard'); ?></th>
                <th><?php _e('Amount', 'frontend-dashboard'); ?></th>
                <th><?php _e('Expires', 'frontend-dashboard'); ?></th>
                <th><?php _e('Purchase Date', 'frontend-dashboard'); ?></th>
                <th>
                    <i class="fa fa-print"></i>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($transactions)) {
                foreach ($transactions as $transaction) {?>
                    <tr>
                        <?php if (fed_is_admin()) { ?>
                            <td><?php printf('<strong>ID:</strong> %s <br> <strong>Name:</strong> %s <br> <strong>Email:</strong> %s',
                                    esc_attr($transaction['user_id']),
                                    esc_attr($transaction['user_nicename']),
                                    esc_attr($transaction['user_email'])) ?></td>
                        <?php } ?>
                        <td><?php echo esc_attr($transaction['transaction_id']) ?></td>
                        <td><?php echo fed_transaction_product_details($transaction); ?></td>
                        <td><?php echo esc_attr($transaction['amount']).' '.mb_strtoupper(esc_attr($transaction['currency'])) ?></td>
                        <td><?php echo esc_attr($transaction['ends_at']) ?></td>
                        <td><?php echo esc_attr($transaction['created']) ?></td>
                        <td>
                            <form method="post" class="fed_ajax_print_invoice"
                                  action="<?php echo fed_get_ajax_form_action('fed_ajax_request').'&fed_action_hook=FEDInvoice@download'; ?>">
                                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                                <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>"/>
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
        <div class="modal fade" id="fed_invoice_popup">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><?php _e('Invoice', 'frontend-dashboard') ?></h4>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-default fed_invoice_print" data-dismiss="modal">
                            <i class="fa fa-print"></i>
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
    <?php
} else {
    _e('Sorry something went wrong', 'frontend-dashboard');
}
?>

<div class="modal fade" id="fed_transaction_modal" tabindex="-1" role="dialog">
    <form class="fed_ajax"
          action="<?php echo fed_get_ajax_form_action('fed_ajax_request').'&fed_action_hook=FEDTransaction@update'; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Add New Transaction', 'frontend-dashboard'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('User Name', 'frontend-dashboard') ?></label>
                                        <?php wp_dropdown_users(array(
                                            'name'             => 'user_id',
                                            'show_option_none' => 'Please Select the User',
                                            'class'            => 'form-control',
                                            'role__not_in'     => array('administrator'),
                                        )) ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('Transaction ID', 'frontend-dashboard') ?></label>
                                        <input type="text" value="<?php echo strtoupper(fed_get_random_string(15)); ?>"
                                               placeholder="<?php _e('Transaction ID', 'frontend-dashboard') ?>"
                                               name="transaction_id" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php _e('Purchase Date', 'frontend-dashboard') ?></label>
                                        <input type="date"
                                               placeholder="<?php _e('Purchase Date - dd/mm/yyyy',
                                                   'frontend-dashboard') ?>"
                                               name="created" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php _e('Payment Source', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => array(
                                                                 '' => __('Please select', 'frontend-dashboard'),
                                                             ) + fed_get_payment_sources(),
                                            'input_meta'  => 'payment_source',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php _e('Status', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => fed_payment_status(),
                                            'input_meta'  => 'status',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 p-10">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label><?php _e('Product Name', 'frontend-dashboard') ?></label>
                                        <input type="text"
                                               placeholder="<?php _e('Product Name', 'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][plan_name]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('Payment for', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => array('' => 'Please Select') + fed_payment_for(),
                                            'input_meta'  => 'items['.$random.'][type]',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?php _e('Quantity', 'frontend-dashboard') ?></label>
                                        <input type="number"
                                               placeholder="<?php _e('(eg) 1 or 2',
                                                   'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][quantity]" class="form-control"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('Plan Type', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => fed_mp_default_plan(),
                                            'input_meta'  => 'items['.$random.'][plan_type]',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>

                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('If Plan Type Custom', 'frontend-dashboard') ?></label>
                                        <input type="text"
                                               placeholder="<?php _e('Number of Days',
                                                   'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][plan_days]" class="form-control"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('User Role', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => array(
                                                                 'fed_null' => __('Let it be default',
                                                                     'frontend-dashboard'),
                                                             ) + fed_get_user_roles_without_admin(),
                                            'input_meta'  => 'items['.$random.'][user_role]',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label><?php _e('Default User Role', 'frontend-dashboard') ?></label>
                                        <?php echo fed_get_input_details(array(
                                            'input_value' => array(
                                                                 'fed_null' => __('Let it be default',
                                                                     'frontend-dashboard'),
                                                             ) + fed_get_user_roles_without_admin(),
                                            'input_meta'  => 'items['.$random.'][default_user_role]',
                                            'input_type'  => 'select',
                                        )); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label><?php _e('Amount', 'frontend-dashboard') ?></label>
                                    <div class="input-group fed_input_group_select">
                                        <input type="text"
                                               placeholder="<?php _e('Amount', 'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][amount]" class="form-control"/>
                                        <span class="input-group-addon">
                                            <?php echo fed_get_input_details(array(
                                                'input_value' => fed_get_currency_type_key_value(),
                                                'input_meta'  => 'items['.$random.'][currency]',
                                                'input_type'  => 'select',
                                            )); ?>
                                          </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label><?php _e('Tax', 'frontend-dashboard') ?></label>
                                    <div class="input-group fed_input_group_select">
                                        <input type="text"
                                               placeholder="<?php _e('Tax', 'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][tax_value]" class="form-control"/>
                                        <span class="input-group-addon">
                                            <?php echo fed_get_input_details(array(
                                                'input_value' => fed_mp_discount_type(),
                                                'input_meta'  => 'items['.$random.'][tax]',
                                                'input_type'  => 'select',
                                            )); ?>
                                          </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label><?php _e('Discount', 'frontend-dashboard') ?></label>
                                    <div class="input-group fed_input_group_select">
                                        <input type="text"
                                               placeholder="<?php _e('Discount Value', 'frontend-dashboard') ?>"
                                               name="items[<?php echo $random; ?>][discount_value]"
                                               class="form-control"/>
                                        <span class="input-group-addon">
                                            <?php echo fed_get_input_details(array(
                                                'input_value' => fed_mp_discount_type(),
                                                'input_meta'  => 'items['.$random.'][discount]',
                                                'input_type'  => 'select',
                                            )); ?>
                                          </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close',
                            'frontend-dashboard') ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e('Add New Transaction',
                            'frontend-dashboard') ?></button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </form>
</div><!-- /.modal -->
