<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Orders\OrderedProductModel as DFOrderedProductModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel AS DFOrdersModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\DressFairOpenCart\Orders\OrderHistory AS DFOrderHistory;
use App\Models\DressFairOpenCart\Orders\OrderOptionsModel as DFOrderOptionsModel;
use App\Models\OpenCart\Orders\OrderHistory;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\OptionDescriptionModel AS DFOptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductOptionValueModel AS DFProductOptionValueModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsOrderProductModel;
use App\Models\Oms\OmsOrderReshipHistoryModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\ShippingProvidersModel;
use App\Models\OpenCart\Orders\OrderedProductModel;
use App\Models\OpenCart\Orders\OrderOptionsModel;
use App\Models\OpenCart\Orders\OrderStatusModel;
use App\Models\OpenCart\Products\ProductOptionValueModel;
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
				$order_data = OmsPlaceOrderModel::with(['omsOrder'=>function($q) use($store){
                    $q->where("store",$store);
                }])->where('order_id', $orderId)->where('store',$store)->first();
				if ( $order_data->omsOrder && $order_data->omsOrder->order_id > 0 ) {
                    //just change to cancel status
                    OmsOrdersModel::where("order_id",$orderId)->where('store',$store)->update(['oms_order_status'=>5]);  //5 for cancel order
                }else{
                    $omsOrder = new OmsOrdersModel();
                    $omsOrder->{OmsOrdersModel::FIELD_ORDER_ID} = $orderId;
                    $omsOrder->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_CANCEL;
                    $omsOrder->{OmsOrdersModel::UPDATED_AT} = \Carbon\Carbon::now();
                    $omsOrder->store = $store;
                    $omsOrder->save();
                }
                //check if order is in onhold
                $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId)->where('store',$store)->first();
                if( $check_on_hold ){
                   $this->availableInventoryQuantity($orderId,$store);
                }
                //oms activity log
                OmsActivityLogModel::newLog($orderId,10,$store); //10 is for cancel order
                return array('success' => true, 'data' => array(), 'error' => array('message' => ''));

			}
		} catch (\Exception $e) {
			return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
		}
	}
    public function availableInventoryQuantity($order_id,$store_id){
        $order_products = OmsOrderProductModel::with(['product','productOption'])->where('order_id',$order_id)->where("store_id",$store_id)->get();
        if( $order_products ){
            $order_quantity = 0;
            foreach( $order_products as $key => $order_product ){
                $order_quantity = $order_product->quantity;
                $check_exist = OmsInventoryOnholdQuantityModel::where("order_id",$order_product->order_id)->where('product_id',$order_product->product_id)
                ->where('option_id',$order_product->productOption->option_id)->where('option_value_id',$order_product->productOption->option_value_id)->where('store',$order_product->store_id)->first();
                if( $check_exist ){
                    OmsInventoryProductOptionModel::where(["product_id"=>$order_product->product_id,"product_option_id"=>$order_product->product_option_id])
                    ->update(['available_quantity'=>DB::raw("available_quantity + $order_quantity"),'onhold_quantity'=>DB::raw("onhold_quantity - $order_quantity")]);
                }
            }
        }
	}
    //add quantity for Business Arcae
    // public function availableInventoryQuantity($order_id){
	// 	$orderd_products = OrdersModel::with(['orderd_products'])->where(OrdersModel::FIELD_ORDER_ID, $order_id)->first();
	// 	if($orderd_products->orderd_products){
	// 		foreach ($orderd_products->orderd_products as $key => $product) {
	// 			$opencart_sku = ProductsModel::select('sku')->where('product_id', $product->product_id)->first();
	// 			$exists = OmsInventoryProductModel::select("*","option_name AS color","option_value AS size")->where('sku', $opencart_sku->sku)->first();
	// 			if($exists){
	// 				$product_id = $exists->product_id;

	// 				if(!empty($exists->size) && $exists->size > 0){
	// 					$total_quantity = 0;
	// 					foreach ($product->order_options as $key => $option) {
	// 						$option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
	// 						->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
	// 						->where('option_description.name', $option->name)
	// 						->where('ovd.name', $option->value)
	// 						->first();
	// 						$ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
	// 						if($option_data && $option_data->option_id != $ba_color_option_id){
	// 							$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
	// 							$total_quantity = $total_quantity + $product->quantity;
	// 							$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
    //                             $onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
	// 							if($packedExists){
	// 								$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
	// 								OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
	// 							}else if($onholdExists){
	// 								$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
	// 								OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
	// 							}
	// 						}
	// 					}
	// 				}else{
	// 					foreach ($product->order_options as $key => $option) {
	// 						$option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
	// 						->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
	// 						->where('option_description.name', $option->name)
	// 						->where('ovd.name', $option->value)
	// 						->first();
	// 						if($option_data){
	// 							$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromBa($option_data->option_id,$option_data->option_value_id);
	// 							$packedExists = OmsInventoryPackedQuantityModel::where('order_id', $order_id)->where('oms_product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
	// 							$onholdExists = OmsInventoryOnholdQuantityModel::where('order_id', $order_id)->where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->exists();
	// 							if($packedExists){
	// 								$decrement_query = 'IF (pack_quantity-' . $product->quantity . ' <= 0, 0, pack_quantity-' . $product->quantity . ')';
	// 								OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('pack_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
	// 							}else if( $onholdExists ){
	// 								$decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
	// 								OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $oms_option_det->oms_options_id)->where('option_value_id', $oms_option_det->oms_option_details_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
	// 							}
	// 						}
	// 					}
	// 				}
    //                 //from helper
    //                 updateSitesStock($opencart_sku->sku);
	// 			}
	// 		}
	// 	}
	// }
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
							$ba_color_option_id = OmsInventoryOptionModel::dfColorOptionId();
							if($option_data && $option_data->option_id != $ba_color_option_id){
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_data->option_id,$option_data->option_value_id);
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
								$oms_option_det = OmsInventoryOptionValueModel::OmsOptionsFromDf($option_data->option_id,$option_data->option_value_id);
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
        // dd( RequestFacad::all() );
        $orderIDs         = ( RequestFacad::get('orderIDs') && count(RequestFacad::get('orderIDs')) > 0 ) ? RequestFacad::get('orderIDs') : [RequestFacad::get('order_id')];
        $order_id  = RequestFacad::has('order_id') ? RequestFacad::get('order_id') : '';
        $orderIDs = array_unique($orderIDs);
        $awb_from_packing = 0;
        if( $order_id != "" && $order_id > 0 ){
        $awb_from_packing = 1;
        $get_courier_data = OmsOrdersModel::with(["assignedCourier"])->where(OmsOrdersModel::FIELD_ORDER_ID, $order_id)->first();
        // dd( $get_courier_data->toArray() );
        if($get_courier_data){
            $shippingProviders =  $get_courier_data->assignedCourier->name; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $get_courier_data->assignedCourier->shipping_provider_id;  // Shipping Provider ID
        }

        }else{
            $shippingProviderInput = explode('_', RequestFacad::get('shipping_providers'));
            $shippingProviders = $shippingProviderInput[1]; // Shipping provider Name // GetGive , MaraXpress etc
            $shippingProviderID = $shippingProviderInput[0]; // Shipping Provider ID
        }

        $assigned_courier_data = OmsOrdersModel::with(["assignedCourier"])->whereIn(OmsOrdersModel::FIELD_ORDER_ID, $orderIDs)->where("picklist_courier","!=",$shippingProviderID)->get();
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


			    $orders = OmsOrdersModel::select('oms_order_id','oms_order_status','last_shipped_with_provider',"order_id","store")->whereIn("order_id",$orderIDs)->get();


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
                    $omsOrderIDs[$omsOrder->order_id] = $omsOrder->oms_order_id;
                    $store = $omsOrder->store;

                    $order = OmsPlaceOrderModel::with(['orderProducts.product'])->where('order_id',$omsOrder->order_id)->first();

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
					$orderGolem->setOrderID($order->order_id);
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
					foreach ($order->orderProducts as $product) {
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
					if (!empty($airwayBillNumber[ShippingProvidersInterface::AIRWAYBILL_NUMBER])) {
						$omsUpdateStatus = OmsOrdersModel::find($omsOrderIDs[$orderID]);
						$awbTracking = new AirwayBillTrackingModel();
						// Store Oms ID
						$awbTracking->{AirwayBillTrackingModel::FIELD_OMS_ORDER_ID} = $omsOrderIDs[$orderID];
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
						$awbTracking->store = $omsUpdateStatus->store;
						$awbTracking->save(); // save the tracking information in table
						// Change the OMS Order STATUS TO AIRWAY_BILL_GENERATED
						$omsUpdateStatus->{OmsOrdersModel::FIELD_OMS_ORDER_STATUS} = OmsOrderStatusInterface::OMS_ORDER_STATUS_AIRWAY_BILL_GENERATED;
						$omsUpdateStatus->{OmsOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER} = $shippingProviderID;
						$omsUpdateStatus->save();

						OmsActivityLogModel::newLog($orderID,4,$omsUpdateStatus->store); //4 is for Generate Airwaybill order
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
  public function getOrderDetail($orderId = '', $amount = 0) {
		try
		{
			$orderId = (RequestFacad::get('orderId')) ? RequestFacad::get('orderId') : $orderId; // if ajax post or same controller call ref: line# 542
			$order = [];
			$omsOrder = OmsOrdersModel::where(OmsOrdersModel::FIELD_ORDER_ID, $orderId)->get();
            $shipping_name = "";
            if ( $omsOrder->count() > 0 ) {
                $store = $omsOrder[0]->store;
                if( $omsOrder[0]->oms_order_status == 5 ){
                    return "<script>alert('$orderId is cancelled.')</script>";
                }else if( $omsOrder[0]->picklist_print != 1 ){
                    return "<script>alert('Change picklist print for $orderId on packing Box.')</script>";
                }
                $order = OmsPlaceOrderModel::with(['orderProducts.product'])->where("order_id",$orderId)->first();
                // $order->oms_order_store = $omsOrder[0]->store;
				// $order->orderd_products = $this->getOrderProductWithImage($order->orderd_products,$store);

				// $omsOrderStatusMap = $omsOrder->mapWithKeys(function ($item) {
				// 	return [$item[OmsOrdersModel::FIELD_ORDER_ID] => $item[OmsOrdersModel::FIELD_OMS_ORDER_STATUS]];
				// });
				// $omsOrderStatus = $omsOrderStatusMap->all();
                //courier details
                $shipping_name =  OmsOrdersModel::shippingName($orderId,$store);
                if( $shipping_name != "" ){
                    $shipping_name = "<i><small>GNRT</small></i> - ".$shipping_name;
                }else{
                    $shipping_name = ShippingProvidersModel::select('name')->where("shipping_provider_id",$omsOrder[0]->picklist_courier)->first();
                    if($shipping_name){
                        $shipping_name = "<i><small>ASGN</small></i> - ".$shipping_name->name;
                    }
                }
			}
            // dd($order->toArray());
			return view(self::VIEW_DIR . ".order_detail_for_ship", ["order" => $order, "file_amount" => $amount,'shipping_name'=>$shipping_name]);
		} catch (\Exception $e) {
			return $e;
		}
	}
    protected function getOrderProductWithImage($orderd_products,$store){
        if( $store == 1 ){
            $website_image_source_path = $this->website_image_source_path;
            $website_image_source_url  = $this->website_image_source_url;
        }else if( $store == 2 ){
            $website_image_source_path = $this->df_website_image_source_path;
            $website_image_source_url  = $this->df_website_image_source_url;
        }
		foreach ($orderd_products as $orderd_product_key => &$orderd_products_value) {
			if(isset($orderd_products_value->product_details) && !empty($orderd_products_value->product_details)){
				$ToolImage = new ToolImage();
				if(file_exists($this->website_image_source_path . $orderd_products_value->product_details->image)){
					$orderd_products_value->product_details->image = $ToolImage->resize($website_image_source_path, $website_image_source_url, $orderd_products_value->product_details->image, 100, 100);
				}else if(strpos($orderd_products_value->product_details->image, "cache/catalog")){
					continue;
				}else{
					$orderd_products_value->product_details->image = $this->website_image_source_url . 'placeholder.png';
				}
			}
		}
		return $orderd_products;
	}
    public function printAwb() {
        // dd(RequestFacad::all());
		if(RequestFacad::get('submit') == 'awb' && RequestFacad::get('order_id')){
			$orderIds = RequestFacad::get('order_id');
            $orders = collect();
            if( is_array($orderIds) && count($orderIds) > 0 ){
                foreach( $orderIds as $order_id ){
                    $data = OmsOrdersModel::where("order_id",$order_id )->first();
                    if( $data->store == 1 ){
                        $order = OrdersModel::with(['status', 'orderd_products'])
                        ->where("order_id", $order_id)->first();
                    }else if( $data->store == 2 ){
                        $order = DFOrdersModel::with(['status', 'orderd_products'])
                        ->where("order_id", $order_id)->first();
                    }
                    $orders->push($order);
                }
            }
			// $order_data = OrdersModel::with(['status', 'orderd_products'])
			// ->whereIn(OrdersModel::FIELD_ORDER_ID, $orderIds)
			// ->get();
			// echo "<pre>"; print_r($order_data->toArray());
			$order_tracking = AirwayBillTrackingModel::whereIn('order_id', $orderIds)->get();
			$order_tracking_ids = $order_tracking->pluck(AirwayBillTrackingModel::FIELD_SHIPPING_PROVIDER_ID);
			// echo "<pre>"; print_r($order_tracking_ids->toArray()); die;
			$shipping_providers = ShippingProvidersModel::whereIn('shipping_provider_id', $order_tracking_ids)->get();

			return view(self::VIEW_DIR . ".awb_print", compact('orders','order_tracking','shipping_providers'));
		}else if(RequestFacad::get('submit') == 'picklist' && count(RequestFacad::all()) > 0){
			$ship_date = date('Y-m-d');
			$omsOrders = OmsOrdersModel::select('oms_orders.*', 'oc_order.order_status_id', 'sp.name')
			->join('airwaybill_tracking AS awb','awb.order_id','=','oms_orders.order_id')
			->join('shipping_providers as sp','sp.shipping_provider_id','=','awb.shipping_provider_id')
			->join(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_order AS oc_order'),'oms_orders.order_id','=','oc_order.order_id')
			->orderBy(OmsOrdersModel::UPDATED_AT, 'desc')
			// ->where('oms_orders.store',$this->store)
			->groupBy('oms_orders.order_id');
			if(RequestFacad::get('order_id')){
				$omsOrders = $omsOrders->where('oms_orders.order_id', RequestFacad::get('order_id'));
			}
			if(RequestFacad::get('order_status_id')){
				$omsOrders = $omsOrders->where('oc_order.order_status_id', RequestFacad::get('order_status_id'));
			}
			if (RequestFacad::get('shipping_provider_id')){
				$omsOrders = $omsOrders->where('oms_orders.'.OmsOrdersModel::FIELD_LAST_SHIPPED_WITH_PROVIDER, Input::get('shipping_provider_id'));
			}
			if (RequestFacad::get('date_from') && RequestFacad::get('date_to')){
				$date_from = Carbon::createFromFormat("Y-m-d", RequestFacad::get('date_from'))->toDateString();
				$date_to = Carbon::createFromFormat("Y-m-d",RequestFacad::get('date_to'))->toDateString();
				$omsOrders = $omsOrders->whereDate('awb.'.AirwayBillTrackingModel::CREATED_AT, '>=', $date_from)
				->whereDate('awb.'.AirwayBillTrackingModel::CREATED_AT, '<=', $date_to);
				if($date_from == $date_to){
					$ship_date = $date_from;
				}
			}
			if (RequestFacad::get('awb_number')){
				$omsOrders = $omsOrders->where('awb.'.AirwayBillTrackingModel::FIELD_AIRWAY_BILL_NUMBER, RequestFacad::get('awb_number'));
			}
			$omsOrders = $omsOrders->get()->toArray();
			$orderIds = array_map(function($value){
				return $value['order_id'];
			}, $omsOrders);

			$orders = OrdersModel::whereIn(OrdersModel::FIELD_ORDER_ID, $orderIds)
			->get();

			$order_details = array();
			foreach ($orders as $key => $value) {
				$awb = AirwayBillTrackingModel::select('airway_bill_number','shipping_provider_id')->where('order_id', $value->order_id)
				->where("store",$this->store)->first();
				$shipper = ShippingProvidersModel::where('shipping_provider_id', $awb->shipping_provider_id)->first();
				$qty = OrderedProductModel::select(DB::Raw('SUM(quantity) as total'))->where('order_id', $value->order_id)->first();

				$address = '';
				if($value->shipping_address_1){
					$address .= $value->shipping_address_1.($value->shipping_address_2 ? ", ".$value->shipping_address_2 : "");
				}
				if($value->shipping_area){
					$address .= ', ' . $value->shipping_area;
				}
				if($value->shipping_city){
					$address .= ', ' . $value->shipping_city;
				}

				$order_details[] = array(
					'order_id'  =>  $value->order_id,
					'awb'       =>  $awb->airway_bill_number,
					'name'      =>  $value->firstname . ' ' . $value->lastname,
					'mobile'    =>  $value->telephone ? : '-',
					'address'   =>  $address,
					'qty'       =>  $qty->total,
					'amount'    =>  $value->total
				);
			}
			$total_orders = count($order_details);
            $record_limit = 16;
            $page = ".ship_print";
            // dd($shipper->toArray());
            if( $shipper->shipment_print == 1  ){
                $page = ".ship_print_short";
                $record_limit = 70;
            }else if( $shipper->shipment_print == 2 ){
                $page = ".ship_print";
                $record_limit = 16;
            }
            $order_details = array_chunk($order_details, $record_limit);
            return view(self::VIEW_DIR . $page, ['orders' => $order_details, 'total_orders' => $total_orders, 'shipper' => $shipper, 'ship_date' => $ship_date]);
        }

		return redirect('/');
	}
    public function getOrderIdFromAirwayBill(Request $request){
        $airwaybill_number = $request->airwaybillno;
        $data = AirwayBillTrackingModel::with('shipping_provider')->where("airway_bill_number",$airwaybill_number)->orderBy("tracking_id","DESC")->first();
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
            return $this->getOrderDetail($data->order_id);
          }
        }
    }
    public function userOrderHistory(Request $request) {
        $old_input = RequestFacad::all();
        // dd($old_input);
        $data = OmsPlaceOrderModel::with(['omsOrder'])
            ->when(@$old_input['telephone'] != "",function($query) use ($old_input){
                return $query->where('mobile',$old_input['telephone']);
            })
            ->orderBy('order_id', 'DESC')->limit(6)->get();
	    $orderss=[];
	    if(!empty($data)){
			foreach ($data as $order) {
				// dd($order);
                $ordernum_url = URL::to('/orders')."?order_id=$order->order_id";
				$orderss[] = array(
					'order_id'   => "<a href='$ordernum_url' target='_blank'>$order->order_id</a>",
					'user'       => "-",
					'name'       => $order->firstname . ' ' . $order->lastname,
	                'address'    => $order->shipping_city_area.", ".$order->shipping_address_1.", ".$order->shipping_street_building.", ".$order->shipping_villa_flat.", ".$order->shipping_city,
					'status'     => "",
					'date_added' => date("d-m-Y G:i:s",strtotime($order->created_at)),
					'courier'    => ($order->name != "") ? $order->name : "-",
					'total'      => number_format($order->total_amount,2),
				);
			}
	    }else{
	      $orderss = "No data found.";
	    }

		return $orderss;
	}
    public function forwardOrderToQueueForAirwayBillGeneration() {
		// dd(RequestFacad::all());
		$courierId = RequestFacad::get('courier_id');
		// $courierId = null;
        $store = RequestFacad::get('oms_store');
		if($courierId) {
			try
			{
				if (null == RequestFacad::get('order_id')) {
					throw new \Exception("Order Id is Empty");
				}
				$orderID = RequestFacad::get('order_id');

				$order_data = OmsPlaceOrderModel::with(['orderProducts','omsOrder'=>function($q) use($store){
                    $q->where('store',$store);
                }])->where("order_id", $orderID)->where("store",$store)->first();
				// echo "d<pre>"; print_r($omsOrderDetails); die;

				if ( $order_data->omsOrder && $order_data->omsOrder->order_id > 0 ) {
					throw new \Exception("Order Already Processed");
				}

				// If qty not available then dont go ahead
				$not_in_stock = false;
				$product_data = array();
				// echo "<pre>"; print_r($order_products->toArray());
				// foreach ($order_data->orderProducts as $order_product) {

				// }
				// if ($not_in_stock) {
				// 	$messageHtml = '';
				// 	if ($product_data) {
				// 		$messageHtml .= '<div class="text-danger">Order Qty Not in Stock!</div>';
				// 		$messageHtml .= '<div class="table-responsive">';
				// 		$messageHtml .= '<table class="table">';
				// 		$messageHtml .= '<thead>';
				// 		$messageHtml .= '<th class="text-center">Image</th>';
				// 		$messageHtml .= '<th class="text-center">Product Code</th>';
				// 		$messageHtml .= '</thead>';
				// 		foreach ($product_data as $key => $value) {
				// 			$image = $this->getProductImage($value['product_id'],$oms_store, 50, 50);
				// 			$messageHtml .= '<tr>';
				// 			$messageHtml .= '<td class="text-center"><img src="' . $image . '" width="50px"></td>';
				// 			$messageHtml .= '<td class="text-center">' . $value['model'] . '</td>';
				// 			$messageHtml .= '</tr>';
				// 		}
				// 		$messageHtml .= '</table>';
				// 		$messageHtml .= '</div>';
				// 	}
				// 	throw new \Exception($messageHtml);
				// }
					$omsOrder = new OmsOrdersModel();
					$omsOrder->oms_order_status = 0;
					$omsOrder->order_id         = $orderID;
					$omsOrder->last_shipped_with_provider = 0;
					$omsOrder->picklist_courier = $courierId;
					$omsOrder->store = $store;
					$omsOrder->save(); // Save the record and start processing of order.

					OmsActivityLogModel::newLog($orderID,2,$store); //2 is for pick list order
				return;
			} catch (\Exception $ex) {
				return $ex->getMessage();
			}
		}else {
			return response()->json([
				'status' => false,
				'courier' => 'no'
			]);
		}
	}
    public function deductQuantity($orderID,$oms_store = null) {
        if( $oms_store == 1 ){
		    $order = OrdersModel::with('orderd_products')->where(OrdersModel::FIELD_ORDER_ID, $orderID)->first();
        }else if( $oms_store == 2 ){
		    $order = DFOrdersModel::with('orderd_products')->where(OrdersModel::FIELD_ORDER_ID, $orderID)->first();
        }
		foreach ($order->orderd_products as $orderProduct) {
            if( $oms_store == 1 ){
			    $opencartProduct = ProductsModel::select('sku')->where('product_id', $orderProduct->product_id)->first();
            }else if( $oms_store == 2 ){
			    $opencartProduct = DFProductsModel::select('sku')->where('product_id', $orderProduct->product_id)->first();
            }
			$omsProduct = OmsInventoryProductModel::where('sku', $opencartProduct->sku)->first();
			if($omsProduct){
			    updateSitesStock($opencartProduct->sku);
			}else{
				// $orderProductOptoins = OrderOptionsModel::where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID, $orderProduct->{OrderOptionsModel::FIELD_ORDER_PRODUCT_ID})->get();

				// if ($orderProductOptoins->count()) {
				// 	foreach ($orderProductOptoins as $orderOption) {
				// 		$orderProductData = OrderedProductModel::select('product_id')->where(OrderedProductModel::FIELD_ORDER_PRODUCT_ID, $orderOption->order_product_id)->first();
				// 		$optionData = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
				// 		->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
				// 		->where('option_description.name', $orderOption->name)
				// 		->where('ovd.name', $orderOption->value)
				// 		->first();

				// 		$productOption = ProductOptionValueModel::where(ProductOptionValueModel::FIELD_PRODUCT_ID, $orderProductData->product_id)->where(ProductOptionValueModel::FIELD_OPTION_ID, $optionData->option_id)->where(ProductOptionValueModel::FIELD_OPTION_VALUE_ID, $optionData->option_value_id)->first();
				// 		if ($productOption && $productOption->subtract) {
				// 			$productOption->decrement('quantity', $orderProduct->quantity);
				// 			$product_total_quantity = ProductOptionValueModel::where(ProductOptionValueModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})
				// 			->where(ProductOptionValueModel::FIELD_SUBTRACT, 1)
				// 			->sum(ProductOptionValueModel::FIELD_QUANTITY);
				// 			ProductsModel::where(ProductsModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})
				// 			->update(array(ProductsModel::FIELD_QUANTITY => $product_total_quantity));
				// 		}
				// 	}
				// } else {
				// 	$product = ProductsModel::where(ProductsModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})->first();
				// 	$product->decrement('quantity', $orderProduct->quantity);
				// }
			}
		}
	}
    protected function getProductImage($product_id = '',$store, $width = 0, $height = 0){
        if( $store == 1){
            $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();
            $website_image_source_path = $this->website_image_source_path;
            $website_image_source_url  = $this->website_image_source_url;
        }else if( $store == 2 ){
            $product_image = DFProductsModel::select('image')->where('product_id', $product_id)->first();
            $website_image_source_path = $this->df_website_image_source_path;
            $website_image_source_url  = $this->df_website_image_source_url;
        }

        if($product_image){
            if(file_exists($website_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
                $ToolImage = new ToolImage();
                return $ToolImage->resize($website_image_source_path, $website_image_source_url, $product_image->image, $width, $height);
            }else{
                return $this->opencart_image_url . $product_image->image;
            }
        }else return $this->opencart_image_url . 'placeholder.png';
    }
    public static function addQuantity($orderID,$oms_store = null) {
        if( $oms_store == 1 ){
		    $order = OrdersModel::with('orderd_products')->where(OrdersModel::FIELD_ORDER_ID, $orderID)->first();
        }else if( $oms_store == 2 ){
		    $order = DFOrdersModel::with('orderd_products')->where(OrdersModel::FIELD_ORDER_ID, $orderID)->first();
        }
		foreach ($order->orderd_products as $orderProduct) {
            if( $oms_store == 1 ){
			    $opencartProduct = ProductsModel::select('sku')->where('product_id', $orderProduct->product_id)->first();
            }else if( $oms_store == 2 ){
			    $opencartProduct = DFProductsModel::select('sku')->where('product_id', $orderProduct->product_id)->first();
            }
			$omsProduct = OmsInventoryProductModel::where('sku', $opencartProduct->sku)->first();
			if($omsProduct){
				//from helper
                updateSitesStock($opencartProduct->sku);
			}
            // else{
			// 	$orderProductOptoins = OrderOptionsModel::where(OrderOptionsModel::FIELD_ORDER_PRODUCT_ID, $orderProduct->{OrderOptionsModel::FIELD_ORDER_PRODUCT_ID})->get();
			// 	if ($orderProductOptoins->count()) {
			// 		foreach ($orderProductOptoins as $orderOption) {
			// 			$orderProductData = OrderedProductModel::select('product_id')->where(OrderedProductModel::FIELD_ORDER_PRODUCT_ID, $orderOption->order_product_id)->first();
			// 			$orderOptionData = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
			// 			->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
			// 			->where('option_description.name', $orderOption->name)
			// 			->where('ovd.name', $orderOption->value)
			// 			->first();

			// 			$productOption = ProductOptionValueModel::where(ProductOptionValueModel::FIELD_PRODUCT_ID, $orderProductData->product_id)->where(ProductOptionValueModel::FIELD_OPTION_ID, $orderOptionData->option_id)->where(ProductOptionValueModel::FIELD_OPTION_VALUE_ID, $orderOptionData->option_value_id)->first();

			// 			if ($productOption && $productOption->subtract) {
			// 				$productOption->increment('quantity', $orderProduct->quantity);
			// 				$product_total_quantity = ProductOptionValueModel::where(ProductOptionValueModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})
			// 				->where(ProductOptionValueModel::FIELD_SUBTRACT, 1)
			// 				->sum(ProductOptionValueModel::FIELD_QUANTITY);
			// 				ProductsModel::where(ProductsModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})
			// 				->update(array(ProductsModel::FIELD_QUANTITY => $product_total_quantity));
			// 			}
			// 		}
			// 	} else {
			// 		$product = ProductsModel::where(ProductsModel::FIELD_PRODUCT_ID, $orderProduct->{ProductOptionModel::FIELD_PRODUCT_ID})->first();
			// 		$product->increment('quantity', $orderProduct->quantity);
			// 	}
			// }
		}
	}
}
