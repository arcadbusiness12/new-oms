<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseAwaitingActionCancelledModel extends Model
{
    protected $table = 'oms_purchase_awaiting_action_cancelled';
    protected $primaryKey = "cancelled_id";

    const FIELD_CANCELLED_ID = 'cancelled_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_REASON = 'reason';
    const FIELD_STATUS = 'status';
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';
}
