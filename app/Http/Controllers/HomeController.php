<?php

namespace App\Http\Controllers;

use App\Models\Oms\DoneDutyHistroryModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\OpenCart\Customers\CustomersModel;
use App\Models\OpenCart\Orders\OrdersModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // dd(session());
        $ba_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $df_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 2)->where('posting_type', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::whereIn('store_id', [1,2])->where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
       
        Session::put('ba_main_setting_list', json_encode($ba_promotion_main_setting));
        Session::put('df_main_setting_list', json_encode($df_promotion_main_setting));
        Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
        if(session('role') == 'ADMIN') {
            $delived_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 25)
                            // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                            ->whereYear('date_added', Carbon::now()->year)
                            ->whereMonth('date_added', Carbon::now()->month)
                            ->count();
            $shipped_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 3)
                            // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                            ->whereYear('date_added', Carbon::now()->year)
                            ->whereMonth('date_added', Carbon::now()->month)
                            ->count();
            $pendding_orders = OrdersModel::where(OrdersModel::FIELD_ORDER_STATUS_ID, 1)
                            // ->whereDate('date_added', '>=', date('2022-07-'))->whereDate('date_added', '<=', date('Y-m-d'))
                            ->whereYear('date_added', Carbon::now()->year)
                            ->whereMonth('date_added', Carbon::now()->month)
                            ->count();

            return view('home')->with(compact('delived_orders','shipped_orders','pendding_orders'));

        }else {
            $staffOperationRecords = $this->employeeOperationRecords(session('user_id'), 'today');
            // dd($staffOperationRecords);
            return view('staff_home')->with(compact('staffOperationRecords'));
        }
        
    }

    public function employeeOperationRecords($user_id, $filter) {
        $performance_sales = OmsUserModel::with(['activities' => function($q) {
          $q->where('duration', '!=', 0);
      }])->find($user_id);
        $xValues = [];
        $yValues = [];
        $dateWhereClause = $this->checkFilterUser($filter, $performance_sales);
            $order_data = DB::table("oms_place_order AS opo")
                ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
                ->select("opo.order_id")
                ->where('opo.user_id',session('user_id'))
                // ->where('ord.oms_order_status','!=',5)
                ->where(function ($query) {
                  $query->where('ord.oms_order_status','!=',5) 
                      ->orWhereNull('ord.oms_order_status');
                })
                ->whereDate('opo.created_at',date('Y-m-d'))
                ->get();

            $totalOrders = $order_data->count();
        if(count($performance_sales->activities) > 0) {
          foreach($performance_sales->activities as $activity) {
            // $performance_sales_achieved = EmployeePerformanceModel::where('duty_list_id', $activity->activity_id)->where('user_id', $user_id)->where('created_at', date('Y-m-d'))->sum('achieved');
            if($performance_sales->userGroupName['name'] == 'Sales Team') {
            $performance_sales_achieved = EmployeePerformanceModel::where('duty_list_id', $activity->activity_id)->where('user_id', $user_id)->where($dateWhereClause)->sum('achieved');
            }
            if($performance_sales->userGroupName['name'] == 'BA Designer' || $performance_sales->userGroupName['name'] == 'DF Designer' || $performance_sales->userGroupName['name'] == 'Designers' || $performance_sales->userGroupName['name'] == 'Designer') {
              $performance_sales_achieved = DoneDutyHistroryModel::where('duty_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
              ->count();
          }
            array_push($xValues, "'".$activity->name."'");
            array_push($yValues, ($activity->activity_id == 2) ? $totalOrders + (int)$performance_sales_achieved : (int)$performance_sales_achieved);

         }
        }

        // For duties

        $duties = [];
            $user = OmsUserModel::with(['activities' => function($q) {
                $q->where('duration', '!=', 0);
            }])->where('user_id', session('user_id'))->first();
            foreach($user->activities as $activity) {
                $target = 0;
                $achieved = 0;
                if($user->userGroupName['name'] == 'Sales Team') {
                    $performance_sales = EmployeePerformanceModel::select('achieved','target')->where('duty_list_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                    ->get();
                    foreach($performance_sales as $ac) {
                        $achieved += $ac['achieved'];
                        $target += $ac['target'];
                    }
                    $activity->quantity = (int)$target;
                }
                if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || 
                $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer' || 
                $user->userGroupName['name'] == 'designers' || $user->userGroupName['name'] == 'designer') {
                   
                    $performance_sales = DoneDutyHistroryModel::where('duty_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                    ->count();
                    $achieved = $performance_sales;
                    if($activity->daily_compulsory == 1) {
                        $total_q = DoneDutyHistroryModel::where('duty_id', $activity->activity_id)->where('user_id', session('user_id'))->where($dateWhereClause)
                                                        ->groupBy('done_date')->get();
                        foreach($total_q as $ac) {
                            $target += $activity->quantity;
                       }
                    }else {
                        $target += $activity->monthly_tasks;
                    }
                    $activity->quantity = (int)$target;
                }

                $activity->achieved = ($activity->activity_id == 2) ? $totalOrders + (int)$achieved : (int)$achieved;
                
                
            }
            $duties = $user->activities->toArray();
        
    //   dd($xValues);
      return [
        'duties' => $duties,
        'xValue' => $xValues,
        'yValue' => $yValues,
        'max' => (count($yValues) > 0) ? max($yValues) : 12
      ];
        // dd($yValues);
      }
      
      function checkFilterUser($filter, $user) {
        $dateWhereClause = [];
        if($filter == 'week') {
            if($user->userGroupName['name'] == 'Sales Team') {
                array_push($dateWhereClause, ['created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d')]);
                array_push($dateWhereClause, ['created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d')]);
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer') {
                // dd(Carbon::now()->endOfWeek()->format('Y-m-d'));
                array_push($dateWhereClause, ['done_date', '>=', Carbon::now()->startOfWeek()->format('Y-m-d')]);
                array_push($dateWhereClause, ['done_date', '<=', Carbon::now()->endOfWeek()->format('Y-m-d')]);
            }
            
        }else if($filter == 'month') {
            if($user->userGroupName['name'] == 'Sales Team') {
                array_push($dateWhereClause, ['created_at', '>=', Carbon::now()->year.'-'.Carbon::now()->month.'-01']);
                array_push($dateWhereClause, ['created_at', '<=', Carbon::now()->endOfMonth()->format('Y-m-d')]);
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer') {
                array_push($dateWhereClause, ['done_date', '>=', Carbon::now()->year.'-'.Carbon::now()->month.'-01']);
                array_push($dateWhereClause, ['done_date', '<=', Carbon::now()->endOfMonth()->format('Y-m-d')]);
            }
            
        }else {
            if($user->userGroupName['name'] == 'Sales Team') {
                $dateWhereClause[] = array('created_at', date('Y-m-d'));
            }
            if($user->userGroupName['name'] == 'BA Designer' || $user->userGroupName['name'] == 'DF Designer' || $user->userGroupName['name'] == 'Designers' || $user->userGroupName['name'] == 'Designer') {
                $dateWhereClause[] = array('done_date', date('Y-m-d'));
            }
        }
        return $dateWhereClause;
    }

    public function Testing() {
        dd("Yessss Ok");
    }
}
