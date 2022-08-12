<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersStatusHistoryModel extends Model
{
    protected $table = 'oms_purchase_order_status_history';
    protected $primaryKey = "status_history_id";

    const FIELD_STATUS_HISTORY_ID = 'status_history_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_ORDER_STATUS_ID = 'order_status_id';
}
