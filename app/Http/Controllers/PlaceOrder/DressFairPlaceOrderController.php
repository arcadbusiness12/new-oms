<?php

namespace App\Http\Controllers\PlaceOrder;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrderedProductModel;
use App\Models\OpenCart\Orders\OrdersModel AS BAOrdersModel;
use App\Models\OpenCart\Orders\OrderedProductModel AS BAOrderedProductModel;
use App\Models\DressFairOpenCart\Orders\OrderVoucherModel;
use App\Models\DressFairOpenCart\Orders\OrderStatusModel;
use App\Models\DressFairOpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductSpecialModel;
use App\Models\DressFairOpenCart\Products\ProductsDescriptionModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ApiIpModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrderReturnProduct;
use App\Models\DressFairOpenCart\Customers\CustomersModel;
use App\Models\OpenCart\Customers\CountryPhoneCodeModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ApiModel;
use App\Models\DressFairOpenCart\ExchangeOrders\CountryModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ZoneModel;
use App\Models\DressFairOpenCart\ExchangeOrders\AreaModel;
use App\Models\OpenCart\ExchangeOrders\SettingModel;
// use App\Models\DressFairOms\OmsPlaceOrderModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Providers\Reson8SmsServiceProvider;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Session;
use Validator;
use Excel;
use App\Models\Oms\OmsActivityLogModel;

class DressFairPlaceOrderController extends Controller
{
    const VIEW_DIR = 'placeOrder.df';
    const PER_PAGE = 20;
    private $DB_DFOPENCART_DATABASE = '';
    private $DB_DFOMS_DATABASE = '';
    private $DB_BAOMS_DATABASE = '';
    private $store = '';

    function __construct(){
        $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
        $this->DB_DFOMS_DATABASE = env('DB_DFOMS_DATABASE');
        $this->DB_BAOMS_DATABASE = env('DB_BAOMS_DATABASE');
        $this->store = 2; //2 for dressfair
    }

    public function view(){
        $api = array();
        $countries = array();

        $key = ApiModel::select('*')->where('username', 'Default')->first();
        $currency = SettingModel::select('value')->where('key', 'config_currency')->first()->value;
        $store_id = 0;
        $order_success_redirect = URL::to('/df/orders');
        $api = array(
            'api_id'    => $key['api_id'],
            'order_id'  => "",
            'username'  => $key['username'],
            'key'       => $key['key'],
            'store_id'  => $store_id,
            'currency'  => $currency,
            'order_success_redirect'  => $order_success_redirect,
        );
        $customers = CustomersModel::select('customer_id','firstname','lastname','email','telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // dd($customers);
        // $orders = OrdersModel::select('customer_id','firstname','lastname','email','telephone','payment_firstname','payment_lastname')->where('order_status_id', '>', 0)->groupBy('telephone')->orderBy('firstname', 'ASC')->get()->toArray();
        // echo "<pre>";print_r($customers);echo "</pre>";
        // echo "<pre>";print_r($orders);echo "</pre>";die;
        $countries = CountryModel::select('country_id','name')->get()->toArray();

        return view(self::VIEW_DIR . ".index", ["api" => $api, "countries" => $countries, "customers" => $customers]);
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
                'order_id'  =>  $value['order_id'],
                'customer_id'  =>  $value['customer_id'],
                'name'  =>  ($value['firstname'] ? $value['firstname'] : $value['payment_firstname']) . "" . ($value['lastname'] ? $value['lastname'] : $value['payment_lastname']),
                'email'  =>  $value['email'],
                'telephone'  =>  $value['telephone']
            );
        }

