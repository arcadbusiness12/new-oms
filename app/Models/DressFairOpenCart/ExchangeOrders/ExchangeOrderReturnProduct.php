<?php
/*
 * Exchange Order table model
 */
namespace App\Models\DressFairOpenCart\ExchangeOrders;
use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;
use App\Models\DressFairOpenCart\Orders\OrderedProductModel;

/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class ExchangeOrderReturnProduct extends AbstractDressFairOpenCartModel{
    protected $table = 'exchange_order_return_product';
    protected $primaryKey = 'exchange_order_return_product_id';

    const FIELD_ORDER_RETURN_PRODUCT_ID = 'exchange_order_return_product_id';
    const FIELD_ORDER_ID = 'order_id';
    const FIELD_ORDER_PRODUCT_ID = 'order_product_id';
    const FIELD_ORDER_QUANTITY = 'quantity';

    public function orderd_products(){
        return $this->hasMany(__NAMESPACE__ . '\ExchangeOrderProductModel', self::FIELD_ORDER_ID, self::FIELD_ORDER_ID)
                    ->with(['product_details', 'order_options']);
    }
}