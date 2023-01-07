<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsOrderProductModel;
use DB;
use Illuminate\Support\Facades\URL;
use App\Models\Oms\storeModel;
use Illuminate\Support\Facades\Request AS RequestFacad;
use App\Platform\Helpers\ToolImage;
use Illuminate\Support\Facades\Storage;
use Session;
use Illuminate\Support\Collection;

class ExchangeOrdersController extends Controller
{
    const VIEW_DIR = 'exchange';
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
        $data = DB::table("oms_exchange_orders AS eord")
        ->rightjoin(DB::raw("(SELECT * FROM
                    ( SELECT exchange_order_id,order_id,1 AS oms_store,order_status_id,firstname,lastname,telephone,email,total,payment_code,shipping_address_1,shipping_address_2,shipping_area,shipping_zone,payment_address_1,payment_address_2,payment_area,shipping_city,date_added,date_modified FROM $this->DB_BAOPENCART_DATABASE.oc_exchange_order
                    UNION
                    SELECT exchange_order_id,order_id,2 AS oms_store,order_status_id,firstname,lastname,telephone,email,total,payment_code,shipping_address_1,shipping_address_2,shipping_area,shipping_zone,payment_address_1,payment_address_2,payment_area,shipping_city,date_added,date_modified FROM $this->DB_DFOPENCART_DATABASE.oc_exchange_order
                    )
                    AS exchanges) AS exchanges"),function($join){
                    $join->on('exchanges.order_id','=','eord.order_id');
                    $join->on('exchanges.oms_store','=','eord.store');
        })
        ->select(DB::raw("exchanges.*,eord.oms_order_status,0 AS payment_status"))
        ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
            return $query->where("order_id",$old_input['order_id']);
        })
        ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
            return $query->where("order_status_id",$old_input['order_status_id']);
        })->orderByRaw("date_modified DESC")
        ->paginate(20);
        // dd($data->toArray());
        //
        $data = $this->getOrdersWithImage($data);
        // dd($data->toArray());
        $searchFormAction = "exchange";
        $orderStatus = ExchangeOrderStatusModel::all();
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus'));
    }
    public function pickingListAwaiting(){
        $old_input = RequestFacad::all();
        $data = DB::table("oms_exchange_orders AS eord")
        ->rightjoin(DB::raw("(SELECT * FROM
                    ( SELECT exchange_order_id,order_id,1 AS oms_store,order_status_id,firstname,lastname,telephone,email,total,payment_code,shipping_address_1,shipping_address_2,shipping_area,shipping_zone,payment_address_1,payment_address_2,payment_area,shipping_city,date_added,date_modified FROM $this->DB_BAOPENCART_DATABASE.oc_exchange_order
                    UNION
                    SELECT exchange_order_id,order_id,2 AS oms_store,order_status_id,firstname,lastname,telephone,email,total,payment_code,shipping_address_1,shipping_address_2,shipping_area,shipping_zone,payment_address_1,payment_address_2,payment_area,shipping_city,date_added,date_modified FROM $this->DB_DFOPENCART_DATABASE.oc_exchange_order
                    )
                    AS exchanges) AS exchanges"),function($join){
                    $join->on('exchanges.order_id','=','eord.order_id');
                    $join->on('exchanges.oms_store','=','eord.store');
        })
        ->select(DB::raw("exchanges.*,eord.oms_order_status,0 AS payment_status"))
        ->where('eord.oms_order_status',0)
        ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
            return $query->where("order_id",$old_input['order_id']);
        })
        ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
            return $query->where("order_status_id",$old_input['order_status_id']);
        })->orderByRaw("date_modified DESC")
        ->paginate(20);
        // dd($data->toArray());
        //
        $data = $this->getOrdersWithImage($data);
        // dd($data->toArray());
        $searchFormAction = "exchange";
        $orderStatus = ExchangeOrderStatusModel::all();
        return view(self::VIEW_DIR.".pick_list_view",compact('data','searchFormAction','orderStatus'));
    }
    protected function getOrdersWithImage($orders){
        foreach ($orders as $key => $order) {
            $ordered_products = $this->orderedProducts($order);
            // dd($ordered_products);
            foreach ($ordered_products as $orderd_product_key => $orderd_products_value) {
                if(isset($orderd_products_value->product_details) && !empty($orderd_products_value->product_details)){
                    $ToolImage = new ToolImage();
                    if(file_exists($this->website_image_source_path . $orderd_products_value->product_details->image)){
                        $orderd_products_value->product_details->image = $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $orderd_products_value->product_details->image, 100, 100);
                    }else if(strpos($orderd_products_value->product_details->image, "cache/catalog")){
                        continue;
                    }else{
                        $orderd_products_value->product_details->image = $this->website_image_source_url . 'placeholder.png';
                    }
                }
            }
            $order->orderd_products = $ordered_products;
        }
        return $orders;
    }
    protected function orderedProducts($order){
        if( $order->oms_store == 1 ){
            $data = ExchangeOrderProductModel::with(['product_details'=>function($query){
                $query->select('product_id','image');
            }])->where('exchange_order_id',$order->exchange_order_id)->get();
        }else if( $order->oms_store == 2 ){
            $data = DFExchangeOrderProductModel::with(['product_details'=>function($query){
                $query->select('product_id','image');
            }])->where('exchange_order_id',$order->exchange_order_id)->get();
        }
        return $data;
    }
    public function createExchange(Request $request){
        $order_id            = $request->order_id_for_exchange;
        $store_id            = $request->store_id_for_exchange;
        $ordered_product_ids = $request->ordered_product_ids;
        // dd($ordered_product_ids);
        $data = [];
        if( is_array( $ordered_product_ids ) && count($ordered_product_ids) > 0 ){
            foreach($ordered_product_ids as $product_option_id => $product_id){
                $product = OmsOrderProductModel::with(['product'])->where("product_id",$product_id)->where("product_option_id",$product_option_id)->where("order_id",$order_id)->first();
                if( $product ){
                    $data[] = $product->toArray();
                }
            }
        }
        $store_data = storeModel::where('id',$store_id)->first();
        return view("placeExchange.index",compact('data','store_data'));
    }
    public function placeExchange(){
        // dd($request->all());
        die("placeExchange");
    }
}
