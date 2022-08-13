<?php

/*
 * Order table model
 */

namespace App\Models\DressFairOpenCart\Orders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of ReturnOrdersModel
 *
 * @author kamran
 */
class ReturnOrdersModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'return';
  protected $primaryKey = 'return_id';

  public function return_products()
  {
    return $this->hasMany('App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrderReturnProduct','order_id','order_id');
                    // ->with(['product_details', 'order_options']);
  }

}
