<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\DressFairOpenCart\Orders\OrderHistory AS DFOrderHistory;
use App\Models\OpenCart\Orders\OrderHistory;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\OptionDescriptionModel AS DFOptionDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsOrderReshipHistoryModel;
use App\Platform\Golem\OrderGolem;
use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS RequestFacad;
use App\Platform\Helpers\ToolImage;
use App\Platform\ShippingProviders\ShippingProvidersInterface;
use Illuminate\Support\Facades\Input;
use Session;
use Validator;
use Illuminate\Support\Facades\Storage;
use URL;

class OrdersAjaxController extends Controller {

	const VIEW_DIR = 'orders';
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    private $website_image_source_url =  '';
    private $website_image_source_path =  '';
    //for dressfair
    private $df_website_image_source_path =  '';
	private $df_website_image_source_url  =  '';
    //
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

    }
    public function activityDetails(Request $request){
		// echo $request->order_id;
		if($request->method()=="POST"){
			$activity_list = OmsActivityLogModel::with(['activity'=>function($q){
            $q->select('id','title')->get();
         },'user'=>function($query){
             $query->select('user_id','firstname','lastname')->get();
				 },'courier'=>function($query){
          $query->select('shipping_provider_id','name','auto_deliver')->get();
         }])->where('ref_id',$request->order_id)->where('store',$request->store)->get();
			// echo Response::json();
			// dd( $activity_list->toArray() );
			return response()->json( $activity_list );
		}
	}
    public function cancelOrder(Request $request) {
        // dd($request->all());
		try
		{
			$orderId = $request->order_id;
            $store   = $request->store;
			// dd($orderId);
			if ($orderId == '') {
				throw new \Exception("Please select an order to cancel");
			} else {
				// Change the OMS Order STATUS TO CANCEL
				$omsOrder = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $orderId)->where('store',$store)->first();
				// dd($omsOrder);
                if( $store == 1 ){
				    $openCartOrder = OrdersModel::findOrFail($orderId);
                }if( $store == 2 ){
				    $openCartOrder = DFOrdersModel::findOrFail($orderId);
                }
				if ($omsOrder == null || ($omsOrder && in_array($omsOrder->oms_order_status, array(OmsOrderStatusInterface::OMS_ORDER_STATUS_IN_QUEUE_PICKING_LIST, OmsOrderStatusInterface::OMS_ORDER_STATUS_PACKED)) )) {
					if($omsOrder !== null){
						//UPDATE OMS ORDER STATUS
						$omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
						$omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
						$omsOrder->save();
					}else{
						$omsOrder = new OmsOrdersModel();
						$omsOrder->{OmsOrdersModel::FIELD_ORDER_ID} = $orderId;
						$omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
						$omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
						$omsOrder->store = $store;
						$omsOrder->save();
					}

					//UPDATE OPENCART STATUS
					$openCartOrder->{OrdersModel::FIELD_DATE_MODIFIED} = \Carbon\Carbon::now();
					$openCartOrder->{OrdersModel::FIELD_ORDER_STATUS_ID} = OrdersModel::OPEN_CART_STATUS_CANCELED;
					$openCartOrder->online_approved = 1; //incase if order is reject from online tab it should now show in all orders as cancel order.
					$openCartOrder->save(); // update the order status

					//UPDATE OPENCART ORDER HISTORY
                    if( $store == 1 ){
                        $orderHistory = new OrderHistory();
                    }if( $store == 2 ){
                        $orderHistory = new DFOrderHistory();
                    }
					$orderHistory->{OrderHistory::FIELD_COMMENT} = "Order canceled from OMS";
					$orderHistory->{OrderHistory::FIELD_ORDER_ID} = $orderId;
					$orderHistory->{OrderHistory::FIELD_ORDER_STATUS_ID} = OrdersModel::OPEN_CART_STATUS_CANCELED;
					$orderHistory->{OrderHistory::FIELD_DATE_ADDED} = \Carbon\Carbon::now();
					$orderHistory->{OrderHistory::FIELD_NOTIFY} = OrderHistory::NOTIFY_CUSTOMER;
					$orderHistory->save();
                    //check if order is in onhold
                    $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId)->where('store',$store)->first();
                    if( $check_on_hold ){
                        if( $store == 1 ){
                            $this->availableInventoryQuantity($orderId);
                        }if( $store == 2 ){
                            $this->availableInventoryQuantityDF($orderId);
                        }
                        // self::addQuantity($orderId);
                    }
                    //oms activity log
			        OmsActivityLogModel::newLog($orderId,10,$store); //10 is for cancel order
					return array('success' => true, 'data' => array(), 'error' => array('message' => ''));
				} else {
					throw new \Exception("Order can't be canceled in this status");
				}
			}
		} catch (\Exception $e) {
			return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
		}
	}
    //add quantity for Business Arcae
    public function availableInventoryQuantity($order_id){
		$orderd_products = OrdersModel::with(['orderd_products'])->where(OrdersModel::FIELD_ORDER_ID, $order_id)->first();
		if($orderd_products->orderd_products){
			foreach ($orderd_products->orderd_products as $key => $product) {
				$opencart_sku = ProductsModel::select('sku')->where('product_id', $product->product_id)->first();
				$exists = OmsInventoryProductModel::select("*","option_name AS color","option_value AS size")->where('sku', $opencart_sku->sku)->first();
				if($exists){
					$product_id = $exists->product_id;

					if(!empty($exists->size) && $exists->size > 0){
						$total_quantity = 0;
						foreach ($product->order_options as $key => $option) {
							$option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
							->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
							->where('option_description.name', $option->name)
							->where('ovd.name', $option->value)
							->first();
							$ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
							if($option_data && $option_data->option_id != $ba_color_option_id){
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
								$total_quantity = $total_quantity + $product->quantity;
								$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
                                $onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								if($packedExists){
									$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}else if($onholdExists){
									$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}
							}
						}
					}else{
						foreach ($product->order_options as $key => $option) {
							$option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
							->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
							->where('option_description.name', $option->name)
							->where('ovd.name', $option->value)
							->first();
							if($option_data){
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
								$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								$onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								if($packedExists){
									$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}else if( $onholdExists ){
									$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}
							}
						}
					}
                    //from helper
                    updateSitesStock($opencart_sku->sku);
				}
			}
		}
	}
    //add quantity for Dressfair.
    public function availableInventoryQuantityDF($order_id){
		$orderd_products = DFOrdersModel::with(['orderd_products'])->where(OrdersModel::FIELD_ORDER_ID, $order_id)->first();
		if($orderd_products->orderd_products){
			foreach ($orderd_products->orderd_products as $key => $product) {
				$opencart_sku = DFProductsModel::select('sku')->where('product_id', $product->product_id)->first();
				$exists = OmsInventoryProductModel::select("*","option_name AS color","option_value AS size")->where('sku', $opencart_sku->sku)->first();
				if($exists){
					$product_id = $exists->product_id;
					if(!empty($exists->size) && $exists->size > 0){
						$total_quantity = 0;
						foreach ($product->order_options as $key => $option) {

							$option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
							->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
							->where('option_description.name', $option->name)
							->where('ovd.name', $option->value)
							->first();
							$ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
							if($option_data && $option_data->option_id != $ba_color_option_id){
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
								$total_quantity = $total_quantity + $product->quantity;
								$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
                                $onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								if($packedExists){
									$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}else if($onholdExists){
									$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}
							}
						}
					}else{
						foreach ($product->order_options as $key => $option) {
							$option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
							->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
							->where('option_description.name', $option->name)
							->where('ovd.name', $option->value)
							->first();
							if($option_data){
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
								$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								$onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
								if($packedExists){
									$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}else if( $onholdExists ){
									$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
									OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
								}
							}
						}
					}
                    //from helper
                    updateSitesStock($opencart_sku->sku);
				}
			}
		}
	}
    public function reship(Request $request){
		// dd($request->all());
		////return just history start
        $store = $request->store;
		if($request->history){
			$reship_comment = OmsOrderReshipHistoryModel::where('order_id',$request->order_id)->where("store",$store)->first();
			if(!empty($reship_comment)){
				echo json_encode($reship_comment->comment);
			}
			exit;
		}
		////return just history end
		$qrysts = OmsOrdersModel::where(['order_id'=>$request->order_id])->where("store",$store)->update(["reship"=>'-1']);

		if($qrysts){
			OmsOrderReshipHistoryModel::create(['order_id'=>$request->order_id,'comment'=>$request->comment,'store'=>$store]);
			OmsActivityLogModel::newLog($request->order_id,6,$store); //6 is for reship order
			echo json_encode(["status"=>$qrysts,"msg"=>"Reshipment request send to admin."]);
		}else{
			echo json_encode(["status"=>$qrysts,"msg"=>"Error,Reshipment request not send."]);
		}
	}
    public function forwardForShipping() {
        $orderIDs         = ( RequestFacad::get('orderIDs') && count(RequestFacad::get('orderIDs')) > 0 ) ? RequestFacad::get('orderIDs') : [RequestFacad::get('order_id')];
        $store     = RequestFacad::has('store') ? RequestFacad::get('store') : '';
        $order_id  = RequestFacad::has('order_id') ? RequestFacad::get('order_id') : '';
        $orderIDs = array_unique($orderIDs);
        $awb_from_packing = 0;
        if( $order_id != "" && $order_id > 0 ){
        $awb_from_packing = 1;
        $get_courier_data = OmsOrdersModel::with(["assigned_courier"])->where(OmsOrdersModel::FIELD_ORDER_ID, $order_id)->where("store",$store)->first();
        if($get_courier_data){
            $shippingProviders =  $get_courier_data->assigned_courier->name; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $get_courier_data->assigned_courier->shipping_provider_id;  // Shipping Provider ID
        }

        }else{
            $shippingProviderInput = explode('_', RequestFacad::get('shipping_providers'));
            $shippingProviders = $shippingProviderInput[1]; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $shippingProviderInput[0]; // Shipping Provider ID
        }

        $assigned_courier_data = OmsOrdersModel::with(["assigned_courier"])->whereIn(OmsOrdersModel::FIELD_ORDER_ID, $orderIDs)->where("picklist_courier","!=",$shippingProviderID)->get();
            //echo "<pre>"; print_r($orderIDs);
        // echo "<pre>"; print_r($assigned_courier_data);
        if( $assigned_courier_data->count() > 0 ){
        $courier_msg = "";
        foreach( $assigned_courier_data as $key => $cvalue ){
            $courier_msg .= $cvalue->order_id." is Assigned to ".@$cvalue->assigned_courier->name.", can't generate to $shippingProviders<br>";
        }
        return response()->json(array(
                    'success' => false,
                    'data' => "<div class=\"alert bg-red\">{$courier_msg}</div>",
                ));
        }
        //further processing
		Session::push('orderIdsForAWBGenerate', $orderIDs);
		try
		{
			$openCartOrderStatus = RequestFacad::get('open_cart_order_status') ? RequestFacad::get('open_cart_order_status') : 15; // Status to be updated in opencart
			// Value from Ajax form
			if (empty($shippingProviders)) {
				throw new \Exception("Please select Shipping Provider");
			}
			if (empty($orderIDs)) {
				throw new \Exception("Please select an Order to Generate AWB");
			}

			// echo "<pre>"; print_r($shippingProviderInput); die;

			if (!empty($openCartOrderStatus) && !empty($shippingProviders)) {
				// Get orders from OMS table where oms status is processing
				$omsOrders = OmsOrdersModel::whereIn(OmsOrdersModel::FIELD_ORDER_ID, $orderIDs)->get();
				// Map Opencart Order id to Oms order id
				$omsOrderIDtoOpencarOrderIDMap = $omsOrders->mapWithKeys(function ($item) {
					return [$item[OmsOrdersModel::FIELD_ORDER_ID] => $item[OmsOrdersModel::FIELD_OMS_ORDER_ID]];
				});

				$omsOrderIDtoOpencarOrderIDMap = $omsOrderIDtoOpencarOrderIDMap->toArray();
				// echo "<pre>"; print_r($omsOrderIDtoOpencarOrderIDMap); die;

				// Get Order Details from Opencart
				$orders = OrdersModel::with(['status', 'orderd_products'])->whereIn(OrdersModel::FIELD_ORDER_ID, $orderIDs)->get();
				// dd($orders->toArray());

				$ordersGolemArray = [];
				foreach ($orders as $order) {
					// echo "<pre>"; print_r($order->toArray()); die;
					$omsOrder = OmsOrdersModel::select('oms_order_status','last_shipped_with_provider',"order_id")->where(OmsOrdersModel::FIELD_ORDER_ID, $order->order_id)->where('store',$this->store)->first();
					if($omsOrder->oms_order_status != OmsOrderStatusInterface::OMS_ORDER_STATUS_PACKED){
						throw new \Exception("AWB only Generate in 'Packed' Status, $omsOrder->order_id");
					}

                    if( $omsOrder->last_shipped_with_provider == $shippingProviderID ){
                                    throw new \Exception("AWB already Generated for order # $omsOrder->order_id");
                    }

					$shippingCompanyClass = "\\App\\Platform\\ShippingProviders\\" . $shippingProviders;
					if (!class_exists($shippingCompanyClass)) {
						throw new \Exception("Shipping Provider Class {$shippingCompanyClass} does not exist");
					}


					$shipping = new $shippingCompanyClass();

					// Initialize Order Golem to make a unified order object representation in order to send data to all shipping providers
					$orderGolem = new OrderGolem();
					$orderGolem->setOrderID($order->{OrdersModel::FIELD_ORDER_ID});
					if ($shippingProviders === 'ShamilExpress') {
						if ($order->invoice_no != 0) {
							$orderGolem->setInvoiceNumber($order->{'invoice_prefix'} . $order->{'invoice_no'} . '-BA');
						} else {
							$data = file_get_contents($this->APP_OPENCART_URL . 'index.php?route=account/order/createinvoiceno&order_id=' . $order->order_id . '&type=order');
							$invoice_no = json_decode($data, true);
							$orderGolem->setInvoiceNumber($invoice_no['invoice_no'] . '-BA');
						}
						$orderGolem->setOrderDate($order->{'date_added'});
					} else if ($shippingProviders === 'LeopardsExpress') {
						$orderGolem->setCustomerCoutry($order->{'payment_country'});
						$orderGolem->setToCompany($order->{'payment_company'});
					} else if ($shippingProviders === 'FetchrExpress') {
						$orderGolem->setCustomerCoutry($order->{'payment_country'});
					} else if ($shippingProviders === 'Jeebly') {
						$orderGolem->setcustomerPincode($order->{'shipping_postcode'});
					}
					$orderGolem->setOrderID($order->{OrdersModel::FIELD_ORDER_ID});
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
					$orderGolem->setCustomerArea($order->{OrdersModel::FIELD_SHIPPING_AREA});
					$orderGolem->setCustomerAlternateNumber($order->alternate_number);
					$productDesc = "";
					$qty = 0;
					foreach ($order->orderd_products as $product) {
						$productDesc .= "[" . $product['model'];
						$productDesc .= " (QTY:{$product['quantity']})";
						if (count($product['order_options']) > 0) {
							foreach ($product['order_options'] as $option) {
								if ($product['order_product_id'] == $option['order_product_id']) {
									$productDesc .= " (" . $option['name'] . ":" . $option['value'] . ")";
								}
							}
						}
						$productDesc .= "] ";
						$qty = $qty + $product['quantity'];
					}
					// echo $shippingProviders; die;
					if ($shippingProviders === 'FetchrExpress' || $shippingProviders === 'Jeebly' ||  $shippingProviders === 'RisingStar') {
						$orderItems = array();
						foreach ($order->orderd_products as $product) {
							$productDesc = '';
							$productDesc .= "[" . $product['model'];
							$productDesc .= " (QTY:{$product['quantity']})";
							if (count($product['order_options']) > 0) {
								foreach ($product['order_options'] as $option) {
									if ($product['order_product_id'] == $option['order_product_id']) {
										$productDesc .= " (" . $option['name'] . ":" . $option['value'] . ")";
									}
								}
							}
							$productDesc .= "]";
							$orderItems[] = array(
								'description' => $productDesc,
								'sku' => $product['model'],
								'quantity' => $product['quantity'],
								'order_value_per_unit' => $product['price'],
								'weight' => $product->product_details->weight,
								'height' => $product->product_details->height,
								'length' => $product->product_details->length,
								'width' => $product->product_details->width,
							);
						}
						$orderGolem->setOrderItems($orderItems);
					}

					$orderGolem->setTotalItemsQuantity($qty);
					$orderGolem->setGoodsDescription($productDesc);
					$orderGolem->setStore($this->store);
					$ordersGolemArray[] = $orderGolem;
				}
				$response = $shipping->forwardOrder($ordersGolemArray);
				// echo "<pre>"; print_r($response); die("on main page");
				$shippingProviderResposne = [];
				foreach ($response as $orderID => $airwayBillNumber) {
					if (!empty($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER])) {
						$awbTracking = new AirwayBillTrackingModel();
						// Store Oms ID
						$awbTracking->{AirwayBillTrackingModel::FIELD_OMS_ORDER_ID} = $omsOrderIDtoOpencarOrderIDMap[$orderID];
						// Store Opencart Order IDs
						$awbTracking->{AirwayBillTrackingModel::FIELD_ORDER_ID} = $orderID;
						// Store Shipping Provider ID
						$awbTracking->{AirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID} = $shippingProviderID;
						$awbTracking->{AirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER} = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
						$awbTracking->{AirwayBillTrackingModel::FIELD_AIRWAY_BILL_CREATION_ATTEMPT} = 1;
                        if( isset( $airwayBillNumber['pdf_print_link'] ) ){
                                    $awbTracking->pdf_print_link = $airwayBillNumber['pdf_print_link'];
                        }
                        if( isset( $airwayBillNumber['sortingCode'] ) ){
                                    $awbTracking->sortingCode = $airwayBillNumber['sortingCode'];
                        }
						$awbTracking->store = $this->store;
						$awbTracking->save(); // save the tracking information in table
						// Change the OMS Order STATUS TO AIRWAY_BILL_GENERATED
						$omsUpdateStatus = OmsOrdersModel::find($omsOrderIDtoOpencarOrderIDMap[$orderID]);
						$omsUpdateStatus->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED;
						$omsUpdateStatus->{OmsOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER} = $shippingProviderID;
						$omsUpdateStatus->save();
						// Chnage the OpenCart Order Status to the status selected
						$openCartStatusUpdate = OrdersModel::find($orderID);
						$openCartStatusUpdate->{OrdersModel::FIELD_ORDER_STATUS_ID} = $openCartOrderStatus;
						$openCartStatusUpdate->{OrdersModel::FIELD_DATE_MODIFIED} = \Carbon\Carbon::now();
						$openCartStatusUpdate->save();
						// Store the Order History in Order history table

						$orderHistory = new OrderHistory();
						$orderHistory->{OrderHistory::FIELD_COMMENT} = "Tracking Link: " . $shippingCompanyClass::getTrackingUrl($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER]);
						$orderHistory->{OrderHistory::FIELD_ORDER_ID} = $orderID;
						$orderHistory->{OrderHistory::FIELD_ORDER_STATUS_ID} = $openCartOrderStatus;
						$orderHistory->{OrderHistory::FIELD_DATE_ADDED} = \Carbon\Carbon::now();
						$orderHistory->{OrderHistory::FIELD_NOTIFY} = OrderHistory::NOTIFY_CUSTOMER;
						$orderHistory->save();
						OmsActivityLogModel::newLog($orderID,4,$this->store); //4 is for Generate Airwaybill order
						$shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
					} else {
						$shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::MESSAGE_FROM_PROVIDER];
					}
				}
			} else {
				throw new \Exception("Please select the status to update after airwaybill Generation");
			}
		} catch (\Exception $ex) {
			return response()->json(array(
				'success' => false,
				'data' => "<div class=\"alert bg-red\">{$ex->getMessage()}</div>",
			));
		}
		// dd($shippingProviderResposne);

		return response()->json(array(
			'success' => true,
			'data' => view(self::VIEW_DIR . ".shipping_providers_response", ["response" => $shippingProviderResposne])->render(),
		));
	}
}
