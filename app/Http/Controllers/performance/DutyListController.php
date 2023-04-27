<?php

namespace App\Http\Controllers\EmployeePerformance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\DutyListsModel;
use App\Models\Oms\DutyModel;
use App\Models\Oms\OmsSubDutyListModel;

class DutyListController extends Controller
{
    const VIEW_DIR = 'employee_performance.duties';
    const PER_PAGE = 20;
    public function dutyActivities() {
        $activities = DutyListsModel::with('duty','sub_duty_lists')->get()->sortBy(function($query){
            return $query->duty->name;
            });
        $duties = DutyModel::all();
        $activities = customPaginate($activities, 12, []);
        // dd($activities);
        return view(self::VIEW_DIR. '.activities')->with(compact('activities','duties'));
    }

    public function getAllSubDuties() {
        $sub_activaties = OmsSubDutyListModel::with('duty_list')->paginate(12);
        $activities = DutyListsModel::all();

        return view(self::VIEW_DIR. '.sub_activities')->with(compact('sub_activaties','activities'));
    }

    public function saveSubDuty(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'sub_activity.*' => 'required'
        ]);
        $activity = $request->sub_activity;
        $activity_id = $request->sub_id;
        // dd($activity_id);
            if($activity && count($activity) > 0) {
                for($i = 0 ; $i < count($activity); $i++) {
               
                    $request_data = array(
                        'duty_list_id' => $request->duty,
                        'name' => $activity[$i],
                        'created_at' => date('Y-m-d')
                    );
                    
                    OmsSubDutyListModel::updateOrCreate(
                        ['id' => $activity_id[$i]],
                        $request_data
                    );
                    // $data[] = $request_data;
                }
            }
            
        return response()->json([
                'status' => true
            ]);
    }

    public function deletetExistSubDuty(OmsSubDutyListModel $list) {
       
        if($list->delete()) {
            return response()->json([
                'status'=> true
            ]);
        }else {
            return response()->json([
                'status'=> false
            ]);
        }

    }

    function deletetActivity(DutyListsModel $activity) {
        if($activity->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Activity deleted successfully'
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Opps! Something went wrong try again'
            ]);
        }
    }

}
