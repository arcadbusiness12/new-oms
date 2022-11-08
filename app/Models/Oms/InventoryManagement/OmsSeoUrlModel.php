<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsSeoUrlModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'oms_seo_urls';
    protected $fillable = ['store_id','product_id','type','seo_url'];

    public function inventoryProduct() {
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
}
