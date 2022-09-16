<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseShippedOrdersProductModel extends Model
{
    protected $table = 'oms_purchase_shipped_order_product';
    protected $primaryKey = "order_product_id";

    const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
    const FIELD_SHIPPED_ORDER_ID = 'shipped_order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_NAME = 'name';
    const FIELD_MODEL = 'model';
    const FIELD_TYPE = 'type';

    public function orderProductQuantities() {
        return $this->hasMany(OmsPurchaseShippedOrdersProductQuantityModel::class, 'order_product_id');
    }
}
