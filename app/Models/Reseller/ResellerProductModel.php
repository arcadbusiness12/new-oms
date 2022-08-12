<?php

namespace App\Models\Reseller;

use Illuminate\Database\Eloquent\Model;

class ResellerProductModel extends Model
{
    public $timestamps = false;
    protected $table = 'reseller_products';
    protected $fillable = ['user_id', 'group_id', 'product_id', 'sku', 'product_price', 'price', 'created_at'];
    
    public function price_histories() {
        return $this->hasMany(PriceHistoryModel::class, 'reseller_product_id')->orderBy('id','DESC');
    }
}
