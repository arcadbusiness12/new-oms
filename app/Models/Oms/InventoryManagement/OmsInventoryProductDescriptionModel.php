<?php

namespace App\Models\Oms\InventoryManagement;

use App\Models\Oms\storeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryProductDescriptionModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'oms_inventory_product_descriptions';

    public function inventoryProduct() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }

    public function store() {
        return $this->belongsTo(storeModel::class, 'store_id');
    }
}
