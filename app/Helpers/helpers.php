<?php

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\OpenCart\Products\ProductsModel as ProductsModel;
use App\Models\OpenCart\Products\ProductOptionValueModel as ProductOptionValueModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DressFairProductsModel;
use App\Models\DressFairOpenCart\Products\ProductOptionValueModel as DressFairProductOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\OmsSettingsModel;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

function customPaginate($items, $perPage, $options = null,  $page = null)
    {
        if($options && count($options) < 1) {
            $options = [];
        }
        // dd($options);
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $user_array = new LengthAwarePaginator($items->forPage($page, $perPage), count($items), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()], $options);

        // dd($user_array);
        return $user_array;
        // return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, ['path' => LengthAwarePaginator::resolveCurrentPath()], $page, $options);
    }

     function get_inventory_product_options_format($product_id){
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
    function getLastSaleQtyWithOptionShipped($product_id, $options){
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
    ///sync site quantity with oms inventory
    function updateSitesStock($sku)
        {
          $df_product_id = 0;
          $ba_product_id = 0;
          $static_option_id = OmsSettingsModel::get('product_option', 'color');
          $omsProduct = OmsInventoryProductModel::select('product_id','option_name','option_value AS size')->where('sku', $sku)->first();
          $BAProduct = ProductsModel::select('product_id')->where('sku', $sku)->first();
            if ($omsProduct && $BAProduct) {
              $omsProductOptions = OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->get();
              // echo "<pre>"; print_r($omsProductOptions->toArray());
              $total_quantity = 0;
              // if ($omsProduct->size != 0) {
              if ( !empty($omsProductOptions) ) {
                foreach ($omsProductOptions as $key => $value) {
                  // if ($value->option_id == $static_option_id) {
                  //   $total_quantity = $value->available_quantity;
                  // } commented because color is no more enter in OmsInventoryProductOptionModel
                  $ba_option_data = OmsInventoryOptionValueModel::select('ba_option_id','ba_option_value_id')->where(['oms_options_id'=>$value->option_id,'oms_option_details_id'=>$value->option_value_id])->first();
                  // echo "<pre>"; print_r($ba_option_data->toArray());
                  // echo $BAProduct->product_id."<br>";
                  $product_total_quantity = ProductOptionValueModel::where('product_id', $BAProduct->product_id)
                  ->where('option_id', $ba_option_data->ba_option_id)
                  ->where('option_value_id', $ba_option_data->ba_option_value_id)
                  ->update(['quantity' => $value->available_quantity]);
                  $total_quantity += $value->available_quantity;
                }
              }
              //update color row
              $oms_product_color_data = OmsDetails::where(["value"=>$omsProduct->option_name])->first();
              if( !empty($oms_product_color_data) ){
                $oms_product_color_id = $oms_product_color_data->id;
                $oms_color_option_id = $oms_product_color_data->options;
                $ba_color_option_data = OmsInventoryOptionValueModel::select('ba_option_id','ba_option_value_id')->where(['oms_options_id'=>$oms_color_option_id,'oms_option_details_id'=>$oms_product_color_id])->first();

                // echo "<pre>"; print_r($ba_color_option_data->toArray()); die;
                if( !empty($BAProduct) ){
                  ProductOptionValueModel::where('product_id', $BAProduct->product_id)
                  ->where('option_id', $ba_color_option_data->ba_option_id)
                  ->where('option_value_id', $ba_color_option_data->ba_option_value_id)
                  ->update(['quantity' => $total_quantity]);
                  //update main table
                  ProductsModel::where('product_id', $BAProduct->product_id)->update(array('quantity' => $total_quantity));
                  $ba_product_id = $BAProduct->product_id;
                }
              }
            }
            //Dress fair code will uncomment when will use=============================================================================================
          $DFProduct = DressFairProductsModel::select('product_id')->where('sku', $sku)->first();
          if ($omsProduct && $DFProduct) {
            $omsProductOptions = OmsInventoryProductOptionModel::where('product_id', $omsProduct->product_id)->get();
            // echo "<pre>"; print_r($omsProductOptions->toArray());
            $total_quantity = 0;
            // if ($omsProduct->size != 0) {
            if ( !empty($omsProductOptions) ) {
              foreach ($omsProductOptions as $key => $value) {
                // if ($value->option_id == $static_option_id) {
                //   $total_quantity = $value->available_quantity;
                // } commented because color is no more enter in OmsInventoryProductOptionModel
                $df_option_data = OmsInventoryOptionValueModel::select('df_option_id','df_option_value_id')->where(['oms_options_id'=>$value->option_id,'oms_option_details_id'=>$value->option_value_id])->first();
                // echo "<pre>"; print_r($ba_option_data->toArray());
                // echo $BAProduct->product_id."<br>";
                $product_total_quantity = DressFairProductOptionValueModel::where('product_id', $DFProduct->product_id)
                ->where('option_id', $df_option_data->df_option_id)
                ->where('option_value_id', $df_option_data->df_option_value_id)
                ->update(['quantity' => $value->available_quantity]);
                $total_quantity += $value->available_quantity;
              }
            }
            //update color row
            $oms_product_color_data = OmsDetails::where(["value"=>$omsProduct->option_name])->first();

            if( !empty($oms_product_color_data) ){
              $oms_product_color_id = $oms_product_color_data->id;
              $oms_color_option_id = $oms_product_color_data->options;
              $df_color_option_data = OmsInventoryOptionValueModel::select('df_option_id','df_option_value_id')->where(['oms_options_id'=>$oms_color_option_id,'oms_option_details_id'=>$oms_product_color_id])->first();

              // echo "<pre>"; print_r($ba_color_option_data->toArray()); die;
              if( !empty($DFProduct) ){
                DressFairProductOptionValueModel::where('product_id', $DFProduct->product_id)
                ->where('option_id', $df_color_option_data->df_option_id)
                ->where('option_value_id', $df_color_option_data->df_option_value_id)
                ->update(['quantity' => $total_quantity]);
                //update main table
                DressFairProductsModel::where('product_id', $DFProduct->product_id)->update(array('quantity' => $total_quantity));
                $df_product_id = $DFProduct->product_id;
                $check_que_data = DB::connection(env('DB_DFOPENCART_CONNECTION'))->table("cedamazon_queue")->where('key','inventory')->where('values','LIKE',"%".$df_product_id."%")->first();
                // echo $check_que_data; die("test");
                if($check_que_data === null){
                  DB::connection(env('DB_DFOPENCART_CONNECTION'))->table("cedamazon_queue")->insert(['key'=>'inventory','values'=>json_encode([$df_product_id])]);
                }
              }
            }
          }
        // Log
          $log_data = "Update Site Stock Date - ".date('Y-m-d h:i:s').PHP_EOL.PHP_EOL;
          file_put_contents(env('BA_DIR_LOGS').'inventory_stock_manage.txt', $log_data, FILE_APPEND);
          return ['df_product_id'=>$df_product_id,'ba_product_id'=>$ba_product_id];
        // End Log
        }
