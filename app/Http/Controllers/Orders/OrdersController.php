<?php

namespace App\Http\Controllers\Orders;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\DressFairOpenCart\Orders\OrderStatusModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\OpenCart\Orders\OrderedProductModel;
use App\Models\OpenCart\Orders\OrdersModel;
use DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use App\Platform\Helpers\ToolImage;
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
        $old_input = Request::all();
        // $data = DB::table("oms_orders AS ord")
        // ->rightjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
        //   $join->on("baord.order_id","=","ord.order_id");
        //   $join->on("ord.store","=",DB::raw("1"));
        // })
        // ->rightjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
        //     $join->on("dford.order_id","=","ord.order_id");
        //     $join->on("ord.store","=",DB::raw("2"));
        //   })
        // ->orderBy('baord.date_added','DESC')->limit(100)->get();
        // dd($data->toArray());
        $data = DB::table("oms_orders AS ord")
              ->rightjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
                $join->on("baord.order_id","=","ord.order_id");
                $join->on("ord.store","=",DB::raw("1"));
              })
              ->rightjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
                $join->on("dford.order_id","=","ord.order_id");
                $join->on("ord.store","=",DB::raw("2"));
              })
              ->select(DB::raw("baord.order_id AS baordidd,dford.order_id AS dfordidd,
                        (CASE WHEN baord.order_id IS NOT NULL THEN baord.order_id  ELSE dford.order_id END) AS order_id
                "))
            // ->join("airwaybill_tracking AS awbt",function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
            //  ->leftjoin(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
            //   $join->on('awbt.order_id','=','ord.order_id');
            //   $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
            //  })
            //  ->join("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
            //  ->select(DB::raw("ord.order_id,ord.oms_order_status,ord.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
            //       (CASE WHEN ord.store = 1 THEN baord.total WHEN ord.store = 2 THEN dford.total ELSE 0 END) AS amount,
            //       (CASE WHEN ord.store = 1 THEN baord.payment_code WHEN ord.store = 2 THEN dford.payment_code ELSE 0 END) AS payment_code,
            //       (CASE WHEN ord.store = 1 THEN baord.shipping_address_1 WHEN ord.store = 2 THEN dford.shipping_address_1 ELSE 0 END) AS shipping_address_1,
            //       (CASE WHEN ord.store = 1 THEN baord.shipping_address_2 WHEN ord.store = 2 THEN dford.shipping_address_2 ELSE 0 END) AS shipping_address_2,
            //       (CASE WHEN ord.store = 1 THEN baord.shipping_area WHEN ord.store = 2 THEN dford.shipping_area ELSE 0 END) AS shipping_area,
            //       (CASE WHEN ord.store = 1 THEN baord.shipping_zone WHEN ord.store = 2 THEN dford.shipping_zone ELSE 0 END) AS shipping_zone,
            //       (CASE WHEN ord.store = 1 THEN baord.payment_address_1 WHEN ord.store = 2 THEN dford.payment_address_1 ELSE 0 END) AS payment_address_1,
            //       (CASE WHEN ord.store = 1 THEN baord.payment_address_2 WHEN ord.store = 2 THEN dford.payment_address_2 ELSE 0 END) AS payment_address_2,
            //       (CASE WHEN ord.store = 1 THEN baord.payment_area WHEN ord.store = 2 THEN dford.payment_area ELSE 0 END) AS payment_area,
            //       (CASE WHEN ord.store = 1 THEN baord.shipping_city WHEN ord.store = 2 THEN dford.shipping_city ELSE 0 END) AS shipping_city,
            //       (CASE WHEN ord.store = 1 THEN baord.firstname WHEN ord.store = 2 THEN dford.firstname ELSE 0 END) AS firstname,
            //       (CASE WHEN ord.store = 1 THEN baord.lastname WHEN ord.store = 2 THEN dford.lastname ELSE 0 END) AS lastname,
            //       (CASE WHEN ord.store = 1 THEN baord.telephone WHEN ord.store = 2 THEN dford.telephone ELSE 0 END) AS telephone,
            //       (CASE WHEN ord.store = 1 THEN baord.email WHEN ord.store = 2 THEN dford.email ELSE 0 END) AS email,
            //       (CASE WHEN ord.store = 1 THEN baord.total WHEN ord.store = 2 THEN dford.total ELSE 0 END) AS total,
            //       (CASE WHEN ord.store = 1 THEN baord.date_modified WHEN ord.store = 2 THEN dford.date_modified ELSE 0 END) AS date_modified,
            //       (CASE WHEN ord.store = 1 THEN baord.date_added WHEN ord.store = 2 THEN dford.date_added ELSE 0 END) AS date_added
            //     "))
            ->orderByRaw("CASE WHEN baord.order_id IS NOT NULL THEN baord.date_added ELSE dford.date_added END DESC")
            ->paginate(100);
            $data = $this->getOrdersWithImage($data);
            dd($data->toArray());
        ///
        $searchFormAction = URL::to('orders');
        $orderStatus = OrderStatusModel::all();
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input'));
    }
    public function online(){
        $old_input = Request::all();

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

        // foreach ($orders as $order_key => &$orders_value) {
        //     if(isset($orders_value->orderd_products) && !empty($orders_value->orderd_products)){
        //         foreach ($orders_value->orderd_products as $orderd_product_key => &$orderd_products_value) {

        //             // dd($orderd_products_value);
        //             if(isset($orderd_products_value->product_details) && !empty($orderd_products_value->product_details)){
        //                 $ToolImage = new ToolImage();
        //                 if(file_exists($this->website_image_source_path . $orderd_products_value->product_details->image)){
        //                     $orderd_products_value->product_details->image = $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $orderd_products_value->product_details->image, 100, 100);
        //                 }else if(strpos($orderd_products_value->product_details->image, "cache/catalog")){
        //                     // dd("find");
        //                     continue;
        //                 }else{
        //                     // echo "Not <br>". $this->website_image_source_url;
        //                     $orderd_products_value->product_details->image = $this->website_image_source_url . 'placeholder.png';
        //                 }
        //             }
        //         }
        //     }
        // }
        return $orders;
    }
    protected function orderedProducts($order){
        $data = OrderedProductModel::with(['product_details'=>function($query){
            $query->select('product_id','image');
          }])->where('order_id',$order->order_id)->get();
        return $data;
    }
}
