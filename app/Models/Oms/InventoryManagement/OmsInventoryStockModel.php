<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryStockModel extends Model
{
    protected $table = 'oms_inventory_stock';
    protected $primaryKey = "stock_id";

    const FIELD_STOCK_ID = 'stock_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_OPTION_ID = 'option_id';
    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_MINIMUM_QUANTITY = 'minimum_quantity';
    const FIELD_AVERAGE_QUANTITY = 'average_quantity';
    const FIELD_DURATION = 'duration';
}
