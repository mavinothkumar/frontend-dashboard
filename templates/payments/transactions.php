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
            <a class="btn btn-primary m-b-10" data-toggle="modal"
               href="#fed_transaction_modal"><?php _e('Add New Transaction', 'frontend-dashboard'); ?></a>
        <?php } ?>
        <table class="table table-hover table-striped">
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
                            <td><?php printf('ID: %s <br> Name: %s <br> Email: %s', esc_attr($transaction['user_id']),
                                    esc_attr($transaction['user_nicename']),
                                    esc_attr($transaction['user_email'])) ?></td>
                        <?php } ?>
                        <td><?php echo esc_attr($transaction['transaction_id']) ?></td>
                        <td><?php echo fed_transaction_product_details($transaction); ?></td>
                        <td><?php echo esc_attr($transaction['amount']).' '.mb_strtoupper(esc_attr($transaction['currency'])) ?></td>
                        <td><?php echo esc_attr($transaction['ends_at']) ?></td>
                        <td><?php echo esc_attr($transaction['created']) ?></td>
                        <td>
                            <a target="_blank" href="<?php echo esc_url($transaction['invoice_url']) ?>">
                                <i class="fa fa-download"></i>
                            </a>
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

<div class="modal fade" id="fed_transaction_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                Modal body ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
