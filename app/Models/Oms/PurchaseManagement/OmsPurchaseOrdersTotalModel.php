<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersTotalModel extends Model
{
    protected $table = 'oms_purchase_order_total';
    protected $primaryKey = "order_total_id";

    const FIELD_ORDER_TOTAL_ID = 'order_total_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_CODE = 'code';
    const FIELD_TITLE = 'title';
    const FIELD_VALUE = 'value';
    const FIELD_SORT_ORDER = 'sort_order';

    public function purchaseOrder() {
        return $this->belongsTo(OmsPurchaseOrdersModel::class, 'order_id');
    }
}
