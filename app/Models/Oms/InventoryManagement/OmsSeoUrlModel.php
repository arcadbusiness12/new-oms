<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsSeoUrlModel extends Model
{
    use HasFactory;
    protected $table = 'oms_seo_urls';

    public function inventoryProduct() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
}
