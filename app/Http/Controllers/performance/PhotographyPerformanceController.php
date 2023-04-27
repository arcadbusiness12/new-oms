<?php
namespace App\Http\Controllers\EmployeePerformance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductGroup\PromotionScheduleSettingController;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\EmployeePerformanceSaleProductModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\PhotographySettingsDetail;
use App\Models\Oms\PhotographySetting;
use App\Models\Oms\PhotographySettingsSocialPosting;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\PaidAdsChatHistoryModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionScheduleSettingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\ProductPhotographyModel;
use App\Models\Oms\ProductPhotographyPostingModel;
use App\Models\OpenCart\Products\ProductsDescriptionModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Products\ProductSpecialModel;
use App\Models\OpenCart\Products\PromotionProductModel;
use App\Models\DressFairOpenCart\Products\ProductsDescriptionModel AS DFProductsDescriptionModel;
use App\Models\DressFairOpenCart\Products\ProductsModel AS DFProductsModel;
use App\Models\DressFairOpenCart\Products\ProductSpecialModel AS DFProductSpecialModel;
use App\Models\DressFairOpenCart\Products\PromotionProductModel AS DFPromotionProductModel;
use App\Models\Oms\EmployeeCustomDutyFileModel;
use App\Models\Oms\EmployeeCustomeDutiesModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\AdsTypeModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\Oms\SmartLookModel;
use App\Models\Oms\DailyAdResult;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\PaidAdsCampaign;
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
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Oms\OmsUserModel as OmsOmsUserModel;

