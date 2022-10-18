<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryProductSpecialModel extends Model
{
    use HasFactory;
    protected $table = 'oms_inventory_product_specials';

    public function inventoryProduct() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
}
