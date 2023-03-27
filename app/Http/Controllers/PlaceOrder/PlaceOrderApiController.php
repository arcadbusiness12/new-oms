<?php
namespace App\Http\Controllers\PlaceOrder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\City;
use App\Models\Oms\CityArea;
use App\Models\Oms\Country;
use App\Models\Oms\Customer;
use App\Models\Oms\CustomerAddress;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\DutyAssignedUserModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsBaOrderCounterModel;
use App\Models\Oms\OmsCart;
use App\Models\Oms\OmsDfOrderCounterModel;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\OmsOrderTotalModel;
use App\Models\Oms\PaymentMethodModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\Oms\storeModel;
use App\Providers\Reson8SmsServiceProvider;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Session;
use Validator;
use Excel;

class PlaceOrderApiController extends Controller
{

    const VIEW_DIR = 'placeOrder.';
    const PER_PAGE = 20;

    function __construct(){
    }
  public function saveCustomer($order_rec,$store_id){
    // dd($request->all());
    $json = array();
    if( $store_id < 1 ){
        return response()->json(['status'=>false,'Invalid store.']);
    }
    $country_id     = 221;
    $city_name = ($order_rec->shipping_zone != "" ) ? $order_rec->shipping_zone : $order_rec->payment_zone;
    $city_data = City::where("name",$city_name)->where('country_id',$country_id)->first();

    $area_name = ($order_rec->shipping_area != "" ) ? $order_rec->shipping_area : $order_rec->payment_area;
    $area_data = CityArea::where("name",$city_name)->where('country_id',$country_id)->first();

    $customer_id    = $order_rec->customer_id;
    $firstname      = $order_rec->firstname;
    $email          = $order_rec->email;
    $telephone      = $order_rec->telephone;
    $city_id        = ($city_data) ? $city_data->id : 0;
    $area_id        = $order_rec->area_id;
    $gmap_link      = $order_rec->gmap_link;
    $address        = $order_rec->address;
    ///
    $telephone =  str_replace("+","",$telephone);

    if( $customer_id && $customer_id > 0 ){
        //existing customer update
        $data = Customer::where('id',$customer_id)->first();
        $data->firstname = $firstname;
        if( $data->save() ){
          $add_data =   CustomerAddress::where('id',$data->customer_address_id)->first();
          if( $add_data ){
            $add_data->firstname = $firstname;
            $add_data->address = $address;
            $add_data->street_building = $address_street_building;
            $add_data->villa_flat = $address_villa_flate;
            $add_data->country_id = $country_id;
            $add_data->city_id    = $city_id;
            $add_data->area_id    = $area_id;
            $add_data->save();
          }
        }

    }else{
         //customer record not exist create new
        $data = new Customer();
        $data->store_id  =   $store_id;
        $data->firstname =  $firstname;
        $data->mobile    =  $full_telephone;
        $data->email     =  $email;
        $data->status    =  1;
        if( $data->save() ){
            $customer_id = $data->id;
            $cust_add = new CustomerAddress();
            $cust_add->customer_id  = $customer_id;
            $cust_add->firstname = $firstname;
            $cust_add->address = $address;
            // $cust_add->street_building = $address_street_building;
            // $cust_add->villa_flat = $address_villa_flate;
            $cust_add->country_id = $country_id;
            $cust_add->city_id    = $city_id;
            $cust_add->area_id    = $area_id;
            if($cust_add->save()){
                $customer_address_lastid = $cust_add->id;
                Customer::where("id",$customer_id)->update(['customer_address_id'=> $customer_address_lastid]);
            }
        }
    }
  }
 public function fetchOrderFromBA(){
    // die("in function ");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://businessarcade.com/index.php?route=rest/order/returnOrders");
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS,
    //             "postvar1=value1&postvar2=value2&postvar3=value3");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);
    $order_data = json_decode($server_output);
    // count($order_data);
    foreach($order_data as $order){
        $exist_order = DB::table("oms_ba_order_counters")->where("order_id_in_store",$order->order_id)->first();
        if( !$exist_order ){
         $this->directPlaceOrder($order,1);
        }
    }
    // $this->directPlaceOrder();
}
public function directPlaceOrder($order_rec,$store_id){
    $store_id = $store_id;
    $comment  = "";
    $google_map_link     = "";
    $alternate_number    = "";
    if( $store_id < 1 ){
        // return response()->json(['status'=>false,"data"=>'','msg'=>"No store selected, or invalid store ID."]);
        echo "No store selected, or invalid store ID.";
    }
    $customer_mobile = str_replace("+","",$order_rec->telephone);
    $customer_data = Customer::with(['defaultAddress.country','defaultAddress.city','defaultAddress.area'])->where('mobile',$customer_mobile)->first();

    if( !$customer_data ){
        $this->saveCustomer($order_rec);
    }
    // dd( $payment_method );
    // dd($customer_data->toArray());
    //first entry in store counter tabel
    DB::beginTransaction();
    try {

        if( $store_id == 1 ){
            $order_counter = OmsBaOrderCounterModel::create();
            $order_id =  $order_counter->id;
        }else if( $store_id == 2 ){
            $order_counter = OmsDfOrderCounterModel::create();
            $order_id =  $order_counter->id;
        }
        //entry in total table
        $totals = $this->formatTotal($store_id);
        echo "<pre>";print_r($totals);
        $order_total_amount = 0;
        if( is_array($totals) && count($totals) > 0 ){
            foreach($totals as $total){
                $insert_order_tot           = new OmsOrderTotalModel();
                $insert_order_tot->store_id = $store_id;
                $insert_order_tot->title    = $total['title'];
                $insert_order_tot->value    = $total['value'];
                $insert_order_tot->order_id = $order_id;
                if( $insert_order_tot->save() ){
                    $order_total_amount += $total;
                }
            }

        }

        //entry in place order table
        $place_order = new OmsPlaceOrderModel();
        $place_order->order_id    = $order_id;
        $place_order->user_id     = session('user_id');
        $place_order->store       = $store_id;
        $place_order->customer_id = $customer_data->id;
        $place_order->firstname   = $customer_data->firstname;
        $place_order->email       = $customer_data->email;
        $place_order->mobile      = $customer_data->mobile;
        // $place_order->alternate_number      = $alternate_number;
        $country_id     = 221;
        $city_name = ($order_rec->shipping_zone != "" ) ? $order_rec->shipping_zone : $order_rec->payment_zone;
        $city_data = City::where("name",$city_name)->where('country_id',$country_id)->first();

        $area_name = ($order_rec->shipping_area != "" ) ? $order_rec->shipping_area : $order_rec->payment_area;
        $area_data = CityArea::where("name",$city_name)->first();
        //payment address
        $place_order->payment_country_id     =  $country_id;
        $place_order->payment_city_id        =  ($city_data) ? $city_data->id : 0;
        $place_order->payment_city_area_id   =  $customer_data->defaultAddress->area_id;
        $place_order->payment_firstname      =  $customer_data->defaultAddress->firstname;
        $place_order->payment_address_1      =  $customer_data->defaultAddress->address;
        // $place_order->payment_street_building=  $customer_data->defaultAddress->street_building;
        // $place_order->payment_villa_flat     =  $customer_data->defaultAddress->villa_flat;
        $place_order->payment_city           =  $city_name;
        $place_order->payment_city_area      =  $customer_data->defaultAddress->area->name;
        $place_order->payment_country        =  $customer_data->defaultAddress->country->name;
        // //shipping address
        $place_order->shipping_country_id     = $country_id;
        $place_order->shipping_city_id        = ($city_data) ? $city_data->id : 0;
        $place_order->shipping_city_area_id   = $customer_data->defaultAddress->area_id;
        $place_order->shipping_firstname      = $customer_data->defaultAddress->firstname;
        $place_order->shipping_address_1      = $customer_data->defaultAddress->address;
        // $place_order->shipping_street_building= $customer_data->defaultAddress->street_building;
        // $place_order->shipping_villa_flat     = $customer_data->defaultAddress->villa_flat;
        $place_order->shipping_city           = $customer_data->defaultAddress->city->name;
        $place_order->shipping_city_area      = $customer_data->defaultAddress->area->name;
        $place_order->shipping_country        = $customer_data->defaultAddress->country->name;
        //payment method
        $place_order->payment_method_id       = $payment_method['id'];
        $place_order->payment_method_name     = $payment_method['name'];
        //general statuses
        $place_order->total_amount     = $order_total_amount;
        $place_order->comment          = $comment;
        $place_order->google_map_link  = $google_map_link ;
        $place_order->online_approved  = 1;
        $place_order->reseller_approve = 1;
        $place_order->save();
        //insertion in oms_order_products table
        if($cart_data){
            foreach($cart_data as $key => $cart_item){
                $cart_quantity = $cart_item->product_quantity;
                $cart_option_name = $cart_item->productOption->option->option_name;
                $cart_option_value = $cart_item->productOption->optionVal->value;
                if( $cart_quantity > $cart_item->productOption->available_quantity ){
                    throw new \Exception("Desired quanity not available for ".$cart_item->product_sku." ".$cart_option_name.": ".$cart_option_value);
                }
                $insert_order_products = new OmsOrderProductModel();
                $insert_order_products->order_id   = $order_id;
                $insert_order_products->product_id = $cart_item->product_id;
                $insert_order_products->store_id   = $cart_item->store_id;
                $insert_order_products->name       = $cart_item->product_name;
                $insert_order_products->sku        = $cart_item->product_sku;
                $insert_order_products->quantity   = $cart_quantity;
                $insert_order_products->price      = $cart_item->product_price;
                $insert_order_products->total      = ($cart_quantity * $cart_item->product_price);
                $insert_order_products->product_option_id = $cart_item->product_option_id;
                $insert_order_products->option_name  = $cart_option_name;
                $insert_order_products->option_value = $cart_option_value;
                $insert_order_products->save();
            }
        }
        $this->onHoldQuantity($order_id,$store_id);
        OmsActivityLogModel::newLog($order_id,1,$store_id); // 1 for place order
         DB::commit();
    } catch (\Exception $e) {
        DB::rollback();
        // something went wrong
        return response()->json(['status'=>false,"data"=>'','msg'=>$e->getMessage()]);
    }
    return response()->json(['status'=>true,"data"=>'','msg'=>"order placed successfully."]);
  }
  public function onHoldQuantity($order_id,$store_id){
    $order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
    if( $order_products ){
        $order_quantity = 0;
        foreach( $order_products as $key => $order_product ){
            $order_quantity = $order_product->quantity;
            $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
            ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
            if( !$check_exist ){
                $new_onhold = new OmsInventoryOnholdQuantityModel();
                $new_onhold->order_id        = $order_product->order_id;
                $new_onhold->product_id      = $order_product->product_id;
                $new_onhold->option_id       = $order_product->productOption->option_id;
                $new_onhold->option_value_id = $order_product->productOption->option_value_id;
                $new_onhold->quantity        = $order_quantity;
                $new_onhold->store           = $order_product->store_id;
                if( $new_onhold->save() ){
                    $decrement_query = 'IF (available_quantity-' . $order_quantity . ' <= 0, 0, available_quantity-' . $order_quantity . ')';
                    OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                    ->update(['available_quantity'=>DB::raw($decrement_query),'onhold_quantity'=>DB::raw("onhold_quantity + $order_quantity")]);
                }
            }
        }
    }
    // dd($order_products->toArray());
  }
}
