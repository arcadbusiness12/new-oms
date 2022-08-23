<?php

namespace App\Http\Controllers\inventoryManagement;

use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Products\DressFairProductsModel;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\OpenCart\Products\ProductsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Description of PurchaseController
 *
 * @author Siraj ALi
 */

 class InventoryManagementController extends Controller {
    const VIEW_DIR = 'inventoryManagement';
    const PER_PAGE = 20;
    private $opencart_image_url = '';
    
    function __construct() {
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }

    public function addInventory() {
        $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
        $option_detail = OmsOptions::where('id','!=',$ba_color_option_id)->orderBy('option_name')->get();
        $option_value = OmsDetails::select('id','value')->where(['options'=>$ba_color_option_id])->orderBy('value')->get();
        $placeholder = 'https://businessarcade.com/image/no_image.png';
        $categories = GroupCategoryModel::all();
        $subcategories = GroupSubCategoryModel::all();
        return view(self::VIEW_DIR. '.addInventory')->with(compact('placeholder','option_value','option_detail','categories','subcategories'));
    }

    public function getLatestGroup($category) {
        $latestGroup = ProductGroupModel::where('category_id', $category)->orderBy('id', 'DESC')->first();
        $subcategories = GroupSubCategoryModel::where('group_main_category_id', $category)->get();
        if(strpos($latestGroup->name, '-') !== false) {
            $code = explode('-', $latestGroup->name);
        }else {
            $code = explode('-', $latestGroup->group_sku);
        }
        return response()->json([
            'status' => true,
            'newSku' => $code[0].'-'.$code[1]+1,
            'code' => $code[1]+1,
            'subCategories' => $subcategories
        ]);
    }

    public function add_inventory_product_add($id=null,Request $request){
        // dd($request->all());
        $placeholder = $this->opencart_image_url.'no_image.png';
        $option_detail = DB::table('oms_options')->get();
        $option_color =$request->input('option_color');
        $option_value_detail = DB::table('oms_options_details')
        ->join('oms_options', 'oms_options.id', '=', 'oms_options_details.options')
          // ->join('Colors', 'Colors.id', '=', 'oms_options.color_id') ,'oms_options.color_id','Colors.color_name'
        ->select('oms_options_details.id', 'oms_options_details.value', 'oms_options.option_name')
        ->where(['oms_options_details.options' => $id])
        ->get();
        if($request->ajax()){
         $data['placeholder'] = $placeholder;
         $data['option_detail'] = $option_detail;
         $data['option_value_detail'] = $option_value_detail;
        //  dd($option_value_detail);
         return view(self::VIEW_DIR. '.addInventoryOptionDetails')->with(compact('option_value_detail'));
        // return response()->json($data);
      }
      return view(self::VIEW_DIR.".addInventory")->with(compact('option_detail','placeholder','option_value_detail','colors'))->render();
         //return json_encode(array('data'=>$userData));
    }

    public function addInventoryProduct(Request $request) {
        $sku = $request->sku;
        $this->validate($request, [
            'sku'      => 'required | unique:oms_inventory_product,sku',
            'category' => 'required',
            'options' => 'required'
        ]);
        $image = "";
        if($request->hasFile('image')) {
            $file = $request->image;
            $extension = $file->getClientOriginalExtension();
            $filename = md5(uniqid(rand(), true)).'.'.$extension;
            $file->move(public_path('uploads/inventory_products/'), $filename);
            $image = $filename;
        }

        $category= GroupCategoryModel::find($request->category);
        $group = new ProductGroupModel();
        $group->group_sku = $request->newSku;
        $group->name = $sku;
        $group->category_name = $category->name;
        $group->category_id = $request->category;
        $group->sub_category_id = $request->subCategory;
        $group->save();

        $product = new OmsInventoryProductModel();
        $product->group_id = $group->id;
        $product->sku = $sku;
        $product->image = $image;
        $product->option_name = $request->options;
        $product->option_value = $request->title;
        $product->save();
        
        if($request->value) {
            foreach($request->value as $key => $value) {
                $productOption = new OmsInventoryProductOptionModel();
                $productOption->product_id = $product->product_id;
                $productOption->option_id  = $request->title;
                $productOption->option_value_id  = $request->value[$key];
                $productOption->save();
            }
        }else {
            $color = OmsDetails::where('value', $request->options)->first();
            $productOption = new OmsInventoryProductOptionModel();
            $productOption->product_id = $product->product_id;
            $productOption->option_id  = $color->options;
            $productOption->option_value_id  = $color->id;
            $productOption->save();
        }

        return back()->with('success', 'Value Successfully Uploaded');
    }

    public function inventoryDashboard(Request $request) {
        $products = OmsInventoryProductModel::with(['omsOptions','ProductsSizes.omsOptionDetails']);
        if($request->by_type != ""){
            $products = $products->whereHas('productGroups', function ($query) use ($request){
              $query->where('product_type_id',$request->by_type);
            });
          }
          $products->orderByRaw('SUBSTRING_INDEX(sku,"-",1),CAST(SUBSTRING_INDEX(sku,"-",-1) AS SIGNED INTEGER)'); 
          if( $request->product_sku !="" ){
            $products=$products->where('sku',"LIKE",$request->product_sku."%");
          }
          if( $request->sku_range_from != "" && $request->sku_range_to != "" ){
            $whereInString = $this->returnSkuFromRange($request->sku_range_from,$request->sku_range_to);
            if( count($whereInString) > 0 ){
              foreach($whereInString as $k => $s_val){
                $products = $products->orWhere('sku','LIKE',$s_val.'__');
              }
            }
          }
          if($request->product_model != "") {
            $product_sku = ProductsModel::select('sku')->where('model', $request->product_model)->first();
            if($product_sku){
              $products = $products->where('sku', 'LIKE', "%".$product_sku->sku."%");
            }
          }
          if( $request->bystatus !="" ){
            $products=$products->where('status',$request->bystatus);
           }else{
             $products = $products->whereIn('status',[0,1]);
           }
           if( $request->generate_csv ){
            $per_page = 3000;
          }else{
            $per_page = 20;
          }
           $products = $products->paginate($per_page);
           if( $request->generate_csv != "" && $products->count() > 0 ){
            $products=$products->toArray();
            $products=$products['data'];
            $this->csvDashboardReport($products);
          }
          $old_input = $request->all();
          $product_types = PromotionTypeModel::where('product_status',1)->get();
          
        return view(self::VIEW_DIR. ".dashboard")->with(compact('products','old_input','product_types'));
    }

    private function returnSkuFromRange($from,$to){
        $from = explode('-',$from);
        $to   = explode('-',$to);
        $sku_letter = $from[0].'-';
        $from = $from[1];
        $to = $to[1];
        $sku_in = [];
        while($to >= $from){
          if($from < 10) $from = sprintf("%02d", $from);
          $sku_in[] = $sku_letter.$from;
          $from++;
        }
        return $sku_in;
      }

      public function csvDashboardReport($list){
        $file_name = "stockReport".date('dMY');
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="'.$file_name.'.csv"');
        $fp = fopen('php://output', 'w');
        fputcsv($fp, ['SKU','Options']);
        foreach ($list as $fields) {
          $row = [];
          $options = $fields['products_sizes'];
          $row[] = $fields['sku']."-".$fields['option_name'];
          if( is_array($options) && count($options) > 0 ){
            $size = $fields['oms_options']['option_name'];
            foreach($options as $option){
              $row[] = $size.' - '.$option['oms_option_details']['value'].' = '.$option['available_quantity'];
            }
          }
          fputcsv($fp, $row);
          fputcsv($fp, ['']);
        }
        
        fclose($fp);
        exit();
      }
    
      public function changeProductStatus(Request $request) {
        $update = OmsInventoryProductModel::where('product_id', $request->product_id)->update(['status' => $request->status]);
        if($update) {
            $msg = "Product status updated successfully in OMS only.";
            $omsProduct = OmsInventoryProductModel::where(['product_id' => $request->product_id])->first();
            if(!empty($omsProduct)) {
                if($request->status == 1) {
                    $this->changeGroupType($omsProduct->group_id);
                }
                $ba_product_status_update = false;
                $df_product_status_update = false;
                $ba_exist = ProductsModel::where('sku',$omsProduct->sku)->exists();
                $dr_exist = DressFairProductsModel::where('sku', $omsProduct->sku)->exists();
                if($ba_exist){ 
                    $ba_product_status_update  = ProductsModel::where('sku',$omsProduct->sku)->update(['status'=>$request->status]);
                    if($ba_product_status_update){
                    $msg = "Products updated successfully in Business Arcade.";
                    }
                }
                if($dr_exist) {
                    $df_product_status_update = DressFairProductsModel::where('sku',$omsProduct->sku)->update(['status' => $request->status]);
                    if($df_product_status_update) {
                    $msg = "Products updated successfully in Dressfair.";
                    }
                }

                if($ba_product_status_update && $df_product_status_update) {
                    $msg = "Products updated successfully in OMS, Business Arcade and Dressfair.";
                }elseif($ba_product_status_update && !$df_product_status_update) {
                    $msg = "Products updated successfully in OMS and Business Arcade.";
                }elseif($df_product_status_update && !$ba_product_status_update) {
                    $msg = "Products updated successfully in OMS and Dressfair.";
                }else{
                    $msg = "No Product found in Business Arcade and Dressfair Against".$omsProduct->sku;
                }
            }
        }
        return response()->json([
            'status' => $update,
            'msg' => $msg
        ]);
      }

      public function changeGroupType($group_id){
        if( $group_id > 0 ){
          $check_sale = OmsInventoryProductModel::join('oms_inventory_delivered_quantity as dl','dl.product_id','=','oms_inventory_product.product_id')
                      ->where('oms_inventory_product.group_id',$group_id)->first();
          if( !$check_sale ){
              ProductGroupModel::where('id',$group_id)->update(['product_type_id'=>3]);
          }
        }
      }

    public function viewInventory($sku, Request $request) {
        $exists = OmsInventoryProductModel::where('sku', $sku)->first();
        if($exists) {
            $duration = OmsSettingsModel::getByKey('duration');
            $product = OmsInventoryProductModel::with('ProductsSizes')->where('sku', $sku)->first();
        }
        $product_options = DB::table('oms_inventory_product')
                            ->join('oms_inventory_product_option','oms_inventory_product_option.product_id','=','oms_inventory_product.product_id')
                            ->join('oms_options_details','oms_options_details.id','=','oms_inventory_product_option.option_value_id')
                            ->where('oms_inventory_product.sku', $sku)
                            ->get();
        return view(self::VIEW_DIR. '.viewInventory')->with(compact('product_options','product'))->render();
    }

    public function inventoryProductHistory($id) {
      $history = OmsInventoryAddStockHistoryModel::with('user')->where('product_id', $id)->orderBy('history_id', 'DESC')->get();
      if(count($history) > 0) {
        return response()->json([
          'status' => true,
          'history' => $history
        ]);
      }else {
        return response()->json([
          'status' => false
        ]);
      }
    }

    public function inventoryEditProductLocation($id= null, Request $request) {
      if($request->isMethod('post')) {
        OmsInventoryProductModel::where('product_id', $request->product_id)->update(['row' => $request->row]);
        $product_option_id_ar = $request->product_option_id;
        $option_value_id_ar = $request->option_value_id;
        $rake_arr = $request->product_rakk;
        $shelf = $request->product_shelf;
        if(count($product_option_id_ar) > 0) {
          foreach ($product_option_id_ar as $key => $product_option) {
            $where = ['product_option_id'=>$product_option,'product_id'=>$request->product_id,'option_id'=>$request->option_id,'option_value_id'=>$option_value_id_ar[$key]];
            $data = ['rack' => $rake_arr[$key], 'shelf' => $shelf[$key]];
            $update_loc = OmsInventoryProductOptionModel::where($where)->update($data);
          }
        }
        if($update_loc){
          return response([
            'mesge' => 'Location updated successfully.'
          ]);
        }else{
          return response([
            'mesge' => 'Error,While updating location.'
          ]);
        }
      }else {
        $product = OmsInventoryProductModel::with('ProductsSizes.omsOptionDetails')->where('product_id', $id)->get();
        return view(self::VIEW_DIR. ".editLocation")->with(compact('product'));
      }
    }

    public function EditInventoryProduct($id, Request $request) {
      // dd($id);
      if($request->isMethod('post')) {

      }else {
        $option_value = OmsDetails::select('options','value')->where('options', 1)->get();
        $option_detail = OmsOptions::select('id','option_name')->where('id', '>', 1)->get();
        $inventory_product = OmsInventoryProductModel::where('product_id', $id)->get();
        
        return view(self::VIEW_DIR. '.editInventory')->with(compact('inventory_product','option_detail','option_value'))->render();
        
      }
    }

    public function EditInventoryProductOptionDetails() {
      $option_value = OmsDetails::select('value')->where('options','==', 1)->get();
      $option_detail = OmsOptions::select('id','option_name')->where('id', '>', 1)->get();
      $placeholder = $this->opencart_image_url.'no_image.png';
      $edit = '';
    }
 }
 

