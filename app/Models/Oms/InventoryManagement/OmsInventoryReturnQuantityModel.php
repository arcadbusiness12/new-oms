<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryReturnQuantityModel extends Model
{
    protected $table = 'oms_inventory_return_quantity';
    protected $primaryKey = "return_id";

    const FIELD_RETURN_ID = 'return_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_OMS_PRODUCT_ID = 'oms_product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_STORE = 'store';
}
