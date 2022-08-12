<?php

namespace App\Models\Oms\PurchaseManagement;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseShippedOrdersProductOptionModel extends Model
{
    protected $table = 'oms_purchase_shipped_order_product_option';
    protected $primaryKey = "order_product_option_id";

    const FIELD_ORDER_PRODUCT_OPTION_ID = 'order_product_option_id';
    const FIELD_ORDER_PRODUCT_QUANTITY_ID = 'order_product_quantity_id';
    const FIELD_SHIPPED_ORDER_ID = 'shipped_order_id';
    const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
    const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
    const FIELD_PRODUCT_OPTION_VALUE_ID = 'product_option_value_id';
    const FIELD_NAME = 'name';
    const FIELD_VALUE = 'value';
}
