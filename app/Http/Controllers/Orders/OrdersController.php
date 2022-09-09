<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\ExchangeOrders\AreaModel AS DFAreaModel;
use App\Models\DressFairOpenCart\Orders\OrderStatusModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsActivityModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\ShippingProvidersModel;
use App\Models\OpenCart\ExchangeOrders\AreaModel;
use App\Models\OpenCart\Orders\OrderedProductModel;
use App\Models\DressFairOpenCart\Orders\OrderHistory AS DFOrderHistory;
use App\Models\OpenCart\Orders\OrderHistory;
use App\Models\DressFairOpenCart\Orders\OrderedProductModel AS DFOrderedProductModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsUserModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\OpenCart\Orders\OrderOptionsModel;
use App\Models\DressFairOpenCart\Orders\OrderOptionsModel AS DFOrderOptionsModel;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\OptionDescriptionModel AS DFOptionDescriptionModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\OpenCart\Products\ProductOptionValueModel;
use App\Models\DressFairOpenCart\Products\ProductOptionValueModel AS DFProductOptionValueModel;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
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
    // public function index(){
    //     $old_input = Request::all();
    //     // dd($old_input);
    //     $ba_orders = OrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
    //             $query->whereNotIn('code',['tax']);
    //          }])
    //         ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,
    //         shipping_city,shipping_zone,date_added,date_modified,
    //         1 AS store"))
    //         // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
    //         ->where('order_type', 'like', 'normal')
    //         ->where('order_status_id', '>', 0)
    //         ->where('online_approved', 1)
    //         ->where('reseller_approve', 1)
    //         ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
    //             return $query->where('order_id',$old_input['order_id']);
    //         })
    //         ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
    //             return $query->where('order_status_id',$old_input['order_status_id']);
    //         })
    //         ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
    //             return $query->whereDate('date_added','>=',$old_input['date_from']);
    //         })
    //         ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
    //             return $query->whereDate('date_added','<=',$old_input['date_to']);
    //         });
    //     //dressfair orders
    //     $df_orders = DFOrdersModel::with(['status', 'orderd_products','orderd_totals'=>function($query){
    //         $query->whereNotIn('code',['tax']);
    //         }])
    //         ->select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,
    //         shipping_city,shipping_zone,date_added,date_modified,
    //         2 AS store"))
    //         // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
    //         ->where('order_type', 'like', 'normal')
    //         ->where('order_status_id', '>', 0)
    //         ->where('online_approved', 1)
    //         ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
    //             return $query->where('order_id',$old_input['order_id']);
    //         })
    //         ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
    //             return $query->where('order_status_id',$old_input['order_status_id']);
    //         })
    //         ->when(@$old_input['date_from'] != "",function($query) use ($old_input){
    //             return $query->whereDate('date_added','>=',$old_input['date_from']);
    //         })
    //         ->when(@$old_input['date_to'] != "",function($query) use ($old_input){
    //             return $query->whereDate('date_added','<=',$old_input['date_to']);
    //         });
    //     if( @$old_input['by_store'] == 1 ){
    //         $data = $ba_orders->paginate(20);
    //     }else if( @$old_input['by_store'] == 2 ){
    //         $data = $df_orders->paginate(20);
    //     }else{
    //         $data = $ba_orders->union($df_orders)->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc');
    //         $data = $data->paginate(20);
    //     }
    //     // dd($data->toArray());
    //     $searchFormAction = URL::to('orders');
    //     $orderStatus = OrderStatusModel::all();
    //     return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input'));
    // }
    public function index(){
        $old_input = RequestFacad::all();
        $data = DB::table("oms_place_order AS opo")
                ->leftjoin("oms_orders AS ord",function($join){
                    $join->on("ord.order_id","=","opo.order_id");
                    $join->on("ord.store","=","opo.store");
                })
              ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
                $join->on("baord.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("1"));
              })
              ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
                $join->on("dford.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("2"));
              })
            // ->join("airwaybill_tracking AS awbt",function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
             ->leftjoin(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
              $join->on('awbt.order_id','=','ord.order_id');
              $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
             })
             ->leftjoin("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
             ->select(DB::raw("opo.order_id,ord.oms_order_status,ord.reship,opo.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS amount,
                  (CASE WHEN opo.store = 1 THEN baord.payment_code WHEN opo.store = 2 THEN dford.payment_code ELSE 0 END) AS payment_code,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_1 WHEN opo.store = 2 THEN dford.shipping_address_1 ELSE 0 END) AS shipping_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_2 WHEN opo.store = 2 THEN dford.shipping_address_2 ELSE 0 END) AS shipping_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_area WHEN opo.store = 2 THEN dford.shipping_area ELSE 0 END) AS shipping_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_zone WHEN opo.store = 2 THEN dford.shipping_zone ELSE 0 END) AS shipping_zone,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_1 WHEN opo.store = 2 THEN dford.payment_address_1 ELSE 0 END) AS payment_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_2 WHEN opo.store = 2 THEN dford.payment_address_2 ELSE 0 END) AS payment_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.payment_area WHEN opo.store = 2 THEN dford.payment_area ELSE 0 END) AS payment_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_city WHEN opo.store = 2 THEN dford.shipping_city ELSE 0 END) AS shipping_city,
                  (CASE WHEN opo.store = 1 THEN baord.firstname WHEN opo.store = 2 THEN dford.firstname ELSE 0 END) AS firstname,
                  (CASE WHEN opo.store = 1 THEN baord.lastname WHEN opo.store = 2 THEN dford.lastname ELSE 0 END) AS lastname,
                  (CASE WHEN opo.store = 1 THEN baord.telephone WHEN opo.store = 2 THEN dford.telephone ELSE 0 END) AS telephone,
                  (CASE WHEN opo.store = 1 THEN baord.email WHEN opo.store = 2 THEN dford.email ELSE 0 END) AS email,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS total,
                  (CASE WHEN opo.store = 1 THEN baord.date_modified WHEN opo.store = 2 THEN dford.date_modified ELSE 0 END) AS date_modified,
                  (CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) AS date_added
                "))
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_status_id = '.$old_input["order_status_id"].' WHEN opo.store = 2 THEN dford.order_status_id = '.$old_input["order_status_id"].' ELSE 0 END)');
                });
            // if( @$old_input['order_id'] != "" ){
            //     $data = $data->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
            // }
            $data = $data->orderByRaw("(CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) DESC")
                ->paginate(20);
            $data = $this->getOrdersWithImage($data);
            // dd($data->toArray());
        ///
        $searchFormAction = URL::to('orders');
        $orderStatus = OrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        // dd($data)->toArray();
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    public function online(){
        $old_input = RequestFacad::all();

        $ba_orders = OrdersModel::
            select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 0)
            ->where('reseller_approve', 1);

        //dressfair orders
        $df_orders = DFOrdersModel::
            select(DB::raw("order_id,firstname,lastname,telephone,alternate_number,email,total,shipping_address_1,shipping_address_2,shipping_city,shipping_zone,date_added,date_modified"))
            // ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc')
            ->where('order_type', 'like', 'normal')
            ->where('order_status_id', '>', 0)
            ->where('online_approved', 0);
        // fileter by store
        if( @$old_input['by_store'] == 1 ){
            $data = $ba_orders->paginate(20);
        }else if( @$old_input['by_store'] == 2 ){
            $data = $df_orders->paginate(20);
        }else{
            // die("elase");
            $data = $ba_orders->union($df_orders)
            ->orderBy(OrdersModel::FIELD_DATE_MODIFIED, 'desc');
            $data = $data->paginate(20);
        }
        // dd($data->toArray());
        ///extra variable
        $searchFormAction = URL::to('orders/online');
        $orderStatus = OrderStatusModel::all();
        return view(self::VIEW_DIR.".online",compact('data','searchFormAction','orderStatus','old_input','old_input'));
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
                if( $store == 1 ){
                    $ocupd = OrdersModel::where("order_id",$store)->update(['order_status_id'=>2]);
                }else if(  $store == 2 ){
                    $ocupd = DFOrdersModel::where("order_id",$store)->update(['order_status_id'=>2]);
                }
                if($ocupd){
                   OmsActivityLogModel::newLog($request->order_id,9,$store); //9 is for reship Approved order
                    Session::flash('success','Order successfully Reshiped.');
                }
            }
        }
        $old_input = RequestFacad::all();
        $data = DB::table("oms_place_order AS opo")
                ->leftjoin("oms_orders AS ord",function($join){
                    $join->on("ord.order_id","=","opo.order_id");
                    $join->on("ord.store","=","opo.store");
                })
              ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
                $join->on("baord.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("1"));
              })
              ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
                $join->on("dford.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("2"));
              })
            // ->join("airwaybill_tracking AS awbt",function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
             ->leftjoin(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
              $join->on('awbt.order_id','=','ord.order_id');
              $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
             })
             ->leftjoin("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
             ->select(DB::raw("opo.order_id,ord.oms_order_status,ord.reship,opo.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS amount,
                  (CASE WHEN opo.store = 1 THEN baord.payment_code WHEN opo.store = 2 THEN dford.payment_code ELSE 0 END) AS payment_code,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_1 WHEN opo.store = 2 THEN dford.shipping_address_1 ELSE 0 END) AS shipping_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_2 WHEN opo.store = 2 THEN dford.shipping_address_2 ELSE 0 END) AS shipping_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_area WHEN opo.store = 2 THEN dford.shipping_area ELSE 0 END) AS shipping_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_zone WHEN opo.store = 2 THEN dford.shipping_zone ELSE 0 END) AS shipping_zone,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_1 WHEN opo.store = 2 THEN dford.payment_address_1 ELSE 0 END) AS payment_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_2 WHEN opo.store = 2 THEN dford.payment_address_2 ELSE 0 END) AS payment_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.payment_area WHEN opo.store = 2 THEN dford.payment_area ELSE 0 END) AS payment_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_city WHEN opo.store = 2 THEN dford.shipping_city ELSE 0 END) AS shipping_city,
                  (CASE WHEN opo.store = 1 THEN baord.firstname WHEN opo.store = 2 THEN dford.firstname ELSE 0 END) AS firstname,
                  (CASE WHEN opo.store = 1 THEN baord.lastname WHEN opo.store = 2 THEN dford.lastname ELSE 0 END) AS lastname,
                  (CASE WHEN opo.store = 1 THEN baord.telephone WHEN opo.store = 2 THEN dford.telephone ELSE 0 END) AS telephone,
                  (CASE WHEN opo.store = 1 THEN baord.email WHEN opo.store = 2 THEN dford.email ELSE 0 END) AS email,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS total,
                  (CASE WHEN opo.store = 1 THEN baord.date_modified WHEN opo.store = 2 THEN dford.date_modified ELSE 0 END) AS date_modified,
                  (CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) AS date_added
                "))
                ->where('reship','-1')
                ->where('oms_order_status',3)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_status_id = '.$old_input["order_status_id"].' WHEN opo.store = 2 THEN dford.order_status_id = '.$old_input["order_status_id"].' ELSE 0 END)');
                });
            // if( @$old_input['order_id'] != "" ){
            //     $data = $data->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
            // }
            $data = $data->orderByRaw("(CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) DESC")
                ->paginate(20);
            $data = $this->getOrdersWithImage($data);
            // dd($data->toArray());
        ///
        $searchFormAction = URL::to('orders/reship-orders');
        $orderStatus = OrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        return view(self::VIEW_DIR.".reship_orders",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    public function pickingListAwaiting(){
        $old_input = RequestFacad::all();
        if (isset($old_input['o_id'])){
            return $this->generatePickingList($old_input['o_id']);
        }
        $data = DB::table("oms_place_order AS opo")
                ->leftjoin("oms_orders AS ord",function($join){
                    $join->on("ord.order_id","=","opo.order_id");
                    $join->on("ord.store","=","opo.store");
                })
              ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
                $join->on("baord.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("1"));
              })
              ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
                $join->on("dford.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("2"));
              })
            // ->join("airwaybill_tracking AS awbt",function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
             ->leftjoin(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
              $join->on('awbt.order_id','=','ord.order_id');
              $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
             })
             ->leftjoin("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
             ->select(DB::raw("opo.order_id,ord.oms_order_status,ord.reship,ord.picklist_print,opo.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS amount,
                  (CASE WHEN opo.store = 1 THEN baord.payment_code WHEN opo.store = 2 THEN dford.payment_code ELSE 0 END) AS payment_code,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_1 WHEN opo.store = 2 THEN dford.shipping_address_1 ELSE 0 END) AS shipping_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_address_2 WHEN opo.store = 2 THEN dford.shipping_address_2 ELSE 0 END) AS shipping_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_area WHEN opo.store = 2 THEN dford.shipping_area ELSE 0 END) AS shipping_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_zone WHEN opo.store = 2 THEN dford.shipping_zone ELSE 0 END) AS shipping_zone,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_1 WHEN opo.store = 2 THEN dford.payment_address_1 ELSE 0 END) AS payment_address_1,
                  (CASE WHEN opo.store = 1 THEN baord.payment_address_2 WHEN opo.store = 2 THEN dford.payment_address_2 ELSE 0 END) AS payment_address_2,
                  (CASE WHEN opo.store = 1 THEN baord.payment_area WHEN opo.store = 2 THEN dford.payment_area ELSE 0 END) AS payment_area,
                  (CASE WHEN opo.store = 1 THEN baord.shipping_city WHEN opo.store = 2 THEN dford.shipping_city ELSE 0 END) AS shipping_city,
                  (CASE WHEN opo.store = 1 THEN baord.firstname WHEN opo.store = 2 THEN dford.firstname ELSE 0 END) AS firstname,
                  (CASE WHEN opo.store = 1 THEN baord.lastname WHEN opo.store = 2 THEN dford.lastname ELSE 0 END) AS lastname,
                  (CASE WHEN opo.store = 1 THEN baord.telephone WHEN opo.store = 2 THEN dford.telephone ELSE 0 END) AS telephone,
                  (CASE WHEN opo.store = 1 THEN baord.email WHEN opo.store = 2 THEN dford.email ELSE 0 END) AS email,
                  (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS total,
                  (CASE WHEN opo.store = 1 THEN baord.date_modified WHEN opo.store = 2 THEN dford.date_modified ELSE 0 END) AS date_modified,
                  (CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) AS date_added
                "))
                ->where('oms_order_status',0)
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_status_id = '.$old_input["order_status_id"].' WHEN opo.store = 2 THEN dford.order_status_id = '.$old_input["order_status_id"].' ELSE 0 END)');
                })
                ->when(@$old_input['search_by_print'] != "",function($query) use ($old_input){
                    if( $old_input['search_by_print'] == 1 ){
                        return $query->where('picklist_print',$old_input['search_by_print']);
                    }else if( $old_input['search_by_print'] == 0 ){
                       return $query->whereNull('picklist_print');
                    }

                });
            // if( @$old_input['order_id'] != "" ){
            //     $data = $data->whereRaw('(CASE WHEN opo.store = 1 THEN baord.order_id = '.$old_input["order_id"].' WHEN opo.store = 2 THEN dford.order_id = '.$old_input["order_id"].' ELSE 0 END)');
            // }
            $data = $data->orderByRaw("(CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) DESC")
                ->paginate(20);
            $data = $this->getOrdersWithImage($data);
            // dd($data->toArray());
        ///
        $searchFormAction = URL::to('orders/picking-list-awaiting');
        $orderStatus = OrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        // dd($data)->toArray();
        return view(self::VIEW_DIR.".pick_list_view",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }
    protected function generatePickingList($orderIds = []){
        $orders = DB::table("oms_place_order AS opo")
                ->leftjoin("oms_orders AS ord",function($join){
                    $join->on("ord.order_id","=","opo.order_id");
                    $join->on("ord.store","=","opo.store");
                })
            ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
                $join->on("baord.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("1"));
            })
            ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
                $join->on("dford.order_id","=","opo.order_id");
                $join->on("opo.store","=",DB::raw("2"));
            })
            // ->join("airwaybill_tracking AS awbt",function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
            ->leftjoin(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
            $join->on('awbt.order_id','=','ord.order_id');
            $join->on('awbt.shipping_provider_id','=','ord.picklist_courier');
            })
            ->leftjoin("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
            ->select(DB::raw("opo.order_id,ord.oms_order_status,ord.reship,opo.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS amount,
                (CASE WHEN opo.store = 1 THEN baord.currency_code WHEN opo.store = 2 THEN dford.currency_code ELSE 0 END) AS currency_code,
                (CASE WHEN opo.store = 1 THEN baord.payment_code WHEN opo.store = 2 THEN dford.payment_code ELSE 0 END) AS payment_code,
                (CASE WHEN opo.store = 1 THEN baord.shipping_address_1 WHEN opo.store = 2 THEN dford.shipping_address_1 ELSE 0 END) AS shipping_address_1,
                (CASE WHEN opo.store = 1 THEN baord.shipping_address_2 WHEN opo.store = 2 THEN dford.shipping_address_2 ELSE 0 END) AS shipping_address_2,
                (CASE WHEN opo.store = 1 THEN baord.shipping_area WHEN opo.store = 2 THEN dford.shipping_area ELSE 0 END) AS shipping_area,
                (CASE WHEN opo.store = 1 THEN baord.shipping_zone WHEN opo.store = 2 THEN dford.shipping_zone ELSE 0 END) AS shipping_zone,
                (CASE WHEN opo.store = 1 THEN baord.payment_address_1 WHEN opo.store = 2 THEN dford.payment_address_1 ELSE 0 END) AS payment_address_1,
                (CASE WHEN opo.store = 1 THEN baord.payment_address_2 WHEN opo.store = 2 THEN dford.payment_address_2 ELSE 0 END) AS payment_address_2,
                (CASE WHEN opo.store = 1 THEN baord.payment_area WHEN opo.store = 2 THEN dford.payment_area ELSE 0 END) AS payment_area,
                (CASE WHEN opo.store = 1 THEN baord.shipping_city WHEN opo.store = 2 THEN dford.shipping_city ELSE 0 END) AS shipping_city,
                (CASE WHEN opo.store = 1 THEN baord.firstname WHEN opo.store = 2 THEN dford.firstname ELSE 0 END) AS firstname,
                (CASE WHEN opo.store = 1 THEN baord.lastname WHEN opo.store = 2 THEN dford.lastname ELSE 0 END) AS lastname,
                (CASE WHEN opo.store = 1 THEN baord.telephone WHEN opo.store = 2 THEN dford.telephone ELSE 0 END) AS telephone,
                (CASE WHEN opo.store = 1 THEN baord.alternate_number WHEN opo.store = 2 THEN dford.alternate_number ELSE 0 END) AS alternate_number,
                (CASE WHEN opo.store = 1 THEN baord.email WHEN opo.store = 2 THEN dford.email ELSE 0 END) AS email,
                (CASE WHEN opo.store = 1 THEN baord.total WHEN opo.store = 2 THEN dford.total ELSE 0 END) AS total,
                (CASE WHEN opo.store = 1 THEN baord.comment WHEN opo.store = 2 THEN dford.comment ELSE 0 END) AS comment,
                (CASE WHEN opo.store = 1 THEN baord.date_modified WHEN opo.store = 2 THEN dford.date_modified ELSE 0 END) AS date_modified,
                (CASE WHEN opo.store = 1 THEN baord.date_added WHEN opo.store = 2 THEN dford.date_added ELSE 0 END) AS date_added
                "))
                ->whereIn("opo.order_id",$orderIds)
                ->get();

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
        $orders = $this->getOrdersWithImage($orders);
        // dd($orders);
        return view(self::VIEW_DIR . ".print_pick_list", ["orders" => $orders, "pagination" => '']);
    }
    public function updateCustomerDetails(Request $request){
        $order_id = $request->order_id;
        $store    = $request->store;
        if( $request->isMethod('POST') ){
          //update details
          $address_1 = $request->address_1;
          $area = $request->area;
          $city = $request->city;
          $adress_2 = $request->street_building.",-".$request->villa_flat;
          $telephone = $request->telephone;
          $name = $request->name;
          $google_map_link = $request->google_map;
          $update_array = ["payment_address_1"=>$address_1,"payment_address_2"=>$adress_2,"payment_city"=>$city,"payment_area"=>$area,"payment_zone"=>$city,"shipping_address_1"=>$address_1,"shipping_address_2"=>$adress_2,"shipping_city"=>$city,"shipping_area"=>$area,"shipping_zone"=>$city,'telephone'=>$telephone,'firstname'=>$name,'google_map_link'=>$google_map_link];
          if( $store == 1 ){
            $qry = OrdersModel::where('order_id',$order_id)->update($update_array);
          }else if(  $store == 2 ){
            $qry = DFOrdersModel::where('order_id',$order_id)->update($update_array);
          }
          if($qry){
            OmsActivityLogModel::newLog($order_id,21,$store);
          }
          return redirect()->back()->with('success_msg',"Customer details updated successfully in order # ".$order_id);
        }else{
          //get details
          if( $store == 1 ){
            $data = OrdersModel::select("payment_address_1","shipping_address_2","shipping_zone","payment_area",'telephone','firstname','google_map_link','payment_zone_id')->where("order_id",$order_id)->first();
            $areas = AreaModel::select('name')->where('zone_id', $data->payment_zone_id)->where('status', 1)->pluck('name');
          }else if( $store == 2 ){
            $data  = DFOrdersModel::select("payment_address_1","shipping_address_2","shipping_zone","payment_area",'telephone','firstname','google_map_link','payment_zone_id')->where("order_id",$order_id)->first();
            $areas = DFAreaModel::select('name')->where('zone_id', $data->payment_zone_id)->where('status', 1)->pluck('name');
          }
          $data->areas = $areas->toArray();
          $data->store = $store;
          if( $data  ){
            $address_two_arr = explode(',-',$data->shipping_address_2);
            if( is_array($address_two_arr) && count($address_two_arr) > 0 ){
              $data->street_building = $address_two_arr[0];
              $data->villa_flat = @$address_two_arr[1];
            }else{
              $data->street_building = "";
              $data->villa_flat = "";
            }
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
                $order = OmsOrdersModel::select('*')
                ->where('order_id', $order_id)
                ->where('oms_order_status', OmsOrderStatusInterface::OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST)
                // ->where('store',$this->store)
                ->where('picklist_print', 1)
                ->first();
                $order_array = array();
                if($order){
                    $store = $order->store;
                    if( $store == 1 ){
                        $products = OrderedProductModel::select('*')->where('order_id', $order_id)->get();
                        $opencartOrder = OrdersModel::select('total')->where('order_id', $order_id)->first();
                    }else if(  $store == 2 ){
                        $products = DFOrderedProductModel::select('*')->where('order_id', $order_id)->get();
                        $opencartOrder = DFOrdersModel::select('total')->where('order_id', $order_id)->first();
                    }

                    $product_array = array();
                    if($opencartOrder){
                        foreach ($products as $product) {
                            if( $store == 1 ){
                                $opencartProduct = ProductsModel::select('sku')->where('product_id', $product['product_id'])->first();
                            }else if(  $store == 2 ){
                                $opencartProduct = DFProductsModel::select('sku')->where('product_id', $product['product_id'])->first();
                            }
                            $omsProduct = OmsInventoryProductModel::select('*','option_name as color','option_value as size')->where('sku', $opencartProduct->sku)->first();
                            if(!empty($omsProduct)){
                                // die("exist");
                                if( $store == 1 ){
                                    $options = OrderOptionsModel::select('order_option.product_option_id','order_option.product_option_value_id','order_option.name','order_option.value','op.quantity')
                                    ->leftJoin('order_product as op', 'op.order_product_id', '=', 'order_option.order_product_id')
                                    ->where('order_option.order_id', $order['order_id'])->where('order_option.order_product_id', $product->order_product_id)->get()->toArray();
                                }else if($store == 2){
                                    $options = DFOrderOptionsModel::select('order_option.product_option_id','order_option.product_option_value_id','order_option.name','order_option.value','op.quantity')
                                    ->leftJoin('order_product as op', 'op.order_product_id', '=', 'order_option.order_product_id')
                                    ->where('order_option.order_id', $order['order_id'])->where('order_option.order_product_id', $product->order_product_id)->get()->toArray();
                                }
                                // dd($options);
                                $option_array = array();
                                foreach ($options as $option) {
                                    if( $store == 1 ){
                                        $optionData = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                        ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                        ->where('option_description.name', $option['name'])
                                        ->where('ovd.name', $option['value'])
                                        ->first();
                                    }else if($store == 2){
                                        $optionData = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                        ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                        ->where('option_description.name', $option['name'])
                                        ->where('ovd.name', $option['value'])
                                        ->first();
                                    }
                                    // echo "<pre>"; print_r($optionData->toArray());
                                    if( $store == 1 ){
                                        $OmsOptionsData = OmsInventoryOptionValueModel::OmsOptionsFromBa($optionData->option_id,$optionData->option_value_id);
                                    }else if($store == 2){
                                        $OmsOptionsData = OmsInventoryOptionValueModel::OmsOptionsFromDf($optionData->option_id,$optionData->option_value_id);
                                    }
                                    $omsColorId = OmsDetails::colorId($omsProduct['color']);
                                    if($omsProduct['size'] == 0){
                                        $barcode = $omsProduct->product_id;
                                        $barcode .= $omsColorId;
                                        $option_n_v = $option['name']. ' - ' .$option['value'];

                                        $alreadyPicked = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('product_id', $product->product_id)->where('option_id',19)->where('option_value_id', $omsColorId)->where('store', $store)->exists();

                                        $option_array[] = array(
                                            'option'                    =>  $option_n_v,
                                            'option_id'                 =>  $optionData->option_id,
                                            'option_value_id'           =>  $optionData->option_value_id,
                                            'barcode'                   =>  $barcode,
                                            'quantity'                  =>  $alreadyPicked ? 0 : $option['quantity'],
                                            'product_option_value_id'   =>  $option['product_option_value_id'],
                                            'manual_checkable'          =>  $this->forceScanning($product->model)
                                        );
                                    }else{
                                        if( $store == 1 ){
                                            $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                                        }else if($store == 2){
                                            $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                                        }
                                        if($optionData->option_id != $ba_color_option_id){
                                            $barcode = $omsProduct->product_id;
                                            $barcode .= $OmsOptionsData->oms_option_details_id;
                                            $option_n_v = $option['name']. ' - ' .$option['value'];
                                            $alreadyPicked = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('product_id', $product->product_id)->where('option_id', $OmsOptionsData->oms_options_id)->where('option_value_id', $OmsOptionsData->oms_option_details_id)->where('store', $store)->exists();
                                            // dd($alreadyPicked);
                                            // echo "<pre>"; print_r($category);
                                            $option_array[] = array(
                                                'option'                    =>  $option_n_v,
                                                'option_id'                 =>  $optionData->option_id,
                                                'option_value_id'           =>  $optionData->option_value_id,
                                                'barcode'                   =>  $barcode,
                                                'quantity'                  =>  $alreadyPicked ? 0 : $option['quantity'],
                                                'product_option_value_id'   =>  $option['product_option_value_id'],
                                                'manual_checkable'          =>  $this->forceScanning($product->model)
                                            );
                                        }
                                    }

                                }
                                // dd($option_array);
                                $product_array[] = array(
                                'order_product_id'  =>  $product->order_product_id,
                                'product_id'        =>  $product->product_id,
                                'oms_product_id'    =>  $omsProduct->product_id,
                                'image'             =>  $this->getProductImage($product->product_id,$store, 100, 100),
                                'name'              =>  $product->name,
                                'model'             =>  $product->model,
                                'options'           =>  $option_array,
                            );
                            }
                        }
                        $order_array = array(
                            'order_id'          =>  $order_id,
                            'total'             =>  $opencartOrder->total,
                            'status'            =>  $order->status,
                            'date'              =>  $order->created_at,
                            'store'             =>  $store,
                            'products'          =>  $product_array,
                        );
                    }
                }
            }
            return view(self::VIEW_DIR.'.pack_order_search', ["order" => $order_array]);
        }

        public function updatePackOrder(){
            // die("testing update_pack_order func");
            $old_input = RequestFacad::all();
            if(count($old_input) > 0 && $old_input['submit'] == 'update_picked'){
                $order_id = $old_input['order_id'];
                $store    = $old_input['store'];
                $exists = OmsOrdersModel::select('*')
                ->where('order_id', $order_id)
                ->where('oms_order_status', OmsOrderStatusInterface::OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST)
                ->where('store',$store)
                ->where('picklist_print', 1)
                ->exists();
                // echo "<pre>"; print_r(mixed:value, bool:return=false); die;
                // dd(Input::get('packed'));
                if($exists){
                    foreach ($old_input['packed'] as $product_id => $values) {
                        if( $store == 1 ){
                            $opencartProduct = ProductsModel::select('sku')->where('product_id', $product_id)->first();
                        }else if( $store == 2 ){
                            $opencartProduct = DFProductsModel::select('sku')->where('product_id', $product_id)->first();
                        }
                        $omsProduct = OmsInventoryProductModel::select('product_id','option_name as color','option_value as size')->where('sku', $opencartProduct->sku)->first();
                        // dd($values);
                        if(!empty($omsProduct->size)){
                            $total_quantity = 0;
                            foreach ($values as $option_value_id => $packed) {
                                $total_quantity = $total_quantity + count($packed);
                                if( $store == 1 ){
                                    $option_id = ProductOptionValueModel::select('option_id')->where('option_value_id', $option_value_id)->first();
                                    //get oms option details
                                    $omsOptionData = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_id->option_id,$option_value_id);
                                }else if( $store == 2 ){
                                    $option_id = DFProductOptionValueModel::select('option_id')->where('option_value_id', $option_value_id)->first();
                                    //get oms option details
                                    $omsOptionData = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_id->option_id,$option_value_id);
                                }
                                //entry in packed quantity table
                                $OmsInventoryPackedQuantityModel = new OmsInventoryPackedQuantityModel();
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_ORDER_ID} = $order_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_PRODUCT_ID} = $product_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OMS_PRODUCT_ID} = $omsProduct->product_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OPTION_ID} = $omsOptionData->oms_options_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OPTION_VALUE_ID} = $omsOptionData->oms_option_details_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_QUANTITY} = count($packed);
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_STORE} = $store;
                                $OmsInventoryPackedQuantityModel->save();

                                $decrement_query = 'IF (onhold_quantity-'.count($packed) . ' <= 0, 0, onhold_quantity-'.count($packed) . ')';
                                OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->where('option_id', $omsOptionData->oms_options_id)->where('option_value_id', $omsOptionData->oms_option_details_id)->update(array('onhold_quantity' => DB::Raw($decrement_query), 'pack_quantity' => DB::Raw('pack_quantity+'.count($packed)) ));
                            }
                        }else{
                            foreach ($values as $option_value_id => $packed) {
                                if( $store == 1 ){
                                    $option_id = ProductOptionValueModel::select('option_id')->where('option_value_id', $option_value_id)->first();
                                    //get oms option details
                                    $omsOptionData = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_id->option_id,$option_value_id);
                                }else if( $store == 2 ){
                                    $option_id = DFProductOptionValueModel::select('option_id')->where('option_value_id', $option_value_id)->first();
                                    //get oms option details
                                    $omsOptionData = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_id->option_id,$option_value_id);
                                }
                                //entry in packed quantity table
                                $OmsInventoryPackedQuantityModel = new OmsInventoryPackedQuantityModel();
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_ORDER_ID} = Input::get('order_id');
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_PRODUCT_ID} = $product_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OMS_PRODUCT_ID} = $omsProduct->product_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OPTION_ID} = $omsOptionData->oms_options_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_OPTION_VALUE_ID} = $omsOptionData->oms_option_details_id;
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_QUANTITY} = count($packed);
                                $OmsInventoryPackedQuantityModel->{OmsInventoryPackedQuantityModel::FIELD_STORE} =  $store;
                                $OmsInventoryPackedQuantityModel->save();

                                $decrement_query = 'IF (onhold_quantity-'.count($packed) . ' <= 0, 0, onhold_quantity-'.count($packed) . ')';
                                OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->where('option_id', $omsOptionData->oms_options_id)->where('option_value_id', $omsOptionData->oms_option_details_id)->update(array('onhold_quantity' => DB::Raw($decrement_query), 'pack_quantity' => DB::Raw('pack_quantity+'.count($packed)) ));
                            }
                        }
                    }

                        //UPDATE OMS ORDER STATUS
                    $omsOrder = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $order_id)->where('store', $store)->first();
                    $omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_PACKED;
                    $omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
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
      $ordersStatus = OrderStatusModel::all();
      $shippingProviders = ShippingProvidersModel::where('is_active', 1)->get();
      return view(self::VIEW_DIR . ".generate_awb", ["orderStatus" => $ordersStatus, "shippingProviders" => $shippingProviders]);
    }
    public function awb(){
        $orderIds = Session::get('orderIdsForAWBGenerate')[0];
        Session::put('orderIdsForAWBGenerate', array());
        $orders = collect();
        if( is_array($orderIds) && count($orderIds) > 0 ){
            foreach( $orderIds as $order_id ){
                $data = OmsOrdersModel::where("order_id",$order_id )->first();
                if( $data->store == 1 ){
                    $order = OrdersModel::with(['status', 'orderd_products'])
                    ->where("order_id", $order_id)->first();
                }else if( $data->store == 2 ){
                    $order = DFOrdersModel::with(['status', 'orderd_products'])
                      ->where("order_id", $order_id)->first();
                }
                $orders->push($order);
            }
        }
        // dd($orderIds);
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
            $ordersStatus = OrderStatusModel::whereIn('order_status_id',[3,15,25])->get();
            if( !RequestFacad::all() ){
                RequestFacad::merge(['order_status_id' => 3]);
            }
        }else{
            $ordersStatus = OrderStatusModel::get();
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
        $ordersStatus      = OrderStatusModel::all();

        return view(self::VIEW_DIR . ".airway_bill_generated_orders",compact('omsOrders','shippingProviders','ordersStatus'));
    }
    public function shipOrdersToCourier(){
        // $tab_links = $this->tab_links();
        return view(self::VIEW_DIR . ".ship_orders_to_courier");
    }
    public function shipOrders(){
        $orderIds = RequestFacad::get('generate-awb-chbx'); // ordersID array
        $stores   = RequestFacad::get('store');
        // dd($stores);
        if ($orderIds){
            try{
                foreach ($orderIds as $orderId)
                {
                    $omsOrder = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $orderId)->where('store',$stores[$orderId])->first();
                    if( $omsOrder->store == 1 ){
                        $openCartOrder = OrdersModel::findOrFail($orderId);
                    }else if( $omsOrder->store == 2 ){
                        $openCartOrder = DFOrdersModel::findOrFail($orderId);
                    }
                    // echo $omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS}."==".OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED; die;
                    if (($omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} == OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED)){
                        if( $omsOrder->reship != 1 ){
                          $this->shippedInventoryQuantity($omsOrder);
                        }
                        //UPDATE OMS ORDER STATUS
                        $omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_SHIPPED;
                        $omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                        $omsOrder->save();

                        $openCartOrder->{OrdersModel::FIELD_DATE_MODIFIED} = \Carbon\Carbon::now();
                        $openCartOrder->{OrdersModel::FIELD_ORDER_STATUS_ID} = OrdersModel::OPEN_CART_STATUS_SHIPPED;
                        $openCartOrder->save(); // update the order status

                        /*if($openCartOrder->shipping_country_id == 99){
                            $header = 'RESIND';
                        }else{
                            $header = 'Reson8';
                        }*/
                        $awb_number = AirwayBillTrackingModel::select('airway_bill_number')->where('order_id', $orderId)->where('store',$omsOrder->store)->first();

                        // $sms = new Reson8SmsServiceProvider();
                        // $message = "Dear " . $openCartOrder->firstname . "\n";
                        // $message .= "Your order #" . $openCartOrder->order_id . " shipped out to courier Shipping number " . $awb_number->airway_bill_number . ". if have any Question contact us on 971-506465814.";
                        // $sms->SendSMS(urlencode($message), $openCartOrder->telephone, 'BusinsArcad');

                        //UPDATE OPENCART ORDER HISTORY

                        if( $omsOrder->store == 1 ){
                            $orderHistory = new OrderHistory();
                        }else if( $omsOrder->store == 2 ){
                            $orderHistory = new DFOrderHistory();
                        }
                        $orderHistory->{OrderHistory::FIELD_COMMENT} = "Order shipped from OMS";
                        $orderHistory->{OrderHistory::FIELD_ORDER_ID} = $orderId;
                        $orderHistory->{OrderHistory::FIELD_ORDER_STATUS_ID} = OrdersModel::OPEN_CART_STATUS_SHIPPED;
                        $orderHistory->{OrderHistory::FIELD_DATE_ADDED} = \Carbon\Carbon::now();
                        $orderHistory->{OrderHistory::FIELD_NOTIFY} = OrderHistory::NOTIFY_CUSTOMER;
                        $orderHistory->save();
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
            return redirect('/orders/ship-orders');
        }
    }
    public function shippedInventoryQuantity($omsOrder){
        $order_id = $omsOrder->order_id;
        if( $omsOrder->store == 1 ){
            $orderd_products = OrdersModel::with(['orderd_products'])->where(OrdersModel::FIELD_ORDER_ID, $order_id)->first();
        }else if( $omsOrder->store == 2 ){
            $orderd_products = DFOrdersModel::with(['orderd_products'])->where(OrdersModel::FIELD_ORDER_ID, $order_id)->first();
        }
        // echo "<pre>"; print_r($orderd_products->toArray()); die;
        if($orderd_products->orderd_products){
            foreach ($orderd_products->orderd_products as $key => $product) {
                if( $omsOrder->store == 1 ){
                    $opencart_sku = ProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }else if( $omsOrder->store == 2 ){
                    $opencart_sku = DFProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }
                $exists = OmsInventoryProductModel::select("*","option_name AS color","option_value AS size")->where('sku', $opencart_sku->sku)->first();
                // dd($exists->toArray());
                if($exists){
                    $product_id = $exists->product_id;
                    if(!empty($exists->size) && $exists->size > 0){
                        $total_quantity = 0;
                        foreach ($product->order_options as $key => $option) {
                            if( $omsOrder->store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }else if( $omsOrder->store == 2 ){
                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }

                            // dd($product->order_options->toArray());
                            if( $omsOrder->store == 1 ){
                                $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                            }else if( $omsOrder->store == 2 ){
                                $ba_color_option_id = OmsInventoryOptionModel::dfColorOptionId();
                            }
                            if($option_data && $option_data->option_id != $ba_color_option_id){
                                if( $omsOrder->store == 1 ){
                                    $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
                                }else if( $omsOrder->store == 2 ){
                                    $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_data->option_id,$option_data->option_value_id);
                                }
                                // dd($oms_option_det->toArray());
                                $total_quantity = $total_quantity + $product->quantity;
                                $decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'shipped_quantity' => DB::raw('shipped_quantity+' . $product->quantity) ));
                                $OmsInventoryShippedQuantityModel = new OmsInventoryShippedQuantityModel();
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_PRODUCT_ID} = $product_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_ORDER_ID} = $order_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_OPTION_ID} = $oms_option_det->oms_options_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_OPTION_VALUE_ID} = $oms_option_det->oms_option_details_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_QUANTITY} = $product->quantity;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_STORE} = $omsOrder->store;
                                $OmsInventoryShippedQuantityModel->save();
                            }
                        }
                    }else{
                        // echo $exists->size."<br>";
                        // echo $product->quantity;
                        // dd($product->order_options->toArray());
                        foreach ($product->order_options as $key => $option) {
                            if( $omsOrder->store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                ->where('option_description.name', $option->name)
                                ->where('ovd.name', $option->value)
                                ->first();
                            }else if( $omsOrder->store == 2 ){
                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }

                            if($option_data){
                                if( $omsOrder->store == 1 ){
                                    $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
                                }else if( $omsOrder->store == 2 ){
                                    $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_data->option_id,$option_data->option_value_id);
                                }
                                // dd($oms_option_det->toArray());
                                $decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'shipped_quantity' => DB::raw('shipped_quantity+' . $product->quantity) ));

                                $OmsInventoryShippedQuantityModel = new OmsInventoryShippedQuantityModel();
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_PRODUCT_ID} = $product_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_ORDER_ID} = $order_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_OPTION_ID} = $oms_option_det->oms_options_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_OPTION_VALUE_ID} = $oms_option_det->oms_option_details_id;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_QUANTITY} = $product->quantity;
                                $OmsInventoryShippedQuantityModel->{OmsInventoryShippedQuantityModel::FIELD_STORE} = $omsOrder->store;
                                $OmsInventoryShippedQuantityModel->save();
                            }
                        }
                    }
                }
            }
        }
    }
}
