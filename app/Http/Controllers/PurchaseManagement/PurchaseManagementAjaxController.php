<?php

namespace App\Http\Controllers\PurchaseManagement;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsSettingsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseManagementAjaxController extends Controller
{
    const VIEW_DIR = 'PurchaseManagement';
    const PER_PAGE = 10;
    private $opencart_image_url = '';
    private $static_option_id = 0;

    function __construct()
    {
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
        $this->DB_BAOPENCART_DATABASE = env("DB_BAOPENCART_DATABASE");
        $this->static_option_id = OmsSettingsModel::get('product_option','color');
    }
    public function getPurchaseProductOrderOption(Request $request) {
        $product = OmsInventoryProductModel::select('*')->where('product_id', $request->product_id)->first();
        
        $product_array = array();
        if ($product){
            $product_array = array(
                'product_id'    =>  $product->product_id,
            );
            
            $options = get_inventory_product_options_format($product->product_id);

            $product_array['options'] = array();
            foreach ($options as $option) {
                $option_values = array();
                foreach ($option['option_values'] as $value) {
                    $quantity = $minimum_quantity = $average_quantity = $duration = '';
                    $quantity = $value['available_quantity'];

                    $OmsInventoryStockModel = OmsInventoryStockModel::select('minimum_quantity','average_quantity','duration')->where('product_id', $product->product_id)->where('option_id', $option['option_id'])->where('option_value_id', $value['option_value_id'])->first();

                    $average_quantity = $this->getLastSaleQtyWithOption($product->product_id, $value);
                    if($OmsInventoryStockModel){
                        $minimum_quantity = $OmsInventoryStockModel->minimum_quantity;
                    }
                    $option_values[] = array(
                        'option_value_id'   =>  $value['option_value_id'],
                        'name'              =>  $value['name'],
                        'quantity'          =>  $quantity,
                        'minimum_quantity'  =>  $minimum_quantity,
                        'average_quantity'  =>  $average_quantity->total,
                        'duration'          =>  30,
                    );
                }
                $product_array['options'][] = array(
                    'option_id'     =>  $option['option_id'],
                    'static_option_id'  =>  $this->static_option_id,  
                    'name'          =>  $option['name'],
                    'type'          =>  $option['type'],
                    'option_values' =>  $option_values,
                );
            }
        }
        return view(self::VIEW_DIR.".addProductOptionRow", ['product' => $product_array]); 
    }

    protected function getLastSaleQtyWithOption($product_id, $options){
        $today = Carbon::now();
        $today_date = Carbon::createFromFormat('Y-m-d H:i:s', $today)->toDateTimeString();
        $now = Carbon::now();
        $last = $now->subDays(30);
        $last_date = Carbon::createFromFormat('Y-m-d H:i:s', $last)->toDateTimeString();
        return OmsInventoryDeliveredQuantityModel::select(DB::Raw('SUM(quantity) as total'))
        ->where('product_id', $product_id)
        ->where('option_id', $options['option_id'])
        ->where('option_value_id', $options['option_value_id'])
        ->where('updated_at', '>=', $last_date)
        ->first();
    }

  public function addPurchaseProductManualy() {
      $productOPtionColors = [];
      $productOPtionSizes = [];
      $allColorOptions = OmsDetails::select('id', 'value', 'code')->where('options', $this->static_option_id)->orderBy('value', 'ASC')->get();
      $allSizeOptions = OmsOptions::select('id', 'option_name')->where('id', '!=', $this->static_option_id)->orderBy('option_name', 'ASC')->get()->toArray();
      foreach($allSizeOptions as $size) {
          $productOPtionSizes[$size['option_name']] = $size['id'];
      }
      $categories = GroupCategoryModel::all();
      $subcategories = GroupSubCategoryModel::all();
      $placeholder = $this->opencart_image_url. 'placeholder.png';
      return view(self::VIEW_DIR.".addManuallyRow", ['colors' => $allColorOptions, 'sizes' => $productOPtionSizes, 'categories' => $categories, 'subcategories' => $subcategories, 'placeholder' => $placeholder]);
  }

  public function getManuallyAllOptions(Request $request) {
    $options[$this->static_option_id] = OmsDetails::select('options AS option_id','id AS option_value_id','value AS name')->where('id', $request->color)->get()->toArray();
    $count = 1;
    if($request->size > 0) {
        $options[$request->size] = OmsDetails::select('options AS option_id','id AS option_value_id','value AS name')->where('options', $request->size)->orderBy('sort', 'ASC')->get()->toArray();
        $count = count($options[$request->size]);
    }
    return view(self::VIEW_DIR. '.addManualAllOptions', ['options' => $options, 'data_product_id' => $request->data_product_id, 'count' => $count]);
  }

  public function getPurchaseProductSku(Request $request) {
    $skus = array();
        if(count($request->all()) > 0){
            $product_sku = $request->product_sku;
        }
        if($product_sku){
            $product_skus = OmsInventoryProductModel::select('sku')->where('sku','LIKE',"{$product_sku}%")->groupBy('sku')->limit(10)->get();
            if($product_skus->count()){
                foreach ($product_skus as $product) {
                    $skus[] = $product->sku;
                }
            }
        }
        return response()->json(array('skus' => $skus));
  }

  public function addProduct(Request $request) {
    $whereCluase = [];
    if($request->product_sku) {
        $whereCluase[] = array('sku', $request->product_sku);
    }
    if($request->product_model) {
        $whereCluase[] = array('model', $request->product_model);
    }
    $product = OmsInventoryProductModel::where($whereCluase)->first();
    $product_array = array();
    if($product) {
        $product_array = array(
            'product_id' => $product->product_id,
            'image'      => $this->get_inventory_product_image($product->image),
            'sku'        => $product->sku
        );

        $options = get_inventory_product_options_format($product->product_id);
        if($options) {
            $product_array['options'] = array();
            foreach($options as $option) {
                 $option_values = array();
                foreach($option['option_values'] as $value) {
                    $quantity = $minimum_quantity = $average_quantity = $duration = '';
                    $quantity = $value['available_quantity'];
                    $OmsInventoryStockModel = OmsInventoryStockModel::select('minimum_quantity','average_quantity','duration')
                                                                      ->where('product_id', $product->product_id)
                                                                      ->where('option_id', $option['option_id'])
                                                                      ->where('option_value_id', $value['option_value_id'])
                                                                      ->first();
                    $average_quantity_shipped = getLastSaleQtyWithOptionShipped($product->product_id, $value); //for shipped quantity
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
    $product = $product_array;
    return view(self::VIEW_DIR.".addProductRow")->with(compact('product'));
  }

  protected function get_inventory_product_image($product_image = ''){
        return \URL::to('/uploads/inventory_products/' . $product_image);
    }
}
