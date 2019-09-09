<?php

$user_role = fed_get_current_user_role_key();
if ($user_role === 'administrator') {
    $transactions = fed_fetch_rows_by_table(BC_FED_TABLE_PAYMENT, 'desc');
} else {
    $transactions = fed_fetch_table_rows_by_key_value(BC_FED_TABLE_PAYMENT, 'user_id', get_current_user_id());
}

if ( ! $transactions instanceof WP_Error) {
    ?>
    <div class="table-responsive">
        <?php if ($user_role === 'administrator') { ?>
            <button class="btn btn-primary m-b-10">Add New Transaction</button>
        <?php } ?>
        <table class="table table-hover table-striped">
            <thead>
            <tr>
                <th>Transaction</th>
                <th>Amount</th>
                <th>Expires</th>
                <th>Purchase Date</th>
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
                        <td><?php echo $transaction['transaction_id'] ?></td>
                        <td><?php echo $transaction['amount'].' '.$transaction['currency'] ?></td>
                        <td><?php echo $transaction['ends_at'] ?></td>
                        <td><?php echo $transaction['created'] ?></td>
                        <td>
                            <a target="_blank" href="<?php echo $transaction['invoice_url'] ?>">
                                <i class="fa fa-download"></i>
                            </a>
                        </td>
                    </tr>
                <?php }
            } else {
                ?>
                <tr>
                    <td colspan="5">
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
