<?php

namespace App\Models\DressFairOpenCart\Orders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of OrdersProductModel
 *
 * @author kamran
 */
class OrderVoucherModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'order_voucher';
  protected $primaryKey = 'order_voucher_id';

  const FIELD_ORDER_ID = 'order_id';
  const FIELD_VOUCHER_ID = 'voucher_id';

}
