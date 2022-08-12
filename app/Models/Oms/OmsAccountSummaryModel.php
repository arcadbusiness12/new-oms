<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsAccountSummaryModel extends Model
{
    protected $table = 'oms_account_summary';
    protected $primaryKey = "account_id";

    const FIELD_ACCOUNT_ID = "account_id";
    const FIELD_USER_ID = "user_id";
    const FIELD_BALANCE = "balance";
}