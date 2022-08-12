<?php

namespace App\Models\Reseller;

use Illuminate\Database\Eloquent\Model;

class PriceHistoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'reseller_price_histories';
    protected $fillable = ['reseller_product_id','user_id', 'product_id', 'group_id', 'sku', 'product_price', 'old_price', 'new_price', 'created_at'];
}
