<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsWithdrawRequestModel extends Model
{
    protected $table = 'oms_withdraw_request';
    protected $primaryKey = "request_id";

    const FIELD_REQUEST_ID = "request_id";
    const FIELD_USER_ID = "user_id";
    const FIELD_AMOUNT = "amount";
    const FIELD_STATUS = "status";
}