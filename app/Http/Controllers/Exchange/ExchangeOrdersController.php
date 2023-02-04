<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrdersModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryReturnQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsActivityModel;
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
use App\Models\Oms\ShippingProvidersModel;
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
                ->with(['exchangeProducts.product','omsExchange.generatedCourier','omsStore','omsExchange.lastAwb'])
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
            $data = $data->orderByRaw("(CASE WHEN oms_exchange_orders.order_id > 0 THEN oms_exchange_orders.updated_at ELSE oms_place_exchanges.created_at END) DESC")
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

            $order = OmsPlaceExchangeModel::with(['exchangeProducts.product','exchangeProducts.productOption','omsStore'])
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
                foreach ($order->exchangeProducts as $key => $orderProduct) {
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

                $omsOrder = OmsExchangeOrdersModel::where('order_id', $order_id)->where('store', $store)->first();
                $omsOrder->oms_order_status = 1; //1 for packed exchange
                $omsOrder->updated_at = \Carbon\Carbon::now();
                $omsOrder->save();
                OmsActivityLogModel::newLog($order_id,13,$store); //13 is for pack exchange
                //create airwaybill
                // $awb_response = app(\App\Http\Controllers\Orders\ExchangeOrdersAjaxController::class)->forwardForShipping();
                Session::flash('message', 'Exchange product packed successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('/exchange/pack')->with('packed_order_id',$order_id);
            }else{
                Session::flash('message', "Order product packed in 'Picklist' status only.");
                Session::flash('alert-class', 'alert-warning');
                return redirect('/exchange/pack');
            }
        }else{
            Session::flash('message', 'Something went wrong, please try again!');
            Session::flash('alert-class', 'alert-warning');
            return redirect('/exchange/pack');
        }
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

        $omsOrders = OmsExchangeOrdersModel::with(['airway_bills','shipping_provider'])
        ->orderBy(OmsExchangeOrdersModel::UPDATED_AT, 'DESC')
        ->groupBy('oms_exchange_orders.order_id');
        if(RequestFacad::get('order_id')){
            $omsOrders = $omsOrders->where('oms_orders.order_id', RequestFacad::get('order_id'));
        }
        if(RequestFacad::get('order_status_id')){
            $omsOrders = $omsOrders->where('oc_order.order_status_id', RequestFacad::get('order_status_id'));
        }
        if (RequestFacad::get('shipping_provider_id')){
            $omsOrders = $omsOrders->where('oms_orders.'.OmsOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER, RequestFacad::get('shipping_provider_id'));
        }
        if (RequestFacad::get('date_from') && RequestFacad::get('date_to')){
            $date_from = Carbon::createFromFormat("Y-m-d", RequestFacad::get('date_from'))->toDateString();
            $date_to = Carbon::createFromFormat("Y-m-d",RequestFacad::get('date_to'))->toDateString();

            $omsOrders = $omsOrders->whereDate('awb.'.ExchangeAirwayBillTrackingModel::CREATED_AT, '>=', $date_from)
            ->whereDate('awb.'.ExchangeAirwayBillTrackingModel::CREATED_AT, '<=', $date_to);
        }
        if (RequestFacad::get('awb_number')){
            $omsOrders = $omsOrders->where('awb.'.ExchangeAirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER, RequestFacad::get('awb_number'));
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
    public function generateAwb(){
        $ordersStatus = OmsOrderStatusModel::all();
        $shippingProviders = ShippingProvidersModel::where('is_active', 1)->get();
        return view(self::VIEW_DIR . ".generate_awb", ["orderStatus" => $ordersStatus, "shippingProviders" => $shippingProviders]);
    }
    public function awb(){
        $orderIds = Session::get('orderIdsForAWBGenerate')[0];
        Session::put('orderIdsForAWBGenerate', array());
        $orders = collect();
        $orders = OmsPlaceExchangeModel::with(['exchangeProducts.product'])->whereIn('order_id',$orderIds)->get();
        //// Need enhancement
        $order_tracking = ExchangeAirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
        // dd($order_tracking->toArray());
        $order_tracking_ids = $order_tracking->pluck(ExchangeAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
        $shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

        return view(self::VIEW_DIR . ".awb", ['orders' => $orders, 'order_tracking' => $order_tracking, 'shipping_providers' => $shipping_providers]);
    }
    public function shipExchangeToCourier(){
        // $tab_links = $this->tab_links();
        return view(self::VIEW_DIR . ".ship_exchange_to_courier");
    }
    public function shipExchange(){
        $orderIds = RequestFacad::get('generate-awb-chbx'); // ordersID array
        $stores   = RequestFacad::get('store');
        if ($orderIds){
            try{
                foreach ($orderIds as $orderId)
                {
                    $orderId = str_replace("-1","",$orderId);

                    $omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->where('store',$stores[$orderId])->first();

                    if ( $omsOrder->oms_order_status == 2 ){
                        $this->shippedInventoryQuantity($omsOrder);
                        //UPDATE OMS ORDER STATUS
                        $omsOrder->oms_order_status = 3; // 3 for shipped status
                        $omsOrder->updated_at = \Carbon\Carbon::now();
                        $omsOrder->save();

                        // $awb_number = AirwayBillTrackingModel::select('airway_bill_number')->where('order_id', $orderId)->where('store',$omsOrder->store)->first();

					    OmsActivityLogModel::newLog($orderId,16, $omsOrder->store); //16 is for Ship exchange

                        // if($openCartOrder->reseller_id > 0) {
                        //     $this->manageResellerAccount($orderId ,$openCartOrder->reseller_id, 1);
                        // }
                    }else{
                        throw new \Exception("Order can only be shipped in 'AWB Generated' status.");
                    }
                }

                Session::flash('message', "Exchange Shipped successfully.");
                Session::flash('alert-class', 'alert-success');
                return redirect('/exchange/ship');
            }
            catch (\Exception $e){
                Session::flash('message', $e->getMessage());
                Session::flash('alert-class', 'alert-danger');
                return redirect('/exchange/ship');
            }
        }else{
            Session::flash('message', 'Please select order to ship.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('/exchange/ship');
        }
    }
    public function shippedInventoryQuantity($omsOrder){
        $order_id = $omsOrder->order_id;
        $store_id = $omsOrder->store;
        $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryShippedQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( !$check_exist ){
                    $new_shipped = new OmsInventoryShippedQuantityModel();
                    $new_shipped->order_id        = $order_product->order_id."-1";
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
    public function return(){
        $old_input = RequestFacad::all();
        return view(self::VIEW_DIR.".return_exchange",compact('old_input'));
    }
    public function getReturn(){
        if(count(RequestFacad::all()) > 0){
            $order_id = RequestFacad::get('order_id');
            $order_id = str_replace('-1','',$order_id);
            $order = OmsExchangeOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 3) //3 for shipped order
            // ->where('store',$this->store)
            ->first();

            if( $order && ( $order->ready_for_return == 1 OR $order->ready_for_return === 0 ) ){
              echo "<h2 style='color:red'>Exchange cannot be return, contact operation team.</h2>";
              return;
            }
            if($order){
                $order->order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id", $order->store)->get();
            }else{
                echo "<h2 style='color:red'>Exchange cannot be return in this status OR order not found.</h2>";
                return;
            }
        }
        return view(self::VIEW_DIR.'.return_exchange_search', ["order" => $order]);
    }
    public function updateReturn(){
        // echo "<pre>"; print_r($_SERVER); die;
        // dd(RequestFacad::all());
        if(count(RequestFacad::all()) > 0 && RequestFacad::get('submit') == 'update_returned'){
            $order_id  = RequestFacad::get('order_id');
            $store_id = RequestFacad::get('oms_store');
            $exists    = OmsExchangeOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 3)  //3 for shipped status
            ->where('store',$store_id)
            ->exists();

            if($exists){
                $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
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
                            $new_return = new OmsInventoryReturnQuantityModel();
                            $new_return->order_id        = $order_product->order_id."-1";
                            $new_return->product_id      = $product_id;
                            $new_return->oms_product_id  = $product_id;
                            $new_return->option_id       = $option_id;
                            $new_return->option_value_id = $option_value_id;
                            $new_return->quantity        = $order_quantity;
                            $new_return->store           = $order_product->store_id;
                            if( $new_return->save() ){
                                $decrement_query     = 'IF (shipped_quantity-' . $order_quantity . ' <= 0, 0, shipped_quantity-' . $order_quantity . ')';
                                $decrement_available = 'IF (available_quantity-' . $order_quantity . ' <= 0, 0, available_quantity-' . $order_quantity . ')';
                                $return_query = OmsInventoryProductOptionModel::where(["product_id"=>$product_id,"product_option_id"=>$order_product->product_option_id])
                                ->update(['shipped_quantity'=>DB::raw($decrement_query),'return_quantity'=>DB::raw("return_quantity + $order_quantity"),"available_quantity"=>DB::raw($decrement_available)]);
                                if( $return_query ){
                                    //stock history
                                    $quantity_data .= $order_product->option_value . "-(" .$order_quantity. "), ";
                                    $comment = "This quantity added is returned from the Exchange number #".$order_id."-1 from Customer.<br>Quantity: ". rtrim($quantity_data, ", ");
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
                    OmsExchangeOrdersModel::where("order_id",$order_id)->update(['oms_order_status'=>6]);  //6 is for return order.
                }
                OmsActivityLogModel::newLog($order_id,18, $store_id); //18 is for return exchange order
                Session::flash('message', 'Exchange returned successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('/exchange/return');
            }else{
                Session::flash('message', "Exchange product returned in 'Shipped' status only OR order not found.");
                Session::flash('alert-class', 'alert-danger');
                return redirect('/exchange/return');
            }
        }
    }
    public function printLabel($order_id){
        $print_label = '';
        $order = OmsExchangeOrdersModel::select('*')->where('order_id', $order_id)->first();
        if($order){
            $products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id", $order->store)->get();
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

        return view('orders.print_label', ["labels" => $label_array,"label_type" => $print_label]);
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
    public function deliverSingleOrder($order_id,$store_id){
        $order_id = str_replace("-1","",$order_id);
        try{

            $omsOrder = OmsExchangeOrdersModel::where("order_id", $order_id)->where('store',$store_id)->first();

            if( $omsOrder->oms_order_status == 3 ){
                //delivered quantity in oms_inventory_delivered_quantity and oms_inventory_product_option tables
                $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
                // dd($order_products->toArray());
                if( $order_products ){
                    $order_quantity = 0;
                    foreach( $order_products as $key => $order_product ){
                        $order_quantity = $order_product->quantity;
                        $product_id      = $order_product->product_id;
                        $option_id       = $order_product->productOption->option_id;
                        $option_value_id = $order_product->productOption->option_value_id;
                        $check_exist = OmsInventoryDeliveredQuantityModel::where("order_id",$order_id."-1")->where('product_id',$product_id)
                        ->where('option_id',$option_id)->where('option_value_id',$option_value_id)->where('store',$order_product->store_id)->first();
                        if( !$check_exist ){
                            $new_deliver = new OmsInventoryDeliveredQuantityModel();
                            $new_deliver->order_id        = $order_product->order_id."-1";
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
                $omsOrder->updated_at = \Carbon\Carbon::now();
                $qry = $omsOrder->save();
                //oms activity log
                $check_activity = OmsActivityLogModel::where("ref_id",$order_id)->where('store',$store_id)->where("activity_id",17)->first();
                if( !$check_activity ){
                    $activity_ins_obj = new OmsActivityLogModel();
                    $activity_ins_obj->activity_id = 17; //17 is for deliver exchange
                    $activity_ins_obj->ref_id = $order_id;
                    $activity_ins_obj->store = $store_id;
                    $activity_ins_obj->created_by_courier = $omsOrder->last_shipped_with_provider;
                    $activity_ins_obj->created_by = 0;
                    $activity_ins_obj->created_at = date('Y-m-d H:i:s');
                    $activity_ins_obj->save();
                }
                if($qry){
                    ExchangeAirwayBillTrackingModel::where(['order_id'=>$order_id,"shipping_provider_id"=>$omsOrder->last_shipped_with_provider,'store'=>$store_id])->update(["courier_delivered"=>1]);
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
