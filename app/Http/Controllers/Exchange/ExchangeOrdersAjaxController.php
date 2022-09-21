<?php
namespace App\Http\Controllers\Exchange;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrderHistoryModel as DFExchangeOrderHistoryModel;
use App\Models\DressFairOpenCart\ExchangeOrders\ExchangeOrdersModel as DFExchangeOrdersModel;
use App\Models\DressFairOpenCart\Products\OptionDescriptionModel as DFOptionDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DFProductsModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\OmsExchangeOrdersModel;
use App\Models\Oms\OmsOrderStatusInterface;
use App\Models\Oms\OmsSettingsModel;
use App\Models\OpenCart\ExchangeOrders\ExchangeOrderHistoryModel;
use App\Models\OpenCart\ExchangeOrders\ExchangeOrdersModel;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use Illuminate\Support\Facades\Request AS Input;
use DB;
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
    public function cancelOrder(){
        try{
            $orderId   = Input::get('order_id');
            $oms_store = Input::get('oms_store');
            $exchange_order_id = Input::get('exchange_order_id');
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
                // $exchange_order_id = ExchangeOrdersModel::select(ExchangeOrdersModel::FIELD_EXCHANGE_ORDER_ID)->where(ExchangeOrdersModel::FIELD_ORDER_ID,$orderId)->first()->exchange_order_id;
                // Change the OMS Order STATUS TO CANCEL
                $omsOrder = OmsExchangeOrdersModel::where("order_id", $orderId)->where('store', $oms_store)->first();
                if( $oms_store ==1 ){
                    $openCartOrder = ExchangeOrdersModel::findOrFail($exchange_order_id);
                }else if( $oms_store == 2 ){
                    $openCartOrder = ExchangeOrdersModel::findOrFail($exchange_order_id);
                }
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

                //UPDATE OPENCART STATUS
                if (in_array($openCartOrder->{ExchangeOrdersModel::FIELD_ORDER_STATUS_ID}, array(ExchangeOrdersModel::OPEN_CART_STATUS_PENDING,ExchangeOrdersModel::OPEN_CART_STATUS_PROCESSING))){

                    $openCartOrder->{ExchangeOrdersModel::FIELD_DATE_MODIFIED} = \Carbon\Carbon::now();
                    $openCartOrder->{ExchangeOrdersModel::FIELD_ORDER_STATUS_ID} = ExchangeOrdersModel::OPEN_CART_STATUS_CANCELED;
                    $openCartOrder->save(); // update the order status
					OmsActivityLogModel::newLog($orderId,19,$oms_store); //19 is for cancel Exchange order
                    //UPDATE OPENCART ORDER HISTORY
                    if( $oms_store == 1 ){
                        $orderHistory = new ExchangeOrderHistoryModel();
                    }else if( $oms_store == 2 ){
                        $orderHistory = new DFExchangeOrderHistoryModel();
                    }
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_COMMENT} = "Order canceled from OMS";
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_ORDER_ID} = $exchange_order_id;
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_ORDER_STATUS_ID} = ExchangeOrdersModel::OPEN_CART_STATUS_CANCELED;
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_DATE_ADDED} = \Carbon\Carbon::now();
                    $orderHistory->{ExchangeOrderHistoryModel::FIELD_NOTIFY} = ExchangeOrderHistoryModel::NOTIFY_CUSTOMER;
                    $orderHistory->save();
                    //check if order is in onhold
                    $check_on_hold = OmsInventoryOnholdQuantityModel::where("order_id",$orderId_onhold)->where('store',$oms_store)->first();
                    if( $check_on_hold ){
                        $this->availableInventoryQuantity($orderId,$oms_store);
                    //   self::addQuantity($orderId);
                    }

                    return array('success' => true, 'data' => array(), 'error' => array('message' => ''));
                }else{
                    throw new \Exception("Order can't be canceled in this status");
                }
            }
        }
        catch (\Exception $e){
            return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
        }
    }
    public function cancelQuantity(){
        $this->availableInventoryQuantity(1115552,1);
    }
    public function availableInventoryQuantity($order_id,$oms_store = null){
        if( $oms_store == 1 ){
            $orderd_products = ExchangeOrdersModel::with(['orderd_products'])->where(ExchangeOrdersModel::FIELD_ORDER_ID, $order_id)->first();
        }else if( $oms_store == 2 ){
            $orderd_products = DFExchangeOrdersModel::with('orderd_products')->where(ExchangeOrdersModel::FIELD_ORDER_ID,$order_id)->first();
        }
        if($orderd_products->orderd_products){
            foreach ($orderd_products->orderd_products as $key => $product) {
                if( $oms_store == 1 ){
                    $opencart_sku = ProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }else if( $oms_store == 2 ){
                    $opencart_sku = DFProductsModel::select('sku')->where('product_id', $product->product_id)->first();
                }
                $exists = OmsInventoryProductModel::where('sku', $opencart_sku->sku)->first();
                if($exists){
                    $product_id = $exists->product_id;
                    if(!empty($exists->size)){
                        $total_quantity = 0;
                        foreach ($product->order_options as $key => $option) {
                            if( $oms_store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                ->where('option_description.name', $option->name)
                                ->where('ovd.name', $option->value)
                                ->first();
							    $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
                            }else if( $oms_store == 2 ){

                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                ->where('option_description.name', $option->name)
                                ->where('ovd.name', $option->value)
                                ->first();
							    $ba_color_option_id = OmsInventoryOptionModel::dfColorOptionId();
                            }
                            if($option_data && $option_data->option_id != $ba_color_option_id){
                                $total_quantity = $total_quantity + $product->quantity;
                                $decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $option_data->option_id)->where('option_value_id', $option_data->option_value_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
                            }
                        }

                        $decrement_query = 'IF (onhold_quantity-' . $total_quantity . ' <= 0, 0, onhold_quantity-' . $total_quantity . ')';
                        OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $ba_color_option_id)->where('option_value_id', $exists->color)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $total_quantity) ));
                    }else{
                        foreach ($product->order_options as $key => $option) {
                            if( $oms_store == 1 ){
                                $option_data = OptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }else if( $oms_store == 2 ){
                                $option_data = DFOptionDescriptionModel::select('option_description.option_id','ovd.option_value_id')
                                    ->leftJoin('option_value_description as ovd', 'ovd.option_id', '=', 'option_description.option_id')
                                    ->where('option_description.name', $option->name)
                                    ->where('ovd.name', $option->value)
                                    ->first();
                            }

                            if($option_data){
                                $decrement_query = 'IF (onhold_quantity-' . $product->quantity . ' <= 0, 0, onhold_quantity-' . $product->quantity . ')';
                                OmsInventoryProductOptionModel::where('product_id', $product_id)->where('option_id', $option_data->option_id)->where('option_value_id', $option_data->option_value_id)->update(array('onhold_quantity' => DB::raw($decrement_query), 'available_quantity' => DB::raw('available_quantity+' . $product->quantity) ));
                            }
                        }
                    }
                    updateSitesStock($opencart_sku->sku);
                }
            }
        }
    }
}
