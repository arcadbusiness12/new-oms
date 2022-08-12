<?php

namespace App\Models\Reseller;

use App\Models\Oms\OmsUserModel;
use App\Models\OpenCart\Customers\CustomersModel;
use Illuminate\Database\Eloquent\Model;

class CustomerEWalletModel extends Model
{
    public $timestamps = false;   
    protected $table = 'oms_customer_e_wallets';
    protected $fillable = ['ba_opencart_customer_id', 'reseller_id', 'amount', 'status', 'transfor_date', 'approval_date'];

    public function customer() {
        return $this->belongsTo(CustomersModel::class, 'ba_opencart_customer_id');
    }
    public function reseller() {
        return $this->belongsTo(OmsUserModel::class, 'reseller_id');
    }
}
