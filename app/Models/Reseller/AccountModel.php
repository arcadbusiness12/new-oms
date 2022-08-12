<?php

namespace App\Models\Reseller;

use Illuminate\Database\Eloquent\Model;

class AccountModel extends Model
{
    public $timestamps = false;
    protected $table = 'reseller_accounts';
    protected $fillable = ['reseller_id', 'order_id', 'ba_opencart_customer_id', 'transaction_type', 'product_detail', 'product_charges', 'shipping_charges', 'total', 'transaction_status', 'transaction_date'];

    public function acountDetails() {
        return $this->hasMany(ResellerAccountDetailModel::class, 'reseller_account_id');
    }

    
}
