<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class ExchangeReason extends Model
{
    // protected $table = 'oms_exchange_orders';
    // protected $primaryKey = "oms_exchange_order_id";
    public $timestamps = false;

  //   public function shipping_provider(){
  //       return $this->hasOne(__NAMESPACE__ . '\ShippingProvidersModel', ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID, self::FIELD_LAST_SHIPPED_WITH_PROVIDER);
  //   }

  //   public function airway_bills(){
  //       return $this->hasMany(__NAMESPACE__ . '\ExchangeAirwayBillTrackingModel', self::FIELD_ORDER_ID, self::FIELD_ORDER_ID)
  //                   ->orderBy(Model::CREATED_AT, 'desc')
  //                   ->with(['shipping_provider']);
  //   }
  //   public static function shippingName($order_id){
  //     $data = self::with('shipping_provider')->where('order_id',$order_id)->where('last_shipped_with_provider','>',0)->first();
  //     if(!empty($data)){
  //       $shipping_name = $data->shipping_provider->name;
  //     }else{
  //       $shipping_name = "";
  //     }
  //     return $shipping_name;
  // }
}