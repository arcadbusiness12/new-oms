<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseShippedOrdersTotalModel extends Model
{
    protected $table = 'oms_purchase_shipped_order_total';
    protected $primaryKey = "order_total_id";

    const FIELD_ORDER_TOTAL_ID = 'order_total_id';
    const FIELD_SHIPPED_ORDER_ID = 'shipped_order_id';
    const FIELD_CODE = 'code';
    const FIELD_TITLE = 'title';
    const FIELD_VALUE = 'value';
    const FIELD_SORT_ORDER = 'sort_order';
}
