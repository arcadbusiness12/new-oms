<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\OmsOrdersModel;
class OmsInventoryOnholdQuantityModel extends Model
{
    protected $table = 'oms_inventory_onhold_quantity';
    protected $primaryKey = "onhold_id";

    const FIELD_ONHOLD_ID = 'onhold_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_STORE = 'store';
    public function order()
    {
      return $this->belongsTo(OmsOrdersModel::class, 'order_id', 'order_id');
    }
}
