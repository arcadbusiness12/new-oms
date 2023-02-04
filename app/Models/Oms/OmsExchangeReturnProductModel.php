<?php

namespace App\Models\Oms;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsExchangeReturnProductModel extends Model
{
    protected $table = "oms_exchange_return_products";
    public function product(){
        return $this->belongsTo(OmsInventoryProductModel::class,'product_id','product_id');
    }
    public function productOption(){
        return $this->belongsTo(OmsInventoryProductOptionModel::class,'product_option_id','product_option_id');
    }
}
