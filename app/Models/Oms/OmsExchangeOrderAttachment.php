<?php
namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OmsExchangeOrderAttachment extends Model
{
    // protected $table = 'oms_exchange_orders';
    // protected $primaryKey = "oms_exchange_order_id";
    public $timestamps = false;

      public function exchangeReason(){
          return $this->hasOne(__NAMESPACE__ . '\ExchangeReason', 'id','exchange_reason_id');
      }
      public function getCustomerChatImageAttribute($value)
      {
        return asset(Storage::url($value));
      }
      public function getProductImageAttribute($value)
      {
        return asset(Storage::url($value));
      }

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