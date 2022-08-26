<?php

namespace App\Http\Controllers\PurchaseManagement;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function orderOutStockProduct(Request $request) {
        $products = [];
        // dd($request->all());
    		foreach ($request->order_product as $product_id) {
    			// dd($product_id);
                $product = OmsInventoryProductModel::where('product_id', $product_id)->first();
                $product_array = array();
                if ($product){
                    // $product = (array)$product;
                    $product = $product->toArray();
                    $product_array = array(
                        'product_id'    =>  $product['product_id'],
                        'image'         =>  \URL::to('/uploads/inventory_products/' . $product['image']),
                        'sku'           =>  $product['sku'],
                    );
        
                    $options = $this->get_inventory_product_options_format($product['product_id']);
                    if($options){
                        $product_array['options'] = array();
                        // dd($options);
                        foreach ($options as $option) {
                            $option_values = array();
                            foreach ($option['option_values'] as $value) {
                                $quantity = $minimum_quantity = $average_quantity = $duration = '';
                                $quantity = $value['available_quantity'];
        
                                $OmsInventoryStockModel = OmsInventoryStockModel::select('minimum_quantity','average_quantity','duration')->where('product_id', $product['product_id'])->where('option_id', $option['option_id'])->where('option_value_id', $value['option_value_id'])->first();
        
                                //$average_quantity = $this->getLastSaleQtyWithOption($product['product_id'], $value);  //for delivered quantity
                                $average_quantity_shipped = $this->getLastSaleQtyWithOptionShipped($product['product_id'], $value); //for shipped quantity
                                // $average_tot_quantity = $average_quantity->total+$average_quantity_shipped->total;
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
            
            // dd($products);
            $suppliers = OmsUserModel::select('user_id','username','firstname','lastname')->where('user_group_id', 2)->where('status',1)->get()->toArray();
            return view(self::VIEW_DIR.".addProduct", ["suppliers" => $suppliers, "products" => $products]);
    }

    protected function get_inventory_product_options_format($product_id){
        $inventory_options = OmsInventoryProductModel::select("option_name as color","option_value as size")->where('product_id', $product_id)->first();
        
        if($inventory_options['color']){
            $option_data = DB::table("oms_options_details")->where("value",$inventory_options['color'])->first();
            $option_value_data = OmsInventoryProductOptionModel::select('oms_inventory_product_option.option_id','oms_inventory_product_option.option_value_id','oms_inventory_product_option.available_quantity','oms_options_details.value as name')
            ->where('product_id', $product_id)
            ->join('oms_options_details', 'oms_options_details.id', '=', 'oms_inventory_product_option.option_value_id')
            ->where('oms_inventory_product_option.option_id', $option_data->options)
            ->get()
            ->toArray();
            if( empty($option_value_data ) ){
              $option_value_data[] = array(
              "option_id" => $option_data->options,
              "option_value_id" => $option_data->id,
              "available_quantity" => 0,
              "name" => $option_data->value 
              );
            }
            $options[] = array(
                'option_id' =>  $option_data->options,
                'name'      =>  'Color',
                'type'      =>  "radio",
                'option_values' =>  $option_value_data,
            );
        }
        if($inventory_options['size']){
            $option_data = DB::table("oms_options")->where("id",$inventory_options['size'])->first();
            $option_value_data = OmsInventoryProductOptionModel::select('oms_inventory_product_option.option_id','oms_inventory_product_option.option_value_id','oms_inventory_product_option.available_quantity','oms_options_details.value as name')
            ->where('product_id', $product_id)
            ->join('oms_options_details', 'oms_options_details.id', '=', 'oms_inventory_product_option.option_value_id')
            ->where('oms_inventory_product_option.option_id', $inventory_options['size'])
            ->orderBy('oms_options_details.sort')
            ->get()
            ->toArray();
            $options[] = array(
                'option_id' =>  $option_data->id,
                'name'      =>  $option_data->option_name,
                'type'      =>  "radio",
                'option_values' =>  $option_value_data,
            );
        }
        return $options;
    }

    protected function getLastSaleQtyWithOptionShipped($product_id, $options){
        $today = Carbon::now();
        $today_date = Carbon::createFromFormat('Y-m-d H:i:s', $today)->toDateTimeString();
        $now = Carbon::now();
        $last = $now->subDays(30);
        $last_date = Carbon::createFromFormat('Y-m-d H:i:s', $last)->toDateTimeString();
        return OmsInventoryShippedQuantityModel::select(DB::Raw('SUM(quantity) as total'))
        ->where('product_id', $product_id)
        ->where('option_id', $options['option_id'])
        ->where('option_value_id', $options['option_value_id'])
        ->where('updated_at', '>=', $last_date)
        ->first();
    }
}
