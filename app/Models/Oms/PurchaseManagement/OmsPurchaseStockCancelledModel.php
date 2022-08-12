<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseStockCancelledModel extends Model
{
    protected $table = 'oms_purchase_stock_cancelled';
    protected $primaryKey = "cancelled_id";

    const FIELD_CANCELLED_ID = 'cancelled_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_STATUS = 'status';
}
