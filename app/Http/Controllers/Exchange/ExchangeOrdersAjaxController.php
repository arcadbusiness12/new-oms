<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrderHistoryModel as DFExchangeOrderHistoryModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrdersModel as DFExchangeOrdersModel;
use App\Models\DressFairOpenCart\Products\OptionDescriptionModel as DFOptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DFProductsModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsCart;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\PaymentMethodModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\OpenCart\ExchangeOrders\ExchangeOrderHistoryModel;
use App\Models\OpenCart\ExchangeOrders\ExchangeOrdersModel;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use Illuminate\Support\Facades\Request AS Input;
use DB;
class ExchangeOrdersAjaxController extends Controller
{
    const VIEW_DIR = 'exchange';
    const PER_PAGE = 20;
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_BAOMS_DATABASE = '';
    private $APP_OPENCART_URL = '';
    private $static_option_id = 0;
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $opencart_image_url = '';
    private $store = '';

    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_BAOMS_DATABASE = env('DB_BAOMS_DATABASE');
        $this->APP_OPENCART_URL = env('APP_OPENCART_URL');
        $this->static_option_id = OmsSettingsModel::get('product_option','color');
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =  isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : ''.'://'. $_SERVER["HTTP_HOST"] .'/image/';
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }
    public function cancelOrder(){
        try{
            $orderId   = Input::get('order_id');
            $oms_store = Input::get('oms_store');
            $exchange_order_id = Input::get('exchange_order_id');
            $orderId_onhold = 0;
            if (strpos($orderId, '-1') !== false) {
              //order id contain -1
              $orderId_onhold = $orderId;
            }else{
              $orderId_onhold = $orderId."-1";
            }
            if ($orderId == ''){
                throw new \Exception("Please select an order to cancel");
            }else{
                // $exchange_order_id = ExchangeOrdersModel::select(ExchangeOrdersModel::FIELD_EXCHANGE_ORDER_ID)->where(ExchangeOrdersModel::FIELD_ORDER_ID,$orderId)->first()->exchange_order_id;
                // Change the OMS Order STATUS TO CANCEL
                $omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->where('store', $oms_store)->first();
                if( $oms_store ==1 ){
                    $openCartOrder = ExchangeOrdersModel::findOrFail($exchange_order_id);
                }else if( $oms_store == 2 ){
                    $openCartOrder = ExchangeOrdersModel::findOrFail($exchange_order_id);
                }
                if ($omsOrder !== null){
                    //UPDATE OMS ORDER STATUS
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
                    $omsOrder->{OmsExchangeOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $omsOrder->save();
                }else {
                    $omsOrder = new OmsExchangeOrdersModel();
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_ORDER_ID} = $orderId;
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
                    $omsOrder->{OmsExchangeOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $omsOrder->store = $oms_store;
                    $omsOrder->save();
                }

                //UPDATE OPENCART STATUS
                if (in_array($openCartOrder->{ExchangeOrdersModel::FIELD_ORDER_STATUS_ID}, array(ExchangeOrdersModel::OPEN_CART_STATUS_PENDING,ExchangeOrdersModel::OPEN_CART_STATUS_PROCESSING))){

                    $openCartOrder->{ExchangeOrdersModel::FIELD_DATE_MODIFIED} = \Carbon\Carbon::now();
                    $openCartOrder->{ExchangeOrdersModel::FIELD_ORDER_STATUS_ID} = ExchangeOrdersModel::OPEN_CART_STATUS_CANCELED;
                    $openCartOrder->save(); // update the order status
					OmsActivityLogModel::newLog($orderId,19,$oms_store); //19 is for cancel Exchange order
                    //UPDATE OPENCART ORDER HISTORY
                    if( $oms_store == 1 ){
                        $orderHistory = new ExchangeOrderHistoryModel();
                    }else if( $oms_store == 2 ){
                        $orderHistory = new DFExchangeOrderHistoryModel();
                    }
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_COMMENT} = "Order canceled from OMS";
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_ORDER_ID} = $exchange_order_id;
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_ORDER_STATUS_ID} = ExchangeOrdersModel::OPEN_CART_STATUS_CANCELED;
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_DATE_ADDED} = \Carbon\Carbon::now();
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_NOTIFY} = ExchangeOrderHistoryModel::NOTIFY_CUSTOMER;
                    $orderHistory->save();
                    //check if order is in onhold
                    $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId_onhold)->where('store',$oms_store)->first();
                    if( $check_on_hold ){
                        $this->availableInventoryQuantity($orderId,$oms_store);
                    //   self::addQuantity($orderId);
                    }

                    return array('success' => true, 'data' => array(), 'error' => array('message' => ''));
                }else{
                    throw new \Exception("Order can't be canceled in this status");
                }
            }
        }
        catch (\Exception $e){
            return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
        }
    }
    public function cancelQuantity(){
        $this->availableInventoryQuantity(1115552,1);
    }
    public function availableInventoryQuantity($order_id,$oms_store = null){
        if( $oms_store == 1 ){
            $orderd_products = ExchangeOrdersModel::with(['orderd_products'])->where(ExchangeOrdersModel::FIELD_ORDER_ID, $order_id)->first();
        }else if( $oms_store == 2 ){
            $orderd_products = DFExchangeOrdersModel::with('orderd_products')->where(ExchangeOrdersModel::FIELD_ORDER_ID,$order_id)->first();
        }
        if($orderd_products->orderd_products){
            foreach ($orderd_products->orderd_products as $key => $product) {
                if( $oms_store == 1 ){
                    $opencart_sku = ProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }else if( $oms_store == 2 ){
                    $opencart_sku = DFProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }
                $exists = OmsInventoryProductModel::where('sku', $opencart_sku->sku)->first();
                if($exists){
                    $product_id = $exists->product_id;
                    if(!empty($exists->size)){
                        $total_quantity = 0;
                        foreach ($product->order_options as $key => $option) {
                            if( $oms_store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                ->where('option_description.name', $option->name)
                                ->where('ovd.name', $option->value)
                                ->first();
							    $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                            }else if( $oms_store == 2 ){

                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                ->where('option_description.name', $option->name)
                                ->where('ovd.name', $option->value)
                                ->first();
							    $ba_color_option_id = OmsInventoryOptionModel::dfColorOptionId();
                            }
                            if($option_data && $option_data->option_id != $ba_color_option_id){
                                $total_quantity = $total_quantity + $product->quantity;
                                $decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $option_data->option_id)->where('option_value_id', $option_data->option_value_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
                            }
                        }

                        $decrement_query = 'IF (onhold_quantity-' . $total_quantity . ' <= 0, 0, onhold_quantity-' . $total_quantity . ')';
                        OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $ba_color_option_id)->where('option_value_id', $exists->color)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $total_quantity) ));
                    }else{
                        foreach ($product->order_options as $key => $option) {
                            if( $oms_store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }else if( $oms_store == 2 ){
                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }

                            if($option_data){
                                $decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $option_data->option_id)->where('option_value_id', $option_data->option_value_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
                            }
                        }
                    }
                    updateSitesStock($opencart_sku->sku);
                }
            }
        }
    }
    //place Exchange code start=========================
    public function addToCart(Request $request){
        // dd($request->all());
            // echo session()->getId();
        $server_session_id = session()->getId();
        $product_id        = $request->product_id;
        $product_option_id = $request->product_option_id;
        $product_sku       = $request->product_sku;
        $product_name      = $request->product_name;
        $product_image      = $request->product_image;
        $product_color      = $request->product_color;
        $product_quantity  = $request->product_quantity;
        $product_price     = $request->product_price;
        $store = $request->store;
        $data = OmsCart::where("session_id",$server_session_id)->where("product_id",$product_id)->where("product_option_id",$product_option_id)->first();
        if($data){
            $request_quantity = ($data->product_quantity + $product_quantity);
            $availabe_quantity = $this->checkAvailableQuantity($data->product_id,$data->product_option_id);
            if( $request_quantity > $availabe_quantity   ){
                return response()->json(['status'=>0,"msg"=>"Quantity not in stock, Available quantity is <strong>$availabe_quantity</strong>"]);
            }
            $data->product_quantity = $request_quantity;
            $data->product_price = $product_price;
            $create_update_cart = $data->save();
        }else{
            $create_update_cart = OmsCart::create([
                "store_id"         => $store,
                "session_id"       =>$server_session_id,
                "product_id"       =>$product_id,
                "product_option_id"=>$product_option_id,
                "product_sku"      =>$product_sku,
                "product_name"     =>$product_name,
                "product_image"    =>$product_image,
                "product_color"    =>$product_color,
                "product_quantity" =>$product_quantity,
                "product_price"    =>$product_price,
                "is_exchange"      => 1
            ]);
        }
        if( $create_update_cart ){
            return response()->json(['status'=>1,"msg"=>"Item addedd to cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error while adding to cart."]);
        }
    }
    public function getCart(Request $request){
        $store   = $request->store;
        $server_session_id = session()->getId();
        $data = OmsCart::with('cartProductSize')->where("session_id",$server_session_id)->where("store_id",$store)->where('is_exchange',1)->get();
        // dd($data->toArray());
        return view('placeExchange.cartview',compact('data'));
    }
    public function updateCart(Request $request){
        $cart_id  = $request->cart_id;
        $quantity = $request->quantity;

        $data = OmsCart::where("id",$cart_id)->first();
        $availabe_quantity = 0;
        if($data){
            $availabe_quantity = $this->checkAvailableQuantity($data->product_id,$data->product_option_id);
        }
        if( $quantity > $availabe_quantity   ){
            return response()->json(['status'=>0,"msg"=>"Quantity not in stock, Available quantity is <strong>$availabe_quantity</strong>"]);
        }

        $data = OmsCart::where("id",$cart_id)->update(['product_quantity'=>$quantity]);
        if($data){
            return response()->json(['status'=>1,"msg"=>"Item updated in cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error, cart not updated"]);
        }
    }
    private function checkAvailableQuantity($product_id,$product_option_id){
        $data = OmsInventoryProductOptionModel::where("product_id",$product_id)->where("product_option_id",$product_option_id)->first();
        if($data){
            return $data->available_quantity;
        }else{
            return 0;
        }
    }
    public function setPaymentMethod(Request $request){
        session()->forget('payment_method');
        $store_id = $request->store_id;
        $request_payment_method = $request->payment_method;
        $data = PaymentMethodModel::select('id','name','fee','fee_label')->where("id",$request_payment_method)->first();
        if( $data ){
            session()->put('payment_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function setShippingMethod(Request $request){
        session()->forget('shipping_method');
        $store_id = $request->store_id;
        $shipping_method = $request->shipping_method;
        $data = ShippingMethodModel::select('id','name','amount')->where("store_id",$store_id)->where('id',$shipping_method)->first();
        if( $data ){
            session()->put('shipping_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function confirm(Request $request){
        $store_id = $request->store_id;
        $comment  = $request->comment;
        $google_map_link     = $request->google_map_link;
        $alternate_number    = $request->alternate_number;
        $shipping_method     = session('exchange_shipping_method');
        $payment_method      = session('exchange_payment_method');
        $server_session_id   = session()->getId();
        $cart_data = OmsCart::with(['product','productOption.option','productOption.optionVal'])->where("session_id",$server_session_id)->where('store_id',$store_id)->get();
        // dd($cart_data->toArray()); die("test");
        if( $store_id < 1 ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"No store selected, or invalid store ID."]);
        }
        if( $cart_data->count() == 0 ){
          return response()->json(['status'=>false,"data"=>'','msg'=>"Your cart is empty"]);
        }
        if( $cart_data->count() > 0 ){
            if( $cart_data[0]->customer_id < 1 ){
                return response()->json(['status'=>false,"data"=>'','msg'=>"Customer details not set in cart"]);
            }
            $customer_data = Customer::with(['defaultAddress.country','defaultAddress.city','defaultAddress.area'])->where('id',$cart_data[0]->customer_id)->first();
        }
        if( !$payment_method ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Payment method missing"]);
        }
        if( !$shipping_method ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Shipping method missing"]);
        }
        if( !$customer_data ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Customer data no found."]);
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
            $order_total_amount = 0;
            if( is_array($totals) && count($totals) > 0 ){
                foreach($totals as $title=>$total){
                    $insert_order_tot           = new OmsOrderTotalModel();
                    $insert_order_tot->order_id = $order_id;
                    $insert_order_tot->title    = $title;
                    $insert_order_tot->value    = $total;
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
            $place_order->alternate_number      = $alternate_number;
            //payment address
            $place_order->payment_country_id     =  $customer_data->defaultAddress->country_id;
            $place_order->payment_city_id        =  $customer_data->defaultAddress->city_id;
            $place_order->payment_city_area_id   =  $customer_data->defaultAddress->area_id;
            $place_order->payment_firstname      =  $customer_data->defaultAddress->firstname;
            $place_order->payment_address_1      =  $customer_data->defaultAddress->address;
            $place_order->payment_street_building=  $customer_data->defaultAddress->street_building;
            $place_order->payment_villa_flat     =  $customer_data->defaultAddress->villa_flat;
            $place_order->payment_city           =  $customer_data->defaultAddress->city->name;
            $place_order->payment_city_area      =  $customer_data->defaultAddress->area->name;
            $place_order->payment_country        =  $customer_data->defaultAddress->country->name;
            // //shipping address
            $place_order->shipping_country_id     = $customer_data->defaultAddress->country_id;
            $place_order->shipping_city_id        = $customer_data->defaultAddress->city_id;
            $place_order->shipping_city_area_id   = $customer_data->defaultAddress->area_id;
            $place_order->shipping_firstname      = $customer_data->defaultAddress->firstname;
            $place_order->shipping_address_1      = $customer_data->defaultAddress->address;
            $place_order->shipping_street_building= $customer_data->defaultAddress->street_building;
            $place_order->shipping_villa_flat     = $customer_data->defaultAddress->villa_flat;
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
        $this->clearSessionAterOrder();
        return response()->json(['status'=>true,"data"=>'','msg'=>"order placed successfully."]);
      }
      private function clearSessionAterOrder(){
        $server_session_id   = session()->getId();
        OmsCart::where('session_id',$server_session_id)->where('is_exchange',1)->delete();
        session()->forget('exchange_shipping_method');
        session()->forget('exchange_payment_method');
      }
}
