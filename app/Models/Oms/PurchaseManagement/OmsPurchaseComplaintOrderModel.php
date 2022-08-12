<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseComplaintOrderModel extends Model
{
    protected $table = 'oms_purchase_complaint_order';
    protected $primaryKey = "complaint_id";

    const FIELD_COMPLAINT_ID = 'complaint_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SHIPPED_ID = 'shipped_id';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_COMMENT = 'comment';
}
