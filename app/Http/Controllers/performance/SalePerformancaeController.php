<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\UserStartEndTimeModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\DutyListsModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\EmployeePerformanceDetailModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Session;
use Validator;
use Excel;
use Illuminate\Http\Request;

class SalePerformancaeController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.sale';
    function __construct() {

    }

    public function index(Request $request) {
        // dd($request->all());
        $staffs = OmsUserModel::select('user_id','username')->whereIn('user_group_id',[12])->where('status',1)->get();
        $where = [];
        $flag = false;
        if( session('user_group_id') != 1 && !array_key_exists('employee-performance/sale', json_decode(session('access'),true)) ){
            $flag = true;
            $where = ['user_id'=>session('user_id')];
          }
        if( $request->user_id != "" && $request->user_id > 0 ){
            $flag = true;
            $where = ['user_id'=> $request->user_id];
          }
        $performances = [];
        $dataPerformance = EmployeePerformanceModel::with(['performanceDetails','sale_person.activities'])->where($where)->orderBy('id','DESC')->groupBy(['user_id','created_at'])->get();
        // dd($where);
        $data = EmployeePerformanceModel::with(['performanceDetails','sale_person.activities'])->where($where)->orderBy('id','DESC');
        $user_daily_workH = UserStartEndTimeModel::where($where)->get();
        
        if( $request->date_from != "" && $request->date_to != "" ){
            $data = $data->whereDate('created_at','>=',$request->date_from);
            $data = $data->whereDate('created_at','<=',$request->date_to);
          }
          if( $request->date_from != "" && $request->date_to == "" ){
            $data = $data->whereDate('created_at','>=',$request->date_from);
          }
          if( $request->date_from == "" && $request->date_to != "" ){
            $data = $data->whereDate('created_at','<=',$request->date_to);
          }
          $data = $data->get();
          $dataArray = $data->toArray();
          
          foreach($dataPerformance as $performance) {
            $performanceData = [];
            foreach($dataArray as $d) {
              if($performance->user_id == $d['user_id'] && $performance->created_at == $d['created_at']) {
                  // Live order for cuurent date start
                if($request->date_from == "" && $request->date_to == "") {
                    if($d['created_at'] == date('Y-m-d') && $d['duty_list_id'] == 2){
                        $orderData = OmsPlaceOrderModel::with('omsOrder',function($q) {
                            $q->where('oms_order_status','!=',5)
                            ->orWhereNull('ord.oms_order_status');
                        })->select('order_id')->where('user_id', $d['user_id'])
                          ->whereDate('created_at',date('Y-m-d'))
                          ->get();
                          $totalOrders = $orderData->count();
                        if($d['duty_list_id'] == 2) {
                            $d['achieved'] = $totalOrders;
                        }
                    }
                }
                // Live order for cuurent date end
               array_push($performanceData, $d);
              }
            }
            if(count($performanceData) > 0) {
                array_push($performances, $performanceData);
              }
          }
          $data = $performances;
          $old_input = $request->all();
          
          $activities = DutyListsModel::where('status', 1)->where('is_custom', 0)->where('duty_id', 2)
            ->whereHas('assignedUsersDuties', function($q) use($where) {
                $q->where($where);
            })->get();
            $parameters = $request->getQueryString();
            $parameters = preg_replace('/&page(=[^&]*)?|^page(=[^&]*)?&?/','', $parameters);
            $path = url('/') . '/performance/sale/staff/duty/report?' . $parameters;
            $data = $this->paginate($data, $path);

            return view(self::VIEW_DIR.'.saleStaffDutyReport',compact('data','old_input','staffs', 'activities', 'user_daily_workH'));
    }

    public function saveDailyProgress(Request $request,$id="") {
        // dd($request->all());
        $today_converation = EmployeePerformanceModel::with('performanceDetails')->where('user_id',session('user_id'))->where('created_at',date('Y-m-d'))->get();
        if( $request->isMethod('post') ){
            $this->updateDailyProgressQuery($request,$id);
          }
        $today_converation = EmployeePerformanceSaleModel::with('sale_products')->where('user_id',session('user_id'))->where('date',date('Y-m-d'))->first();
        $status_products = [];
        $catelog_products = [];
        if( $today_converation && $today_converation->sale_products ){
            foreach( $today_converation->sale_products as $key => $product ){
              if( $product->posting_type == 1 ){
                $status_products[] = $product->product_group_name;
              }
              if( $product->posting_type == 2 ){
                $catelog_products[] = $product->product_group_name;
              }
            }
          }
          //find total order
          $orderData = OmsPlaceOrderModel::with('omsOrder',function($q) {
                                            $q->where('oms_order_status','!=',5)
                                                ->orWhereNull('ord.oms_order_status');
                                            })->select('order_id')->where('user_id', session('user_id'))
                                            ->whereDate('created_at',date('Y-m-d'))
                                            ->get();
          $totalOrders = $orderData->count();
          $product_groups = ProductGroupModel::all();
          $user_duties = OmsUserModel::with(['activities', 'performance_sales' => function($q) {
            $q->where('created_at', date('Y-m-d'));
            $q->with(['performanceDetails' => function($detail) {
              $detail->where('created_at', date('Y-m-d'));
            }]);
          }])->find(session('user_id'));

          foreach($user_duties->activities as $duty) {
            $group_ids = [];
            if(count($user_duties->performance_sales) > 0) {
              foreach($user_duties->performance_sales as $performance_sales) {
                if($duty->activity_id == $performance_sales->duty_list_id) {
                  if(count($performance_sales->performanceDetails) > 0) {
                    foreach($performance_sales->performanceDetails as $performanceDetails) {
                      array_push($group_ids, $performanceDetails->product_group_id);
                    }
                    $duty->achieved = $group_ids; 
                  }else {
                      $duty->achieved = $performance_sales->achieved;
                  }
                  $duty->achieved_point = $performance_sales->achieved_point; 
                  $duty->performance_id = $performance_sales->id; 
                }
              }
            }else {
              if($duty->activity_id == 2) {
                $duty->achieved_point = $duty->per_quantity_point*$totalOrders;
                $duty->achieved = $totalOrders;
              }
            }
            
      
          }
          $old_input = $request->all();
          $total_saved_contacts = EmployeePerformanceSaleModel::select('total_contact')->where("cs_confirm",1)->where('user_id',session('user_id'))->orderBy("id",'DESC')->first();
          return view(self::VIEW_DIR.'.saveDailySaleProgress',compact('product_groups','old_input','id','today_converation','status_products','catelog_products','total_saved_contacts','user_duties','totalOrders'));
    }

    private function updateDailyProgressQuery($request,$id){
      $activity_ids = $request->activity_id;
      $target = $request->target;
      $complete_quantity = $request->complete_quantity;
      $id = $request->id;
      $status_published_products = $request->status_published;
      $catalog_product_add       = $request->catalog_product_add;
      $achieved_point = $request->achieved_points;
      DB::beginTransaction();
      try {
        $achieved = [];
        $activities = [];
        if(isset($complete_quantity)) {
          foreach($complete_quantity as $key => $comp_quantity) {
            if(isset($comp_quantity)) {
              if($comp_quantity == 0) {
                $activity = $activity_ids[$key];
                if($activity == 15 && isset($catalog_product_add)) {
                  // dd((string)count($catalog_product_add[$activity]));
                  array_push($achieved, (string)count($catalog_product_add[$activity]));
                  array_push($activities, $activity_ids[$key]);
                 //  $this->savePerformanceDetails($catalog_product_add[$activity],$activity, 2);
                 
                }elseif($activity == 9 && isset($status_published_products)) {
                  array_push($achieved, (string)count($status_published_products[$activity]));
                   array_push($activities, $activity_ids[$key]);
                }else {
                  array_push($achieved, $comp_quantity);
                  if($activity != 64 && $activity != 65) {
                   array_push($activities, $activity_ids[$key]);
                  }
                  
                 // continue;
                }
              }else{

                array_push($achieved, $comp_quantity);
                if($activity_ids[$key] != 64 && $activity_ids[$key] != 65) {
                 array_push($activities, $activity_ids[$key]);
                }
              }
            }
          }
        }

        for($i = 0; $i < count($activities); $i++) {
          $complete_quantity = $request->complete_quantity;
          $performance = ($id[$i]) ? EmployeePerformanceModel::find($id[$i]) : new EmployeePerformanceModel();
          $performance->user_id = session('user_id');
          $performance->duty_list_id = $activities[$i];
          $performance->achieved = $achieved[$i];
          $performance->target = $target[$i];
          $performance->achieved_point = $achieved_point[$i];
          $performance->created_at = date('Y-m-d');
          if($performance->save()) {
            
            EmployeePerformanceDetailModel::where('employee_performance_id', $performance->id)->delete();
            if($activities[$i] == 15 && isset($catalog_product_add)) {
              $this->savePerformanceDetails($catalog_product_add[$activities[$i]],$performance->id, 2);
            
            }
            if($activities[$i] == 9 && isset($status_published_products)) {
                $this->savePerformanceDetails($status_published_products[$activities[$i]],$performance->id, 1);
              
              }
          }

        }
        DB::commit();
        Session::flash('query_success', 'Progress Updated Successfully !');
      }catch(\Exception $e) {
        DB::rollback();
        Session::flash('query_error', $e->getMessage());
      }
    }

    public function savePerformanceDetails($values,$performance_id, $type) {
      // dd($performance_id); 
            EmployeePerformanceDetailModel::where('employee_performance_id', $performance_id)->where("type",$type)->delete();
            //new entries
            foreach($values as $key => $sp_product_id){
              $group_name = ProductGroupModel::select('name')->where("id",$sp_product_id)->first();
              $create_array = ["employee_performance_id"=>$performance_id,"product_group_id"=>$sp_product_id,"product_group_name"=>$group_name->name,"type"=>$type, "created_at" => date('Y-m-d')];
              EmployeePerformanceDetailModel::create($create_array);
            }
    }

    public function paginate($items, $path, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $paginator = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $paginator = $paginator->withPath($path);
    } 
}
