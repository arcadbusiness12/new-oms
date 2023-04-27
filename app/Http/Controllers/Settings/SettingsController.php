<?php
namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\PublicHoliday;
use App\Models\Oms\CommissionSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use DB;
use Session;
use Validator;
use Excel;

/**
 * OrdersController to Handle Orders from opencart db and move to oms
 *
 * @author kamran
 */
class SettingsController extends Controller
{
    const VIEW_DIR = 'settings';
    const PER_PAGE = 20;

    public function index(){
        // $product_options = DB::table(env('OPENCART_DATABASE').'.oc_option_description')->select(DB::raw('*'))->get();
        $product_options = DB::table('oms_options')->select(DB::raw('*'))->get();
        // dd( $product_options->toArray() );
        $stock_level['out_of_stock'] = OmsSettingsModel::getByKey('out_of_stock');
        $stock_level['low_stock'] = OmsSettingsModel::getByKey('low_stock');
        $stock_level['enough_stock'] = OmsSettingsModel::getByKey('enough_stock');
        $stock_level['over_stock'] = OmsSettingsModel::getByKey('over_stock');
        $duration = OmsSettingsModel::getByKey('duration');
        $alarm = OmsSettingsModel::getByCode('alarm');
        $product_option['color'] = OmsSettingsModel::get('product_option','color');
        $product_option['size'] = OmsSettingsModel::get('product_option','size');

        $alarm_period = array(
            'first'  =>  '0 - 20 Percent',
            'second'  =>  '21 - 40 Percent',
            'third'  =>  '41 - 60 Percent',
            'fourth'  =>  '61 - 80 Percent',
            'fifth'  =>  '81 - 100 Percent',
        );
        return view(self::VIEW_DIR.".settings", ["options" => $product_options, "stock_level" => $stock_level, "duration" => $duration, "product_option" => $product_option, "alarm" => $alarm, "alarm_period" => $alarm_period]);
    }
    public function save_settings(){
        if(Input::get('stock_level')){
            OmsSettingsModel::where('code', 'stock_level')->delete();
            foreach (Input::get('stock_level') as $key => $value) {
                $OmsSettingsModel = new OmsSettingsModel();
                $OmsSettingsModel->{OmsSettingsModel::FIELD_CODE} = 'stock_level';
                $OmsSettingsModel->{OmsSettingsModel::FIELD_KEY} = $key;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_VALUE} = $value;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_SERIALIZE} = 0;
                $OmsSettingsModel->save();
            }
        }
        if(Input::get('duration')){
            OmsSettingsModel::where('code', 'duration')->delete();
            $OmsSettingsModel = new OmsSettingsModel();
            $OmsSettingsModel->{OmsSettingsModel::FIELD_CODE} = 'duration';
            $OmsSettingsModel->{OmsSettingsModel::FIELD_KEY} = 'duration';
            $OmsSettingsModel->{OmsSettingsModel::FIELD_VALUE} = json_encode(Input::get('duration'));
            $OmsSettingsModel->{OmsSettingsModel::FIELD_SERIALIZE} = 1;
            $OmsSettingsModel->save();
        }
        if(Input::get('product_option')){
            OmsSettingsModel::where('code', 'product_option')->delete();
            foreach (Input::get('product_option') as $key => $value) {
                $OmsSettingsModel = new OmsSettingsModel();
                $OmsSettingsModel->{OmsSettingsModel::FIELD_CODE} = 'product_option';
                $OmsSettingsModel->{OmsSettingsModel::FIELD_KEY} = $key;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_VALUE} = $value;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_SERIALIZE} = 0;
                $OmsSettingsModel->save();
            }
        }
        if(Input::get('alarm')){
            OmsSettingsModel::where('code', 'alarm')->delete();
            foreach (Input::get('alarm') as $key => $value) {
                $OmsSettingsModel = new OmsSettingsModel();
                $OmsSettingsModel->{OmsSettingsModel::FIELD_CODE} = 'alarm';
                $OmsSettingsModel->{OmsSettingsModel::FIELD_KEY} = $key;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_VALUE} = (int)$value;
                $OmsSettingsModel->{OmsSettingsModel::FIELD_SERIALIZE} = 0;
                $OmsSettingsModel->save();
            }
        }
        return redirect('settings');
    }
    public function get_supplier(){
        $data = OmsUserModel::select('*')->where('role','!=','ADMIN')->get()->toArray();
        $suppliers = array();
        foreach ($data as $supplier) {
            $suppliers[] = array(
                'username'      =>  $supplier['username'],
                'name'          =>  $supplier['firstname']." ".$supplier['lastname'],
                'email'         =>  $supplier['email'],
                'access'        =>  json_decode($supplier['access'], true),
                'status'        =>  $supplier['status'],
                'date_added'    =>  $supplier['created_at'],
                'edit'          =>  url("/supplier/edit/{$supplier['user_id']}")
            );
        }
        return view("supplier.supplier_list", ["suppliers" => $suppliers]);
    }
    public function add_supplier($user_id = ''){
        if($user_id){
            $OmsUserModel = OmsUserModel::select('*')->where('user_id', $user_id)->first()->toArray();
            $userDetail = array();
            if($OmsUserModel){
                $userDetail = array(
                    'user_id'   =>  $OmsUserModel['user_id'],
                    'username'  =>  $OmsUserModel['username'],
                    'firstname' =>  $OmsUserModel['firstname'],
                    'lastname'  =>  $OmsUserModel['lastname'],
                    'email'     =>  $OmsUserModel['email'],
                    'password'  =>  $OmsUserModel['password'],
                    'access'    =>  json_decode($OmsUserModel['access'], true),
                    'status'    =>  $OmsUserModel['status']
                );
            }
        }else{
            $userDetail['user_id'] = $userDetail['username'] = $userDetail['firstname'] = $userDetail['lastname'] = $userDetail['email'] = $userDetail['password'] = $userDetail['status'] = '';
            $userDetail['access'] = array();
        }
        $permissions = array('orders' => 'Orders','exchange_orders' => 'Exchange Orders','purchase' => 'Purchase Management','settings' => 'Settings');
        return view("supplier.supplier_form", ["userDetail" => $userDetail, "permissions" => $permissions]);
    }
    public function save_supplier(){
        if(Input::all() > 0){
            if(Input::get('access')){
                $access = json_encode(Input::get('access'));
            }else{
                $access = '';
            }
            if(Input::get('user_id')){
                $update = array(
                    'username'  =>  Input::get('username'),
                    'firstname'  =>  Input::get('firstname'),
                    'lastname'  =>  Input::get('lastname'),
                    'email'  =>  Input::get('email'),
                    'access'  =>  $access,
                    'status'  =>  Input::get('status')
                );
                OmsUserModel::where('user_id', Input::get('user_id'))->update($update);
            }else{
                $OmsUserModel = new OmsUserModel();
                $OmsUserModel->{OmsUserModel::FIELD_USER_NAME} = Input::get('username');
                $OmsUserModel->{OmsUserModel::FIELD_PASSWORD} = base64_encode(Input::get('password'));
                $OmsUserModel->{OmsUserModel::FIELD_FIRSTNAME} = Input::get('firstname');
                $OmsUserModel->{OmsUserModel::FIELD_LASTNAME} = Input::get('lastname');
                $OmsUserModel->{OmsUserModel::FIELD_EMAIL} = Input::get('email');
                $OmsUserModel->{OmsUserModel::FIELD_ROLE} = 'SUPPLIER';
                $OmsUserModel->{OmsUserModel::FIELD_ACCESS} = $access;
                $OmsUserModel->{OmsUserModel::FIELD_STATUS} = Input::get('status');
                $OmsUserModel->save();
            }
        }
        return redirect('supplier');
    }
    public function commissionSettings(Request $request){
      $data = CommissionSetting::where('id',1)->first();
      if($request->isMethod('post')){
        $data->below_target_deduction_amount             = $request->below_target_deduction_amount;
        $data->minimum_delivery_success                  = $request->minimum_delivery_success;
        $data->minimum_delivery_success_deduction_amount = $request->minimum_delivery_success_deduction_amount;
        $data->commission_qualify_delivery_success       = $request->commission_qualify_delivery_success;
        $data->per_order_above_target_commission_amount  = $request->per_order_above_target_commission_amount;
        $data->	commission_conditions_amount                     = json_encode($request->commission_conditions_amount); 
        $data->save();
        
      }
      // dd($data->toArray());
      return view("settings.commission_settings",compact('data'));
    }

    public function areaConnections() {
        // dd('Ok');
       
        $cities = DB::connection('opencart')->table('area')->whereIn('zone_id',[2457,2458,2459,2460,2461,2462])->get();
        // dd($cities);
        $oms_op_val = [];
        return view('settings.areaConnection', compact('cities','oms_op_val'));
    }
    public function addPublicHolidays() {
      // dd('Ok');
      $data = PublicHoliday::with(['createdBy'=>function($query){
        $query->select("user_id","firstname");
      }])->orderBy("id",'DESC')->paginate(20);
      // dd($data->toArray());
      return view('settings.public_holidays',compact('data'));
    }
    public function savePublicHolidays(Request $request) {
      // dd('Ok');
      // dd($request->all());
      $date_range = $request->date_range;
      if( $date_range && count($date_range) > 0 ){
        foreach( $date_range as $date => $type ){
          $new_row = new PublicHoliday();
          $new_row->date = date("Y-m-d",strtotime($date));
          $new_row->reason = $request->reason;
          $new_row->type = $type;
          $new_row->created_by = session('user_id');
          $new_row->save();
        }
      }
      return redirect()->back();
    }
}