<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use DB;
class OmsOrderStatusModel extends Model
{
    //public $timestamps = true;
    protected $table = 'oms_order_statuses';
    // public function createdBy(){
    //   return $this->belongsTo(OmsUserModel::class,'created_by');
    // }
    // public function products(){
    //   return $this->hasMany(OmsInventoryProductModel::class,'group_id','product_group_id');
    // }
}
