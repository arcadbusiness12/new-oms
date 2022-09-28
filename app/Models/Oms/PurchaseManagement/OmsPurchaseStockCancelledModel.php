<?php

namespace App\Models\Oms\PurchaseManagement;

use App\Models\Oms\OmsUserModel;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseStockCancelledModel extends Model
{
    protected $table = 'oms_purchase_stock_cancelled';
    protected $primaryKey = "cancelled_id";

    const FIELD_CANCELLED_ID = 'cancelled_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SHIPPED_ORDER_ID = 'shiped_order_id';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_STATUS = 'status';

    public function shippedOrder() {
        return $this->hasOne(OmsPurchaseShippedOrdersModel::class, 'shipped_id', 'shiped_order_id');
    }

    public function purchasedOrder() {
        return $this->hasOne(OmsPurchaseOrdersModel::class, 'order_id', 'order_id');
    }

    public function orderProducts() {
        return $this->hasMany(OmsPurchaseShippedOrdersProductModel::class, 'shiped_order_id', 'shipped_order_id');
    }

    public function orderSupplier() {
        return $this->belongsTo(OmsUserModel::class, 'supplier');
    }
}
