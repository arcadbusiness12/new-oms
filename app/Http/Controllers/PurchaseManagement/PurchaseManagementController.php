<?php

namespace App\Http\Controllers\PurchaseManagement;

use App\Http\Controllers\Controller;
use App\Http\Controllers\inventoryManagement\InventoryManagementController;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsAccountSummaryModel;
use App\Models\Oms\OmsAccountTransactionModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseAwaitingActionCancelledModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseComplaintOrderModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseConfirmedCancelledModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersHistoryModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductOptionModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductQuantityModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersStatusHistoryModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersStatusModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersTotalModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseProductModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseShippedOrdersModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseShippedOrdersProductModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseShippedOrdersProductOptionModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseShippedOrdersProductQuantityModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseShippedOrdersTotalModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseStockCancelledModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseTabsModel;
use App\Models\OpenCart\Products\OptionValueDescriptionModel;
use App\Models\OpenCart\Products\ProductSkuModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;
use Illuminate\Support\Facades\Request AS Input;

class PurchaseManagementController extends Controller
{
    const VIEW_DIR = 'PurchaseManagement';
    const PER_PAGE = 10;
    private $opencart_image_url = '';
	private $static_option_id = 0;
	private $website_image_source_path =  '';
	private $website_image_source_url =  '';
	private $oms_manual_product_image_source_path = '';
	private $oms_manual_product_image_source_url = '';
	private $oms_inventory_product_image_source_path = '';
	private $oms_inventory_product_image_source_url = '';

    function __construct()
    {
        $this->DB_BAOPENCART_DATABASE = env("DB_BAOPENCART_DATABASE");
		$this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
        $this->static_option_id = OmsSettingsModel::get('product_option','color');
		$this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . $_SERVER["DOCUMENT_ROOT"] . '/image/';
		$this->website_image_source_url =  isset( $_SERVER["REQUEST_SCHEME"] ) ? $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/' : "";
		$this->oms_upload_website_image_destination_path = public_path('uploads/images/cache/');
		$this->oms_manual_product_image_source_path = public_path('uploads/products/');
		$this->oms_manual_product_image_source_url = url('/public/uploads/products/');
		$this->oms_manual_product_image_cache_path = public_path('uploads/products/cache/');
		$this->oms_inventory_product_image_source_path = public_path('uploads/inventory_products/');
		$this->oms_inventory_product_image_source_url = url('/public/uploads/inventory_products/');
        $this->oms_inventory_product_image_cache_path = public_path('uploads/inventory_products/cache/');
    }

