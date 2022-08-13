<?php

/*
 * Opencart order status model
 */

namespace App\Models\OpenCart\ExchangeOrders;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of OrderStatus
 *
 * @author kamran
 */
class ExchangeOrderStatusModel extends AbstractOpenCartModel
{
	protected $table = 'exchange_order_status';
	protected $primaryKey = 'exchange_order_status_id';
	
	const FIELD_ORDER_STATUS_ID = 'exchange_order_status_id';
}
