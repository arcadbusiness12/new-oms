<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

/**
 * Description of AirwayBillTrackingModel
 *
 * @author kamran
 */
class AirwayBillTrackingModel extends Model
{

  protected $table = 'airwaybill_tracking';

  const FIELD_OMS_ORDER_ID = 'oms_order_id';
  const FIELD_ORDER_ID = 'order_id';
  const FIELD_SHIPPING_PROVIDER_ID = 'shipping_provider_id';
  const FIELD_AIRWAY_BILL_NUMBER = 'airway_bill_number';
  const FIELD_AIRWAY_BILL_CREATION_ATTEMPT = 'airway_bill_creation_attempt';

  public function shipping_provider()
  {
    return $this->hasOne(__NAMESPACE__ . '\ShippingProvidersModel', self::FIELD_SHIPPING_PROVIDER_ID, ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID);
  }
  
  public function awb_tracking()
  {
    return $this->hasOne(__NAMESPACE__.'\AirwayBillTrackingModel',self::FIELD_ORDER_ID, self::FIELD_ORDER_ID);
  }

}
