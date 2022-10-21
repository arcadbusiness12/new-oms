<?php

namespace App\Http\Controllers\productgroup;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsProductSizeChartValueModel;
use App\Models\Oms\OmsSizeChartOptionModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DFProductsModel;
use Illuminate\Http\Request;

class ProductGroupController extends Controller
{
    const VIEW_DIR = 'productGroup';
    const PER_PAGE = 20;
    
    public function addSubCategoryToGroup($cate, ProductGroupModel $group){
        $group->sub_category_id = $cate;
        if($group->update()) {
            return response()->json([
                'status' => true,
                'message' => 'Category assigned to group '.$group->name.' successfully'
            ]);
        }
    }

    public function changeGroupType($type, ProductGroupModel $group){
        $group->product_type_id = $type;
        if($group->update()) {
            return response()->json([
                'status' => true,
                'message' => 'Type added to group '.$group->name.' successfully'
            ]);
        }
    }

    public function groupChangeProductStatus(Request $request) {
        $update = OmsInventoryProductModel::where(['group_id'=>$request->group_id])->update(['status'=>$request->status]);
        if($update) {
            $msg = "Products updated successfully in inventory";
        }else {
            $msg = "Opps! Somethings went wrong.";
        }

     return response()->json(['status'=>true,'msgs'=>$msg]);
    }

    public function getProductSizeChart(Request $request) {
          $group_array = explode("_", $request->group_name);
        //   dd($group_array);
            $group = ProductGroupModel::with('products.ProductsSizes')->find($group_array[1]);
            // dd($group->products[0]->ProductsSizes);
            $option_ids = $group->products[0]->ProductsSizes->pluck('option_value_id')->toArray();
            // dd($option_ids);
            $optionid = 0;
            if($group->category_id) {
                $optionid = $group->products[0]->ProductsSizes[0]['option_id'];
                // if($group->category_name == 'Clothings') {
                //     $optionid = 11;           
                // }
                // if($group->category_name == 'Shoes') {
                //     $optionid = 14;           
                // }
                // if($group->category_name == 'Rings') {
                //     $optionid = 15;           
                // }
                // if($group->category_name == 'Bags') {
                //     $optionid = 19;           
                // }
                $sizeOptions = OmsOptions::with(['omsOptionsDetails' => function($q) use($option_ids) {
                    $q->whereIn('id', $option_ids)->orderBy('sort', 'asc');
                }])->find($optionid);
                // dd($sizeOptions);
                $topOptions = OmsSizeChartOptionModel::where('category_id', $group->category_id)->get();
                
                $groupid = $group_array[1];
                $groupname = $group_array[0];
                $sizeChartValues = OmsProductSizeChartValueModel::where('group_id', $groupid)->get();
                // dd($sizeChartValues);
                return view(self::VIEW_DIR. '.size_chart', compact('sizeOptions', 'topOptions','groupid', 'sizeChartValues', 'groupname'));
               
            }else {
                return response()->json([
                    'status' => 'notconnect',
                    'mesge'  => 'Category is not connect, first connect cstegory'
                ]);
            }
        }

        public function updateProductSizeChart(Request $request){
            $cmss = [];
            
            OmsProductSizeChartValueModel::where('group_id', $request->groupid)->delete();
            foreach($request->cm as $k => $cms) {
               foreach($cms as $key => $cm) {
                   if($cm[0]) {
                    $inseData = [
                        'option_id' => $k,
                        'size_chart_option_id' => $key,
                        'group_id' => $request->groupid,
                        'group_name' => $request->groupname,
                        'value' => $cm[0],
                        'cm_inch' => 'cm'
                    ];
                    // CM entry
                    OmsProductSizeChartValueModel::create($inseData);
                   }
                   
               }
               
            }
            foreach($request->inch as $k => $inches) {
               foreach($inches as $key => $inch) {
                   if($inch[0]) {
                    $inseData = [
                        'option_id' => $k,
                        'size_chart_option_id' => $key,
                        'group_id' => $request->groupid,
                        'group_name' => $request->groupname,
                        'value' => $inch[0],
                        'cm_inch' => 'inch'
                    ];
                    // Inches Entry
                    OmsProductSizeChartValueModel::create($inseData);
                   }
                   
               }
               
            }
    
            $products = OmsInventoryProductModel::where('group_id', $request->groupid)->pluck('sku')->toArray();
            // ProductsModel::whereIn('sku', $products)->update(['oms_size_chart' => 1]);
            // DFProductsModel::WhereIn('sku', $products)->update(['oms_size_chart' => 1]);
            
            return response()->json([
                'status' => true
            ]);
          }
}
