<?php

namespace App\Models\OpenCart\ExchangeOrders;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of OrderOptionsModel
 *
 * @author kamran
 */
class ExchangeOrderOptionsModel extends AbstractOpenCartModel
{

  //put your code here
  protected $table = 'exchange_order_option';
  protected $primaryKey = 'exchange_order_option_id';

  const FIELD_ORDER_OPTION_ID = 'exchange_order_option_id';
  const FIELD_ORDER_ID = 'exchange_order_id';
  const FIELD_ORDER_PRODUCT_ID = 'exchange_order_product_id';
  const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
  const FIELD_PRODUCT_OPTION_VALUE_ID = 'product_option_value_id';
  const FIELD_NAME = 'name';
  const FIELD_VALUE = 'value';
  const FIELD_TYPE = 'type';
}
