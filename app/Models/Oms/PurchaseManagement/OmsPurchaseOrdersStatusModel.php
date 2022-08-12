<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersStatusModel extends Model
{
    protected $table = 'oms_purchase_order_status';
    protected $primaryKey = "order_status_id";

    const FIELD_ORDER_STATUS_ID = 'order_status_id';
    const FIELD_NAME = 'name';
}
