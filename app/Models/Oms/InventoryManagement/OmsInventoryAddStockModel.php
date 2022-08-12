<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryAddStockModel extends Model
{
    protected $table = 'oms_inventory_add_stock';
    protected $primaryKey = "add_stock_id";

    const FIELD_STOCK_ID = 'add_stock_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_PRINT_LABEL = 'print_label';
}
