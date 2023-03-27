<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsActivityLogModel;
use Illuminate\Http\Request;
use DB;

class StockPerformanceController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.stock';

    public function index(Request $request) {
        $staffs = OmsUserModel::select('user_id','username')->whereIn('user_group_id', [5,6])->get();
        $whereCluase = [];
        if( $request->date_from != "" &&  $request->date_to != "" ){
            $whereCluase[] = array('created_at', '>=', $request->date_from);
            $whereCluase[] = array('created_at', '<=', $request->date_to);
          }
        // $data = OmsActivityLogModel::with(['activity','user' => function($q) {
        //     $q->whereIn('user_group_id',[5,6]);
        // }])->where($whereCluase)->orderBy('activity_id')
        // //    ->select(DB::raw("SUM(CASE WHEN created_by=10 THEN 1 ELSE 0 END) AS user_one"),DB::raw("SUM(CASE WHEN created_by=42 THEN 1 ELSE 0 END) AS user_two"))
        //    ->get();

           $data = DB::table("oms_activity_log AS log")
            ->join("oms_activities AS act","act.id","=","log.activity_id")
            ->join("oms_user AS usr","usr.user_id","=","log.created_by")
            ->select("act.title AS activity","usr.firstname AS user_name",DB::raw("SUM(CASE WHEN log.created_by=10 THEN 1 ELSE 0 END) AS user_one"),DB::raw("SUM(CASE WHEN log.created_by=42 THEN 1 ELSE 0 END) AS user_two"))
            ->whereIn('usr.user_group_id',[5,6]);
            if( $request->date_from != "" &&  $request->date_to != "" ){
              $data  = $data->whereDate('log.created_at','>=',$request->date_from)
                      ->whereDate('log.created_at','<=',$request->date_to);
            }
            $data = $data->groupBy("activity_id")
                    ->get();
        $old_input = $request->all();
        return view(self::VIEW_DIR.'.stock',compact('staffs','data','old_input'));
        
    }
}
