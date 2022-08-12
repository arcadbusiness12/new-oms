<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryDeliveredQuantityModel extends Model
{
    protected $table = 'oms_inventory_delivered_quantity';
    protected $primaryKey = "delivered_id";

    const FIELD_DELIVERED_ID = 'delivered_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_STORE = 'store';
}
