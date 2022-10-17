<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsSeoUrlModel extends Model
{
    use HasFactory;
    protected $table = 'oms_seo_urls';

    public function seoUrls() {
        return $this->hasMany(OmsInventoryProductModel::class, 'inventory_product_id');
    }
}
