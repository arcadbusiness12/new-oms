<?php

namespace App\Http\Controllers\PurchaseManagement;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseAwaitingActionCancelledModel;
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
use App\Models\OpenCart\Products\ProductsModel;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Session;

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
		$this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
		$this->website_image_source_url =  $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/';
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
        )->where($whereClause)->whereIn('order_status_id', [4,7])->orderBy('order_id', 'DESC')->groupBy('order_id')
        ->paginate(self::PER_PAGE)->appends($request->all());
        if($count == true){ return $orders->count(); }
        // dd($orders->toArray());
        foreach($orders as $order) {
            $stock_cancel = OmsPurchaseStockCancelledModel::where(OmsPurchaseStockCancelledModel::FIELD_ORDER_ID, $order['order_id'])->where(OmsPurchaseStockCancelledModel::FIELD_SUPPLIER, session('user_id'))->where(OmsPurchaseStockCancelledModel::FIELD_STATUS, 0)->whereNull('shiped_order_id')->exists();
            $order['stock_cancel'] = $stock_cancel;
            // dd($order->orderProducts->toArray());
            foreach($order->orderProducts as $key => $product) {
                $any_qty_remain = 0;
                $product['image'] = $this->omsProductImage($product->product_id, 300, 300, $product->type);
                $quantities = OmsPurchaseOrdersProductQuantityModel::where('order_id', $order['order_id'])->where('order_product_id', $product->product_id)->get();
                $units = OmsPurchaseOrdersProductQuantityModel::select(DB::Raw('SUM(order_quantity) as unit'),'order_id','order_product_id')->where('order_id', $order['order_id'])->where('order_product_id', $product['product_id'])->groupBy('order_id','order_product_id')->first();
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
            // dd($order->shipped_orders);
            if(count((array)$order->shipped_orders) > 0) {
                foreach($order->shippedOrders as $sorder) {
                    foreach($sorder->orderProducts as $sproduct) {
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
        }
        // dd($orders->toArray());
        $statuses = array(
            'to_be_shipped' =>  4,
            'shipped'       =>  5,
            'cancelled'     =>  7,
        );
        $counter = $this->productCount();
        $search_form_action = \URL::to('/PurchaseManagement/get/to/be/shipped'); 
        // dd($search_form_action);
        $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->get()->toArray();
        return view(self::VIEW_DIR.".toBeShipped", ["orders" => $orders->toArray(),"pagination" => $orders->render(), "suppliers" => $suppliers, "tabs" => $tabs, "counter" => $counter, "statuses" => $statuses, "search_form_action" => $search_form_action, "old_input" => $request->all()]);
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
