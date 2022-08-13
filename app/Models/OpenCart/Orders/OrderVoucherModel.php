<?php

namespace App\Models\OpenCart\Orders;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of OrdersProductModel
 *
 * @author kamran
 */
class OrderVoucherModel extends AbstractOpenCartModel
{

  protected $table = 'order_voucher';
  protected $primaryKey = 'order_voucher_id';

  const FIELD_ORDER_ID = 'order_id';
  const FIELD_VOUCHER_ID = 'voucher_id';

}
