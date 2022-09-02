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
use App\Models\DressFairOpenCart\Orders\OrderedProductModel AS DFOrderedProductModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\Oms\OmsUserModel;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request AS RequestFacad;
use App\Platform\Helpers\ToolImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class OrdersController extends Controller
{
	const VIEW_DIR = 'orders';
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    private $website_image_source_url =  '';
    private $website_image_source_path =  '';
    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =   isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/' : "";
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
                ->where('oms_order_status',0)
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
}
