<?php

/*
 * Order table model
 */

namespace App\Models\OpenCart\Orders;

use App\Models\OpenCart\AbstractOpenCartModel;

/**
 * Description of ReturnOrdersModel
 *
 * @author kamran
 */
class ReturnOrdersModel extends AbstractOpenCartModel
{

  protected $table = 'return';
  protected $primaryKey = 'return_id';

  public function return_products()
  {
    return $this->hasMany('App\Models\OpenCart\ExchangeOrders\ExchangeOrderReturnProduct','order_id','order_id');
                    // ->with(['product_details', 'order_options']);
  }
}
