<?php

namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\OmsInventoryProductDescriptionModel;
use Illuminate\Database\Eloquent\Model;

class storeModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_store';
    protected $fillable = ['name','status'];

    public function productDescriptions() {
        return $this->hasMany(OmsInventoryProductDescriptionModel::class, 'store_id');
      }

      public function specialPrices() {
        return $this->hasMany(OmsInventoryProductSpecialModel::class, 'store_id');
      }
}
