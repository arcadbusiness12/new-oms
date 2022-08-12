<?php

namespace App\Models\Oms\Purchase;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of OmsOrdersModel
 *
 * @author kamran
 */
class PurchaseOrderModel extends Model
{

  protected $table = 'purchase_orders';
  protected $primaryKey = "po_id";

  const FIELD_PO_ID = 'po_id';
  const FIELD_SUPPLIER_ID = 'supplier_id';
  const FIELD_TOTAL_ITEMS = 'total_items';
  const FIELD_TOTAL_AMOUNT = 'total_amount';
  const FIELD_PO_STATUS = 'po_status'; // Shipped / Processing / etc
  const FIELD_INVOICE_REFERENCE = 'invoice_reference';
  const FIELD_PO_COMPLETED = 'po_completed'; // Po Inventory received status // all / partial etc
  const FIELD_PO_PAYMENT_DONE = 'po_payment_done'; // Paid to supplier 
  const FIELD_PO_SHIPPED_THROUGH_PROVIDER = 'po_shipped_through_provider';
  const FIELD_PO_SHIPPING_TRACKING_NUMBER = 'po_shipping_tracking_number';

  public function products()
  {
    return $this->hasMany(__NAMESPACE__ . '\PurchaseOrderItemsModel', PurchaseOrderItemsModel::FIELD_PO_ID, self::FIELD_PO_ID)
                    ->with(['options']);
  }

}
