<?php

namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use Illuminate\Database\Eloquent\Model;
use DB;
class OmsCart extends Model
{
    // protected $table = 'oms_promotion_socials';
    protected $fillable = ['store_id','session_id','product_id','product_option_id','product_sku','product_name','product_image','product_color','product_quantity','product_price','is_exchange'];

    public function cartProductSize(){

        return $this->hasOne('App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel','product_option_id','product_option_id')
        ->join('oms_options_details', 'oms_options_details.id', '=', 'oms_inventory_product_option.option_value_id');
    }
    public function product(){
        return $this->belongsTo(OmsInventoryProductModel::class, 'product_id');
    }
    public function productOption(){
        return $this->belongsTo(OmsInventoryProductOptionModel::class, 'product_option_id');
    }
    public static function getCartTotalProduct($store_id){
        //return only product totals
        $server_session_id = session()->getId();
        return self::where("session_id",$server_session_id)->where("store_id",$store_id)->sum('product_quantity');
    }
    public static function getCartTotalAmount($store_id){
        //return only product totals
        $server_session_id = session()->getId();
        $data = self::select(DB::raw('SUM(product_price * product_quantity) AS total'))->where('session_id',$server_session_id)->where('store_id',$store_id)->first();
        $total = 0;
        if($data){
            $total = $data->total;
        }
        return $total;
    }
}
