<?php

namespace App\Models\DressFairOpenCart\Orders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class OrderHistory extends AbstractDressFairOpenCartModel
{

  protected $table = 'order_history';
  protected $primaryKey = 'order_history_id';

  const NOTIFY_CUSTOMER = 1;
  const DONT_NOTIFY_CUSTOMER = 0;
  const FIELD_ORDER_ID = 'order_id';
  const FIELD_ORDER_STATUS_ID = 'order_status_id';
  const FIELD_NOTIFY = 'notify';
  const FIELD_COMMENT = 'comment';
  const FIELD_DATE_ADDED = 'date_added';

}
