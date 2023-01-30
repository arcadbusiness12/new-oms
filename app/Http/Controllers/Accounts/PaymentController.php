<?php

namespace App\Http\Controllers\Accounts;
//dev
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsLedger;
use App\Models\Oms\OmsLedgerDetail;
use App\Models\Oms\ShippingProvidersModel;
use App\Providers\Reson8SmsServiceProvider;
use App\Platform\OpenCart\Currency;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Session;
use Excel;

class PaymentController extends Controller
{
    const VIEW_DIR = 'accounts';
    const PER_PAGE = 20;
    private $DB_BAOPENCART_DATABASE = '';
    private $DB_BAOMS_DATABASE = '';
    private $store = '';
    private $static_option_id = 0;
    private $website_image_source_path =  '';
    private $website_image_source_url =  '';
    private $opencart_image_url = '';

    function __construct(){
        $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
        $this->DB_BAOMS_DATABASE = env('DB_BAOMS_DATABASE');
        $this->store = 'businessarcade';
        $this->website_image_source_path =  $_SERVER["DOCUMENT_ROOT"] . '/image/';
        $this->website_image_source_url =  $_SERVER["REQUEST_SCHEME"] . '://'. $_SERVER["HTTP_HOST"] .'/image/';
        $this->opencart_image_url = env('OPEN_CART_IMAGE_URL');
    }

    public function index(Request $request){
        if( $request->ajax() && $request->ledger_id > 0 ){
          return $this->confirmPayment($request);
        }
        if( $request->isMethod('post') && $request->payment_submit != "" ){
          $this->addPayment($request);
        }
        $today = date("Y-m-d");
        $data_payment = OmsLedger::SELECT("*",DB::raw('SUM(total_amount) OVER() AS total_payments'),
          DB::raw('SUM( CASE WHEN confirm_payment = 1 THEN total_amount ELSE 0 END) OVER() AS total__confirm_payments'))
          ->with(['shippingProvider','user','transactionType'])->where('amount_type',2)->orderBy("id","DESC");
        $data_receipt = OmsLedger::SELECT("*",DB::raw('SUM(paid_amount) OVER() AS total_receipt'))->with(['shippingProvider','user','transactionType'])->where('amount_type',1)->where('created_by','!=',23)->orderBy("id","DESC");
        if( $request->date_from != '' &&  $request->date_to != '' ){
          $data_payment = $data_payment->whereDate('created_at','>=',$request->date_from);
          $data_payment = $data_payment->whereDate('created_at','<=',$request->date_to);
          //
          $data_receipt = $data_receipt->whereDate('created_at','>=',$request->date_from);
          $data_receipt = $data_receipt->whereDate('created_at','<=',$request->date_to);
        }else{
          $data_payment = $data_payment->whereDate('created_at','>','2021-06-30');
          $data_receipt = $data_receipt->whereDate('created_at','>','2021-06-30');
        }
        // $data_payment = $data_payment->get();
        // $data_receipt = $data_receipt->get();
        // dd($data_payment->toArray());
        $data_receipt = $data_receipt->paginate(30)->appends(Input::all());
        $data_payment = $data_payment->paginate(30)->appends(Input::all());
        // dd($data_receipt->toArray());
        // dd($data->toArray());
        $old_input = Input::all();
        $shipping_data = ShippingProvidersModel::where("is_active",1)->get();
        return view(self::VIEW_DIR . ".payment_listing",compact('shipping_data','data_payment','data_receipt','old_input'));
    }
    protected function confirmPayment( $request ){
      $confirm_query = OmsLedger::where('id',$request->ledger_id)->find($request->ledger_id);
      if( $confirm_query->confirm_payment == 1 ){
        $confirm_payment = null;
      }else{
        $confirm_payment = 1;
      }
      $confirm_query->confirm_payment = $confirm_payment;
      if( $confirm_query->save() ){
        return 1;
      }else{
        return 0;
      }
    }
    protected function addPayment($request){
      $new_row = new OmsLedger();
      $new_row->amount_type = 2;
      $new_row->total_amount = $request->payment_amount;
      $new_row->description  = $request->payment_description;
      $new_row->created_at   = date('Y-m-d H:i:s');
      $new_row->updated_at   = date('Y-m-d H:i:s');
      $new_row->created_by   = session('user_id');
      if( $new_row->save() ){
        session()->flash('mesg',"Payment saved Successfuly.");
      }
    }
 
}