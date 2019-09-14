<?php
/**
 * Transaction Template
 *
 * @package frontend-dashboard
 */

$transactions = fed_get_transactions();

if ( ! $transactions instanceof WP_Error) {
    ?>
    <div class="table-responsive">
        <?php if (fed_is_admin()) { ?>
            <button type="button" data-toggle="modal" data-target="#fed_transaction_modal" class="btn btn-primary m-b-10 fed_frontend_add_new_transaction"><?php _e('Add New Transaction', 'frontend-dashboard'); ?></button>
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
                foreach ($transactions as $transaction) { ?>
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
                            <?php if($transaction['invoice_url'] !== 'custom'){ ?>
                            <a target="_blank" href="<?php echo esc_url($transaction['invoice_url']) ?>">
                                <i class="fa fa-download"></i>
                            </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td colspan="7">
                        <?php _e('Sorry no transaction yet', 'frontend-dashboard'); ?>
                    </td>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>
    <?php
} else {
    _e('Sorry something went wrong', 'frontend-dashboard');
}
?>

<div class="modal fade" id="fed_transaction_modal" tabindex="-1" role="dialog" >
    <form class="fed_ajax"
          action="<?php echo fed_get_form_action('fed_ajax_request').'&fed_action_hook=FEDTransaction@update'; ?>">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <?php fed_wp_nonce_field('fed_nonce', 'fed_nonce') ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php _e('Add New Transaction', 'frontend-dashboard'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php _e('User Name', 'frontend-dashboard') ?></label>
                                <?php wp_dropdown_users(array(
                                    'name'             => 'user_id',
                                    'show_option_none' => 'Please Select the User',
                                    'class'            => 'form-control',
                                    'role__not_in'     => array('administrator'),
                                )) ?>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Transaction ID', 'frontend-dashboard') ?></label>
                                <input type="text" value="<?php echo fed_get_random_string(15); ?>"
                                       placeholder="<?php _e('Transaction ID', 'frontend-dashboard') ?>"
                                       name="transaction_id" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Purchase Date', 'frontend-dashboard') ?></label>
                                <input type="date"
                                       placeholder="<?php _e('Purchase Date - dd/mm/yyyy', 'frontend-dashboard') ?>"
                                       name="created" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Payment Source', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_get_payment_sources(),
                                    'input_meta'  => 'payment_source',
                                    'input_type'  => 'select',
                                )); ?>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Plan Type', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_mp_default_plan(),
                                    'input_meta'  => 'plan_type',
                                    'input_type'  => 'select',
                                )); ?>
                            </div>
                            <div class="form-group">
                                <label><?php _e('If Plan Type Custom', 'frontend-dashboard') ?></label>
                                <input type="text"
                                       placeholder="<?php _e('If Plan Type Custom, Enter the Days',
                                           'frontend-dashboard') ?>"
                                       name="plan_days" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Membership Type', 'frontend-dashboard') ?></label>
                                <input type="text"
                                       placeholder="<?php _e('Membership Type (eg) Membership, Post',
                                           'frontend-dashboard') ?>"
                                       name="type" class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label><?php _e('Product Name', 'frontend-dashboard') ?></label>
                                <input type="text"
                                       placeholder="<?php _e('Product Name', 'frontend-dashboard') ?>"
                                       name="name" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Amount', 'frontend-dashboard') ?></label>
                                <input type="text"
                                       placeholder="<?php _e('Amount', 'frontend-dashboard') ?>"
                                       name="amount" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Currency Type', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_get_currency_type_key_value(),
                                    'input_meta'  => 'currency',
                                    'input_type'  => 'select',
                                )); ?>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Discount Value', 'frontend-dashboard') ?></label>
                                <input type="text"
                                       placeholder="<?php _e('Discount Value', 'frontend-dashboard') ?>"
                                       name="discount_value" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Discount', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_mp_discount_type(),
                                    'input_meta'  => 'discount',
                                    'input_type'  => 'select',
                                )); ?>
                            </div>

                            <div class="form-group">
                                <label><?php _e('User Role', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_get_user_roles_without_admin(),
                                    'input_meta'  => 'user_role',
                                    'input_type'  => 'select',
                                )); ?>
                            </div>
                            <div class="form-group">
                                <label><?php _e('Default User Role', 'frontend-dashboard') ?></label>
                                <?php echo fed_get_input_details(array(
                                    'input_value' => fed_get_user_roles_without_admin(),
                                    'input_meta'  => 'default_user_role',
                                    'input_type'  => 'select',
                                )); ?>
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
