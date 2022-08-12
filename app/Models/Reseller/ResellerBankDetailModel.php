<?php

namespace App\Models\Reseller;

use App\Models\Oms\OmsUserModel;
use Illuminate\Database\Eloquent\Model;

class ResellerBankDetailModel extends Model
{
    public $timestamps = false;
    protected $table = 'reseller_bank_details';
    protected $fillable = ['reseller_id', 'bank_name', 'bank_branch', 'account_no', 'created_at', 'updated_at'];

    public function reseller() {
        return $this->belongsTo(OmsUserModel::class, 'reseller_id');
    }
}
