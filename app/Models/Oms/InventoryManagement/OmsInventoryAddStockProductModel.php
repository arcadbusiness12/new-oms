<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryAddStockProductModel extends Model
{
    protected $table = 'oms_inventory_add_stock_product';
    protected $primaryKey = "add_stock_product_id";

    const FIELD_STOCK_PRODUCT_ID = 'add_stock_product_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_STATUS = 'status';
    const FIELD_ROW = 'row';
    const FIELD_RACK = 'rack';
    const FIELD_SHELF = 'shelf';
}