    public function purchaseOrders(Request $request) {
        $supplier_order_orders = [];
        $whereClause = [];
        $relationWhereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->order_type || $request->order_type === 0) {
            $whereClause[] = ['urgent', $request->order_type];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', $request->product_model];
        }
        if($request->product_sku) {
            $sku_parts = explode("-", $request->product_sku);
            if(count($sku_parts) == 2 && $sku_parts[1] != "") {
                $relationWhereClause[] = ['model', 'REGEXP', $request->product_sku . "[A-Z]"];
            }else {
                $relationWhereClause[] = ['model', 'LIKE', $request->product_sku . "%"];
            }
        }
        $shippedWhereClause = [];
        if($request->order_status_id) {
            $shippedWhereClause[] = ['status', $request->order_status_id];
        }
        $orders = OmsPurchaseOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },
            'orderProducts.orderProductQuantities' => function($qu) {
                $qu->orderBy('order_product_quantity_id', 'ASC');
            },
            'orderProducts.orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },
            'orderHistories',
            'shippedOrders' => function($sh) use($shippedWhereClause) {
                $sh->where($shippedWhereClause)->orderBy('shipped_order_id', 'ASC');
            },
            'shippedOrders.orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },
            'shippedOrders.orderProducts' =>function($sopro) {
                $sopro->having(DB::RAW('(type = \'manual\') OR (type = \'opencart\')'), '=', 1);
            },
            'shippedOrders.orderProductQuantities' =>function($soproq) {
                $soproq->orderBy('order_product_quantity_id', 'ASC');
            },
            'shippedOrders.orderProductQuantities.productOptions' =>function($soproop) {
                $soproop->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderSupplier'

        ]
        )->where($whereClause)->orderBy('order_id', 'DESC')->paginate(self::PER_PAGE)->appends($request->all());
        foreach($orders as $order) {
            $order['status_history'] = $this->statusHistory($order['order_id']);
            foreach($order->orderProducts as $product) {
                $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
            }
            if(count((array)$order->shippedOrders) > 0) {
                foreach($order->shippedOrders as $sorder) {
                    foreach($sorder->orderProducts as $sproduct) {
                        $sproduct['image'] = $this->omsProductImage($sproduct->product_id, 300, 300, $sproduct->type);
                    }

                }
            }

        }
        $order_statuses = OmsPurchaseOrdersStatusModel::get()->toArray();
        $shipped_order_statuses = $this->shippedOrderStatuses();
        $pagination = $orders->render();
        $old_input = $request->all();
        $status_cancel = 7;
        $orders = $orders->toArray();
        // dd($orders);
        return view(self::VIEW_DIR. ".purchaseOrders")->with(compact('orders','pagination','order_statuses','shipped_order_statuses','status_cancel','old_input'));
    }

    public function newPurchaseOrder(Request $request) {
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        $whereClause = [];
        $relationWhereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
            $whereClause[] = ['supplier', session('user_id')];
        }
        $orders = OmsPurchaseOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },
            'orderProducts.orderProductQuantities' => function($qu) {
                $qu->orderBy('order_product_quantity_id', 'ASC');
            },
            'orderProducts.orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderHistories','orderSupplier','orderTotals'
            ])->whereIn('order_status_id', [0,1])->where($whereClause)
            ->orderBy('oms_purchase_order.order_id', 'DESC')
            ->paginate(self::PER_PAGE)
            ->appends($request->all());
            foreach($orders as $order) {
                $cancelled_status = OmsPurchaseAwaitingActionCancelledModel::select('reason','status')->where('order_id', $order['order_id'])->where('supplier', $order['supplier'])->first();
                $order['cancelled_status'] = $cancelled_status;
                foreach($order->orderProducts as $product) {
                    $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
                    $listing_link = OmsInventoryProductModel::where('sku',$product['model'])->first();
                    $product['listing_status'] = $listing_link;
                    $new_arrival_check = OmsPurchaseShippedOrdersProductModel::where('model',$product['model'])->first();
                    $product['new_arrival_check'] = $new_arrival_check;
                }

                if($order['order_status_id'] == 0){
                    $order['status'] = 'insert';
                }else if($order['order_status_id'] == 1){
                    $order['status'] = 'update';
                }
            }
         $counter = $this->productCount();
        //  dd($orders->toArray());
         $search_form_action = \URL::to('/PurchaseManagement/new/purchase/order');
         $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
         return view(self::VIEW_DIR.".newOrders", ["orders" => $orders->toArray(), "pagination" => $orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "counter" => $counter, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
    }

    public function awaitingApproval(Request $request, $count = false) {
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        $whereClause = [];
        $relationWhereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
            $whereClause[] = ['supplier', session('user_id')];
        }
        $awaiting_approval_orders = OmsPurchaseOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },
            'orderProducts.orderProductQuantities' => function($qu) {
                $qu->orderBy('order_product_quantity_id', 'ASC');
            },
            'orderProducts.orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderHistories','orderSupplier','orderTotals'
            ])->where('order_status_id', 1)->where($whereClause)
            ->orderBy('oms_purchase_order.order_id', 'DESC')
            ->paginate(self::PER_PAGE)
            ->appends($request->all());
            if($count == true){ return $awaiting_approval_orders->total(); }

            foreach($awaiting_approval_orders as $order) {
                $cancelled_status = OmsPurchaseAwaitingActionCancelledModel::select('reason','status')->where('order_id', $order['order_id'])->where('supplier', $order['supplier'])->first();
                $order['cancelled_status'] = $cancelled_status;
                foreach($order->orderProducts as $product) {
                    $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
                    $listing_link = OmsInventoryProductModel::where('sku',$product['model'])->first();
                    $product['listing_status'] = $listing_link;
                    $new_arrival_check = OmsPurchaseShippedOrdersProductModel::where('model',$product['model'])->first();
                    $product['new_arrival_check'] = $new_arrival_check;
                }

                if($order['order_status_id'] == 0){
                    $order['status'] = 'insert';
                }else if($order['order_status_id'] == 1){
                    $order['status'] = 'update';
                }
            }
         $counter = $this->productCount();
        //  dd($awaiting_approval_orders->toArray());
        $search_form_action = \URL::to('/PurchaseManagement/awaiting/approval');
         $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
         return view(self::VIEW_DIR.".awaitingApproval", ["orders" => $awaiting_approval_orders->toArray(), "pagination" => $awaiting_approval_orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "counter" => $counter, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
    }

    public function confirmedOrders(Request $request, $count = false) {
        // dd($request->all());
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        $option_id = OmsSettingsModel::get('product_option','color');
        $whereClause = [];
        $relationWhereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
            $whereClause[] = ['supplier', session('user_id')];
        }
        $confirmed_orders = OmsPurchaseOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },
            'orderProductQuantities' => function($oqu) {
                $oqu->orderBy('order_product_quantity_id', 'ASC');
            },
            'orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderHistories','orderSupplier','orderTotals'
            ])->where('order_status_id', 2)->where($whereClause)
            ->orderBy('oms_purchase_order.order_id', 'DESC')
            ->paginate(self::PER_PAGE)
            ->appends($request->all());
            if($count == true){ return $confirmed_orders->total(); }

        //  dd($confirmed_orders->toArray());
            foreach($confirmed_orders as $order) {
                $cancelled_status = OmsPurchaseConfirmedCancelledModel::select('reason','status')->where('order_id', $order['order_id'])->where('supplier', $order['supplier'])->first();;
                $order['cancelled_status'] = $cancelled_status;
                foreach($order->orderProducts as $product) {
                    $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
                    $quantities = OmsPurchaseOrdersProductQuantityModel::with('productOptions')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->get();
                    $units = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('SUM(order_quantity) as unit'),'order_id','order_product_id')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->groupBy('order_id','order_product_id')->first();
                    // dd($units);
                    $product['unit'] = $units->unit;
                    $confirmed_options = array();
                        foreach($quantities as $quantity) {
                            // dd($quantity);
                            foreach($quantity->productOptions as $option) {
                                if($option['product_option_id'] == $option_id){
                                    $option['static'] = 'static';
                                }else{
                                    $option['static'] = 'size';
                                }

                            }
                        }

                        // dd($quantities);
                    $product['quantities'] = $quantities;
                }
            }
            // dd($confirmed_orders->toArray());
         $counter = $this->productCount();
        $search_form_action = \URL::to('/PurchaseManagement/confirmed');
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
        return view(self::VIEW_DIR.".confirmed", ["orders" => $confirmed_orders->toArray(), "pagination" => $confirmed_orders->render(), "tabs" => $tabs, "counter" => $counter, "search_form_action" => $search_form_action, "old_input" => $request->all(), 'suppliers' => $suppliers]);
    }

    public function shippedOrderStatuses(){
        return array(
            1 =>  'Forward',
            2 =>  'Shipped',
            3 =>  'Delivered',
            5 =>  'Cancelled',
        );
    }
    public function placePurchaseOrder(Request $request) {
        $products = [];
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->where('status', 1)->get()->toArray();
        return view(self::VIEW_DIR.".addProduct", ["suppliers" => $suppliers, "products" => $products]);
    }

    public function orderOutStockProduct(Request $request) {
        $products = [];
    		foreach ($request->order_product as $product_id) {
                $product = OmsInventoryProductModel::where('product_id', $product_id)->first();
                $product_array = array();
                if ($product){
                    $product = $product->toArray();
                    $product_array = array(
                        'product_id'    =>  $product['product_id'],
                        'image'         =>  \URL::to('/uploads/inventory_products/' . $product['image']),
                        'sku'           =>  $product['sku'],
                    );

                    $options = get_inventory_product_options_format($product['product_id']);
                    if($options){
                        $product_array['options'] = array();
                        foreach ($options as $option) {
                            $option_values = array();
                            foreach ($option['option_values'] as $value) {
                                $quantity = $minimum_quantity = $average_quantity = $duration = '';
                                $quantity = $value['available_quantity'];

                                $OmsInventoryStockModel = OmsInventoryStockModel::select('minimum_quantity','average_quantity','duration')->where('product_id', $product['product_id'])->where('option_id', $option['option_id'])->where('option_value_id', $value['option_value_id'])->first();

                                $average_quantity_shipped = getLastSaleQtyWithOptionShipped($product['product_id'], $value); //for shipped quantity
                                $average_tot_quantity = $average_quantity_shipped->total;
                                if($OmsInventoryStockModel){
                                    $minimum_quantity = $OmsInventoryStockModel->minimum_quantity;
                                }
                                $option_values[] = array(
                                    'option_id'   =>  $option['option_id'],
                                    'option_value_id'   =>  $value['option_value_id'],
                                    'name'              =>  $value['name'],
                                    'quantity'          =>  $quantity,
                                    'minimum_quantity'  =>  $minimum_quantity,
                                    'average_quantity'  =>  $average_tot_quantity,
                                    'duration'          =>  30,
                                );
                            }
                            $product_array['options'][] = array(
                                'option_id'         =>  $option['option_id'],
                                'static_option_id'  =>  $this->static_option_id,
                                'name'              =>  $option['name'],
                                'type'              =>  $option['type'],
                                'option_values'     =>  $option_values,
                            );
                        }
                    }
                }
             array_push($products, $product_array);
    		}

            $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->where('status',1)->get()->toArray();
            return view(self::VIEW_DIR.".addProduct", ["suppliers" => $suppliers, "products" => $products]);
    }

    public function addOrder(Request $request) {
        if(count($request->all()) > 0){
            $option_id = OmsSettingsModel::get('product_option','color');
            $same_option = false;
            if($request->purchase){
	            foreach ($request->purchase as $product_id => $op_code_arr) {
	                $p_o_id[$product_id] = array();
	                if($product_id == 'product'){
	                    foreach ($op_code_arr as $product_kk => $product_option_arr) {
	                        $p_o_id_color[$product_kk] = array();
	                        if(isset($product_option_arr['options']) && !empty($product_option_arr['options'])){
	                            foreach ($product_option_arr['options'] as $product_keys => $product_values) {
	                                if(in_array($product_values['option'], $p_o_id_color[$product_kk])){
	                                    $same_option = true;
	                                }else{
	                                    array_push($p_o_id_color[$product_kk], $product_values['option']);
	                                }

	                            }
	                        }else{
	                        	return response()->json(array('error' => 'Options must be selected!'));
	                        	die;
	                        }
	                    }
	                }else{
	                    foreach ($op_code_arr as $option_arr) {
	                        if(isset($option_arr['option']) && !empty($option_arr['option'])){
	                            foreach ($option_arr['option'] as $o_id => $o_v_id) {
	                                if($o_id != $option_id){
	                                    if(in_array($o_v_id, $p_o_id[$product_id])){
	                                        $same_option = true;
	                                    }else{
	                                        array_push($p_o_id[$product_id], $o_v_id);
	                                    }
	                                }
	                            }
	                        }else{
	                        	return response()->json(array('error' => 'Options must be selected!'));
	                        	die;
	                        }
	                    }
	                }
	            }
            }else{
            	return response()->json(array('error' => 'Product and option must be selected!'));
            	die;
            }
            if($same_option){
                return response()->json(array('error' => 'Same option in multiple time not allowed!'));
                die;
            }
            if($request->purchase){
	            foreach ($request->purchase as $product_id => $op_code_arr) {
	                $p_o_id[$product_id] = array();
	                if($product_id == 'product'){
	                    foreach ($op_code_arr as $product_kk => $product_option_arr) {
				            if(isset($product_option_arr['add_to_inventory']) && $product_option_arr['add_to_inventory'] == 'on'){
				            	$skuExists = OmsInventoryProductModel::where('sku', $product_option_arr['name'])->exists();
				            	if($skuExists){
				            		return response()->json(array('error' => 'Product SKU with name <b>'.$product_option_arr['name'].'</b> already exists!'));
				            		die;
				            	}
				            }
				          }
				          }
            }
          }
            if($request->urgent) $urgent = 1;
            else $urgent = 0;

            $OmsPurchaseOrdersModel = new OmsPurchaseOrdersModel();
            $OmsPurchaseOrdersModel->{OmsPurchaseOrdersModel::FIELD_TOTAL} = 0;
            $OmsPurchaseOrdersModel->{OmsPurchaseOrdersModel::FIELD_ORDER_STATUS_ID} = 0;
            $OmsPurchaseOrdersModel->{OmsPurchaseOrdersModel::FIELD_LINK} = '';
            $OmsPurchaseOrdersModel->{OmsPurchaseOrdersModel::FIELD_URGENT} = $urgent;
            $OmsPurchaseOrdersModel->{OmsPurchaseOrdersModel::FIELD_SUPPLIER} = $request->supplier;
            $OmsPurchaseOrdersModel->save();

            $order_id = $OmsPurchaseOrdersModel->order_id;

            $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Admin';
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
            $OmsPurchaseOrdersHistoryModel->save();

            foreach ($request->purchase as $product_id => $value) {
                if($product_id == 'product'){
                    foreach ($value as $key => $manual_product) {
                        $name = '';
                        $image = '';
                        if($request->hasFile('image')) {
                            $file = $request->file('image')[$key];
                            $extension = $request->file('image')[$key]->getClientOriginalExtension();
                            $filename = md5(uniqid(rand(), true)) . '.' .$extension;
                            $file->move(base_path('public/uploads/products/'), $filename);
                            if(isset($manual_product['add_to_inventory']) && $manual_product['add_to_inventory'] == 'on'){
                                copy(base_path('public/uploads/products/') . $filename, base_path('public/uploads/inventory_products/') . $filename);
                            }
                            $name = $manual_product['name'];
                            $image = $filename;
                        }

                        $OmsPurchaseProductModel = new OmsPurchaseProductModel();
                        $OmsPurchaseProductModel->{OmsPurchaseProductModel::FIELD_NAME} = $name;
                        $OmsPurchaseProductModel->{OmsPurchaseProductModel::FIELD_IMAGE} = $image;
                        $OmsPurchaseProductModel->save();
                        $product_id = $OmsPurchaseProductModel->product_id;
                        $sku_to_model = "";
                        if(isset($manual_product['add_to_inventory']) && $manual_product['add_to_inventory'] == 'on'){
                          $sku_to_model = $name;
                          $name = "";
                        }
                        $OmsPurchaseOrdersProductModel = new OmsPurchaseOrdersProductModel();
                        $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_ORDER_ID} = $order_id;
                        $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_PRODUCT_ID} = $product_id;
                        $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_NAME} = $name;
                        $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_MODEL} = $sku_to_model;
                        $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_TYPE} = 'manual';
                        $OmsPurchaseOrdersProductModel->save();
                        foreach ($manual_product['options'] as $optionKey => $product) {
                            $OmsPurchaseOrdersProductQuantityModel = new OmsPurchaseOrdersProductQuantityModel();
                            $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_ORDER_ID} = $order_id;
                            $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                            $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_QUANTITY} = $product['quantity'];
                            $OmsPurchaseOrdersProductQuantityModel->save();

                            if(isset($product['option']) && is_array($product['option'])){
                                $quantity_id = $OmsPurchaseOrdersProductQuantityModel->order_product_quantity_id;
                                foreach ($product['option'] as $option_id => $option_value_id) {
                                    $option_name = OmsOptions::select('option_name')->where('id', $option_id)->first()->option_name;
                                    $option_value = OmsDetails::select('value')->where('id', $option_value_id)->first()->value;

                                    $OmsPurchaseOrdersProductOptionModel = new OmsPurchaseOrdersProductOptionModel();
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_PRODUCT_QUANTITY_ID} = $quantity_id;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_ID} = $order_id;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_PRODUCT_OPTION_ID} = $option_id;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_PRODUCT_OPTION_VALUE_ID} = $option_value_id;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_NAME} = $option_name;
                                    $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_VALUE} = $option_value;
                                    $OmsPurchaseOrdersProductOptionModel->save();
                                }
                            }
                        }

                        if(isset($manual_product['add_to_inventory']) && $manual_product['add_to_inventory'] == 'on'){
                            $product_options = $manual_product['options'];

                            $groupName = $this->getGroupName($manual_product['name']);
                            $group_exist = ProductGroupModel::where('name', $groupName)->first();
                            if(!$group_exist) {
                                $cateName = GroupCategoryModel::find($manual_product['category']);
                                $group = new ProductGroupModel();
                                $group->name = $groupName;
                                $group->category_id = $manual_product['category'];
                                $group->category_name = $cateName->name;
                                $group->sub_category_id = $manual_product['subCategory'] ? $manual_product['subCategory'] : null;
                                $group->group_sku = $manual_product['newSku'];
                                if($group->save()) {
                                    $group_id = $group->id;
                                }
                            }else {
                                $group_id = $group_exist->id;
                            }
                            $color_name = OmsDetails::where("id",$manual_product['manually_option_color'])->first();
                            if( $color_name ){
                              $color_name = $color_name->value;
                            }
                            $OmsInventoryProductModel = new OmsInventoryProductModel();
                            $OmsInventoryProductModel->{OmsInventoryProductModel::FIELD_SKU} = $manual_product['name'];
                            $OmsInventoryProductModel->{OmsInventoryProductModel::FIELD_IMAGE} = $image;
                            $OmsInventoryProductModel->option_name = $color_name;
                            $OmsInventoryProductModel->option_value = $manual_product['manually_option_size'];
                            $OmsInventoryProductModel->group_id = $group_id;
                            $OmsInventoryProductModel->save();

                            $total_quantity = 0;
                            foreach ($product_options as $key => $options) {
                                    foreach ($options['option'] as $option_id => $option_value_id) {
                                        if(  $option_id != $this->static_option_id || count($options['option']) == 1 ){
                                            $OmsInventoryProductOptionModel = new OmsInventoryProductOptionModel();
                                            $OmsInventoryProductOptionModel->{OmsInventoryProductOptionModel::FIELD_PRODUCT_ID} = $OmsInventoryProductModel->product_id;
                                            $OmsInventoryProductOptionModel->{OmsInventoryProductOptionModel::FIELD_OPTION_ID} = $option_id;
                                            $OmsInventoryProductOptionModel->{OmsInventoryProductOptionModel::FIELD_OPTION_VALUE_ID} = $option_value_id;
                                            $OmsInventoryProductOptionModel->{OmsInventoryProductOptionModel::FIELD_AVAILABLE_QUANTITY} = 0;
                                            $OmsInventoryProductOptionModel->save();
                                        }
                                    }
                            }
                        }
                    }
                }else{
                    $product_data = OmsInventoryProductModel::where('product_id',$product_id)->first();
                    $OmsPurchaseOrdersProductModel = new OmsPurchaseOrdersProductModel();
                    $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_ORDER_ID} = $order_id;
                    $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_PRODUCT_ID} = $product_id;
                    $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_NAME} = '';
                    $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_MODEL} = $product_data->sku;
                    $OmsPurchaseOrdersProductModel->{OmsPurchaseOrdersProductModel::FIELD_TYPE} = 'opencart';
                    $OmsPurchaseOrdersProductModel->save();
                    // dd($value);
                    foreach ($value as $product) {
                        $OmsPurchaseOrdersProductQuantityModel = new OmsPurchaseOrdersProductQuantityModel();
                        $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_ORDER_ID} = $order_id;
                        $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                        $OmsPurchaseOrdersProductQuantityModel->{OmsPurchaseOrdersProductQuantityModel::FIELD_QUANTITY} = $product['quantity'] ? $product['quantity'] : 0;
                        $OmsPurchaseOrdersProductQuantityModel->save();

                        if(isset($product['option']) && is_array($product['option'])){
                            $quantity_id = $OmsPurchaseOrdersProductQuantityModel->order_product_quantity_id;
                            foreach ($product['option'] as $option_id => $option_value_id) {
                                $option_name  = OmsOptions::where('id',$option_id)->first()->option_name;
                                $option_value  = OmsDetails::where('id',$option_value_id)->first()->value;
                                $OmsPurchaseOrdersProductOptionModel = new OmsPurchaseOrdersProductOptionModel();
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_PRODUCT_QUANTITY_ID} = $quantity_id;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_ID} = $order_id;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_PRODUCT_OPTION_ID} = $option_id;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_PRODUCT_OPTION_VALUE_ID} = $option_value_id;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_NAME} = $option_name;
                                $OmsPurchaseOrdersProductOptionModel->{OmsPurchaseOrdersProductOptionModel::FIELD_VALUE} = $option_value;
                                $OmsPurchaseOrdersProductOptionModel->save();
                            }
                        }
                    }
                }
            }

            Session::flash('message', 'Purchase order added successfully.');
            Session::flash('alert-class', 'alert-success');
            return redirect()->route('inventory.alarm')->with('message', 'Purchase order added successfully.');
        }else{
            Session::flash('message', 'Something went wrong, please try again!');
            Session::flash('alert-class', 'alert-warning');
            return redirect()->route('place.purchase.order')->with('message', 'Something went wrong, please try again!');
        }
    }

    public function updateAwaitingActionArder(Request $request) {
        $order_id = $request->order_id;
        if($request->submit == 'update_awaiting_action') {
            // Check Qauntities
            $error = false;
            $error = $this->checkOrderQuantity($request->quantity);
            if($error) {
                return Redirect::back()->with('error_message', 'Do not enter greater than order quantity!');
            }
            $exist = OmsPurchaseOrdersModel::where('order_id', $order_id)->where('order_status_id', 1)->exists();
            if(!$exist) {
                $this->updateOrderQuantityPrice($request, $order_id);
                return Redirect::back()->with('message', 'Your Order #'.$order_id.' actioned successfully.');
            }else {
                return Redirect::back()->with('message', 'You have already actioned your order!');
            }
        }elseif($request->submit == 'update_quantity_price') {
            $error = false;
            $error = $this->checkOrderQuantity($request->quantity);
            if($error) {
                return Redirect::back()->with('error_message', 'Do not enter greater than order quantity!');
            }
            $this->updateOrderQuantityPrice($request, $order_id);
            return Redirect::back()->with('message', 'Your Order #'.$order_id.' updated successfully.');

        }elseif($request->submit == 'cancel') {
            $purchaseOrder = OmsPurchaseOrdersModel::where('order_id', $order_id)->where('order_status_id', 7)->exists();
           if(!$purchaseOrder) {
               $updateData = array(
                   'total' => $request->total,
                   'link'  => $request->supplier_link,
                   'order_status_id' => 7
               );
               OmsPurchaseOrdersModel::where('order_id', $order_id)->update($updateData);
               return response()->json([
                   'success' => true,
                   'message' => 'Your Order #'.$order_id.' cancelled successfully.'
               ]);
           }else {
            return response()->json(array('success' => false, 'message' => 'You have already cancelled your order!'));
           }
        }elseif($request->submit == 'delete-order') {
            $exist = OmsPurchaseOrdersModel::where('order_id', $order_id)->exists();
            if($exist){
                OmsPurchaseOrdersModel::where('order_id', $order_id)->delete();
                OmsPurchaseOrdersHistoryModel::where('order_id', $order_id)->delete();
                OmsPurchaseOrdersProductModel::where('order_id', $order_id)->delete();
                OmsPurchaseOrdersProductOptionModel::where('order_id', $order_id)->delete();
                OmsPurchaseOrdersProductQuantityModel::where('order_id', $order_id)->delete();
                OmsPurchaseOrdersTotalModel::where('order_id', $order_id)->delete();

                return response()->json(array('success' => true, 'message' => 'Your Order deleted successfully.'));
            }else{
                return response()->json(array('success' => false, 'message' => 'You have already deleted your order!'));
            }
        }elseif($request->submit == 'save-comment') {
            if($request->instruction && !empty($request->instruction)){
                $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
                  $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
                  $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = session('user_group_id')==2 ? 'Supplier' : 'Admin';
                  $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
                  $OmsPurchaseOrdersHistoryModel->save();

                  return Redirect::back()->with('message', 'Comment Added Your Order #'.$order_id.' successfully.');
            }else{
                return Redirect::back()->with('error_message', 'Enter comment and submit!');
            }
        }else{
            return Redirect::back()->with('error_message', 'Something went wrong, please try again!');
        }
    }

    public function editPurchaseOrders($order_id) {
        // dd($ordesssr_id);
        $order = OmsPurchaseOrdersModel::with(['orderProducts', 'orderHistories', 'orderTotals','orderSupplier', 'orderProducts.orderProductQuantities' => function($qu) {
            $qu->orderBy('order_product_quantity_id', 'ASC');
        },
        'orderProducts.orderProductQuantities.productOptions' => function($qo) {
            $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
        }])->where('order_id', $order_id)->first();
        // dd($order->orderProducts);
        foreach($order->orderProducts as $product) {
            $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
            foreach($product->orderProductQuantities as $quantity) {
                foreach($quantity->productOptions as $option) {
                    if($this->static_option_id != $option['product_option_id']){
                        $option_dropdown = OptionValueDescriptionModel::select('option_value_id','name')->where('option_id', $option['product_option_id'])->groupBy('name')->get()->toArray();
                        $option['options'] = $option_dropdown;
                    }
                }
            }
        }
        // dd($order->order_id);
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->where('status', 1)->get()->toArray();
        return view(self::VIEW_DIR. '.editPurchaseOorder', ["order" => $order, "suppliers" => $suppliers, "status_cancel" => 7]);
    }

    public function updatePurchaseOrders(Request $request) {
        $order_id = $request->order_id;
        $products = $request->product;
        if(count($products) > 0) {
            foreach($products as $product_id => $product) {
                if(isset($product['name'])) {
                    $updateProduct = ['name' => $product['name']];
                }
                if(isset($product['model'])) {
                    $updateProduct = ['model' => $product['model']];
                }
                OmsPurchaseOrdersProductModel::where('order_id', $order_id)->where('product_id', $product_id)->update($updateProduct);
                foreach ($product['quantity'] as $order_product_quantity_id => $value) {
					OmsPurchaseOrdersProductQuantityModel::where('order_id', $order_id)->where('order_product_quantity_id', $order_product_quantity_id)->update(array('quantity' => $value));
		        }

                if(isset($product['option'])) {
                    foreach($product['option'] as $order_product_option_id => $option) {
                        $option_name = OptionValueDescriptionModel::select('name')->where('option_value_id', $option)->first();
                        OmsPurchaseOrdersProductOptionModel::where('order_id', $order_id)->where('order_product_option_id', $order_product_option_id)
                                                             ->update(['product_option_value_id' => $option, 'value' => $option_name->name]);
                    }
                }
            }
            OmsPurchaseOrdersModel::where('order_id', $order_id)->update(['supplier' => $request->supplier]);
            return redirect()->route('purchase.orders')->with('message', 'Your Order #'. $order_id .' updated successfully.');
        }else {

            return redirect()->route('purchase.orders')->with('error_message', 'Something went wrong!');
        }
    }

    public function updateAwaitingApprovalOrder(Request $request) {
        $order_id = $request->order_id;
            if($request->submit == 'approve') {
                $exist = OmsPurchaseOrdersModel::where('order_id', $order_id)->where('order_status_id', 2)->exists();
                if(!$exist) {
                    OmsPurchaseOrdersModel::where('order_id', $order_id)->update(['order_status_id'=> 2]);
                    $this->addOrderStatusHistory($order_id, 2);
                    if($request->instruction) {
                        $orderHistory = new OmsPurchaseOrdersHistoryModel();
                        $orderHistory->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
                        $orderHistory->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Admin';
                        $orderHistory->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
                        $orderHistory->save();
                    }
                    //update product listing link in inventory_product table start
                    if($request->product_listing_link) {
                        foreach($request->porudct_listing_links as $sku => $link){
                            OmsInventoryProductModel::where('sku', $sku)->update(['supplier_link'=>$link]);
                        }
                    }
                    return redirect()->route('awaiting.approval.purchase.orders')->with('message', 'Your Order #'.$order_id.' approved successfully.');
                }else {
                    return redirect()->route('awaiting.approval.purchase.orders')->with('error_message', 'You have already approved your order!');
                }
            }elseif($request->submit == 'cancel') {
                $exist = OmsPurchaseOrdersModel::where('order_id', $order_id)->where('order_status_id', 7)->exists();
                if(!$exist) {
                    OmsPurchaseOrdersModel::where('order_id', $order_id)->update(['order_status_id' => 7]);
                    return response()->json(array('success' => true, 'message' => 'Your Order #'.$order_id.' cancelled successfully.'));
                }else {
                    return response()->json(array('success' => true, 'message' => 'You have already cancelled your order!'));
                }
            }else {
                return redirect()->route('awaiting.approval.purchase.orders')->with('error_message', 'Something went wrong, please try again!');
            }
    }

    public function orderShipping($order_id = '') {
        if(empty($order_id) || session('role') == 'ADMIN') {
            return redirect()->route('to.be.ship.order');
        }
        $order = OmsPurchaseOrdersModel::with(['orderProducts','orderHistories'])->where('order_id', $order_id)->whereIn('order_status_id', [2,4])
                                         ->where('supplier', session('user_id'))->first();

        if($order) {
            $option_id = OmsSettingsModel::get('product_option','color');
            foreach($order->orderProducts as $product) {
                $units = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('SUM(order_quantity - shipped_quantity) as unit'),'order_product_quantity_id')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                $product['unit'] = $units['unit'];
                $options = OmsPurchaseOrdersProductOptionModel::select('order_product_quantity_id','product_option_id','product_option_value_id','name','value')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->groupBy('product_option_id','product_option_value_id')->orderBy('name', 'ASC')->get()->toArray();
                $ship_options = array();
                 if($options && is_array($options)){
                    foreach($options as $k => $option) {
                        $quantity = OmsPurchaseOrdersProductQuantityModel::select('order_quantity','shipped_quantity')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->where('order_product_quantity_id', $option['order_product_quantity_id'])->first()->toArray();

                        if($quantity['order_quantity'] > $quantity['shipped_quantity']){
                            if($option['product_option_id'] == $option_id) {
                                $ship_options['static'] = array(
                                    'order_product_quantity_id'     =>  $option['order_product_quantity_id'],
                                    'product_option_id'             =>  $option['product_option_id'],
                                    'product_option_value_id'       =>  $option['product_option_value_id'],
                                    'name'                          =>  $option['name'],
                                    'value'                         =>  $option['value'],
                                    'quantity'                      =>  $quantity['order_quantity'] - $quantity['shipped_quantity'],
                                );
                            }else{
                                $ship_options[] = array(
                                    'order_product_quantity_id'     =>  $option['order_product_quantity_id'],
                                    'product_option_id'             =>  $option['product_option_id'],
                                    'product_option_value_id'       =>  $option['product_option_value_id'],
                                    'name'                          =>  $option['name'],
                                    'value'                         =>  $option['value'],
                                    'quantity'                      =>  $quantity['order_quantity'] - $quantity['shipped_quantity'],
                                );
                            }
                            $options[$k]['quantity'] = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                        }
                  }
                //   dd($options);
                    $product['quantity'] = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                    $product['image']    = $this->omsProductImage($product['product_id'],300,300,$product['type']);

                }else {
                    $quantity = OmsPurchaseOrdersProductQuantityModel::select('order_quantity','shipped_quantity')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                    $product['quantity'] = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                    $product['image']    = $this->omsProductImage($product['product_id'],300,300,$product['type']);
                }
                $product['options'] = $ship_options;

            }
        }
        return view(self::VIEW_DIR.".ship", ["order" => $order]);
    }

    public function addToShip(Request $request) {
        // dd($request->all());
        OmsPurchaseStockCancelledModel::where('order_id', $request->order_id)->delete();
        $valid = true;
        foreach($request->product as $product_id => $options) {
            foreach($options['option'] as $key => $option) {
                $quantities = OmsPurchaseOrdersProductQuantityModel::select('order_quantity','shipped_quantity')->where('order_id', $request->order_id)->where('order_product_id',$product_id)->where('order_product_quantity_id',$option['order_product_quantity_id'])->first();
                // dd($quantities);
                if($quantities) {
                    if(($quantities->order_quantity - $quantities->shipped_quantity) < $option['shipped_quantity']) {
                        $valid = false;
                    }
                }else {
                    $valid = false;
                }
            }
        }
        if($valid) {
            $total_order_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(order_quantity) as total_quantity'))->where('order_id', $request->order_id)->first()->total_quantity;
            $total_shipped_orders = OmsPurchaseShippedOrdersModel::select(DB::Raw('count(shipped_order_id) as total_shipped_orders'))->where('order_id', $request->order_id)->first()->total_shipped_orders;
            $link = $request->link ? $request->link : '';
            $shipping[$request->shipped] = [
                'name' => $request->shipping_name,
                'tracking' => $request->tracking_number,
                'date'      =>  date('Y-m-d')
            ];
            $shippingOrder = new OmsPurchaseShippedOrdersModel();
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED_ID} = $request->order_id . '-' . ($total_shipped_orders + 1);
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_ORDER_ID} = $request->order_id;
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED} = $request->shipped;
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_LINK} = $link;
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_URGENT} = $request->urgent;
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_SUPPLIER} = session('user_id');
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPING} = json_encode($shipping);
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_TOTAL} = 0;
            if($request->shipped == 'dubai'){
                $shipped_status = 2;
            }else{
                $shipped_status = 1;
            }
            $shippingOrder->{OmsPurchaseShippedOrdersModel::FIELD_STATUS} = $shipped_status;
            $shippingOrder->save();
            $order_id = $shippingOrder->shipped_order_id;
            $shipped_id = $shippingOrder->shipped_id;
            if($request->instruction){
                $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $request->order_id;
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Supplier';
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
                $OmsPurchaseOrdersHistoryModel->save();
            }

            $shipped_sub_total = 0;
            foreach ($request->product as $product_id => $options) {
                $order_product_type = OmsPurchaseOrdersProductModel::select('*')->where('product_id', $product_id)->where('order_id',$request->order_id)->where('type', $options['type'])->first();
                $product_name = $product_model = '';
                if($order_product_type){
                    $product_model = $order_product_type->model;
                    $product_name = $order_product_type->name;
                }
                $OmsPurchaseShippedOrdersProductModel = new OmsPurchaseShippedOrdersProductModel();
                $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
                $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_PRODUCT_ID} = $product_id;
                $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_NAME} = $product_name;
                $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_MODEL} = $product_model;
                $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_TYPE} =$order_product_type ? $order_product_type->type : 'opencart';
                $OmsPurchaseShippedOrdersProductModel->save();

                foreach ($options['option'] as $key => $option) {
                    $quantity_details = OmsPurchaseOrdersProductQuantityModel::select('*')->where('order_product_quantity_id', $option['order_product_quantity_id'])->first()->toArray();
                    $shipped_total = $option['shipped_quantity'] * $quantity_details['price'];
                    $shipped_sub_total = $shipped_sub_total + $shipped_total;

                    $OmsPurchaseShippedOrdersProductQuantityModel = new OmsPurchaseShippedOrdersProductQuantityModel();
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_QUANTITY} = $option['shipped_quantity'];
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_RECEIVED_QUANTITY} = 0;
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_PRICE} = $quantity_details['price'];
                    $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_TOTAL} = $shipped_total;
                    $OmsPurchaseShippedOrdersProductQuantityModel->save();

                    $quantity_id = $OmsPurchaseShippedOrdersProductQuantityModel->order_product_quantity_id;

                    $options_details = OmsPurchaseOrdersProductOptionModel::select('*')->where('order_product_quantity_id', $option['order_product_quantity_id'])->get()->toArray();
                    if($options_details){
                        foreach ($options_details as $quantity_option) {
                            $OmsPurchaseShippedOrdersProductOptionModel = new OmsPurchaseShippedOrdersProductOptionModel();
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_QUANTITY_ID} = $quantity_id;
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_ID} = $quantity_option['product_option_id'];
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_VALUE_ID} = $quantity_option['product_option_value_id'];
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_NAME} = $quantity_option['name'];
                            $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_VALUE} = $quantity_option['value'];
                            $OmsPurchaseShippedOrdersProductOptionModel->save();
                        }
                    }

                    OmsPurchaseOrdersProductQuantityModel::where('order_product_quantity_id', $option['order_product_quantity_id'])->update(array('shipped_quantity' => DB::raw('shipped_quantity+'.$option['shipped_quantity'])));
                }
                $model = '';
                if($order_product_type->model) {
                    $model = $order_product_type->model;
                }else {
                    $model = $order_product_type->name;
                }
                $inventpry_product = OmsInventoryProductModel::where('sku', $model)->update(['confirm_date' => date('Y-m-d')]);
            }
            $order_totals = OmsPurchaseOrdersTotalModel::select('code','title','value','sort_order')->where('order_id', $request->order_id)->orderBy('sort_order', 'ASC')->get()->toArray();
            $local_shipping = array(
               'code'  =>  'Local Shipping Cost',
               'title'  =>  'local_shipping_cost',
               'value'  =>  isset($request->local_cost) ? $request->local_cost : 0,
               'sort_order'  =>  count($order_totals)
             );
            $main_total = end($order_totals);
            $main_total['value'] = $shipped_sub_total;
            foreach ($order_totals as $key => $value) {
                if($value['title'] === 'sub_total'){
                    $order_totals[$key]['value'] = $shipped_sub_total;
                }
                if($value['title'] === 'local_express_cost'){
                     $main_total['value'] = $main_total['value'] + $value['value'];
                }
            }

            $main_total['value'] = $main_total['value'] + $local_shipping['value'];
            $main_total['sort_order'] = count($order_totals) + 1;
            array_pop($order_totals);
            array_push($order_totals, $local_shipping);
            array_push($order_totals, $main_total);
            foreach ($order_totals as $total) {
                $value_amount = $total['value'];
                if( $total['title'] == 'local_express_cost' && $total_shipped_orders > 0 ){
                    $value_amount = 0;
                }
                $OmsPurchaseShippedOrdersTotalModel = new OmsPurchaseShippedOrdersTotalModel();
                $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
                $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_CODE} = $total['code'];
                $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_TITLE} = $total['title'];
                $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_VALUE} = $value_amount;
                $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SORT_ORDER} = $total['sort_order'];
                $OmsPurchaseShippedOrdersTotalModel->save();
            }

            OmsPurchaseShippedOrdersModel::where('shipped_order_id', $order_id)->update(array('total' => $main_total['value']));
            $total_shipped_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(shipped_quantity) as total_quantity'))->where('order_id', $request->order_id)->first()->total_quantity;
            $order_status_id = OmsPurchaseOrdersModel::select('order_status_id')->where('order_id', $request->order_id)->first();

            if($order_status_id->order_status_id !== 7){
                $forwarder_count = OmsPurchaseShippedOrdersModel::select(DB::raw("COUNT(shipped_order_id) as total"))->where('order_id', $request->order_id)->where('status', 1)->first();

                if(($total_order_quantity == $total_shipped_quantity && $forwarder_count->total == 0)){
                  // || Input::get('shipped') == 'dubai'
                    OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(array('order_status_id' => 5));

                    $this->addOrderStatusHistory($request->order_id, 5);
                    return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$request->order_id.' shipped successfully.');
                }else{
                    OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(array('order_status_id' => 4));

                    $this->addOrderStatusHistory($request->order_id, 4);
                    return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$request->order_id.' shipped successfully.');
                }

            }else{
                return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$shipped_id.' shipped successfully.');
            }
        }else {
            return redirect()->back()->with('error_message', 'Do not enter greater than order quantity!');
        }
    }

    public function shipToDubai(Request $request) {
        if($request->submit == 'update_ship_to_dubai'){
            $shipping_info = array();
            $shipping = OmsPurchaseShippedOrdersModel::select('shipping')->where('order_id', $request->order_id)->where('shipped_id', $request->shipped_id)->first();
            if($shipping->shipping) $shipping_info = json_decode($shipping->shipping,1);
            $shipping_new['dubai'] = array(
                'name'      =>  $request->shipping_name,
                'tracking'  =>  $request->tracking_number,
                'date'      =>  date('Y-m-d'),
            );
            $shipping_info = array_merge($shipping_info, $shipping_new);

            OmsPurchaseShippedOrdersModel::where('order_id', $request->order_id)->where('shipped_id', $request->shipped_id)->update(array('shipped' =>  'dubai', 'shipping' => json_encode($shipping_info), 'status' => 2));

            $total_order_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(order_quantity) as total_quantity'))->where('order_id', $request->order_id)->first()->total_quantity;
            $total_shipped_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(shipped_quantity) as total_quantity'))->where('order_id', $request->order_id)->first()->total_quantity;

            $forwarder_count = OmsPurchaseShippedOrdersModel::select(DB::raw("COUNT(shipped_order_id) as total"))->where('order_id', $request->order_id)->where('status', 1)->first();

            $order_status_id = OmsPurchaseOrdersModel::select('order_status_id')->where('order_id', $request->order_id)->first();

            if($order_status_id->order_status_id !== 7){
                if($total_order_quantity == $total_shipped_quantity && $forwarder_count->total == 0){
                    OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(array('order_status_id'   => 5));

                    $this->addOrderStatusHistory($request->order_id, 5);
                }else{
                    OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(array('order_status_id'   => 4));

                    $this->addOrderStatusHistory($request->order_id, 4);
                }
            }
            return redirect()->back()->with('message', 'Your Order #'.$request->shipped_id.' shipped successfully.');
        }
    }

    public function getToBeShipped(Request $request, $count = false) {
        // dd("Ok");
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        $option_id = OmsSettingsModel::get('product_option','color');
        $whereClause = [];
        $relationWhereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
            $whereClause[] = ['supplier', session('user_id')];
        }
        $shippedWhereClause = [];
        if($request->order_status_id) {
            $shippedWhereClause[] = ['status', $request->order_status_id];
        }
        $orders = OmsPurchaseOrdersModel::with([
            'orderProducts',
            'orderProductQuantities' => function($qu) {
                $qu->orderBy('order_product_quantity_id', 'ASC');
            },
            'orderProducts.orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },
            'shippedOrders' => function($sh) use($shippedWhereClause) {
                $sh->where($shippedWhereClause)->orderBy('shipped_order_id', 'ASC')
                    ->where('status', 1);
            },
            'shippedOrders.orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },
            'shippedOrders.orderProducts',
            'shippedOrders.orderProductQuantities' =>function($soproq) {
                $soproq->orderBy('order_product_quantity_id', 'ASC');
            },'shippedOrders.orderProducts.orderProductQuantities' =>function($soproq) {
                $soproq->orderBy('order_product_quantity_id', 'ASC');
            },
            'shippedOrders.orderProducts.orderProductQuantities.productOptions' =>function($soproop) {
                $soproop->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },
            'orderSupplier'

        ]
        )->where($whereClause)->whereIn('order_status_id', [4])->orderBy('order_id', 'DESC')->groupBy('order_id')
        ->paginate(self::PER_PAGE)->appends($request->all());
        if($count == true){ return $orders->count(); }
        // dd($orders->toArray());
        foreach($orders as $order) {
            $order_total = 0;
            $stock_cancel = OmsPurchaseStockCancelledModel::where(OmsPurchaseStockCancelledModel::FIELD_ORDER_ID, $order['order_id'])->where(OmsPurchaseStockCancelledModel::FIELD_SUPPLIER, session('user_id'))->where(OmsPurchaseStockCancelledModel::FIELD_STATUS, 0)->whereNull('shiped_order_id')->exists();
            $order['stock_cancel'] = $stock_cancel;
            // dd($order->orderProducts->toArray());
            foreach($order->orderProducts as $key => $product) {
                $any_qty_remain = 0;
                $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
                $quantities = OmsPurchaseOrdersProductQuantityModel::where('order_id', $order['order_id'])->where('order_product_id', $product->product_id)->get();
                $units = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('SUM((order_quantity - shipped_quantity)) as unit'),'order_id','order_product_id')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->groupBy('order_id','order_product_id')->first();
                $product['unit'] = $units->unit;
                $any_qty_remain = 0;
                foreach($quantities as $k => $quantity) {
                    $options = OmsPurchaseOrdersProductOptionModel::select('product_option_id','name','value')->where('order_product_quantity_id', $quantity['order_product_quantity_id'])->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC')->get()->toArray();

                    $to_be_shipped_options = array();
                    foreach ($options as $option) {
                        if($option['product_option_id'] == $option_id){
                            $to_be_shipped_options['static'] = array(
                                'name'  =>  $option['name'],
                                'value' =>  $option['value'],
                            );
                        }else{
                            $to_be_shipped_options[] = array(
                                'name'  =>  $option['name'],
                                'value' =>  $option['value'],
                            );
                        }
                    }
                    // $quantities[$k]['options'] = $to_be_shipped_options;
                    $order_total += ($quantity['order_quantity'] - $quantity['shipped_quantity']) * $quantity['price'];
                    $to_be_shipped_quantities[] = array(
                        'quantity'                  =>  $quantity['quantity'],
                        'order_product_id'          =>  $quantity['order_product_id'],
                        'order_quantity'            =>  $quantity['order_quantity'] - $quantity['shipped_quantity'],
                        'price'                     =>  $quantity['price'],
                        'total'                     =>  ($quantity['order_quantity'] - $quantity['shipped_quantity']) * $quantity['price'],
                        'options'                   =>  $to_be_shipped_options,
                    );
                    $any_qty_remain = $any_qty_remain + ($quantity['order_quantity'] - $quantity['shipped_quantity']);

                }
                $product['quantities'] = $to_be_shipped_quantities;
                if($any_qty_remain < 1) {
                    unset($order->orderProducts[$key]);
                }
                // dd($product);
            }
            $order['total'] = $order_total;
                foreach($order->shippedOrders as $sorder) {
                    foreach($sorder->orderProducts as $k => $sproduct) {
                        $sproduct['image'] = $this->omsProductImage($sproduct->product_id, 300, 300, $sproduct->type);
                        foreach($sproduct->orderProductQuantities as $squantity) {
                            $to_be_shipped_options = array();
                            foreach($squantity->productOptions as $option) {
                                if($option['product_option_id'] == $option_id){
                                    $to_be_shipped_options['static'] = array(
                                        'name'  =>  $option['name'],
                                        'value' =>  $option['value'],
                                    );
                                }else{
                                    $to_be_shipped_options[] = array(
                                        'name'  =>  $option['name'],
                                        'value' =>  $option['value'],
                                    );
                                }
                                // $option[]
                             }
                            }
                    }

                }
        }
        $statuses = array(
            'to_be_shipped' =>  4,
            'shipped'       =>  5,
            'cancelled'     =>  7,
        );
        $counter = $this->productCount();
        $search_form_action = \URL::to('/PurchaseManagement/get/to/be/shipped');
        // dd($orders->toArray());
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
        return view(self::VIEW_DIR.".toBeShipped", ["orders" => $orders->toArray(),"pagination" => $orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "counter" => $counter, "statuses" => $statuses, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
    }

    public function viewConfirmed($order_id) {
        $option_id = OmsSettingsModel::get('product_option','color');
        if($order_id) {
            $order = OmsPurchaseOrdersModel::with(['orderProducts', 'orderTotals' => function($q) {
                $q->orderBy('sort_order', 'ASC');
            },'orderHistories'])->where('order_id', $order_id)->where('order_status_id', 4)->first();

            foreach($order->orderProducts as $k => $product) {
                $units = OmsPurchaseOrdersProductQuantityModel::select(DB::RAW('SUM(order_quantity - shipped_quantity) as unit'))->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                $quantities = OmsPurchaseOrdersProductQuantityModel::with('productOptions')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->get()->toArray();

                $qutites = [];
                $any_qty_remain = 0;
                foreach($quantities as $quantity) {
                    foreach($quantity['product_options'] as $key =>$option) {
                        if($option['product_option_id'] == $option_id){
                            $quantity['product_options'][$key]['static'] = 'static';
                        }else{
                            $quantity['product_options'][$key]['static'] = 'size';
                        }
                    }

                    $any_qty_remain = $any_qty_remain + ($quantity['order_quantity'] - $quantity['shipped_quantity']);
                    $remainquantity = $quantity['order_quantity'] - $quantity['shipped_quantity'];
                    if($remainquantity == 0) {
                        unset($quantity);
                        continue;
                    }
                    array_push($qutites, $quantity);
                }
                $product['quantities'] = $qutites;
                $product['unit'] = $units['unit'];
                if($any_qty_remain < 1) {
                    unset($order->orderProducts[$k]);
                }
            }

        }

        return view(self::VIEW_DIR.".viewConfirmed", ["order" => $order]);
    }

    public function shippedOrders(Request $request) {
        $count = false;
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        // $tabs = $this->orderTabs();
        $search_form_action = \URL::to('/PurchaseManagement/shipped/orders');
        $whereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
            $whereClause[] = ['supplier', session('user_id')];
        }
        $shippedWhereClause = [];
        if($request->order_status_id) {
            $shippedWhereClause[] = ['status', $request->order_status_id];
        }
        $orders = OmsPurchaseOrdersModel::with([
            'orderProducts','orderProducts.orderProductQuantities' => function($qu) {
                $qu->orderBy('order_product_quantity_id', 'ASC');
            },'orderProducts.orderProductQuantities.productOptions' => function($qo) {
                $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },'orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },'orderHistories',
            'shippedOrders' => function($sh) use($shippedWhereClause) {
                $sh->where('status', 2)->where($shippedWhereClause)->orderBy('shipped_order_id', 'ASC');
            },'shippedOrders.orderTotals' => function($q1) {
                $q1->orderBy('sort_order', 'ASC');
            },'shippedOrders.orderProducts','shippedOrders.orderProductQuantities' =>function($soproq) {
                $soproq->orderBy('order_product_quantity_id', 'ASC');
            },'shippedOrders.orderProducts.orderProductQuantities' =>function($soproq) {
                $soproq->orderBy('order_product_quantity_id', 'ASC');
            },'shippedOrders.orderProducts.orderProductQuantities.productOptions' =>function($soproop) {
                $soproop->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
            },'orderSupplier'
        ])->where($whereClause)->whereIn('order_status_id', [4,5])->whereHas('shippedOrders', function ($query) {
            $query->where('status', 2);
            })->orderBy('order_id', 'DESC')->groupBy('order_id')
          ->paginate(self::PER_PAGE)->appends($request->all());
        foreach($orders as $order) {
            $stock_cancel = false;
            foreach($order->orderProducts as $key => $product) {
                $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
            }
            foreach($order['shippedOrders'] as $shipped_order) {
                foreach($shipped_order->orderProducts as $k => $sproduct) {
                    $sproduct['image'] = $this->omsProductImage($sproduct->product_id, 300, 300, $sproduct->type);
                }
                $shipped_order['shipping'] = json_decode($shipped_order['shipping'], true);
                $stock_cancel = OmsPurchaseStockCancelledModel::where(OmsPurchaseStockCancelledModel::FIELD_ORDER_ID, $order['order_id'])->where('shiped_order_id',$shipped_order['shipped_id'])->where(OmsPurchaseStockCancelledModel::FIELD_SUPPLIER, session('user_id'))->where(OmsPurchaseStockCancelledModel::FIELD_STATUS, 0)->exists();

            }
            $order['stock_cancel'] = $stock_cancel;
        }
        // dd($orders->toArray());
        if($count == true){ return $orders->count(); }
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
        return view(self::VIEW_DIR.".shipped", ["orders" => $orders->toArray(), "pagination" => $orders->render(), "suppliers" => $suppliers, 'tabs' => $tabs, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
    }

    public function addToDeliver(Request $request) {
        $option_id = OmsSettingsModel::get('product_option', 'color');
        $search_form_action = \URL::to('/PurchaseManagement/add/to/deliver/orders');
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
        $whereClause = [];
        if($request->order_id) {
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->product_title) {
            $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
        }
        if($request->product_model) {
            $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
        }
        if($request->supplier) {
            $whereClause[] = ['supplier', $request->supplier];
        }
        $shippedWhereClause = [];
        if($request->order_status_id) {
            $shippedWhereClause[] = ['status', $request->order_status_id];
        }
        $orders = OmsPurchaseShippedOrdersModel::with(['orderProducts','orderTotals' => function($q) {
            $q->orderBy('sort_order', 'ASC');
        },'orderHistories','orderSupplier'])->where('status', 2)->where($whereClause)->orderBy('shipped_order_id', 'DESC')->groupBy('shipped_order_id')->paginate(self::PER_PAGE)->appends($request->all());

        foreach($orders as $order) {
            foreach($order['orderProducts'] as $product) {
                $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
                $opencartSku = ProductsModel::select('sku')->where('product_id', $product['product_id'])->first();
                $options = OmsPurchaseShippedOrdersProductOptionModel::select('order_product_quantity_id','product_option_id','product_option_value_id','name','value')->where('shipped_order_id', $order['shipped_order_id'])
                                                                       ->where('order_product_id', $product['product_id'])->groupBy('product_option_id','product_option_value_id')->orderBy('order_product_option_id', 'ASC')
                                                                       ->get()->toArray();
                $ship_options = array();
                if($options && is_array($options)) {
                    foreach($options as $option) {
                        $quantity = OmsPurchaseShippedOrdersProductQuantityModel::select('quantity','received_quantity')->where('shipped_order_id', $order['shipped_order_id'])->where('order_product_id', $product['product_id'])->where('order_product_quantity_id', $option['order_product_quantity_id'])->first()->toArray();
                        if($option['product_option_id'] == $option_id) {
                            $ship_options['static'] = array(
                                'order_product_quantity_id'     =>  $option['order_product_quantity_id'],
                                'product_option_id'             =>  $option['product_option_id'],
                                'product_option_value_id'       =>  $option['product_option_value_id'],
                                'name'                          =>  $option['name'],
                                'value'                         =>  $option['value'],
                                'quantity'                      =>  $quantity['quantity'],
                            );
                        }else {
                            $ship_options[] = array(
                                'order_product_quantity_id'     =>  $option['order_product_quantity_id'],
                                'product_option_id'             =>  $option['product_option_id'],
                                'product_option_value_id'       =>  $option['product_option_value_id'],
                                'name'                          =>  $option['name'],
                                'value'                         =>  $option['value'],
                                'quantity'                      =>  $quantity['quantity'],
                            );
                        }
                    }
                    $product['sku'] = $opencartSku ? $opencartSku->sku : '-';
                    $product['options'] = $ship_options;
                }else{
                    $quantity = OmsPurchaseShippedOrdersProductQuantityModel::select('quantity','received_quantity','order_product_quantity_id')->where('shipped_order_id', $order['shipped_order_id'])->where('order_product_id', $product['product_id'])->first();
                    if($quantity){
                      $quantity = $quantity->toArray();
                    }
                    $product['sku'] = $opencartSku ? $opencartSku->sku : '-';
                    $product['quantity'] = $quantity['quantity'];
                }
            }
        //    $products = OmsPurchaseShippedOrdersProductModel::where('shipped_order_id', $order['shipped_order_id'])
        }
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
        return view(self::VIEW_DIR.".deliver", ["orders" => $orders->toArray(), "pagination" => $orders->render(), "suppliers" => $suppliers, "search_form_action" => $search_form_action, 'tabs' => $tabs, "old_input" => $request->all()]);
    }

    public function UpdateDeliver(Request $request) {
        // dd($request->all());
        $error = false;
        $less_recieved_status = false;
        foreach($request->product as $product) {
            foreach($product['options'] as $quantity) {
                if($quantity['received_quantity'] > $quantity['old_received_quantity']) {
                    $error = true;
                }
                if($quantity['received_quantity'] < $quantity['old_received_quantity']) {
                   $less_recieved_status = true;
                }
            }
        }
        if($error) {
            return redirect()->back()->with('error_message', 'Do not enter greater than order quantity!');
        }
        if($less_recieved_status){
            $this->addToshipAfterLessQuantityRecieved($request->order_id);
        }
        $exist = OmsPurchaseShippedOrdersModel::where('order_id', $request->order_id)->where('shipped_order_id', $request->shipped_order_id)->where('status', 5)->exists();
        if(!$exist) {
            $order_status_id = OmsPurchaseOrdersModel::select('order_status_id')->where('order_id', $request->order_id)->first();
            // dd($order_status_id);
            if($order_status_id->order_status_id != 7) {
                $remain_deliver = OmsPurchaseShippedOrdersModel::select(DB::Raw('COUNT(oms_purchase_shipped_order.shipped_order_id) as total_deliver'))
                                                                 ->join('oms_purchase_order as po', 'po.order_id', '=', 'oms_purchase_shipped_order.order_id')
                                                                 ->where('oms_purchase_shipped_order.order_id', $request->order_id)
                                                                ->whereIn('oms_purchase_shipped_order.status', [2,1])
                                                                ->whereIn('po.order_status_id', [5,7])
                                                                ->first();
                if($remain_deliver->total_deliver == 1 && !$less_recieved_status) {
                    OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(['order_status_id' => 6]);
                    $this->addOrderStatusHistory($request->order_id, 6);
                }
            }
            if($request->instruction){
                $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $request->order_id;
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Admin';
                $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
                $OmsPurchaseOrdersHistoryModel->save();
            }
            $sub_total_amount = 0;
            if($request->product) {
                foreach($request->product as $product) {
                    foreach($product['options'] as $quantity) {
                        $order_quantity_data = OmsPurchaseShippedOrdersProductQuantityModel::select('quantity','received_quantity','price')->where('order_product_quantity_id', $quantity['order_product_quantity_id'])->first();
                        $update_total = $quantity['received_quantity'] * $order_quantity_data->price;
                        $sub_total_amount = $sub_total_amount + $update_total;
                        OmsPurchaseShippedOrdersProductQuantityModel::where('order_product_quantity_id', $quantity['order_product_quantity_id'])
                                                                      ->update(['received_quantity' => $quantity['received_quantity'], 'total' => $update_total]);
                    }
                }
                // order total
                 $supplier = OmsPurchaseOrdersModel::select('supplier')->where('order_id', $request->order_id)->first();
                 $supplier_commission = OmsUserModel::select('commission','commission_on')->where('user_id', (int)$supplier->supplier)->first();
                 $order_totals = OmsPurchaseShippedOrdersTotalModel::select('code','title','value','sort_order')->where('shipped_order_id', $request->shipped_order_id)->orderBy('sort_order','ASC')->get()->toArray();
                 $totals_array = array();
                 $sub_total_value = $sub_total_amount;
                 $local_express_cost_value = 0;
                 $local_shipping_cost_value = 0;
                 $total_value = 0;
                 $commission_value = 0;
                 $grand_total_value = 0;
                 foreach ($order_totals as $key => $value) {
                	if($value['title'] == 'local_express_cost'){
                		$local_express_cost_value = $value['value'];
                	}else if($value['title'] == 'local_shipping_cost'){
                		$local_shipping_cost_value = $value['value'];
                	}
                }
                $total_value = $sub_total_value + $local_express_cost_value + $local_shipping_cost_value;
                $totals_array[] = array(
                    'title' => 'sub_total',
                    'code'  => 'Sub Total',
                    'value' => $sub_total_value,
                    'sort_order' => 1
                );
                $totals_array[] = array(
                    'title'  		=>  'local_express_cost',
					'code'  		=>  'Local Express Cost',
					'value'  		=>  $local_express_cost_value,
					'sort_order'  	=>  2
                );
                $totals_array[] = array(
					'title'  		=>  'local_shipping_cost',
					'code'  		=>  'Local Shipping Cost',
					'value'  		=>  $local_shipping_cost_value,
					'sort_order'  	=>  3
        		);
        		$totals_array[] = array(
					'title'  		=>  'total',
					'code'  		=>  'Total',
					'value'  		=>  $total_value,
					'sort_order'  	=>  4
        		);
                if($supplier_commission && $supplier_commission->commission_on && $supplier_commission->commission_on == 'stock_total'){
                	$commission_value = ($sub_total_value * $supplier_commission->commission) / 100;
            		$totals_array[] = array(
						'title'  		=>  'commission',
						'code'  		=>  $supplier_commission->commission . '% Commission',
						'value'  		=>  $commission_value,
						'sort_order'  	=>  5
            		);
	            }
                $grand_total_value = $total_value + $commission_value;
        		$totals_array[] = array(
					'title'  		=>  'grand_total',
					'code'  		=>  'Grand Total',
					'value'  		=>  $grand_total_value,
					'sort_order'  	=>  $commission_value ? 6 : 5
        		);
                OmsPurchaseShippedOrdersTotalModel::where('shipped_order_id', $request->shipped_order_id)->delete();
                foreach ($totals_array as $total) {
                    $OmsPurchaseShippedOrdersTotalModel = new OmsPurchaseShippedOrdersTotalModel();
                    $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SHIPPED_ORDER_ID} = $request->shipped_order_id;
                    $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_CODE} = $total['code'];
                    $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_TITLE} = $total['title'];
                    $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_VALUE} = $total['value'];
                    $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SORT_ORDER} = $total['sort_order'];
                    $OmsPurchaseShippedOrdersTotalModel->save();
                }
                // End Order Totals
                OmsPurchaseShippedOrdersModel::where('shipped_order_id', $request->shipped_order_id)->update(array('status' => 3));
                $supplier = OmsPurchaseShippedOrdersModel::select('supplier')->where('order_id', $request->order_id)->first();
                $account_summary = OmsAccountSummaryModel::where(OmsAccountSummaryModel::FIELD_USER_ID, $supplier->supplier)->first();

                OmsAccountSummaryModel::where(OmsAccountSummaryModel::FIELD_USER_ID, $supplier->supplier)->update(array('balance' => DB::raw('balance+'. $grand_total_value)));
                $OmsAccountTransactionModel = new OmsAccountTransactionModel();
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_ACCOUNT_ID} = $account_summary->account_id;
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_ORDER_ID} = $request->order_id;
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_DESCRIPTION} = 'Received from Order #'.$request->shipped_id;
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_CREDIT} = $grand_total_value;
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_DEBIT} = 0;
                $OmsAccountTransactionModel->{OmsAccountTransactionModel::FIELD_BALANCE} = ($account_summary->balance + $grand_total_value);
                $OmsAccountTransactionModel->save();
                $this->addInventoryStock($request->order_id, $request->product);
                ?> <script>window.open('/oms/PurchaseManagement/barcode/generate/<?php echo $request->print_label ?>/<?php echo $request->shipped_order_id ?>', '_blank');</script> <?php
                return redirect()->back()->with('message', 'Your Order #'.$request->order_id.' delivered successfully.');
            }else {
                return redirect()->back()->with('message', 'Your order already delivered!');
            }
        }else {
            return redirect()->back()->with('message', 'Something went wrong, please try again!');
        }
    }

    protected function addToshipAfterLessQuantityRecieved($order_id){
        if(count(Input::all()) > 0){
            OmsPurchaseStockCancelledModel::where('order_id', $order_id)->delete();
                $valid = true;
                $ordered_products = OmsPurchaseOrdersProductModel::where("order_id",$order_id)->get()->toArray();

                  if(1){
                    $total_order_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(order_quantity) as total_quantity'))->where('order_id', $order_id)->first()->total_quantity;
                    $total_shipped_orders = OmsPurchaseShippedOrdersModel::select(DB::Raw('count(shipped_order_id) as total_shipped_orders'))->where('order_id', $order_id)->first()->total_shipped_orders;

                    $prev_shipped_data = OmsPurchaseShippedOrdersModel::where('order_id', $order_id)->orderBy("shipped_order_id",'DESC')->first();
                    // dd($prev_shipped_data->toArray());
                    $OmsPurchaseShippedOrdersModel = new OmsPurchaseShippedOrdersModel();
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED_ID} = $order_id . '-' . ($total_shipped_orders + 1);
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_ORDER_ID} = $order_id;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED} = $prev_shipped_data->shipped;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_LINK} = $prev_shipped_data->link;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_URGENT} = $prev_shipped_data->urgent;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SUPPLIER} = $prev_shipped_data->supplier;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPING} = $prev_shipped_data->shipping;
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_TOTAL} = 0;
                    if($prev_shipped_data->shipped == 'dubai'){
                        $shipped_status = 2;
                    }else{
                        $shipped_status = 1;
                    }
                    $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_STATUS} = $shipped_status;
                    $OmsPurchaseShippedOrdersModel->save();

                    $shipped_order_id = $OmsPurchaseShippedOrdersModel->shipped_order_id;
                    $shipped_id = $OmsPurchaseShippedOrdersModel->shipped_id;

                    if(Input::get('instruction')){
                        $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
                        $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
                        $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Supplier';
                        $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = Input::get('instruction');
                        $OmsPurchaseOrdersHistoryModel->save();
                    }

                    $shipped_sub_total = 0;
                    foreach (Input::get('product') as $product_id => $options) {
                        $stop_main_iteration = 1;
                        foreach ($options['options'] as $key => $option) {
                            // $remaining_to_ship =  $option['old_received_quantity'] - $option['received_quantity'];
                            if( ( $option['old_received_quantity'] - $option['received_quantity'] ) > 0 ){
                                $stop_main_iteration = 0;
                                break;
                            }
                        }
                        if($stop_main_iteration){
                            continue;
                        }
                        $order_product_type = OmsPurchaseOrdersProductModel::select('*')->where('product_id', $product_id)->where('order_id', $order_id)->first();
                        $product_name = $product_model = '';
                        if($order_product_type){
                            $product_model = $order_product_type->model;
                            $product_name = $order_product_type->name;
                        }
                        $OmsPurchaseShippedOrdersProductModel = new OmsPurchaseShippedOrdersProductModel();
                        $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_SHIPPED_ORDER_ID} = $shipped_order_id;
                        $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_PRODUCT_ID} = $product_id;
                        $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_NAME} = $product_name;
                        $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_MODEL} = $product_model;
                        $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_TYPE} =$order_product_type ? $order_product_type->type : 'opencart';
                        $OmsPurchaseShippedOrdersProductModel->save();

                        foreach ($options['options'] as $key => $option) {
                            $remaining_to_ship =  $option['old_received_quantity'] - $option['received_quantity'];
                            if( $remaining_to_ship < 1 ) continue;
                            $quantity_details = OmsPurchaseShippedOrdersProductQuantityModel::select('*')->where('order_product_quantity_id', $option['order_product_quantity_id'])->first()->toArray();

                            $shipped_total = $remaining_to_ship * $quantity_details['price'];
                            $shipped_sub_total = $shipped_sub_total + $shipped_total;

                            $OmsPurchaseShippedOrdersProductQuantityModel = new OmsPurchaseShippedOrdersProductQuantityModel();
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_SHIPPED_ORDER_ID} = $shipped_order_id;
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_QUANTITY} = $remaining_to_ship;
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_RECEIVED_QUANTITY} = 0;
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_PRICE} = $quantity_details['price'];
                            $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_TOTAL} = $shipped_total;
                            $OmsPurchaseShippedOrdersProductQuantityModel->save();

                            $quantity_id = $OmsPurchaseShippedOrdersProductQuantityModel->order_product_quantity_id;

                            $required_order_product_quantity_id = OmsPurchaseOrdersProductOptionModel::select('order_product_quantity_id')
                                ->where('order_product_id', $product_id)->where('order_id',$order_id)->where('product_option_id',$option['product_option_id'])->where('product_option_value_id',$option['product_option_value_id'])
                                ->where('name',$option['option_name'])->where('value',$option['option_value'])->first();
                                if(!empty($required_order_product_quantity_id)){
                                    $options_details = OmsPurchaseOrdersProductOptionModel::select('*')->where('order_product_quantity_id',$required_order_product_quantity_id->order_product_quantity_id)->get()->toArray();
                                }
                            if($options_details){
                                foreach ($options_details as $quantity_option) {
                                    $OmsPurchaseShippedOrdersProductOptionModel = new OmsPurchaseShippedOrdersProductOptionModel();
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_QUANTITY_ID} = $quantity_id;
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_SHIPPED_ORDER_ID} = $shipped_order_id;
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_ID} = $product_id;
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_ID} = $quantity_option['product_option_id'];
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_VALUE_ID} = $quantity_option['product_option_value_id'];
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_NAME} = $quantity_option['name'];
                                    $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_VALUE} = $quantity_option['value'];
                                    $OmsPurchaseShippedOrdersProductOptionModel->save();
                                }
                            }
                            // for supplier shipped issue the below query is commented
                            // OmsPurchaseOrdersProductQuantityModel::where('order_product_quantity_id', $option['order_product_quantity_id'])->update(array('shipped_quantity' => DB::raw('shipped_quantity+'.$remaining_to_ship)));
                        }
                    }

                    $order_totals = OmsPurchaseOrdersTotalModel::select('code','title','value','sort_order')->where('order_id', $order_id)->orderBy('sort_order', 'ASC')->get()->toArray();
                    $local_shipping = array(
                        'code'  =>  'Local Shipping Cost',
                        'title'  =>  'local_shipping_cost',
                        'value'  =>  0, ///local cost to be from
                        'sort_order'  =>  count($order_totals)
                    );
                    $main_total = end($order_totals);
                    $main_total['value'] = $shipped_sub_total;
                    foreach ($order_totals as $key => $value) {
                        if($value['title'] === 'sub_total'){
                            $order_totals[$key]['value'] = $shipped_sub_total;
                        }
                        if($value['title'] === 'local_express_cost'){
                            $value['value'] = 0;
                            $main_total['value'] = $main_total['value'] + $value['value'];
                        }
                    }
                    $main_total['value'] = $main_total['value'] + $local_shipping['value'];
                    $main_total['sort_order'] = count($order_totals) + 1;
                    array_pop($order_totals);
                    array_push($order_totals, $local_shipping);
                    array_push($order_totals, $main_total);
                    foreach ($order_totals as $total) {
                        if($total['title'] == "local_express_cost"){
                            $valuee = 0;
                        }else{
                            $valuee = $total['value'];
                        }
                        $OmsPurchaseShippedOrdersTotalModel = new OmsPurchaseShippedOrdersTotalModel();
                        $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SHIPPED_ORDER_ID} = $shipped_order_id;
                        $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_CODE} = $total['code'];
                        $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_TITLE} = $total['title'];
                        $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_VALUE} = $valuee;
                        $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SORT_ORDER} = $total['sort_order'];
                        $OmsPurchaseShippedOrdersTotalModel->save();
                    }

                    OmsPurchaseShippedOrdersModel::where('shipped_order_id', $shipped_order_id)->update(array('total' => $main_total['value']));
                    $total_shipped_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(shipped_quantity) as total_quantity'))->where('order_id', $order_id)->first()->total_quantity;
                    $order_status_id = OmsPurchaseOrdersModel::select('order_status_id')->where('order_id', $order_id)->first();

                if($order_status_id->order_status_id !== 1){
                    $forwarder_count = OmsPurchaseShippedOrdersModel::select(DB::raw("COUNT(shipped_order_id) as total"))->where('order_id', $order_id)->where('status', 1)->first();

                    if(($total_order_quantity == $total_shipped_quantity && $forwarder_count->total == 0) || $prev_shipped_data->shipped == 'dubai'){

                    if($total_order_quantity == $total_shipped_quantity){

                        OmsPurchaseOrdersModel::where('order_id', $order_id)->update(array('order_status_id'   => 5));
                        $this->addOrderStatusHistory($order_id, 5);
                    }else{

                        OmsPurchaseOrdersModel::where('order_id', $order_id)->update(array('order_status_id'   => 4));
                        $this->addOrderStatusHistory($order_id, 4);
                    }

                    return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$order_id.' shipped successfully.');

                    }else{

                        $test=  OmsPurchaseOrdersModel::where('order_id', $order_id)->update(array('order_status_id'   => 4));
                        $this->addOrderStatusHistory($order_id, 4);
                        return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$order_id.' shipped successfully.');

                    }

        }else{
            return redirect()->route('confirmed.purchase.orders')->with('message', 'Your Order #'.$shipped_id.' shipped successfully.');
        }
    }else{
        return redirect()->route('add.to.ship')->with('message', 'Do not enter greater than order quantity!');
    }

 }

}

