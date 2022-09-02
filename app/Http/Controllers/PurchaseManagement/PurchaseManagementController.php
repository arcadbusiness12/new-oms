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
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersHistoryModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductOptionModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersProductQuantityModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersStatusHistoryModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersStatusModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseProductModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'shippedOrders.orderTotals',
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
        return view(self::VIEW_DIR. ".purchaseOrders")->with(compact('orders','pagination','order_statuses','shipped_order_statuses','status_cancel','old_input'));
    }
    public function shippedOrderStatuses(){
        return array(
            1  =>  'Forward',
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
}