class PhotographyPerformanceController extends Controller
{
  const VIEW_DIR = 'employee_performance.photography';
  private $DB_BAOPENCART_DATABASE = '';
  private $DB_DFOPENCART_DATABASE = '';
  function __construct(){
    $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
    $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
  }
public function test(){

}
public function changeGroup(Request $request){
  // dd($request->all());
  $row_id        =  $request->row_id; // for update
  $group_id      = $request->group_id;
  $schedule_date = $request->posting_date;
  $group_data = ProductGroupModel::select("name")->where("id",$group_id)->first();
  if($group_data){
    $group_name = $group_data->name;
    // $check = ProductPhotographyModel::where("product_group_name",$group_name)->where("product_group_id",$group_id)->first();
    $check_model = OmsUserModel::where("status",1)->where("user_id",$request->model_id)->first();
    $model_id = $check_model ? $request->model_id : 0;
    // if( !$check ){
      if($row_id > 0){ //upddate
        $upd_rec = ProductPhotographyModel::where("id",$row_id)->first();
        $upd_rec->product_group_id   = $group_id;
        $upd_rec->product_group_name = $group_name;
        $upd_rec->schedule_date      = $schedule_date;
        $upd_rec->save();
      }else{ //add new
        $new_entry = new ProductPhotographyModel();
        $new_entry->product_group_id   = $group_id;
        $new_entry->product_group_name = $group_name;
        $new_entry->schedule_date      = $schedule_date;
        $new_entry->model_id           = $model_id;
        $new_entry->save();
      }
    //}
  }
  return Redirect::back();
}
 public function saveShootData(Request $request){
    $product_group_id = $request->product_group_id;
    $product_group_name = $request->product_group_name;
    $task_id = $request->task_id;
    $update_rec = ProductPhotographyModel::where('product_group_id',$product_group_id)->where('product_group_name',$product_group_name)->where('id',$task_id)->update(['photo_shoot'=>1,"video_shoot"=>1]);
    if($update_rec){
      $update_rec = OmsInventoryProductModel::where('group_id',$product_group_id)->update(['designed'=>1,'photo_shoot'=>1,"video_shoot"=>1]);
      $resp = ['status'=>1,'msg'=>"Updated successfully."];
    }else{
      $resp = ['status'=>0,'msg'=>"error."];
    }
    return response()->Json($resp);
 }
 public function saveShootPosting(Request $request){
  $product_group_id = $request->product_group_id;
  $product_group_name = $request->product_group_name;
  $task_id   = $request->task_id;
  $social_id = $request->social_id;
  $df_video_link = $request->df_video_link;
  $ba_video_link = $request->ba_video_link;
  $create_posting = new ProductPhotographyPostingModel();
  $create_posting->promotion_social_id     = $social_id;
  $create_posting->product_photography_id  = $task_id;
  $create_posting->product_group_id        = $product_group_id;
  $create_posting->product_group_name      = $product_group_name;
  $create_posting->df_video_link           = $df_video_link;
  $create_posting->ba_video_link           = $ba_video_link;
  $create_posting->created_by              = session('user_id');
  if( $create_posting->save() ){
    if( $ba_video_link != ""  ){
      $this->updateBaProductVideoLink($product_group_id,$ba_video_link);
    }
    if( $df_video_link != ""  ){
      $this->updateDfProductVideoLink($product_group_id,$df_video_link);
    } 
    $resp = ['status'=>1,'msg'=>"Posted successfully."];
  }else{
    $resp = ['status'=>0,'msg'=>"error."];
  }
  if($request->ajax()){
    return response()->Json($resp);
  }else{
   return redirect()->back();
  }

}
private function updateBaProductVideoLink($group_id,$video_link){
  $data = OmsInventoryProductModel::where("group_id",$group_id)->get();
  if( $data && !empty($data)){
    foreach($data as $key=>$rec){
      $product = ProductsModel::where("sku",$rec->sku)->first();
      if( $product ){
        $product->video_link = $video_link;
        $product->save();
      }
    }
  }
}
private function updateDfProductVideoLink($group_id,$video_link){
  //
  $data = OmsInventoryProductModel::where("group_id",$group_id)->get();
  // dd($data->toArray());
  if( $data && !empty($data)){
    foreach($data as $key=>$rec){
      $product = DFProductsModel::where("sku",$rec->sku)->first();
      if( $product ){
        $product->video_link = $video_link;
        $product->save();
      }
    }
  }
}
 public function taskList() {
     // dd(Input::all());
    
    $request_url =  \Request::getRequestUri();
    $active_tab = '';
    $all_models = OmsUserModel::where("user_group_id",22);
    if( session('user_group_id') == 22 ){
      $all_models = $all_models->where("user_id",session('user_id'));
    }
    $all_models = $all_models->get();
    if( Input::get('model_id') && Input::get('model_id') > 0 ){
      $request_model_id = Input::get('model_id');
    }else{
      if( $all_models && !empty($all_models)){
        $request_model_id = $all_models[0]->user_id;
      }
    }

    $new_arrivals = ProductPhotographyModel::with(['photographyPosting','products'=>function($query){
      $query->where("status",1);
    }])->where(function($query) use ($request_model_id){
      $query->where('model_id',$request_model_id)->orWhere('model_id',0);
    })->get();
    
    // ->where('model_id',$request_model_id)->orWhereNull()->get();
    // dd($new_arrivals->toArray());
    $new_arrivals_data = [];
    
    foreach($new_arrivals as $val) {
      $new_arrivals_data[$val->schedule_date][] = $val;
    }
    // dd($new_arrivals_data);
    if(count(Input::all()) > 0) {
      if(Input::get('current')) {
        $today = date('Y-m-01');
        $list_date = date('Y-m-d', strtotime("+1 day"));
        $active_tab = Input::get('current');
      }else {
        $today = Input::get('previous_month');
        $list_date = Input::get('current_month');
        $active_tab = Input::get('previous');
      }
    }else{
      $today = date('Y-m-01');
        $list_date = date('Y-m-d', strtotime("+1 day"));
    }
    
    $days = $this->calculate_week_Days_for_listing($today, $list_date);
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
          'name' => date('M-Y', strtotime(-$i . 'month')),
          'month' => date('Y-m-01', strtotime(-$i . 'month'))
        ];
        array_push($previousMonths, $m);
      }
      
    }
    // dd("ok");
  
  // dd($previousMonths);
    $currentMonth = date('Y-m-d');
    $previousMonth = date('Y-m-01', strtotime('-1 month', time()));
    // dd(date('Y-m-01', strtotime($previousMonth.' +1 month', time())));
    $social_channel = SocialModel::where('photography_posting_status',1)->get();
    $all_groups = $this->returnAllGroups();
    // dd($all_groups->toArray());
    // echo "<pre>"; print_r($all_groups->toArray()); die;
    // $old_input = Input::all();
    // dd($new_arrivals_data);
    return view(self::VIEW_DIR.'.task_listing', compact('new_arrivals','days','new_arrivals_data','row_num','currentMonth','previousMonth','previousMonths','active_tab','social_channel','all_groups','all_models','request_url','request_model_id'));

 }
 protected function returnAllGroups(){
  $data = ProductGroupModel::with('producType')->whereHas("products",function($query){
      // $query->where("status",1);
    })
    ->leftJoin("oms_product_photography AS opp","opp.product_group_id","=","oms_inventory_product_groups.id")
    ->join("oms_inventory_product AS oip","oip.group_id","=","oms_inventory_product_groups.id")
    ->join("oms_inventory_product_option AS oipo","oipo.product_id","=","oip.product_id")
    ->select('oms_inventory_product_groups.*','opp.id AS photography_id')
    // ->whereNull('opp.id')
    // ->whereNotNull('opp.id')
    ->where('product_type_id','!=',5)
    //->whereIn('category_id',[1]) // 1 for bag
    // ->orderBy('product_type_id')
    //->orderByRaw('FIELD(`product_type_id`, 3,1,2,5,4,0) ASC, id DESC')
    ->groupBy('oms_inventory_product_groups.id')
    ->having(DB::raw('SUM(oipo.available_quantity)'),'>',0)
    ->get();
    return $data;
 }
 public function calculate_week_Days_for_listing($today, $list_date = null) {
  // dd(strtotime("+5 days", strtotime($today)));  
  $list_date = null;
  if($list_date) {
    $oneweekfromnow = strtotime($list_date);
  }else {
    // $oneweekfromnow = strtotime("+5 days", strtotime($today));
    $oneweekfromnow = strtotime(date('Y-m-t', strtotime($today)));
  }
  
  $oneweekfromnow = date('Y-m-d', $oneweekfromnow);
  $today = new DateTime($today);
  $oneweekfromnow = new DateTime($oneweekfromnow);
  $days = [];
  for($date = $today; $date <= $oneweekfromnow; $date->modify('+1 day')) {
      $dates = [
          'display_date' => $date->format('D-d-F'),
          'hiddn_date'   => $date->format('Y-m-d')
      ];
      array_push($days, $dates);
  }
  // dd($days);
  return $days;
} 
public function settings(){
  $store = 1;
  $socials = SocialModel::where('status', 1)->get();
  $posting_staff = OmsUserModel::where('status', 1)->where('user_access','LIKE','%employee-performance\\\\/photography\\\\/product-shoot%')->get();
  $models = OmsUserModel::where('status', 1)->whereIn('user_group_id',[22])->get();
  $types_for_setting = PromotionTypeModel::where('status', 1)->orderBy('name', 'ASC')->get();
  $categories = GroupCategoryModel::all();
  // real data
  $data = PhotographySetting::with('model')->get();
  // dd($data->toArray());
  return view(self::VIEW_DIR.'.settings',compact('data','store','socials','models','types_for_setting','categories','posting_staff'));
}
public function saveSettings(Request $request){
  // dd($request->all());
  $this->validate($request, [
    'setting_name' => 'required',
    'user' => 'required',
    //'type.*' => 'required',
    //'category.*' => 'required'
  ]);
  // dd( $request->all() );
  $posting_staff = $request->posting_staff;
  $type = $request->type;
  $category     = $request->category;
  $sub_category = $request->sub_category;
  $is_active    = $request->is_active;
  // echo "<pre>"; print_r($posting_staff); die("test");
  $new_setting = new PhotographySetting();
  $new_setting->name = $request->setting_name;
  $new_setting->model_id = $request->user;
  $new_setting->save();
  $setting_id = $new_setting->id;
  if( $setting_id > 0 ){
    //entry in social posting
    foreach($posting_staff as $social_id => $user_id){
      if( (int)$user_id < 1 ) continue;
      $new_setting_socials = new PhotographySettingsSocialPosting();
      $new_setting_socials->photography_settings_id = $setting_id;
      $new_setting_socials->user_id = $user_id;
      $new_setting_socials->promotion_social_id = $social_id;
      $new_setting_socials->save();
    }
    //entry in setting details
    if( $category && count($category) > 0 ){
      foreach( $category as $key => $cate ){
        if( $cate == "" ) continue; 
        $new_setting_details = new PhotographySettingsDetail();
        $new_setting_details->photography_settings_id   = $setting_id;
        $new_setting_details->promotion_product_type_id = $type[$key];
        $new_setting_details->category_id = $cate;
        $new_setting_details->sub_category_id = $sub_category[$key];
        $new_setting_details->status = $is_active[$key];
        $new_setting_details->save();
      }
    }

  }

  echo "success";
  // $data = PhotographySettingsDetail::all();
  // echo "<pre>"; print_r($data);
  // echo "<pre>"; print_r($data1);
  // dd($request->all());
} 
public function editSettings($setting_id){
  $models = OmsUserModel::where('status', 1)->whereIn('user_group_id',[22])->get();
  $socials = SocialModel::where('status', 1)->get();
  $posting_staff = OmsUserModel::where('status', 1)->where('user_access','LIKE','%employee-performance\\\\/photography\\\\/product-shoot%')->get();
  $types_for_setting = PromotionTypeModel::where('status', 1)->orderBy('name', 'ASC')->get();
  $categories = GroupCategoryModel::all();
  $data = PhotographySetting::with(['settingsDetail','SettingsSocialPosting'])->where('id',$setting_id)->first();
  //attach sub category
  if($data->settingsDetail){
    foreach($data->settingsDetail as $settingDetails){
        $settingDetails->sub_categories = GroupSubCategoryModel::where("group_main_category_id",$settingDetails->category_id)->get();
    }
  }
  // dd($data->toArray());
  // return view(self::VIEW_DIR.'.edit_setting_ajax_response',compact('data','models','socials','posting_staff','categories','types_for_setting'))->render();
  return view(self::VIEW_DIR.'.edit_settings',compact('data','models','socials','posting_staff','categories','types_for_setting'))->render();
  // echo $id;
}
public function updateSettings(Request $request){
  $this->validate($request, [
    'setting_name' => 'required',
  ]);
  $posting_staff = $request->posting_staff;
  $type = $request->type;
  $category     = $request->category;
  $sub_category = $request->sub_category;
  $is_active    = $request->is_active;
  $setting_id   = $request->setting_id;
  // echo "<pre>"; print_r($posting_staff); die("test");
  $new_setting = PhotographySetting::where('id',$setting_id)->first();
  $new_setting->name = $request->setting_name;
  $new_setting->model_id = $request->user;
  $new_setting->save();
  if( $setting_id > 0 ){
    //entry in social posting
    PhotographySettingsSocialPosting::where('photography_settings_id',$setting_id)->delete();
    foreach($posting_staff as $social_id => $user_id){
      if( (int)$user_id < 1 ) continue;
      $new_setting_socials = new PhotographySettingsSocialPosting();
      $new_setting_socials->photography_settings_id = $setting_id;
      $new_setting_socials->user_id = $user_id;
      $new_setting_socials->promotion_social_id = $social_id;
      $new_setting_socials->save();
    }
    PhotographySettingsDetail::where('photography_settings_id',$setting_id)->delete();
    //entry in setting details
    if( $type && count($type) > 0 ){
      foreach( $type as $key => $type ){
        $new_setting_details = new PhotographySettingsDetail();
        $new_setting_details->photography_settings_id   = $setting_id;
        $new_setting_details->promotion_product_type_id = $type;
        $new_setting_details->category_id = $category[$key];
        $new_setting_details->sub_category_id = $sub_category[$key];
        $new_setting_details->status = $is_active[$key];
        $new_setting_details->save();
      }
    }

  }
  // return view(self::VIEW_DIR.'.edit_setting_ajax_response',compact('data','models','socials','posting_staff','categories','types_for_setting'))->render();
  //return view(self::VIEW_DIR.'.edit_settings',compact('data','models','socials','posting_staff','categories','types_for_setting'))->render();
  // echo $id;
}
public function postingHistory(Request $request){
  // die("test$id");
  // dd($request->all());
  $data =  ProductPhotographyModel::with(['photographyPosting','model'])->where('product_group_id',$request->sku_group_id)->where('video_shoot',1)->get();
  // dd($data->toArray());
  return view(self::VIEW_DIR.'.posting-history',compact('data'))->render();
}
public function updateVideoLinkTest(){
    if( Input::get('query') == 1 ){
      $data = ProductPhotographyModel::with(['photographyPosting'=>function($query){
        $query->where("promotion_social_id",6);
      },'products'=>function($query){
        $query->where("status",1);
      }])->where(function($query) use ($request_model_id){
        $query->where('model_id',$request_model_id)->orWhere('model_id',0);
      })->get();
      // dd($data->toArray());
      foreach( $data as $key => $value ){
        if( $value->products ){
          foreach($value->products as $key1=>$product){
            if(isset($value->photographyPosting[0])){
              echo "<br>";
              echo $q = "UPDATE oc_product SET video_link='".$value->photographyPosting[0]->ba_video_link."' WHERE video_link IS NULL AND sku='$product->sku';";
              // die("testsdf");
            }
          } 
        }
      }
      die("test");
    }
}


}