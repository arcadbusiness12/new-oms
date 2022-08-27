<?php

use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
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