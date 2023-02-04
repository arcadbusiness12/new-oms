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

class PlaceOrderController extends Controller
{

    const VIEW_DIR = 'placeOrder.';
    const PER_PAGE = 20;

    function __construct(){
    }

    public function view($store){
        // $this->onHoldQuantity(20000034,2);
        $countries = array();
        $store_id = 0;
        $order_success_redirect = URL::to('/orders');
        // $customers = CustomersModel::select('customer_id','firstname','lastname','email','telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // $orders = OrdersModel::select('customer_id','firstname','lastname','email','telephone','payment_firstname','payment_lastname')->where('order_status_id', '>', 0)->groupBy('telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // echo "<pre>";print_r($customers);echo "</pre>";
        // echo "<pre>";print_r($orders);echo "</pre>";die;
        // $countries = CountryModel::select('country_id','name')->get()->toArray();
        $store_data = storeModel::where('id',$store)->first();

        return view(self::VIEW_DIR.".index",compact('store_data'));
    }
    public function getCustomerDetails(Request $request){
        $search = $request->get('keyword');

        $customers = OrdersModel::select('order_id','customer_id','firstname','lastname','email','telephone','payment_firstname','payment_lastname')
        ->where('firstname', 'LIKE', '%'.$search.'%')
        ->orWhere('lastname', 'LIKE', '%'.$search.'%')
        ->orWhere('email', 'LIKE', '%'.$search.'%')
        ->orWhere('telephone', 'LIKE', '%'.$search.'%')
        ->orWhere('order_id', $search)
        ->groupBy('telephone')
        ->orderBy('firstname', 'ASC')
        ->limit(20)
        ->get()
        ->toArray();

        $data['customers'] = array();
        foreach ($customers as $key => $value) {
            $data['customers'][] = array(
                'order_id'     =>  $value['order_id'],
                'customer_id'  =>  $value['customer_id'],
                'name'         =>  ($value['firstname'] ? $value['firstname'] : $value['payment_firstname']) . "" . ($value['lastname'] ? $value['lastname'] : $value['payment_lastname']),
                'email'        =>  $value['email'],
                'telephone'    =>  $value['telephone']
            );
        }

        return response()->json($data);
    }
    public function searchProducts(Request $request){
        // dd($request->all());
        $store = $request->store;
        $product = OmsInventoryProductModel::with(['ProductsSizes'=>function($query){
            $query->where("available_quantity",">",0);
        },'productDescriptions'=>function($query) use ($store){
            $query->where("store_id",$store);
        },'productSpecials'=>function($query) use ($store){
            // $query->where("store_id",$store)->whereDate("date_start",'<=',date('Y-m-d'))->whereDate("date_end",'>=',date('Y-m-d'))->orderBy('sort_order');
            $query->where("store_id",$store)
            ->orWhere(function($query){
                $query->whereNull('date_start')->whereNull('date_end');
            })
            ->whereDate("date_start",'<=',date('Y-m-d'))->whereDate("date_end",'>=',date('Y-m-d'))->orderBy('sort_order');
        }])
        ->where('sku','LIKE',"%".$request->product_sku."%")->first();
        // echo $product->productSpecials->count();
        // dd($product->toArray());
        return view(self::VIEW_DIR.'.product_search_form',compact('product'));
    }
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
        $sub_dir = $store == 1 ? "ba" : "df";
        $server_session_id = session()->getId();
        $data = OmsCart::with('cartProductSize')->where("session_id",$server_session_id)->where("store_id",$store)->get();
        // dd($data->toArray());
        return view(self::VIEW_DIR.'.cartview',compact('data'));
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
    public function removeCart(Request $request){
        $cart_id = $request->cart_id;
        $data = OmsCart::destroy($cart_id);
        if($data){
            return response()->json(['status'=>1,"msg"=>"Item deleted from cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error."]);
        }
    }
    public function searchCustomer(Request $request){
        //  dd($request->all());
     $store   = $request->store;
     $sub_dir = $store == 1 ? "ba" : "df";
     $name    = $request->name;
     $mobile  = $request->mobile;
     $email   = $request->email;
     $customer  = Customer::with(['defaultAddress'])
        ->when($mobile,function($query) use ($mobile){
            return $query->where('mobile','LIKE',"%".$mobile."%");
        })
        ->when($email,function($query) use ($email){
            return $query->where('email',$email);
        })
        ->first();
     $default_country = 221;
     $countries = Country::where('status',1)->get();
     $cities    = City::where('status',1)->where("country_id",$default_country)->get();
     $city_id   = 0;
     if($customer){
        $city_id = $customer->defaultAddress->city_id;
     }
     $areas = CityArea::when($city_id > 0,function($q) use ($city_id){
        $q->where('city_id',$city_id);
     })->get();

     $orders = [];
     return view(self::VIEW_DIR.'.customer_search_form',compact('customer','countries','cities','areas','default_country','customer','orders'));
  }
  public function loadAreas(Request $request){
    $city_id = $request->city_id;
    $data = CityArea::where("city_id",$city_id)->get();
    return response()->json($data);
  }
  public function saveCustomer(Request $request){
    // dd($request->all());
    $json = array();
    $store_id       = $request->store_id;
    if( $store_id < 1 ){
        return response()->json(['status'=>false,'Invalid store.']);
    }
    $customer_id    = $request->customer_id;
    $firstname      = $request->firstname;
    $email          = $request->email;
    $telephone_code = $request->telephone_code;
    $telephone      = $request->telephone;
    $country_id     = $request->country_id;
    $city_id        = $request->city_id;
    $area_id        = $request->area_id;
    $gmap_link      = $request->gmap_link;
    $alternate_number        = $request->alternate_number;
    $address_street_building        = $request->address_street_building;
    $address_villa_flate            = $request->address_villa_flate;
    $address            = $request->address;
    ///
    $full_telephone = $telephone_code.$telephone;
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
        if(!preg_match("/^[0-9]*$/", $telephone)){
            $json['error'] = "Enter valid mobile number!";
        }else if($telephone_code == 971 && strlen($telephone) != 9){
            $json['error'] = "Enter valid 9 digit number!";
        }else{
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
                $cust_add->street_building = $address_street_building;
                $cust_add->villa_flat = $address_villa_flate;
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
    //update customer id in cart details
    $server_session_id = session()->getId();
    $update_customer_cart = OmsCart::where('session_id',$server_session_id)->update(['customer_id'=>$customer_id]);
    if( $update_customer_cart ){
        $json = ['status'=>true,"data"=>'',"msg"=>"Customer updated in cart successfully."];
    }else{
        $json = ['status'=>false,"data"=>'',"msg"=>"Error, customer not addedd to cart."];
    }
    return response()->json($json);
  }
  public function paymentShipping(Request $request){
    // dd($request->all());
    $store   = $request->store_id;
    $shipping_methods = ShippingMethodModel::where("store_id",$store)->get();
    $payment_methods  = PaymentMethodModel::where('status',1)->get();
    $e_wallet_balance = 0;
    $shipping_method     = session('shipping_method');
    $payment_method      = session('payment_method');
    $totals = $this->formatTotal($store);
    // dd($totals);
    return view(self::VIEW_DIR . '.paymentshippingview',compact('shipping_methods','payment_methods','e_wallet_balance','totals','shipping_method','payment_method'));
  }
  private function formatTotal($store){
    $totals = [];
    $totals['Sub-Total'] = OmsCart::getCartTotalAmount($store);
    $shipping_method     = session('shipping_method');
    $payment_method      = session('payment_method');
    if($shipping_method ){
        $totals[$shipping_method['name']] = $shipping_method['amount'];
    }
    if( $payment_method && $payment_method['fee'] > 0 ){
        $totals[$payment_method['fee_label']] = $payment_method['fee'];
    }
    return $totals;
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
  public function getPaymetMethod(){

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
  public function getShippingMethod(Request $request){

  }
  public function confirmOrder(Request $request){
    $store_id = $request->store_id;
    $comment  = $request->comment;
    $google_map_link     = $request->google_map_link;
    $alternate_number    = $request->alternate_number;
    $shipping_method     = session('shipping_method');
    $payment_method      = session('payment_method');
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
    OmsCart::where('session_id',$server_session_id)->delete();
    session()->forget('shipping_method');
    session()->forget('payment_method');
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
  public function getProductSku(Request $request){
    // dd($request->all());
    if(count($request->all()) > 0){
        $product_sku = $request->product_sku;
        $store_id    = $request->store;
        $skus = [];
        $products = OmsInventoryProductModel::with(['productDescriptions'])
                    ->whereHas('productDescriptions',function($query) use ($store_id){
                        $query->where('store_id',$store_id);
                    })->select('sku')->where("sku",'LIKE',"{$product_sku}%")->limit(10)->get();
        // dd($products->toArray());
        if($products->count()){
            foreach ($products as $product) {
                $skus[] = $product->sku;
            }
        }
    }
    return response()->json(array('skus' => $skus));
 }
 //get Order from API
 public function fetchOrderFromBA(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://www.example.com/tester.phtml");
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS,
    //             "postvar1=value1&postvar2=value2&postvar3=value3");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec($ch);
    curl_close($ch);

}
}
