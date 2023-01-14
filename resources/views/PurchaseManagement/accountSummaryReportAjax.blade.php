
<div class="card no-b">
   <div class="panel-heading">
    Transactions
   </div>
<div class="panel panel-default panel_order_list">
    <div class="panel-body">
        <table class="table">
            <thead  style="background-color: #3f51b5;color:white">
                <th class="text-center">Transaction ID</th>
                <th class="text-center">Description</th>
                <th class="text-center">Receipt</th>
                <th class="text-center">Payment Date</th>
                <th class="text-center">Transaction Date</th>
                <th class="text-center">Credit</th>
                <th class="text-center">Debit</th>
                <th class="text-center">Balance</th>
            </thead>
            <?php if($transactions) { ?>
            <?php foreach ($transactions as $transaction) { ?>
            <tr>
                <td class="text-center"><?php echo $transaction['transaction_id'] ?></td>
                <td class="text-center"><?php echo $transaction['description'] ?></td>
                <td class="text-center">
                    <?php if($transaction['receipt']) { ?>
                        <a href="<?php echo URL::asset('/uploads/payment-receipts/' . $transaction['receipt']) ?>" target="_blank"><button type="button" class="btn-link">View</button></a>
                    <?php } else { ?>
                        <label>-</label>
                    <?php } ?>
                </td>
                <td class="text-center">
                    <?php if($transaction['payment_date']) { ?>
                    <?php echo $transaction['payment_date'] ?>        
                    <?php } else { ?>
                        <label>-</label>
                    <?php } ?>
                </td>
                <td class="text-center"><?php echo $transaction['created_at'] ?></td>
                <td class="text-center"><?php echo $transaction['credit'] ? number_format($transaction['credit'],2) : '-' ?></td>
                <td class="text-center"><?php echo $transaction['debit'] ? number_format($transaction['debit'],2) : '-' ?></td>
                <td class="text-center"><?php echo number_format($transaction['balance'],2) ?></td>
            </tr>
            <?php } ?>
            <?php } else { ?>
            <tr>
                <td colspan="8" class="text-center">No Transaction Found!</td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <div class="account-summary-pagination-area text-left">
        <?php echo $pagination ?>
    </div>
</div>
</div>