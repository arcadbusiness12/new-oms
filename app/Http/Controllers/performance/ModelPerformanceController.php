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
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\PaidAdsChatHistoryModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionScheduleSettingModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\ProductModelingModel;
use App\Models\Oms\ProductModelingPostingModel;
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

class ModelPerformanceController extends Controller
{
  const VIEW_DIR = 'employee_performance.model';
  private $DB_BAOPENCART_DATABASE = '';
  private $DB_DFOPENCART_DATABASE = '';
  function __construct(){
    $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
    $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
  }
 public function saveShootData(Request $request){
    $product_group_id = $request->product_group_id;
    $product_group_name = $request->product_group_name;
    $task_id = $request->task_id;
    $update_rec = ProductModelingModel::where('product_group_id',$product_group_id)->where('product_group_name',$product_group_name)->where('id',$task_id)->update(['photo_shoot'=>1,"video_shoot"=>1]);
    if($update_rec){
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
  $create_posting = new ProductModelingPostingModel();
  $create_posting->promotion_social_id     = $social_id;
  $create_posting->product_modeling_id  = $task_id;
  $create_posting->product_group_id        = $product_group_id;
  $create_posting->product_group_name      = $product_group_name;
  $create_posting->created_by              = session('user_id');
  if( $create_posting->save() ){
    $resp = ['status'=>1,'msg'=>"Posted successfully."];
  }else{
    $resp = ['status'=>0,'msg'=>"error."];
  }
  return response()->Json($resp);
}
 public function taskList() {
  //  dd(Input::all());
  
  $active_tab = '';
    // $new_arrivals = OmsInventoryProductModel::select('oms_inventory_product.*','pop.model','pop.order_id','po.order_status_id','po.order_id','po.total','ipg.id as group_id','ipg.name as group_name')
    //                 ->leftJoin('oms_purchase_order_product as pop', 'pop.model', '=', 'oms_inventory_product.sku')
    //                 ->leftJoin('oms_purchase_order as po', 'po.order_id', '=', 'pop.order_id')
    //                 ->leftJoin('oms_inventory_product_groups as ipg', 'ipg.id', '=', 'oms_inventory_product.group_id')
    //                 ->where('po.order_status_id', '>=', 2)
    //                 ->where('oms_inventory_product.listing', 0)
    //                 ->orderBy('oms_inventory_product.updated_at','DESC')
    //                 ->groupBy('oms_inventory_product.group_id')
    //                 ->get();
      // dd($new_arrivals->toArray());
      $new_arrivals = ProductModelingModel::with('modelingPosting')->get();
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
    // echo "<pre>"; print_r($days); die;
    $social_channel = SocialModel::where('model_posting_status',1)->get();
    return view(self::VIEW_DIR.'.task_listing', compact('new_arrivals','days','new_arrivals_data','row_num','currentMonth','previousMonth','previousMonths','active_tab','social_channel'));

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

}