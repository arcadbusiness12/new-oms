<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersModel extends Model
{
    protected $table = 'oms_purchase_order';
    protected $primaryKey = "order_id";

    const FIELD_ORDER_ID = 'order_id';
    const FIELD_TOTAL = 'total';
    const FIELD_ORDER_STATUS_ID = 'order_status_id';
    const FIELD_LINK = 'link';
    const FIELD_URGENT = 'urgent';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_DATE_ADDED = 'created_at';
    const FIELD_DATE_MODIFIED = 'updated_at';
}
