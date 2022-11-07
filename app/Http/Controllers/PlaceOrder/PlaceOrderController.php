<?php
namespace App\Http\Controllers\PlaceOrder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\City;
use App\Models\Oms\CityArea;
use App\Models\Oms\Country;
use App\Models\Oms\Customer;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\DutyAssignedUserModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsCart;
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
        $sub_dir = $store == 1 ? "ba" : "df";
        $countries = array();
        $store_id = 0;
        $order_success_redirect = URL::to('/orders');
        // $customers = CustomersModel::select('customer_id','firstname','lastname','email','telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // $orders = OrdersModel::select('customer_id','firstname','lastname','email','telephone','payment_firstname','payment_lastname')->where('order_status_id', '>', 0)->groupBy('telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // echo "<pre>";print_r($customers);echo "</pre>";
        // echo "<pre>";print_r($orders);echo "</pre>";die;
        // $countries = CountryModel::select('country_id','name')->get()->toArray();

        return view(self::VIEW_DIR.$sub_dir. ".index");
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
        $sub_dir = $request->store == 1 ? "ba" : "df";
        $product = OmsInventoryProductModel::with(['ProductsSizes'=>function($query){
            $query->where("available_quantity",">",0);
        }])
        ->join("oms_inventory_product_descriptions AS oipd","oipd.product_id","=","oms_inventory_product.product_id")
        ->where("oipd.store_id",$store)->where('sku','LIKE',"%".$request->product_sku."%")->first();
        // dd($product->toArray());
        return view(self::VIEW_DIR . $sub_dir . '.product_search_form',compact('product'));
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
        return view(self::VIEW_DIR . $sub_dir . '.cartview',compact('data'));
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
     $customer  = Customer::with(['addresses'])
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
     $orders = [];
     //  dd($countries->toArray());
     return view(self::VIEW_DIR . $sub_dir . '.customer_search_form',compact('customer','countries','cities','default_country','customer','orders'));
  }
  public function loadAreas(Request $request){
    $city_id = $request->city_id;
    $data = CityArea::where("city_id",$city_id)->get();
    return response()->json($data);
  }
  public function save_customer(Request $request){
    $json = array();
    $customer_id = $request->get('customer_id');

    if($customer_id){
        // Ignore space and first zero
        $telephone = str_replace(" ", "", $request->get('telephone'));
        $telephone = (int)$telephone;

        if(!preg_match("/^[0-9]*$/", $telephone)){
            $json['error'] = "Enter valid mobile number!";
        }else if($request->get('telephone_code') == 971 && strlen($telephone) != 9){
            $json['error'] = "Enter valid 9 digit number!";
        }else if($request->get('telephone_code') != 971 && strlen($telephone) != 10){
            $json['error'] = "Enter valid 10 digit number!";
        }else{
            $telephone = $request->get('telephone_code') . $telephone;

            $names = CustomersModel::getCustomerNames($request->get('firstname'));
            $address_id = CustomersModel::select('address_id')->where('customer_id', $customer_id)->first();
            if($address_id){
                $customer = array(
                    'firstname' => $names['firstname'],
                    'lastname' => $names['lastname'],
                    'telephone' => $telephone,
                    'address_1' => $request->get('address_1'),
                    'area' => $request->get('area'),
                    'zone_id' => $request->get('zone_id'),
                    'country_id' => $request->get('country_id'),
                );

                DB::table(env("DB_BAOPENCART_DATABASE").'.oc_address')->where('address_id', $address_id->address_id)->update($customer);
            }
            $json['success'] = true;
            $json['telephone'] = $telephone;
        }
    }else{
        // Ignore space and first zero
        $telephone = str_replace(" ", "", $request->get('telephone'));
        $telephone = (int)$telephone;

        if(!preg_match("/^[0-9]*$/", $telephone)){
            $json['error'] = "Enter valid mobile number!";
        }else if($request->get('telephone_code') == 971 && strlen($telephone) != 9){
            $json['error'] = "Enter valid 9 digit number!";
        }else if($request->get('telephone_code') != 971 && strlen($telephone) != 10){
            $json['error'] = "Enter valid 10 digit number!";
        }else{
          //  $this->session->data['account'] = 'guest';
            $telephone = $request->get('telephone_code') . $telephone;

            $json['success'] = true;
            $json['telephone'] = $telephone;
        }
    }

    return response()->json($json);
}
    public function getAddress(){
      $customer = array();
      $customer_id = Input::get('customer_id');

      $customer_data = Input::get('customer');

      if($customer_id){
          $address_id = CustomersModel::select('address_id')->where('customer_id', $customer_id)->first();
          if($address_id){
              $address_data = DB::table(env("DB_BAOPENCART_DATABASE").'.oc_address')->select('*')->where('address_id', $address_id->address_id)->first();
              $customer = array(
                  'shipping_address' => $address_data ? $address_data['address_id'] : $address_data->address_id,
                  'shipping_firstname' => $address_data ? $address_data['firstname'] : $address_data->firstname,
                  'shipping_lastname' => $address_data ? $address_data['lastname'] : $address_data->lastname,
                  'shipping_company' => $address_data->company,
                  'shipping_address_1' => $address_data ? $address_data['address_1'] : $address_data->address_1,
                  'shipping_address_2' => $address_data->address_2,
                  'shipping_city' => $address_data ? $address_data['city'] : $address_data->city,
                  'shipping_area' => $address_data ? $address_data['area'] : $address_data->area,
                  'shipping_postcode' => $address_data->postcode,
                  'shipping_zone_id' => $address_data ? $address_data['zone_id'] : $address_data->zone_id,
                  'shipping_country_id' => $address_data ? $address_data['country_id'] : $address_data->country_id,
              );
          }
          $countries = CountryModel::select('country_id','name')->get()->toArray();

          $html = view(self::VIEW_DIR . '.address', ['customer' => $customer, 'countries' => $countries]);
          $contents = (string)$html;
          $contents = $html->render();

          return response()->json(array('success' => true, 'html' => $contents));
      }else{
          $names = CustomersModel::getCustomerNames($customer_data['firstname']);
          $customer = array(
              'shipping_address' => "",
              'shipping_firstname' => $names['firstname'],
              'shipping_lastname' => $names['lastname'],
              'shipping_company' => "",
              'shipping_address_1' => $customer_data['address_1'],
              'shipping_address_2' => $customer_data['address_2'],
              'shipping_city' => "",
              'shipping_area' => $customer_data['area'],
              'shipping_postcode' => "",
              'shipping_zone_id' => $customer_data['zone_id'],
              'shipping_country_id' => $customer_data['country_id'],
          );
          $countries = CountryModel::select('country_id','name')->get()->toArray();

          $html = view(self::VIEW_DIR . '.address', ['customer' => $customer, 'countries' => $countries]);
          $contents = (string)$html;
          $contents = $html->render();

          return response()->json(array('success' => true, 'html' => $contents));
      }
  }
    public function get_product_image($product_id = ''){
        $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();
        return env('OPEN_CART_IMAGE_URL') . $product_image->image;
    }
    public function update_return_product(){
        if(Input::all() > 0 && Input::get('submit') == 'update_return_product'){
            foreach (Input::get('order') as $order) {
                ExchangeOrderReturnProduct::where(ExchangeOrderReturnProduct::FIELD_ORDER_ID, Input::get('order_id'))->where(ExchangeOrderReturnProduct::FIELD_ORDER_PRODUCT_ID, $order['product_id'])->update(array(ExchangeOrderReturnProduct::FIELD_ORDER_QUANTITY => $order['quantity']));
            }
        }
        return redirect('/exchange_orders/add/'. Input::get('order_id'));
    }

    public function reports(){
        $orders = array();
        $whereClause = [];

        if (Input::get('user_id')){
            array_push($whereClause, ['oms_place_order.user_id', Input::get('user_id')]);
        }
        if (Input::get('order_id')){
            array_push($whereClause, ['oc_order.order_id', Input::get('order_id')]);
        }
        if (Input::get('order_status_id')){
            array_push($whereClause, ['oc_order.order_status_id', Input::get('order_status_id')]);
        }
        if (Input::get('min_amount')){
            array_push($whereClause, ['oc_order.total', '>=', Input::get('min_amount')]);
        }
        if (Input::get('max_amount')){
            array_push($whereClause, ['oc_order.total', '<=', Input::get('max_amount')]);
        }
        if (Input::get('date_from')){
            $date_from = Carbon::createFromFormat("Y-m-d", Input::get('date_from'))->toDateString();
            array_push($whereClause, [DB::raw("DATE_FORMAT(oc_order.date_added,'%Y-%m-%d')"), '>=', "$date_from"]);
        }
        if (Input::get('date_to')){
            $date_to = Carbon::createFromFormat("Y-m-d", Input::get('date_to'))->toDateString();
            array_push($whereClause, [DB::raw("DATE_FORMAT(oc_order.date_added,'%Y-%m-%d')"), '<=', "$date_to"]);
        }
        if( Input::get('generate_csv') != ""  ){
            $per_page = 40000;
        }else{
            $per_page = self::PER_PAGE;
        }
      if(session('role') == 'ADMIN' || session('user_group_id')==4 || session('user_group_id')==8){
        	$orders_data = DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_order AS oc_order'))
          ->join(DB::raw($this->DB_BAOMS_DATABASE. '.oms_place_order AS oms_place_order'),'oms_place_order.order_id','=','oc_order.order_id')
          ->where($whereClause)
          ->where('oms_place_order.store', $this->store)
          ->where('oc_order.order_status_id','!=',7)
          ->orderBy('oc_order.order_id', 'DESC')
          ->paginate($per_page)->appends(Input::all());
      }else{
       $orders_data = DB::table(DB::raw($this->DB_BAOPENCART_DATABASE. '.oc_order AS oc_order'))
       ->join(DB::raw($this->DB_BAOMS_DATABASE. '.oms_place_order AS oms_place_order'),'oms_place_order.order_id','=','oc_order.order_id')
       ->where('oms_place_order.user_id', session('user_id'))
       ->where($whereClause)
       ->where('oms_place_order.store', $this->store)
       ->where('oc_order.order_status_id','!=',7)
       ->orderBy('oms_place_order.place_order_id', 'DESC')
       ->paginate($per_page)->appends(Input::all());
     }

   foreach ($orders_data as $key => $value) {
    $user = OmsUserModel::select('username','firstname','lastname')->where('user_id', $value->user_id)->first();
    $status = OrderStatusModel::select('name')->where('order_status_id', $value->order_status_id)->first();
    $shipping_company_data = OmsOrdersModel::with('airway_bills')->where('order_id',$value->order_id)->where('last_shipped_with_provider','>',0)->first();
    // echo $shipping_company_data->airway_bills[0]->airway_bill_number;
    // echo "<br>";
    // echo $shipping_company_data->airway_bills[0]->shipping_provider->name;
    // echo "<br>";
    // dd($shipping_company_data->toArray());
    if( !empty($shipping_company_data) ){
        $airwaybill_no = $shipping_company_data->airway_bills[0]->airway_bill_number;
        $shipping_company = $shipping_company_data->airway_bills[0]->shipping_provider->name;
    }else{
        $shipping_company = "";
        $airwaybill_no  = "";
    }

    $orders[] = array(
        'order_id'  =>  $value->order_id,
        'user'      =>  $user ? $user->username : '',
        'amount'    =>  $value->total,
        'shipping_company' => $shipping_company,
        'airwaybill_no' => $airwaybill_no,
        'status'    =>  $status ? $status->name : '',
        'date'      =>  $value->date_added,
    );
    }
    if( Input::get('generate_csv') != "" && !empty($orders) ){
        $this->csvReport($orders);
    }
$staff_members = OmsUserModel::select('user_id','username')->whereIn(OmsUserModel::FIELD_USER_GROUP_ID,[11,12])->where('status',1)->get();
$ordersStatus = OrderStatusModel::all();

return view(self::VIEW_DIR . ".reports", ["orders" => $orders, "orderStatus" => $ordersStatus, "staffs" => $staff_members, "role" => session('role'), "pagination" => $orders_data->render(), "old_input" => Input::all(), "orders_data" => $orders_data]);
}
public function csvReport($list){
    $file_name = "saleReport".date('d-M-Y');
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="'.$file_name.'.csv"');
    $fp = fopen('php://output', 'w');
    // $list = array (
    //     array('aaa', 'bbb', 'ccc', 'dddd'),
    //     array('123', '456', '789'),
    //     array('"aaa"', '"bbb"')
    // );

    // $fp = fopen('file.csv', 'w');
    fputcsv($fp, ['Order #','Sale Person','Amount','Courier Company','AirWay Bill #','OMS Status','Date']);
    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);
    exit();
}
public function addUserOrder(Request $request){
    $order_id = $request->get('order_id');
    $user_id = Session::get('user_id');
    //get target data to save
    // $target_data = OmsUserModel::with(['paidAdPage','singleAssignedDuties'=>function($query){
    //   $query->where("activity_id",2);
    // }])->where("user_id",$user_id)->first();

    $OmsPlaceOrderModel = new OmsPlaceOrderModel();
    $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_ORDER_ID} = $order_id;
    $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_USER_ID} = $user_id;
    $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_STORE} = $this->store;
    // if($target_data){
    //   $OmsPlaceOrderModel->amount_target = $target_data->commission_on_delivered_amount;
    //   if( $target_data->paidAdPage ){
    //     $OmsPlaceOrderModel->page_setting_channel_id = $target_data->paidAdPage->id;
    //   }
    //   if( $target_data->singleAssignedDuties ){
    //   $OmsPlaceOrderModel->order_target = $target_data->singleAssignedDuties->quantity;
    //   }
    // }
    $order_save = $OmsPlaceOrderModel->save();
    if($order_save){
      OmsActivityLogModel::newLog($order_id,1,$this->store); //1 is for place order
    }
    // $this->checkDuplicateOrder($order_id);
    return response()->json(array('success' => true));
}
public function get_customer(){
    $customers = array();
    if(count(Input::all()) > 0){
        $customer = Input::get('customer');

        $customers = CustomersModel::select('customer_id','firstname','lastname','email','telephone')->where('firstname','LIKE',"%{$customer}%")->orWhere('lastname','LIKE',"%{$customer}%")->orWhere('email','LIKE',"%{$customer}%")->orWhere('telephone','LIKE',"%{$customer}%")->limit(10)->get();
        if($customers->count()){
            foreach ($customers as $customer) {
                $customers[] = $customer->firstname . ' ' . $customer->lastname . ' - ' . $customer->email . ' - ' . $customer->telephone;
            }
        }
    }
    return response()->json(array('customers' => $customers));
}
public function getProductSku(Request $request){
    // dd($request->all());
    if(count($request->all()) > 0){
        $product_sku = $request->product_sku;
        $skus = [];
        $products = OmsInventoryProductModel::select('sku')->where("sku",'LIKE',"{$product_sku}%")->limit(10)->get();
        // dd($products->toArray());
        if($products->count()){
            foreach ($products as $product) {
                $skus[] = $product->sku;
            }
        }
    }
    return response()->json(array('skus' => $skus));
}
public function get_zone(){
    $postData = Input::all();
    $zones = array();
    if(isset($postData['country_id'])){
        $zones = ZoneModel::select('zone_id','name')->where(ZoneModel::FIELD_COUNTRY_ID,$postData['country_id'])->get()->toArray();
    }
    return response()->json(array('success' => true, 'zones' => $zones));
}
public function get_area(){
    $postData = Input::all();
    $areas = array();
    if(isset($postData['zone_id'])){
        $areas = AreaModel::select('area_id','name')->where(AreaModel::FIELD_ZONE_ID,$postData['zone_id'])->get()->toArray();
    }
    return response()->json(array('success' => true, 'areas' => $areas));
}
}