public function deliveredOrders(Request $request) {
    $count = false;
    $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
    $search_form_action = \URL::to('/PurchaseManagement/delivered/orders');
    $whereClause = [];
    $relationWhereClause = [];
    if($request->order_id) {
        $whereClause[] = ['order_id', $request->order_id];
    }
    if($request->product_title) {
        $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
    }
    if($request->product_model) {
        $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
    }
    if($request->supplier) {
        $whereClause[] = ['supplier', $request->supplier];
    }
    if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
        $whereClause[] = ['supplier', session('user_id')];
    }
    $shippedWhereClause = [];
        if($request->order_status_id) {
            $shippedWhereClause[] = ['status', $request->order_status_id];
        }

    $orders = OmsPurchaseOrdersModel::with([
        'orderProducts','orderProductQuantities' => function($q) {
            $q->orderBy('order_product_quantity_id', 'ASC');
        },
        'orderProducts.orderProductQuantities' => function($qu) {
            $qu->orderBy('order_product_quantity_id', 'ASC');
        },'orderProducts.orderProductQuantities.productOptions' => function($qo) {
            $qo->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
        },'orderTotals' => function($q1) {
            $q1->orderBy('sort_order', 'ASC');
        },'orderHistories',
        'shippedOrders' => function($sh) use($shippedWhereClause) {
            $sh->where('status', 3)->where($shippedWhereClause)->orderBy('shipped_order_id', 'ASC');
        },'shippedOrders.orderTotals' => function($q1) {
            $q1->orderBy('sort_order', 'ASC');
        },'shippedOrders.orderProducts','shippedOrders.orderProductQuantities' =>function($soproq) {
            $soproq->orderBy('order_product_quantity_id', 'ASC');
        },'shippedOrders.orderProducts.orderProductQuantities' =>function($soproq) {
            $soproq->orderBy('order_product_quantity_id', 'ASC');
        },'shippedOrders.orderProducts.orderProductQuantities.productOptions' =>function($soproop) {
            $soproop->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC');
        },'orderSupplier'
    ])->where($whereClause)->whereIn('order_status_id', [4,5,6])->whereHas('shippedOrders', function ($query) {
        $query->where('status', 3);
        })->orderBy('order_id', 'DESC')->groupBy('order_id')
      ->paginate(self::PER_PAGE)->appends($request->all());
      foreach($orders as $order) {
        $order['status_history'] = $this->statusHistory($order['order_id']);
        $stock_cancel = false;
        foreach($order->orderProducts as $key => $product) {
            $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
        }
        foreach($order['shippedOrders'] as $shipped_order) {
            foreach($shipped_order->orderProducts as $k => $sproduct) {
                $sproduct['image'] = $this->omsProductImage($sproduct->product_id, 300, 300, $sproduct->type);
            }
            $shipped_order['shipping'] = json_decode($shipped_order['shipping'], true);
        }
    }
    // dd($orders->toArray());
    $order_statuses = OmsPurchaseOrdersStatusModel::get()->toArray();
    $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
    return view(self::VIEW_DIR.".delivered", ["orders" => $orders->toArray(), "pagination" => $orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "search_form_action" => $search_form_action, 'order_statuses' => $order_statuses, "old_input" => $request->all()]);

}

