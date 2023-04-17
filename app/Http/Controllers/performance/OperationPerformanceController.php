<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\EmployeePerformanceSaleProductModel;
use App\Models\Oms\EmployeePerformanceDetailModel;
use App\Models\Oms\DoneDutyHistroryModel;
use DB;
use Session;
use Illuminate\Http\Request;

class OperationPerformanceController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.operation';
    
    public function __construct() {

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
}