        return response()->json($data);
    }
    public function searchProducts(Request $request){
        $allProducts = [];
        if (count(Input::all()) > 0){
            if (Input::get('product_title')){
                $product_title = htmlentities(Input::get('product_title'));
            }
            if (Input::get('product_model')){
                $model = Input::get('product_model');
            }
            if (Input::get('product_sku')){
                $sku = Input::get('product_sku');
            }
        }
        $products = ProductsModel::select(DB::Raw('*'))
        ->join('product_description', 'product.product_id', '=', 'product_description.product_id');
        if (isset($product_title)){
            $products = $products->where('product_description.name', 'LIKE', "%{$product_title}%");
        }
        if (isset($model)){
            $products = $products->where('product.model', 'LIKE', "{$model}");
        }
        if (isset($sku)){
            $products = $products->where('product.sku', 'LIKE', "{$sku}");
        }
        $products = $products->where('status',1)->get()->toArray();
        // print_r($products); die;
        if (count($products) > 0){
            $arrayFlat = new \App\Platform\Helpers\FlattenCollection();
            $flat = $arrayFlat->flattenWithKey($products)->getFlatten();
            $allProducts[] = $flat;
            foreach ($allProducts as $key => $product){
                  $productOptions = new \App\Platform\DressFairOpenCart\ProductOptions();
                  $options = $productOptions->getProductOptions($product['product_id']);
                $allProducts[$key]['options'] = $options;
                $today_date = date('Y-m-d');
                $specials = ProductSpecialModel::select('*')->where('product_id',$product['product_id'])->where("date_start",'<=',$today_date)->where('date_end','>',$today_date)->orderBy('priority','ASC')->first();
                if( !$specials ){
                  $specials = ProductSpecialModel::select('*')->where('product_id',$product['product_id'])->where("date_start",'0000-00-00')->where('date_end','0000-00-00')->orderBy('priority','ASC')->first();
                }
                if( !$specials ){
                 $specials = ProductSpecialModel::select('*')->where('product_id',$product['product_id'])->orderBy('priority','ASC')->first();
                }
                if($specials) {
                    $allProducts[$key]['special'] = $specials->price;
                }
            }
        }
        // dd($allProducts);
        // if (count($products) > 0){
        //     $arrayFlat = new \App\Platform\Helpers\FlattenCollection();
        //     $flat = $arrayFlat->flattenWithKey($products)->getFlatten();
        //     $allProducts[] = $flat;
        //     foreach ($allProducts as $key => $product){
        //         $productOptions = new \App\Platform\DressFairOpenCart\ProductOptions();
        //         $options = $productOptions->getProductOptions($product['product_id']);
        //         $allProducts[$key]['options'] = $options;

        //         $specials = ProductSpecialModel::select('*')->where('product_id',$product['product_id'])->orderBy('priority','ASC')->first();
        //         if($specials) {
        //             $allProducts[$key]['special'] = $specials->price;
        //         }
        //     }
        // }
        return view(self::VIEW_DIR . '.product_search_form', ['products' => $allProducts]);
    }
    public function searchCustomer(Request $request){
        $customer = array();
        $orders = array();
        if (count(Input::all()) > 0){
            if(Input::get('type') == 'search'){
                $order_id = Input::get('customer');
                $name = Input::get('name');
                $number = Input::get('number');
                $email = Input::get('email');
                // $customer_data = CustomersModel::select('*')->where('telephone', 'LIKE', $telephone)->first();
                $customer_data = OrdersModel::select('*');
                if(!empty($name)){
                    $customer_data = $customer_data->where('firstname', 'LIKE', $name . "%");
                }
                if(!empty($number)){
                    $customer_data = $customer_data->where('telephone', 'LIKE', "%" . $number . "%");
                }
                if(!empty($email)){
                    $customer_data = $customer_data->where('email', 'LIKE', $email . "%");
                }
                $customer_data = $customer_data->orderBy('order_id', 'DESC')->get();

                // echo "<pre>";print_r($customer_data);echo "</pre>";die;
                $ba_customer_data = $this->baOrdersHistory($name,$number,$email);
                $registered_customer = CustomersModel::where('telephone', 'LIKE', "%" . $number . "%")->first();
                if($customer_data OR $ba_customer_data OR $registered_customer){
                    // $address_data = DB::table(env("DB_DFOPENCART_DATABASE").'.oc_address')->select('*')->where('address_id', $customer_data->address_id)->first();
                    $address_street_building = "";
                    $address_villa_flate = "";
                    // echo "<pre>"; print_r($customer_data->toArray()); die;
                    if(isset($customer_data[0]) && $customer_data[0]->shipping_address_2 != "" ){
                        $address_2 = explode(",-",$customer_data[0]->shipping_address_2);
                        if(is_array($address_2) && count($address_2) > 0){
                            $address_street_building = $address_2[0];
                            $address_villa_flate    = $address_2[1];
                        }
                    }
                    if($customer_data && $customer_data->count() > 0){
                      $customer = array(
                          'customer_id' => isset($registered_customer) ? $registered_customer->customer_id : "",
                          'customer_group_id' => isset($customer_data[0]) ? $customer_data[0]->customer_group_id : "",
                          'firstname' => isset($customer_data[0]) ? $customer_data[0]->firstname : "",
                          'lastname' => isset($customer_data[0]) ? $customer_data[0]->lastname : "",
                          'email' => isset($customer_data[0]) ? $customer_data[0]->email : "",
                          'telephone' => isset($customer_data[0]) ? $customer_data[0]->telephone : "",
                          'alternate_phone' => isset($customer_data[0]) ? $customer_data[0]->alternate_number : "",
                          'gmap_link'=> isset($customer_data[0]) ? $customer_data[0]->google_map_link : "",
                          'fax' => isset($customer_data[0]) ? $customer_data[0]->fax : "",
                          'address_1' => isset($customer_data[0]) ? $customer_data[0]->shipping_address_1 : "",
                          'address_street_building' => $address_street_building,
                          'address_villa_flate' => $address_villa_flate,
                          'city' => isset($customer_data[0]) ? $customer_data[0]->shipping_city : "",
                          'area' => isset($customer_data[0]) ? $customer_data[0]->shipping_area : "",
                          'country_id' => isset($customer_data[0]) ? $customer_data[0]->shipping_country_id : 0,
                          'zone_id' => isset($customer_data[0]) ? $customer_data[0]->shipping_zone_id : 0,
                      );
                    }else{
                      $customer = array(
                        'customer_id' => isset($registered_customer) ? $registered_customer->customer_id : "",
                        'customer_group_id' => isset($registered_customer) ? $registered_customer->customer_group_id : "",
                        'firstname' => isset($registered_customer) ? $registered_customer->firstname : "",
                        'lastname' => isset($registered_customer) ? $registered_customer->lastname : "",
                        'email' => isset($registered_customer) ? $registered_customer->email : "",
                        'telephone' => isset($registered_customer) ? $registered_customer->telephone : "",
                        'alternate_phone' => "",
                        'gmap_link'=> "",
                        'fax' => '',
                        'address_1' => '',
                        'address_street_building' => '',
                        'address_villa_flate' => '',
                        'city' => '',
                        'area' => '',
                        'country_id' => '',
                        'zone_id' => '',
                      );
                    }
                }
                if($customer_data){
                  $customer_data = $customer_data->merge($ba_customer_data);
                }else{
                  $customer_data = $ba_customer_data;
                }
                $customer_data = $customer_data->sortByDesc('date_added');
                foreach ($customer_data as $order) {
                    // $product_total = OrderedProductModel::select(DB::Raw('COUNT(*) AS total'))->where('order_id', $order['order_id'])->first();
                    // $voucher_total = OrderVoucherModel::select(DB::Raw('COUNT(*) AS total'))->where('order_id', $order['order_id'])->first();
                    // $user = OmsPlaceOrderModel::select('ou.username')->join($this->DB_BAOMS_DATABASE . '.oms_user as ou', 'ou.user_id', '=', 'oms_place_order.user_id')->where('oms_place_order.order_id', $order['order_id'])->first();
                    $user = OmsPlaceOrderModel::select('ou.username','store')->join('oms_user as ou', 'ou.user_id', '=', 'oms_place_order.user_id')
                            ->where('oms_place_order.order_id', $order['order_id'])->first();
                    if($user){
                      if( $user->store == 1 ){
                        $store_name = "BA";
                        $product_total = BAOrderedProductModel::select(DB::Raw('COUNT(*) AS total'))->where('order_id', $order['order_id'])->first();
                      }else if( $user->store == 2 ){
                        $store_name = "DF";
                        $product_total = OrderedProductModel::select(DB::Raw('COUNT(*) AS total'))->where('order_id', $order['order_id'])->first();
                      }
                    }else{
                      $store_name = "";
                    }

                    $orders[] = array(
                        'order_id'   => $order['order_id'],
                        'user'       => $user ? $user->username : "-",
                        'store_name' => $store_name,
                        'name'       => $order['firstname'] . ' ' . $order['lastname'],
                        'status'     => $order['status'],
                        'date_added' => $order['date_added'],
                        'products'   => @$product_total ? $product_total->total : 0,
                        'total'      => $order['total'],
                    );
                }
            }else{
                $customer = array(
                    'customer_id' => 0,
                    'customer_group_id' => 1,
                    'firstname' => "",
                    'lastname' => "",
                    'email' => "",
                    'telephone' => "",
                    'alternate_phone' => "",
                    'gmap_link'=> "",
                    'fax' => "",
                    'address_1' => "",
                    'address_street_building' => "",
                    'address_villa_flate' => "",
                    'city' => "",
                    'area' => "",
                    'country_id' => "",
                    'zone_id' => "",
                );
            }
        }
        $countries = CountryModel::select('country_id','name')->get()->toArray();
        $setting = SettingModel::get('config', 'config_login_countries');
        $login_countries = json_decode($setting, true);
        $country_phonecodes = CountryPhoneCodeModel::select('nicename', 'phonecode')->whereIn('id', $login_countries)->get();
        // dd($customer);
        return view(self::VIEW_DIR . '.customer_search_form', ['customer' => $customer, 'orders' => $orders, 'countries' => $countries, 'login_countries' => $country_phonecodes]);
    }
    protected function baOrdersHistory($name,$number,$email){
      $customer_data = BAOrdersModel::select('*');
      if(!empty($name)){
          $customer_data = $customer_data->where('firstname', 'LIKE', $name . "%");
      }
      if(!empty($number)){
          $customer_data = $customer_data->where('telephone', 'LIKE', "%" . $number . "%");
      }
      if(!empty($email)){
          $customer_data = $customer_data->where('email', 'LIKE', $email . "%");
      }
      $customer_data = $customer_data->orderBy('order_id', 'DESC')->get();

      return $customer_data;
    }
    public function save_customer(Request $request){
        $json = array();
        // dd($request->all());
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

                    DB::connection(env('DB_DFOPENCART_CONNECTION'))->table('address')->where('address_id', $address_id->address_id)->update($customer);
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
                // $this->session->data['account'] = 'guest';
                $telephone = $request->get('telephone_code') . $telephone;

                $json['success'] = true;
                $json['telephone'] = $telephone;
            }
        }

        return response()->json($json);
    }
    public function addToCart(Request $request){
        $cart_add_item = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        if($post_data['product']){
            $cart_add_item = array(
                'product_id' => $post_data['product']['product_id'],
                'quantity' => $post_data['product']['qty'],
            );
            if(isset($post_data['product']['option'])){
                foreach ($post_data['product']['option'] as $key => $value) {
                    $cart_add_item['option'][$key] = $value;
                }
            }
        }
        return response()->json(array('success' => true,'cart_add_item' => $cart_add_item));
    }
    public function getCart(Request $request){
        $products = array();
        $errors = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        if(isset($post_data['products']) && is_array($post_data['products'])){
            foreach ($post_data['products'] as $product) {
                $option = array();
                if(isset($product['option'])){
                    foreach ($product['option'] as $value) {
                        $option[] = array(
                            'product_option_id'         =>  $value['product_option_id'],
                            'product_option_value_id'   =>  $value['product_option_value_id'],
                            'name'                      =>  $value['name'],
                            'value'                     =>  $value['value'],
                        );
                    }
                }
                $products[] = array(
                    'cart_id'       =>  $product['cart_id'],
                    'product_id'    =>  $product['product_id'],
                    'image'         =>  $this->get_product_image($product['product_id']),
                    'name'          =>  $product['name'],
                    'model'         =>  $product['model'],
                    'quantity'      =>  $product['quantity'],
                    'options'       =>  $option,
                    'price'         =>  $product['price'],
                    'total'         =>  $product['total'],
                    'stock'         =>  $product['stock'],
                );
            }
        }
        if(isset($post_data['error'])){
            $errors = $post_data['error'];
        }
        $totals = $post_data['totals'];
        return view(self::VIEW_DIR . '.cartview', ['products' => $products,'totals' => $totals,'errors' => $errors]);
    }
    public function getAddress(){
        $customer = array();
        $customer_id = Input::get('customer_id');

        $customer_data = Input::get('customer');
        if($customer_id){
            $address_id = CustomersModel::select('address_id')->where('customer_id', $customer_id)->first();
            if($address_id){
                $address_data = DB::table(env("DB_DFOPENCART_DATABASE").'.oc_address')->select('*')->where('address_id', $address_id->address_id)->first();
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
    public function getShippingAddress(){
        $customer = array();
        $customer_id = Input::get('customer_id');

        $payment_address = array();
        parse_str(Input::get('payment_address'), $payment_address);

        if($customer_id){
            $address_id = CustomersModel::select('address_id')->where('customer_id', $customer_id)->first();
            if($address_id){
                $address_data = DB::table(env("DB_DFOPENCART_DATABASE").'.oc_address')->select('*')->where('address_id', $address_id->address_id)->first();
                $names = CustomersModel::getCustomerNames($payment_address['firstname']);
                $customer = array(
                    'shipping_address' => $payment_address ? $payment_address['payment_address'] : $address_data->address_id,
                    'shipping_firstname' => $payment_address ? $names['firstname'] : $address_data->firstname,
                    'shipping_lastname' => $payment_address ? $names['lastname'] : $address_data->lastname,
                    'shipping_company' => $payment_address ? $payment_address['company'] : $address_data->company,
                    'shipping_address_1' => $payment_address ? $payment_address['address_1'] : $address_data->address_1,
                    'shipping_address_2' => $payment_address ? $payment_address['address_2'] : $address_data->address_2,
                    'shipping_city' => $address_data ? $address_data->city : "",
                    'shipping_area' => $payment_address ? $payment_address['area'] : $address_data->area,
                    'shipping_postcode' => $payment_address ? $payment_address['postcode'] : $address_data->postcode,
                    'shipping_zone_id' => $payment_address ? $payment_address['zone_id'] : $address_data->zone_id,
                    'shipping_country_id' => $payment_address ? $payment_address['country_id'] : $address_data->country_id,
                );
            }
            $countries = CountryModel::select('country_id','name')->get()->toArray();

            $html = view(self::VIEW_DIR . '.shipping_address', ['customer' => $customer, 'countries' => $countries]);
            $contents = (string)$html;
            $contents = $html->render();

            return response()->json(array('success' => true, 'html' => $contents));
        }else{
            $names = CustomersModel::getCustomerNames($payment_address['firstname']);
            $customer = array(
                'shipping_address' => "",
                'shipping_firstname' => $names['firstname'],
                'shipping_lastname' => $names['lastname'],
                'shipping_company' => $payment_address['company'],
                'shipping_address_1' => $payment_address['address_1'],
                'shipping_address_2' => $payment_address['address_2'],
                'shipping_city' => "",
                'shipping_area' => $payment_address['area'],
                'shipping_postcode' => $payment_address['postcode'],
                'shipping_zone_id' => $payment_address['zone_id'],
                'shipping_country_id' => $payment_address['country_id'],
            );
            $countries = CountryModel::select('country_id','name')->get()->toArray();

            $html = view(self::VIEW_DIR . '.shipping_address', ['customer' => $customer, 'countries' => $countries]);
            $contents = (string)$html;
            $contents = $html->render();

            return response()->json(array('success' => true, 'html' => $contents));
        }
    }
    public function getPaymentShipping(Request $request){
        $payment_method = '';
        $shipping_method = '';
        $payment_methods = array();
        $shipping_methods = array();
        $e_wallet_balance = 0;
        $totals = array();
        if (count(Input::all()) > 0){
            $post_data = Input::all();
        }
        if(isset($post_data['totals'])){
            $totals = $post_data['totals'];
        }
        if(isset($post_data['e_wallet_balance'])){
          $e_wallet_balance = $post_data['e_wallet_balance'];
        }
        if(isset($post_data['payment_method'])){
            $payment_method = $post_data['payment_method'];
        }
        if(isset($post_data['shipping_method'])){
            $shipping_method = $post_data['shipping_method'];
        }
        if(isset($post_data['payment_methods']) && is_array($post_data['payment_methods'])){
            $payment_methods = $post_data['payment_methods'];
        }
        if(isset($post_data['shipping_methods']) && is_array($post_data['shipping_methods'])){
            $shipping_methods = $post_data['shipping_methods'];
        }
        return view(self::VIEW_DIR . '.paymentshippingview', ['payment_method' => $payment_method, 'payment_methods' => $payment_methods, 'shipping_method' => $shipping_method, 'shipping_methods' => $shipping_methods, 'totals' => $totals,'e_wallet_balance'=>$e_wallet_balance]);
    }
    public function addIP(Request $request){
        $post_data = Input::all();
        $ApiIpModel = new ApiIpModel();
        $ApiIpModel->{ApiIpModel::FIELD_API_ID} = $post_data['api_id'];
        if(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        $ApiIpModel->{ApiIpModel::FIELD_IP} = $ipAddress;
        $ApiIpModel->save();
        if($ApiIpModel->api_ip_id) $success = 'Success: You have modified APIs!';
        else $success = false;
        return response()->json(array('success' => $success));
    }
    public function getcartTotal(Request $request){
        $post_data = Input::all();
        $sub_total = 0;
        $total = 0;
        $exchange_item_amount = $post_data['exchange_item_amount'] ? $post_data['exchange_item_amount'] : '';
        if(isset($post_data['product'])){
            foreach ($post_data['product'] as $key => $product) {
                $sub_total = $sub_total + ($product['price'] * $product['qty']);
            }
        }
        $total = $sub_total - $post_data['exchange_item_amount'];
        $response_data = array(
            'success' => true,
            'sub_total' => $sub_total,
            'exchange_item_amount' => $exchange_item_amount,
            'total' => $total,
        );
        return response()->json($response_data);
    }
    public function get_product_image($product_id = ''){
        $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();
        return env('DF_OPEN_CART_IMAGE_URL') . $product_image->image;
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
        	$orders_data = OmsPlaceOrderModel::join($this->DB_DFOPENCART_DATABASE . '.oc_order AS oc_order', 'oms_place_order.order_id', '=', 'oc_order.order_id')
                    		->where($whereClause)
                        ->where('oms_place_order.store', $this->store)
                        ->where('oc_order.order_status_id','!=',7)
                    		->orderBy('oms_place_order.place_order_id', 'DESC')
                    		->paginate($per_page)->appends(Input::all());
        }else{
        	$orders_data = OmsPlaceOrderModel::join($this->DB_DFOPENCART_DATABASE . '.oc_order AS oc_order', 'oms_place_order.order_id', '=', 'oc_order.order_id')
                    		->where('oms_place_order.user_id', session('user_id'))
                    		->where($whereClause)
                            ->where('oms_place_order.store', $this->store)
                    		->orderBy('oms_place_order.place_order_id', 'DESC')
                    		->paginate($per_page)->appends(Input::all());
        }

        foreach ($orders_data as $key => $value) {
            $user = OmsUserModel::select('username','firstname','lastname')->where('user_id', $value->user_id)->first();
            $status = OrderStatusModel::select('name')->where('order_status_id', $value->order_status_id)->first();
            $shipping_company_data = OmsOrdersModel::with('airway_bills')->where('order_id',$value->order_id)->where('last_shipped_with_provider','>',0)->first();

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

        $OmsPlaceOrderModel = new OmsPlaceOrderModel();
        $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_ORDER_ID} = $order_id;
        $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_USER_ID} = $user_id;
        $OmsPlaceOrderModel->{OmsPlaceOrderModel::FIELD_STORE} = $this->store;
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
    public function get_product_name(){
        $titles = array();
        if(count(Input::all()) > 0){
            $product_name = Input::get('product_name');

            $product_names = ProductsDescriptionModel::select('name')->where('name','LIKE',"%{$product_name}%")->limit(10)->get();
            if($product_names->count()){
                foreach ($product_names as $product) {
                    $titles[] = $product->name;
                }
            }
        }
        return response()->json(array('titles' => $titles));
    }
    public function get_product_model(){
        $models = array();
        if(count(Input::all()) > 0){
            $product_model = Input::get('product_model');

            $product_models = ProductsModel::select('model')->where('model','LIKE',"%{$product_model}%")->limit(10)->get();
            if($product_models->count()){
                foreach ($product_models as $product) {
                    $models[] = $product->model;
                }
            }
        }
        return response()->json(array('models' => $models));
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