public function cancelledOrders(Request $request) {
    $count = false;
    $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();
    $search_form_action = \URL::to('/PurchaseManagement/cancelled/orders');
    $option_id = OmsSettingsModel::get('product_option','color');
    $whereClause = [];
    $relationWhereClause = [];
        if($request->order_id){
            if($request->action != 'shipped') {
                $whereClause[] = ['oms_purchase_order.order_id', $request->order_id];
            }else {
                $whereClause[] = ['oms_purchase_shipped_order.order_id', $request->order_id];
            }

        }
    if($request->title) {
        $relationWhereClause[] = ['name', 'LIKE', '%'. $request->product_title . '%'];
    }
    if($request->model) {
        $relationWhereClause[] = ['model', 'LIKE', $request->product_model .'%'];
    }
    if($request->supplier) {
        $whereClause[] = ['supplier', $request->supplier];
    }
    if(session('role') != 'ADMIN' && session('role') != 'STAFF') {
        $whereClause[] = ['supplier', session('user_id')];
    }
    // dd($whereClause);
    if($request->action != 'shipped') {
        $orders = OmsPurchaseOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },'orderSupplier'
        ])->where($whereClause)->where('order_status_id', 7)->orderBy('order_id', 'DESC')
        ->paginate(self::PER_PAGE)->appends($request->all());
    }else {
        $orders = OmsPurchaseShippedOrdersModel::with([
            'orderProducts' => function($q) use($relationWhereClause) {
                $q->where($relationWhereClause);
            },'orderSupplier'
        ])->where($whereClause)->where('status', 5)->orderBy('shipped_order_id', 'DESC')
        ->paginate(self::PER_PAGE)->appends($request->all());
    }
      foreach($orders as $order) {
          foreach($order->orderProducts as $product) {
              if($request->action != 'shipped') {
                    $options = OmsPurchaseOrdersProductOptionModel::select('order_product_quantity_id','product_option_id','name','value')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC')->get()->toArray();
                }else {
                    $options = OmsPurchaseShippedOrdersProductOptionModel::select('order_product_quantity_id', 'product_option_id','name','value')->where('shipped_order_id', $product['shipped_order_id'])->where('order_product_id', $product['product_id'])->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC')->get()->toArray();
                }
              if($options) {
                $cancelled_order_options = array();
                foreach($options as $option) {
                    if($request->action != 'shipped') {
                         $quantity = OmsPurchaseOrdersProductQuantityModel::select('quantity','order_quantity','shipped_quantity','received_quantity','price','total')->where('order_product_quantity_id', $option['order_product_quantity_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                       }else {
                         $quantity = OmsPurchaseShippedOrdersProductQuantityModel::select('quantity','received_quantity','price','total')->where('order_product_quantity_id', $option['order_product_quantity_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                       }
                       if($option['product_option_id'] != $option_id) {
                           $cancelled_order_options[] = array(
                               'name'              => $option['name'],
                               'value'             =>  $option['value'],
                               'quantity'          =>  $quantity['quantity'],
                               'order_quantity'    =>  ($request->action != 'shipped') ? $quantity['order_quantity'] : 0,
                               'remain_quantity'   =>  ($request->action != 'shipped') ? $quantity['order_quantity'] - $quantity['shipped_quantity'] : 0,
                               'price'             =>  $quantity['price'],
                               'total'             =>  $quantity['total'],
                           );
                       }
                }
                $product['options'] = $cancelled_order_options;
              }
              $product['image'] = $this->omsProductImage($product['product_id'], 300, 300,$product['type']);
          }
      }
    //   dd($orders->toArray());
      $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
      return view(self::VIEW_DIR.".cancelledOrders", ["orders" => $orders->toArray(), "pagination" => $orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
}

public function shippedStockCancelledRequests() {
    $option_id = OmsSettingsModel::get('product_option','color');
    $whereClause = [];
    if(Input::all() > 0){
        if(Input::get('order_id')){
            $whereClause[] = ['order_id', Input::get('order_id')];
        }
        if(Input::get('supplier')){
            $whereClause[] = ['supplier', Input::get('supplier')];
        }
    }
    $orders = OmsPurchaseStockCancelledModel::with(['shippedOrder','shippedOrder.orderProducts','shippedOrder.orderProducts.ProductsSizes','orderSupplier'])->where('status', 0)->whereNotNull('shiped_order_id')->whereNot('shiped_order_id', '')->where($whereClause)->paginate(self::PER_PAGE)->appends(Input::all());
    foreach ($orders as $order) {
        foreach($order->shippedOrder->orderProducts as $product) {
            $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
            $stock_cancelled_order_options = array();
            foreach($product->ProductsSizes as $k => $option) {
                $quantity = OmsPurchaseShippedOrdersProductQuantityModel::select('quantity','received_quantity','price','total')->where('order_product_quantity_id', $option['order_product_quantity_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                if($option['product_option_id'] != $option_id){
                    $stock_cancelled_order_options[] = array(
                        'name'              =>  $option['name'],
                        'value'             =>  $option['value'],
                        'order_quantity'    =>  $quantity['quantity'],
                        // 'remain_quantity'   =>  $quantity['order_quantity'] - $quantity['shipped_quantity'],
                        'price'             =>  $quantity['price'],
                        'total'             =>  $quantity['total'],
                    );
                }
                $product->ProductsSizes[$k] = $stock_cancelled_order_options;
            }

        }
    }
    $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
    return view(self::VIEW_DIR.".stockCancelRequests", ["orders" => $orders->toArray(), "suppliers" => $suppliers, "pagination" => $orders->render(), "old_input" => Input::all()]);
}

public function toBeShippedStockCancelledRequests(Request $request) {
    $option_id = OmsSettingsModel::get('product_option','color');
    $whereClause = [];
    if($request->all() > 0){
        if($request->order_id){
            $whereClause[] = ['order_id', $request->order_id];
        }
        if($request->supplier){
            $whereClause[] = ['supplier', $request->supplier];
        }
    }
    $orders = OmsPurchaseStockCancelledModel::with(['purchasedOrder','purchasedOrder.orderProducts','orderSupplier'])->where('status', 0)->whereNull('shiped_order_id')->orWhere('shiped_order_id', '')->where($whereClause)->paginate(self::PER_PAGE)->appends(Input::all());

    foreach ($orders as $kk => $order) {
        foreach($order->purchasedOrder->orderProducts as $key => $product) {
            $product['image'] = $this->omsProductImage($product['product_id'],300,300,$product['type']);
            $stock_cancelled_order_options = array();
            $options = OmsPurchaseOrdersProductOptionModel::select('order_product_quantity_id','product_option_id','name','value')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC')->get()->toArray();

            if($options && count($options) > 0) {
                foreach($options as $k => $option) {
                    $quantity = OmsPurchaseOrdersProductQuantityModel::select('quantity','order_quantity','shipped_quantity','received_quantity','price','total')->where('order_product_quantity_id', $option['order_product_quantity_id'])->where('order_product_id', $product['product_id'])->first()->toArray();
                    if($option['product_option_id'] != $option_id){
                        $stock_cancelled_order_options[] = array(
                            'name'              =>  $option['name'],
                            'value'             =>  $option['value'],
                            'order_quantity'    =>  $quantity['quantity'],
                            'remain_quantity'   =>  $quantity['order_quantity'] - $quantity['shipped_quantity'],
                            'price'             =>  $quantity['price'],
                            'total'             =>  $quantity['total'],
                        );
                    }
                }
                if($stock_cancelled_order_options && count($stock_cancelled_order_options) > 0) {
                    $order->purchasedOrder->orderProducts[$key]['products_sizes'] = $stock_cancelled_order_options;
                }else {
                    $order->purchasedOrder->orderProducts[$key]['order_quantity']  =  $quantity['order_quantity'];
                    $order->purchasedOrder->orderProducts[$key]['remain_quantity'] =  $quantity['order_quantity'] - $quantity['shipped_quantity'];
                    $order->purchasedOrder->orderProducts[$key]['price']           =  $quantity['price'];
                    $order->purchasedOrder->orderProducts[$key]['total']           =  $quantity['total'];
                }
            }else {
                $quantity = OmsPurchaseOrdersProductQuantityModel::select('quantity','order_quantity','shipped_quantity','received_quantity','price','total')->where('order_product_id', $product['product_id'])->where('order_id', $order['order_id'])->first()->toArray();
                $order->purchasedOrder->orderProducts[$key]['order_quantity']  =  $quantity['order_quantity'];
                $order->purchasedOrder->orderProducts[$key]['remain_quantity'] =  $quantity['order_quantity'] - $quantity['shipped_quantity'];
                $order->purchasedOrder->orderProducts[$key]['price']           =  $quantity['price'];
                $order->purchasedOrder->orderProducts[$key]['total']           =  $quantity['total'];
            }
        }
    }
    $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
    return view(self::VIEW_DIR.".toBeShippedStockCancelRequests", ["orders" => $orders->toArray(), "suppliers" => $suppliers, "pagination" => $orders->render(), "old_input" => $request->all()]);
}

public function addComplaint(Request $request) {
    $option_id = OmsSettingsModel::get('product_option','color');
        $missing_order = array();
        $whereClause = [];
        if($request->all() > 0){
            if($request->order_id){
                $whereClause[] = ['shipped_id', $request->order_id];
            }
            if($request->supplier){
                $whereClause[] = ['supplier', $request->supplier];
            }
        }
        $orders = OmsPurchaseShippedOrdersModel::join('oms_purchase_shipped_order_product_quantity as psopq', 'psopq.shipped_order_id', '=', 'oms_purchase_shipped_order.shipped_order_id')
                    ->where('oms_purchase_shipped_order.status', 3)
                    ->having(DB::Raw('SUM(psopq.quantity)'), '!=', DB::Raw('SUM(psopq.received_quantity)'))->where($whereClause)
                    ->groupBy('oms_purchase_shipped_order.shipped_order_id')->orderBy('oms_purchase_shipped_order.shipped_order_id','DESC')
                    ->paginate(self::PER_PAGE)->appends(Input::all());
                        //  dd($orders->toArray());
                    if($orders){
                        foreach ($orders as $key => $order) {
                            $order = $order->toArray();
                            $products = OmsPurchaseShippedOrdersProductModel::select('*')->where('shipped_order_id', $order['shipped_order_id'])->get()->toArray();

                            $missing_order_products = array();
                            foreach ($products as $key => $product) {
                                $quantities = OmsPurchaseShippedOrdersProductQuantityModel::select('*')->where('shipped_order_id', $order['shipped_order_id'])->where('order_product_id', $product['product_id'])->orderBy('order_product_quantity_id', 'ASC')->get()->toArray();

                                $missing_order_quantities = array();
                                foreach ($quantities as $quantity) {
                                    $options = OmsPurchaseShippedOrdersProductOptionModel::select('name','value')->where('order_product_quantity_id', $quantity['order_product_quantity_id'])->orderBy('name', 'ASC')->orderBy('order_product_option_id', 'ASC')->get()->toArray();

                                    $missing_order_options = array();
                                    foreach ($options as $option) {
                                        $missing_order_options[] = array(
                                            'name'  =>  $option['name'],
                                            'value' =>  $option['value'],
                                        );
                                    }
                                    $missing_order_quantities[] = array(
                                        'order_product_quantity_id' =>  $quantity['order_product_quantity_id'],
                                        'quantity'  =>  $quantity['quantity'],
                                        'received'  =>  $quantity['received_quantity'],
                                        'remain'    =>  ($quantity['quantity'] - $quantity['received_quantity']),
                                        'price'     =>  $quantity['price'],
                                        'total'     =>  $quantity['total'],
                                        'options'   =>  $missing_order_options,
                                    );
                                }
                                $missing_order_products[] = array(
                                    'product_id'    =>  $product['product_id'],
                                    'image'         =>  $this->get_product_image($product['type'], $product['product_id'], 300, 300),
                                    'name'          =>  $product['name'],
                                    'model'         =>  $product['model'],
                                    'quantities'    =>  $missing_order_quantities,
                                );
                            }

                            $complaint = OmsPurchaseComplaintOrderModel::select('comment')->where(OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID, $order['shipped_id'])->first();
                            $complaint_history = array();
                            if($complaint){
                                $complaint_history = json_decode($complaint->comment,true);
                            }
                            $supplier = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_id', $order['supplier'])->first();
                            $missing_order[] = array(
                                'shipped_order_id'  =>  $order['shipped_order_id'],
                                'shipped_id'        =>  $order['shipped_id'],
                                'order_id'          =>  $order['order_id'],
                                'supplier'          =>  $supplier ? $supplier->toArray() : array(),
                                'shipped'           =>  $order['shipped'],
                                'link'              =>  $order['link'],
                                'urgent'            =>  $order['urgent'],
                                'ship_by_sea'       =>  $order['ship_by_sea'],
                                'order_status_id'   =>  $order['status'],
                                'total'             =>  $order['total'],
                                'history'           =>  $complaint_history,
                                'products'          =>  $missing_order_products,
                            );
                        }
                    }
                    $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
                    return view(self::VIEW_DIR.".addComplaint", ["orders" => $missing_order, "suppliers" => $suppliers, "pagination" => $orders->render(), "old_input" => Input::all()]);
}

public function updateComplaintOrder(Request $request) {
    if($request->all() > 0 && $request->submit == 'update_complaint_order'){
        $exists = OmsPurchaseComplaintOrderModel::where(OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID, $request->shipped_id)->exists();
        if(!$exists){
            $comment[] = array('Admin' => $request->comment);
            $OmsPurchaseComplaintOrderModel = new OmsPurchaseComplaintOrderModel();
            $OmsPurchaseComplaintOrderModel->{OmsPurchaseComplaintOrderModel::FIELD_ORDER_ID} = $request->order_id;
            $OmsPurchaseComplaintOrderModel->{OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID} = $request->shipped_id;
            $OmsPurchaseComplaintOrderModel->{OmsPurchaseComplaintOrderModel::FIELD_SUPPLIER} = $request->supplier;
            $OmsPurchaseComplaintOrderModel->{OmsPurchaseComplaintOrderModel::FIELD_COMMENT} = json_encode($comment);
            $OmsPurchaseComplaintOrderModel->save();

            return redirect()->route('add.complaint')->with('message', 'Complaint For Order #'.$request->shipped_id.' added successfully.');
        }else{
            $complaint = OmsPurchaseComplaintOrderModel::select(OmsPurchaseComplaintOrderModel::FIELD_COMMENT)->where(OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID, $request->shipped_id)->first();
            if($complaint){
                $comment = json_decode($complaint->comment,true);
                $comment_new[] = array('Admin' => $request->comment);
                $update_comment = json_encode(array_merge($comment,$comment_new));

                OmsPurchaseComplaintOrderModel::where(OmsPurchaseComplaintOrderModel::FIELD_ORDER_ID, $request->order_id)->where(OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID, $request->shipped_id)->update(array(OmsPurchaseComplaintOrderModel::FIELD_COMMENT => $update_comment));
            }else{
                $comment[] = array('Admin' => $request->comment);
                OmsPurchaseComplaintOrderModel::where(OmsPurchaseComplaintOrderModel::FIELD_ORDER_ID, $request->order_id)->where(OmsPurchaseComplaintOrderModel::FIELD_SHIPPED_ID, $request->shipped_id)->update(array(OmsPurchaseComplaintOrderModel::FIELD_COMMENT => json_encode($comment) ));
            }
            return redirect()->route('add.complaint')->with('message', 'Comment For Order #'.$request->shipped_id.' added successfully.');
        }
    }else{
        return redirect()->route('add.complaint')->with('message', 'Something went wrong, please try again!');
    }
}

public function updateStockCancelOrderRequest(Request $request) {
    if($request->all() > 0 && $request->submit == 'update_stock_cancelled'){

        OmsPurchaseStockCancelledModel::where('order_id', $request->order_id)->where('supplier', $request->supplier)->where('shiped_order_id', $request->shiped_order_id)->update(array('status' => 1));
        OmsPurchaseShippedOrdersModel::where('order_id', $request->order_id)->where('shipped_id', $request->shiped_order_id)->update(array('status' => 5));

        $this->addOrderStatusHistory($request->shiped_order_id, 5);
        return redirect()->back()->with('message', 'Your Order #'.$request->shiped_order_id.' cancelled successfully.');
    }elseif( $request->all() > 0 && $request->submit == 'cancel_stock_cancelled' ){
      OmsPurchaseStockCancelledModel::where('order_id', $request->order_id)->where('supplier', $request->supplier)->where('shiped_order_id', $request->shiped_order_id)->delete();

      return redirect()->back()->with('message', 'Your Order #'.$request->shiped_order_id.' Disapproved successfully.');
    }
}

public function updateToBeStockCancelOrderRequest(Request $request) {
      $order_id = $request->order_id;
      $supplier = $request->supplier;
      if( $request->submit == 'approve_stock_cancelled' ){
        $this->add_to_ship_for_cancelation($order_id, $supplier);
        return redirect()->back();
      }elseif( $request->submit == 'cancel_stock_cancelled' ){
        OmsPurchaseStockCancelledModel::where('order_id', $order_id)->whereNull('shiped_order_id')->delete();
        return redirect()->back()->with('message', 'Your Order #'.$order_id.' Request cancelled successfully.');
      }
}

public function add_to_ship_for_cancelation($main_order_id, $supplier){
    if( $main_order_id != "" && $main_order_id > 0 ){
      $total_order_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(order_quantity) as total_quantity'))->where('order_id', $main_order_id)->first()->total_quantity;
      $total_shipped_orders = OmsPurchaseShippedOrdersModel::select(DB::Raw('count(shipped_order_id) as total_shipped_orders'))->where('order_id', $main_order_id)->first()->total_shipped_orders;
      $shipping['cancel'] = array(
        'name'      =>  'cancel',
        'tracking'  =>  'cancel',
        'date'      =>  '',
      );
      $OmsPurchaseShippedOrdersModel = new OmsPurchaseShippedOrdersModel();
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED_ID} = $main_order_id . '-' . ($total_shipped_orders + 1);
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_ORDER_ID} = $main_order_id;
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPED} = 'canceled';
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_LINK} = '';
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_URGENT} = 0;
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SUPPLIER} = $supplier;
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_SHIPPING} = json_encode($shipping);
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_TOTAL} = 0;
      $shipped_status = 2;
      $OmsPurchaseShippedOrdersModel->{OmsPurchaseShippedOrdersModel::FIELD_STATUS} = $shipped_status;
      $OmsPurchaseShippedOrdersModel->save();

      $order_id = $OmsPurchaseShippedOrdersModel->shipped_order_id;
      $shipped_id = $OmsPurchaseShippedOrdersModel->shipped_id;

      $shipped_sub_total = 0;
      $ordered_products = OmsPurchaseOrdersProductModel::where('order_id',$main_order_id)->get();
      foreach ($ordered_products as $ordered_product) {

          $OmsPurchaseShippedOrdersProductModel = new OmsPurchaseShippedOrdersProductModel();
          $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
          $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_PRODUCT_ID} = $ordered_product->product_id;
          $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_NAME} = $ordered_product->name;
          $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_MODEL} = $ordered_product->model;
          $OmsPurchaseShippedOrdersProductModel->{OmsPurchaseShippedOrdersProductModel::FIELD_TYPE} = $ordered_product->type;
          $OmsPurchaseShippedOrdersProductModel->save();
          $ordered_quantities = OmsPurchaseOrdersProductQuantityModel::select('*')->where('order_id', $main_order_id)->where('order_product_id', $ordered_product->product_id)->get();
          foreach ($ordered_quantities as $ordered_quantity) {
              $shipped_quantity = $ordered_quantity->order_quantity - $ordered_quantity->shipped_quantity;
              if($shipped_quantity < 1) continue;
              $shipped_total  = $shipped_quantity * $ordered_quantity->price;
              $shipped_sub_total = $shipped_sub_total + $shipped_total;
              $OmsPurchaseShippedOrdersProductQuantityModel = new OmsPurchaseShippedOrdersProductQuantityModel();
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_ORDER_PRODUCT_ID} = $ordered_quantity->order_product_id;
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_QUANTITY} = $shipped_quantity;
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_RECEIVED_QUANTITY} = 0;
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_PRICE} = $ordered_quantity->price;
              $OmsPurchaseShippedOrdersProductQuantityModel->{OmsPurchaseShippedOrdersProductQuantityModel::FIELD_TOTAL} = $shipped_total;
              $OmsPurchaseShippedOrdersProductQuantityModel->save();

              $quantity_id = $OmsPurchaseShippedOrdersProductQuantityModel->order_product_quantity_id;

              $options_details = OmsPurchaseOrdersProductOptionModel::select('*')->where('order_product_quantity_id', $ordered_quantity->order_product_quantity_id)->get()->toArray();
              if($options_details){
                  foreach ($options_details as $quantity_option) {
                      $OmsPurchaseShippedOrdersProductOptionModel = new OmsPurchaseShippedOrdersProductOptionModel();
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_QUANTITY_ID} = $quantity_id;
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_ORDER_PRODUCT_ID} = $ordered_quantity->order_product_id;
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_ID} = $quantity_option['product_option_id'];
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_PRODUCT_OPTION_VALUE_ID} = $quantity_option['product_option_value_id'];
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_NAME} = $quantity_option['name'];
                      $OmsPurchaseShippedOrdersProductOptionModel->{OmsPurchaseShippedOrdersProductOptionModel::FIELD_VALUE} = $quantity_option['value'];
                      $OmsPurchaseShippedOrdersProductOptionModel->save();
                  }
              }

              OmsPurchaseOrdersProductQuantityModel::where('order_product_quantity_id', $ordered_quantity->order_product_quantity_id)->update(array('shipped_quantity' => DB::raw('shipped_quantity+'.$shipped_quantity)));
          }

      }

      $order_totals = OmsPurchaseOrdersTotalModel::select('code','title','value','sort_order')->where('order_id', $main_order_id)->orderBy('sort_order', 'ASC')->get()->toArray();
      $local_shipping = array(
          'code'  =>  'Local Shipping Cost',
          'title'  =>  'local_shipping_cost',
          'value'  =>  0,
          'sort_order'  =>  count($order_totals)
      );
      $main_total = end($order_totals);
      $main_total['value'] = $shipped_sub_total;
      foreach ($order_totals as $key => $value) {
          if($value['title'] === 'sub_total'){
              $order_totals[$key]['value'] = $shipped_sub_total;
          }
          if($value['title'] === 'local_express_cost'){
              $main_total['value'] = $main_total['value'] + $value['value'];
          }
      }
      $main_total['value'] = $main_total['value'] + $local_shipping['value'];
      $main_total['sort_order'] = count($order_totals) + 1;
      array_pop($order_totals);
      array_push($order_totals, $local_shipping);
      array_push($order_totals, $main_total);
      foreach ($order_totals as $total) {
          $value_amount = $total['value'];

          $OmsPurchaseShippedOrdersTotalModel = new OmsPurchaseShippedOrdersTotalModel();
          $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SHIPPED_ORDER_ID} = $order_id;
          $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_CODE} = isset($total['code']) ? $total['code'] : '';
          $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_TITLE} = isset($total['title']) ? $total['title'] : '';
          $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_VALUE} = $value_amount;
          $OmsPurchaseShippedOrdersTotalModel->{OmsPurchaseShippedOrdersTotalModel::FIELD_SORT_ORDER} = $total['sort_order'];
          $OmsPurchaseShippedOrdersTotalModel->save();
      }

      OmsPurchaseShippedOrdersModel::where('shipped_order_id', $order_id)->update(array('total' => $main_total['value']));
      $total_shipped_quantity = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('sum(shipped_quantity) as total_quantity'))->where('order_id', Input::get('order_id'))->first()->total_quantity;
      $order_status_id = OmsPurchaseOrdersModel::select('order_status_id')->where('order_id', $main_order_id)->first();

      if($order_status_id->order_status_id !== 7){
          $forwarder_count = OmsPurchaseShippedOrdersModel::select(DB::raw("COUNT(shipped_order_id) as total"))->where('order_id', $main_order_id)->where('status', 1)->first();

          if(($total_order_quantity == $total_shipped_quantity && $forwarder_count->total == 0)){
            // || Input::get('shipped') == 'dubai'
            OmsPurchaseOrdersModel::where('order_id', $main_order_id)->update(array('order_status_id'   => 5));

            $this->addOrderStatusHistory($main_order_id, 5);
              $this->cancelFromToBeShipped($main_order_id,$shipped_id);
              return redirect()->route('get.shipped.orders')->with('message', 'Your Order #'.$shipped_id.' shipped and Approved successfully.');
          }else{
              OmsPurchaseOrdersModel::where('order_id', $main_order_id)->update(array('order_status_id'   => 5));

              $this->addOrderStatusHistory($main_order_id, 5);
              $this->cancelFromToBeShipped($main_order_id,$shipped_id);
              return redirect()->route('get.shipped.orders')->with('message', 'Your Order #'.$shipped_id.' shipped and Approved successfully.');
          }

      }else{
          $this->cancelFromToBeShipped($main_order_id,$shipped_id);
          return redirect()->route('get.shipped.orders')->with('message', 'Your Order #'. $shipped_id .' shipped and Approved  successfully.');
      }
    }
  }
  protected function cancelFromToBeShipped($main_order_id,$shipped_id){
    OmsPurchaseStockCancelledModel::where('order_id', $main_order_id)->whereNull('shiped_order_id')->update(array('status' => 1));
    $shipped_orders = OmsPurchaseShippedOrdersModel::where('order_id', $main_order_id)
                                                          ->where('shipped_id', $shipped_id)
                                                          ->update(array('status' => 5));
    $this->addOrderStatusHistory($shipped_id, 5);
  }

