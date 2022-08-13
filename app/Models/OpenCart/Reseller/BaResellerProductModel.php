<?php

namespace App\Models\OpenCart\Reseller;

use App\Models\OpenCart\AbstractOpenCartModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaResellerProductModel extends AbstractOpenCartModel
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'reseller_products';
    protected $fillable = ['user_id', 'group_id', 'product_id', 'sku', 'product_price', 'price', 'created_at'];
    
    const FIELD_USER_ID = 'user_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_SKU = 'sku';
    const FIELD_PRODUCT_PRICE = 'product_price';
    const FIELD_PRICE = 'price';
    const FIELD_CREATED_AT = 'created_at';
}
