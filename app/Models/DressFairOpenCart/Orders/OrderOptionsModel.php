<?php

namespace App\Models\DressFairOpenCart\Orders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of OrderOptionsModel
 *
 * @author kamran
 */
class OrderOptionsModel extends AbstractDressFairOpenCartModel
{

  //put your code here
  protected $table = 'order_option';
  protected $primaryKey = 'order_option_id';

  const FIELD_ORDER_OPTION_ID = 'order_option_id';
  const FIELD_ORDER_ID = 'order_id';
  const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
  const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
  const FIELD_PRODUCT_OPTION_VALUE_ID = 'product_option_value_id';
  const FIELD_NAME = 'name';
  const FIELD_VALUE = 'value';
  const FIELD_TYPE = 'type';

}
