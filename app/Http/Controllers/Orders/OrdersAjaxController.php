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
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Http\Request;
use App\Platform\Helpers\ToolImage;
use Illuminate\Support\Facades\Input;
use Session;
use Validator;
use Illuminate\Support\Facades\Storage;
use URL;

class OrdersAjaxController extends Controller {

	const VIEW_DIR = 'orders';
	function __construct(){
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
}
