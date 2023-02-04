<?php

namespace App\Http\Controllers\Accounts;
//dev
use App\Http\Controllers\Controller;
use App\Models\Oms\OmsLedger;
use App\Models\Oms\AirwayBillTrackingModel;
use App\Models\Oms\ExchangeAirwayBillTrackingModel;
use App\Models\Oms\OmsLedgerDetail;
use App\Models\Oms\OmsOrdersModel;
use App\Models\Oms\ShippingProvidersModel;
use App\Models\OpenCart\Orders\OrderedProductModel;
use App\Providers\Reson8SmsServiceProvider;
use App\Platform\OpenCart\Currency;
use App\Platform\Helpers\ToolImage;
use Carbon\Carbon;
use DB;
use App\Models\Oms\OmsActivityLogModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request AS Input;
use Illuminate\Support\Facades\URL;
use Session;
use Excel;
use Validator;
use App\Services\LengthPager;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromView;

class ReceiptController extends Controller
{
    const VIEW_DIR = 'accounts';
    const PER_PAGE = 20;

    function __construct(){
    }

    public function index(Request $request){
      $data = OmsLedger::with(['shippingProvider','user','transactionType'])->where('amount_type',1)->orderBy("id","DESC");
      if( $request->date_from != '' ){
          $data = $data->whereDate('created_at','>=',$request->date_from);
      }
      if( $request->date_to != '' ){
          $data = $data->whereDate('created_at','<=',$request->date_to);
      }
      if( $request->search_by_courier != "" ){
          $data = $data->where("account_id",$request->search_by_courier);
      }
      $data = $data->paginate()->appends(Input::all());
    //   dd($data->toArray());
      $shipping_data = ShippingProvidersModel::where("is_active",1)->get();
      $old_input = Input::all();
      return view(self::VIEW_DIR . ".receipt_listing",compact('data','shipping_data','old_input'));
    }
    public function pendingReciepts(Request $request){
        // $data = AirwayBillTrackingModel::with(['shipping_provider'])->where('payment_status',0)->orderBy("tracking_id","DESC");
        //normal qeury start
        // dd($request->all());
        $statusWhereCluase = [];
        if($request->payment != null && ($request->payment == 1 || $request->payment == 0)){
          // dd("ok");
          $statusWhereCluase[] = array("awbt.payment_status",$request->payment);
        }
        $data = DB::table("oms_orders AS ord")
                ->leftjoin("oms_place_order AS pord",function($join){
                  $join->on("pord.order_id","=","ord.order_id");
                  $join->on("pord.store","=","ord.store");
                })
               ->join(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
                $join->on('awbt.order_id','=','ord.order_id');
                $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
               })
               ->join("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
               ->select(DB::raw("ord.order_id,ord.oms_order_status,ord.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                    pord.total_amount,
                    SUM( pord.total_amount ) OVER() AS grand_total_amount,
                    pord.payment_method_id,
                    pord.payment_method_name,
                    1 AS order_type,
                    pord.shipping_address_1,
                    pord.shipping_address_2,
                    pord.shipping_city_area,
                    pord.shipping_city,
                    pord.firstname,
                    pord.lastname,
                    pord.mobile
                  "))
              ->where('ord.oms_order_status',"!=",6)
              ->where('ord.oms_order_status',"!=",5)
              ->where($statusWhereCluase);
        //normal qeury end
        //exchange qeury start
        $data_exchange = DB::table("oms_exchange_orders AS ord")
                ->leftjoin("oms_place_exchanges AS peord",function($join){
                  $join->on("ord.order_id","=","peord.order_id");
                  $join->on("ord.store","=","peord.store");
                })
              // ->join("exchange_airwaybill_tracking AS awbt",function($join){
              ->join(DB::raw("(SELECT * FROM `exchange_airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM exchange_airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
                  $join->on('awbt.order_id','=','ord.order_id');
                  $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
               })
               ->join("shipping_providers AS courier","courier.shipping_provider_id","=","ord.last_shipped_with_provider")
               ->select(DB::raw("ord.order_id,ord.oms_order_status,ord.store,courier.name AS courier_name,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                      peord.total_amount,
                      SUM( peord.total_amount ) OVER() AS grand_total_amount,
                      peord.payment_method_id,
                      peord.payment_method_name,
                      2 AS order_type,
                      peord.shipping_address_1,
                      peord.shipping_address_2,
                      peord.shipping_city_area,
                      peord.shipping_city,
                      peord.firstname,
                      peord.lastname,
                      peord.mobile
                  "))
              ->where('ord.oms_order_status',"!=",6)
              ->where('ord.oms_order_status',"!=",5)
              ->where('ord.oms_order_status',"!=",7)
              ->where($statusWhereCluase);
              // dd($statusWhereCluase);
        //exchange qeury start end
        if( $request->date_from != '' ){
            $data = $data->whereDate('awbt.created_at','>=',$request->date_from);
            $data_exchange = $data_exchange->whereDate('awbt.created_at','>=',$request->date_from);
        }
        if( $request->date_to != '' ){
            $data = $data->whereDate('awbt.created_at','<=',$request->date_to);
            $data_exchange = $data_exchange->whereDate('awbt.created_at','<=',$request->date_to);
        }
        $courier_info = 0;
        if( $request->search_by_courier != "" ){
          $data = $data->where("ord.last_shipped_with_provider",$request->search_by_courier);
          $data_exchange = $data_exchange->where("ord.last_shipped_with_provider",$request->search_by_courier);
          $courier_info  = ShippingProvidersModel::where("shipping_provider_id",$request->search_by_courier)->first();
        }
        if( $request->by_store != "" ){
          $data_exchange = $data_exchange->where("ord.store",$request->by_store);
        }
        if( $request->courier_delivered != "" ){
          $data = $data->where("awbt.courier_delivered",$request->courier_delivered);
          $data_exchange = $data_exchange->where("awbt.courier_delivered",$request->courier_delivered);
        }
        if( $request->oms_delivered > 0 ){
          $data = $data->where("ord.oms_order_status",$request->oms_delivered);
          $data_exchange = $data_exchange->where("ord.oms_order_status",$request->oms_delivered);
        }


        // dd($data);
        $data = $data->groupBy('ord.order_id');
        $data_exchange = $data_exchange->groupBy('ord.order_id');
        $per_page = self::PER_PAGE;
        $data = $data->union($data_exchange);
        $data = $data->orderBy('created_at','DESC');
        if($request->export && $request->export == 'Yes') {
          $data = $data->get();
        }else {
          // $data = $data->simplePaginate(30)->appends(Input::all());
          $data  = $data->get();
          if( $request->payment_type != "" ){
            $data = $data->whereIn("payment_code",explode(',',$request->payment_type));
          }
          if( $request->order_type != "" ){
            $data = $data->where("order_type",$request->order_type);
          }
          if( $request->order_amount != "" ){
            $data = $data->where("amount",$request->order_amount);
          }
          if( $request->order_number != "" ){
            $data = $data->where("order_id",$request->order_number);
          }
          if( $request->airway_bill_number != "" ){
            $data = $data->where("airway_bill_number",$request->airway_bill_number);
          }

          if( isset($request->only_shipped) && $request->only_shipped == "on" ){
            $data = $data->where("oms_order_status",3);
            // dd($request->all());
            if( isset($request->courier_print_submit) && $request->courier_print_submit == "courier_print_submit" ){
              // dd($data);
              return $this->courierShipmentPrint($data,$request->search_by_courier);
            }
          }

          if( $request->courier_delivered == 1 && $request->oms_delivered == 4 && $request->payment == 0 && $courier_info && $courier_info->auto_deliver == 0 ){
            $pagination_flag = 0;
          }else{
            $pagination_flag = 1;
            $data  = $data->toArray();
            $data = customPaginate($data, 30, Input::all());
          }
        }
        // dd($data);
        $old_input = Input::all();
        // echo "<pre>"; dd($old_input);
        $shipping_data = ShippingProvidersModel::where("is_active",1)->get();

        if($request->export && $request->export == 'Yes') {
             // dd("ok");
            //   Excel::download('shipment_list', function($excel) use($data,$shipping_data) {
            //     $excel->sheet('Payment History', function($sheet) use($data,$shipping_data) {
            //         $sheet->loadView(self::VIEW_DIR . ".export_receipt_listing", array("data" => $data,'shipping_data' => $shipping_data));
            //     });
            //   })->export('shipment_list.xls');x
            $this->exportCsv($data);
        }else {
          return view(self::VIEW_DIR . ".pending_receipt_listing",compact('data','shipping_data','old_input','courier_info','pagination_flag'));
        }
      }
      public function exportCsv($data){
        // dd($data->toArray());
        $filename = "shipment.csv";
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');

        // open the "output" stream
        // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
        $f = fopen('php://output', 'w');
        $line = array("Courier","Order Id","AWB No","Amount","Courier Delivered","OMS Delivered","Payment Recieved","Created at");
        fputcsv($f, $line);
        foreach ($data as $key => $row) {
            $courier_delivered = $row->courier_delivered == 1 ? "Yes" : "No";
            $oms_delivered     = $row->oms_order_status  == 4 ? "Yes" : "No";
            $payment_status    = $row->payment_status    == 1 ? "Yes" : "No";
            $line = array($row->courier_name,$row->order_id,$row->airway_bill_number,$row->total_amount,$courier_delivered, $oms_delivered, $payment_status,$row->created_at);
            fputcsv($f, $line);
        }
      }
    private function courierShipmentPrint($data,$courier_id){
      $data = $data->toArray();
      // dd($data);
      $order_details = array();
			$shipper = ShippingProvidersModel::where('shipping_provider_id', $courier_id)->first();
      $ship_date = "";
			foreach ($data as $key => $value) {
				// $awb = AirwayBillTrackingModel::select('airway_bill_number','shipping_provider_id')->where('order_id', $value->order_id)->where("store",$this->store)->first();
				// $qty = OrderedProductModel::select(DB::Raw('SUM(quantity) as total'))->where('order_id', $value->order_id)->first();

				$address = '';
				if($value->shipping_address_1){
					$address .= $value->shipping_address_1.($value->shipping_address_2 ? ", ".$value->shipping_address_2 : "");
				}
				if($value->shipping_city_area){
					$address .= ', ' . $value->shipping_city_area;
				}
				if($value->shipping_city){
					$address .= ', ' . $value->shipping_city;
				}
        $order_type = "";
        if( $value->order_type == 2 ){
          $order_type = "-1";
        }
				$order_details[] = array(
					'order_id'  =>  $value->order_id.$order_type,
					'awb'       =>  $value->airway_bill_number,
					'name'      =>  $value->firstname . ' ' . $value->lastname,
					'mobile'    =>  $value->mobile ? : '-',
					'address'   =>  $address,
					'qty'       =>  0,
					'amount'    =>  number_format($value->total_amount,2)
				);
            $ship_date = date("Y-m-d",strtotime($value->created_at));
			}
			$total_orders = count($order_details);
      $record_limit = 16;
      $page = ".ship_print";
      // dd($shipper->toArray());
      if( $shipper->shipment_print == 1  ){
        $page = ".ship_print_short";
        $record_limit = 70;
      }else if( $shipper->shipment_print == 2 ){
        $page = ".ship_print";
        $record_limit = 16;
      }
			$order_details = array_chunk($order_details, $record_limit);
      $ship_date = date("Y-m-d");
	  return view("orders". $page, ['orders' => $order_details, 'total_orders' => $total_orders, 'shipper' => $shipper, 'ship_date' => $ship_date]);
    }
    public function savePendingReciepts(Request $request){
    //    dd($request->all());
        $shipment_id = $request->courier_id;
        $shipping_provider_data = ShippingProvidersModel::where("shipping_provider_id",$shipment_id)->first();
        $pending_orders_ids = $request->pending_order_ids;
        $order_ids     = $request->order_ids;
        $payment_codes = $request->payment_code;
        $amounts    = $request->amount;
        $store_id   = $request->store_id;
        $order_type = $request->order_type;
        //
        $received_amount = $request->received_amount;
        $balance_amount = $request->balance_amount;
        if( is_array($pending_orders_ids) && count($pending_orders_ids) > 0 ){
            $total_amount = 0;
            $ledgerObj = new OmsLedger();
            $ledgerObj->account_id = $shipment_id;
            $ledgerObj->account_type = 1; // 1 for shipment provider
            $ledgerObj->transaction_type_id = 0; // for both 1 for normal order and exchange
            $ledgerObj->amount_type = 1; // 1 for reciept
            $ledgerObj->store = 0; //for both BA and DF
            $ledgerObj->created_by = session('user_id');
            $ledgerObj->save();
            foreach($pending_orders_ids as $key => $awb_number){
                $is_exchange = ($order_type[$awb_number]==2) ? 1 : 0;
                $check_payment = OmsLedgerDetail::where('ref_id',$order_ids[$awb_number])->where('is_exchange',$is_exchange)->first();
                if( $check_payment ){
                  continue;
                }
                $is_prepaid = 0;
                // if( $payment_codes[$awb_number] == 1){
                //     $is_prepaid = 0;
                // }else{
                //     $is_prepaid = 1;
                // }
                $ledger_detailObj = new OmsLedgerDetail();
                $ledger_detailObj->ledger_id = $ledgerObj->id;
                $ledger_detailObj->ref_id = $order_ids[$awb_number];
                $ledger_detailObj->amount = $amounts[$awb_number] ? $amounts[$awb_number] : 0;
                $ledger_detailObj->is_prepaid = $is_prepaid;
                $ledger_detailObj->store = $store_id[$awb_number];
                $ledger_detailObj->airway_bill_number = $awb_number;
                $ledger_detailObj->is_exchange = $is_exchange;
                $ledger_detailObj->save();
                if($order_type[$awb_number]==1){
                  AirwayBillTrackingModel::where(['airway_bill_number'=>$awb_number,'order_id'=>$order_ids[$awb_number],"shipping_provider_id"=>$shipment_id,'store'=>$store_id[$awb_number]])->update(["payment_status"=>1]);
                  OmsActivityLogModel::newLog($order_ids[$awb_number],23,$store_id[$awb_number]);  //23 Normal Payment Received
                }else if($order_type[$awb_number]==2){
                  ExchangeAirwayBillTrackingModel::where(['airway_bill_number'=>$awb_number,'order_id'=>$order_ids[$awb_number],"shipping_provider_id"=>$shipment_id,'store'=>$store_id[$awb_number]])->update(["payment_status"=>1]);
                  OmsActivityLogModel::newLog($order_ids[$awb_number],24,$store_id[$awb_number]);  //24 Exchange Payment Received
                }
                $total_amount += $amounts[$awb_number];
            }
            $bill_no = $shipping_provider_data->short_code."-".$ledgerObj->id;
            $ledgerObj->total_amount = $total_amount;
            $ledgerObj->bill_no = $bill_no;
            $ledgerObj->paid_amount =  $received_amount;
            $ledgerObj->balance_amount =  $balance_amount;
            $ledgerObj->save();

            $data = OmsLedger::where("id",$ledgerObj->id)->with(['ledgerDetails','shippingProvider'])->first();
            // dd($data->toArray());
            // echo  view(self::VIEW_DIR .".print_deliver_report_shipping_provider_wise_all",compact('data'))->render(); die;
            return redirect('accounts/receipts');
        }

    }

    public function receivePendingReciepts(Request $request) {
      dd($request->all());
      $pending_order_awb = $request->pending_order_awb;
      $order_id = $request->order_id;
      $order_type = $request->order_type;
      // dd($order_type['6261307555']);
      foreach($pending_order_awb as $awb) {
        dd($order_type[$awb]);
        if($order_type[$awb] == 1) {
          AirwayBillTrackingModel::where('airway_bill_number', $awb)->where('order_id', $order_type[$awb])->update(['']);
        }
      }
    }
    public function updateShippingPayment(Request $request){
      if( $request->isMethod('POST') ){
          $adjustment_amount = isset($request->adjustment_amount) ? $request->adjustment_amount : 0;
          $comments          = isset($request->adjustment_amount_comment) ? $request->adjustment_amount_comment : '';
          $data = OmsLedger::where('id',$request->ledger_id)->first();
          $data->paid_amount       = $request->recieved_amount;
          $data->balance_amount    = $request->balance_amount;
          $data->adjustment_amount = $adjustment_amount;
          $data->description       = $comments;
          if( $data->save() ){
              return response()->json(['msg'=>"Saved successfully."], 200);
          }else{
              return response()->json(['msg'=>"something wrong."], 400);
          }


      }
    }
    public function getReceiptPopup(Request $request){
        $id = $request->id;
        // $id = 1;
        $data = OmsLedger::with(['shippingProvider','user','ledgerDetails'])->where("id",$id)->first();
        // dd($data->toArray());
        return view(self::VIEW_DIR . ".popup_receipt_details_content",compact('data'))->render();
    }

  public function processPendingExReceiptFile(Request $request) {
    $all = '';
    $validator = Validator::make(
			[
				'file' => $request->deliverd_orders_file,
				'extension' => strtolower($request->deliverd_orders_file->getClientOriginalExtension()),
			],
			[
				'file' => 'required',
				'extension' => 'required|in:csv,xlsx,xls',
			]
		);
		if ($validator->passes()) {

			try
			{

				$fileName = 'deliverd-orders-file-' . Carbon::now('Asia/Muscat')->format('m-d-Y-H-i-s') . '.' . $request->deliverd_orders_file->getClientOriginalExtension();
				$request->deliverd_orders_file->move(public_path('/uploads'), $fileName);
                // error_reporting(E_ALL ^ E_WARNING);
                $records = Excel::toArray([],public_path('/uploads/' . $fileName));
                // echo "<pre>"; print_r($records); die;
                if( is_array($records) ){
                    $records = $records[0];
                }
                // echo "<pre>"; print_r($records);
                // die("test");
                $exchange_data = [];
                $normal_data = [];
                $missing_data = [];
                $count = 0;
                foreach ($records as $key => $order) {
                    if( $key == 0 ) continue;
                    $excel_orer_id     = $order[0];
                    $excel_amount      = $order[1];
                    $excel_airwaybill  = $order[2];
                    // dd(is_numeric($order['order']));
                    if ( $excel_orer_id != '') {
                        if(strpos($excel_orer_id, '-') !== false) {
                            //exchange qeury start
                            $excel_orer_id = str_replace("-1","",$excel_orer_id);
                            //  dd($awb[0]);
                            $data_exchange = DB::table("oms_exchange_orders AS eord")
                            ->leftjoin("oms_place_exchanges AS pord",function($join){
                                $join->on("pord.order_id","=","eord.order_id");
                                $join->on("pord.store","=","eord.store");
                            })
                            ->join("exchange_airwaybill_tracking AS awbt",function($join){
                            $join->on('awbt.order_id','=','eord.order_id');
                            $join->on('awbt.shipping_provider_id','=','eord.last_shipped_with_provider');
                            })
                            ->select(DB::raw("eord.order_id,eord.oms_order_status,eord.store,awbt.tracking_id,awbt.shipping_provider_id,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                                (pord.total_amount) AS amount,
                                pord.payment_method_id,
                                SUM(pord.total_amount) OVER() AS total_amount,
                                2 AS order_type
                                "))
                            ->where('awbt.order_id',$excel_orer_id)
                            ->where('awbt.airway_bill_number',$excel_airwaybill)
                            ->first();
                            if($data_exchange) {
                                if($data_exchange->amount == $excel_amount) {
                                    $data_exchange->amount_match = 1;
                                }else {
                                    $data_exchange->amount_match = 0;
                                }
                                $data_exchange->ex_amount = number_format((float)$excel_amount, 2, '.', '');
                                $data_exchange->exchange_or = 1;
                                array_push($exchange_data,$data_exchange);
                            }else{
                                array_push($missing_data,$order);
                            }

                        }else {
                            $data = DB::table("oms_orders AS ord")
                            ->leftjoin("oms_place_order AS pord",function($join){
                                $join->on("pord.order_id","=","ord.order_id");
                                $join->on("pord.store","=","ord.store");
                            })
                            ->join("airwaybill_tracking AS awbt",function($join){
                                $join->on('awbt.order_id','=','ord.order_id');
                                $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
                            })
                            ->select(DB::raw("ord.order_id,ord.oms_order_status,ord.store,awbt.tracking_id,awbt.shipping_provider_id,awbt.airway_bill_number,awbt.courier_delivered,awbt.payment_status,awbt.created_at,
                                (pord.total_amount) AS amount,
                                pord.payment_method_id,
                                SUM(pord.total_amount) OVER() AS total_amount,
                                1 AS order_type
                            "))
                            ->where('awbt.order_id',$excel_orer_id)
                            ->where('awbt.airway_bill_number',$excel_airwaybill)
                            ->first();
                            // dd($data);
                            if($data) {
                                if($data->amount == $excel_amount) {
                                $data->amount_match = 1;
                                }else {
                                $data->amount_match = 0;
                                }
                                $data->ex_amount = number_format((float)$excel_amount, 2, '.', '');
                                $data->exchange_or = 0;
                                array_push($normal_data,$data);
                            }else{
                                array_push($missing_data,$order);
                            }
                        }

                    }
                } //end foreach
                $all_orders = array_merge($normal_data,$exchange_data);
                // echo "<pre>"; print_r($all_orders);
                $this->returnSheetPendingOrders($all_orders,$missing_data);
			} catch (\Exception $e) {
                // echo "<script>alert('".$e->getMessage()."')</script>";
				return array('success' => false, 'data' => array(), 'error' => array('message' => $e->getMessage()));
			}
		} else {
             // echo "<script>alert('".$validator->errors()->first()."')</script>";
			return array('success' => false, 'data' => array(), 'error' => array('message' => $validator->errors()->first()));
		}
  }

  protected function returnSheetPendingOrders($orders,$missing_data){
    $missing_content = "";
    // echo "<pre>"; print_r($missing_data); die;
    if( count($missing_data) > 0 ){
      foreach($missing_data as $missing_order){
        $missing_content .= "<tr style='color:red'><td>&nbsp;</td><td>".$missing_order['order']."</td><td>".$missing_order['awb']."</td><td>".$missing_order['aed']."</td></tr>";
      }
    }
    $form_data = "";
    $form = "";
    $ship_charges = ShippingProvidersModel::select('shipping_charges','name')->where('shipping_provider_id', $orders[0]->shipping_provider_id)->first();
    $chanrges = count($orders) * $ship_charges->shipping_charges;
    // $token = csrf_token();
    //$url = URL::to('/accounts/save/pending/receipts');
    //$content  = "<form  method='post' id='excel-form' action='$url'>";
    // $content .= "<input type='hidden' name='_token' value='$token'>";
    $content = "";
    $content .= $missing_content;
    $flag = true;
    $amount_total = 0;
	foreach($orders as $key=>$order){
      if( $order->oms_order_status == 3 ){
        $shippingCompanyClass = "\\App\\Platform\\ShippingProviders\\" . $ship_charges->name;
        $shipping = new $shippingCompanyClass();
        $awb_type = 0;
        if( $order->exchange_or == 1 ){
          $awb_type = 1;
        }
        $airway_bill_data = new \stdClass();
        $airway_bill_data->airway_bill_number = $order->airway_bill_number;
        $airway_bill_data->order_id = $order->order_id;
        $airway_bill_data->store = $order->store;
        $shipping->getOrderTrackingHistory($airway_bill_data,$awb_type);
      }
      $is_prepaid = '';
    //   if( $order->payment_code != "cod" && $order->payment_code != "cod_order_fee" && $order->payment_code != ""){
    //     $is_prepaid = "<b class='text-danger'>PP</b><br>";
    //   }
      if( $is_prepaid == "" ){
      $amount_total += $order->amount;
      }
      $am_class = '';
      $co_deliver = ($order->courier_delivered == 1) ? '<i style="color: #13a813; font-size:18px">Yes</i>' : '<i style="color: red; font-size:15px">No</i>';
      $oms_order_status = ($order->oms_order_status == 4) ? '<i style="color: #13a813; font-size:18px">Yes</i>' : '<i style="color: red; font-size:15px">No</i>';
      $payment_status = ($order->payment_status == 1) ? '<i style="color: #13a813; font-size:18px">Yes</i>' : '<i style="color: red; font-size:15px">No</i>';
      if($order->courier_delivered == 0 || $order->oms_order_status != 4 || $order->payment_status == 1) {
        $flag = false;
      }
      if($order->amount_match == 0) {
        $am_class = 'text-danger';
      }
      $display_orderId = ($order->exchange_or == 1) ? $order->order_id.'-1' : $order->order_id;
      $content .= "<input type='hidden' name='pending_order_ids[]' value='$order->airway_bill_number'>
      <input type='hidden' name='order_ids[$order->airway_bill_number]' value='$order->order_id'>
      <input type='hidden' name='order_type[$order->airway_bill_number]' value='$order->order_type'>
      <input type='hidden' name='courier_id' value='$order->shipping_provider_id'>
      <input type='hidden' name='amount[$order->airway_bill_number]' value='$order->amount'>
      <input type='hidden' name='payment_code[$order->airway_bill_number]' value='$order->payment_method_id'>
      <input type='hidden' name='store_id[$order->airway_bill_number]' value='$order->store'>";
        $content .= "<tr class='shipped-row' style='border-bottom: 1px solid lightgray !important;'>
            <td align='center'>".($key+1)."</td>
            <td align='center'>".$display_orderId."</td>
            <td align='center'>".$order->airway_bill_number."</td>
            <td align='center' class='$am_class'>$is_prepaid".number_format((float)$order->amount, 2, '.', '').'<br>'.$order->ex_amount."</td>
            <td align='center'>".$co_deliver."</td>
            <td align='center'>".$oms_order_status."</td>
            <td align='center'>".$payment_status."</td>
        </tr>";
    }
    $g_total = $amount_total-$chanrges;
    $content .= "<tr class='shipped-row'>
    <td align='right' colspan='6'> <strong>Total Amount:</strong></td>
    <td>$amount_total</td>
    </tr>";
    $content .= "<tr class='shipped-row'>
    <td align='right' colspan='6' align='right'> <strong>Courier Charges:".count($orders).'*'.$ship_charges->shipping_charges."</strong></td>
    <td>".$chanrges."</td>
    </tr>";
    $content .= "<tr class='shipped-row' style='border-bottom: 1px solid lightgray !important;'>
    <td align='right' colspan='6' align='right'> <strong>Grand Total:</strong></td>
    <td>".$g_total."</td>
    </tr>";
    $content .= "<tr class='shipped-row'>
        <td colspan='6' align='right'><strong>Enter Amount You Received:</strong></td>
        <td colspan='6' align='right'><input type='text' name='received_amount'></td>
        </tr>";
    $content .= "<tr class='shipped-row'>
        <td colspan='6' align='right'><strong>Balance:</strong></td>
        <td colspan='6' align='right'><input type='text' name='balance_amount' readonly></td>
        </tr>";
      $content .= '<input type="submit" class="btn btn-lg btn-success float-right save-courier-payment-popup active" name="save-courier-payment" value="RECEIVE">';
    //   $content .= '</form>';
      echo $content;
	}
}
