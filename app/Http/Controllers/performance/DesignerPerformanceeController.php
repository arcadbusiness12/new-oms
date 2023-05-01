<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\EmployeePerformanceSaleProductModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionScheduleSettingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\OpenCart\Products\ProductsDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Products\ProductSpecialModel;
use App\Models\OpenCart\Products\PromotionProductModel;
use App\Models\DressFairOpenCart\Products\ProductsDescriptionModel AS DFProductsDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\DressFairOpenCart\Products\ProductSpecialModel AS DFProductSpecialModel;
use App\Models\DressFairOpenCart\Products\PromotionProductModel AS DFPromotionProductModel;
use App\Models\Oms\ProductPhotographyModel;
use App\Providers\Reson8SmsServiceProvider;
use App\Models\OpenCart\ExchangeOrders\ApiModel;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Session;
use Validator;
use Excel;

class DesignerPerformanceeController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.designer';
        function __construct(){
    }
  public function index(Request $request){
    // echo session('user_group_id'); die;
    $staffs = OmsUserModel::select('user_id','username')->whereIn('user_group_id',[3])->where('status',1)->get();
    $where = [];
    $data = EmployeePerformanceSaleModel::with(['sale_products','sale_person'])->where('user_id',session('user_id'))->orderBy('id','DESC')->get();
    // if(  session('user_group_id') == )
    // dd($data->toArray());
    $old_input = $request->all();
    return view(self::VIEW_DIR.'.all_daily_sale_progress',compact('data','old_input','staffs'));
  }
  // public function saveDailyWork(Request $request){
  //   if( $request->isMethod('post') ){
  //     dd($request->all());
  //   }
  //   $user_type_id = session('user_type_id');
  //   $store_id     = $user_type_id == 13 ? 1 : 2;
  //   $store_id     = 1;
  //   $yesterday_date = date('Y-m-d',strtotime('-1 day'));
  //   $end_date = date('Y-m-d',strtotime('+3 day'));
  //   $group_by_date = PromotionProductPostModel::select(DB::raw("DATE(date) AS s_date"))->whereDate('date','>=',$yesterday_date)->whereDate('date','<=',$end_date)->groupBy(DB::raw("DATE(date)"))->orderBy('date')->get();
  //   $group_by_time = PromotionProductPostModel::select(DB::raw("TIME(date) AS s_time"))->whereDate('date','>=',$yesterday_date)->whereDate('date','<=',$end_date)->groupBy(DB::raw("TIME(date)"))->orderBy('time')->get();
  //   // dd($group_by_time->toArray());
  //   $data = PromotionScheduleSettingMainModel::with(['productPosted'=>function($query){
  //     $query->with(['group','social_media']);
  //   }])->where('id',1)->where('store_id',$store_id)->where('posting_type',1)->get();
  //   // dd($data->toArray());
  //   return view(self::VIEW_DIR.'.save_daily_work',compact('data','group_by_date','group_by_time'));
  // }
  public function saveDailyWork(Request $request, $id=""){
    // dd($request);
    if( $request->isMethod('post') ){
      dd($request->all());
    }
    $store_id = 0;
    $user_group_id = session('user_group_id');
    if( $user_group_id == 13 ){
      $store_id = 1;
    }else if( $user_group_id == 14 ){
      $store_id = 2;
    }else if( $user_group_id == 1 ){
      $store_id = 'all';
    }
    $store = 1;
    $all_lists = PromotionScheduleSettingMainModel::where('is_deleted',0)->where('posting_type',1);
    if( $user_group_id != 1 ){
      $all_lists = $all_lists->where(function($query){
        // $query->where("designing_person",session('user_id'))->orWhere("posting_person",session('user_id'));
      });
    }
    $all_lists = $all_lists->get();
    if( $id == "" &&  $all_lists->count() > 0 ){
      $id = $all_lists[0]->id;
    }
    $templates = PromotionScheduleSettingMainModel::with('postPages')->select('id','social_ids', 'title','designing_person','posting_person','pages')->find($id);
    // dd($templates);
    if($templates) {
      $templates->pages = $templates->pages ? explode(",", $templates->pages) : [];
    }
    if( $id != "" ){
     $template_socials = explode(',', $templates->social_ids);
    }else{
      $template_socials = [];
    }
    $product_pro_posts = PromotionScheduleSettingModel::with('type')->where('main_setting_id', $id)->where('is_deleted', 0)->get();
    
    $socials = SocialModel::where('status', 1)->get();
    $post_where = [];
    if( $store_id > 0 ){
      $post_where[] = ['store_id'=>$store];
    }
    // $pro_posts = PromotionProductPostModel::with('group')->where($post_where)->get();
    $pro_posts = PromotionProductPostModel::with('group','promo_cate_posts')->where('posting_type',1)->whereDate('created_at','>',date('Y-m-d',strtotime('-7 days')))->get();
    // echo session('user_id'). "<br>";
    // dd($pro_posts->toArray());
    $days = $this->calculate_week_Days(date('Y-m-d',strtotime('-1 days')));
    // echo "<pre>"; print_r($days); die;
    // dd($templates->toArray());
    return view(self::VIEW_DIR.'.save_daily_work',compact('product_pro_posts', 'pro_posts', 'days', 'socials', 'templates', 'template_socials', 'store', 'id','all_lists'));
  }
 
 
  public function saveDailyWorkQuery(){

  }
  public function changePostStatus($id,$action, $page = null){
    $history = [];
    $post_complete = 0;
    $data = PromotionProductPostModel::with('main_setting.activity')->where('id',$id)->first();
    // dd($data);
    $date = explode(" ", $data->date);

    // echo "<pre>"; print_r(count(explode(",", $data->main_setting->pages))); die;
    $post_pages = count(explode(",", $data->main_setting->pages));

    $promo_status = "";
    if( $action == "posted" ){
      if( $data->store_id == 1 ){ //1 for ba
       $promo_status = $this->updateSitePromotionDateBa($data->group_id);
      }elseif( $data->store_id == 2 ){ //2 for df
        $promo_status = $this->updateSitePromotionDateDf($data->group_id);
      }
      
    }
    if( $data ){
      // if($data->main_setting->designing_person == $data->main_setting->posting_person) {
      //   if( $action == "designed" ){
      //     $history = array(
      //     'user' => $data->main_setting['designing_person'],
      //     'duty_id' => $data->main_setting->activity->id,
      //     'duty_name' => $data->main_setting->activity->name,
      //     'done_date' => $date[0],
      //     'created_at' => date('Y-m-d H:i:s'),
      //   );
      //  }
      // }else {
        // dd($data->toArray());
        if( $action == "designed" ){

          $history = array(
          'user' => $data->main_setting['designing_person'],
          'duty_id' => $data->main_setting->activity ? $data->main_setting->activity->id : 0,
          'post_id' => $data->main_setting->activity ? $data->main_setting->activity->id : 0,
          'duty_name' => $data->main_setting->activity ? $data->main_setting->activity->name : 0,
          'done_date' => $date[0],
          'created_at' => date('Y-m-d H:i:s'),
         );
        }
        // else {
        //   $history = array(
        //     'user' => $data->main_setting['designing_person'],
        //     'duty_id' => $data->main_setting->activity->id,
        //     'duty_name' => $data->main_setting->activity->name,
        //     'done_date' => $date[0],
        //     'created_at' => date('Y-m-d H:i:s'),
        //   );
        // }
        
    // }

      $data = PromotionProductPostModel::where('date',$data->date)->where('time',$data->time)->where('main_setting_id',$data->main_setting_id)->where('posting_type',$data->posting_type)->where('setting_id', $data->setting_id)->get();
      
      $flag = false;
      $photography = false;
      if($data){
        foreach($data as $key => $rec){
          // dd($rec);
            if($rec->pages) { // if pages entry avaible
             if($action == "posted") {
              $pages = explode(",", $rec->pages);
              $posted_for = $rec->total_pages ? explode(",", $rec->total_pages) : [];
              array_push($posted_for, $page);
              // $remain_pages = $t_page;
              if($post_pages <= 1) { // if in main setting pages less or equal to 1 and in posts are greater than 1
                $posted_for = $pages;
              }
              if(count($pages) == count($posted_for)) {
                // dd("post condition");
                $photography = true;
                $flag = true;
                $post_complete = 1;
                $update_data = [$action=>1,$action."_by"=>session('user_id'), 'total_pages' => implode(",", $posted_for)];
              }else {
                // dd("post else");
                $photography = false;
                $flag = false;
                $update_data = [$action."_by"=>session('user_id'), 'total_pages' => implode(",", $posted_for)];
              }
            }else {
              $photography = false;
              $flag = true;
              $update_data = [$action=>1,$action."_by"=>session('user_id')];
            }
            $upost = PromotionProductPostModel::where('id',$id)->orWhere('id', $rec->id)->update($update_data);
          }else {// if pages entry not avaible
            if($action == "posted") { $flag = false; $photography = true; } else { $flag = true; $photography = false;}
            $post_complete = 1;
            PromotionProductPostModel::where('id',$id)->update([$action=>1,$action."_by"=>session('user_id')]);
          }
          if($upost && $photography && $rec->product_type_id == 10) { 
             PromotionProductPostModel::where('id',$id)->update(['designed' =>1]);
             ProductPhotographyModel::where('product_group_id', $rec->group_id)->update(['is_organic_posted' => 1]);
          }
          
        }
      }

      // if($flag && count($history) > 0) {
      //   doneDutyHistory($history);
      // }
      
    }
    return response()->json([
      'status' => true,
      'mesg' => ucfirst($action)." Successfully.",
      'remain_pages' => $post_complete
    ]);
    // return redirect()->back()->with('query_status', ucfirst($action)." Successfully.")->with('promo_status',$promo_status);
  }
  protected function updateSitePromotionDateBa($product_group_id){
    $data = OmsInventoryProductModel::select('sku')->where('group_id',$product_group_id)->where('status',1)->get();
    $response = "";
    if( $data->count() > 0 ){
      $weak_after_date = date('Y-m-d',strtotime('+1 week'));
      foreach ($data as $key => $product) {
            $product_data = ProductsModel::select('product_id','sku','quantity','price')->where('sku',$product->sku)->first();
            if( $product_data ){
              $p_price = $product_data->price;
               $promotion_product_data =  PromotionProductModel::where('product_id',$product_data->product_id)->first();
               if( $promotion_product_data ){
                  // if( $promotion_product_data->date_expire > date('Y-m-d') ) continue;
                  $promotion_product_data->date_expire = $weak_after_date;
                    if($promotion_product_data->save()){
                      $special_price_update = ProductSpecialModel::where(["product_special_id"=>$promotion_product_data->special_id])->first();
                      if($special_price_update){
                        $promotion_product_data->value = $p_price-$special_price_update->price ? $special_price_update->price : 0;
                        $promotion_product_data->update();
                      if( $special_price_update->date_start == "0000-00-00" ){
                        $special_price_update->date_start = date('Y-m-d');
                      }
                      $special_price_update->date_end = $weak_after_date;
                      $special_price_update->save();
                      
                      // dd($p_price);
                      $response = "Promotion updated successfully in BA.";
                    }
                  }
               }else{
                  //no entry found in promotion
                  $product_special_data = ProductSpecialModel::where("product_id",$product_data->product_id)->where("date_end",'!=','0000-00-00')->orderBy('priority')->first();
                  
                  if($product_special_data){
                    $p_price = $p_price-$product_special_data->price ? $product_special_data->price : 0;
                    if( $product_special_data->date_start == "0000-00-00" ){
                      $product_special_data->date_start = date('Y-m-d');
                    }
                    $product_special_data->date_end = $weak_after_date;
                    if( $product_special_data->save() ){
                      $product_title_data = ProductsDescriptionModel::select('name')->where("product_id",$product->id)->first();
                      if( $product_title_data ){
                        $product_title_data = $product_title_data->name;
                      }
                      $org_quantity =  ($product_data->quantity/3) + $product_data->quantity;
                      PromotionProductModel::create(['product_id'=>$product_data->product_id,'special_id'=>$product_special_data->product_special_id,'name'=>$product_title_data,'type'=>'f','value'=>$p_price,'quantity'=>$org_quantity,'org_quantity'=>$product_data->quantity,'date_expire'=>$weak_after_date,'days_into_hour'=>1]);
                      // dd($p_price);
                      $response = "Promotion added successfully in BA.";
                    }
                  
                  }else{
                    //no special entry found
                    $product_special_data = ProductSpecialModel::where("product_id",$product_data->product_id)->orderBy('priority')->first();
                    $p_price = $p_price-$product_special_data->price ? $product_special_data->price : 0;
                    $product_special_insert = new ProductSpecialModel();
                    $product_special_insert->product_id = $product_data->product_id;
                    $product_special_insert->customer_group_id = 1;
                    $product_special_insert->priority = 0;
                    $product_special_insert->price = $product_special_data->price;
                    $product_special_insert->date_start = date('Y-m-d');
                    $product_special_insert->date_end   = $weak_after_date;
                    if( $product_special_insert->save() ){
                      $product_title_data = ProductsDescriptionModel::select('name')->where("product_id",$product->id)->first();
                      if( $product_title_data ){
                        $product_title_data = $product_title_data->name;
                      }
                      $org_quantity =  ($product_data->quantity/3) + $product_data->quantity;
                      PromotionProductModel::create(['product_id'=>$product_data->product_id,'special_id'=>$product_special_insert->product_special_id,'name'=>$product_title_data,'type'=>'f','value'=>$p_price,'quantity'=>$product_data->quantity,'org_quantity'=>$org_quantity,'date_expire'=>$weak_after_date,'days_into_hour'=>1]);
                      $response = "Promotion added successfully in DF.";
                    }
                  }
                }
            }else{
              $response =  "No product found in BusinessArcade website";
            }
      }
    }
    return $response;
  }
  protected function updateSitePromotionDateDf($product_group_id){
    $data = OmsInventoryProductModel::select('sku')->where('group_id',$product_group_id)->where('status',1)->get();
    $response = "";
    if( $data->count() > 0 ){
      $weak_after_date = date('Y-m-d',strtotime('+1 week'));
      foreach ($data as $key => $product) {
            $product_data = DFProductsModel::select('product_id','sku','quantity')->where('sku',$product->sku)->first();
            if( $product_data ){
              $p_price = $product_data->price;
               $promotion_product_data =  DFPromotionProductModel::where('product_id',$product_data->product_id)->first();
               if( $promotion_product_data ){
                    // if( $promotion_product_data->date_expire > date('Y-m-d') ) continue;
                    $promotion_product_data->date_expire = $weak_after_date;
                    if($promotion_product_data->save()){
                      $special_price_update = DFProductSpecialModel::where(["product_special_id"=>$promotion_product_data->special_id])->first();
                      
                      if($special_price_update){
                        $promotion_product_data->value = $p_price-$special_price_update->price ? $special_price_update->price : 0;
                        $promotion_product_data->update();
                      if( $special_price_update->date_start == "0000-00-00" ){
                        $special_price_update->date_start = date('Y-m-d');
                      }
                      $special_price_update->date_end = $weak_after_date;
                      $special_price_update->save();
                      $response = "Promotion updated successfully in DF.";
                    }
                  }
               }else{
                  //no entry found in promotion
                  $product_special_data = DFProductSpecialModel::where("product_id",$product_data->product_id)->where("date_end",'!=','0000-00-00')->orderBy('priority')->first();
                  //  echo "<pre>"; print_r($product_special_data); 
                  // dd($product_special_data);
                  if($product_special_data){
                    $p_price = $p_price- $product_special_data->price ? $product_special_data->price : 0;
                    if( $product_special_data->date_start == "0000-00-00" ){
                      $product_special_data->date_start = date('Y-m-d');
                    }
                    $product_special_data->date_end = $weak_after_date;
                    if( $product_special_data->save() ){
                      $product_title_data = DFProductsDescriptionModel::select('name')->where("product_id",$product->id)->first();
                      if( $product_title_data ){
                        $product_title_data = $product_title_data->name;
                      }
                      $org_quantity =  ($product_data->quantity/3) + $product_data->quantity;
                      DFPromotionProductModel::create(['product_id'=>$product_data->product_id,'special_id'=>$product_special_data->product_special_id,'name'=>$product_title_data,'type'=>'f','value'=>$p_price,'quantity'=>$product_data->quantity,'org_quantity'=>$org_quantity,'date_expire'=>$weak_after_date,'days_into_hour'=>1]);
                      $response = "Promotion added successfully in DF.";
                    }
                  
                  }else{
                    //no special entry found
                    $product_special_data = DFProductSpecialModel::where("product_id",$product_data->product_id)->orderBy('priority')->first();
                    if($product_special_data) {
                      $p_price = $p_price-$product_special_data->price ? $product_special_data->price : 0;
                      $product_special_insert = new DFProductSpecialModel();
                      $product_special_insert->product_id = $product_data->product_id;
                      $product_special_insert->customer_group_id = 1;
                      $product_special_insert->priority = 0;
                      $product_special_insert->price = $product_special_data->price;
                      $product_special_insert->date_start = date('Y-m-d');
                      $product_special_insert->date_end   = $weak_after_date;
                      if( $product_special_insert->save() ){
                        $product_title_data = DFProductsDescriptionModel::select('name')->where("product_id",$product->id)->first();
                        if( $product_title_data ){
                          $product_title_data = $product_title_data->name;
                        }
                        $org_quantity =  ($product_data->quantity/3) + $product_data->quantity;
                        DFPromotionProductModel::create(['product_id'=>$product_data->product_id,'special_id'=>$product_special_insert->product_special_id,'name'=>$product_title_data,'type'=>'f','value'=>$p_price,'quantity'=>$product_data->quantity,'org_quantity'=>$org_quantity,'date_expire'=>$weak_after_date,'days_into_hour'=>1]);
                        $response = "Promotion added successfully in DF.";
                      }
                     }
                    }
                    
                }
            }else{
              $response =  "No product found in DressFair website";
            }
      }
    }
    return $response;
  }
  public function calculate_week_Days($today, $list_date = null) {
    // dd($list_date);
    if($list_date) {
      $oneweekfromnow = strtotime($list_date);
    }else {
      $oneweekfromnow = strtotime("+6 days", strtotime($today));
    }
    
    $oneweekfromnow = date('Y-m-d', $oneweekfromnow);
    $today = new DateTime($today);
    $oneweekfromnow = new DateTime($oneweekfromnow);
    $days = [];
    for($date = $today; $date < $oneweekfromnow; $date->modify('+1 day')) {
        $dates = [
            'display_date' => $date->format('D-d-F'),
            'hiddn_date'   => $date->format('Y-m-d')
        ];
        array_push($days, $dates);
    }
    return $days;
}  

