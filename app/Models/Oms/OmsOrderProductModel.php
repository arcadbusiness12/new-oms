<?php

namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OmsOrderProductModel extends Model
{
    use HasFactory;

    protected $table = 'oms_order_products';

    public function product(){
        return $this->belongsTo(OmsInventoryProductModel::class,'product_id','product_id');
    }
}
