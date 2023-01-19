<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsExchangeProductModel;
use App\Models\Oms\OmsExchangeTotalModel;
use App\Models\Oms\OmsPlaceExchangeModel;
use App\Models\Oms\Customer;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsCart;
use App\Models\Oms\OmsExchangeOrderAttachment;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsExchangeReturnProductModel;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsReturnOrdersModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\PaymentMethodModel;
use App\Models\Oms\ReturnAirwayBillTrackingModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\Oms\ShippingProvidersModel;
use App\Platform\Golem\OrderGolem;
use App\Platform\ShippingProviders\ShippingProvidersInterface;
use Illuminate\Support\Facades\Request AS Input;
use DB;
use Session;
use Carbon\Carbon;
class ExchangeOrdersAjaxController extends Controller
{
    const VIEW_DIR = 'exchange';
    const PER_PAGE = 20;
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_BAOMS_DATABASE = '';
    private $APP_OPENCART_URL = '';
    private $static_option_id = 0;
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $opencart_image_url = '';
    private $store = '';

    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_BAOMS_DATABASE = env('DB_BAOMS_DATABASE');
        $this->APP_OPENCART_URL = env('APP_OPENCART_URL');
        $this->static_option_id = OmsSettingsModel::get('product_option','color');
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =  isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : ''.'://'. $_SERVER["HTTP_HOST"] .'/image/';
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }
    public function forwardOrderToQueueForAirwayBillGeneration() {
		// dd(RequestFacad::all());
		$courierId = Input::get('courier_id');
		// $courierId = null;
        $store = Input::get('oms_store');
		// if($courierId) {
			try
			{
				if (null == Input::get('order_id')) {
					throw new \Exception("Order Id is Empty");
				}
				$orderID = Input::get('order_id');

				$order_data = OmsPlaceExchangeModel::with(['exchangeProducts','omsExchange'=>function($q) use($store){
                    $q->where('store',$store);
                }])->where("order_id", $orderID)->where("store",$store)->first();
				// echo "d<pre>"; print_r($omsOrderDetails); die;

				if ( $order_data->omsOrder && $order_data->omsExchange->order_id > 0 ) {
					throw new \Exception("Exchange Already Processed");
				}

				// If qty not available then dont go ahead
				$not_in_stock = false;
				$product_data = array();
					$omsOrder = new OmsExchangeOrdersModel();
					$omsOrder->oms_order_status = 0;
					$omsOrder->order_id         = $orderID;
					$omsOrder->last_shipped_with_provider = 0;
					$omsOrder->store = $store;
					$omsOrder->save(); // Save the record and start processing of order.

					OmsActivityLogModel::newLog($orderID,12,$store); //12 is for pick exchange
				return;
			} catch (\Exception $ex) {
				return $ex->getMessage();
			}
		// }else {
		// 	return response()->json([
		// 		'status' => false,
		// 		'courier' => 'no'
		// 	]);
		// }
	}
    public function cancel(){
        try{
            $orderId   = Input::get('order_id');
            $oms_store = Input::get('oms_store');
            $orderId_onhold = 0;
            if (strpos($orderId, '-1') !== false) {
              //order id contain -1
              $orderId_onhold = $orderId;
            }else{
              $orderId_onhold = $orderId."-1";
            }
            if ($orderId == ''){
                throw new \Exception("Please select an order to cancel");
            }else{
                // Change the OMS Order STATUS TO CANCEL
                $omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->where('store', $oms_store)->first();

                if ($omsOrder !== null){
                    //UPDATE OMS ORDER STATUS
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
                    $omsOrder->{OmsExchangeOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $omsOrder->save();
                }else {
                    $omsOrder = new OmsExchangeOrdersModel();
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_ORDER_ID} = $orderId;
                    $omsOrder->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
                    $omsOrder->{OmsExchangeOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $omsOrder->store = $oms_store;
                    $omsOrder->save();
                }

                OmsActivityLogModel::newLog($orderId,19,$oms_store); //19 is for cancel Exchange order

                //check if order is in onhold
                $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId_onhold)->where('store',$oms_store)->first();
                if( $check_on_hold ){
                    $this->availableInventoryQuantity($orderId,$oms_store);
                //   self::addQuantity($orderId);
                }

                // return array('success' => true, 'data' => array(), 'error' => array('message' => ''));
            }
        }
        catch (\Exception $e){
            return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
        }
    }
    public function getExchangeDetail($orderId = '', $amount = 0) {
		try
		{
			$orderId = (Input::get('orderId')) ? Input::get('orderId') : $orderId; // if ajax post or same controller call ref: line# 542
            $orderId = str_replace("-1","",$orderId);
			$order = [];

			$omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->get();
            $shipping_name = "";
            if ( $omsOrder->count() > 0 ) {
                $store = $omsOrder[0]->store;
                if( $omsOrder[0]->oms_order_status == 5 ){
                    return "<script>alert('$orderId is cancelled.')</script>";
                }
                // else if( $omsOrder[0]->picklist_print != 1 ){
                //     return "<script>alert('Change picklist print for $orderId on packing Box.')</script>";
                // }
                $order = OmsPlaceExchangeModel::with(['exchangeProducts.product'])->where("order_id",$orderId)->where('store',$store)->first();
                // $order->oms_order_store = $omsOrder[0]->store;
				// $order->orderd_products = $this->getOrderProductWithImage($order->orderd_products,$store);

				// $omsOrderStatusMap = $omsOrder->mapWithKeys(function ($item) {
				// 	return [$item[OmsOrdersModel::FIELD_ORDER_ID] => $item[OmsOrdersModel::FIELD_OMS_ORDER_STATUS]];
				// });
				// $omsOrderStatus = $omsOrderStatusMap->all();
                //courier details
                $shipping_name = "";
                // $shipping_name =  OmsOrdersModel::shippingName($orderId,$store);
                // if( $shipping_name != "" ){
                //     $shipping_name = "<i><small>GNRT</small></i> - ".$shipping_name;
                // }else{
                //     $shipping_name = ShippingProvidersModel::select('name')->where("shipping_provider_id",$omsOrder[0]->picklist_courier)->first();
                //     if($shipping_name){
                //         $shipping_name = "<i><small>ASGN</small></i> - ".$shipping_name->name;
                //     }
                // }
			}
            // dd($order->toArray());
			return view(self::VIEW_DIR . ".exchange_detail_for_ship", ["order" => $order, "file_amount" => $amount,'shipping_name'=>$shipping_name]);
		} catch (\Exception $e) {
			return $e;
		}
	}
    public function forwardForShipping() {
        // dd( RequestFacad::all() );
        $orderIDs         = ( Input::get('orderIDs') && count(Input::get('orderIDs')) > 0 ) ? Input::get('orderIDs') : [Input::get('order_id')];
        $order_id  = Input::has('order_id') ? Input::get('order_id') : '';
        $this->generateReturnCollection(20000034); die("after return collection");
        $orderIDs = array_unique($orderIDs);
        $awb_from_packing = 0;
        if( $order_id != "" && $order_id > 0 ){
        $awb_from_packing = 1;

        $get_courier_data = OmsExchangeOrdersModel::where("order_id", $order_id)->first();
        // dd( $get_courier_data->toArray() );
        if($get_courier_data){
            $shippingProviders =  $get_courier_data->assignedCourier->name; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $get_courier_data->assignedCourier->shipping_provider_id;  // Shipping Provider ID
        }

        }else{
            $shippingProviderInput = explode('_', Input::get('shipping_providers'));
            $shippingProviders = $shippingProviderInput[1]; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $shippingProviderInput[0]; // Shipping Provider ID
        }

        $assigned_courier_data = OmsExchangeOrdersModel::whereIn("order_id", $orderIDs)->get();
            //echo "<pre>"; print_r($orderIDs);
        // echo "<pre>"; print_r($assigned_courier_data);
        // if( $assigned_courier_data->count() > 0 ){
        //     $courier_msg = "";
        //     foreach( $assigned_courier_data as $key => $cvalue ){
        //         $courier_msg .= $cvalue->order_id." is Assigned to ".@$cvalue->assigned_courier->name.", can't generate to $shippingProviders<br>";
        //     }
        //     return response()->json(array(
        //                 'success' => false,
        //                 'data' => "<div class=\"alert bg-red\">{$courier_msg}</div>",
        //             ));
        // }
        //further processing
		Session::push('orderIdsForAWBGenerate', $orderIDs);
		// try
		// {
			// Value from Ajax form
			if (empty($shippingProviders)) {
				throw new \Exception("Please select Shipping Provider");
			}
			if (empty($orderIDs)) {
				throw new \Exception("Please select an Order to Generate AWB");
			}

			// echo "<pre>"; print_r($shippingProviderInput); die;

			if ( !empty($shippingProviders) ) {


			    $orders = OmsExchangeOrdersModel::select('oms_exchange_order_id','oms_order_status','last_shipped_with_provider',"order_id","store")->whereIn("order_id",$orderIDs)->get();


				$ordersGolemArray = [];
                $omsOrderIDs = [];
				foreach ($orders as $omsOrder) {
					// echo "<pre>"; print_r($order->toArray()); die;
					// $omsOrder = OmsOrdersModel::select('oms_order_status','last_shipped_with_provider',"order_id")->where(OmsOrdersModel::FIELD_ORDER_ID, $order->order_id)->first();
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
                    $omsOrderIDs[$omsOrder->order_id] = $omsOrder->oms_exchange_order_id;
                    $store = $omsOrder->store;

                    $order = OmsPlaceExchangeModel::with(['exchangeProducts.product'])->where('order_id',$omsOrder->order_id)->first();

					$shipping = new $shippingCompanyClass();

					// Initialize Order Golem to make a unified order object representation in order to send data to all shipping providers
					$orderGolem = new OrderGolem();
					// $orderGolem->setOrderID($order->{OrdersModel::FIELD_ORDER_ID});
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
					$orderGolem->setOrderID($order->order_id."-1");
					$name = $order->firstname . " " . $order->lastname;
					$orderGolem->setCustomerName($name);

					$orderGolem->setCustomerMobileNumber($order->mobile);
					$orderGolem->setOrderTotalAmount($order->total_amount);

					$shppingAddress = $order->shipping_address_1 . " " .
					$order->shipping_address_2;

					$orderGolem->setCustomerAddress($shppingAddress);
					$orderGolem->setCustomerCity($order->shipping_city);
					$orderGolem->setPaymentMethod($order->payment_method_id);
					$orderGolem->setCashOnDeliveryAmount($order->total_amount);
					$orderGolem->setSpecialInstructions($order->comment);
					$orderGolem->setCustomerEmail($order->email);
					$orderGolem->setCustomerArea($order->shipping_city_area);
					$orderGolem->setCustomerAlternateNumber($order->alternate_number);
					$productDesc = "";
					$qty = 0;
					foreach ($order->exchangeProducts as $product) {
						$productDesc .= "[" . $product->model;
						$productDesc .= " (QTY:{ $product->quantity })";
                        if( $product->product?->option_value > 0  ){
                            $productDesc .= " (" . $product->option_name . ":" . $product->option_value . ")";
                        }
                        $productDesc .= " (Color :" . $product->product->option_name . ")";

						$productDesc .= "] ";
						$qty = $qty + $product->quantity;
					}
					// echo $shippingProviders; die;

					$orderGolem->setTotalItemsQuantity($qty);
					$orderGolem->setGoodsDescription($productDesc);
					$orderGolem->setStore($store);
					$ordersGolemArray[] = $orderGolem;
				}
				// echo "<pre>"; print_r($orderGolem); die("on main page");
				$response = $shipping->forwardOrder($ordersGolemArray);
				// echo "<pre>"; print_r($omsOrderIDs); die("on main page");
				$shippingProviderResposne = [];
				foreach ($response as $orderID => $airwayBillNumber) {
                    $orderID = str_replace("-1", "", $orderID);
					if (!empty($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER])) {
						$omsUpdateStatus = OmsExchangeOrdersModel::find($omsOrderIDs[$orderID]);

						$awbTracking = new ExchangeAirwayBillTrackingModel();
						// Store Oms ID
						$awbTracking->{ExchangeAirwayBillTrackingModel::FIELD_OMS_ORDER_ID} = $omsOrderIDs[$orderID];
						// Store Opencart Order IDs
						$awbTracking->{ExchangeAirwayBillTrackingModel::FIELD_ORDER_ID} = $orderID;
						// Store Shipping Provider ID
						$awbTracking->{ExchangeAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID} = $shippingProviderID;
						$awbTracking->{ExchangeAirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER} = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
						$awbTracking->{ExchangeAirwayBillTrackingModel::FIELD_AIRWAY_BILL_CREATION_ATTEMPT} = 1;
                        if( isset( $airwayBillNumber['pdf_print_link'] ) ){
                                    $awbTracking->pdf_print_link = $airwayBillNumber['pdf_print_link'];
                        }
                        if( isset( $airwayBillNumber['sortingCode'] ) ){
                                    $awbTracking->sortingCode = $airwayBillNumber['sortingCode'];
                        }
						$awbTracking->store = $omsUpdateStatus->store;
						$awbTracking->save(); // save the tracking information in table
						// Change the OMS Order STATUS TO AIRWAY_BILL_GENERATED
						$omsUpdateStatus->{OmsExchangeOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED;
						$omsUpdateStatus->{OmsExchangeOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER} = $shippingProviderID;
						$omsUpdateStatus->save();

						OmsActivityLogModel::newLog($orderID,15,$omsUpdateStatus->store); //15 is for Exchange Generate Airwaybill order
						$shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
					} else {
						$shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::MESSAGE_FROM_PROVIDER];
					}
				}
			} else {
				throw new \Exception("Please select the status to update after airwaybill Generation");
			}
		// } catch (\Exception $ex) {
		// 	return response()->json(array(
		// 		'success' => false,
		// 		'data' => "<div class=\"alert bg-red\">{$ex->getMessage()}</div>",
		// 	));
		// }
		// dd($shippingProviderResposne);

		return response()->json(array(
			'success' => true,
			'data' => view(self::VIEW_DIR . ".shipping_providers_response", ["response" => $shippingProviderResposne])->render(),
		));
	}
    public function generateReturnCollection($order_id){
             $order_id = str_replace("-1","",$order_id);
        // try
        // {
              if ( $order_id == "" ) {
                throw new \Exception("Order id is empty.");
              }
              $order = OmsPlaceOrderModel::with(['omsOrder'])->where("order_id", $order_id)->first();
              $store_id = $order->store;
                $shippingProviderInput = explode('_', Input::get('shipping_providers'));
                $shippingProviders = $shippingProviderInput[1]; // Shipping provider Name // GetGive , MaraXpress etc
                $shippingProviderID = $shippingProviderInput[0]; // Shipping Provider ID

              $shippingCompanyClass = "\\App\\Platform\\ShippingProviders\\" . $shippingProviders;
              if (!class_exists($shippingCompanyClass)) {
                throw new \Exception("Shipping Provider Class {$shippingCompanyClass} does not exist");
              }

              $shipping = new $shippingCompanyClass();

              // Initialize Order Golem to make a unified order object representation in order to send data to all shipping providers
              $orderGolem = new OrderGolem();
              $orderGolem->setOrderID($order->order_id."-2");
              $name = $order->firstname . " " . $order->lastname;
              $orderGolem->setCustomerName($name);

              $orderGolem->setCustomerMobileNumber($order->mobile);
              $orderGolem->setOrderTotalAmount(0);

              $shppingAddress = $order->shipping_address_1 . " " .
					$order->shipping_address_2;

              $orderGolem->setCustomerAddress($shppingAddress);
              $orderGolem->setCustomerCity($order->shipping_city);
              $orderGolem->setPaymentMethod($order->payment_method_id);
              $orderGolem->setCashOnDeliveryAmount(0);
              $orderGolem->setSpecialInstructions($order->comment);
              $orderGolem->setCustomerEmail($order->email);
              $orderGolem->setCustomerArea($order->shipping_city_area);
              $productDesc = "";
              $qty = 0;

              $return_products = OmsExchangeReturnProductModel::with(['product','productOption'])->where('order_id',$order->order_id)->where('store_id',$store_id)->get();
              foreach ($return_products as $product){

                  $productDesc .= "[";
                  $productDesc .= $product->sku;
                  $productDesc .= " (QTY:{$product->quantity})";
                 $productDesc .= "(".$product->option_name.":".$product->option_value .")";
                  $productDesc .= "] ";
                  $qty = $qty + $product->quantity;
              }
              // echo $shippingProviders; die;

              $orderGolem->setTotalItemsQuantity($qty);
              $orderGolem->setGoodsDescription($productDesc);
              $orderGolem->setStore($this->store);
              $ordersGolemArray[] = $orderGolem;
             //   echo "<pre>"; print_r($ordersGolemArray);die;
            $response = $shipping->forwardOrder($ordersGolemArray);
            //   echo "<pre>"; print_r($response); die("on main page");
            $shippingProviderResposne = [];
            foreach ($response as $orderID => $airwayBillNumber) {
              $orderID = str_replace("-2", "", $orderID);
              if (!empty($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER])) {
                $awbTracking = new ReturnAirwayBillTrackingModel();
                // Store Oms ID
                $awbTracking->oms_order_id = $order->omsOrder->oms_order_id;
                // Store Opencart Order IDs
                $awbTracking->order_id = $orderID;
                // Store Shipping Provider ID
                $awbTracking->shipping_provider_id = $shippingProviderID;
                $awbTracking->airway_bill_number = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
                $awbTracking->airway_bill_creation_attempt = 1;
                $awbTracking->store = $order->store;
                $awbTracking->save(); // save the tracking information in table
                OmsActivityLogModel::newLog($order_id,14,$this->store); //14 for Generate AWB For Return
                //entry in oms_return_order table

                if(!OmsReturnOrdersModel::where('order_id',$orderID)->where("store",$store_id)->exists()){
                  $omsUpdateStatus = new OmsReturnOrdersModel();
                  $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_ORDER_ID} = $orderID;
                  $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_OMS_ORDER_STATUS} = 2; //2 for airway bill generated
                  $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER} = $shippingProviderID;
                  $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_CREATED_AT} = Carbon::now();
                  $omsUpdateStatus->{OmsReturnOrdersModel::FIELD_UPDATED_AT} = Carbon::now();
                  $omsUpdateStatus->store = $store_id;
                  $omsUpdateStatus->save();
                }
                $shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER];
              } else {
                $shippingProviderResposne[$orderID] = $airwayBillNumber[ShippingProvidersInterface::MESSAGE_FROM_PROVIDER];
              }
            }
        // } catch (\Exception $ex) {
        //   // return response()->json(array(
        //   //   'success' => false,
        //   //   'data' => "<div class=\"alert bg-red\">{$ex->getMessage()}</div>",
        //   // ));
        // }
      }
    public function printAwb() {
        // dd(RequestFacad::all());
		if(Input::get('submit') == 'awb' && Input::get('order_id')){
			$orderIds = Input::get('order_id');
            $orders = collect();
            if( is_array($orderIds) && count($orderIds) > 0 ){
                foreach( $orderIds as $order_id ){
                    $order = OmsPlaceExchangeModel::with(['exchangeProducts.product'])->where("order_id",$order_id)->first();
                    $orders->push($order);
                }
            }
			// $order_data = OrdersModel::with(['status', 'orderd_products'])
			// ->whereIn(OrdersModel::FIELD_ORDER_ID, $orderIds)
			// ->get();
			// echo "<pre>"; print_r($order_data->toArray());

			$order_tracking = ExchangeAirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
			$order_tracking_ids = $order_tracking->pluck(ExchangeAirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
			// echo "<pre>"; print_r($order_tracking_ids->toArray()); die;
			$shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

			return view(self::VIEW_DIR . ".awb_print", compact('orders','order_tracking','shipping_providers'));
		}

		return redirect('/');
	}
    public function getExchangeIdFromAirwayBill(Request $request){
        $airwaybill_number = $request->airwaybillno;

        $data = ExchangeAirwayBillTrackingModel::with('shipping_provider')->where("airway_bill_number",$airwaybill_number)->orderBy("tracking_id","DESC")->first();
        // dd($data->toArray());
        if( $data ){
          $pickup_start_time = ($data->shipping_provider) ? $data->shipping_provider->pickup_start_time : "";
          $pickup_end_time   = ($data->shipping_provider) ? $data->shipping_provider->pickup_end_time : "";
          $courier_name   = ($data->shipping_provider) ? $data->shipping_provider->name : "";
          if( time() < strtotime($pickup_start_time) && time() < strtotime($pickup_end_time) ){
            $formatted_from = date('h:i A',strtotime($pickup_start_time));
            $formatted_to = date('h:i A',strtotime($pickup_end_time));
            return "<script>alert('$courier_name pickup time is from  $formatted_from to $formatted_to.')</script>";
          }else{
            return $this->getExchangeDetail($data->order_id);
          }
        }
    }
    public function cancelQuantity(){
        $this->availableInventoryQuantity(1115552,1);
    }
    public function availableInventoryQuantity($order_id,$store_id){
        $order_id_dash = $order_id."-1";
        $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_id_dash)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( $check_exist ){
                    OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                    ->update(['available_quantity'=>DB::raw("available_quantity + $order_quantity"),'onhold_quantity'=>DB::raw("onhold_quantity - $order_quantity")]);
                }
            }
        }
	}
    //place Exchange code start=========================
    public function addToCart(Request $request){
        // dd($request->all());
            // echo session()->getId();
        $server_session_id = session()->getId();
        $product_id        = $request->product_id;
        $product_option_id = $request->product_option_id;
        $product_sku       = $request->product_sku;
        $product_name      = $request->product_name;
        $product_image      = $request->product_image;
        $product_color      = $request->product_color;
        $product_quantity  = $request->product_quantity;
        $product_price     = $request->product_price;
        $store = $request->store;
        $data = OmsCart::where("session_id",$server_session_id)->where("product_id",$product_id)->where("product_option_id",$product_option_id)->first();
        if($data){
            $request_quantity = ($data->product_quantity + $product_quantity);
            $availabe_quantity = $this->checkAvailableQuantity($data->product_id,$data->product_option_id);
            if( $request_quantity > $availabe_quantity   ){
                return response()->json(['status'=>0,"msg"=>"Quantity not in stock, Available quantity is <strong>$availabe_quantity</strong>"]);
            }
            $data->product_quantity = $request_quantity;
            $data->product_price = $product_price;
            $create_update_cart = $data->save();
        }else{
            $create_update_cart = OmsCart::create([
                "store_id"         => $store,
                "session_id"       =>$server_session_id,
                "product_id"       =>$product_id,
                "product_option_id"=>$product_option_id,
                "product_sku"      =>$product_sku,
                "product_name"     =>$product_name,
                "product_image"    =>$product_image,
                "product_color"    =>$product_color,
                "product_quantity" =>$product_quantity,
                "product_price"    =>$product_price,
                "is_exchange"      => 1
            ]);
        }
        if( $create_update_cart ){
            return response()->json(['status'=>1,"msg"=>"Item addedd to cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error while adding to cart."]);
        }
    }
    public function getCart(Request $request){
        $store   = $request->store;
        $total_exchange_amount = $request->total_exchange_amount;
        $server_session_id = session()->getId();
        $data = OmsCart::with('cartProductSize')->where("session_id",$server_session_id)->where("store_id",$store)->where('is_exchange',1)->get();
        // dd($data->toArray());
        return view('placeExchange.cartview',compact('data','total_exchange_amount'));
    }
    public function updateCart(Request $request){
        $cart_id  = $request->cart_id;
        $quantity = $request->quantity;

        $data = OmsCart::where("id",$cart_id)->first();
        $availabe_quantity = 0;
        if($data){
            $availabe_quantity = $this->checkAvailableQuantity($data->product_id,$data->product_option_id);
        }
        if( $quantity > $availabe_quantity   ){
            return response()->json(['status'=>0,"msg"=>"Quantity not in stock, Available quantity is <strong>$availabe_quantity</strong>"]);
        }

        $data = OmsCart::where("id",$cart_id)->update(['product_quantity'=>$quantity]);
        if($data){
            return response()->json(['status'=>1,"msg"=>"Item updated in cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error, cart not updated"]);
        }
    }
    private function checkAvailableQuantity($product_id,$product_option_id){
        $data = OmsInventoryProductOptionModel::where("product_id",$product_id)->where("product_option_id",$product_option_id)->first();
        if($data){
            return $data->available_quantity;
        }else{
            return 0;
        }
    }
    public function paymentShipping(Request $request){
        // dd($request->all());
        $store   = $request->store_id;
        $sub_dir = $store == 1 ? "ba" : "df";
        $shipping_methods = ShippingMethodModel::where("store_id",$store)->get();
        $payment_methods  = PaymentMethodModel::where('status',1)->get();
        $e_wallet_balance = 0;
        $shipping_method     = session('exchange_shipping_method');
        $payment_method      = session('exchange_payment_method');
        $totals = $this->formatTotal($store);

        // dd($totals);
        return view('placeExchange.paymentshippingview',compact('shipping_methods','payment_methods','e_wallet_balance','totals','shipping_method','payment_method'));
      }
    public function removeCart(Request $request){
        $cart_id = $request->cart_id;
        $data = OmsCart::destroy($cart_id);
        if($data){
            return response()->json(['status'=>1,"msg"=>"Item deleted from cart successfully."]);
        }else{
            return response()->json(['status'=>0,"msg"=>"Error."]);
        }
    }
    public function setPaymentMethod(Request $request){
        session()->forget('exchange_payment_method');
        $store_id = $request->store_id;
        $request_payment_method = $request->payment_method;
        $data = PaymentMethodModel::select('id','name','fee','fee_label')->where("id",$request_payment_method)->first();
        if( $data ){
            session()->put('exchange_payment_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function setShippingMethod(Request $request){
        session()->forget('exchange_shipping_method');
        $store_id = $request->store_id;
        $shipping_method = $request->shipping_method;
        $data = ShippingMethodModel::select('id','name','amount')->where("store_id",$store_id)->where('id',$shipping_method)->first();
        if( $data ){
            session()->put('exchange_shipping_method', $data->toArray());
        }else{
            echo "no data found, in table.";
        }
    }
    public function confirm(Request $request){
        // dd($request->all());
        $return_products_quantity = $request->quantity;
        $return_products_amount   = $request->amount;
        // echo count($return_products_quantity);
        // dd($request->all());
        $store_id = $request->store_id;
        $comment  = $request->comment;
        $order_id = $request->order_id;
        $google_map_link     = $request->google_map_link;
        $alternate_number    = $request->alternate_number;
        $shipping_method     = session('exchange_shipping_method');
        $payment_method      = session('exchange_payment_method');
        $server_session_id   = session()->getId();
        $cart_data = OmsCart::with(['product','productOption.option','productOption.optionVal'])->where("session_id",$server_session_id)->where('is_exchange',1)->where('store_id',$store_id)->get();
        // dd($cart_data->toArray()); die("test");
        if( $store_id < 1 ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"No store selected, or invalid store ID."]);
        }
        if( $cart_data->count() == 0 ){
          return response()->json(['status'=>false,"data"=>'','msg'=>"Your cart is empty"]);
        }
        if( $cart_data->count() > 0 ){
            if( $cart_data[0]->customer_id < 1 ){
                return response()->json(['status'=>false,"data"=>'','msg'=>"Customer details not set in cart"]);
            }
            $customer_data = Customer::with(['defaultAddress.country','defaultAddress.city','defaultAddress.area'])->where('id',$cart_data[0]->customer_id)->first();
        }
        if( !$payment_method ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Payment method missing"]);
        }
        if( !$shipping_method ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Shipping method missing"]);
        }
        if( !$customer_data ){
            return response()->json(['status'=>false,"data"=>'','msg'=>"Customer data no found."]);
        }
        // dd( $payment_method );
        // dd($customer_data->toArray());

        //first entry in store counter tabel
        DB::beginTransaction();
        try {

            //entry in total table
            $totals = $this->formatTotal($store_id);
            $order_total_amount = 0;
            if( is_array($totals) && count($totals) > 0 ){
                foreach($totals as $title=>$total){

                    $insert_order_tot           = new OmsExchangeTotalModel();
                    $insert_order_tot->store_id = $store_id;
                    $insert_order_tot->order_id = $order_id;
                    $insert_order_tot->title    = $title;
                    $insert_order_tot->value    = $total;
                    $insert_order_tot->order_id = $order_id;
                    if( $insert_order_tot->save() ){
                        $order_total_amount += $total;
                    }
                }

            }

            //entry in place order table
            $place_order = new OmsPlaceExchangeModel();
            $place_order->order_id    = $order_id;
            $place_order->user_id     = session('user_id');
            $place_order->store       = $store_id;
            $place_order->customer_id = $customer_data->id;
            $place_order->firstname   = $customer_data->firstname;
            $place_order->email       = $customer_data->email;
            $place_order->mobile      = $customer_data->mobile;
            $place_order->alternate_number      = $alternate_number;
            //payment address
            $place_order->payment_country_id     =  $customer_data->defaultAddress->country_id;
            $place_order->payment_city_id        =  $customer_data->defaultAddress->city_id;
            $place_order->payment_city_area_id   =  $customer_data->defaultAddress->area_id;
            $place_order->payment_firstname      =  $customer_data->defaultAddress->firstname;
            $place_order->payment_address_1      =  $customer_data->defaultAddress->address;
            $place_order->payment_street_building=  $customer_data->defaultAddress->street_building;
            $place_order->payment_villa_flat     =  $customer_data->defaultAddress->villa_flat;
            $place_order->payment_city           =  $customer_data->defaultAddress->city->name;
            $place_order->payment_city_area      =  $customer_data->defaultAddress->area->name;
            $place_order->payment_country        =  $customer_data->defaultAddress->country->name;
            // //shipping address
            $place_order->shipping_country_id     = $customer_data->defaultAddress->country_id;
            $place_order->shipping_city_id        = $customer_data->defaultAddress->city_id;
            $place_order->shipping_city_area_id   = $customer_data->defaultAddress->area_id;
            $place_order->shipping_firstname      = $customer_data->defaultAddress->firstname;
            $place_order->shipping_address_1      = $customer_data->defaultAddress->address;
            $place_order->shipping_street_building= $customer_data->defaultAddress->street_building;
            $place_order->shipping_villa_flat     = $customer_data->defaultAddress->villa_flat;
            $place_order->shipping_city           = $customer_data->defaultAddress->city->name;
            $place_order->shipping_city_area      = $customer_data->defaultAddress->area->name;
            $place_order->shipping_country        = $customer_data->defaultAddress->country->name;
            //payment method
            $place_order->payment_method_id       = $payment_method['id'];
            $place_order->payment_method_name     = $payment_method['name'];
            //general statuses
            $place_order->total_amount     = $order_total_amount;
            $place_order->comment          = $comment;
            $place_order->google_map_link  = $google_map_link ;
            $place_order->save();
            //insertion in oms_order_products table
            if($cart_data){
                foreach($cart_data as $key => $cart_item){
                    $cart_quantity = $cart_item->product_quantity;
                    $cart_option_name = $cart_item->productOption->option->option_name;
                    $cart_option_value = $cart_item->productOption->optionVal->value;
                    if( $cart_quantity > $cart_item->productOption->available_quantity ){
                        throw new \Exception("Desired quanity not available for ".$cart_item->product_sku." ".$cart_option_name.": ".$cart_option_value);
                    }
                    $item_total_price = ($cart_quantity * $cart_item->product_price);
                    $insert_order_products = new OmsExchangeProductModel();
                    $insert_order_products->order_id   = $order_id;
                    $insert_order_products->product_id = $cart_item->product_id;
                    $insert_order_products->store_id   = $cart_item->store_id;
                    $insert_order_products->name       = $cart_item->product_name;
                    $insert_order_products->sku        = $cart_item->product_sku;
                    $insert_order_products->quantity   = $cart_quantity;
                    $insert_order_products->price      = $cart_item->product_price;
                    $insert_order_products->total      = $item_total_price;
                    $insert_order_products->product_option_id = $cart_item->product_option_id;
                    $insert_order_products->option_name  = $cart_option_name;
                    $insert_order_products->option_value = $cart_option_value;
                    $insert_order_products->save();
                }
            }
            //return products entry
            if( is_array( $return_products_quantity ) && count($return_products_quantity) > 0 ){
                foreach( $return_products_quantity as $order_product_id => $quantity ){  //$order_product_id this contain primary key of oms_order_products
                    $order_product_data = OmsOrderProductModel::where("id",$order_product_id)->first();
                    // dd( $order_product_data->toArray() );
                    $insert_return_products = new OmsExchangeReturnProductModel();
                    $insert_return_products->order_id	 = $order_product_data->order_id;
                    $insert_return_products->store_id	 = $order_product_data->store_id;
                    $insert_return_products->product_id	 = $order_product_data->product_id;
                    $insert_return_products->name	     = $order_product_data->name;
                    $insert_return_products->sku	     = $order_product_data->sku;
                    $insert_return_products->total	     = ( $quantity * $order_product_data->price);
                    $insert_return_products->product_option_id = $order_product_data->product_option_id;
                    $insert_return_products->option_name = $order_product_data->option_name;
                    $insert_return_products->option_value= $order_product_data->option_value;
                    $insert_return_products->quantity	 = $quantity;  //quantity is comming from post, how many quantity customer want to return
                    $insert_return_products->save();
                }
            }
            $this->onHoldQuantity($order_id,$store_id);
            OmsActivityLogModel::newLog($order_id,11,$store_id); // 1 for place exchange
             DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            return response()->json(['status'=>false,"data"=>'','msg'=>$e->getMessage()]);
        }
        $this->clearSessionAfterOrder();
        return response()->json(['status'=>true,"data"=>'','msg'=>"order placed successfully."]);
      }
      public function onHoldQuantity($order_id,$store_id){
        $order_products = OmsExchangeProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( !$check_exist ){
                    $new_onhold = new OmsInventoryOnholdQuantityModel();
                    $new_onhold->order_id        = $order_product->order_id."-1";
                    $new_onhold->product_id      = $order_product->product_id;
                    $new_onhold->option_id       = $order_product->productOption->option_id;
                    $new_onhold->option_value_id = $order_product->productOption->option_value_id;
                    $new_onhold->quantity        = $order_quantity;
                    $new_onhold->store           = $order_product->store_id;
                    if( $new_onhold->save() ){
                        $decrement_query = 'IF (available_quantity-' . $order_quantity . ' <= 0, 0, available_quantity-' . $order_quantity . ')';
                        OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                        ->update(['available_quantity'=>DB::raw($decrement_query),'onhold_quantity'=>DB::raw("onhold_quantity + $order_quantity")]);
                    }
                }
            }
        }
        // dd($order_products->toArray());
      }
      private function formatTotal($store){
        $totals = [];
        $exchange_total = Input::get('total_exchange_amount');
        $cart_sub_total = OmsCart::getExchangeCartTotalAmount($store);
        $totals['Sub-Total'] = $cart_sub_total;
        //exchange start
        //shipping method
        $shipping_method     = session('exchange_shipping_method');
        if($shipping_method ){
            $totals[$shipping_method['name']] = $shipping_method['amount'];
        }
        //payment method
        $payment_method      = session('exchange_payment_method');
        if( $payment_method && $payment_method['fee'] > 0 ){
            $totals[$payment_method['fee_label']] = $payment_method['fee'];
        }
        //exchange start
        $totals['Exchange-Total'] = -$exchange_total;
        //Ewallet start
        $ewallet_amount = $exchange_total-$cart_sub_total;
        if( $ewallet_amount > 0 ){
            $totals['E-wallet Total'] = $ewallet_amount;
        }
        return $totals;
      }
      private function clearSessionAfterOrder(){
        $server_session_id   = session()->getId();
        OmsCart::where('session_id',$server_session_id)->where('is_exchange',1)->delete();
        session()->forget('exchange_shipping_method');
        session()->forget('exchange_payment_method');
      }

}
