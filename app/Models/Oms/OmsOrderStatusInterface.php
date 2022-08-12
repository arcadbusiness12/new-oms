<?php



namespace App\Models\Oms;



/**

 *

 * @author kamran

 */

interface OmsOrderStatusInterface

{



  const OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST = 0;
  
  const OMS_ORDER_STATUS_PACKED = 1;

  const OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED = 2;

  const OMS_ORDER_STATUS_SHIPPED = 3;

  const OMS_ORDER_STATUS_DELEIVERED = 4;

  const OMS_ORDER_STATUS_CANCEL = 5;

  const OMS_ORDER_STATUS_RETURN = 6;

  const OMS_ORDER_STATUS_UNDELEIVERED = 7;


}

