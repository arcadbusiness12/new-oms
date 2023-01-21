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

    public function exchangeReturnProduct(){
        $json = array();
        $json['success'] = false;
        $json['redirect'] = false;
        $json['error'] 	= false;

        $postData = Input::all();
        if($postData){
            $order_id = $postData['order_id'];
            $order_product_ids = $postData['order_product_ids'];
            $isAlreadyOrdered = ExchangeOrdersModel::select('*')->where('order_id',$postData['order_id'])->get();
            if(!$isAlreadyOrdered->count()){
            	ExchangeOrderReturnProduct::where('order_id',$postData['order_id'])->delete();
	            foreach ($order_product_ids as $order_product_id => $quantity) {
	                $orderReturnProductModel = new ExchangeOrderReturnProduct();
	                $orderReturnProductModel->{ExchangeOrderReturnProduct::FIELD_ORDER_ID} = $order_id;
                    $orderReturnProductModel->{ExchangeOrderReturnProduct::FIELD_ORDER_PRODUCT_ID} = $order_product_id;
	                $orderReturnProductModel->{ExchangeOrderReturnProduct::FIELD_ORDER_QUANTITY} = $quantity;
	                $orderReturnProductModel->save();
	            }
	            $json['success'] = true;
	            $json['redirect'] = url('/exchange_orders/add/' . $postData['order_id']);
            }else{
	            $json['error'] = 'Exchange Order Already Placed!';
            }
        }
        return response()->json(array('success' => $json['success'],'redirect' => $json['redirect'],'error' => $json['error']));
    }
    public function checkOrderStatus(Request $request){
        $postData = Input::all();
        $status = false;
        if(isset($postData['order_id']) && $postData['order_id']){
            $status_id = OrderStatusModel::select('*')->where('name','Delivered')->first()->order_status_id;
            $history = OrdersModel::select('order_status_id')->where('order_id',$postData['order_id'])->first();
            if($history){
                if($status_id == $history->order_status_id) $status = true;
            }
        }
        return response()->json(array('status' => $status));
    }
    public function generateAWB(){
        $ordersStatus = OrderStatusModel::all();
        $shippingProviders = ShippingProvidersModel::where('is_active', 1)->get();
        return view(self::VIEW_DIR . ".generateAWBBarcodeRead", ["orderStatus" => $ordersStatus, "shippingProviders" => $shippingProviders]);
    }
	public function awb(){
        if(Session::get('orderIdsForReturnAWBGenerate')){
            $orderIds = Session::get('orderIdsForReturnAWBGenerate')[0];
            Session::put('orderIdsForReturnAWBGenerate', array());

            $orders = array();
            foreach ($orderIds as $orderId) {
                $return_products = ExchangeOrderReturnProduct::select('*')->where('order_id',$orderId)->paginate(self::PER_PAGE)->appends(Input::all());
                if($return_products->count()) {
                    $products = array();
                    foreach ($return_products as $order) {
                        $orderProducts = OrderedProductModel::select('*')->where('order_product_id', $order->order_product_id)->groupBy('order_product_id')->first()->toArray();
                        $products[] = array(
                            'product_id'    => $orderProducts['product_id'],
                            'image'         => $this->get_product_image($orderProducts['product_id'], 100, 100),
                            'name'          => $orderProducts['name'],
                            'model'         => $orderProducts['model'],
                            'quantity'      => $orderProducts['quantity'],
                            'price'         => $orderProducts['price'],
                            'total'         => $orderProducts['total'],
                            'order_options' => OrderOptionsModel::select('*')->where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID,$order->order_product_id)->get()->toArray()
                        );
                    }
                    $orderDetails = OrdersModel::select('*')->where('order_id',$orderId)->first()->toArray();
                    $orders[] = array(
                        'order_id'              =>  $orderId,
                        'shipping_firstname'    =>  $orderDetails['shipping_firstname'],
                        'shipping_lastname'     =>  $orderDetails['shipping_lastname'],
                        'shipping_address_1'    =>  $orderDetails['shipping_address_1'],
                        'shipping_city'         =>  $orderDetails['shipping_city'],
                        'telephone'             =>  $orderDetails['telephone'],
                        'email'                 =>  $orderDetails['email'],
                        'currency_code'         =>  $orderDetails['currency_code'],
                        'total'                 =>  $orderDetails['total'],
                        'payment_method'        =>  $orderDetails['payment_method'],
                        'date_added'            =>  $orderDetails['date_added'],
                        'date_modified'         =>  $orderDetails['date_modified'],
                        'orderd_products'       =>  $products,
                    );
                }
            }
            $order_tracking = ReturnAirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
            $order_tracking_ids = $order_tracking->pluck(ReturnAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
            $shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();
            return view(self::VIEW_DIR . ".return_awd", ['orders' => $orders, 'order_tracking' => $order_tracking, 'shipping_providers' => $shipping_providers]);
        }else{
            return redirect('/');
        }
    }
    public function deliverOrdersView(){
        $tab_links = $this->tab_links();
        return view(self::VIEW_DIR . ".deliver_orders", ["tab_links" => $tab_links]);
    }
    public function deliverOrders(){
        $orderIds = Input::get('generate-awb-chbx'); // ordersID array
        if ($orderIds){
            try{
                foreach ($orderIds as $orderId){
                    $omsOrder = OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID, $orderId)->where('store',$this->store)->first();
                    $oms_e_Order = OmsExchangeOrdersModel::where(OmsExchangeOrdersModel::FIELD_ORDER_ID, $orderId)->first();

                    if($omsOrder->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} == OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED){
                        /*$omsOrder_status_id = $oms_e_Order->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS};
                        $omsrOrder_status_id = $omsOrder->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS};

                        if($omsrOrder_status_id == OmsOrderStatusInterface::OMS_ORDER_STATUS_DELEIVERED){
                            throw new \Exception("Order has already Delivered, so you can't this order to be delivered");
                        }else if($omsOrder_status_id < OmsOrderStatusInterface::OMS_ORDER_STATUS_DELEIVERED){
                            throw new \Exception("Exchange Order hasn't Delivered yet, so you can't this order to be delivered");
                        }else{
                        }*/
                        $this->addInventoryQuantity($orderId);

                        $omsOrder->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_DELEIVERED;
                        $omsOrder->{OmsReturnOrdersModel::UPDATED_AT} = Carbon::now();
                        $omsOrder->save();

                        $ExchangeOrderReturnProduct = ExchangeOrderReturnProduct::where('order_id',$orderId)->get()->toArray();
                        foreach ($ExchangeOrderReturnProduct as $order) {
                            $OrderedProductModel = OrderedProductModel::select('product_id','quantity')->where('order_product_id', $order['order_product_id'])->first()->toArray();
                            $OrderOptionsModel = OrderOptionsModel::select('product_option_id','product_option_value_id')->where('order_product_id', $order['order_product_id'])->first()->toArray();
                            ProductsModel::where('product_id', $OrderedProductModel['product_id'])->update(array('quantity' => DB::raw('quantity+'.$OrderedProductModel['quantity'])));
                            ProductOptionValueModel::where('product_option_value_id', $OrderOptionsModel['product_option_value_id'])->where('product_option_id', $OrderOptionsModel['product_option_id'])->update(array('quantity' => DB::raw('quantity+'.$OrderedProductModel['quantity'])));
                        }
                    }else{
                        throw new \Exception("Order can be Delivered only in 'AWB Generate' Status");
                    }
                }
                return redirect('/exchange_returns/deliver-orders');
            }
            catch (\Exception $e){
                Session::flash('message', $e->getMessage());
                Session::flash('alert-class', 'alert-danger');
                return redirect('/exchange_returns/deliver-orders');
            }
        }else{
            Session::flash('message', 'Please select order to Deliver.');
            Session::flash('alert-class', 'alert-danger');
            return redirect('/exchange_returns/deliver-orders');
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
			// $order_data = OrdersModel::with(['status', 'orderd_products'])
			// ->whereIn(OrdersModel::FIELD_ORDER_ID, $orderIds)
			// ->get();
			// echo "<pre>"; print_r($orders->toArray());

			$order_tracking = ReturnAirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
			$order_tracking_ids = $order_tracking->pluck(ReturnAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
			// echo "<pre>"; print_r($order_tracking_ids->toArray()); die;
			$shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

			return view(self::VIEW_DIR . ".awb_print", compact('orders','order_tracking','shipping_providers'));
        }
    }

    public function awbExport(){
        $where = [];
        if (Input::get('order_id')){
            $where = array_dot(array_add($where, 'oms_return_orders.'.ExchangeOrdersModel::FIELD_ORDER_ID, str_replace("-2", "", Input::get('order_id'))));
        }
        if(Input::get('order_status_id')){
            $where = array_add($where, ExchangeOrdersModel::FIELD_ORDER_STATUS_ID , Input::get('order_status_id'));
        }else{

        }
        if (Input::get('shipping_provider_id')){
            $where = array_add($where, OmsExchangeOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER, Input::get('shipping_provider_id'));
        }
        if (Input::get('date_from') && Input::get('date_to')){
            $date_from = Carbon::createFromFormat("Y-m-d", Input::get('date_from'))->toDateString();
            $date_to = Carbon::createFromFormat("Y-m-d",Input::get('date_to'))->toDateString();
            $orderIDs = ReturnAirwayBillTrackingModel::whereDate(ReturnAirwayBillTrackingModel::CREATED_AT, '>=', $date_from)
                                                ->whereDate(ReturnAirwayBillTrackingModel::CREATED_AT, '<=', $date_to)
                                                ->get();
            $ids = $orderIDs->pluck(ReturnAirwayBillTrackingModel::FIELD_ORDER_ID);
        }
        $shippingProviders = ShippingProvidersModel::orderBy('is_active', 'DESC')->get();
        $return_orders = OmsReturnOrdersModel::select('*')->where($where)->where('store', $this->store)->paginate(self::PER_PAGE)->appends(Input::all());
        $orders = array();
        if(count($return_orders)){
            foreach ($return_orders as $key => $order) {
                $airway_bills_array = array();
                $airway_bills = ReturnAirwayBillTrackingModel::select('order_id','airway_bill_number','shipping_provider_id','created_at')->where(ReturnAirwayBillTrackingModel::FIELD_ORDER_ID, $order->order_id)->get()->toArray();

                foreach ($airway_bills as $bill) {
                    $airway_bills_array[] = array(
                        'order_id'              =>  $bill['order_id'],
                        'airway_bill_number'    =>  $bill['airway_bill_number'],
                        'shipping_provider_id'  =>  $bill['shipping_provider_id'],
                        'shipping_provider'     => ShippingProvidersModel::select('name')->where(ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID, $bill['shipping_provider_id'])->first()->toArray(),
                        'created_at'            =>  $bill['created_at'],
                    );
                }
                $order_data = OrdersModel::select('total','telephone')->where('order_id', $order->order_id)->first();
                if($order_data){
                    $order_data = $order_data->toArray();
                    $orders[] = array(
                        'order_id'              =>  $order->order_id,
                        'total'                 =>  $order_data['total'],
                        'telephone'             =>  $order_data['telephone'],
                        'shipping_provider'     =>  ShippingProvidersModel::select('name')->where(ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID, $order->last_shipped_with_provider)->first()->toArray(),
                        'airway_bills'          =>  count($airway_bills_array) ? $airway_bills_array : ''
                    );
                }
            }
        }
        $ordersStatus = OrderStatusModel::all();

        Excel::create('AWB List', function($excel) use($orders,$ordersStatus,$shippingProviders) {
            $excel->sheet('AWB History', function($sheet) use($orders,$ordersStatus,$shippingProviders) {
                $sheet->loadView(self::VIEW_DIR . ".export_airway_bills", array("orders" => $orders,'orderStatus' => $ordersStatus, "shippingProviders" => $shippingProviders));
            });
        })->export('xls');
    }
    public function getOrderDetail($orderId=''){
        try{
            $order = array();
            $products = array();
            $orderId = (Input::get('orderId')) ? str_replace('-2', '', Input::get('orderId')) : str_replace('-2', '', $orderId); // if ajax post or same controller call ref: line# 542

            $return_products = ExchangeOrderReturnProduct::select('*')->where('order_id',$orderId)->paginate(self::PER_PAGE)->appends(Input::all());
            if($return_products->count()) {
                foreach ($return_products as $order) {
                    $orderProducts = OrderedProductModel::select('*')->where('order_product_id', $order->order_product_id)->groupBy('order_product_id')->first()->toArray();
                    $products[] = array(
                        'product_id'    => $orderProducts['product_id'],
                        'image'         => $this->get_product_image($orderProducts['product_id'], 100, 100),
                        'name'          => $orderProducts['name'],
                        'model'         => $orderProducts['model'],
                        'quantity'      => $orderProducts['quantity'],
                        'price'         => $orderProducts['price'],
                        'total'         => $orderProducts['total'],
                        'options'       => OrderOptionsModel::select('*')->where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID,$order->order_product_id)->get()->toArray()
                    );
                }
                $omsOrderStatus = OmsReturnOrdersModel::select(OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS,'created_at','updated_at')->where(OmsReturnOrdersModel::FIELD_ORDER_ID,$orderId)->where('store', $this->store)->get()->toArray();
                $orderDetails = OrdersModel::select('*')->where('order_id',$orderId)->first()->toArray();
                $order = array(
                    'order_id'              =>  $orderId,
                    'firstname'             =>  $orderDetails['firstname'],
                    'lastname'              =>  $orderDetails['lastname'],
                    'shipping_address_1'    =>  $orderDetails['shipping_address_1'],
                    'shipping_city'         =>  $orderDetails['shipping_city'],
                    'telephone'             =>  $orderDetails['telephone'],
                    'email'                 =>  $orderDetails['email'],
                    'currency_code'         =>  $orderDetails['currency_code'],
                    'total'                 =>  $orderDetails['total'],
                    'date_added'            =>  isset($omsOrderStatus[0]['created_at']) ? $omsOrderStatus[0]['created_at'] : '',
                    'date_modified'         =>  isset($omsOrderStatus[0]['updated_at']) ? $omsOrderStatus[0]['updated_at'] : '',
                    'products'              =>  $products,
                    'oms_status'            =>  $omsOrderStatus ? $omsOrderStatus[0]['oms_order_status'] : '',
                );
            }
            return view(self::VIEW_DIR . ".orderDetailForAWBPrint", ["order" => $order]);
        }
        catch (\Exception $e){
            return $e;
        }
    }
    public function forwardForShipping(){
        $orderIDs = Input::get('orderIDs');
        Session::push('orderIdsForReturnAWBGenerate', $orderIDs);

        try{
            $openCartOrderStatus = Input::get('open_cart_order_status'); // Status to be updated in opencart
            // Value from Ajax form
            if (empty(Input::get('shipping_providers'))){
                throw new \Exception("Please select Shipping Provider");
            }
            if (empty($orderIDs)){
                throw new \Exception("Please select an Order to Generate AWB");
            }
            $shippingProviderInput = explode('_', Input::get('shipping_providers'));
            $shippingProviderID = $shippingProviderInput[0]; // Shipping Provider ID
            $shippingProviders = $shippingProviderInput[1]; // Shipping provider Name // GetGive , MaraXpress etc

            if (!empty($openCartOrderStatus) && !empty($shippingProviders)){
                // Get Order Details from Opencart
                //$orders = ExchangeOrdersModel::with(['status', 'orderd_products'])->whereIn(ExchangeOrdersModel::FIELD_ORDER_ID, $orderIDs)->get();
                $orders = OrdersModel::select('*')->whereIn(OrdersModel::FIELD_ORDER_ID,$orderIDs)->get();

                //dd($orders->toArray());
                $ordersGolemArray = [];
                foreach ($orders as $order){
                    $shippingCompanyClass = "\\App\\Platform\\ShippingProviders\\" . $shippingProviders;
                    if (!class_exists($shippingCompanyClass)){
                        throw new \Exception("Shipping Provider Class {$shippingCompanyClass} does not exist");
                    }
                    $shipping = new $shippingCompanyClass();
                    // Initialize Order Golem to make a unified order object representation in order to send data to all shipping providers
                    $orderGolem = new OrderGolem();
                    $orderGolem->setOrderID($order->{OrdersModel::FIELD_ORDER_ID}."-2");

                    $name = $order->{OrdersModel::FIELD_CUSTOMER_FIRST_NAME} . " " . $order->{OrdersModel::FIELD_CUSTOMER_LAST_NAME};
                    $orderGolem->setCustomerName($name);

                    $orderGolem->setCustomerMobileNumber($order->{OrdersModel::FIELD_CUSTOMER_MOBILE_NUMBER});
                    $orderGolem->setOrderTotalAmount($order->{OrdersModel::FIELD_ORDER_TOTAL});

                    $shppingAddress = $order->{OrdersModel::FIELD_SHIPPING_ADDRESS_1} . " " .
                    $order->{OrdersModel::FIELD_SHIPPING_ADDRESS_2};

                    $orderGolem->setCustomerAddress($shppingAddress);
                    $orderGolem->setCustomerCity($order->{OrdersModel::FIELD_SHIPPING_ZONE});
                    $orderGolem->setPaymentMethod($order->{OrdersModel::FIELD_PAYMENT_METHOD});
                    $orderGolem->setCashOnDeliveryAmount($order->{OrdersModel::FIELD_ORDER_TOTAL});
                    $orderGolem->setSpecialInstructions($order->{OrdersModel::FIELD_ORDER_COMMENTS});
                    $orderGolem->setCustomerEmail($order->{OrdersModel::FIELD_CUSTOMER_EMAIL});
                    $orderGolem->setCustomerArea($order->{OrdersModel::FIELD_SHIPPING_ZONE});
                    $productDesc = "";
                    $qty = 0;
                    $return_products = ExchangeOrderReturnProduct::select('*')->where('order_id',$order->order_id)->get()->toArray();
                    foreach ($return_products as $product){
                        $orderProducts = OrderedProductModel::select('*')->where('order_product_id', $product['order_product_id'])->first()->toArray();
                        $orderProductOptions = OrderOptionsModel::select('*')->where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID,$product['order_product_id'])->get()->toArray();

                        $productDesc .= "[" . $orderProducts['model'];
                        $productDesc .= " (QTY:{$orderProducts['quantity']})";
                        if (count($orderProductOptions) > 0){
                            foreach ($orderProductOptions as $option){
                                $productDesc .= " (" . $option['name'] . ":" . $option['value'] . ")";
                            }
                        }
                        $productDesc .= "] ";
                        $qty = $qty + $orderProducts['quantity'];
                    }
                    $orderGolem->setTotalItemsQuantity($qty);
                    $orderGolem->setGoodsDescription($productDesc);
                    $ordersGolemArray[] = $orderGolem;
                }

                $response = $shipping->forwardOrder($ordersGolemArray);
                $shippingProviderResposne = [];

                foreach ($response as $orderID => $airwayBillNumber){
                    $orderID = str_replace("-2", "", $orderID);
                    if (!empty($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER]) && !preg_match("/[a-z]/i", $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER])){

                        //$exchange_order_id = ExchangeOrdersModel::select(ExchangeOrdersModel::FIELD_EXCHANGE_ORDER_ID)->where(ExchangeOrdersModel::FIELD_ORDER_ID,$orderID)->first()->exchange_order_id;
                        $awbTracking = new ReturnAirwayBillTrackingModel();
                        $awbTracking->{ReturnAirwayBillTrackingModel::FIELD_OMS_ORDER_ID} = $orderID;
                        $awbTracking->{ReturnAirwayBillTrackingModel::FIELD_ORDER_ID} = $orderID;
                        $awbTracking->{ReturnAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID} = $shippingProviderID;
                        $awbTracking->{ReturnAirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER} = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
                        $awbTracking->{ReturnAirwayBillTrackingModel::FIELD_AIRWAY_BILL_CREATION_ATTEMPT} = 1;
                        $awbTracking->save();

                        if(!OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID,$orderID)->where('store', $this->store)->exists()){
                            $omsUpdateStatus = new OmsReturnOrdersModel();
                            $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_ORDER_ID} = $orderID;
                            $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED;
                            $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER} = $shippingProviderID;
                            $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_CREATED_AT} = Carbon::now();
                            $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_UPDATED_AT} = Carbon::now();
                            $omsUpdateStatus->save();
                        }

                        $shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
                    }else{
                        $shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::MESSAGE_FROM_PROVIDER];
                    }
                }
            }else{
                throw new \Exception("Please select the status to update after airwaybill Generation");
            }
        }
        catch (\Exception $ex){
            dd($ex);
            return response()->json(array('success' => false,'data' => "<div class=\"alert bg-red\">{$ex->getMessage()}</div>"));
        }
        return response()->json(array('success' => true,'data' => view(self::VIEW_DIR . ".shipping_providers_response", ["response" => $shippingProviderResposne])->render()));
    }
    public function getOrderDetailDeliver(){
        try{
            $orderId = str_replace('-2', '', Input::get('orderId'));
            $order = [];
            $omsOrder = OmsReturnOrdersModel::where(OmsReturnOrdersModel::FIELD_ORDER_ID, $orderId)->where('store', $this->store)->get();
            if ($omsOrder){
                $return_products = ExchangeOrderReturnProduct::select('*')->where('order_id',$orderId)->paginate(self::PER_PAGE)->appends(Input::all());
                if($return_products->count()) {
                    $products = array();
                    $product_total = 0;
                    foreach ($return_products as $order) {
                        $orderProducts = OrderedProductModel::select('*')->where('order_product_id', $order->order_product_id)->groupBy('order_product_id')->first()->toArray();
                        $products[] = array(
                            'product_id'    => $orderProducts['product_id'],
                            'image'         => $this->get_product_image($orderProducts['product_id'], 100, 100),
                            'name'          => $orderProducts['name'],
                            'model'         => $orderProducts['model'],
                            'quantity'      => $order['quantity'],
                            'price'         => number_format($orderProducts['price'],2),
                            'total'         => number_format(($order['quantity'] * $orderProducts['price']),2),
                            'options'       => OrderOptionsModel::select('*')->where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID,$order->order_product_id)->get()->toArray()
                        );
                        $product_total = $product_total + ($order['quantity'] * $orderProducts['price']);
                    }
                    $omsOrderStatus = OmsReturnOrdersModel::select(OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS,'created_at','updated_at')->where(OmsReturnOrdersModel::FIELD_ORDER_ID,$orderId)->where('store', $this->store)->get()->toArray();
                    $orderDetails = OrdersModel::select('*')->where('order_id',$orderId)->first()->toArray();
                    $order = array(
                        'order_id'              =>  $orderId,
                        'firstname'             =>  $orderDetails['firstname'],
                        'lastname'              =>  $orderDetails['lastname'],
                        'shipping_address_1'    =>  $orderDetails['shipping_address_1'],
                        'shipping_city'         =>  $orderDetails['shipping_city'],
                        'telephone'             =>  $orderDetails['telephone'],
                        'email'                 =>  $orderDetails['email'],
                        'currency_code'         =>  $orderDetails['currency_code'],
                        'total'                 =>  number_format($product_total,2),
                        'date_added'            =>  isset($omsOrderStatus[0]['created_at']) ? $omsOrderStatus[0]['created_at'] : '',
                        'date_modified'         =>  isset($omsOrderStatus[0]['updated_at']) ? $omsOrderStatus[0]['updated_at'] : '',
                        'products'              =>  $products,
                        'oms_status'            =>  $omsOrderStatus ? $omsOrderStatus[0]['oms_order_status'] : '',
                    );
                }
                if($order && $orderDetails[ExchangeOrdersModel::FIELD_ORDER_STATUS_ID] != ExchangeOrdersModel::OPEN_CART_STATUS_EXCHANGE){
                    return view(self::VIEW_DIR . ".orderDetailForAWBPrint", ["order" => $order]);
                }
                else return '';
            }
        }
        catch (\Exception $e){
            return $e;
        }
    }
    public function getShippingTrackingDetail(){
        $json = array();
        $shipping_privider = Input::get('shipping_id');
        $shipping_number = Input::get('shipping_number');
        $api_account_number =  env('TFM_ACCOUNT_NUMBER', '');
        $provider = ShippingProvidersModel::select('*')->where(ShippingProvidersModel::FIELD_SHIPPING_PROVIDER_ID, $shipping_privider)->first()->toArray();

        if($provider['name'] == 'GetGive' && $provider['is_active']){
            $json['msg'] = 'Only Track TFM Express Shipping Details!';
            $json['tracking'] = array();
        }else if($provider['name'] == 'MaraXpress' && $provider['is_active']){
            $json['msg'] = 'Only Track TFM Express Shipping Details!';
            $json['tracking'] = array();
        }else if($provider['name'] == 'TfmExpress' && $provider['is_active']){
            $json['tracking'] = array();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.tfmex.com/TFMservice.svc/rest/trackingbyawb/'. $shipping_number .'/'. $api_account_number);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            $api_content = json_decode($response, true);

            /*$api_content = file_get_contents('https://api.tfmex.com/TFMservice.svc/rest/trackingbyawb/'. $shipping_number .'/'. $api_account_number);
            $api_content = json_decode($api_content, true);*/
            if(isset($api_content['trackingbyawbResult'])){
                if($api_content['trackingbyawbResult']['status']){
                    $json['tracking']['shipping_number'] = $shipping_number;
                    foreach ($api_content['trackingbyawbResult']['response'] as $tracking) {
                        $json['tracking']['details'][] = array(
                            'date'      => $tracking['colDate'],
                            'status'    => $tracking['colStatus'],
                        );
                    }
                    $json['msg'] = 'success';
                }
            }
        }else if($provider['name'] == 'ShafiExpress' && $provider['is_active']){
            $json['msg'] = 'Only Track TFM Express Shipping Details!';
            $json['tracking'] = array();
        }else if($provider['name'] == 'NiazExpress' && $provider['is_active']){
            $json['msg'] = 'Only Track TFM Express Shipping Details!';
            $json['tracking'] = array();
        }else if($provider['name'] == 'FetchrExpress' && $provider['is_active']){
            $json['tracking'] = array();
            $accountNumber = config('services.fetchrExpress')['accountNumber'];
            $apiUrl = config('services.fetchrExpress')['url'];
            $apiToken = config('services.fetchrExpress')['apiToken'];
            $client_address_id = config('services.fetchrExpress')['client_address_id'];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl . "order/history/".$shipping_number);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer ".$apiToken
            ));

            $response = curl_exec($ch);
            curl_close($ch);
            $api_content = json_decode($response, true);

            if(isset($api_content['tracking_information'])){
                $json['tracking']['shipping_number'] = $shipping_number;
                foreach ($api_content['tracking_information'] as $tracking) {
                    $json['tracking']['details'][] = array(
                        'date'      => $tracking['status_date_local'],
                        'status'    => $tracking['status_description'],
                    );
                }
                $json['msg'] = 'success';
            }
        }else if($provider['name'] == 'Jeebly' && $provider['is_active']){
            $json['tracking'] = array();

            $accountNumber = config('services.jeebly')['clientCode'];
            $apiUrl = config('services.jeebly')['url'];
            $apiKey = config('services.jeebly')['apiKey'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $apiUrl . "track?reference_number=" . $shipping_number,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_POSTFIELDS => "",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "api-key: " . $apiKey,
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                $json['msg'] = $err;
            } else {
                $api_content = json_decode($response, true);

                if(isset($api_content['events'])){
                    $json['tracking']['shipping_number'] = $shipping_number;
                    foreach ($api_content['events'] as $tracking) {
                        $json['tracking']['details'][] = array(
                            'date'      => $tracking['event_time'],
                            'status'    => strtoupper($tracking['type']),
                        );
                    }
                    $json['msg'] = 'success';
                }
            }
        }else{
            $json['msg'] = 'Only Track TFM Express Shipping Details!';
            $json['tracking'] = array();
        }
        return response()->json(array('msg' => $json['msg'], 'tracking' => $json['tracking']));
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
    protected function get_product_image($product_id = '', $width = 0, $height = 0){
        $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();

        if($product_image){
            if(file_exists($this->website_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
                $ToolImage = new ToolImage();
                return $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $product_image->image, $width, $height);
            }else{
                return $this->opencart_image_url . $product_image->image;
            }
        }else return $this->opencart_image_url . 'placeholder.png';
    }
}
