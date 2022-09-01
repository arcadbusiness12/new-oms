<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseShippedOrdersModel extends Model
{
    protected $table = 'oms_purchase_shipped_order';
    protected $primaryKey = "shipped_order_id";

    const FIELD_SHIPPED_ORDER_ID = 'shipped_order_id';
    const FIELD_SHIPPED_ID = 'shipped_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_SHIPPED = 'shipped';
    const FIELD_LINK = 'link';
    const FIELD_URGENT = 'urgent';
    const FIELD_SUPPLIER = 'supplier';
    const FIELD_SHIPPING = 'shipping';
    const FIELD_TOTAL = 'total';
    const FIELD_STATUS = 'status';
    const FIELD_DATE_ADDED = 'date_added';
    const FIELD_DATE_MODIFIED = 'date_modified';

    public function orderTotals() {
        return $this->hasMany(OmsPurchaseShippedOrdersTotalModel::class, 'shipped_order_id');
    }

    public function orderProducts() {
        return $this->hasMany(OmsPurchaseShippedOrdersProductModel::class, 'shipped_order_id');
    }

    public function orderProductQuantities() {
        return $this->hasMany(OmsPurchaseShippedOrdersProductQuantityModel::class, 'shipped_order_id');
    }
}
