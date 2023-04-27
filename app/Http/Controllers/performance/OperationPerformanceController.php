<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use App\Models\Oms\CommissionSetting;
use App\Models\Oms\DailyAdResult;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\EmployeePerformanceSaleProductModel;
use App\Models\Oms\EmployeePerformanceDetailModel;
use App\Models\Oms\DoneDutyHistroryModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\CustomerChatModel;
use DateInterval;
use DatePeriod;
use DateTime;
use DB;
use Session;
use Illuminate\Http\Request;

class OperationPerformanceController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.operation';
    
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';

    function __construct(){
      $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
      $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
    }

    public function saveConversation(Request $request) {
        if( $request->isMethod('post') ) {
          $sale_perf_ids = $request->sale_perf_id;
          // dd($sale_perf_ids);
          $conversation_opening = $request->conversation_opening;
          $closing_conversation = $request->conversation_closing;
          $user_ids              = $request->user_id;
          $confirm_status_pub_prd = $request->confirm_status_pub_prd;
          $confirm_status_pub_prd_detail = $request->confirm_status_pub_prd_detail;
          $confirm_status_id = $request->confirm_status_id;
          $confirm_detail_id = $request->confirm_detail_id;
          $chat_id = $request->chat_id;
          $chat_prd = $request->chat_prd;
          //confirmation code start.
          $blc_confirm   = $request->blc_confirm;
          $bs_confirm    = $request->bs_confirm;
          $cs_confirm    = $request->cs_confirm;
          $ocf_confirm   = $request->ocf_confirm;
          $cr_confirm    = $request->cr_confirm;
          // echo "<pre>"; print_r($ocf_confirm); die;
          //confirm sale progress start
          foreach($user_ids as $user_id){
            if( isset($sale_perf_ids[$user_id]) && $sale_perf_ids[$user_id] > 0 ){
              if($confirm_status_id && array_key_exists($user_id, $confirm_status_id)) {
                foreach($confirm_status_id[$user_id] as $per_id) {
                  EmployeePerformanceModel::where('id',$per_id)->update(['confirm' => $confirm_status_pub_prd[$user_id][$per_id], 'updated_by' => session('user_id')]);
                }
              }

              if($confirm_status_pub_prd_detail && array_key_exists($user_id, $confirm_status_pub_prd_detail)) {
                if(isset($confirm_status_pub_prd_detail[$user_id])) {
                  foreach($confirm_detail_id[$user_id] as $detail_id) {
                    // dd($detail_id);
                    $details = EmployeePerformanceDetailModel::find($detail_id);
                    $details->confirm = $confirm_status_pub_prd_detail[$user_id][$detail_id];
                    $details->update();
                    // dd($details);
                    EmployeePerformanceModel::where('id',$details['employee_performance_id'])->update(['detailed' => 1]);
                  }
                }
              }

              if(isset($chat_id) && isset($chat_id[$user_id]) && isset($chat_prd)) {
                if($chat_id[$user_id][0]) {
                  foreach($chat_id[$user_id] as $chat) {
                    // dd([$chat]);
                    EmployeePerformanceModel::where('id',$chat)->update(['achieved' => $chat_prd[$user_id][$chat], 'updated_by' => session('user_id')]);
                  
                 }
                }else {
                  $activity_id = $request->activity_id;
                  if( isset($chat_prd) && isset($chat_id[$user_id])){
                    foreach($chat_prd[$user_id] as $key => $chat) {
                      $updateOrCreateArr = ['duty_list_id' =>$activity_id[$user_id][$key],'achieved' => $chat,'user_id' => $user_id,'created_at' => date('Y-m-d'), 'updated_by' => session('user_id')];  
                        EmployeePerformanceModel::updateOrCreate(
                        ['user_id'=>$user_id,'duty_list_id' =>$activity_id[$user_id][$key],'created_at'=>date('Y-m-d')],$updateOrCreateArr);
                  }
                  }
                }

              }

          }else {
            $activity_id = $request->activity_id;
              if( isset($chat_prd) && isset($chat_id[$user_id])){
                 foreach($chat_prd[$user_id] as $key => $chat) {
                  //  dd($activity_id[$user_id][$key]);
                  // EmployeePerformanceModel::where('id',$chat)->update(['achieved' => $chat_prd[$user_id][$chat], 'updated_by' => session('user_id')]);
                  $updateOrCreateArr = ['duty_list_id' =>$activity_id[$user_id][$key],'achieved' => $chat,'user_id' => $user_id,'created_at' => date('Y-m-d'), 'updated_by' => session('user_id')];  
                    EmployeePerformanceModel::updateOrCreate(
                    ['user_id'=>$user_id,'duty_list_id' =>$activity_id[$user_id][$key],'created_at'=>date('Y-m-d')],$updateOrCreateArr);
              }
               }
              
            }
            DoneDutyHistroryModel::where('user_id', $user_id)->where('done_date', date('Y-m-d'))->delete();
            $perfomance = EmployeePerformanceModel::with('performanceDetails', 'activity')->where('user_id', $user_id)->where('created_at', date('Y-m-d'))->get();
            // dd($perfomance->toArray());
            foreach($perfomance as $perfom) {
              if($perfom->detailed == 1) {
                $details = EmployeePerformanceDetailModel::where('employee_performance_id', $perfom->id)->where('confirm', 1)->count();
                // for($i = 0; $i < $details; $i ++) {
                //   $history = array(
                //     'user' => $perfom->user_id,
                //     'duty_id' => $perfom->duty_list_id,
                //     'duty_name' => $perfom->activity['name'],
                //     'done_date' => date('Y-m-d'),
                //     'created_at' => date('Y-m-d H:i:s'),
                //   );
                //     doneDutyHistory($history);
                // }
              }else{
                if($perfom->confirm == 1) {
                  for($i = 0; $i < $perfom->achieved; $i ++) {
                    $history = array(
                      'user' => $perfom->user_id,
                      'duty_id' => $perfom->duty_list_id,
                      'duty_name' => $perfom->activity['name'],
                      'done_date' => date('Y-m-d'),
                      'created_at' => date('Y-m-d H:i:s'),
                    );
                     
                  }
                  
                }
                
              }
              
            }
        }
        Session::flash('query_status', 'Conversation Saved Successfully !');
      }
        $sale_team = OmsUserModel::with(['activities', 'performance_sales' => function($q) {
            $q->where('created_at', date('Y-m-d'));
            $q->with(['performanceDetails' => function($detail) {
              $detail->where('created_at', date('Y-m-d'));
            }]);
          }])->select('user_id','username','firstname','lastname')->where('user_group_id',12)->where('status',1)->get();

          foreach($sale_team as $team) {
            // for today order 
            $totalOrders = null;
            $data = DB::table("oms_place_order AS opo")
            ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
            ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
            ->select(DB::raw("COUNT(*) AS total_order"))
            ->where('sp.status',1)
            ->where(function ($query) {
              $query->where('ord.oms_order_status','!=',5)
                  ->orWhereNull('ord.oms_order_status');
            });
              $data = $data->whereDate('opo.created_at', date('Y-m-d'));
              $data = $data->where("opo.user_id", $team->user_id);
              $data = $data->groupBy("opo.user_id")
            ->first();
            if($data) {
       
               $totalOrders = $data->total_order;
            }
           // dd($totalOrders);
             foreach($team->activities as $duty) {
               $group_ids = [];
               if(count($team->performance_sales) > 0) {
                 foreach($team->performance_sales as $performance_sales) {
                   // dd($duty);
                   if($duty->activity_id == $performance_sales->duty_list_id) {
                     if(count($performance_sales->performanceDetails) > 0) {
                       foreach($performance_sales->performanceDetails as $performanceDetails) {
                         $new_array = array(
                           'id' => $performanceDetails->id,
                           'name' => $performanceDetails->product_group_name,
                           'confirm' => ($performanceDetails->confirm) ? $performanceDetails->confirm : 0,
                         );
                         array_push($group_ids, $new_array);
                       }
                      
                       $duty->achieved = $group_ids;
                       $duty->confirm = $performance_sales->confirm;
                     }else {
                       if($duty->activity_id == 2) {
                         $duty->achieved = $totalOrders;
                         $duty->confirm = $performance_sales->confirm;
                       }else {
                         $duty->achieved = $performance_sales->achieved;
                         $duty->confirm = $performance_sales->confirm;
                       }
                         
                     }
                     
                     $duty->performance_id = $performance_sales->id; 
                   }
                   
                 }
               }else {
                 if($duty->activity_id == 2) {
                   $duty->achieved = $totalOrders;
                 }
               }
               
         
             }
           }

           $opening_conversation = [];
            if( !empty($sale_team) ){
            foreach($sale_team as $row){
                $emp_open_conversation = EmployeePerformanceModel::where('user_id',$row->user_id)->where('created_at',date('Y-m-d'))->first();
                if( $emp_open_conversation ){
                $opening_conversation[$row->user_id] = $emp_open_conversation->toArray();
                }else{
                $opening_conversation[$row->user_id] = '';
                }
            }
            }
            $old_input = $request->all();

            return view(self::VIEW_DIR.'.saveDailySaleConversation',compact('sale_team','old_input','opening_conversation'));
    }

    public function commissionReport(Request $request) {
      // dd($request->all());
     $whereClause = [];
     if($request->date_from != "" && $request->date_to != ""){
        array_push($whereClause, ['date', '>=', $request->date_from]);
        array_push($whereClause, ['date', '<=', $request->date_to]);
     }
     if($request->date_from != "" && $request->date_to == "") {
        array_push($whereClause, ['date', '>=', $request->date_from]);
        array_push($whereClause, ['date', '<=', date('Y-m-d')]);
     }
      $facbkMsges = CustomerChatModel::where($whereClause)->sum('no_of_chat');
      $data = DB::table("oms_place_order AS opo")
    ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
      $join->on("baord.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("1"));
    })
    ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
      $join->on("dford.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("2"));
    })
    ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
    ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
    ->leftjoin("duty_assigned_users AS dau",function($join){
      $join->on("dau.user_id","=","sp.user_id");
      $join->on("dau.activity_id","=",DB::raw("66"));
    })
    // ->leftjoin("oms_activity_log AS oal_app_order",function($join){
    //   $join->on("oal_app_order.ref_id","=","opo.order_id");
    //   // $join->on("oal_app_order.activity_id","=",DB::raw("25"));
    // })
    ->select(DB::raw("sp.user_id,sp.firstname,sp.lastname,sp.commission_on_delivered_amount,COUNT(*) AS total_order,dau.quantity AS daily_order_target,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_orders,
      SUM(baord.total) AS BAAmountTotal,
      SUM(dford.total) AS DFAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN baord.total ELSE 0 END) AS BADeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN dford.total ELSE 0 END) AS DFDeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN baord.total ELSE 0 END) AS BAShippedAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN dford.total ELSE 0 END) AS DFShippedAmountTotal
    "))
    ->where('sp.status',1)
    // ->whereIn('oal.activity_id',[25,12])
    ->where('sp.user_group_id',11)
    ->where(function ($query) {
      $query->where('ord.oms_order_status','!=',5)
          ->orWhereNull('ord.oms_order_status');
    });
    $daysWithoutHoliday = 0;
    if($request->date_from != "" && $request->date_to != "" ){
      $data = $data->whereDate('opo.created_at','>=',$request->date_from)
              ->whereDate('opo.created_at','<=',$request->date_to);
      $daysWithoutHoliday = $this->daysWithoutHolidays($request->date_from,$request->date_to);
    }
    if($request->user != ""){
      $data = $data->where('opo.user_id','=',$request->user);
    }
    if( session('user_group_id') ==12 ){
      $data = $data->where('opo.user_id','=',session('user_id'));
    }
    $data = $data->groupBy("opo.user_id");
    $data = $data->get();
    //assigning chats
    // total_approve_exchange
    if( $data && $request->date_from != "" AND $request->date_to ){
      foreach($data as $key => $row){
        $total_chat_data = DailyAdResult::select(DB::RAW("SUM(results) AS total_chats,SUM(budget_used) AS total_budget_used"))->whereDate('date','>=',$request->date_from)->where('user_id',$row->user_id)->whereDate('date','<=',$request->date_to)->groupBy('user_id')->first();
        if($total_chat_data){
          $row->chat_details = $total_chat_data;
        }
        $row->customer_chats = $facbkMsges; 
        $order_approved_data = OmsActivityLogModel::select(DB::raw("COUNT(*) AS tot_approved"))->where("activity_id",25)->whereDate('created_at','>=',$request->date_from)->whereDate('created_at','<=',$request->date_to)->where("created_by",$row->user_id)->groupBy('activity_id')->first();
        if( $order_approved_data ){
          $row->total_approve_orders = $order_approved_data->tot_approved;
        }else{
          $row->total_approve_orders = 0;
        }
        $exchange_approved_data = OmsActivityLogModel::select(DB::raw("COUNT(*) AS tot_approved"))->where("activity_id",12)->whereDate('created_at','>=',$request->date_from)->whereDate('created_at','<=',$request->date_to)->where("created_by",$row->user_id)->groupBy('activity_id')->first();
        if( $exchange_approved_data ){
          $row->total_approve_exchange = $exchange_approved_data->tot_approved;
        }else{
          $row->total_approve_exchange = 0;
        }
        $cancel_order_data = OmsActivityLogModel::select(DB::raw("COUNT(*) AS tot_cancelled"))->where("activity_id",10)->whereDate('created_at','>=',$request->date_from)->whereDate('created_at','<=',$request->date_to)->where("created_by",$row->user_id)->groupBy('activity_id')->first();
        if( $cancel_order_data ){
          $row->total_cancel_order = $cancel_order_data->tot_cancelled;
        }else{
          $row->total_cancel_order = 0;
        }
      }
    }
    $old_input = $request->all();
    $comm_settings = CommissionSetting::where('id',1)->first();
    $staffs = OmsUserModel::select('user_id','username','firstname','lastname')->whereIn('user_group_id',[11])->where('status',1);
    if(session('user_group_id') ==12){
      $staffs = $staffs->where("user_id",session('user_id'));
    }
    $staffs = $staffs->get();
    // dd($comm_settings->toArray());
    // dd($data->toArray());
    return view(self::VIEW_DIR.".operation_sale_commission_amount",compact('data','staffs','old_input','comm_settings','daysWithoutHoliday'));
    }

    private  function daysWithoutHolidays($date1,$date2){
      $start = new DateTime($date1);
      $end = new DateTime($date2);
      // otherwise the  end date is excluded (bug?)
      $end->modify('+1 day');
  
      $interval = $end->diff($start);
  
      // total days
      $days = $interval->days;
  
      // create an iterateable period of date (P1D equates to 1 day)
      $period = new DatePeriod($start, new DateInterval('P1D'), $end);
  
      // best stored as array, so you can add more than one
      // $holidays = array('2012-09-07');
      $holidays = array();
  
      foreach($period as $dt) {
          $curr = $dt->format('D');
  
          // substract if Saturday or Sunday
          if ($curr == 'Sun') {
              $days--;
          }
  
          // (optional) for the updated question
          elseif (in_array($dt->format('Y-m-d'), $holidays)) {
              $days--;
          }
      }
  
  
      return $days;
    }
}
