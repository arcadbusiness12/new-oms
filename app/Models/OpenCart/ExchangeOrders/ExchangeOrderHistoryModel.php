<?php
namespace App\Models\OpenCart\ExchangeOrders;

use App\Models\OpenCart\AbstractOpenCartModel;

class ExchangeOrderHistoryModel extends AbstractOpenCartModel
{
  protected $table = 'exchange_order_history';
  protected $primaryKey = 'exchange_order_history_id';

  const NOTIFY_CUSTOMER = 1;
  const DONT_NOTIFY_CUSTOMER = 0;
  const FIELD_ORDER_ID = 'exchange_order_id';
  const FIELD_ORDER_STATUS_ID = 'exchange_order_status_id';
  const FIELD_NOTIFY = 'notify';
  const FIELD_COMMENT = 'comment';
  const FIELD_DATE_ADDED = 'date_added';
}