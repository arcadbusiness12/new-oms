<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryAddStockOptionModel extends Model
{
    protected $table = 'oms_inventory_add_stock_option';
    protected $primaryKey = "add_stock_option_id";

    const FIELD_STOCK_ID = 'add_stock_option_id';
    const FIELD_HISTORY_ID = 'history_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_ADD_STOCK_PRODUCT_ID = 'add_stock_product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_QUANTITY = 'quantity';
}
