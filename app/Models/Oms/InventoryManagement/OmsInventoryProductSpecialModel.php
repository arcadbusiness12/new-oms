<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryProductSpecialModel extends Model
{
    use HasFactory;
    protected $table = 'oms_inventory_product_specials';

    public function productSpecials() {
        return $this->hasMany(OmsInventoryProductModel::class, 'inventory_product_id');
    }
}