protected function addInventoryStock($order_id, $products){
      foreach ($products as $product_id => $value) {
          $product_data = OmsPurchaseOrdersProductModel::select('type','model')->where('order_id', $order_id)->where('product_id', $product_id)->first();
          $omsProduct = OmsInventoryProductModel::select('option_value AS size','option_name AS Color','product_id','sku')->where('sku',$product_data->model)->first();

          if($omsProduct){
              if($omsProduct->size != 0){
          $quantity_data = "";
          $total_quantity = 0;
          foreach ($value['options'] as $key => $val) {
            $total_quantity = $total_quantity + $val['received_quantity'];
            $quantity_data .= $val['option_value'] . "-(" . $val['received_quantity'] . "), ";

            OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->where('option_id', $val['product_option_id'])->where('option_value_id', $val['product_option_value_id'])->update(array('available_quantity' => DB::Raw('available_quantity+' . $val['received_quantity'])));
          }

          $comment = "This quantity added from the purchase #".$order_id." .<br>Quantity: ". rtrim($quantity_data, ", ");
          $OmsInventoryAddStockHistoryModel = new OmsInventoryAddStockHistoryModel();
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_PRODUCT_ID} = $omsProduct->product_id;
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_USER_ID} = session('user_id');
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_COMMENT} = $comment;
          $OmsInventoryAddStockHistoryModel->save();

          //OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->where('option_id', $this->static_option_id)->where('option_value_id', $omsProduct->color)->update(array('available_quantity' => DB::Raw('available_quantity+' . $total_quantity)));

          foreach ($value['options'] as $key => $val) {
            $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $omsProduct->product_id;
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID} = $val['product_option_id'];
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $val['product_option_value_id'];
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $val['received_quantity'];
            $OmsInventoryAddStockOptionModel->save();
          }
              }else{
            // dd($value['options']);
            $quantity_data = "";
            foreach ($value['options'] as $key => $val) {
              $quantity_data .= $val['option_value'] . "-(" . $val['received_quantity'] . "), ";
               OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->where('option_id', $val['product_option_id'])->where('option_value_id', $val['product_option_value_id'])->update(array('available_quantity' => DB::Raw('available_quantity+' . $val['received_quantity'])));
            }
          $comment = "This quantity added from the purchase #".$order_id." .<br>Quantity: ". rtrim($quantity_data, ", ");
          $OmsInventoryAddStockHistoryModel = new OmsInventoryAddStockHistoryModel();
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_PRODUCT_ID} = $omsProduct->product_id;
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_USER_ID} = session('user_id');
          $OmsInventoryAddStockHistoryModel->{OmsInventoryAddStockHistoryModel::FIELD_COMMENT} = $comment;
          $OmsInventoryAddStockHistoryModel->save();

          foreach ($value['options'] as $key => $val) {
            $OmsInventoryAddStockOptionModel = new OmsInventoryAddStockOptionModel();
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_HISTORY_ID} = $OmsInventoryAddStockHistoryModel->history_id;
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_PRODUCT_ID} = $omsProduct->product_id;
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_ID} = $val['product_option_id'];
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_OPTION_VALUE_ID} = $val['product_option_value_id'];
            $OmsInventoryAddStockOptionModel->{OmsInventoryAddStockOptionModel::FIELD_QUANTITY} = $val['received_quantity'];
            $OmsInventoryAddStockOptionModel->save();
          }
              }

            //   InventoryManagementController::updateSitesStock($omsProduct->sku);
            updateSitesStock($omsProduct->sku);
          }
      }
  }

  public function barcodeGenerate($label_type = 'small', $shipped_order_id = '') {
    $order = OmsPurchaseShippedOrdersModel::where('shipped_order_id', $shipped_order_id)->where('status', 3)->first();
  $barcode_generate = array();
  if($order){
      $products = OmsPurchaseShippedOrdersProductModel::where('shipped_order_id', $order['shipped_order_id'])->get()->toArray();
      foreach ($products as $product) {
        if($product['type'] == 'manual'){
          $omsProduct = OmsInventoryProductModel::select('*')->where('sku', $product['name'])->first();
        }else{
          $opencartSKU = ProductsModel::select('sku')->where('product_id', $product['product_id'])->first();
          $omsProduct = OmsInventoryProductModel::select('*')->where('sku', $opencartSKU->sku)->first();
        }

        if($omsProduct){
            $quantities = OmsPurchaseShippedOrdersProductQuantityModel::select('order_product_quantity_id','received_quantity')->where('shipped_order_id', $shipped_order_id)->where('order_product_id', $product['product_id'])->get()->toArray();
            foreach ($quantities as $quantity) {
                $options = OmsPurchaseShippedOrdersProductOptionModel::select('product_option_id','product_option_value_id','name','value')->where('shipped_order_id', $order['shipped_order_id'])->where('order_product_quantity_id', $quantity['order_product_quantity_id'])->get()->toArray();

                $barcode = $omsProduct->product_id;

                $color = '';
            foreach ($options as $key => $option) {
                if(count($options) > 1 && $option['product_option_id'] == $this->static_option_id){
                    $option_value_name = OptionValueDescriptionModel::select('name')->where('option_value_id', $option['product_option_value_id'])->first();
                    $color = $option_value_name->name;
                    unset($options[$key]);
                }
            }

              $option_array = array();
                foreach ($options as $option) {
                    $barcode .= $option['product_option_value_id'];
                if($option['product_option_id'] == $this->static_option_id){
                    $option_array = array(
                        'color'   =>  $option['value'],
                        'size'  =>  '',
                    );
                }else{
                    $option_array = array(
                        'color'  =>  $color,
                        'size'   =>  $option['value'],
                    );
                }
                }

                $product_sku = ProductSkuModel::select('*')->where('product_id', $product['product_id'])->where('sku', $barcode)->exists();
                if($product_sku == false){
                    $ProductSkuModel = new ProductSkuModel();
                    $ProductSkuModel->{ProductSkuModel::FIELD_PRODUCT_ID} = $product['product_id'];
                    $ProductSkuModel->{ProductSkuModel::FIELD_PRODUCT_OPTION} = json_encode($options);
                    $ProductSkuModel->{ProductSkuModel::FIELD_SKU} = $barcode;
                    $ProductSkuModel->save();
                }

              for ($i=0; $i < $quantity['received_quantity']; $i++) {
                    $barcode_generate[$label_type][] = array(
                        'product_image' =>  $this->get_oms_product_image($omsProduct->image, 100, 100),
                        'product_sku'   =>  $omsProduct->sku,
                        'option'        =>  $option_array,
                        'barcode'       =>  $barcode,
                    );
              }
            }
        }
      }
  }
  return view(self::VIEW_DIR.".barcodeGenerate", ["labels" => $barcode_generate, "label_type" => $label_type]);
}

    public function supplierCancelledAwaitingActionOrderRequest(Request $request) {
        if($request->order_id && $request->supplier && $request->comment) {
            $exist = OmsPurchaseAwaitingActionCancelledModel::where('order_id', $request->order_id)->where('supplier', $request->supplier)->exists();
            if($exist) {
                OmsPurchaseAwaitingActionCancelledModel::where('order_id', $request->order_id)->where('supplier', $request->supplier)->update(array('reason' => $request->comment, 'status' => 0));
            }else {
                $OmsPurchaseAwaitingActionCancelledModel = new OmsPurchaseAwaitingActionCancelledModel();
	    		$OmsPurchaseAwaitingActionCancelledModel->{OmsPurchaseAwaitingActionCancelledModel::FIELD_ORDER_ID} = $request->order_id;
	    		$OmsPurchaseAwaitingActionCancelledModel->{OmsPurchaseAwaitingActionCancelledModel::FIELD_SUPPLIER} = $request->supplier;
	    		$OmsPurchaseAwaitingActionCancelledModel->{OmsPurchaseAwaitingActionCancelledModel::FIELD_REASON} = $request->comment;
	    		$OmsPurchaseAwaitingActionCancelledModel->{OmsPurchaseAwaitingActionCancelledModel::FIELD_STATUS} = 0;
	    		$OmsPurchaseAwaitingActionCancelledModel->save();
            }
            return Redirect::back()->with('message', 'Your Order #'. $request->order_id .' cancelled request send successfully.');
        }else {
            return Redirect::back()->with('message', 'Something went wrong!');
        }
    }

    public function updateAwaitingActionCancelled(Request $request) {
        if($request->action == 'accept') {
            OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(['order_status_id' => 7]);
            OmsPurchaseAwaitingActionCancelledModel::where('order_id', $request->order_id)->update(['status' => 1]);
            Session::flash('message', 'Your Order #'. $request->order_id .' cancelled successfully.');
			Session::flash('alert-class', 'alert-success');
	    	return response()->json(array('redirect' => true));
        }elseif($request->action == 'reject') {
            OmsPurchaseAwaitingActionCancelledModel::where('order_id', $request->order_id)->update(['status' => 2]);
            Session::flash('message', 'Your Order #'. $request->order_id .' request rejected successfully.');
			Session::flash('alert-class', 'alert-success');
	    	return response()->json(array('redirect' => true));
        }else{
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json(array('redirect' => true));
        }
    }

    public function updateConfirmedActionCancelled(Request $request) {
        if($request->action == 'accept') {
            OmsPurchaseOrdersModel::where('order_id', $request->order_id)->update(['order_status_id' => 7]);
            OmsPurchaseConfirmedCancelledModel::where('order_id', $request->order_id)->update(['status' => 1]);

            Session::flash('message', 'Your Order #'. $request->order_id .' cancelled successfully.');
			Session::flash('alert-class', 'alert-success');
	    	return response()->json(array('redirect' => true));
        }elseif($request->action == 'reject') {
                OmsPurchaseConfirmedCancelledModel::where('order_id', $request->order_id)->update(['status' => 2]);
            Session::flash('message', 'Your Order #'. $request->order_id .' request rejected successfully.');
			Session::flash('alert-class', 'alert-success');
	    	return response()->json(array('redirect' => true));
        }else{
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json(array('redirect' => true));
        }
    }

    public function addApprovalComment(Request $request) {
        if($request->order_id) {
            $order_id = $request->order_id;
            $comment = $request->comment;
            $from = $request->type;
            $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = $from;
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $comment;
            $OmsPurchaseOrdersHistoryModel->save();

            $json['success'] = true;
        }
        return response()->json($json);
    }

    private function checkOrderQuantity($quantity) {
        $error = false;
        foreach($quantity as $opqid => $value) {
            if($value['order_quantity'] > $value['old_order_quantity']) {
                $error = true;
            }
        }
        return $error;
    }

    public function updateOrderQuantityPrice($request, $order_id) {
        if($request->submit == 'update_awaiting_action') {
            $updateData = array(
                'total'             => $request->total,
                'link'              => $request->supplier_link,
                'order_status_id'   => 1
            );
        }else {
            $updateData = array(
                'total'             => $request->total,
                'link'              => $request->supplier_link
            );
        }
        OmsPurchaseOrdersModel::where('order_id', $order_id)->update($updateData);
        $this->addOrderStatusHistory($order_id, 1);

        if($request->instruction) {
            $OmsPurchaseOrdersHistoryModel = new OmsPurchaseOrdersHistoryModel();
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_ORDER_ID} = $order_id;
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_NAME} = 'Supplier';
            $OmsPurchaseOrdersHistoryModel->{OmsPurchaseOrdersHistoryModel::FIELD_COMMENT} = $request->instruction;
            $OmsPurchaseOrdersHistoryModel->save();
        }
        foreach($request->quantity as $order_product_qty_id => $value) {
            $update_array = ['order_quantity' => $value['order_quantity'], 'price' => $value['price'], 'total' => $value['total']];
            OmsPurchaseOrdersProductQuantityModel::where('order_id', $order_id)->where('order_product_quantity_id', $order_product_qty_id)->update($update_array);
        }
        if($request->totals) {
            $sortOrder = 0;
            if($request->submit == 'update_awaiting_action') {
                foreach($request->totals as $code => $value) {
                    $sortOrder++;
                    $orderTotal = new OmsPurchaseOrdersTotalModel();
                    $orderTotal->order_id = $order_id;
                    $orderTotal->code = $code;
                    foreach($value as $title => $va) {
                        $orderTotal->title = $title;
                        $orderTotal->value = $va;
                    }
                    $orderTotal->sort_order = $sortOrder;
                    $orderTotal->save();
                }
            }else { // update price
                foreach ($request->totals as $code => $value) {
                    foreach ($value as $title => $val) {
                        if(OmsPurchaseOrdersTotalModel::where('order_id', $order_id)->where('title', $title)->exists()){
                            OmsPurchaseOrdersTotalModel::where('order_id', $order_id)->where('title', $title)->update(array('value' => $val));
                        }else{
                            $sortOrder++;
                            $OmsPurchaseOrdersTotalModel = new OmsPurchaseOrdersTotalModel();
                            $OmsPurchaseOrdersTotalModel->{OmsPurchaseOrdersTotalModel::FIELD_ORDER_ID} = $order_id;
                            $OmsPurchaseOrdersTotalModel->{OmsPurchaseOrdersTotalModel::FIELD_CODE} = $code;
                            $OmsPurchaseOrdersTotalModel->{OmsPurchaseOrdersTotalModel::FIELD_TITLE} = $title;
                            $OmsPurchaseOrdersTotalModel->{OmsPurchaseOrdersTotalModel::FIELD_VALUE} = $val;
                            $OmsPurchaseOrdersTotalModel->{OmsPurchaseOrdersTotalModel::FIELD_SORT_ORDER} = $sortOrder;
                            $OmsPurchaseOrdersTotalModel->save();
                        }
                    }
                }
            }
        }
        $product_listing_link = $request->product_listing_link;
        //update product listing link in inventory_product table
        if($product_listing_link) {
            foreach($product_listing_link as $sku => $link) {
                OmsInventoryProductModel::where('sku', $sku)->update(['supplier_link' => $link]);
            }
        }
        //update product listing link in inventory_product table end
    }

    public function confirmedOrderCancelled(Request $request) {
        $order_id = $request->order_id;
        if($order_id) {
            $exist = OmsPurchaseConfirmedCancelledModel::where('order_id', $order_id)->where('supplier', $request->supplier)->exists();
            if($exist) {
                OmsPurchaseConfirmedCancelledModel::where('order_id', $order_id)->where('supplier', $request->supplier)->update(['reason' => $request->comment, 'status' => 0]);
            }else {
                $OmsPurchaseConfirmedCancelledModel = new OmsPurchaseConfirmedCancelledModel();
	    		$OmsPurchaseConfirmedCancelledModel->{OmsPurchaseConfirmedCancelledModel::FIELD_ORDER_ID} = $order_id;
	    		$OmsPurchaseConfirmedCancelledModel->{OmsPurchaseConfirmedCancelledModel::FIELD_SUPPLIER} = $request->supplier;
	    		$OmsPurchaseConfirmedCancelledModel->{OmsPurchaseConfirmedCancelledModel::FIELD_REASON} = $request->comment;
	    		$OmsPurchaseConfirmedCancelledModel->{OmsPurchaseConfirmedCancelledModel::FIELD_STATUS} = 0;
	    		$OmsPurchaseConfirmedCancelledModel->save();
            }

              return redirect()->back()->with('message', 'Your Order #'. $order_id .' cancelled request send successfully.');
        }else {
            return redirect()->back()->with('message', 'Something went wrong!');
        }
    }

    public function toBeShipOrderCancelRequest(Request $request) {
        if($request->all() > 0 && $request->submit == 'cancel'){
            $StockCancel = new OmsPurchaseStockCancelledModel();
            $StockCancel->{OmsPurchaseStockCancelledModel::FIELD_ORDER_ID} = $request->order_id;
            $StockCancel->{OmsPurchaseStockCancelledModel::FIELD_SUPPLIER} = session('user_id');
            $StockCancel->{OmsPurchaseStockCancelledModel::FIELD_STATUS} = 0;
            if(isset($request->shiped_order_id)) {
                $StockCancel->shiped_order_id = $request->shiped_order_id;
            }
            $StockCancel->save();
            return response()->json([
                'success' => true
            ]);
            // return redirect()->back()->with('message', 'Your Order #'. $request->order_id .' cancelled request send successfully.');
        }
    }

    public function getGroupName($sku) {

        if(strpos($sku,"-") !== false) {
            $pieces = explode("-", $sku);
            $first_piece = $pieces[0].'-';
            if(preg_replace('/[^0-9]/','', $pieces[1])) {
                $second_piece = preg_replace('/[^0-9]/','', $pieces[1]);
            }else{
                $second_piece = $pieces[1];
            }
        }else {
            if(preg_replace('/[^0-9]/','', $sku)) {
                $pieces = explode(preg_replace('/[^0-9]/','', $sku), $sku);
                $first_piece = $pieces[0];
                $second_piece = preg_replace('/[^0-9]/','', $sku);
            }else {
                $first_piece = substr($sku, 0, 1);
                $second_piece = substr("sku",1);
            }

        }

        $group = $first_piece.$second_piece;
        return $group;
    }

    protected function statusHistory($order_id){
    	$data = array();

    	$history = OmsPurchaseOrdersStatusHistoryModel::where('order_id', $order_id)->get();
    	if($history->count()){
	    	foreach ($history as $key => $value) {
          $user_name = $this->returnUsername($value['created_by']);
	    		$data[$value['order_status_id']] = $value['created_at']->toDateString().$user_name;
	    	}
	    	if(isset($data[4])){
	    		$data[3] = $data[4];
	    	}
    	}
    	return $data;
    }
    private function returnUsername($user_id){
        $data = OmsUserModel::where('user_id',$user_id)->first();
        if( !empty($data) ){
          return "<br>".$data->firstname;
        }else{
          return '';
        }
      }

      protected function omsProductImage($product_id = 0,$width = 0, $height = 0,$type=""){
        if( $type == "" ||  $type == "opencart"){
          $product_data = OmsInventoryProductModel::where('product_id',$product_id)->first();
          $img_dir = "inventory_products";
        }else{
          $product_data = OmsPurchaseProductModel::where('product_id',$product_id)->first();
          $img_dir = "inventory_products";
        }
        if( !empty( $product_data ) && $product_data->image != "" ){
          $source = asset("uploads/$img_dir/$product_data->image");
          $image = $source;
        }else{
          $image = "";
        }
        if( $image == "" ){
          $image = $this->get_product_image("opencart",$product_id,$width = 0, $height = 0);
        }
        return $image;
      }

      protected function get_product_image($type = 'opencart', $product_id = 0, $width = 0, $height = 0){
        $slash = DIRECTORY_SEPARATOR;
        $return_image = '';
        if($type == 'opencart'){
            $product_image = ProductsModel::select('image')->where('product_id', $product_id)->first();

            if($product_image){
                if(file_exists($this->website_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
					$ToolImage = new ToolImage();
					return $ToolImage->resize($this->website_image_source_path, $this->website_image_source_url, $product_image->image, $width, $height);
                }else{
                    return $this->opencart_image_url . $product_image->image;
                }
            }else return $this->opencart_image_url . 'placeholder.png';
        }else if($type == 'manual'){
            $product_image = OmsPurchaseProductModel::select('image')->where('product_id', $product_id)->first();
            if($product_image){
	            if(file_exists($this->oms_manual_product_image_source_path . $product_image->image) && !empty($width) && !empty($height)){
	            	$ToolImage = new ToolImage();
	            	return $ToolImage->resize($this->oms_manual_product_image_source_path, $this->oms_manual_product_image_source_url, $product_image->image, $width, $height);
	            }else{
	                return str_replace("public/", "", url('public/uploads/products/cache/' . $product_image->image));
	            }
            }else return $this->opencart_image_url . 'placeholder.png';
        }
    }

    public function productCount(){
        // dd(session('role'));
        $whereClause = [];
        $counter = array();
        $tabs = OmsPurchaseTabsModel::orderBy('sort_order', 'ASC')->get()->toArray();

        if(session('role') != 'ADMIN' && session('role') != 'STAFF'){
            $whereClause[] = array('supplier', session('user_id'));
        }
        // dd($tabs);
        foreach($tabs as $tab) {
            $orders = OmsPurchaseOrdersModel::where('order_status_id', $tab['order_status'])->where($whereClause)->get();
            $counter[$tab['name']] = count($orders);
        }
        return $counter;
    }

    protected function addOrderStatusHistory($order_id, $order_status_id){
    	if(!OmsPurchaseOrdersStatusHistoryModel::where('order_id', $order_id)->where('order_status_id', $order_status_id)->exists()){
			$OmsPurchaseOrdersStatusHistoryModel = new OmsPurchaseOrdersStatusHistoryModel();
			$OmsPurchaseOrdersStatusHistoryModel->{OmsPurchaseOrdersStatusHistoryModel::FIELD_ORDER_ID} = $order_id;
			$OmsPurchaseOrdersStatusHistoryModel->{OmsPurchaseOrdersStatusHistoryModel::FIELD_ORDER_STATUS_ID} = $order_status_id;
			$OmsPurchaseOrdersStatusHistoryModel->created_by = session('user_id');
			$OmsPurchaseOrdersStatusHistoryModel->save();
    	}
    }
}
