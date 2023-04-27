<?php

namespace App\Http\Controllers\EmployeePerformance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\DoneDutyHistroryModel;
use App\Models\Oms\DutyListsModel;
use App\Models\Oms\DutyModel;
use App\Models\Oms\UserStartEndTimeModel;
use Illuminate\Support\Facades\Input;
use App\Models\Oms\OmsUserModel as OmsOmsUserModel;

class DutyController extends Controller
{
    const VIEW_DIR = 'employee_performance.duties';
    const PER_PAGE = 20;
    public function getAllDuties() {
        $duties = DutyModel::with('dutyLists')->get();
        return view(self::VIEW_DIR. '.duties')->with(compact('duties'));
    }

    public function saveDuty(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'duty_name' => 'required'
        ]);
        $duty = ($request->id) ? DutyModel::find($request->id) : new DutyModel();
        $duty->name = $request->duty_name;
        $activity = $request->activity;
        $is_custom = $request->is_custom;
        $is_auto = $request->is_auto;
        $activity_id = $request->activity_id;
        $points = $request->points;
        // dd($activity_id);
        if($duty->save()) {
            if($activity && count($activity) > 0) {
                for($i = 0 ; $i < count($activity); $i++) {
               
                    $request_data = array(
                        'duty_id' => $request->id,
                        'name' => $activity[$i],
                        'is_custom' => $is_custom[$i],
                        'is_auto' => $is_auto[$i],
                        'points' => $points[$i]
                    );
                    
                    $settings = DutyListsModel::updateOrCreate(
                        ['id' => $activity_id[$i]],
                        $request_data
                    );
                    // $data[] = $request_data;
                }
            }
            
            
        }
        return response()->json([
                'status' => true
            ]);
    }

    
    public function getExistActivities($id) {
        $activities = DutyListsModel::where('duty_id', $id)->get();
        return response()->json([
            'status' => true,
            'activities' => $activities
        ]);
    }

    public function deletetExistActivity(DutyListsModel $list) {
       
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

    public function employeeWorkReports() {
        $filterWhereClause = [];
        $date_flag = false;
        $flag = false;
      $user = null;
      $old_input = Input::all();
      if(Input::get('user_id')) {
        $flag = true;
         $filterWhereClause[] = array('user_id', Input::get('user_id'));
      }
      
      if(Input::get('date_from') && !Input::get('date_to')) {
        $flag = true;
         $date_flag = true;
         $filterWhereClause[] = array('date', '>=', Input::get('date_from'));
      }
      if(Input::get('date_to') && !Input::get('date_from')) {
        $flag = true;
         $date_flag = true;
         $filterWhereClause[] = array('date', '<=', Input::get('date_to'));
      }
      if(Input::get('date_from') && Input::get('date_to')) {
        $flag = true;
         $date_flag = true;
         $startd = Input::get('date_from');
         array_push($filterWhereClause, ['date', '>=', Input::get('date_from')]);
         array_push($filterWhereClause, ['date', '<=', Input::get('date_to')]);
      }
      $whereclause = [];
      $done_histories = [];
      if(session('role') != 'ADMIN') {
           $startd = date('Y-m-01');
           $endd = date('Y-m-d');
           $old_input['date_from'] = $startd;
           $old_input['date_to'] = $endd;
           array_push($whereclause, ['date', '>=', $startd]);
           array_push($whereclause, ['date', '<=', $endd]);
           array_push($whereclause, ['user_id', session('user_id')]);
           $flag = true;
           $user = OmsOmsUserModel::find(session('user_id'));
        }else {
            if($flag) {
                if(Input::get('user_id')) {
                   $user = OmsOmsUserModel::find(Input::get('user_id'));
                }else {
                 $user = OmsOmsUserModel::where('status',1)->get();
                 $user = $user->toArray();
                }
             }else {
              //   dd($date_flag);
                if(!$date_flag) {
                 $startd = date('Y-m-01');
                 $endd = date('Y-m-d');
                 $old_input['date_from'] = $startd;
                 $old_input['date_to'] = $endd;
                 array_push($whereclause, ['date', '>=', $startd]);
                 array_push($whereclause, ['date', '<=', $endd]);
                }
              
              $user = OmsOmsUserModel::where('status',1)->get();
              $user = $user->toArray();
        }
    }

        $histories = $this->empoloyeeWorks($user, $whereclause, $filterWhereClause);
        // dd($histories);
        $users = OmsOmsUserModel::select('user_id', 'username')->where('user_group_id', '!=', 1)->where('status',1)->get();
        return view('employee_performance.custom_duty.daily_work_report')->with(compact('histories', 'users', 'old_input'));
        
  }

  public function empoloyeeWorks($user, $whereclause, $filterWhereClause) {
    $new_array = [];
    if(is_array($user) && count($user) > 0) {
       foreach($user as $us) {
          $work_histories = [];
          $done_histories = UserStartEndTimeModel::where('user_id', $us['user_id'])->where($whereclause)->where($filterWhereClause)->orderBy('date', 'DESC')->get();
          array_push($work_histories, $done_histories);
          $us['done_duties'] = $work_histories;
          array_push($new_array, $us);
       }
    }else {
       $work_histories = [];
          $done_histories = UserStartEndTimeModel::where($whereclause)->where($filterWhereClause)->orderBy('date', 'DESC')->get();
          array_push($work_histories, $done_histories);
       $user['done_duties'] = $work_histories;
       array_push($new_array, $user);
    }
    return $new_array;
 }
}
