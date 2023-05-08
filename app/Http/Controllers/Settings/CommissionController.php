<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\PublicHoliday;
use App\Models\Oms\CommissionSetting;
use App\Models\Oms\ShippingProvidersModel;
use App\Models\Oms\DailyAdResult;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\OpenCart\Orders\OrderStatusModel;
use App\Models\OpenCart\Orders\OrderedProductModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DressFairProductsModel;
use App\Models\DressFairOpenCart\Orders\OrdersModel as DressFairOrdersModel;
use App\Models\DressFairOpenCart\Orders\OrderedProductModel as DressFairOrderProductsModel;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;

class CommissionController extends Controller
{
  const VIEW_DIR = 'settings';
  const PER_PAGE = 20;
  private $DB_BAOPENCART_DATABASE = '';
  private $DB_DFOPENCART_DATABASE = '';

  function __construct(){
    $this->DB_BAOPENCART_DATABASE = env('DB_BAOPENCART_DATABASE');
    $this->DB_DFOPENCART_DATABASE = env('DB_DFOPENCART_DATABASE');
  }
  public function index(){
    
  }
  public function sale(Request $request){
    $staffs = OmsUserModel::select('user_id','username','firstname','lastname')->whereIn('user_group_id',[12])->where('status',1)->get();

    $data = DB::table("oms_place_order AS opo")
    ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
      $join->on("baord.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("1"));
    })
    ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
      $join->on("dford.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("2"));
    })
    ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
    ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
    ->leftjoin("duty_assigned_users AS dau",function($join){
      $join->on("dau.user_id","=","sp.user_id");
      $join->on("dau.activity_id","=",DB::raw("2"));
    })
    ->select(DB::raw("sp.firstname,sp.lastname,COUNT(*) AS total_order,dau.quantity AS daily_order_target,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_order,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      SUM(CASE WHEN ord.oms_order_status = 6 THEN 1 ELSE 0 END) AS return_order,
      SUM(CASE WHEN baord.total >= 300 AND ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS BA300Amount,
      SUM(CASE WHEN baord.total >= 300 AND ord.oms_order_status = 4 THEN baord.total ELSE 0 END) AS BA300AmountTotal,
      SUM(CASE WHEN dford.total >= 300 AND ord.oms_order_status = 4  THEN 1 ELSE 0 END) AS DF300Amount,
      SUM(CASE WHEN dford.total >= 300 AND ord.oms_order_status = 4  THEN dford.total ELSE 0 END) AS DF300AmountTotal
    "))
    ->where('sp.status',1)
    ->where('sp.user_group_id',12)
    ->where(function ($query) {
      $query->where('ord.oms_order_status','!=',5)
          ->orWhereNull('ord.oms_order_status');
    });
    $daysWithoutFriday = 0;
    if($request->date_from != "" && $request->date_to != "" ){
      $data = $data->whereDate('opo.created_at','>=',$request->date_from)
              ->whereDate('opo.created_at','<=',$request->date_to);
      $daysWithoutFriday = $this->daysWithoutHolidays($request->date_from,$request->date_to);
    }
    if($request->user != ""){
      $data = $data->whereDate('opo.user_id','=',$request->user);
    }
    $data = $data->groupBy("opo.user_id")->get();
    // dd($data->toArray());
    $old_input = $request->all();
    $comm_settings = CommissionSetting::where('id',1)->first();
    // dd($comm_settings->toArray());
    return view(self::VIEW_DIR.".sale_commission",compact('data','staffs','old_input','comm_settings','daysWithoutFriday'));
  }
  private function returnDatesFromDuration($filter_by_duration){
    $date_from = "";
    $date_to   = "";
    if( $filter_by_duration == "today" ){
      $date_from = date("Y-m-d");
      $date_to   = date("Y-m-d");
    }elseif( $filter_by_duration == "yesterday" ){
      $date_from = date("Y-m-d",strtotime('-1 days'));
      $date_to   = date("Y-m-d",strtotime('-1 days'));
    }elseif( $filter_by_duration == "thisweek" ){
      $date_from = date("Y-m-d",strtotime('last Monday'));
      $date_to   = date("Y-m-d");
    }elseif( $filter_by_duration == "lastweek" ){
      $date_from = $date_from = date("Y-m-d",strtotime('last Monday -7 day'));
      $date_to   = date("Y-m-d",strtotime('last Sunday'));
    }elseif( $filter_by_duration == "thismonth" ){
      $date_from = date('Y-m-01');
      $date_to   = date('Y-m-t');
    }elseif( $filter_by_duration == "lastmonth" ){
      $date_from = date('Y-m-d',strtotime("first day of last month"));
      $date_to   = date('Y-m-d',strtotime("last day of last month"));
    }
    return [$date_from,$date_to];
  }
  public function saleOnTotalDeliveredAmount(Request $request){
    // $data = PublicHoliday::select(DB::raw("SUM( CASE WHEN type = 1 THEN 1 WHEN type=2 THEN .5 ELSE 0 END) AS tot_holdays"))->first();
    // dd($data->tot_holdays);
    // echo date("Y-m-d",strtotime("last Monday -7")); die;
    $filter_by_duration = $request->by_duration;
    if( $filter_by_duration != "custom" ){
      $dates = $this->returnDatesFromDuration($filter_by_duration);
    }else{
      $dates = "";
    }
    // echo "<pre>"; print_r($dates); die;
    if( is_array($dates) && $dates[0] != "" && $dates[1] != "" ){
      $date_from = $dates[0];
      $date_to   = $dates[1];
    }else{
      $date_from = $request->date_from;
      $date_to   = $request->date_to;
    }
    $data = DB::table("oms_place_order AS opo")
    ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
      $join->on("baord.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("1"));
    })
    ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
      $join->on("dford.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("2"));
    })
    ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
    ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
    ->leftjoin("duty_assigned_users AS dau",function($join){
      $join->on("dau.user_id","=","sp.user_id");
      $join->on("dau.activity_id","=",DB::raw("2"));
    })
    ->select(DB::raw("sp.user_id,sp.firstname,sp.lastname,sp.commission_on_delivered_amount,COUNT(*) AS total_order,dau.quantity AS daily_order_target,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 0 AND baord.order_status_id = 7 THEN 1 ELSE 0 END) AS ba_site_cancel_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 0 AND dford.order_status_id = 7 THEN 1 ELSE 0 END) AS df_site_cancel_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 0 AND opo.user_id = 76 THEN 1 ELSE 0 END) AS ba_site_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 0 AND opo.user_id = 76 AND baord.utm_medium = 'social' THEN 1 ELSE 0 END) AS ba_site_fbAd_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 1 THEN 1 ELSE 0 END) AS ba_app_android_orders,
      SUM(CASE WHEN opo.user_id != 76 AND opo.store=1 THEN 1 ELSE 0 END) AS ba_oms_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 1 AND baord.app_order_source = 0 THEN 1 ELSE 0 END) AS ba_app_android_direct_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 1 AND baord.app_order_source = 1 THEN 1 ELSE 0 END) AS ba_app_android_fbAd_orders,
      SUM(CASE WHEN baord.mobile_app_sale = 1 AND baord.app_order_source = 2 THEN 1 ELSE 0 END) AS ba_app_android_pushNt_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 0 AND opo.user_id = 76  THEN 1 ELSE 0 END) AS df_site_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 0 AND opo.user_id = 76 AND dford.utm_medium = 'social' THEN 1 ELSE 0 END) AS df_site_fbAd_orders,
      SUM(CASE WHEN opo.user_id != 76 AND opo.store=2 THEN 1 ELSE 0 END) AS df_oms_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 1 THEN 1 ELSE 0 END) AS df_app_android_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 1 AND dford.app_order_source = 0 THEN 1 ELSE 0 END) AS df_app_android_direct_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 1 AND dford.app_order_source = 1 THEN 1 ELSE 0 END) AS df_app_android_fbAd_orders,
      SUM(CASE WHEN dford.mobile_app_sale = 1 AND dford.app_order_source = 2 THEN 1 ELSE 0 END) AS df_app_android_pushNt_orders,
      SUM(baord.total) AS BAAmountTotal,
      SUM(dford.total) AS DFAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN baord.total ELSE 0 END) AS BADeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN dford.total ELSE 0 END) AS DFDeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN baord.total ELSE 0 END) AS BAShippedAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN dford.total ELSE 0 END) AS DFShippedAmountTotal
    "))
    // ->where('sp.status',1)
    // ->where('sp.user_group_id',12)
    ->where(function ($query) {
      $query->where('ord.oms_order_status','!=',5)
          ->orWhereNull('ord.oms_order_status');
    })
    ->where('sp.user_group_id','!=',20);
    $daysWithoutHoliday = 0;
    $all_days = 0;
    if( $date_from != "" && $date_to != "" ){
      $data = $data->whereDate('opo.created_at','>=',$date_from)
              ->whereDate('opo.created_at','<=',$date_to);
      $daysWithoutHoliday = $this->daysWithoutHolidays($date_from,$date_to);
      $all_days = $this->daysFromDates($date_from,$date_to);
    }
    if($request->user != ""){
      $data = $data->where('opo.user_id','=',$request->user);
    }
    if( session('user_group_id') == 12 ){
      $data = $data->where('opo.user_id','=',session('user_id'));
    }
    $data = $data->groupBy("opo.user_id");
    $data = $data->get();
    //assigning chats
    if( $data && $date_from != "" AND $date_to ){
      foreach($data as $key => $row){
        $total_chat_data = DailyAdResult::with('mainSetting')
        ->select(DB::RAW("promotion_schedule_setting_main_id,SUM(results) AS total_chats,SUM(budget_used) AS total_budget_used,
        SUM(CASE WHEN opsm.store_id = 1 THEN budget_used ELSE 0 END) AS ba_total_budget_used,
        SUM(CASE WHEN opsm.store_id = 2 THEN budget_used ELSE 0 END) AS df_total_budget_used"))
        ->leftJoin("oms_promotion_schedule_setting_main AS opsm","opsm.id","=","daily_ad_results.promotion_schedule_setting_main_id")
        ->whereDate('date','>=',$date_from)->where('daily_ad_results.user_id',$row->user_id)->whereDate('date','<=',$date_to)
        ->groupBy('daily_ad_results.user_id')->first();
        if($total_chat_data){
          $row->chat_details = $total_chat_data;
        }
        // echo "<pre>"; print_r($total_chat_data->toArray());
      }
    }
    // dd($data->toArray());
    $old_input = $request->all();
    $comm_settings = CommissionSetting::where('id',1)->first();
    $staffs = OmsUserModel::select('user_id','username','firstname','lastname')->whereIn('user_group_id',[12])->where('status',1);
    if(session('user_group_id') ==12){
      $staffs = $staffs->where("user_id",session('user_id'));
    }
    $staffs = $staffs->get();
    // dd($data->toArray());
    // dd($comm_settings->toArray());
    return view(self::VIEW_DIR.".sale_commission_on_total_delivered_amount",compact('data','staffs','old_input','comm_settings','daysWithoutHoliday','all_days'));
  }

  public function chatSaleOrderReport(Request $request) {
    // dd($request->all());
    $whereCluase = [];
    $flag = false;
    $campaignns = [];
    $selectedField = [];
    if($request->campaign) {
      // $whereCluase[] = array('id', $request->campaign);
      array_push($selectedField, $request->campaign);
      $flag = true;
    }
    if($request->user) {
      // $whereCluase[] = array('user_id', $request->user);
      $user = OmsUserModel::with('campaigns')->find($request->user);
      $campaign_ids = $user->campaigns->pluck('id')->toArray();
      foreach($campaign_ids as $campaign) {
        array_push($selectedField, $campaign);
      }
      $flag = true;
    }
    // $whereCluase[] = array('status', 1);
    // dd($whereCluase);
    $filter_by_duration = $request->by_duration;
    if( $filter_by_duration != "custom" ){
      $dates = $this->returnDatesFromDuration($filter_by_duration);
    }else{
      $dates = "";
    }
    $date_from = '';
    $date_to = '';
    if( is_array($dates) && $dates[0] != "" && $dates[1] != "" ){
      $date_from = $dates[0];
      $date_to   = $dates[1];
    }else{
      $date_from = ($request->date_from) ? $request->date_from : '';
      $date_to   = ($request->date_to) ? $request->date_to : '';
    }
    // dd($selectedField);
    if($flag) {
      $campaignns = PaidAdsCampaign::with(['users','paidAds' => function($q) {
        $q->where('posting', 1)->where('is_active_paid_ads', 1)
          ->groupBy('group_id');
      },'paidAds.setting','paidAds.chats' => function($q1) use($date_from,$date_to) {
        $q1->whereDate('date', '>=', $date_from);
        $q1->whereDate('date', '<=', $date_to);
      },'mainSetting'])->whereIn('id', $selectedField)->where(function($q) {
        $q->where('status', 1)->orWhere('status', 2);
      })->get();
    }
    // dd($campaignns);
    $users = [];
    $products = [];
    $old_input = $request->all();
    $users = OmsUserModel::where('user_group_id', 12)->where('status', 1)->get();
    if(count($campaignns) > 0) {
      foreach($campaignns as $campaign) {
        $userIds = $campaign->users->pluck('user_id')->toArray();
        // dd($users);
        $products = [];
        if(count($campaign->paidAds) > 0) {
            foreach($campaign->paidAds as $paidAds) {
              $totalQuantities = 0;
              $omsProducts = OmsInventoryProductModel::where('group_id', $paidAds->group_id)->get();
              foreach($omsProducts as $omsProduct) {
                if($campaign->mainSetting->store_id == 1) {
                  $product = ProductsModel::where('sku', $omsProduct->sku)->first();
                  // dd($product->model);
                  $orderProducts = OrderedProductModel::whereHas('order', function($q) use($date_from,$date_to,$userIds) {
                    if($date_from != '' && $date_to != '') {
                      $q->whereDate('date_added','>=',$date_from)
                      ->whereDate('date_added','<=',$date_to)
                      ->whereIn('order_id_user', $userIds);
                    }
                    
                  })->where('model', 'LIKE','%'.$omsProduct->sku)->get();
                }else {
                  $dfproduct = DressFairProductsModel::where('sku', $omsProduct->sku)->first();
                  // echo $dfproduct->model;
                  $orderProducts = DressFairOrderProductsModel::with('order')->whereHas('order', function($q) use($date_from,$date_to,$userIds) {
                    // if($date_from != '' && $date_to != '') {
                      $q->whereDate('date_added','>=',$date_from)
                      ->whereDate('date_added','<=',$date_to)
                      ->whereIn('order_id_user', $userIds);
                    // }
                    // $dfproduct->model
                  })->where('model', 'LIKE','%'.$omsProduct->sku)->get();
                }
                
                if(count($orderProducts) > 0) {
                  
                  // dd($products);
                  foreach($orderProducts as $orderProduct) {
                    $totalQuantities += $orderProduct->quantity;
                  }
                  // $totalQuantities += array_sum(array_column($orderProducts->toArray(), 'quantity'));
      
                }
                
              }
              $paidAds['totalSoldQuantities'] = $totalQuantities;
              // dd($paidAds);
              $chatTotals = array_sum(array_column($paidAds->chats->toArray(), 'daily_chat'));
              $paidAds['chatTotals'] = $chatTotals;
            }
          
        }
        // dd($products);
      }
      
      
    }
    
    $campaigns = PaidAdsCampaign::whereIn('status', [1,2])->get();
    return view(self::VIEW_DIR.".ad_chat_order_summary")->with(compact('campaignns','old_input','campaigns','users'));
    
  }
  
  public function courierSummary(Request $request){
    // $data = PublicHoliday::select(DB::raw("SUM( CASE WHEN type = 1 THEN 1 WHEN type=2 THEN .5 ELSE 0 END) AS tot_holdays"))->first();
    // dd($data->tot_holdays);
    // echo date("Y-m-d",strtotime("last Monday -7")); die;
    // dd("Ok");
    $filter_by_duration = $request->by_duration;
    $search_by_courier  = $request->search_by_courier;
    if( $filter_by_duration != "custom" ){
      $dates = $this->returnDatesFromDuration($filter_by_duration);
    }else{
      $dates = "";
    }
    // echo "<pre>"; print_r($dates); die;
    if( is_array($dates) && $dates[0] != "" && $dates[1] != "" ){
      $date_from = $dates[0];
      $date_to   = $dates[1];
    }else{
      $date_from = $request->date_from;
      $date_to   = $request->date_to;
    }
    $data = DB::table("oms_place_order AS opo")
    ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
      $join->on("baord.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("1"));
    })
    ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
      $join->on("dford.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("2"));
    })
    ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
    ->join(DB::raw("(SELECT * FROM `airwaybill_tracking` WHERE tracking_id IN( SELECT MAX(`tracking_id`) FROM airwaybill_tracking GROUP BY order_id)) AS awbt"),function($join){
              $join->on('awbt.order_id','=','ord.order_id');
              $join->on('awbt.shipping_provider_id','=','ord.last_shipped_with_provider');
             })
    // ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
    ->join("shipping_providers AS sp","sp.shipping_provider_id","=","ord.last_shipped_with_provider")
    ->select(DB::raw("sp.shipping_provider_id,sp.name,COUNT(*) AS total_order,
      (SELECT COUNT(*) FROM oms_exchange_orders WHERE created_at >= '".$date_from."' AND created_at <= '".$date_to."' AND last_shipped_with_provider = ord.last_shipped_with_provider) AS tot_exchange,
      (SELECT COUNT(*) FROM oms_exchange_orders WHERE created_at >= '".$date_from."' AND created_at <= '".$date_to."' AND oms_order_status = 3 AND last_shipped_with_provider = ord.last_shipped_with_provider) AS tot_exchange_shipped,
      (SELECT COUNT(*) FROM oms_exchange_orders WHERE created_at >= '".$date_from."' AND created_at <= '".$date_to."' AND oms_order_status = 4 AND last_shipped_with_provider = ord.last_shipped_with_provider) AS tot_exchange_delivered,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_orders,
      SUM(CASE WHEN ord.oms_order_status = 3 AND receive_back = 1 THEN 1 ELSE 0 END) AS receive_back_orders,
      SUM(CASE WHEN ord.reship = 0 THEN 1 ELSE 0 END) AS normal_orders,
      SUM(CASE WHEN ord.reship = 1 THEN 1 ELSE 0 END) AS reship_orders,
      SUM(CASE WHEN ord.oms_order_status = 6 THEN 1 ELSE 0 END) AS returned_orders,
      SUM(baord.total) AS BAAmountTotal,
      SUM(dford.total) AS DFAmountTotal,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN baord.total ELSE 0 END) AS BADeliveredAmountTotal,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN dford.total ELSE 0 END) AS DFDeliveredAmountTotal,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN baord.total ELSE 0 END) AS BAShippedAmountTotal,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN dford.total ELSE 0 END) AS DFShippedAmountTotal
    "))
    // ->where('sp.status',1)
    // ->where('sp.user_group_id',12)
    ->where(function ($query) {
      $query->where('ord.oms_order_status','!=',5)
          ->orWhereNull('ord.oms_order_status');
    });
    $daysWithoutHoliday = 0;
    $all_days = 0;
    if( $date_from != "" && $date_to != "" ){
      $data = $data->whereDate('awbt.created_at','>=',$date_from)
              ->whereDate('awbt.created_at','<=',$date_to);
    }
    if( $search_by_courier ){
      $data = $data->where('awbt.shipping_provider_id',$search_by_courier);
    }
    // $data = $data->groupBy("opo.user_id");
    $data = $data->groupBy("ord.last_shipped_with_provider");
    // dd("Ok");
    $data = $data->get();
    
    // dd($data->toArray());
    $old_input = $request->all();
    $couriers = ShippingProvidersModel::where('is_active',1)->get();
    // dd($couriers->toArray());
    // dd($data->toArray());
    // dd($comm_settings->toArray());
    return view(self::VIEW_DIR.".courier_summary",compact('data','old_input','couriers'));
  }
  private function daysFromDates($date1,$date2){
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    $days = $interval->format('%a');//now do whatever you like with $days
    return $days;
  }
  public function saleOnTotalDeliveredAmountAjax(Request $request){
    $data = DB::table("oms_place_order AS opo")
    ->leftjoin($this->DB_BAOPENCART_DATABASE.".oc_order AS baord",function($join){
      $join->on("baord.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("1"));
    })
    ->leftjoin($this->DB_DFOPENCART_DATABASE.".oc_order AS dford",function($join){
      $join->on("dford.order_id","=","opo.order_id");
      $join->on("opo.store","=",DB::raw("2"));
    })
    ->leftjoin("oms_orders AS ord","ord.order_id","=","opo.order_id")
    ->join("oms_user AS sp","sp.user_id","=","opo.user_id")
    ->leftjoin("duty_assigned_users AS dau",function($join){
      $join->on("dau.user_id","=","sp.user_id");
      $join->on("dau.activity_id","=",DB::raw("2"));
    })
    ->select(DB::raw("sp.user_id,sp.firstname,sp.lastname,sp.commission_on_delivered_amount,COUNT(*) AS total_order,dau.quantity AS daily_order_target,
      SUM(CASE WHEN ord.oms_order_status = 4 THEN 1 ELSE 0 END) AS delivered_order,
      SUM(CASE WHEN ord.oms_order_status = 3 THEN 1 ELSE 0 END) AS shipped_orders,
      SUM(baord.total) AS BAAmountTotal,
      SUM(dford.total) AS DFAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN baord.total ELSE 0 END) AS BADeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 4  THEN dford.total ELSE 0 END) AS DFDeliveredAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN baord.total ELSE 0 END) AS BAShippedAmountTotal,
      SUM(CASE WHEN  ord.oms_order_status = 3  THEN dford.total ELSE 0 END) AS DFShippedAmountTotal
    "))
    ->where('sp.status',1)
    // ->where('sp.user_group_id',12)
    ->where(function ($query) {
      $query->where('ord.oms_order_status','!=',5)
          ->orWhereNull('ord.oms_order_status');
    });
    $daysWithoutHoliday = 0;
    $first_day_this_month = date('Y-m-01'); 
    $last_day_this_month  = date('Y-m-t');
    //
    // $first_day_this_month = "2021-02-01"; 
    // $last_day_this_month  = '2022-02-28';
    $data = $data->whereDate('opo.created_at','>=',$first_day_this_month)
              ->whereDate('opo.created_at','<=',$last_day_this_month);
    $daysWithoutHoliday = $this->daysWithoutHolidays($first_day_this_month,$last_day_this_month);
    $data = $data->where('opo.user_id','=',session('user_id'));
    $row = $data->groupBy("opo.user_id")->first();
    $comm_settings = CommissionSetting::where('id',1)->first();
    return view(self::VIEW_DIR.".sale_commission_on_total_delivered_amount_ajax",compact('row','daysWithoutHoliday','comm_settings'));

  }
  private  function daysWithoutHolidays($date1,$date2){
    $start = new DateTime($date1);
    $end = new DateTime($date2);
    // otherwise the  end date is excluded (bug?)
    $end->modify('+1 day');

    $interval = $end->diff($start);

    // total days
    $days = $interval->days;

    // create an iterateable period of date (P1D equates to 1 day)
    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    // best stored as array, so you can add more than one
    // $holidays = array('2012-09-07');
    $holidays = [];
    // if( $data && count($data) > 0 ){
    //   //dd($data->toArray());
    //   $holidays = $data->toArray();
    // }

    foreach($period as $dt) {
        $curr = $dt->format('D');

        // substract if Saturday or Sunday
        if ($curr == 'Sun') {
            $days--;
        }

        // (optional) for the updated question
        elseif (in_array($dt->format('Y-m-d'), $holidays)) {
            $days--;
        }
    }


    return $days;
  }
}
