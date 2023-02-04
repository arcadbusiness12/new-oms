<?php
namespace App\Http\Controllers\Supplier;

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
use Illuminate\Support\Facades\Input;
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
class SupplierController extends Controller
{
    const VIEW_DIR = 'supplier';
    const PER_PAGE = 20;

    public function get_supplier(){
        $suppliers = OmsUserModel::select('*')->where('role', OmsUserGroupInterface::OMS_USER_GROUP_SUPPLIER)->get()->toArray();
        return view("supplier.supplier_list", ["suppliers" => $suppliers]);
    }
    public function add_supplier($user_id = ''){
        if($user_id){
            $OmsUserModel = OmsUserModel::select('*')->where('user_id', $user_id)->first();
            $userDetail = array();
            if($OmsUserModel){
                $OmsUserGroupModel = OmsUserGroupModel::select('access')->where('id', $OmsUserModel->user_group_id)->first();
                $userDetail = array(
                    'user_id'       =>  $OmsUserModel->user_id,
                    'user_group_id' =>  $OmsUserModel->user_group_id,
                    'username'      =>  $OmsUserModel->username,
                    'firstname'     =>  $OmsUserModel->firstname,
                    'lastname'      =>  $OmsUserModel->lastname,
                    'email'         =>  $OmsUserModel->email,
                    'password'      =>  $OmsUserModel->password,
                    'status'        =>  $OmsUserModel->status,
                    'access'        =>  $OmsUserGroupModel ? $OmsUserGroupModel->access : '',
                    'commission'    =>  $OmsUserModel->commission,
                    'commission_on' =>  $OmsUserModel->commission_on,
                );
            }
        }else{
            $userDetail['user_id'] = $userDetail['username'] = $userDetail['firstname'] = $userDetail['lastname'] = $userDetail['email'] = $userDetail['password'] = $userDetail['status'] = $userDetail['commission'] = $userDetail['commission_on'] = '';
            $userDetail['access'] = array();
        }

        $supplierGroups = OmsUserGroupModel::select('id','name')->get()->toArray();
        return view("supplier.supplier_form", ["userDetail" => $userDetail, "supplierGroups" => $supplierGroups]);
    }
    public function save_supplier(){
        if(Input::all() > 0){
            if(Input::get('user_id')){
                $user = OmsUserModel::select('username','email')->where('user_id', Input::get('user_id'))->first();
                $check_username = OmsUserModel::select('*')->where('username', 'LIKE', Input::get('username'))->exists();
                $check_email = OmsUserModel::select('*')->where('email', 'LIKE', Input::get('email'))->exists();

                if(($user->username != Input::get('username')) && $check_username){
                    Session::flash('message', 'Username already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('supplier/edit/'.Input::get('user_id'));
                }
                if(($user->email != Input::get('email')) && $check_email){
                    Session::flash('message', 'Email Address already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('supplier/edit/'.Input::get('user_id'));
                }

                $update = array(
                    'username'  =>  Input::get('username'),
                    'firstname'  =>  Input::get('firstname'),
                    'lastname'  =>  Input::get('lastname'),
                    'email'  =>  Input::get('email'),
                    'status'  =>  Input::get('status'),
                    'commission'  =>  Input::get('commission'),
                    'commission_on'  =>  Input::get('commission_on'),
                );
                if(Input::get('password') && Input::get('c_password') && Input::get('password') == Input::get('c_password')){
                    $salt = OmsUserModel::token(9);
                    $password = sha1($salt . sha1($salt . sha1(Input::get('password'))));
                    $update['salt'] = $salt;
                    $update['password'] = $password;
                }else if(Input::get('password') != Input::get('c_password')){
                    Session::flash('message', 'Password does not match!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('supplier/edit/'.Input::get('user_id'));
                }
                OmsUserModel::where('user_id', Input::get('user_id'))->update($update);
            }else{
                $check_username = OmsUserModel::select('*')->where('username', 'LIKE', Input::get('username'))->exists();
                $check_email = OmsUserModel::select('*')->where('email', 'LIKE', Input::get('email'))->exists();

                if($check_username){
                    Session::flash('message', 'Username already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('supplier/add');
                }
                if($check_email){
                    Session::flash('message', 'Email Address already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('supplier/add');
                }

                $salt = OmsUserModel::token(9);
                $password = sha1($salt . sha1($salt . sha1(Input::get('password'))));

                $OmsUserModel = new OmsUserModel();
                $OmsUserModel->{OmsUserModel::FIELD_USER_NAME} = Input::get('username');
                $OmsUserModel->{OmsUserModel::FIELD_USER_GROUP_ID} = OmsUserGroupInterface::OMS_USER_GROUP_SUPPLIER;
                $OmsUserModel->{OmsUserModel::FIELD_SALT} = $salt;
                $OmsUserModel->{OmsUserModel::FIELD_PASSWORD} = $password;
                $OmsUserModel->{OmsUserModel::FIELD_FIRSTNAME} = Input::get('firstname');
                $OmsUserModel->{OmsUserModel::FIELD_LASTNAME} = Input::get('lastname');
                $OmsUserModel->{OmsUserModel::FIELD_EMAIL} = Input::get('email');
                $OmsUserModel->{OmsUserModel::FIELD_COMMISSION} = Input::get('commission');
                $OmsUserModel->{OmsUserModel::FIELD_COMMISSION_ON} = Input::get('commission_on');
                $OmsUserModel->{OmsUserModel::FIELD_STATUS} = Input::get('status');
                $OmsUserModel->save();
            }
        }
        return redirect('supplier');
    }
    public function delete_supplier($user_id = ''){
        if($user_id){
            OmsUserModel::where('user_id',  $user_id)->delete();
        }
        return redirect('supplier');
    }
    public function login_supplier(){
        if(count(Input::all()) > 0 && Input::get('login-user')){
            $details = OmsUserModel::where('user_id', Input::get('login-user'))->get()->first();
            if(!$details){
                return redirect('/login')->withInput()->withErrors('User details not found');
            }else{
                $userName = trim(filter_var($details->username, FILTER_SANITIZE_STRING));
                $user = OmsUserModel::where(OmsUserModel::FIELD_USER_NAME, $userName)->first();

                if (isset($user->{OmsUserModel::FIELD_USER_ID}) && $user->{OmsUserModel::FIELD_STATUS}){
                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_SUPPLIER){
                        $OmsUserModel = OmsUserModel::where('user_id', $user->{OmsUserModel::FIELD_USER_ID})->first();
                        $permissions = OmsUserGroupModel::select('access')->where('id', $user->{OmsUserModel::FIELD_ROLE})->first();
                    }

                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_SUPPLIER){
                        $session['role'] = "SUPPLIER";
                    }else{
                        $session['role'] = "ADMIN";
                    }

                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_SUPPLIER){
                        if($permissions){
                            $per = $permissions->access;
                        }else{
                            $per = '[]';
                        }
                        $session['access'] = json_encode(array_flip(json_decode($per)));
                    }else{
                        $session['access'] = json_encode(array_flip(array()));
                    }
                    \Session::put(array_merge($user->toArray(), $session));
                    return redirect('/home');
                }else{
                    return redirect('/login')->withInput()->withErrors('You haven\'t permission to access');
                }
            }
        }
        return redirect('supplier');
    }

    public function get_supplier_group(){
        $groups = OmsUserGroupModel::get()->toArray();
        return view("supplier.supplier_group_list", ["groups" => $groups]);
    }
    public function add_supplier_group($user_group_id = ''){
        $access = array();
        // dd($user_group_id);
        if($user_group_id){
            $groupDetail = OmsUserGroupModel::where('id', $user_group_id)->first();
            $groupDetail = $groupDetail->toArray();
            $access = json_decode($groupDetail['access']);
        }else{
            $groupDetail['id'] = $groupDetail['name'] = '';
            $groupDetail['access'] = array();
        }

        $oms_permissions = array();
        $oms_permissions['Place_Order'] = OmsUserGroupModel::place_order_routes();
        $oms_permissions['Business_Arcade_Normal_Order'] = OmsUserGroupModel::normal_order_routes();
        $oms_permissions['Business_Arcade_Exchange_Order'] = OmsUserGroupModel::exchange_order_routes();
        $oms_permissions['Business_Arcade_Return_Order'] = OmsUserGroupModel::return_order_routes();
        $oms_permissions['Dress_Fair_Place_Order'] = OmsUserGroupModel::df_place_order_routes();
        $oms_permissions['Dress_Fair_Normal_Order'] = OmsUserGroupModel::df_normal_order_routes();
        $oms_permissions['Dress_Fair_Exchange_Order'] = OmsUserGroupModel::df_exchange_order_routes();
        $oms_permissions['Dress_Fair_Return_Order'] = OmsUserGroupModel::df_return_order_routes();
        $oms_permissions['Purchase_Management'] = OmsUserGroupModel::purchase_management_routes();
        $oms_permissions['Inventory_Management'] = OmsUserGroupModel::inventory_management_routes();
        $oms_permissions['promotion'] = OmsUserGroupModel::promotion_management_routes();
        $oms_permissions['promotion_options'] = OmsUserGroupModel::oms_group_page_options_routes();
        $oms_permissions['promotion_organic_options'] = OmsUserGroupModel::oms_organic_post_options_routes();
        $oms_permissions['promotion_paid_ads_options'] = OmsUserGroupModel::oms_paid_ads_options_routes();
        $oms_permissions['Employee_Performance'] = OmsUserGroupModel::employee_performance_management_routes();
        $oms_permissions['Sales']       = OmsUserGroupModel::employeePerformanceSaleRoutes();
        $oms_permissions['Operation']   = OmsUserGroupModel::employeePerformanceOperationRoutes();
        $oms_permissions['Designer']   = OmsUserGroupModel::employeePerformanceDesignerRoutes();
        $oms_permissions['IT_Team']   = OmsUserGroupModel::employeePerformanceItTeamRoutes();
        $oms_permissions['Marketing']   = OmsUserGroupModel::employeePerformanceMarketingRoutes();
        $oms_permissions['Photography']   = OmsUserGroupModel::employeePerformancePhotographyRoutes();
        $oms_permissions['Model']       = OmsUserGroupModel::employeePerformanceModelRoutes();
        $oms_permissions['duties_setting']   = OmsUserGroupModel::employeePerformanceDuties_setting();
        $oms_permissions['Vouchers']   = OmsUserGroupModel::oms_vouchers_routes();
        $oms_permissions['Requests']   = OmsUserGroupModel::oms_requests_routes();
        $dashboard_options = OmsUserGroupModel::inventory_management_dashboard_option_routes();
        $oms_permissions['Resellers'] = OmsUserGroupModel::oms_manage_reseller_routes();
        $oms_permissions['Resellers_User'] = OmsUserGroupModel::ResellerUsersRouts();
        $oms_permissions['Resellers_Product_List'] = OmsUserGroupModel::ResellerProductListRoutes();
        $oms_permissions['Resellers_Product'] = OmsUserGroupModel::ResellerAssignedProductRoutes();
        $oms_permissions['Resellers_Withdraw_Request'] = OmsUserGroupModel::ResellerWithdrawRequestRoutes();
        $oms_permissions['Resellers_Customer_E_Wallet'] = OmsUserGroupModel::ResellerCustomerEWalletRoutes();
        $oms_permissions['Deals_Routes'] = OmsUserGroupModel::DealsRoutes();
        // $oms_permissions['Oms_settings'] = OmsUserGroupModel::oms_setting_routes();
        // dd($oms_permissions['Operation']);
        $oms_dutties = DutyModel::where('status', 1)->get();
        // dd($oms_dutties);
        return view("supplier.supplier_group_form", ["groupDetail" => $groupDetail, "oms_permissions" => $oms_permissions, "access" => $access, 'dashboard_options' => $dashboard_options, 'oms_dutties' => $oms_dutties]);
    }
    public function save_supplier_group(){
        // dd(Input::all());
        if(Input::all() > 0){
            if(Input::get('id')){
                if(Input::get('access')){
                    $access = json_encode(Input::get('access'));
                }else{
                    $access = json_encode(array());
                }
                $update = array(
                    'name'  =>  Input::get('name'),
                    'access'  =>  $access,
                    'duty_id'  =>  Input::get('duty')
                );
                OmsUserGroupModel::where('id', Input::get('id'))->update($update);

                Session::flash('message', 'User Group updated successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('supplier_groups');
            }else{
                if(Input::get('access')){
                    $access = json_encode(Input::get('access'));
                }else{
                    $access = json_encode(array());
                }
                $OmsUserGroupModel = new OmsUserGroupModel();
                $OmsUserGroupModel->{OmsUserGroupModel::FIELD_NAME} = Input::get('name');
                $OmsUserGroupModel->{OmsUserGroupModel::FIELD_ACCESS} = $access;
                $OmsUserGroupModel->duty_id = Input::get('duty');
                $OmsUserGroupModel->save();

                Session::flash('message', 'User Group created successfully.');
                Session::flash('alert-class', 'alert-success');
                return redirect('supplier_groups');
            }
        }
        return redirect('supplier_groups');
    }
    public function delete_supplier_group($user_group_id = ''){
        if($user_group_id){
            OmsUserGroupModel::where('id',  $user_group_id)->delete();
        }

        Session::flash('message', 'User Group deleted successfully.');
        Session::flash('alert-class', 'alert-success');
        return redirect('supplier_groups');
    }

    public function get_staff(Request $request){
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
        $staffs = OmsUserModel::with('userGroupName')->where('role', OmsUserGroupInterface::OMS_USER_GROUP_STAFF)->where($where)->get()->toArray();
        $userGroups = OmsUserGroupModel::select('id','name')->get();
        return view("supplier.staff_list", ["staffs" => $staffs,"userGroups"=>$userGroups,'old_input'=>$old_input]);
    }
    public function add_staff($user_id = ''){
        if($user_id){
            $OmsUserModel = OmsUserModel::with('activities','payments')->select('*')->where('user_id', $user_id)->first();
            $userDetail = array();
            if($OmsUserModel){
                // $OmsUserGroupModel = OmsUserGroupModel::select('access')->where('id', $OmsUserModel->user_group_id)->first();
                $OmsUserGroupModel = OmsUserGroupModel::with('duty.dutyLists')->where('id', $OmsUserModel->user_group_id)->first();

                $total_points = 0;
                // foreach($OmsUserGroupModel->duty->dutyLists as $duty) {
                //     if(count($duty->assignedUsersDuties) > 0) {
                //         if($duty->assignedUsersDuties[0]->duration > 0) {
                //             $total_points = $total_points + $duty->assignedUsersDuties[0]->point;

                //         }

                //         }

                // }
                $userDetail = array(
                    'user_id'       =>  $OmsUserModel->user_id,
                    'user_group_id' =>  $OmsUserModel->user_group_id,
                    'user_group_name' =>  $OmsUserGroupModel->name,
                    'username'      =>  $OmsUserModel->username,
                    'firstname'     =>  $OmsUserModel->firstname,
                    'lastname'      =>  $OmsUserModel->lastname,
                    'email'         =>  $OmsUserModel->email,
                    'password'      =>  $OmsUserModel->password,
                    'status'        =>  $OmsUserModel->status,
                    'basic_salary'        =>  $OmsUserModel->basic_salary,
                    'salary'        =>  $OmsUserModel->salary,
                    'commission_on_delivered_amount'        =>  $OmsUserModel->commission_on_delivered_amount,
                    'currency'      =>  $OmsUserModel->currency,
                    'duty_time_from'=>  $OmsUserModel->duty_time_from,
                    'duty_time_to'  =>  $OmsUserModel->duty_time_to,
                    'break_time'  =>  $OmsUserModel->break_time,
                    'office_location'  =>  $OmsUserModel->office_location,
                    'access'        =>  $OmsUserGroupModel ? $OmsUserGroupModel->access : '',
                    'duties'        =>  $OmsUserGroupModel->duty,
                    'activities'    =>  $OmsUserModel->activities->toArray(),
                    'regular_points'    =>  $total_points,
                    'payments'    =>  $OmsUserModel->payments,
                );
                // dd($userDetail['currency']);
                ///permission queries start========================================================================================
                $OmsUserModel = OmsUserModel::select('*')->where('user_id', $user_id)->first();
                $access = json_decode($OmsUserModel->user_access);
                //  echo "user per<pre>"; print_r($access);
                $group = OmsUserGroupModel::where('id',$OmsUserModel->user_group_id)->first();
                $group_access = json_decode($group->access);
                $oms_permissions = array();
                $oms_permissions['Place_Order'] = OmsUserGroupModel::place_order_routes();
                $oms_permissions['Business_Arcade_Normal_Order'] = OmsUserGroupModel::normal_order_routes();
                $oms_permissions['Business_Arcade_Exchange_Order'] = OmsUserGroupModel::exchange_order_routes();
                $oms_permissions['Business_Arcade_Return_Order'] = OmsUserGroupModel::return_order_routes();
                $oms_permissions['Dress_Fair_Place_Order'] = OmsUserGroupModel::df_place_order_routes();
                $oms_permissions['Dress_Fair_Normal_Order'] = OmsUserGroupModel::df_normal_order_routes();
                $oms_permissions['Dress_Fair_Exchange_Order'] = OmsUserGroupModel::df_exchange_order_routes();
                $oms_permissions['Dress_Fair_Return_Order'] = OmsUserGroupModel::df_return_order_routes();
                $oms_permissions['Purchase_Management'] = OmsUserGroupModel::purchase_management_routes();
                $oms_permissions['Inventory_Management'] = OmsUserGroupModel::inventory_management_routes();
                $oms_permissions['promotion'] = OmsUserGroupModel::promotion_management_routes();
                $oms_permissions['promotion_options'] = OmsUserGroupModel::oms_group_page_options_routes();
                $oms_permissions['promotion_organic_options'] = OmsUserGroupModel::oms_organic_post_options_routes();
                $oms_permissions['promotion_paid_ads_options'] = OmsUserGroupModel::oms_paid_ads_options_routes();
                $oms_permissions['Employee_Performance'] = OmsUserGroupModel::employee_performance_management_routes();
                $oms_permissions['Sales']       = OmsUserGroupModel::employeePerformanceSaleRoutes();
                $oms_permissions['Operation']   = OmsUserGroupModel::employeePerformanceOperationRoutes();
                $oms_permissions['Designer']   = OmsUserGroupModel::employeePerformanceDesignerRoutes();
                $oms_permissions['IT_Team']   = OmsUserGroupModel::employeePerformanceItTeamRoutes();
                $oms_permissions['Marketing']   = OmsUserGroupModel::employeePerformanceMarketingRoutes();
                $oms_permissions['Photography']   = OmsUserGroupModel::employeePerformancePhotographyRoutes();
                $oms_permissions['Model']   = OmsUserGroupModel::employeePerformanceModelRoutes();
                $oms_permissions['duties_setting']   = OmsUserGroupModel::employeePerformanceDuties_setting();
                $oms_permissions['Vouchers']   = OmsUserGroupModel::oms_vouchers_routes();
                $oms_permissions['Requests']   = OmsUserGroupModel::oms_requests_routes();
                $dashboard_options = OmsUserGroupModel::inventory_management_dashboard_option_routes();
                $oms_permissions['Resellers'] = OmsUserGroupModel::oms_manage_reseller_routes();
                $oms_permissions['Resellers_User'] = OmsUserGroupModel::ResellerUsersRouts();
                $oms_permissions['Resellers_Product_List'] = OmsUserGroupModel::ResellerProductListRoutes();
                $oms_permissions['Resellers_Product'] = OmsUserGroupModel::ResellerAssignedProductRoutes();
                $oms_permissions['Withdraw_Request'] = OmsUserGroupModel::ResellerWithdrawRequestRoutes();
                $oms_permissions['Customer_E_Wallet'] = OmsUserGroupModel::ResellerCustomerEWalletRoutes();
                $oms_permissions['Deals_Routes'] = OmsUserGroupModel::DealsRoutes();
                // for duties
                $oms_dutties = DutyModel::with('dutyLists')->get();

                //permission queries end=============================================================================================
            }
            // dd($userDetail['duties']);
            if($userDetail['duties']) {
                foreach($userDetail['duties']['dutyLists'] as $activity) {
                    foreach($OmsUserModel->activities as $selected_activity) {
                        if($activity->id == $selected_activity->activity_id) {
                            // dd($selected_activity);
                            $activity->point = $activity->points;
                            $activity->quantity = $selected_activity->quantity;
                            $activity->duration = $selected_activity->duration;
                            $activity->per_quantity_point = $selected_activity->per_quantity_point;
                            $activity->monthly_tasks = $selected_activity->monthly_tasks;
                            $activity->daily_compulsory  =  $selected_activity->daily_compulsory;
                            $activity->disabled = 1;
                        }
                        $activity->point = $activity->points;
                        $activity->disabled = 0;
                    }

                }
            }
            // dd($userDetail);
        }else{
            $userDetail['user_id'] = $userDetail['user_group_id'] = $userDetail['username'] = $userDetail['firstname'] = $userDetail['lastname'] = $userDetail['email'] = $userDetail['password'] = $userDetail['duties'] = $userDetail['status'] = $userDetail['basic_salary'] = $userDetail['salary'] = $userDetail['commission_on_delivered_amount']  = '';
            $userDetail['access'] = array();
        }

        $userGroups = OmsUserGroupModel::select('id','name')->get()->toArray();
        // dd($OmsUserModel);



        // dd($OmsUserModel->activities->toArray());
        // dd($userDetail['duties']['dutyLists']);
        return view("supplier.staff_form", ["userDetail" => $userDetail, "userGroups" => $userGroups,"oms_permissions" => @$oms_permissions, "access" => @$access, 'dashboard_options' => @$dashboard_options,'user_info'=>@$OmsUserModel,'group_access'=>@$group_access, 'oms_dutties' => @$oms_dutties]);
    }
    public function assign_permission($user_id,Request $request){
       $access = array();
       if( $request->isMethod('post') ){
        //  dd($request->all());
        $update_data = OmsUserModel::where('user_id', $user_id)->first();
        $access_array = $request->has('access') ? $request->access : [];
        $update_data->user_access = json_encode($access_array);
        if($update_data->save()){
          Session::flash('message', 'Permission assigned successfully.');
          return redirect()->back();
        }
       }
      //  $OmsUserModel = OmsUserModel::select('*')->where('user_id', $user_id)->first();
      //  $access = json_decode($OmsUserModel->user_access);
      // //  echo "user per<pre>"; print_r($access);
      //  $group = OmsUserGroupModel::where('id',$OmsUserModel->user_group_id)->first();
      //  $group_access = json_decode($group->access);
      //  echo "<pre>"; print_r($group_access);
      //  if( !in_array('orders/online',$group_access) ){
      //   die("if");
      //  }else{
      //   die("else");
      //  }

      //  die;
      // $oms_permissions = array();
      // $oms_permissions['Place_Order'] = OmsUserGroupModel::place_order_routes();
      // $oms_permissions['Business_Arcade_Normal_Order'] = OmsUserGroupModel::normal_order_routes();
      // $oms_permissions['Business_Arcade_Exchange_Order'] = OmsUserGroupModel::exchange_order_routes();
      // $oms_permissions['Business_Arcade_Return_Order'] = OmsUserGroupModel::return_order_routes();
      // $oms_permissions['Dress_Fair_Place_Order'] = OmsUserGroupModel::df_place_order_routes();
      // $oms_permissions['Dress_Fair_Normal_Order'] = OmsUserGroupModel::df_normal_order_routes();
      // $oms_permissions['Dress_Fair_Exchange_Order'] = OmsUserGroupModel::df_exchange_order_routes();
      // $oms_permissions['Dress_Fair_Return_Order'] = OmsUserGroupModel::df_return_order_routes();
      // $oms_permissions['Purchase_Management'] = OmsUserGroupModel::purchase_management_routes();
      // $oms_permissions['Inventory_Management'] = OmsUserGroupModel::inventory_management_routes();
      // $oms_permissions['promotion'] = OmsUserGroupModel::promotion_management_routes();
      // $oms_permissions['promotion_options'] = OmsUserGroupModel::oms_group_page_options_routes();
      // $oms_permissions['promotion_organic_options'] = OmsUserGroupModel::oms_organic_post_options_routes();
      // $oms_permissions['promotion_paid_ads_options'] = OmsUserGroupModel::oms_paid_ads_options_routes();
      // $oms_permissions['Employee_Performance'] = OmsUserGroupModel::employeePerformanceRoutes();
      // $oms_permissions['Sales']       = OmsUserGroupModel::employeePerformanceSaleRoutes();
      // $oms_permissions['Operation']   = OmsUserGroupModel::employeePerformanceOperationRoutes();
      // $oms_permissions['Designer']   = OmsUserGroupModel::employeePerformanceDesignerRoutes();
      // $oms_permissions['Marketing']   = OmsUserGroupModel::employeePerformanceMarketingRoutes();
      // $oms_permissions['Vouchers']   = OmsUserGroupModel::oms_vouchers_routes();
      // $dashboard_options = OmsUserGroupModel::inventory_management_dashboard_option_routes();
      // $oms_permissions['Oms_settings'] = OmsUserGroupModel::oms_setting_routes();
      // dd($oms_permissions['promotion_options']);
      // dd($access);
      // return view("supplier.assign_permission", ["oms_permissions" => $oms_permissions, "access" => $access, 'dashboard_options' => $dashboard_options,'user_info'=>$OmsUserModel,'group_access'=>$group_access]);
    }

    public function assign_duty($user_id,Request $request){
       $access = array();
       if( $request->isMethod('post') ){
        //  dd($request->all());
        $compulsory = [];
         $access = $request->access;
         $quantity = $request->quantity;
         $calculeted_points = $request->calculeted_points;
         $monthly_tasks = $request->monthly_tasks;
         $compulsory = $request->compulsory;
         $points = [];
         foreach($request->point as $point) {
            if(isset($point)) {
                array_push($points, $point);
            }
         }
         $durations = [];
         if(isset($request->duration)) {
            foreach($request->duration as $duration) {
                if(isset($duration)) {
                    array_push($durations, $duration);
                }
             }
         }
         $calcu_points = [];
        //  dd($calculeted_points);
         if(isset($request->calculeted_points)) {
            foreach($request->calculeted_points as $calculeted_point) {
                if(isset($calculeted_point)) {
                    array_push($calcu_points, $calculeted_point);
                }
             }
         }
          $calcu_points = [];
        //  dd($calculeted_points);
         if(isset($request->calculeted_points)) {
            foreach($request->calculeted_points as $calculeted_point) {
                if(isset($calculeted_point)) {
                    array_push($calcu_points, $calculeted_point);
                }
             }
         }
         $month_tasks = [];
         //  dd($calculeted_points);
          if(isset($request->monthly_tasks)) {
             foreach($request->monthly_tasks as $calculeted_task) {
                 if(isset($calculeted_task)) {
                     array_push($month_tasks, $calculeted_task);
                 }
              }
          }
        //  $points = $request->point;
        //  $points = array_values(array_filter($points));
        //  dd($durations);
        // $compulsories = [];
        // foreach($compulsory as $k => $comp) {
        //     array_push($compulsories, $k);
        // }
        // dd($compulsory);
         DutyAssignedUserModel::where('user_id', $user_id)->delete();
         if($access) {
            for($i = 0; $i < count($access); $i++) {
                $duty_data = array(
                    'user_id' => $user_id,
                    'activity_id' => $access[$i],
                    'point' => (count($points) > 0) ? $points[$i] : 0,
                    'quantity' => (count($quantity) > 0) ? $quantity[$i] : 0,
                    'duration' => (count($durations) > 0) ? $durations[$i] : 0,
                    // 'per_quantity_point' => (count($calcu_points) > 0) ? number_format($calcu_points[$i], 2, '.', '') : 0,
                    'per_quantity_point' => (count($calcu_points) > 0) ? $calcu_points[$i] : 0,
                    'monthly_tasks' => (count($month_tasks) > 0) ? $month_tasks[$i] : 0,
                    'daily_compulsory' => (array_key_exists($access[$i], $compulsory ? $compulsory : [])) ? 1 : 0,
                );
                // dd($duty_data);
                DB::table('duty_assigned_users')->insert($duty_data);
            }
         }


          Session::flash('message', 'Duties assigned successfully.');
          return redirect()->back();
       }
    }
    public function save_staff(Request $request){
        // dd($request->all());
        // date_default_timezone_set('Asia/Karachi');
        // $timestamp = date("H:i:s", strtotime(Input::get('break_time')));

        // dd($_SERVER);
        $this->validate($request, [
            'duty_start' => 'required',
            'duty_end' => 'required',
            'break_time' => 'required',
            'office_location' => 'required'
        ]);
        if(Input::all() > 0){
            if(Input::get('user_id')){
                $user = OmsUserModel::select('username','email')->where('user_id', Input::get('user_id'))->first();
                $check_username = OmsUserModel::select('*')->where('username', 'LIKE', Input::get('username'))->exists();
                $check_email = OmsUserModel::select('*')->where('email', 'LIKE', Input::get('email'))->exists();

                if(($user->username != Input::get('username')) && $check_username){
                    Session::flash('message', 'Username already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('staff/edit/'.Input::get('user_id'));
                }
                if(($user->email != Input::get('email')) && $check_email){
                    Session::flash('message', 'Email Address already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('staff/edit/'.Input::get('user_id'));
                }
                $update = array(
                    'user_group_id'  =>  Input::get('user_group_id'),
                    'username'  =>  Input::get('username'),
                    'firstname'  =>  Input::get('firstname'),
                    'lastname'  =>  Input::get('lastname'),
                    'email'  =>  Input::get('email'),
                    'basic_salary'  =>  Input::get('basic_salary'),
                    'salary'  =>  Input::get('salary'),
                    'commission_on_delivered_amount'  =>  Input::get('commission_on_delivered_amount'),
                    'currency'  =>  Input::get('currency'),
                    'duty_time_from'  =>  Input::get('duty_start'),
                    'duty_time_to'  =>  Input::get('duty_end'),
                    'break_time'  =>  Input::get('break_time'),
                    'office_location'  =>  Input::get('office_location'),
                    'basic_break_time'  =>  Input::get('break_time'),
                    'status'  =>  Input::get('status'),
                    'role'  =>  OmsUserGroupInterface::OMS_USER_GROUP_STAFF
                );
                if(Input::get('password') && Input::get('c_password') && Input::get('password') == Input::get('c_password')){
                    $salt = OmsUserModel::token(9);
                    $password = sha1($salt . sha1($salt . sha1(Input::get('password'))));
                    $update['salt'] = $salt;
                    $update['password'] = $password;
                }else if(Input::get('password') != Input::get('c_password')){
                    Session::flash('message', 'Password does not match!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('staff/edit/'.Input::get('user_id'));
                }
                OmsUserModel::where('user_id', Input::get('user_id'))->update($update);
            }else{
                $check_username = OmsUserModel::select('*')->where('username', 'LIKE', Input::get('username'))->exists();
                $check_email = OmsUserModel::select('*')->where('email', 'LIKE', Input::get('email'))->exists();

                if($check_username){
                    Session::flash('message', 'Username already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('staff/add');
                }
                if($check_email){
                    Session::flash('message', 'Email Address already exists!');
                    Session::flash('alert-class', 'alert-danger');
                    return redirect('staff/add');
                }

                $salt = OmsUserModel::token(9);
                $password = sha1($salt . sha1($salt . sha1(Input::get('password'))));

                $OmsUserModel = new OmsUserModel();
                $OmsUserModel->{OmsUserModel::FIELD_USER_NAME} = Input::get('username');
                $OmsUserModel->{OmsUserModel::FIELD_USER_GROUP_ID} = Input::get('user_group_id');
                $OmsUserModel->{OmsUserModel::FIELD_SALT} = $salt;
                $OmsUserModel->{OmsUserModel::FIELD_PASSWORD} = $password;
                $OmsUserModel->{OmsUserModel::FIELD_FIRSTNAME} = Input::get('firstname');
                $OmsUserModel->{OmsUserModel::FIELD_LASTNAME} = Input::get('lastname');
                $OmsUserModel->{OmsUserModel::FIELD_EMAIL} = Input::get('email');
                $OmsUserModel->{OmsUserModel::FIELD_STATUS} = Input::get('status');
                $OmsUserModel->salary = Input::get('salary');
                $OmsUserModel->basic_salary =  Input::get('basic_salary');
                $OmsUserModel->commission_on_delivered_amount = Input::get('commission_on_delivered_amount');
                $OmsUserModel->{OmsUserModel::FIELD_ROLE} = OmsUserGroupInterface::OMS_USER_GROUP_STAFF;
                $OmsUserModel->{OmsUserModel::FIELD_COMMISSION} = 0;
                $OmsUserModel->{OmsUserModel::FIELD_COMMISSION_ON} = '';
                $OmsUserModel->save();
            }
        }
        return redirect('staff');
    }
    public function delete_staff($user_id = ''){
        if($user_id){
            OmsUserModel::where('user_id',  $user_id)->delete();
        }
        return redirect('staff');
    }
    public function login_staff(){
        if(count(Input::all()) > 0 && Input::get('login-user')){
            $details = OmsUserModel::where('user_id', Input::get('login-user'))->get()->first();
            if(!$details){
                return redirect('/login')->withInput()->withErrors('User details not found');
            }else{
                $userName = trim(filter_var($details->username, FILTER_SANITIZE_STRING));
                $user = OmsUserModel::where(OmsUserModel::FIELD_USER_NAME, $userName)->first();

                if (isset($user->{OmsUserModel::FIELD_USER_ID}) && $user->{OmsUserModel::FIELD_STATUS}){
                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_STAFF){
                        $OmsUserModel = OmsUserModel::where('user_id', $user->{OmsUserModel::FIELD_USER_ID})->first();
                        $permissions = OmsUserGroupModel::select(OmsUserGroupModel::FIELD_ACCESS)->where(OmsUserGroupModel::FIELD_ID, $OmsUserModel->user_group_id)->first();
                    }

                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_STAFF){
                        $session['role'] = "STAFF";
                    }else if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_RESELLER){
                        $session['role'] = "RESELLER";
                    }else{
                        $session['role'] = "ADMIN";
                    }

                    if($user->{OmsUserModel::FIELD_ROLE} == OmsUserGroupInterface::OMS_USER_GROUP_STAFF){
                        $user_perm = $user->user_access;
                        if($permissions){
                          if( count(json_decode($user_perm)) > 0 ){
                            $per = $user_perm;
                          }else{
                            $per = $permissions->access;
                          }
                        }else{
                            $per = '[]';
                        }
                        $session['access'] = json_encode(array_flip(json_decode($per)));
                    }else{
                        $session['access'] = json_encode(array_flip(array()));
                    }

                    $session['duties'] = json_encode($user->activities);
                    \Session::put(array_merge($user->toArray(), $session));
                    return redirect('/home');
                }else{
                    return redirect('/login')->withInput()->withErrors('You haven\'t permission to access');
                }
            }
        }
        return redirect('/home');
    }

    public function userPaymentByMonth(Request $request) {
        // dd($request->all());
        $total_paid = 0;
        $payments = UserPaymentModel::where('user_id', $request->user_id)->where('month', $request->month.'-01')->get();
        foreach($payments as $payment) {
            $total_paid += $payment->amount;
            $payment->month = date('F, Y', strtotime($payment->month));
            $payment->payment_date = date('F, Y', strtotime($payment->payment_date));
        }
        return view('supplier.userPaymentFilter')->with(compact('payments','total_paid'));
        // return response()->json([
        //     'status' => true,
        //     'payments' => $payments,
        //     'total_paid' => $total_paid
        // ]);
    }
}
