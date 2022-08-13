<?php
/*
 * Exchange Order table model
 */
namespace App\Models\OpenCart\ExchangeOrders;
use App\Models\OpenCart\AbstractOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class ExchangeOrdersModel extends AbstractOpenCartModel{
    protected $table = 'exchange_order';
    protected $primaryKey = 'exchange_order_id';

    const OPEN_CART_STATUS_PROCESSING = 2;
    const OPEN_CART_STATUS_PENDING = 1;
    const OPEN_CART_STATUS_SHIPPED = 3;
    const OPEN_CART_STATUS_CANCELED = 7;
    const OPEN_CART_STATUS_RETURNED = 9;
    const OPEN_CART_STATUS_DELIVERED = 25;
    const OPEN_CART_STATUS_EXCHANGE = 28;

    const FIELD_ORDER_STATUS_ID = 'order_status_id';
    const FIELD_EXCHANGE_ORDER_STATUS_ID = 'exchange_order_status_id';
    const FIELD_EXCHANGE_ORDER_ID = 'exchange_order_id';
    const FIELD_ORDER_ID = 'order_id';
    // Customer Details
    const FIELD_CUSTOMER_ID = 'customer_id';
    const FIELD_FIRST_NAME = 'firstname';
    const FIELD_LAST_NAME = 'lastname';
    const FIELD_CUSTOMER_EMAIL = 'email';
    const FIELD_CUSTOMER_MOBILE_NUMBER = 'telephone';
    // Order Details
    const FIELD_PAYMENT_METHOD = 'payment_code';
    const FIELD_ORDER_TOTAL = 'total';
    const FIELD_ORDER_COMMENTS = 'comment';
    const FIELD_DATE_MODIFIED = 'date_modified';
    // Payment details
    const FIELD_PAYMENT_FIRST_NAME = 'payment_firstname';
    const FIELD_PAYMENT_LAST_NAME = 'payment_lastname';
    const FIELD_PAYMENT_ZONE = 'payment_zone';
    const FIELD_PAYMENT_CITY = 'payment_city';
    const FIELD_PAYMENT_ADDRESS_1 = 'payment_address_1';
    const FIELD_PAYMENT_ADDRESS_2 = 'payment_address_2';
    // Shipping details
    const FIELD_CUSTOMER_FIRST_NAME = 'shipping_firstname';
    const FIELD_CUSTOMER_LAST_NAME = 'shipping_lastname';
    const FIELD_SHIPPING_ZONE = 'shipping_zone';
    const FIELD_SHIPPING_CITY = 'shipping_city';
    const FIELD_SHIPPING_ADDRESS_1 = 'shipping_address_1';
    const FIELD_SHIPPING_ADDRESS_2 = 'shipping_address_2';

    public function status(){
        return $this->hasOne('\\App\\Models\\OpenCart\\Orders\\OrderStatusModel', self::FIELD_ORDER_STATUS_ID, self::FIELD_ORDER_STATUS_ID);
    }

    public function orderd_products(){
        return $this->hasMany(__NAMESPACE__ . '\ExchangeOrderProductModel', self::FIELD_EXCHANGE_ORDER_ID, self::FIELD_EXCHANGE_ORDER_ID)
                    ->with(['product_details', 'order_options']);
    }
}