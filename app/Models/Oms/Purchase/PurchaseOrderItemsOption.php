<?php

namespace App\Models\Oms\Purchase;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class PurchaseOrderItemsOption extends Model
{

  protected $table = 'purchase_order_items_options';
  protected $primaryKey = "id";

  const FIELD_ID = 'id';
  const FIELD_PURCHASE_ORDER_PRODUCT_ID = 'purchase_order_product_id';
  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
  const FIELD_PRODUCT_OPTION_VALUE_ID = 'product_option_value_id';
  const FIELD_PRODUCT_OPTION_NAME = 'product_option_name';
  const FIELD_PRODUCT_OPTION_VALUE = 'product_option_value';
  const FIELD_PRODUCT_OPTION_TYPE = 'product_option_type';

}