public function newProductImage(Request $request, $action = null) {
  $whereCluase = [];
  // dd($action);
  $active_tab = '';
  if(session('role') != 'ADMIN') {
    $whereCluase[] = array('npi.user_id', '=', session('user_id'));
  }
  $new_arrivals = OmsInventoryProductModel::select('oms_inventory_product.*','pop.model','pop.order_id','po.order_status_id','po.order_id','po.total','ipg.id as group_id','ipg.name as group_name')
                  ->leftJoin('oms_purchase_order_product as pop', 'pop.model', '=', 'oms_inventory_product.sku')
                  ->leftJoin('oms_purchase_order as po', 'po.order_id', '=', 'pop.order_id')
                  ->leftJoin('oms_inventory_product_groups as ipg', 'ipg.id', '=', 'oms_inventory_product.group_id')
                  ->leftJoin('new_product_images as npi', 'npi.product_id', '=', 'oms_inventory_product.product_id')
                  ->where($whereCluase)
                  ->where('po.order_status_id', '>=', 2)
                  ->where('oms_inventory_product.designed', 1)
                  ->where('oms_inventory_product.assigned_to_photoshoot', 1)
                  ->orderBy('oms_inventory_product.updated_at','DESC')
                  ->groupBy('oms_inventory_product.group_id')
                  ->get();
    $new_arrivals_data = [];
    // dd($new_arrivals);
    foreach($new_arrivals as $val) {
      $new_arrivals_data[$val->confirm_date][] = $val;
    }
    if(count($request->all()) > 0) {
      if($request->current) {
        $today = date('Y-m-01');
        $list_date = date('Y-m-d', strtotime("+1 day"));
        $active_tab = $request->current;
      }else {
        $today = $request->previous_month;
        $list_date = $request->current_month;
        $active_tab = $request->previous;
      }
    }else{
      $today = date('Y-m-01');
        $list_date = date('Y-m-d', strtotime("+1 day"));
    }
    // $days = $this->calculate_week_Days_for_listing($today, $list_date);
    $row_num = 0;
    foreach($new_arrivals_data as $val) {
      if(count($val) > $row_num) {
        $row_num = count($val);
      }
      
    }
    $previousMonths = [];
    $previousMonthName = [];
    for ($i = 0; $i <= 5; $i++) {
      if(date('Y-m-01', strtotime(-$i . 'month')) != date('Y-m-01')) {
        $m = [
          'name' => date('M-Y', strtotime(+$i . 'month')),
          'month' => date('Y-m-01', strtotime(+$i . 'month'))
        ];
        array_push($previousMonths, $m);
      }
      
    }
    // dd($previousMonths);
    $currentMonth = date('Y-m-d');
    $previousMonth = date('Y-m-01', strtotime('-1 month', time()));
    // echo $new_arrivals_data['2021-11-25'][0] ;
    // dd($new_arrivals_data);
   $days = $this->calculate_week_Days($today, $list_date);
    
  return view(self::VIEW_DIR.'.new_product_image', compact('new_arrivals','days','new_arrivals_data','row_num','currentMonth','previousMonth','previousMonths','active_tab','action'));
}

public function detailOfNewArrivalProduct($group) {
  $products = OmsInventoryProductModel::where('group_id', $group)->get();
  // dd($products);
  return response()->json([
    'status' => true,
    'products' => $products
  ]);
}
public function designNewArrivalProductImage($id) {
  $newProduct = OmsInventoryProductModel::where('group_id', $id)->update(['designed' => 2]);
    return response()->json([
      'status' => true,
      'mesg' => 'Designed successfully.'
    ]);
 }
}
