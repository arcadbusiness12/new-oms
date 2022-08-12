<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsAccountTransactionModel extends Model
{
    protected $table = 'oms_account_transaction';
    protected $primaryKey = "transaction_id";

    const FIELD_TRANSACTION_ID = "transaction_id";
    const FIELD_ACCOUNT_ID = "account_id";
    const FIELD_ORDER_ID = "order_id";
    const FIELD_DESCRIPTION = "description";
    const FIELD_RECEIPT = "receipt";
    const FIELD_PAYMENT_DATE = "payment_date";
    const FIELD_CREDIT = "credit";
    const FIELD_DEBIT = "debit";
    const FIELD_BALANCE = "balance";
}