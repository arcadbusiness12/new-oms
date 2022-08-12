<?php

namespace App\Models\Reseller;

use App\Models\Oms\OmsUserModel;
use Illuminate\Database\Eloquent\Model;

class WithdrawRequestModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_reseller_withdraw_request';
    protected $fillable = ['reseller_id', 'amount', 'status', 'created_at', 'updated_at'];
    
    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'reseller_id');
    }
}
