<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\OmsExchangeOrderAttachment;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsExchangeProductModel;
use App\Models\Oms\OmsExchangeReturnProductModel;
use App\Models\Oms\OmsExchangeTotalModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\OmsOrderStatusModel;
use App\Models\Oms\OmsPlaceExchangeModel;
use App\Models\Oms\OmsUserModel;
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
        $old_input = RequestFacad::all();

        $data = OmsPlaceExchangeModel::select('oms_place_exchanges.*')
                ->with(['exchangeProducts.product','omsExchange','omsStore'])
                ->leftjoin("oms_exchange_orders",function($join){
                    $join->on('oms_exchange_orders.order_id', '=', 'oms_place_exchanges.order_id');
                    $join->on('oms_exchange_orders.store', '=', 'oms_place_exchanges.store');
                })
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.store',$old_input['by_store']);
                })
                ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.mobile','LIKE',"%".$old_input['telephone']."%");
                })
                ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.firstname','LIKE',"%".$old_input['customer']."%");
                })
                ->when(@$old_input['email'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.email','LIKE',"%".$old_input['email']."%");
                })
                ->when(@$old_input['total'] != "",function($query) use ($old_input){
                    return $query->where('oms_place_exchanges.total_amount',$old_input['total']);
                })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_exchange_orders.oms_order_status',$old_input['order_status_id']);
                });
            $data = $data->orderByRaw("(CASE WHEN oms_exchange_orders.order_id > 0 THEN oms_exchange_orders.updated_at ELSE oms_exchange_orders.created_at END) DESC")
                ->paginate(20);
        // dd($data->toArray());
        $searchFormAction = "exchange";
        $orderStatus = OmsOrderStatusModel::all();
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus'));
    }
    public function pickingListAwaiting(){
        $old_input = RequestFacad::all();
        if (isset($old_input['o_id'])){
            return $this->generatePickingList($old_input['o_id']);
        }
        $data = OmsPlaceExchangeModel::with(['exchangeProducts.product','omsExchange','omsStore'])
        ->whereHas('omsExchange',function($q) use($old_input){
            $q->where('oms_order_status',0);
        })
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
        // $data = $data->orderByRaw("omsOrder.updated_at DESC");
        $data = $data->paginate(10);
        // dd($data->toArray());
        $searchFormAction = "exchange";
        $orderStatus = OmsOrderStatusModel::all();
        return view(self::VIEW_DIR.".pick_list_view",compact('data','searchFormAction','orderStatus'));
    }
    protected function generatePickingList($orderIds = []){
        $orders = OmsPlaceExchangeModel::with(['exchangeProducts.product','omsStore'])
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
                $omsUpdateStatus = OmsExchangeOrdersModel::where('order_id', $order->order_id)->where('store',$order->store);
                $print_status = $omsUpdateStatus->update(['picklist_print' => 1]);
            }
        }
        // dd($orders);
        // $orders = $this->getOrdersWithImage($orders);
        // dd($orders);
        return view(self::VIEW_DIR . ".print_pick_list", ["orders" => $orders, "pagination" => '']);
    }
    public function pack(){
        $old_input = RequestFacad::all();
        return view(self::VIEW_DIR.".pack_order",compact('old_input'));
    }
    public function getPack(){
        $old_input = RequestFacad::all();
        if(count($old_input) > 0){
            $order_id = $old_input['order_id'];

            $order = OmsPlaceExchangeModel::with(['exchangeProducts.product','exchangeProducts.productOption','omsStore'])
            ->whereHas('omsExchange',function($query){
                $query->where('oms_order_status', 0)->where('picklist_print', 1);
            })
            ->where('order_id', $order_id)
            ->first();
        }
        return view(self::VIEW_DIR.'.pack_order_search',compact('order'));
    }
    public function updatePack(){
        // die("testing update_pack_order func");
        $old_input = RequestFacad::all();
        // dd($old_input);
        if(count($old_input) > 0 && $old_input['submit'] == 'update_picked'){
            $order_id = $old_input['order_id'];
            $order_id = str_replace("-1","",$order_id);
            $store    = $old_input['store'];
            // $exists = OmsOrdersModel::select('*')
            // ->where('order_id', $order_id)
            // ->where('oms_order_status', OmsOrderStatusInterface::OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST)
            // ->where('store',$store)
            // ->where('picklist_print', 1)
            // ->exists();

            $order = OmsPlaceExchangeModel::with(['orderProducts.product','orderProducts.productOption','omsStore'])
            ->whereHas('omsExchange',function($query){
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
                    $OmsInventoryPackedQuantityModel->order_id = $order->order_id."-1";
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
                // $awb_response = app(\App\Http\Controllers\Orders\OrdersAjaxController::class)->forwardForShipping();
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
        return view("placeExchange.index",compact('data','store_data','order_id'));
    }
    public function delete(Request $request){
        // dd($request->all());
        $order_id = $request->order_id;
        $store    = $request->oms_store;
        $dash_order_id = $order_id."-1";
        DB::beginTransaction();
        try{
            OmsPlaceExchangeModel::where('order_id',$order_id)->where('store',$store)->delete();
            OmsExchangeOrdersModel::where('order_id',$order_id)->where('store',$store)->delete();
            OmsExchangeProductModel::where('order_id',$order_id)->where('store_id',$store)->delete();
            OmsExchangeTotalModel::where('order_id',$order_id)->where('store_id',$store)->delete();
            OmsExchangeOrderAttachment::where('order_id',$order_id)->where('store_id',$store)->delete();
            OmsExchangeReturnProductModel::where('order_id',$order_id)->where('store_id',$store)->delete();
            //with dashed order id
            OmsInventoryOnholdQuantityModel::where('order_id',$dash_order_id)->where('store',$store)->delete();
            OmsInventoryPackedQuantityModel::where('order_id',$dash_order_id)->where('store',$store)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            // return response()->json(['status'=>false,"data"=>'','msg'=>$e->getMessage()]);
            echo $e->getMessage();
        }
        redirect('exchange');
     }
}
