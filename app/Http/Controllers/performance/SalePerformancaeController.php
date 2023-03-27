<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use App\Models\Oms\EmployeePerformanceSaleModel;
use App\Models\Oms\UserStartEndTimeModel;
use App\Models\Oms\OmsPlaceOrderModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\EmployeePerformanceModel;
use App\Models\Oms\DutyListsModel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
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
                          $totalOrders = $order_data->count();
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

    public function paginate($items, $path, $perPage = 20, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        $paginator = new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
        return $paginator = $paginator->withPath($path);
    } 
}
