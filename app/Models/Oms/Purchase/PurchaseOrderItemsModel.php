<?php

namespace App\Models\Oms\Purchase;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class PurchaseOrderItemsModel extends Model
{

  protected $table = 'purchase_order_items';
  protected $primaryKey = "purchase_order_product_id";

  const FIELD_PURCHASE_ORDER_PRODUCT_ID = 'purchase_order_product_id';
  const FIELD_PO_ID = 'po_id';
  const FIELD_PRODUCT_NAME = 'product_name';
  const FIELD_MODEL = 'model';
  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_QTY = 'qty';
  const FIELD_COST_PRICE = 'cost_price';
  const FIELD_LOCAL_EXPRESS_COST = 'local_express_cost';
  const FIELD_PRODUCT_LINK = 'product_link';

  public function options()
  {
    return $this->hasMany(__NAMESPACE__ . '\PurchaseOrderItemsOption', PurchaseOrderItemsOption::FIELD_PURCHASE_ORDER_PRODUCT_ID, self::FIELD_PURCHASE_ORDER_PRODUCT_ID);
  }

}
