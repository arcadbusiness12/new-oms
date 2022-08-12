<?php

namespace App\Models\Reseller;

use Illuminate\Database\Eloquent\Model;

class ResellerAccountDetailModel extends Model
{
    public $timestamps = false;
    protected $table = 'reseller_account_details';
    protected $fillable = ['reseller_account_id', 'order_id', 'product_id', 'sku', 'product_price', 'reseller_price', 'profit'];

    public function account() {
        return $this->belongsTo(AccountModel::class, 'reseller_account_id');
    }
}
