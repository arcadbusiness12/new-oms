<?php

namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use Illuminate\Database\Eloquent\Model;

class AssignedNewProductImage extends Model
{
    public $timestamps = false;
    protected $table = 'new_product_images';

    public function product() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
}
