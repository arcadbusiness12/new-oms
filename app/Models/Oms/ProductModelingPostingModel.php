<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use DB;
class ProductModelingPostingModel extends Model
{
    public $timestamps = true;
    protected $table = 'oms_product_modeling_posts';
}
