<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\ReturnAirwayBillTrackingModel;

class OmsReturnOrdersModel extends Model
{
    protected $table = 'oms_return_orders';
    protected $primaryKey = "oms_return_order_id";

    const FIELD_OMS_ORDER_ID = "oms_return_order_id";
    const FIELD_ORDER_ID = "order_id";
    const FIELD_OMS_ORDER_STATUS = "oms_order_status";
    const FIELD_LAST_SHIPPED_WITH_PROVIDER = 'last_shipped_with_provider';
    const FIELD_PICKLIST_PRINT = "picklist_print";
    const FIELD_CREATED_AT = "created_at";
    const FIELD_UPDATED_AT = "updated_at";

    public function shipping_provider(){
        return $this->hasOne(__NAMESPACE__ . '\ShippingProvidersModel', ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID, self::FIELD_LAST_SHIPPED_WITH_PROVIDER);
    }

    public function airway_bills(){
        return $this->hasMany(ReturnAirwayBillTrackingModel::class, self::FIELD_ORDER_ID, self::FIELD_ORDER_ID)
                    ->orderBy(Model::CREATED_AT, 'DESC')
                    ->with(['shipping_provider']);
    }
}
