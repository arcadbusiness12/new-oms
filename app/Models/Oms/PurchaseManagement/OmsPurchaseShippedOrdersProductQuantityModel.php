<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseShippedOrdersProductQuantityModel extends Model
{
    protected $table = 'oms_purchase_shipped_order_product_quantity';
    protected $primaryKey = "order_product_quantity_id";

    const FIELD_ORDER_PRODUCT_QUANTITY_ID = 'order_product_quantity_id';
    const FIELD_SHIPPED_ORDER_ID = 'shipped_order_id';
    const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_RECEIVED_QUANTITY = 'received_quantity';
    const FIELD_PRICE = 'price';
    const FIELD_TOTAL = 'total';
}
