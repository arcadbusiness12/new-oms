<?php

namespace App\Models\Oms\PurchaseManagement;

use App\Models\Oms\OmsUserModel;
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

    public function orderProducts() {
        return $this->hasMany(OmsPurchaseOrdersProductModel::class, 'order_id');
    }
    public function orderTotals() {
        return $this->hasMany(OmsPurchaseOrdersTotalModel::class, 'order_id');
    }
    public function orderHistories() {
        return $this->hasMany(OmsPurchaseOrdersHistoryModel::class, 'order_id');
    }
    public function orderProductQuantities() {
        return $this->hasMany(OmsPurchaseOrdersProductQuantityModel::class, 'order_id');
    }
    public function shippedOrders() {
        return $this->hasMany(OmsPurchaseShippedOrdersModel::class, 'order_id');
    }
    public function orderSupplier() {
        return $this->belongsTo(OmsUserModel::class, 'supplier');
    }
    public function orderStatus() {
        return $this->belongsTo(OmsPurchaseOrdersStatusModel::class, 'order_status_id');
    }
}
