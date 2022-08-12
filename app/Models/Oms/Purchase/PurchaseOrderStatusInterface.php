<?php

namespace App\Models\Oms\Purchase;

/**
 *
 * @author kamran
 */
interface PurchaseOrderStatusInterface
{

  const PURCHASE_STATUS_PROCESSING = 0;
  const PURCHASE_STATUS_SHIPPED = 1;
  const PURCHASE_STATUS_DELEIVERD = 2;

}
