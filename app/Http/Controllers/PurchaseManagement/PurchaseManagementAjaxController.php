<?php

namespace App\Http\Controllers\PurchaseManagement;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\OmsSettingsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseManagementAjaxController extends Controller
{
    const VIEW_DIR = 'purchaseManagement';
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
}
