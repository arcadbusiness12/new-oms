<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\ProductPhotographyPostingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use DB;
class ProductPhotographyModel extends Model
{
    public $timestamps = true;
    protected $table = 'oms_product_photography';
    public function photographyPosting(){
      return $this->hasMany(ProductPhotographyPostingModel::class,'product_photography_id');
    }
    public function products(){
      return $this->hasMany(OmsInventoryProductModel::class,'group_id','product_group_id');
    }
}
