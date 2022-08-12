<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\ProductModelingPostingModel;
use DB;
class ProductModelingModel extends Model
{
    public $timestamps = true;
    protected $table = 'oms_product_modeling';
    public function modelingPosting(){
      return $this->hasMany(ProductModelingPostingModel::class,'product_modeling_id');
    }
}
