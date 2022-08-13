<?php

/*
 * Opencart order status model
 */

namespace App\Models\DressFairOpenCart\ExchangeOrders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of OrderStatus
 *
 * @author kamran
 */
class ExchangeOrderStatusModel extends AbstractDressFairOpenCartModel
{
	protected $table = 'exchange_order_status';
	protected $primaryKey = 'exchange_order_status_id';
	
	const FIELD_ORDER_STATUS_ID = 'exchange_order_status_id';
}
