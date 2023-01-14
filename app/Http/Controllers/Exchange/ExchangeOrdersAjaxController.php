<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsExchangeProductModel;
use App\Models\Oms\OmsExchangeTotalModel;
use App\Models\Oms\OmsPlaceExchangeModel;
use App\Models\Oms\Customer;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsCart;
use App\Models\Oms\OmsExchangeOrderAttachment;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsExchangeReturnProductModel;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\PaymentMethodModel;
use App\Models\Oms\ShippingMethodModel;
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
    public function forwardOrderToQueueForAirwayBillGeneration() {
		// dd(RequestFacad::all());
		$courierId = Input::get('courier_id');
		// $courierId = null;
        $store = Input::get('oms_store');
		// if($courierId) {
			try
			{
				if (null == Input::get('order_id')) {
					throw new \Exception("Order Id is Empty");
				}
				$orderID = Input::get('order_id');

				$order_data = OmsPlaceExchangeModel::with(['exchangeProducts','omsExchange'=>function($q) use($store){
                    $q->where('store',$store);
                }])->where("order_id", $orderID)->where("store",$store)->first();
				// echo "d<pre>"; print_r($omsOrderDetails); die;

				if ( $order_data->omsOrder && $order_data->omsExchange->order_id > 0 ) {
					throw new \Exception("Exchange Already Processed");
				}

				// If qty not available then dont go ahead
				$not_in_stock = false;
				$product_data = array();
					$omsOrder = new OmsExchangeOrdersModel();
					$omsOrder->oms_order_status = 0;
					$omsOrder->order_id         = $orderID;
					$omsOrder->last_shipped_with_provider = 0;
					$omsOrder->store = $store;
					$omsOrder->save(); // Save the record and start processing of order.

					OmsActivityLogModel::newLog($orderID,12,$store); //12 is for pick exchange
				return;
			} catch (\Exception $ex) {
				return $ex->getMessage();
			}
		// }else {
		// 	return response()->json([
		// 		'status' => false,
		// 		'courier' => 'no'
		// 	]);
		// }
	}
    public function cancel(){
        try{
            $orderId   = Input::get('order_id');
            $oms_store = Input::get('oms_store');
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
                // Change the OMS Order STATUS TO CANCEL
                $omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->where('store', $oms_store)->first();

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

                OmsActivityLogModel::newLog($orderId,19,$oms_store); //19 is for cancel Exchange order

                //check if order is in onhold
                $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId_onhold)->where('store',$oms_store)->first();
                if( $check_on_hold ){
                    $this->availableInventoryQuantity($orderId,$oms_store);
                //   self::addQuantity($orderId);
                }

                // return array('success' => true, 'data' => array(), 'error' => array('message' => ''));
            }
        }
        catch (\Exception $e){
            return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
        }
    }
    public function cancelQuantity(){
        $this->availableInventoryQuantity(1115552,1);
    }
    public function availableInventoryQuantity($order_id,$store_id){
        $order_id_dash = $order_id."-1";
        $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_id_dash)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( $check_exist ){
                    OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                    ->update(['available_quantity'=>DB::raw("available_quantity + $order_quantity"),'onhold_quantity'=>DB::raw("onhold_quantity - $order_quantity")]);
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
        $total_exchange_amount = $request->total_exchange_amount;
        $server_session_id = session()->getId();
        $data = OmsCart::with('cartProductSize')->where("session_id",$server_session_id)->where("store_id",$store)->where('is_exchange',1)->get();
        // dd($data->toArray());
        return view('placeExchange.cartview',compact('data','total_exchange_amount'));
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
    public function paymentShipping(Request $request){
        // dd($request->all());
        $store   = $request->store_id;
        $sub_dir = $store == 1 ? "ba" : "df";
        $shipping_methods = ShippingMethodModel::where("store_id",$store)->get();
        $payment_methods  = PaymentMethodModel::where('status',1)->get();
        $e_wallet_balance = 0;
        $shipping_method     = session('exchange_shipping_method');
        $payment_method      = session('exchange_payment_method');
        $totals = $this->formatTotal($store);

        // dd($totals);
        return view('placeExchange.paymentshippingview',compact('shipping_methods','payment_methods','e_wallet_balance','totals','shipping_method','payment_method'));
      }
    public function removeCart(Request $request){
        $cart_id = $request->cart_id;
        $data = OmsCart::destroy($cart_id);
        if($data){
            return response()->json(['status'=>1,"msg"=>"Item deleted from cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error."]);
        }
    }
    public function setPaymentMethod(Request $request){
        session()->forget('exchange_payment_method');
        $store_id = $request->store_id;
        $request_payment_method = $request->payment_method;
        $data = PaymentMethodModel::select('id','name','fee','fee_label')->where("id",$request_payment_method)->first();
        if( $data ){
            session()->put('exchange_payment_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function setShippingMethod(Request $request){
        session()->forget('exchange_shipping_method');
        $store_id = $request->store_id;
        $shipping_method = $request->shipping_method;
        $data = ShippingMethodModel::select('id','name','amount')->where("store_id",$store_id)->where('id',$shipping_method)->first();
        if( $data ){
            session()->put('exchange_shipping_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function confirm(Request $request){
        // dd($request->all());
        $return_products_quantity = $request->quantity;
        $return_products_amount   = $request->amount;
        // echo count($return_products_quantity);
        // dd($request->all());
        $store_id = $request->store_id;
        $comment  = $request->comment;
        $order_id = $request->order_id;
        $google_map_link     = $request->google_map_link;
        $alternate_number    = $request->alternate_number;
        $shipping_method     = session('exchange_shipping_method');
        $payment_method      = session('exchange_payment_method');
        $server_session_id   = session()->getId();
        $cart_data = OmsCart::with(['product','productOption.option','productOption.optionVal'])->where("session_id",$server_session_id)->where('is_exchange',1)->where('store_id',$store_id)->get();
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

            //entry in total table
            $totals = $this->formatTotal($store_id);
            $order_total_amount = 0;
            if( is_array($totals) && count($totals) > 0 ){
                foreach($totals as $title=>$total){

                    $insert_order_tot           = new OmsExchangeTotalModel();
                    $insert_order_tot->store_id = $store_id;
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
            $place_order = new OmsPlaceExchangeModel();
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
                    $item_total_price = ($cart_quantity * $cart_item->product_price);
                    $insert_order_products = new OmsExchangeProductModel();
                    $insert_order_products->order_id   = $order_id;
                    $insert_order_products->product_id = $cart_item->product_id;
                    $insert_order_products->store_id   = $cart_item->store_id;
                    $insert_order_products->name       = $cart_item->product_name;
                    $insert_order_products->sku        = $cart_item->product_sku;
                    $insert_order_products->quantity   = $cart_quantity;
                    $insert_order_products->price      = $cart_item->product_price;
                    $insert_order_products->total      = $item_total_price;
                    $insert_order_products->product_option_id = $cart_item->product_option_id;
                    $insert_order_products->option_name  = $cart_option_name;
                    $insert_order_products->option_value = $cart_option_value;
                    $insert_order_products->save();
                }
            }
            //return products entry
            if( is_array( $return_products_quantity ) && count($return_products_quantity) > 0 ){
                foreach( $return_products_quantity as $order_product_id => $quantity ){  //$order_product_id this contain primary key of oms_order_products
                    $order_product_data = OmsOrderProductModel::where("id",$order_product_id)->first();
                    // dd( $order_product_data->toArray() );
                    $insert_return_products = new OmsExchangeReturnProductModel();
                    $insert_return_products->order_id	 = $order_product_data->order_id;
                    $insert_return_products->store_id	 = $order_product_data->store_id;
                    $insert_return_products->product_id	 = $order_product_data->product_id;
                    $insert_return_products->name	     = $order_product_data->name;
                    $insert_return_products->sku	     = $order_product_data->sku;
                    $insert_return_products->total	     = ( $quantity * $order_product_data->price);
                    $insert_return_products->product_option_id = $order_product_data->product_option_id;
                    $insert_return_products->option_name = $order_product_data->option_name;
                    $insert_return_products->option_value= $order_product_data->option_value;
                    $insert_return_products->quantity	 = $quantity;  //quantity is comming from post, how many quantity customer want to return
                    $insert_return_products->save();
                }
            }
            $this->onHoldQuantity($order_id,$store_id);
            OmsActivityLogModel::newLog($order_id,11,$store_id); // 1 for place exchange
             DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return response()->json(['status'=>false,"data"=>'','msg'=>$e->getMessage()]);
        }
        $this->clearSessionAfterOrder();
        return response()->json(['status'=>true,"data"=>'','msg'=>"order placed successfully."]);
      }
      public function onHoldQuantity($order_id,$store_id){
        $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( !$check_exist ){
                    $new_onhold = new OmsInventoryOnholdQuantityModel();
                    $new_onhold->order_id        = $order_product->order_id."-1";
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
      private function formatTotal($store){
        $totals = [];
        $exchange_total = Input::get('total_exchange_amount');
        $cart_sub_total = OmsCart::getExchangeCartTotalAmount($store);
        $totals['Sub-Total'] = $cart_sub_total;
        //exchange start
        //shipping method
        $shipping_method     = session('exchange_shipping_method');
        if($shipping_method ){
            $totals[$shipping_method['name']] = $shipping_method['amount'];
        }
        //payment method
        $payment_method      = session('exchange_payment_method');
        if( $payment_method && $payment_method['fee'] > 0 ){
            $totals[$payment_method['fee_label']] = $payment_method['fee'];
        }
        //exchange start
        $totals['Exchange-Total'] = -$exchange_total;
        //Ewallet start
        $ewallet_amount = $exchange_total-$cart_sub_total;
        if( $ewallet_amount > 0 ){
            $totals['E-wallet Total'] = $ewallet_amount;
        }
        return $totals;
      }
      private function clearSessionAfterOrder(){
        $server_session_id   = session()->getId();
        OmsCart::where('session_id',$server_session_id)->where('is_exchange',1)->delete();
        session()->forget('exchange_shipping_method');
        session()->forget('exchange_payment_method');
      }

}
