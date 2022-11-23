<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsPlaceOrderModel
 *
 * @author kamran
 */
class OmsPlaceOrderModel extends Model
{

    protected $table = 'oms_place_order';
    protected $primaryKey = "place_order_id";

    const FIELD_PLACE_ORDER_ID = "place_order_id";
    const FIELD_ORDER_ID = "order_id";
    const FIELD_USER_ID = "user_id";
    const FIELD_STORE = 'store';
    public function orderProducts(){
        return $this->hasMany(OmsOrderProductModel::class,'order_id','order_id');
    }
    public function omsOrder(){
        return $this->hasOne(OmsOrdersModel::class,"order_id","order_id");
    }
}
