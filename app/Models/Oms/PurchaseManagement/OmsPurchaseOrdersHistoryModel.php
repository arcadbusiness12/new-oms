<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersHistoryModel extends Model
{
    protected $table = 'oms_purchase_order_history';
    protected $primaryKey = "order_history_id";

    const FIELD_ORDER_HISTORY_ID = 'order_history_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_NAME = 'name';
    const FIELD_COMMENT = 'comment';
    const FIELD_DATE_ADDED = 'date_added';
}
