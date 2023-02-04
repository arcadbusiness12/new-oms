<?php
namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\DutyAssignedUserModel;
use App\Models\Oms\DutyModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\OpenCart\AdminUser\AdminUserModel;
use App\Models\OpenCart\AdminUser\AdminUserGroupModel;
use App\Models\Oms\OmsUserGroupInterface;
use App\Models\Oms\UserPaymentModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Request AS Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Session;
use Validator;
use Excel;
use Reflector;

/**
 * OrdersController to Handle Orders from opencart db and move to oms
 *
 * @author kamran
 */
class UserController extends Controller
{
    const VIEW_DIR = 'user';
    const PER_PAGE = 20;
    public function getUsers(Request $request){
        $where = [];
        if($request->status != ""){
          $where[] = ['status',$request->status];
        }else{
          $where[] = ['status',1];
        }
        if($request->user_group_id != "" && $request->user_group_id > 0){
          $where[] = ['user_group_id',$request->user_group_id];
        }
        $old_input = $request->all();
        if( count($old_input) == 0 ){
          $old_input = ['status'=>1];
        }
        $staffs = OmsUserModel::with('userGroupName')->where('role', OmsUserGroupInterface::OMS_USER_GROUP_STAFF)->where($where)->get();
        $userGroups = OmsUserGroupModel::select('id','name')->get();
        return view(self::VIEW_DIR.".user_listing", compact('staffs','userGroups','old_input'));
    }
    public function addUser(Request $request){
        if( $request->isMethod('post') ){
                $validatedData = $request->validate([
                    'username' => 'required|unique:oms_user',
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'password' => 'required|min:5|confirmed',
                    'email' => 'required|email|unique:oms_user',
                    'user_group_id'=>'required'
                ]);


                // $salt = OmsUserModel::token(9);
                // $password = sha1($salt . sha1($salt . sha1($request->password)));
                $password =  Hash::make($request->password);

                $OmsUserModel = new OmsUserModel();
                $OmsUserModel->username      = $request->username;
                $OmsUserModel->user_group_id = $request->user_group_id;
                $OmsUserModel->password      = $password;
                $OmsUserModel->firstname     = $request->firstname;
                $OmsUserModel->lastname      = $request->lastname;
                $OmsUserModel->email         = $request->email;
                $OmsUserModel->status        = $request->status;
                $OmsUserModel->salary        = $request->salary;
                $OmsUserModel->basic_salary  =  $request->basic_salary;
                $OmsUserModel->commission_on_delivered_amount = $request->commission_on_delivered_amount;
                // $OmsUserModel->{OmsUserModel::FIELD_ROLE} = OmsUserGroupInterface::OMS_USER_GROUP_STAFF;
                $OmsUserModel->commission    = 0;
                $OmsUserModel->commission_on = '';
                $OmsUserModel->save();

                return redirect('omsSetting/users/add');
        }
        //inserton end
        $userGroups = OmsUserGroupModel::select('id','name')->get();
        return view(self::VIEW_DIR.".user_form",compact('userGroups'));
    }
    public function editUser(Request $request,$id){
        $user_info = OmsUserModel::where("user_id",$id)->first();
        if( $request->isMethod('post') ){
                $validatedData = $request->validate([
                    // 'username' => 'required|unique:oms_user',
                    'firstname' => 'required',
                    'lastname' => 'required',
                    'password' => 'required|min:5|confirmed',
                    // 'email' => 'required|email|unique:oms_user',
                    'user_group_id'=>'required'
                ]);


                // $salt = OmsUserModel::token(9);
                // $password = sha1($salt . sha1($salt . sha1($request->password)));
                $password =  Hash::make($request->password);

                $user_info->username      = $request->username;
                $user_info->user_group_id = $request->user_group_id;
                if( $request->password != "" ){
                    $user_info->password      = $password;
                }
                $user_info->firstname     = $request->firstname;
                $user_info->lastname      = $request->lastname;
                $user_info->email         = $request->email;
                $user_info->status        = $request->status;
                $user_info->salary        = $request->salary;
                $user_info->basic_salary  =  $request->basic_salary;
                $user_info->commission_on_delivered_amount = $request->commission_on_delivered_amount;
                // $OmsUserModel->{OmsUserModel::FIELD_ROLE} = OmsUserGroupInterface::OMS_USER_GROUP_STAFF;
                $user_info->commission    = 0;
                $user_info->commission_on = '';
                $user_info->save();

                return redirect('omsSetting/users');
        }
        //inserton end
        $userGroups = OmsUserGroupModel::select('id','name')->get();
        return view(self::VIEW_DIR.".edit_user_form",compact('userGroups','user_info'));
    }
}
