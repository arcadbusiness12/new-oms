<?php

namespace App\Models\Oms\PurchaseManagement;

use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use Illuminate\Database\Eloquent\Model;

class OmsPurchaseOrdersProductModel extends Model
{
    protected $table = 'oms_purchase_order_product';
    protected $primaryKey = "order_product_id";

    const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_PRODUCT_ID = 'product_id';
    const FIELD_NAME = 'name';
    const FIELD_MODEL = 'model';
    const FIELD_TYPE = 'type';

    public function ProductsSizes() {
        return $this->hasMany(OmsPurchaseOrdersProductOptionModel::class, 'order_product_id');
    }

    public function orderProductQuantities() {
        return $this->hasMany(OmsPurchaseOrdersProductQuantityModel::class, 'order_id', 'order_id');
    }
}
