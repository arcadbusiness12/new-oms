<?php

namespace App\Http\Controllers\inventoryManagement;

use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\Products\DressFairProductsModel;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\InventoryManagement\OmsDetails;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockHistoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryAddStockOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryDeliveredQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOnholdQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryOptionValueModel;
use App\Models\Oms\InventoryManagement\OmsInventoryPackedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryReturnQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryShippedQuantityModel;
use App\Models\Oms\InventoryManagement\OmsInventoryStockModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\OpenCart\Orders\OrderStatusModel;
use App\Models\OpenCart\Products\OptionDescriptionModel;
use App\Models\OpenCart\Products\OptionValueDescriptionModel;
use App\Models\OpenCart\Products\ProductOptionValueModel;
use App\Models\OpenCart\Products\ProductsModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;

/**
 * Description of PurchaseController
 *
 * @author Siraj ALi
 */

 class InventoryManagementController extends Controller {
    const VIEW_DIR = 'inventoryManagement';
    const PER_PAGE = 20;
    private $opencart_image_url = '';
    private $oms_inventory_product_image_source_path = '';
    private $oms_inventory_product_image_source_url = '';
    
    function __construct() {
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
        $this->oms_inventory_product_image_source_path = public_path('uploads/inventory_products/');
        $this->oms_inventory_product_image_source_url = url('public/uploads/inventory_products/');
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
      if($request->isMethod('post')) {
        // dd($request->all());
        if($request->hasFile('image')) {
          $file = $request->image;
          $extension = $request->image->getClientOriginalExtension();
          $filename = md5(uniqid(rand(), true)).'.'.$extension;
          $file->move(base_path('public/uploads/inventory_products/'), $filename);
          $image = $filename;
          OmsInventoryProductModel::where('product_id', $id)->update(['image' => $image]);
        }
        OmsInventoryProductModel::where('product_id', $id)->update(['sku' => $request->sku]);
        $quantity = $request->quantity;
        $value = $request->value;
        $idxx = $request->idxx;

        foreach($idxx as $key=>$options){
          $var = @$quantity[$key];
          $var1 = $value[$key];
          if( $var1 < 1 ) continue;
          $exists = OmsInventoryProductOptionModel::where("product_id",$id)->where('option_value_id',$value[$key])->exists();
          if($exists){
            OmsInventoryProductOptionModel::where('product_option_id', $options)->update(['option_value_id'=>$var1]);
          }else{
            $prod_option_insert =  new OmsInventoryProductOptionModel();
            $prod_option_insert->product_id = $id;
            $prod_option_insert->option_id = $request->option_name;
            $prod_option_insert->option_value_id = $var1;
            $prod_option_insert->save();
          }
        }
        return redirect()->back()->with('success', 'Product updates successfully.');
      }else {
        $option_value = OmsDetails::select('options','value')->where('options', 1)->get();
        $option_detail = OmsOptions::select('id','option_name')->where('id', '>', 1)->get();
        $inventory_product = OmsInventoryProductModel::where('product_id', $id)->get();
        
        return view(self::VIEW_DIR. '.editInventory')->with(compact('inventory_product','option_detail','option_value'))->render();
        
      }
    }

    public function EditInventoryProductOptionDetails(Request $request) {
      $id = $request->taken_id;
      $placeholder = $this->opencart_image_url.'no_image.png';
      $ba_color_option_id = OmsInventoryOptionModel::baColorOptionId();
      $option_detail = OmsOptions::where('id','!=',$ba_color_option_id)->orderBy('option_name')->get();
      $option_value = OmsDetails::select('value')->where(['options'=>$ba_color_option_id])->orderBy('value')->get();
      $option_color = $request->option_color;

      $optionProWhereCl = [];
      $detailWhereCl = [];
      if($request->product_id > 0){
        $optionProWhereCl = ['oms_inventory_product_option.product_id' => $request->product_id];
      }else{
        $detailWhereCl = ['oms_options_details.options' => $id,'option_name'=>$request->option_color];
      }
      $pid = $request->product_id;
      $option_value_detail = OmsDetails::with(['productOption', 'productOption.product' => function($q1) use($detailWhereCl) {
        $q1->where($detailWhereCl);
      }])->whereHas('productOption', function($q) use($optionProWhereCl) {
        $q->where($optionProWhereCl);
      })->get();
      if($request->ajax()) {
            $data['placeholder'] = $placeholder;
            $data['option_detail'] = $option_detail;
            $data['option_value_detail'] = $option_value_detail;
            if( !empty($option_value_detail) ){
              $ids_arr = [];
              foreach ($option_value_detail as $key=>$val) {
                $current_opton_id = $val->options;
                $ids_arr[] = $val->id;
                $opt_idx = OmsInventoryProductOptionModel::where('option_value_id',$val->id)->first();
                if($opt_idx) {
                  $val['product_option_id'] = $opt_idx->product_option_id;
                }else{
                  $val['product_option_id'] = 0;
                }
                
              }
              $remaining_option_data = OmsDetails::whereNotIn('id', $ids_arr)->where('options',$current_opton_id)->get();
            }
      }
      return view(self::VIEW_DIR.".editInventoryOptionDetails")->with(compact('option_detail','placeholder','option_value_detail','remaining_option_data','option_value'))->render();
    } 
    
    public function addStock(Request $request, $id=null)
    { 
      $q = $request->input('a');
      $stocks = DB::table('oms_inventory_product_option')
      ->select('oms_inventory_product_option.product_option_id','oms_inventory_product_option.available_quantity','oms_inventory_product_option.onhold_quantity','oms_inventory_product_option.product_id','oms_options_details.value','oms_inventory_product.sku','oms_inventory_product.option_name','oms_inventory_product.image','oms_inventory_product.print_label','oms_inventory_product_option.option_id','oms_inventory_product_option.option_value_id')
      ->join('oms_options_details', 'oms_options_details.id', '=','oms_inventory_product_option.option_value_id')
      ->join('oms_inventory_product', 'oms_inventory_product.product_id', '=','oms_inventory_product_option.product_id')
      ->where('oms_inventory_product.sku',$q)
      ->get();
      // echo '<pre>'; print_r($stocks->toArray()); die;
      $proid = OmsInventoryProductOptionModel::select('product_option_id','available_quantity')->where('product_id', $id)->get();
      $quantity         = $request->option_quantity;
      $option_reason  = $request->option_reason;
      $option_id = $request->option_id;
      $option_value_id = $request->option_value_id;
      $option_value = $request->option_value;
        // echo "<pre>"; print_r($quantity); die;
      if($request->isMethod('post')){
        $comment = "";
        foreach($option_value as $key=>$value){
          $qty = $quantity[$key];
          if( abs($qty) > 0 ){
            $comment .= $value."-(".$qty."), ";
          }
        }
        $comment = "Added Stock by Admin (".session('username').").Quantity: ".$comment;
        $username = session('username');
        $user_id = session('user_id');
        $insertdata = new OmsInventoryAddStockHistoryModel;
        $insertdata->product_id = $id;
        $insertdata->comment =$comment;
        $insertdata->reason =$option_reason;
        $insertdata->user_id = $user_id;
        //bussniss arcade quantity updation.
        $ba_product = ProductsModel::where('sku',$request->sku)->first();
        // $ba_product_option = ProductOptionValueModel::where("product_id",$ba_product->product_id)->get();
        // dd($ba_product_option->toArray());
        DB::beginTransaction();
        OmsInventoryProductModel::where('product_id',$id)->update(['print_label' =>$request->print_label]);
        // dd(DB::connection('opencart')->select("select * from oc_product where product_id < 200")); die;
        try{
          $insertdata->save();
          // die("sdf");
          $current_total = 0;
          foreach($proid as $key=> $pro){
            $current_quantity = $quantity[$key];
            if(abs($current_quantity) < 1) continue;
            $current_total += $current_quantity;
            $qty = $pro->available_quantity += $current_quantity;
            OmsInventoryProductOptionModel::where('product_option_id',$pro->product_option_id)->update(['available_quantity' =>$qty]);
            //get ba option detials to update values
            if( !empty($ba_product) ){
              $baOptionData = OmsInventoryOptionValueModel::BaOptionsFromOms($option_id[$key],$option_value_id[$key]);
              if( !empty($baOptionData) ){
                ProductOptionValueModel::where(["product_id"=>$ba_product->product_id,"option_id"=>$baOptionData->ba_option_id,"option_value_id"=>$baOptionData->ba_option_value_id])->update(["quantity"=>DB::raw( 'quantity +'.$current_quantity)]);
              }
            }
            // echo "<pre>"; print_r($baOptionData->toArray());
            $OmsInventoryAddStockOptionModelObj = new OmsInventoryAddStockOptionModel();
            $OmsInventoryAddStockOptionModelObj->history_id=$insertdata->history_id;
            $OmsInventoryAddStockOptionModelObj->product_id=$id;
            $OmsInventoryAddStockOptionModelObj->option_id=$option_id[$key];
            $OmsInventoryAddStockOptionModelObj->option_value_id=$option_value_id[$key];
            $OmsInventoryAddStockOptionModelObj->quantity=$current_quantity;
            $OmsInventoryAddStockOptionModelObj->save();

          }
          //finally update color entry quantity and main product table quantity.
          $OmsColorOptionId =OmsOptions::colorOptionId();
          $OmsColorValueId = OmsDetails::colorId($request->color);
          $baOptionData = OmsInventoryOptionValueModel::BaOptionsFromOms($OmsColorOptionId,$OmsColorValueId);
          if( !empty($baOptionData) && !empty($baOptionData) && !empty($ba_product) ){
            $color_entry_upd = ProductOptionValueModel::where(["product_id"=>$ba_product->product_id,"option_id"=>$baOptionData->ba_option_id,"option_value_id"=>$baOptionData->ba_option_value_id])->update(["quantity"=>DB::connection(self::BA_DB_CONN_NAME)->raw('quantity +'.$current_total)]);
            if($color_entry_upd){
              ProductsModel::where('sku',$request->sku)->where("product_id",$ba_product->product_id)->update(["quantity"=>DB::connection(self::BA_DB_CONN_NAME)->raw('quantity +'.$current_total)]);
            }  
          }   
          // die("test twelve".$key);
          DB::commit();
          $this->updateSitesStock($request->sku);
          Session::flash('message','Stock updated successfully.');
        } catch (\Exception $e) {
        DB::rollback();
        Session::flash('message','Somthing wrong, stock not updated.'.$e);
      }
    }
    $product_id = DB::table('oms_inventory_product')->select('product_id')->where('sku',$q)->get();
    foreach($product_id as $prod){
      $product = $prod->product_id;
      $id = $prod->product_id;
    }
    $user_update = OmsInventoryAddStockHistoryModel::select('comment','updated_at')->orderBy('history_id','DESC')->where('product_id',$id)->limit(15)->get();
    // echo "<pre>"; print_r($user_update->toArray()); die;
    return view(self::VIEW_DIR.".addStock", ["old_input" => $request->all()])->with(compact('stocks','user_update')); 
  }

  public function destoryInventoryProduct($id) {
    $deletedProduct = OmsInventoryProductModel::where(['product_id' => $id])->delete();
    if($deletedProduct) {
      OmsInventoryProductModel::where('product_id', $id)->delete();
    }
    return redirect()->back()->with('success', 'Product Successfully Deleted');
  }

  public function printPendingStockLabel($product_id = null, Request $request) {
    $print_quant = $request->print_quant[$product_id];
        $print_label = '';
        $label_details = OmsInventoryProductOptionModel::select('*')->where('product_id', $product_id)->get();
        $label_array = array();
        if ($label_details->count()) {
            $label_details = $label_details->toArray();
            $color = "";
            foreach ($label_details as $option) {
                $product = OmsInventoryProductModel::select('image', 'sku', 'print_label','option_name','option_value')->where(
                    'product_id',
                    $option['product_id']
                )->first();
                if( $product &&  $product->option_value > 0 ){
                  $size = OmsDetails::where('options',$product->option_value)->where('id',$option['option_value_id'])->first()->value;
                }else{
                  $size = "";
                }
                $color  = $product->option_name;
                $barcode = $option['product_id'];
                $barcode .= $option['option_value_id'];
                $option_array = array(
                  'color'=>$color,
                  'size'=>$size
                );
                $print_label = $product['print_label'];
                $print_quant[$option['option_value_id']];
                $print_label = $request->label_type;
                for ($i = 0; $i < $print_quant[$option['option_value_id']]; $i++) {
                    if ($print_label === 'big') {
                        $label_array['big'][] = array(
                            'product_image' => $this->get_oms_product_image($product['image']),
                            'product_sku' => $product['sku'],
                            'option' => $option_array,
                            'barcode' => $barcode,
                        );
                    } else {
                        $label_array['small'][] = array(
                            'product_image' => $this->get_oms_product_image($product['image']),
                            'product_sku' => $product['sku'],
                            'option' => $option_array,
                            'barcode' => $barcode,
                        );
                    }
                }
            }
        }
        return view(self::VIEW_DIR.'.printLabelPending', ["labels" => $label_array, "label_type" => $print_label]);
  }

  protected function get_oms_product_image($image = '', $width = 0, $height = 0)
        {
          if (file_exists($this->oms_inventory_product_image_source_path.$image)) {
            if (!empty($width) && !empty($height)) {
              $ToolImage = new ToolImage();
              return $ToolImage->resize(
                $this->oms_inventory_product_image_source_path,
                $this->oms_inventory_product_image_source_url,
                $image,
                $width,
                $height
              );
            } else {
              return url('/uploads/inventory_products/'.$image);
            }
          } else {
            return $this->opencart_image_url.'placeholder.png';
          }
        }

  public function stockLevel() {
    return view(self::VIEW_DIR. '.stockLevel');
  }

  public function getProductSku(Request $request) {
    $skus = array();
      $product_skus = collect();
      if($request->product_sku) {
        $field = 'sku';
        $product_sku = $request->product_sku;
        $product_skus = OmsInventoryProductModel::select('sku')->where('sku', 'LIKE', "%{$product_sku}%")->limit(
              10
            )->get();
      }
      if($request->product_model) {
        $field = 'model';
        $product_model = $request->product_model;
        $product_skus = ProductsModel::select('model', 'sku')->where('model','LIKE',"%{$product_model}%")->limit(10)->get();
        
      }
      if ($product_skus->count() >0) {
        foreach ($product_skus as $product) {
          $skus[] = $product->$field;
        }
      }
   return response()->json(array('skus' => $skus));
  }

  public function getInventoryStockLevelProduct(Request $request) {
    // dd($request->all());
    $duration = OmsSettingsModel::getByKey('duration');
    $product_array = [];
    if($request->product_sku) {
      $product_sku = htmlentities($request->product_sku);
      $product = OmsInventoryProductModel::with('ProductsSizes')->where('sku', 'LIKE', "{$product_sku}%")->first();
      if($product) {
        $option_id = OmsSettingsModel::get('product_option', 'color');
        $options = OmsInventoryProductOptionModel::select('option_id','option_value_id','available_quantity','onhold_quantity')
                                            ->where('product_id', $product->product_id)->get()->toArray();
        $product_options = [];
        $now = Carbon::now();
        $last = $now->subDays(30);
        $last_date = Carbon::createFromFormat('Y-m-d H:i:s', $last)->toDateTimeString();
        foreach($options as $option) {
          $stock_level = OmsInventoryStockModel::select('minimum_quantity', 'average_quantity')->where('product_id',$product->product_id)
                                                ->where('option_id', $option['option_id'])->where('option_value_id',$option['option_value_id'])->get()->first();
          $name = OptionDescriptionModel::select('name')->where('option_id', $option['option_id'])->first();
          $value = OptionValueDescriptionModel::select('name')->where('option_value_id',$option['option_value_id'])->first();
          // average quantity
          $quantity = OmsInventoryDeliveredQuantityModel::where('product_id', $product->product_id)->where('option_id', $option['option_id'])
                                                          ->where('option_value_id', $option['option_value_id'])
                                                          ->where('created_at', '>=', $last_date)->get();
         
          if($option['option_id'] == $option_id) {
            $product_options['static'] = array(
              'option_id' => $option['option_id'],
              'option_value_id' => $option['option_value_id'],
              'name' => $name ? $name->name : '',
              'value' => $value ? $value->name : '',
              'quantity' => $option['available_quantity'],
              'minimum_quantity' => $stock_level ? $stock_level->minimum_quantity : '',
              'average_quantity' => count($quantity),
            );
          } else {
            $product_options[] = array(
              'option_id' => $option['option_id'],
              'option_value_id' => $option['option_value_id'],
              'name' => $name ? $name->name : '',
              'value' => $value ? $value->name : '',
              'quantity' => $option['available_quantity'],
              'minimum_quantity' => $stock_level ? $stock_level->minimum_quantity : '',
              'average_quantity' => count($quantity),
            );
          }
        }
        $stock_level = OmsInventoryStockModel::select('minimum_quantity', 'average_quantity')->where('product_id',$product->product_id)->get()->first();
        $product_array = array(
          'product_id' => $product->product_id,
          'image' => $this->get_oms_product_image($product->image, 100, 100),
          'model' => $product->model,
          'name' => $product->name,
          'sku' => $product->sku,
          'quantity' => $product->available_quantity,
          'minimum_quantity' => $stock_level ? $stock_level->minimum_quantity : '',
          'average_quantity' => $stock_level ? $stock_level->average_quantity : '',
          'cost' => $product->price,
          'options' => $product_options,
        );
      }
    }

    return view(self::VIEW_DIR. '.stockLevelSearch', ["duration" => $duration, "product" => $product_array, "old_input" => $request->all()]);
  }

  public function checkStockLevelDurationQuantity(Request $request) {
    // dd($request->all());
    $duration = $request->duration;
    $product_sku = $request->product_sku;
    $options = json_decode($request->options, true);
    $today = Carbon::now();
    $today_date = Carbon::createFromFormat('Y-m-d H:i:s', $today)->toDateTimeString();
    $now = Carbon::now();
    $last = $now->subDays($duration);
    $last_date = Carbon::createFromFormat('Y-m-d H:i:s', $last)->toDateTimeString();
    $opencartProduct = OmsInventoryProductModel::select('product_id')->where('sku', $product_sku)->first();
    $product_id = 0;
    // dd($last_date);
    if ($opencartProduct) {
      $product_id = $opencartProduct->product_id;
    }
    $quantities = array();
    if ($options) {
      foreach ($options as $key => $value) {
        $product_options = OmsInventoryProductOptionModel::select(
          'option_value_id',
          'option_id'
        )->where('product_id', $product_id)->where('option_id', $value['option_id'])->where(
          'option_value_id',
          $value['option_value_id']
        )->first();
        if ($product_options) {
          $product_options = $product_options->toArray();
          $quantity = OmsInventoryDeliveredQuantityModel::where('product_id', $product_id)->where('option_id', $product_options['option_id'])
                                                        ->where('option_value_id', $product_options['option_value_id'])
                                                        ->where('created_at', '>=', $last_date)->get();
          $quantities[] = array(
            'option_id' => $value['option_id'],
            'option_value_id' => $value['option_value_id'],
            'quantity' => count($quantity),
          );
        }
      }
    } else {
      $quantity = OmsInventoryDeliveredQuantityModel::where('product_id', $product_id)->where('created_at', '>=', $last_date)->first();
      $quantities = $quantity->total;
    }
    return response()->json(array('success' => 1, 'quantity' => $quantities));
  }

  public function updateStockLevel(Request $request) {
    foreach($request->product as $product_id => $options) {
      foreach($options as $option) {
        $stockExist = OmsInventoryStockModel::where('product_id', $product_id)->where('option_id',$option['option_id'])->where('option_value_id', $option['option_value_id'])->exists();
        if ($stockExist) {
          OmsInventoryStockModel::where('product_id', $product_id)
                                  ->where('option_id', $option['option_id'])
                                  ->where('option_value_id', $option['option_value_id'])
                                  ->update(
                                    array(
                                      'minimum_quantity' => $option['min_quantity'],
                                      'average_quantity' => $option['average_quantity'],
                                      'duration' => $request->duration,
                                    ));
        }else {
          $OmsInventoryStockModel = new OmsInventoryStockModel();
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_PRODUCT_ID} = $product_id;
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_OPTION_ID} = $option['option_id'];
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_OPTION_VALUE_ID} = $option['option_value_id'];
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_MINIMUM_QUANTITY} = $option['min_quantity'];
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_AVERAGE_QUANTITY} = (int)$option['average_quantity'];
          $OmsInventoryStockModel->{OmsInventoryStockModel::FIELD_DURATION} = $request->duration;
          $OmsInventoryStockModel->save();
        }
      }
    }
    Session::flash('message', 'Product stock level updated successfully.');
    Session::flash('alert-class', 'alert-success');
    return redirect()->back()->with('success', 'Product updates successfully.');
  }

  public function stockReport(Request $request) {
    $whereClause = [];
    $statusWhereClause = '';
    if($request->product_sku) {
      $whereClause[] = array('sku', 'LIKE', $request->sku.'%');
    }
    if ($request->product_model) {
      $sproduct_sku = ProductsModel::select('sku')->where('model', $request->product_model)->first();
      $whereClause[] = array('sku', 'like', $sproduct_sku->sku.'%');
    }
    if ($request->status) {
      $status = $request->status; 
      if($status != 'overall') {
        $statusWhereClause = $status.'_quantity';
      }
    }else {
      $status = 'overall';
    }
    $products = OmsInventoryProductModel::with(['omsOptions','ProductsSizes'])->where($whereClause);
    if($request->product_sku){
      $products->orderBy('sku');
    }
    $products = $products->paginate(10);
    if ($request->from_date && $request->to_date) {
      $whereClauseOption = [];
      $whereClauseOption[] = array('updated_at', '>=', $request->from_date);
      $whereClauseOption[] = array('updated_at', '<=', $request->to_date);
      foreach($products as $key => $product) {
        if($product->ProductsSizes &&  !empty($product->ProductsSizes) ){
          foreach($product->ProductsSizes as $sizes){
            //onhold query start==============================================================================================================
            $onhold_qry = OmsInventoryOnholdQuantityModel::select(
              DB::Raw('SUM(quantity) as onhold_quantity')
            )->where('product_id', $sizes->product_id)->where('option_id', $sizes->option_id)->where('option_value_id', $sizes->option_value_id)
            ->where($whereClauseOption)->first(); 
            $sizes->onhold_quantity = $onhold_qry->onhold_quantity ? $onhold_qry->onhold_quantity : 0;
            //onhold query End===============================================================================================================
            //packed query start==============================================================================================================
            $packed_qry = OmsInventoryPackedQuantityModel::select(
              DB::Raw('SUM(quantity) as pack_quantity')
            )->where('oms_product_id', $sizes->product_id)->where('option_id', $sizes->option_id)->where('option_value_id', $sizes->option_value_id)
            ->where($whereClauseOption)->first();
            $sizes->pack_quantity = $packed_qry->pack_quantity ? $packed_qry->pack_quantity : 0;
            //packed query End=================================================================================================================
            //shipped query start==============================================================================================================
            $shipped_qry = OmsInventoryShippedQuantityModel::select(
              DB::Raw('SUM(quantity) as shipped_quantity')
            )->where('product_id', $sizes->product_id)->where('option_id', $sizes->option_id)->where('option_value_id', $sizes->option_value_id)
            ->where($whereClauseOption)->first();
            $sizes->shipped_quantity = $shipped_qry->shipped_quantity ? $shipped_qry->shipped_quantity : 0;
            //shipped query End=================================================================================================================
            //delivered query start==============================================================================================================
            $delivered_qry = OmsInventoryDeliveredQuantityModel::select(
              DB::Raw('SUM(quantity) as delivered_quantity')
            )->where('product_id', $sizes->product_id)->where('option_id', $sizes->option_id)->where('option_value_id', $sizes->option_value_id)
            ->where($whereClauseOption)->first();
            $sizes->delivered_quantity = $delivered_qry->delivered_quantity ? $delivered_qry->delivered_quantity : 0;
            //delivered query End================================================================================================================
            //return query start=================================================================================================================
            $return_qry = OmsInventoryReturnQuantityModel::select(
              DB::Raw('SUM(quantity) as return_quantity')
            )->where('oms_product_id', $sizes->product_id)->where('option_id', $sizes->option_id)->where('option_value_id', $sizes->option_value_id)
            ->where($whereClauseOption)->first();
            $sizes->return_quantity = $return_qry->return_quantity ? $return_qry->return_quantity : 0;
            //return query End===================================================================================================================
          }
          // echo "<pre>"; print_r($product->ProductsSizes->toArray()); echo "<pre>";
        }
      }
    }
    $old_input = $request->all();
    $products = $products->appends($old_input);
    $page = $products;
    $pagination = $products->render();
    return view(self::VIEW_DIR. '.stockReport')->with(compact('products','page','pagination','status', 'old_input'));
  }

  public function inventoryAlarm(Request $request) {
    $whereClause = [];
    if (count($request->all()) > 0) {
      if ($request->product_sku) {
        $whereClause[] = array('sku', 'LIKE', "{$request->product_sku}%");
      }
      if ($request->from_date) {
        $whereClause[] = array('created_at', '>=', $request->from_date);
      }
      if ($request->status_id != '') {
        $whereClause[] = array('status', $request->status_id);
      }
    }
    if ($request->to_date) {
      $whereClause[] = array('created_at', '<=', $request->to_date);
    } else {
      $today = Carbon::now();
      $today_date = Carbon::createFromFormat('Y-m-d H:i:s', $today)->toDateTimeString();
      $now = Carbon::now();
      $last = $now->subDays(30);
      $last_date = Carbon::createFromFormat('Y-m-d H:i:s', $last)->toDateTimeString();
    }
    $product_array = array();
    $product_array_count = 0;
    $products = OmsInventoryProductModel::with(['omsOptions','ProductsSizes.omsOptionDetails','stockLevels'])->select('*')
                                          ->where($whereClause)->whereHas('stockLevels');
    $products = $products->get();

    $out_stock = [];
    if ($products->count()) {
      foreach ($products as $product) {
        $total_less_quantity = 0;
        $options = array();
        foreach ($product->ProductsSizes as $key => $value) {
          $min_quantity = OmsInventoryStockModel::select('minimum_quantity')->where('product_id',$value['product_id'])->where('option_id', $value['option_id'])
                                                  ->where('option_value_id',$value['option_value_id'])->first();
          $value['minimum_quantity'] = $min_quantity->minimum_quantity;
          if ($min_quantity && $value['available_quantity'] < $min_quantity->minimum_quantity) {
            $option_name = OmsOptions::select('option_name')->where('id',$value['option_id'])->first();
            $option_value_name = OptionValueDescriptionModel::select('name')->where('option_value_id',$value['option_value_id'])->first();
            $less_quantity = $min_quantity->minimum_quantity - $value['available_quantity'];
            $options[] = array(
              'name' => $option_name->option_name,
              'value' => $option_value_name->name,
              'available_quantity' => $value['available_quantity'],
              'less_quantity' => $less_quantity,
            );
            $value['out_qty'] = 1;
            array_push($out_stock, $product);
            // continue;
            $total_less_quantity = $total_less_quantity + $less_quantity;
          }else {
            $value['out_qty'] = 0;
          }
        } 
        if ($request->dashboard) {
          $product_array_count = $product_array_count + 1;
        } else {
          $product_array[] = array(
            'product_id' => $product['product_id'],
            'image' => $this->get_oms_product_image($product['image'], 100, 100),
            'sku' => $product['sku'],
            'total_less' => $total_less_quantity,
            'options' => $options,
          );
        }
      }
    }
    $out_stock = array_unique($out_stock);
    $out_stock = customPaginate($out_stock, 10);
    // dd($out_stock);
    $order_statuses = OrderStatusModel::get()->toArray();
    if ($request->dashboard) {
      return response()->json(["count" => $product_array_count]);
    } else {
      return view(self::VIEW_DIR.'.inventoryAlarm',["products" => $out_stock,"order_statuses" => $order_statuses,"old_input" => $request->all(),"page" => $products,]
      );
    }
  }

  public function orderOutStockProduct(Request $request) {
    dd($request->all());
  }
 }
 

