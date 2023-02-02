<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\InventoryManagement\InventoryManagementController;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsReturnOrdersModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\ShippingProvidersModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\ReturnAirwayBillTrackingModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryReturnQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsExchangeOrderAttachment;
use App\Models\Oms\OmsExchangeProductModel;
use App\Models\Oms\OmsExchangeReturnProductModel;
use App\Models\Oms\OmsOrderStatusModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Reseller\AccountModel;
use App\Models\Reseller\ResellerAccountDetailModel;
use App\Platform\Golem\OrderGolem;
use App\Platform\ShippingProviders\ShippingProvidersInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use App\Platform\Helpers\ToolImage;
use Illuminate\Support\Facades\Request AS RequestFacad;

use DB;
use Session;
use Validator;
use Excel;

class ReturnOrdersController extends Controller
{
    const VIEW_DIR = 'return';
    const PER_PAGE = 20;
    private $DB_BAOPENCART_DATABASE = '';
    private $static_option_id = 0;
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $opencart_image_url = '';
    private $store = '';

    function __construct(){
    //   $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
    //   $this->static_option_id = OmsSettingsModel::get('product_option','color');
    //   $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
    //   $this->website_image_source_url =  $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/';
    //   $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    //   $this->store = 1;
    }


    public function index(){
        $old_input = RequestFacad::all();

        $data = OmsReturnOrdersModel::with(['placeOrder','returnProducts.product','omsStore'])
                ->when(@$old_input['order_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_return_orders.order_id',$old_input['order_id']);
                })
                ->when(@$old_input['by_store'] != "",function($query) use ($old_input){
                    return $query->where('oms_return_orders.store',$old_input['by_store']);
                })
                // ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                //     return $query->where('oms_place_order.mobile','LIKE',"%".$old_input['telephone']."%");
                // })
                // ->when(@$old_input['customer'] != "",function($query) use ($old_input){
                //     return $query->where('oms_place_order.firstname','LIKE',"%".$old_input['customer']."%");
                // })
                // ->when(@$old_input['email'] != "",function($query) use ($old_input){
                //     return $query->where('oms_place_order.email','LIKE',"%".$old_input['email']."%");
                // })
                // ->when(@$old_input['total'] != "",function($query) use ($old_input){
                //     return $query->where('oms_place_order.total_amount',$old_input['total']);
                // })
                ->when(@$old_input['order_status_id'] != "",function($query) use ($old_input){
                    return $query->where('oms_return_orders.oms_order_status',$old_input['order_status_id']);
                })
                ->orderBy("oms_return_orders.updated_at",'DESC')
                //  $data = $data->orderByRaw("(CASE WHEN oms_return_orders.order_id > 0 THEN oms_return_orders.updated_at ELSE oms_return_orders.created_at END) DESC")
                ->paginate(20);
            // $data = $data->paginate(20);
            // $data = $this->getOrdersWithImage($data);
            // dd($data->toArray());
        ///
        $searchFormAction = URL::to('return');
        $orderStatus = OmsOrderStatusModel::all();
        $couriers = ShippingProvidersModel::where('is_active',1)->get();
        // dd($data->toArray());
        return view(self::VIEW_DIR.".index",compact('data','searchFormAction','orderStatus','old_input','couriers'));
    }

    public function approveReturnOrder() {
        OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID,Input::get('order_id'))->update(['is_approve' => 1]);
        return response()->json([
            'status' => true
        ]);

    }

    public function cancelReturnOrder(Request $request) {
        // dd($request->all());
        OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID,$request->order_id)->update(['is_cancel' => 0]);
        $order_id = $request->order_id.'-2';
        AccountModel::where('order_id', $order_id)->update(['is_delete' => 1]);
        $products = ExchangeOrderReturnProduct::select('order_product_id')->where('order_id', $request->order_id)->pluck('order_product_id');
        // dd($products->toArray());
		OrderedProductModel::whereIn('order_product_id', $products->toArray())->update(['is_return' => 0]);
        return response()->json([
            'status' => true
        ]);
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
        $omsOrders = OmsReturnOrdersModel::with(['airway_bills','shipping_provider'])
        ->orderBy('updated_at', 'DESC')
        ->groupBy('oms_return_orders.order_id');
        if(RequestFacad::get('order_id')){
            $omsOrders = $omsOrders->where('oms_orders.order_id', RequestFacad::get('order_id'));
        }
        if(RequestFacad::get('order_status_id')){
            $omsOrders = $omsOrders->where('oc_order.order_status_id', RequestFacad::get('order_status_id'));
        }
        if (RequestFacad::get('shipping_provider_id')){
            $omsOrders = $omsOrders->where('oms_orders.last_shipped_with_provider', RequestFacad::get('shipping_provider_id'));
        }
        if (RequestFacad::get('date_from') && RequestFacad::get('date_to')){
            $date_from = Carbon::createFromFormat("Y-m-d", RequestFacad::get('date_from'))->toDateString();
            $date_to = Carbon::createFromFormat("Y-m-d",RequestFacad::get('date_to'))->toDateString();
            $omsOrders = $omsOrders->whereDate('awb.created_at', '>=', $date_from)
            ->whereDate('awb.created_at', '<=', $date_to);
        }
        if (RequestFacad::get('awb_number')){
            $omsOrders = $omsOrders->where('awb.airway_bill_number', RequestFacad::get('awb_number'));
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
    public function printAwb() {
        if(RequestFacad::get('submit') == 'awb' && RequestFacad::get('order_id')){
            $orderIds = RequestFacad::get('order_id');
            $orders = collect();
            if( is_array($orderIds) && count($orderIds) > 0 ){
                foreach( $orderIds as $order_id ){

                    $order = OmsPlaceOrderModel::with(['returnProducts.product'])->where("order_id",$order_id)->first();
                    $orders->push($order);
                }
            }

			$order_tracking = ReturnAirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
			$order_tracking_ids = $order_tracking->pluck(ReturnAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
			// echo "<pre>"; print_r($order_tracking_ids->toArray()); die;
			$shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

			return view(self::VIEW_DIR . ".awb_print", compact('orders','order_tracking','shipping_providers'));
        }
    }
    private function manageResellerAccount($order_id, $reseller) {

        $resellerAccounts = AccountModel::where('order_id', $order_id.'-2')->where('reseller_id', $reseller)->get();
        foreach($resellerAccounts as $resellerAccount) {
            if($resellerAccount->transaction_type == 'Return Request') {
                $resellerAccount->transaction_type = 'Return Received';
            }
            $resellerAccount->transaction_status = 2;
            $resellerAccount->transaction_date = date('Y-m-d');
            $resellerAccount->save();
        }

    }

    private function addtransaction($data = array()){

		$customer_id = (int)$data['customer_id'];
        $trnsctn = DB::table(DB::raw($this->DB_BAOPENCART_DATABASE. '.oc_e_wallet_transaction'))->insertGetId(
            ['customer_id' => $customer_id, 'price' => $data['amount'], 'description' => $data['description'], 'date_added' => date('Y-m-d H:i:s')]
        );

        $transaction_id = $trnsctn;

		$balance = $this->getBalance($data);
        EWalletModel::where('customer_id', $customer_id)->where('transaction_id', $transaction_id)->update(['balance' => $balance]);
		return $transaction_id;
	}

    public function getBalance($data = array()){
		if(isset($data['customer_id'])) $customer_id = (int)$data['customer_id'];
        $sum = EWalletModel::where('customer_id', $customer_id)->sum('price');
		return $sum;
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
    public function return(){
        return view(self::VIEW_DIR . ".return_order", ["old_input" => RequestFacad::all()]);
    }
    public function getReturn(){
        if(count(RequestFacad::all()) > 0){
            $order_id = RequestFacad::get('order_id');
            $order_id = str_replace("-2","",$order_id);
            $order = OmsReturnOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 2) //3 for shipped order
            // ->where('store',$this->store)
            ->first();
            if($order){
                $order->order_products = OmsExchangeReturnProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id", $order->store)->get();
            }else{
                echo "<h2 style='color:red'>Order cannot be return in this status OR order not found.</h2>";
                return;
            }
        }
        return view(self::VIEW_DIR . '.return_order_search', ["order" => $order]);
    }
    public function updateReturn(){  //-2 return
        if(count(RequestFacad::all()) > 0 && RequestFacad::get('submit') == 'update_returned'){
            $order_id  = RequestFacad::get('order_id');
            $order_id  = str_replace("-2","",$order_id);
            $store_id = RequestFacad::get('oms_store');
            $is_damaged = RequestFacad::get('isdemage');
            // dd($is_damaged );

            $exists    = OmsReturnOrdersModel::where('order_id', $order_id)
            ->where('oms_order_status', 2)  //3 for shipped status
            ->where('store',$store_id)
            ->exists();

            if($exists){
                $order_products = OmsExchangeReturnProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
                // dd($order_products->toArray());
                if( $order_products ){
                    $order_quantity = 0;
                    $quantity_data = "";
                    foreach( $order_products as $key => $order_product ){
                        $order_quantity = $order_product->quantity;
                        $product_id      = $order_product->product_id;
                        $option_id       = $order_product->productOption->option_id;
                        $option_value_id = $order_product->productOption->option_value_id;
                        $order_id_dash_two =  $order_product->order_id."-2";
                        $check_exist = OmsInventoryReturnQuantityModel::where("order_id",$order_id_dash_two)->where('product_id',$product_id)
                        ->where('option_id',$option_id)->where('option_value_id',$option_value_id)->where('store',$order_product->store_id)->first();
                        if( !$check_exist ){
                            $new_return = new OmsInventoryReturnQuantityModel();
                            $new_return->order_id        = $order_id_dash_two;
                            $new_return->product_id      = $product_id;
                            $new_return->oms_product_id  = $product_id;
                            $new_return->option_id       = $option_id;
                            $new_return->option_value_id = $option_value_id;
                            $new_return->quantity        = $order_quantity;
                            $new_return->store           = $order_product->store_id;
                            if( $new_return->save() ){
                                $increment_updated     = 'updated_quantity+' . $order_quantity;
                                $increment_available   = 'available_quantity+' . $order_quantity;
                                if( is_array( $is_damaged ) && array_key_exists($order_product->product_option_id,$is_damaged) ){
                                    $return_query = OmsInventoryProductOptionModel::where(["product_id"=>$product_id,"product_option_id"=>$order_product->product_option_id])
                                    ->update(['updated_quantity'=>DB::raw($increment_updated),"available_quantity"=>DB::raw($increment_available)]);
                                }else{
                                  $return_query = false;
                                }
                                if( $return_query ){
                                    //stock history
                                    $quantity_data .= $order_product->option_value . "-(" .$order_quantity. "), ";
                                    $comment = "This quantity added is returned from the return number #".$order_id."-2 from Customer.<br>Quantity: ". rtrim($quantity_data, ", ");
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

                    OmsReturnOrdersModel::where("order_id",$order_id)->update(['oms_order_status'=>4]);  //4 is for deliver its mean -2 is delivered.
                    // if($order->reseller_id > 0) {
                    //     $this->manageResellerAccount($order_id,$order->reseller_id);

                    //     // $transaction = [
                    //     //     'customer_id' => $order->customer_id,
                    //     //     'amount'      => $opencart_product->total,
                    //     //     'description' => 'Added reseller return amount by system, order id is '.$order_id
                    //     // ];
                    //     // $trnstn = $this->addtransaction($transaction);
                    // }
                }
                OmsActivityLogModel::newLog($order_id,22, $store_id); //8 is for return order
                Session::flash('message', 'Order returned successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('/return/search');
            }else{
                Session::flash('message', "Order product returned in 'AWB status' status only OR order not found.");
                Session::flash('alert-class', 'alert-danger');
                return redirect('/return/search');
            }
        }
    }

    protected function tab_links(){
        $route_name = \Request::route()->getName();
        if($route_name == 'exchange_returns'){
            if(Input::get('order_status_id')){
                return array(
                  'normal'    =>  route('orders') . '?order_status_id=' . Input::get('order_status_id'),
                  'exchange'  =>  route('exchange_orders') . '?order_status_id=' . Input::get('order_status_id'),
                  'return'    =>  route('exchange_returns') . '?order_status_id=' . Input::get('order_status_id'),
                );
            }else{
                return array(
                    'normal'    =>  route('orders'),
                    'exchange'  =>  route('exchange_orders'),
                    'return'    =>  route('exchange_returns'),
                );
            }
        }else if($route_name == 'exchange_returns.deliver-orders'){
            return array(
                'normal'    =>  route('orders.deliver-orders'),
                'exchange'  =>  route('exchange_orders.deliver-orders'),
                'return'    =>  route('exchange_returns.deliver-orders'),
            );
        }else if($route_name == 'exchange_returns.awb-generated'){
            return array(
                'normal'    =>  route('orders.awb-generated'),
                'exchange'  =>  route('exchange_orders.awb-generated'),
                'return'    =>  route('exchange_returns.awb-generated'),
            );
        }
    }
}
