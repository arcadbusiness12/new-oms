<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsActivityModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\ShippingProvidersModel;

use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsUserModel;

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;

use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;

use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\CityArea;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryReturnQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsOrderStatusModel;
use App\Models\Oms\OmsPlaceOrderModel;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request AS RequestFacad;
use App\Platform\Helpers\ToolImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Support\Collection;
class OrdersController extends Controller
{
	const VIEW_DIR = 'orders';
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    private $website_image_source_url =  '';
    private $website_image_source_path =  '';
    //for dressfair
    private $df_website_image_source_path =  '';
	private $df_website_image_source_url  =  '';
    //
    private $opencart_image_url;
    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url  =   isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/' : "";
        //for df
        $this->df_website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/dressfair.com/image/';
		$this->df_website_image_source_url  =   isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/dressfair.com/image/' : "";
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }
    public function index(){
        $old_input = RequestFacad::all();
        $data = OmsPlaceOrderModel::select('oms_place_order.*')
                ->with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore','omsOrder.lastAwb'])
                ->leftjoin("oms_orders",function($join){
                    $join->on('oms_orders.order_id', '=', 'oms_place_order.order_id');
                    $join->on('oms_orders.store', '=', 'oms_place_order.store');
                })
                ->where('online_approved', 1)
                ->where('reseller_approve', 1)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.store',$old_input['by_store']);
                })
                ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.mobile','LIKE',"%".$old_input['telephone']."%");
                })
                ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.firstname','LIKE',"%".$old_input['customer']."%");
                })
                ->when(@$old_input['email'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.email','LIKE',"%".$old_input['email']."%");
                })
                ->when(@$old_input['total'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.total_amount',$old_input['total']);
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_orders.oms_order_status',$old_input['order_status_id']);
                });
            $data = $data->orderByRaw("(CASE WHEN oms_orders.order_id > 0 THEN oms_orders.updated_at ELSE oms_place_order.created_at END) DESC")
                ->paginate(20);
            // $data = $data->paginate(20);
            // $data = $this->getOrdersWithImage($data);
            // dd($data->toArray());
        ///
        // dd($data->toArray());
        $searchFormAction = URL::to('orders');
        $orderStatus = OmsOrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        // dd($data->toArray());
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    public function online(){
        $old_input = RequestFacad::all();
        $data = OmsPlaceOrderModel::select('oms_place_order.*')
                ->with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->leftjoin("oms_orders",function($join){
                    $join->on('oms_orders.order_id', '=', 'oms_place_order.order_id');
                    $join->on('oms_orders.store', '=', 'oms_place_order.store');
                })
                ->where('online_approved', 0)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.store',$old_input['by_store']);
                })
                ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.mobile','LIKE',"%".$old_input['telephone']."%");
                })
                ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.firstname','LIKE',"%".$old_input['customer']."%");
                })
                ->when(@$old_input['email'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.email','LIKE',"%".$old_input['email']."%");
                })
                ->when(@$old_input['total'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.total_amount',$old_input['total']);
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_orders.oms_order_status',$old_input['order_status_id']);
                });
            $data = $data->orderByRaw("(CASE WHEN oms_orders.order_id > 0 THEN oms_orders.updated_at ELSE oms_place_order.created_at END) DESC")
                ->paginate(20);
        ///extra variable
        $searchFormAction = URL::to('orders/online');
        $orderStatus = OmsOrderStatusModel::orderBy('id','DESC')->get();
        return view(self::VIEW_DIR.".online",compact('data','searchFormAction','orderStatus','old_input','old_input'));
    }
    function onlineApprove(Request $request){
        $order_id = $request->order_id;
        $store_id = $request->oms_store;

        $qry  = OmsPlaceOrderModel::where('order_id',$order_id)->where('store', $store_id)->update(['online_approved'=>1]);
        if( $qry ){
            OmsActivityLogModel::newLog($order_id,25,$store_id); //25 for Approve online order
        }
        return redirect('online');
    }
    protected function getOrdersWithImage($orders){
        foreach ($orders as $key => $order) {
          $ordered_products = $this->orderedProducts($order);
          foreach ($ordered_products as $orderd_product_key => $orderd_products_value) {
                if(isset($orderd_products_value->product_details) && !empty($orderd_products_value->product_details)){
                    $ToolImage = new ToolImage();
                    if(file_exists($this->website_image_source_path . $orderd_products_value->product_details->image)){
                        $orderd_products_value->product_details->image = $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $orderd_products_value->product_details->image, 100, 100);
                    }else if(strpos($orderd_products_value->product_details->image, "cache/catalog")){
                        // dd("find");
                        continue;
                    }else{
                        // echo "Not <br>". $this->website_image_source_url;
                        $orderd_products_value->product_details->image = $this->website_image_source_url . 'placeholder.png';
                    }
                }
            }
            $order->orderd_products = $ordered_products;
        }
        return $orders;
    }
    protected function orderedProducts($order){
        if( $order->store == 1 ){
            $data = OrderedProductModel::with(['product_details'=>function($query){
                $query->select('product_id','image');
            }])->where('order_id',$order->order_id)->get();
        }else if( $order->store == 2 ){
            $data = DFOrderedProductModel::with(['product_details'=>function($query){
                $query->select('product_id','image');
            }])->where('order_id',$order->order_id)->get();
        }
        return $data;
    }
    public function approveReshipment(Request $request){
        if($request->isMethod('post')){
            $store = $request->store;
            $updqry = OmsOrdersModel::where("order_id",$request->order_id)->where('store',$store)->update(['oms_order_status'=>1,"reship"=>1,"last_shipped_with_provider"=>0,"picklist_courier"=>$request->reassign_courier,'picklist_print'=>NULL]);
            if($updqry){
                   OmsActivityLogModel::newLog($request->order_id,9,$store); //9 is for reship Approved order
                    Session::flash('success','Order successfully Reshiped.');
                    return redirect('orders/reship-orders');
            }
        }
        $old_input = RequestFacad::all();
        $data = OmsPlaceOrderModel::select('oms_place_order.*')
                ->with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->leftjoin("oms_orders",function($join){
                    $join->on('oms_orders.order_id', '=', 'oms_place_order.order_id');
                    $join->on('oms_orders.store', '=', 'oms_place_order.store');
                })
                ->where('oms_orders.reship', -1)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.store',$old_input['by_store']);
                })
                ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.mobile','LIKE',"%".$old_input['telephone']."%");
                })
                ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.firstname','LIKE',"%".$old_input['customer']."%");
                })
                ->when(@$old_input['email'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.email','LIKE',"%".$old_input['email']."%");
                })
                ->when(@$old_input['total'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.total_amount',$old_input['total']);
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_orders.oms_order_status',$old_input['order_status_id']);
                });
            $data = $data->orderByRaw("(CASE WHEN oms_orders.order_id > 0 THEN oms_orders.updated_at ELSE oms_place_order.created_at END) DESC")
                ->paginate(20);
        // dd($data->toArray());
        $searchFormAction = URL::to('orders/reship-orders');
        $orderStatus = OmsOrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        return view(self::VIEW_DIR.".reship_orders",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    public function pickingListAwaiting(){
        $old_input = RequestFacad::all();
        if (isset($old_input['o_id'])){
            return $this->generatePickingList($old_input['o_id']);
        }
        $data = OmsPlaceOrderModel::with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->whereHas('omsOrder',function($q) use($old_input){
                    $q->where('oms_order_status',0)->orderBy("updated_at","DESC");
                })
                ->join("oms_orders","oms_orders.order_id","=","oms_place_order.order_id")
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('order_id',$old_input["order_id"]);
                });
                if( @$old_input['search_by_print'] != "" ){
                    $data = $data->whereHas('omsOrder',function($query) use($old_input){
                        if( $old_input['search_by_print'] == 1 ){
                            return $query->where('picklist_print',$old_input['search_by_print']);
                        }else if( $old_input['search_by_print'] == 0 ){
                            return $query->whereNull('picklist_print');
                        }
                    });
                }
            $data = $data->orderBy("oms_orders.updated_at","DESC");
            $data = $data->paginate(10);
            // dd($data->toArray());
        ///
        $searchFormAction = URL::to('orders/picking-list-awaiting');
        $orderStatus = OmsOrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        // dd($data)->toArray();
        return view(self::VIEW_DIR.".pick_list_view",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    protected function generatePickingList($orderIds = []){
        $orders = OmsPlaceOrderModel::with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->whereIn("order_id",$orderIds)
                ->get();
        // dd($orders->toArray());
        if (sizeof($orders) > 0)
        {
            // Update the print picklist status to 1 in oms table
            foreach ($orders as $key => $order)
            {
                if(isset($order->reseller_id)) {
                    $reseller = OmsUserModel::with('detail')->where('user_id', $order->reseller_id)->first();
                    // dd($reseller->detail->brand_logo);
                    $order['reseller_name'] = $reseller->firstname;
                    $order['reseller_logo'] = $reseller->detail->brand_logo ? Storage::url($reseller->detail->brand_logo) : "";
                }

                // $omsUpdateStatus = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $order[OmsOrdersModel::FIELD_ORDER_ID]);
                // $omsUpdateStatus->update([OmsOrdersModel::FIELD_PICKLIST_PRINT => 1]);
                $omsUpdateStatus = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $order->order_id)->where('store',$order->store);
                $print_status = $omsUpdateStatus->update([OmsOrdersModel::FIELD_PICKLIST_PRINT => 1]);
            }
        }
        // dd($orders);
        // $orders = $this->getOrdersWithImage($orders);
        // dd($orders);
        return view(self::VIEW_DIR . ".print_pick_list", ["orders" => $orders, "pagination" => '']);
    }
    public function updateCustomerDetails(Request $request){
        // dd($request->all());
        $order_id = $request->order_id;
        $store    = $request->store;
        if( $request->isMethod('POST') ){
          //update details
            // dd($request->all());
          $address_1 = $request->address_1;
          $area = $request->area;
          $city = $request->city;
          $street_building = $request->street_building;
          $villa_flat      = $request->villa_flat;
          $telephone = $request->telephone;
          $name = $request->name;
          $google_map_link = $request->google_map;
          $update_array = ["shipping_address_1"=>$address_1,"shipping_city"=>$city,"shipping_city_area"=>$area,"shipping_street_building"=>$street_building,"shipping_villa_flat"=>$villa_flat,'mobile'=>$telephone,'firstname'=>$name,'google_map_link'=>$google_map_link];

          $qry = OmsPlaceOrderModel::where('order_id',$order_id)->where('store',$store)->update($update_array);
          if($qry){
            OmsActivityLogModel::newLog($order_id,21,$store);
          }
          return redirect()->back()->with('success_msg',"Customer details updated successfully in order # ".$order_id);
        }else{
          $data  = OmsPlaceOrderModel::where('store',$store)->where("order_id",$order_id)->first();
          $areas = CityArea::where('city_id', $data->shipping_city_id)->pluck('name');
          $data->areas = $areas->toArray();
          $data->store = $store;
          if( $data  ){
           return response()->json($data);
          }
        }
      }
      public function forceScanning($product_model){
        $manual_checkable = 0;
        $categorySet = explode("-",$product_model);
        if( is_array($categorySet) && count($categorySet) > 1 ){
          $cate = $categorySet[0];
          if( $cate == 'WB' || $cate == 'WC' || $cate == 'WJ' || $cate == 'S'   ){
            $manual_checkable = 1;
          }
        }
        return $manual_checkable;
      }
        public function packOrder(){
            $old_input = RequestFacad::all();
            return view(self::VIEW_DIR.".pack_order",compact('old_input'));
        }
        protected function getProductImage($product_id = '',$store, $width = 0, $height = 0){
            if( $store == 1){
                $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();
                $website_image_source_path = $this->website_image_source_path;
                $website_image_source_url  = $this->website_image_source_url;
            }else if( $store == 2 ){
                $product_image = DFProductsModel::select('image')->where('product_id', $product_id)->first();
                $website_image_source_path = $this->df_website_image_source_path;
                $website_image_source_url  = $this->df_website_image_source_url;
            }

            if($product_image){
                if(file_exists($website_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
                    $ToolImage = new ToolImage();
                    return $ToolImage->resize($website_image_source_path, $website_image_source_url, $product_image->image, $width, $height);
                }else{
                    return $this->opencart_image_url . $product_image->image;
                }
            }else return $this->opencart_image_url . 'placeholder.png';
        }
        public function getPackOrder(){
            $old_input = RequestFacad::all();
            if(count($old_input) > 0){
                $order_id = $old_input['order_id'];

                $order = OmsPlaceOrderModel::with(['orderProducts.product','orderProducts.productOption','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->whereHas('omsOrder',function($query){
                    $query->where('oms_order_status', 0)->where('picklist_print', 1);
                })
                ->where('order_id', $order_id)
                ->first();
            }
            return view(self::VIEW_DIR.'.pack_order_search',compact('order'));
        }

        public function updatePackOrder(){
            // die("testing update_pack_order func");
            $old_input = RequestFacad::all();
            // dd($old_input);
            if(count($old_input) > 0 && $old_input['submit'] == 'update_picked'){
                $order_id = $old_input['order_id'];
                $store    = $old_input['store'];
                // $exists = OmsOrdersModel::select('*')
                // ->where('order_id', $order_id)
                // ->where('oms_order_status', OmsOrderStatusInterface::OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST)
                // ->where('store',$store)
                // ->where('picklist_print', 1)
                // ->exists();
                $order = OmsPlaceOrderModel::with(['orderProducts.product','orderProducts.productOption','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->whereHas('omsOrder',function($query){
                    $query->where('oms_order_status', 0)->where('picklist_print', 1);
                })
                ->where('store',$store)
                ->where('order_id', $order_id)
                ->first();
                // dd($order->toArray());
                // echo "<pre>"; print_r(mixed:value, bool:return=false); die;
                // dd(Input::get('packed'));
                if($order){
                    foreach ($order->orderProducts as $key => $orderProduct) {
                        //entry in packed quantity table
                        $quantity         = $orderProduct->quantity;
                        $product_id       = $orderProduct->product_id;
                        $option_id        = $orderProduct->productOption->option_id;
                        $option_value_id  = $orderProduct->productOption->option_value_id;
                        $OmsInventoryPackedQuantityModel = new OmsInventoryPackedQuantityModel();
                        $OmsInventoryPackedQuantityModel->order_id = $order->order_id;
                        $OmsInventoryPackedQuantityModel->product_id = $product_id;
                        $OmsInventoryPackedQuantityModel->oms_product_id = $product_id;
                        $OmsInventoryPackedQuantityModel->option_id = $orderProduct->productOption->option_id;
                        $OmsInventoryPackedQuantityModel->option_value_id = $orderProduct->productOption->option_value_id;
                        $OmsInventoryPackedQuantityModel->quantity =  $quantity;
                        $OmsInventoryPackedQuantityModel->store = $store;
                        $OmsInventoryPackedQuantityModel->save();

                        $decrement_query = 'IF (onhold_quantity-'. $quantity . ' <= 0, 0, onhold_quantity-'. $quantity . ')';
                        OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $option_id)->where('option_value_id', $option_value_id)->update(array('onhold_quantity' => DB::Raw($decrement_query), 'pack_quantity' => DB::Raw('pack_quantity+'.$quantity) ));
                    }

                        //UPDATE OMS ORDER STATUS
                    $omsOrder = OmsOrdersModel::where('order_id', $order_id)->where('store', $store)->first();
                    $omsOrder->oms_order_status = 1;
                    $omsOrder->updated_at = \Carbon\Carbon::now();
                    $omsOrder->save();
                    OmsActivityLogModel::newLog($order_id,3,$store); //3 is for pack order
                    //create airwaybill
                    $awb_response = app(\App\Http\Controllers\Orders\OrdersAjaxController::class)->forwardForShipping();
                    Session::flash('message', 'Order product packed successfully.');
                    Session::flash('alert-class', 'alert-success');
                    return redirect('/orders/pack/order')->with('packed_order_id',$order_id);
                }else{
                    Session::flash('message', "Order product packed in 'Picklist' status only.");
                    Session::flash('alert-class', 'alert-warning');
                    return redirect('/orders/pack/order');
                }
            }else{
                Session::flash('message', 'Something went wrong, please try again!');
                Session::flash('alert-class', 'alert-warning');
                return redirect('/orders/pack/order');
            }
        }
    public function generateAwb(){
      $ordersStatus = OmsOrderStatusModel::all();
      $shippingProviders = ShippingProvidersModel::where('is_active', 1)->get();
      return view(self::VIEW_DIR . ".generate_awb", ["orderStatus" => $ordersStatus, "shippingProviders" => $shippingProviders]);
    }
    public function awb(){
        $orderIds = Session::get('orderIdsForAWBGenerate')[0];
        Session::put('orderIdsForAWBGenerate', array());
        $orders = collect();
        $orders = OmsPlaceOrderModel::with(['orderProducts.product'])->whereIn('order_id',$orderIds)->get();
        //// Need enhancement
        $order_tracking = AirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
        // dd($order_tracking->toArray());
        $order_tracking_ids = $order_tracking->pluck(AirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
        $shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

        return view(self::VIEW_DIR . ".awb", ['orders' => $orders, 'order_tracking' => $order_tracking, 'shipping_providers' => $shipping_providers]);
    }
    public function awbGenerated(){
        $orders = array();
        if( session('user_group_id') == 5 || session('user_group_id') == 6 ){
            $ordersStatus = OmsOrderStatusModel::whereIn('order_status_id',[3,15,25])->get();
            if( !RequestFacad::all() ){
                RequestFacad::merge(['order_status_id' => 3]);
            }
        }else{
            $ordersStatus = OmsOrderStatusModel::get();
        }
        $omsOrders = OmsOrdersModel::with(['airway_bills','shipping_provider'])
        ->orderBy(OmsOrdersModel::UPDATED_AT, 'DESC')
        ->groupBy('oms_orders.order_id');
        if(RequestFacad::get('order_id')){
            $omsOrders = $omsOrders->where('oms_orders.order_id', Input::get('order_id'));
        }
        if(RequestFacad::get('order_status_id')){
            $omsOrders = $omsOrders->where('oc_order.order_status_id', Input::get('order_status_id'));
        }
        if (RequestFacad::get('shipping_provider_id')){
            $omsOrders = $omsOrders->where('oms_orders.'.OmsOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER, Input::get('shipping_provider_id'));
        }
        if (RequestFacad::get('date_from') && RequestFacad::get('date_to')){
            $date_from = Carbon::createFromFormat("Y-m-d", RequestFacad::get('date_from'))->toDateString();
            $date_to = Carbon::createFromFormat("Y-m-d",RequestFacad::get('date_to'))->toDateString();
            $omsOrders = $omsOrders->whereDate('awb.'.AirwayBillTrackingModel::CREATED_AT, '>=', $date_from)
            ->whereDate('awb.'.AirwayBillTrackingModel::CREATED_AT, '<=', $date_to);
        }
        if (RequestFacad::get('awb_number')){
            $omsOrders = $omsOrders->where('awb.'.AirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER, RequestFacad::get('awb_number'));
        }
        if( RequestFacad::get('date_modified') != "" ){
          $omsOrders = $omsOrders->whereDate('oms_orders.updated_at',RequestFacad::get('date_modified'));
        }
        $omsOrders = $omsOrders->paginate(20)->appends(RequestFacad::all());
        // dd($omsOrders->toArray());
        $shippingProviders = ShippingProvidersModel::orderBy('is_active', 'DESC')->get();
        $ordersStatus      = OmsOrderStatusModel::all();

        return view(self::VIEW_DIR . ".airway_bill_generated_orders",compact('omsOrders','shippingProviders','ordersStatus'));
    }
    public function shipOrdersToCourier(){
        // $tab_links = $this->tab_links();
        return view(self::VIEW_DIR . ".ship_orders_to_courier");
    }
    public function shipOrders(){
        $orderIds = RequestFacad::get('generate-awb-chbx'); // ordersID array
        $stores   = RequestFacad::get('store');
        if ($orderIds){
            try{
                foreach ($orderIds as $orderId)
                {
                    $omsOrder = OmsOrdersModel::where("order_id", $orderId)->where('store',$stores[$orderId])->first();

                    if ( $omsOrder->oms_order_status == 2 ){
                        if( $omsOrder->reship != 1 ){
                          $this->shippedInventoryQuantity($omsOrder);
                        }
                        //UPDATE OMS ORDER STATUS
                        $omsOrder->oms_order_status = 3; // 3 for shipped status
                        $omsOrder->updated_at = \Carbon\Carbon::now();
                        $omsOrder->save();

                        $awb_number = AirwayBillTrackingModel::select('airway_bill_number')->where('order_id', $orderId)->where('store',$omsOrder->store)->first();

					    OmsActivityLogModel::newLog($orderId,5, $omsOrder->store); //5 is for Ship order

                        // if($openCartOrder->reseller_id > 0) {
                        //     $this->manageResellerAccount($orderId ,$openCartOrder->reseller_id, 1);
                        // }
                    }else{
                        throw new \Exception("Order can only be shipped in 'AWB Generated' status.");
                    }
                }

                Session::flash('message', "Orders Shipped successfully.");
                Session::flash('alert-class', 'alert-success');
                return redirect('/orders/ship/order');
            }
            catch (\Exception $e){
                Session::flash('message', $e->getMessage());
                Session::flash('alert-class', 'alert-danger');
                return redirect('/orders/ship/order');
            }
        }else{
            Session::flash('message', 'Please select order to ship.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('/orders/ship/order');
        }
    }
    public function shippedInventoryQuantity($omsOrder){
        $order_id = $omsOrder->order_id;
        $store_id = $omsOrder->store;
        $order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryShippedQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( !$check_exist ){
                    $new_shipped = new OmsInventoryShippedQuantityModel();
                    $new_shipped->order_id        = $order_product->order_id;
                    $new_shipped->product_id      = $order_product->product_id;
                    $new_shipped->option_id       = $order_product->productOption->option_id;
                    $new_shipped->option_value_id = $order_product->productOption->option_value_id;
                    $new_shipped->quantity        = $order_quantity;
                    $new_shipped->store           = $order_product->store_id;
                    if( $new_shipped->save() ){
                        $decrement_query = 'IF (pack_quantity-' . $order_quantity . ' <= 0, 0, pack_quantity-' . $order_quantity . ')';
                        OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                        ->update(['pack_quantity'=>DB::raw($decrement_query),'shipped_quantity'=>DB::raw("shipped_quantity + $order_quantity")]);
                    }
                }
            }
        }
    }
    public function readyForReturn(Request $request){
        if( $request->isMethod('post') ){
            // dd($request->all());
            $activity = new OmsActivityLogModel();
            $activity->ref_id = $request->order_id_for_return;
            $activity->store = $request->store_id;
            $activity->created_by = session('user_id');
            if(  isset( $request->approve_return_button ) && $request->approve_return_button != "" ){
              $update_qry = OmsOrdersModel::where("ready_for_return",1)->where('store',$request->store_id)->where("order_id",$request->order_id_for_return)->update(['ready_for_return'=>2]);
              if( $update_qry ){
                $activity->activity_id = 27;
                $activity->comment = $request->admin_comment;
                $activity->save();
              }
            }else if( isset( $request->disapprove_return_button ) && $request->disapprove_return_button != "" ){
              $update_qry = OmsOrdersModel::where("ready_for_return",1)->where('store',$request->store_id)->where("order_id",$request->order_id_for_return)->update(['ready_for_return'=>0]);
              if( $update_qry ){
                $activity->activity_id = 28;
                $activity->comment = $request->admin_comment;
                $activity->save();
              }
            }
            redirect()->back();
          }
        $old_input = RequestFacad::all();
        $data = OmsPlaceOrderModel::select('oms_place_order.*')
                ->with(['orderProducts.product','omsOrder.assignedCourier','omsOrder.generatedCourier','omsStore'])
                ->leftjoin("oms_orders",function($join){
                    $join->on('oms_orders.order_id', '=', 'oms_place_order.order_id');
                    $join->on('oms_orders.store', '=', 'oms_place_order.store');
                })
                ->where('oms_orders.ready_for_return', 1)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.store',$old_input['by_store']);
                })
                ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.mobile','LIKE',"%".$old_input['telephone']."%");
                })
                ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.firstname','LIKE',"%".$old_input['customer']."%");
                })
                ->when(@$old_input['email'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.email','LIKE',"%".$old_input['email']."%");
                })
                ->when(@$old_input['total'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_order.total_amount',$old_input['total']);
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_orders.oms_order_status',$old_input['order_status_id']);
                });
            $data = $data->orderByRaw("(CASE WHEN oms_orders.order_id > 0 THEN oms_orders.updated_at ELSE oms_place_order.created_at END) DESC")
                ->paginate(20);
        ///extra variable
        $searchFormAction = URL::to('orders/ready/for/return');
        $orderStatus = OmsOrderStatusModel::orderBy('id','DESC')->get();
        return view(self::VIEW_DIR.".ready_for_return",compact('data','searchFormAction','orderStatus','old_input','old_input'));
    }
    public function returnOrder(){
        $old_input = RequestFacad::all();
        return view(self::VIEW_DIR.".return_order",compact('old_input'));
    }
    public function getReturnOrder(){
        if(count(RequestFacad::all()) > 0){
            $order_id = RequestFacad::get('order_id');
            $order = OmsOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 3) //3 for shipped order
            // ->where('store',$this->store)
            ->first();

            if( $order && ( $order->ready_for_return == 1 OR $order->ready_for_return === 0 ) ){
              echo "<h2 style='color:red'>Order cannot be return, contact operation team.</h2>";
              return;
            }
            if($order){
                $order->order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id", $order->store)->get();
            }else{
                echo "<h2 style='color:red'>Order cannot be return in this status OR order not found.</h2>";
                return;
            }
        }
        return view(self::VIEW_DIR.'.return_order_search', ["order" => $order]);
    }
    public function updateReturnOrder(){
        // echo "<pre>"; print_r($_SERVER); die;
        // dd(RequestFacad::all());
        if(count(RequestFacad::all()) > 0 && RequestFacad::get('submit') == 'update_returned'){
            $order_id  = RequestFacad::get('order_id');
            $store_id = RequestFacad::get('oms_store');
            $exists    = OmsOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 3)  //3 for shipped status
            ->where('store',$store_id)
            ->exists();

            if($exists){
                $order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
                // dd($order_products->toArray());
                if( $order_products ){
                    $order_quantity = 0;
                    $quantity_data = "";
                    foreach( $order_products as $key => $order_product ){
                        $order_quantity = $order_product->quantity;
                        $product_id      = $order_product->product_id;
                        $option_id       = $order_product->productOption->option_id;
                        $option_value_id = $order_product->productOption->option_value_id;
                        $check_exist = OmsInventoryReturnQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$product_id)
                        ->where('option_id',$option_id)->where('option_value_id',$option_value_id)->where('store',$order_product->store_id)->first();
                        if( !$check_exist ){
                            $new_shipped = new OmsInventoryReturnQuantityModel();
                            $new_shipped->order_id        = $order_product->order_id;
                            $new_shipped->product_id      = $product_id;
                            $new_shipped->oms_product_id  = $product_id;
                            $new_shipped->option_id       = $option_id;
                            $new_shipped->option_value_id = $option_value_id;
                            $new_shipped->quantity        = $order_quantity;
                            $new_shipped->store           = $order_product->store_id;
                            if( $new_shipped->save() ){
                                $decrement_query     = 'IF (shipped_quantity-' . $order_quantity . ' <= 0, 0, shipped_quantity-' . $order_quantity . ')';
                                $decrement_available = 'IF (available_quantity-' . $order_quantity . ' <= 0, 0, available_quantity-' . $order_quantity . ')';
                                $return_query = OmsInventoryProductOptionModel::where(["product_id"=>$product_id,"product_option_id"=>$order_product->product_option_id])
                                ->update(['shipped_quantity'=>DB::raw($decrement_query),'return_quantity'=>DB::raw("return_quantity + $order_quantity"),"available_quantity"=>DB::raw($decrement_available)]);
                                if( $return_query ){
                                    //stock history
                                    $quantity_data .= $order_product->option_value . "-(" .$order_quantity. "), ";
                                    $comment = "This quantity added is returned from the order number #".$order_id." from Customer.<br>Quantity: ". rtrim($quantity_data, ", ");
                                    $OmsInventoryAddStockHistoryModel = new OmsInventoryAddStockHistoryModel();
                                    $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_PRODUCT_ID} = $product_id;
                                    $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_USER_ID} = session('user_id');
                                    $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_COMMENT} = $comment;
                                    if( $OmsInventoryAddStockHistoryModel->save() ){
                                        //stock history details details
                                        $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
                                        $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
                                        $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $product_id;
                                        $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID}  = $option_id;
                                        $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $option_value_id;
                                        $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $order_quantity;
                                        $OmsInventoryAddStockOptionModel->save();
                                    }
                                }
                            }
                        }
                    }
                    //update order status
                    OmsOrdersModel::where("order_id",$order_id)->update(['oms_order_status'=>6]);  //6 is for return order.
                }
                OmsActivityLogModel::newLog($order_id,8, $store_id); //8 is for return order
                Session::flash('message', 'Order returned successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('/orders/return/order');
            }else{
                Session::flash('message', "Order product returned in 'Shipped' status only OR order not found.");
                Session::flash('alert-class', 'alert-danger');
                return redirect('/orders/return/order');
            }
        }
    }
    public function deliverSingleOrder($order_id,$store_id){
            try{
                $omsOrder = OmsOrdersModel::where("order_id", $order_id)->where('store',$store_id)->first();

                if( $omsOrder->oms_order_status == 3 ){
                    //delivered quantity in oms_inventory_delivered_quantity and oms_inventory_product_option tables
                    $order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
                    // dd($order_products->toArray());
                    if( $order_products ){
                        $order_quantity = 0;
                        foreach( $order_products as $key => $order_product ){
                            $order_quantity = $order_product->quantity;
                            $product_id      = $order_product->product_id;
                            $option_id       = $order_product->productOption->option_id;
                            $option_value_id = $order_product->productOption->option_value_id;
                            $check_exist = OmsInventoryDeliveredQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$product_id)
                            ->where('option_id',$option_id)->where('option_value_id',$option_value_id)->where('store',$order_product->store_id)->first();
                            if( !$check_exist ){
                                $new_deliver = new OmsInventoryDeliveredQuantityModel();
                                $new_deliver->order_id        = $order_product->order_id;
                                $new_deliver->product_id      = $product_id;
                                $new_deliver->option_id       = $option_id;
                                $new_deliver->option_value_id = $option_value_id;
                                $new_deliver->quantity        = $order_quantity;
                                $new_deliver->store           = $order_product->store_id;
                                if( $new_deliver->save() ){
                                    $decrement_query     = 'IF (shipped_quantity-' . $order_quantity . ' <= 0, 0, shipped_quantity-' . $order_quantity . ')';
                                    $deliver_query = OmsInventoryProductOptionModel::where(["product_id"=>$product_id,"product_option_id"=>$order_product->product_option_id])
                                    ->update(['shipped_quantity'=>DB::raw($decrement_query),'delivered_quantity'=>DB::raw("delivered_quantity + $order_quantity")]);
                                }
                            }
                        }
                    }
                    // UPDATE OMS ORDER STATUS
                    $omsOrder->oms_order_status = 4;
                    $omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $qry = $omsOrder->save();
                    //oms activity log
                    $check_activity = OmsActivityLogModel::where("ref_id",$order_id)->where('store',$store_id)->where("activity_id",7)->first();
                    if( !$check_activity ){
                        $activity_ins_obj = new OmsActivityLogModel();
                        $activity_ins_obj->activity_id = 7; //7 is for deliver order
                        $activity_ins_obj->ref_id = $order_id;
                        $activity_ins_obj->store = $store_id;
                        $activity_ins_obj->created_by_courier = $omsOrder->last_shipped_with_provider;
                        $activity_ins_obj->created_by = 0;
                        $activity_ins_obj->created_at = date('Y-m-d H:i:s');
                        $activity_ins_obj->save();
                    }
                    if($qry){
                        AirwayBillTrackingModel::where(['order_id'=>$order_id,"shipping_provider_id"=>$omsOrder->last_shipped_with_provider,'store'=>$store_id])->update(["courier_delivered"=>1]);
                    }
                }else{
                    throw new \Exception("Order can be Delivered in 'Shipped' status only.");
                }
            }catch (\Exception $e){
              // dd($e);die;
                // Session::flash('message', $e->getMessage());
                // Session::flash('alert-class', 'alert-danger');
                // return redirect('/orders/deliver-orders');
            }
    }
    public function printLabel($order_id){
        $print_label = '';

        $order = OmsOrdersModel::select('*')->where('order_id', $order_id)->first();
        if($order){
            $products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id", $order->store)->get();
            // dd( $products->toArray() );
            $label_array = array();

            foreach ($products as $product) {
                    $color = '';
                    $option_array = array();

                    $barcode = $product->product_id;
                    $barcode .= $product->productOption->option_value_id;

                        // $option_array = array();

                        $option_array = array(
                            'color'   =>  $product->product?->option_name
                        );
                        $option_array['size'] = "";
                        if( $product->product?->option_value > 0 ){
                            $option_array['size']  = $product->option_value;
                        }

                        $print_label = "big";
                        for ($i=0; $i < $product->quantity; $i++) {
                            if($print_label === 'big'){
                                $label_array['big'][] = array(
                                    'product_image' =>  URL::asset('uploads/inventory_products/'.$product->product->image),
                                    'product_sku'   =>  $product->sku,
                                    'option'        =>  $option_array,
                                    'barcode'       =>  $barcode,
                                );
                            }else{
                                $label_array['small'][] = array(
                                    'product_image' =>  URL::asset('uploads/inventory_products/'.$product->product->image),
                                    'product_sku'   =>  $product->sku,
                                    'option'        =>  $option_array,
                                    'barcode'       =>  $barcode,
                                );
                            }
                        }
            }
        }

        return view(self::VIEW_DIR.'.print_label', ["labels" => $label_array,"label_type" => $print_label]);
    }
}
