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

    public function addInventoryQuantity($order_id, $demaged = null){

        $omsOrder = OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID, $order_id)->where('store', $this->store)->first();
        $orderd_products = ExchangeOrderReturnProduct::where('order_id', $order_id)->get();
        $order = OrdersModel::with('orderd_totals')->where('order_id', $order_id)->first();
        // dd($orderd_products);
        $product_ids = [];
        if($orderd_products){
            foreach ($orderd_products as $key => $product) {
                if($demaged && in_array($product->order_product_id, $demaged)) {
                    continue;
                }
                $opencart_product = OrderedProductModel::select('product_id','price','total')->where('order_product_id', $product->order_product_id)->first();
                array_push($product_ids, $opencart_product->product_id);
                $opencart_sku = ProductsModel::select('sku')->where('product_id', $opencart_product->product_id)->first();
                $exists = OmsInventoryProductModel::select("*","option_name AS color","option_value AS size")->where('sku', $opencart_sku->sku)->first();
                if($exists){
                    $product_id = $exists->product_id;

                    if( !empty($exists->size) && $exists->size > 0){
                        $order_options = OrderOptionsModel::where('order_product_id', $product->order_product_id)->get();
                        $total_quantity = 0;
                        if($order_options){
                            $quantity_data = "";
                            foreach ($order_options as $key => $option) {
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                                ->where('option_description.name', $option->name)
                                                ->where('ovd.name', $option->value)
                                                ->first();
							                  $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                                if($option_data && $option_data->option_id  != $ba_color_option_id){
								                    $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);

                                    OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('available_quantity' => DB::raw('available_quantity+' . $product->quantity), 'updated_quantity' => $product->quantity ));

                                    $total_quantity = $total_quantity + $product->quantity;
                                    $quantity_data .= $option->value . "-(" . $product->quantity . "), ";
                                }
                            }

                            // OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $this->static_option_id)->where('option_value_id', $exists->color)->update(array('available_quantity' => DB::raw('available_quantity+' . $total_quantity), 'updated_quantity' => $total_quantity ));

                            $comment = "This quantity added is returned from the order number #".$order_id."-2 <br>Quantity: ". rtrim($quantity_data, ", ");
                            $OmsInventoryAddStockHistoryModel = new OmsInventoryAddStockHistoryModel();
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_PRODUCT_ID} = $product_id;
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_USER_ID} = session('user_id');
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_COMMENT} = $comment;
                            $OmsInventoryAddStockHistoryModel->save();
                            //commented because same as above code.
                            // foreach ($order_options as $key => $option) {
                            //     $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                            //                     ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                            //                     ->where('option_description.name', $option->name)
                            //                     ->where('ovd.name', $option->value)
                            //                     ->first();

                            //     if($option_data && $option_data->option_id != $this->static_option_id){
                            //         $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
                            //         $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
                            //         $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $product_id;
                            //         $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID} = $option_data->option_id;
                            //         $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $option_data->option_value_id;
                            //         $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $product->quantity;
                            //         $OmsInventoryAddStockOptionModel->save();
                            //     }
                            // }

                            // $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
                            // $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
                            // $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $product_id;
                            // $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID} = $this->static_option_id;
                            // $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $exists->color;
                            // $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $total_quantity;
                            // $OmsInventoryAddStockOptionModel->save();
                        }
                    }else{
                        $order_options = OrderOptionsModel::where('order_product_id', $product->order_product_id)->get();
                        $total_quantity = 0;
                        if($order_options){
                            $quantity_data = "";
                            foreach ($order_options as $key => $option) {
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                                ->where('option_description.name', $option->name)
                                                ->where('ovd.name', $option->value)
                                                ->first();

                                if($option_data){
                                  $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
                                    OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('available_quantity' => DB::raw('available_quantity+' . $product->quantity), 'updated_quantity' => $product->quantity ));

                                    $total_quantity = $total_quantity + $product->quantity;
                                    $quantity_data .= $option->value . "-(" . $product->quantity . "), ";
                                }
                            }

                            // OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $this->static_option_id)->where('option_value_id', $exists->color)->update(array('available_quantity' => DB::raw('available_quantity+' . $total_quantity), 'updated_quantity' => $total_quantity ));

                            $comment = "This quantity added is returned from the order number #".$order_id."-2 <br>Quantity: ". rtrim($quantity_data, ", ");
                            $OmsInventoryAddStockHistoryModel = new OmsInventoryAddStockHistoryModel();
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_PRODUCT_ID} = $product_id;
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_USER_ID} = session('user_id');
                            $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_COMMENT} = $comment;
                            $OmsInventoryAddStockHistoryModel->save();

                            foreach ($order_options as $key => $option) {
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                                ->where('option_description.name', $option->name)
                                                ->where('ovd.name', $option->value)
                                                ->first();

                                if($option_data){
                                  $oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
                                    $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
                                    $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
                                    $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $product_id;
                                    $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID} = $oms_option_det->oms_options_id;
                                    $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $oms_option_det->oms_option_details_id;
                                    $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $product->quantity;
                                    $OmsInventoryAddStockOptionModel->save();
                                }
                            }
                        }
                    }

                    InventoryManagementController::updateSitesStock($opencart_sku->sku);
                }
                else{
                    $OrderedProductModel = OrderedProductModel::select('product_id','quantity')->where('order_product_id', $product->order_product_id)->first()->toArray();
                    $OrderOptionsModel = OrderOptionsModel::select('product_option_id','product_option_value_id')->where('order_product_id', $product->order_product_id)->first()->toArray();
                    ProductsModel::where('product_id', $OrderedProductModel['product_id'])->update(array('quantity' => DB::raw('quantity+'.$OrderedProductModel['quantity'])));
                    ProductOptionValueModel::where('product_option_value_id', $OrderOptionsModel['product_option_value_id'])->where('product_option_id', $OrderOptionsModel['product_option_id'])->update(array('quantity' => DB::raw('quantity+'.$OrderedProductModel['quantity'])));
                }

            }
            if($order->reseller_id > 0) {
                $this->manageResellerAccount($order_id,$order->reseller_id);

                // $transaction = [
                //     'customer_id' => $order->customer_id,
                //     'amount'      => $opencart_product->total,
                //     'description' => 'Added reseller return amount by system, order id is '.$order_id
                // ];
                // $trnstn = $this->addtransaction($transaction);
            }
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
    public function return_order(){
        return view(self::VIEW_DIR . ".return_order", ["old_input" => Input::all()]);
    }
    public function get_return_order(){
        if(count(Input::all()) > 0){
            $order_id = Input::get('order_id');
            $order_id = str_replace("-2", "", $order_id);
            $order = OrdersModel::select('*')
                    ->where('order_id', $order_id)
                    ->first();
            $omsOrder = OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID, $order_id)->where(OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS, OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED)->where('is_approve', 1)->where('store', $this->store)->first();
            $order_array = array();
            if($order && $omsOrder){
                $products = ExchangeOrderReturnProduct::select('*')->where('order_id', $order_id)->get();
                $product_array = array();
                if($products){
                    foreach ($products as $product) {
                        $orderProducts = OrderedProductModel::select('*')->where('order_product_id', $product->order_product_id)->groupBy('order_product_id')->first();

                        $opencartProduct = ProductsModel::select('sku')->where('product_id', $orderProducts->product_id)->first();
                        $omsProduct = OmsInventoryProductModel::select('*','option_name AS color','option_value AS size')->where('sku', $opencartProduct->sku)->first();

                        if($omsProduct){
                            $options = OrderOptionsModel::select('order_option.product_option_id','order_option.product_option_value_id','order_option.name','order_option.value','op.quantity')
                                        ->leftJoin('order_product as op', 'op.order_product_id', '=', 'order_option.order_product_id')
                                        ->where('order_option.order_id', $order_id)->where('order_option.order_product_id', $product->order_product_id)->get()->toArray();

                            $option_array = array();
                            foreach ($options as $option) {
                                $optionData = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                                ->where('option_description.name', $option['name'])
                                                ->where('ovd.name', $option['value'])
                                                ->first();
                                $OmsOptionsData = OmsInventoryOptionValueModel::OmsOptionsFromBa($optionData->option_id,$optionData->option_value_id);
                                $omsColorId = OmsDetails::colorId($omsProduct['color']);
                                $oms_color_option_id = OmsOptions::colorOptionId();
                                if($omsProduct['size'] == 0){
                                    $barcode = $omsProduct->product_id;
                                    $barcode .= $omsColorId;
                                    $option_n_v = $option['name']. ' - ' .$option['value'];

                                    // $alreadyPicked = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('product_id', $orderProducts->product_id)->where('option_id',$oms_color_option_id)->where('option_value_id', $omsColorId)->exists();
                                    $alreadyPicked = OmsInventoryReturnQuantityModel::where('order_id', $order_id . "-1")->where('product_id', $orderProducts->product_id)->where('option_id',$oms_color_option_id)->where('option_value_id', $omsColorId)->exists();

                                    $option_array[] = array(
                                        'option'                    =>  $option_n_v,
                                        'option_id'                 =>  $optionData->option_id,
                                        'option_value_id'           =>  $optionData->option_value_id,
                                        'barcode'                   =>  $barcode,
                                        'quantity'                  =>  $alreadyPicked ? 0 : $product->quantity,
                                        'product_option_value_id'   =>  $option['product_option_value_id'],
                                        'manual_checkable'          =>  $this->forceScanning($orderProducts->model)
                                    );

                                }else{
                                    $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                                    if($optionData->option_id != $ba_color_option_id){
                                        $barcode = $omsProduct->product_id;
                                        $barcode .= $OmsOptionsData->oms_option_details_id;
                                        $option_n_v = $option['name']. ' - ' .$option['value'];

                                        $alreadyPicked = OmsInventoryReturnQuantityModel::where('order_id', $order_id . "-1")->where('product_id', $orderProducts->product_id)->where('option_id', $OmsOptionsData->oms_options_id)->where('option_value_id', $OmsOptionsData->oms_option_details_id)->exists();

                                        $option_array[] = array(
                                            'option'                    =>  $option_n_v,
                                            'option_id'                 =>  $optionData->option_id,
                                            'option_value_id'           =>  $optionData->option_value_id,
                                            'barcode'                   =>  $barcode,
                                            'quantity'                  =>  $alreadyPicked ? 0 : $product->quantity,
                                            'product_option_value_id'   =>  $option['product_option_value_id'],
                                            'manual_checkable'          =>  $this->forceScanning($orderProducts->model)

                                        );
                                    }
                                }
                            }

                            $product_array[] = array(
                                'order_product_id'  =>  $orderProducts->order_product_id,
                                'product_id'        =>  $orderProducts->product_id,
                                'oms_product_id'    =>  $omsProduct->product_id,
                                'image'             =>  $this->get_product_image($orderProducts->product_id, 100, 100),
                                'name'              =>  $orderProducts->name,
                                'model'             =>  $orderProducts->model,
                                'options'           =>  $option_array,
                            );
                        }
                    }

                    $order_array = array(
                        'order_id'          =>  $order_id . "-2",
                        'normal_order_id'   =>  $order_id,
                        'total'             =>  $order->total,
                        'status'            =>  $order->status,
                        'date'              =>  $omsOrder->created_at,
                        'products'          =>  $product_array,
                    );
                }
            }
        }
        return view(self::VIEW_DIR . '.return_order_search', ["order" => $order_array]);
    }
    public function update_return_order(){
        // dd(Input::all());
        if(count(Input::all()) > 0 && Input::get('submit') == 'update_returned'){
            $order_id = Input::get('order_id');
            $order_id = str_replace("-2", "", $order_id);
            $isdemage = Input::get('isdemage');

            $omsOrder = OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID, $order_id)->where('store', $this->store)->first();
            $oms_e_Order = OmsExchangeOrdersModel::where(OmsExchangeOrdersModel::FIELD_ORDER_ID, $order_id)->first();

            if($omsOrder->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} == OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED){
                $this->addInventoryQuantity($order_id, $isdemage);

                $omsOrder->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_DELEIVERED;
                $omsOrder->{OmsReturnOrdersModel::UPDATED_AT} = Carbon::now();
                $omsOrder->save();
                OmsActivityLogModel::newLog($order_id,22, $this->store); //22 is for Exchange Deliver/return from customer
            }else{
                Session::flash('message', 'Order can be Delivered only in \'AWB Generate\' Status');
                Session::flash('alert-class', 'alert-danger');
                return redirect('/exchange_returns/return_order');
            }
            return redirect('/exchange_returns/return_order');
        }else{
            Session::flash('message', 'Order product picked successfully.');
            Session::flash('alert-class', 'alert-success');
            return redirect('/exchange_returns/return_order');
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
