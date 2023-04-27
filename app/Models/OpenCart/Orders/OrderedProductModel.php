<?php

namespace App\Models\OpenCart\Orders;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of OrdersProductModel
 *
 * @author kamran
 */
class OrderedProductModel extends AbstractOpenCartModel
{

  protected $table = 'order_product';
  protected $primaryKey = 'order_product_id';

  const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_ORDER_ID = 'order_id';
  const FIELD_ORDER_PRODUCT_NAME = 'name';
  const FIELD_ORDER_PRODUCT_MODEL = 'model';
  const FIELD_ORDER_PRODUCT_QUANTITY = 'quantity';
  const FIELD_ORDER_PRODUCT_REWARD = 'reward';
  const FIELD_ORDER_PRODUCT_PRICE = 'price';
  const FIELD_ORDER_PRODUCT_TOTAL = 'total';
  

  public function product_details()
  {
    return $this->hasOne("App\\Models\\OpenCart\\Products\\ProductsModel", self::FIELD_PRODUCT_ID, self::FIELD_PRODUCT_ID);
  }

  public function order_options()
  {
    return $this->hasMany(__NAMESPACE__ . '\OrderOptionsModel', OrderOptionsModel::FIELD_ORDER_PRODUCT_ID, self::FIELD_ORDER_PRODUCT_ID);
  }

  public function order() {
    return $this->belongsTo(OrdersModel::class, 'order_id', 'order_id');
  }

}
