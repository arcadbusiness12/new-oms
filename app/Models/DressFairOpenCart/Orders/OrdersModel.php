<?php

/*
 * Order table model
 */

namespace App\Models\DressFairOpenCart\Orders;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/**
 * Description of OrdersModel
 *
 * @author kamran
 */
class OrdersModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'order';
  protected $primaryKey = 'order_id';

  const OPEN_CART_STATUS_PROCESSING = 2;
  const OPEN_CART_STATUS_PENDING = 1;
  const OPEN_CART_STATUS_SHIPPED = 3;
  const OPEN_CART_STATUS_CANCELED = 7;
  const OPEN_CART_STATUS_RETURNED = 9;
  const OPEN_CART_STATUS_DELIVERED = 25;
  const OPEN_CART_STATUS_EXCHANGE = 28;
  
  const FIELD_ORDER_STATUS_ID = 'order_status_id';
  const FIELD_ORDER_ID = 'order_id';
  // Customer Details
  const FIELD_CUSTOMER_ID = 'customer_id';
  const FIELD_CUSTOMER_FIRST_NAME = 'shipping_firstname';
  const FIELD_CUSTOMER_LAST_NAME = 'shipping_lastname';
  const FIELD_CUSTOMER_EMAIL = 'email';
  const FIELD_CUSTOMER_MOBILE_NUMBER = 'telephone';
  // Order Details
  const FIELD_PAYMENT_METHOD = 'payment_code';
  const FIELD_ORDER_TOTAL = 'total';
  const FIELD_ORDER_COMMENTS = 'comments';
  const FIELD_DATE_MODIFIED = 'date_modified';
  // Shipping details
  const FIELD_SHIPPING_ZONE = 'shipping_zone';
  const FIELD_SHIPPING_CITY = 'shipping_city';
  const FIELD_SHIPPING_AREA = 'shipping_area';
  const FIELD_SHIPPING_ADDRESS_1 = 'shipping_address_1';
  const FIELD_SHIPPING_ADDRESS_2 = 'shipping_address_2';

  public function status()
  {
    return $this->hasOne(__NAMESPACE__ . '\OrderStatusModel', self::FIELD_ORDER_STATUS_ID, self::FIELD_ORDER_STATUS_ID);
  }

  public function orderd_products()
  {
    return $this->hasMany(__NAMESPACE__ . '\OrderedProductModel', self::FIELD_ORDER_ID, self::FIELD_ORDER_ID)
                    ->with(['product_details', 'order_options']);
  }
  public function orderd_totals()
  {
    return $this->hasMany(__NAMESPACE__ . '\OrderTotalModel', self::FIELD_ORDER_ID, self::FIELD_ORDER_ID);
  }

}
