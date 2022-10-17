<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryProductImageModel extends Model
{
    use HasFactory;
    protected $table = 'oms_inventory_product_images';

    public function productImages() {
        return $this->hasMany(OmsInventoryProductModel::class, 'inventory_product_id');
    }
}
