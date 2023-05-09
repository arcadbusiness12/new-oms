<?php

namespace App\Http\Controllers\performance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\OmsActivityLogModel;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\PaidAdsChatHistoryModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionScheduleSettingModel;
use App\Models\Oms\PromotionSchedulePaidAdsCampaignTemplateModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\PromotionProductPaidPostModel;
use App\Providers\Reson8SmsServiceProvider;
use App\Models\Oms\AdsTypeModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\DailyAdResult;
use App\Models\Oms\EmployeeCustomDutyFileModel;
use App\Models\Oms\EmployeeCustomeDutiesModel;
use App\Models\Oms\OmsUserGroupModel;
use App\Models\Oms\SmartLookModel;
use App\Models\OpenCart\Products\ProductsModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Storage;

class MarketingPerformanceController extends Controller
{
    const PER_PAGE = 20;
    const VIEW_DIR = 'employeePeerformance';
    
    

    public function getOutStockPaidAdsDetails($group) {
      $groupProducts = ProductGroupModel::with('producType','products')->find($group);
      $whereClause = [];
      $socials = SocialModel::where('status', 1)->get();
      $groupProducts->action = 1;
      $groupProducts->histories = [];
      return view(self::VIEW_DIR. '.marketting.scheduleGroupDetails')->with(compact('groupProducts', 'socials'));
    }

    public function savePaidAdChat(Request $request) {
      // dd($request->all());
      $post_data = $request->all();
      $today = date("Y-m-d");
      if(count($post_data) > 0) {
        if(isset($post_data['chat']) && isset($post_data['used_budget']))  {
          $this->validate($request, [
            'chat_entry_date' => 'required|date'
          ]);
          $chat_data = $post_data['chat'];
          $used_budget_data = $post_data['used_budget'];
          foreach($chat_data as $setting_id => $chat) {
            if($chat > 0) {
              $update_data = PromotionSchedulePaidAdsCampaignTemplateModel::where('id', $setting_id)->update(['budget_used' => $used_budget_data[$setting_id], 'result' => $chat]);
              $whereClause = ['setting_id' => $setting_id, 'date' => $request->chat_entry_date];
              $user_chat_data = [
                'promotion_schedule_setting_main_id' => $request->main_id,
                'setting_id' => $setting_id,
                'user_id' => $request->user_id,
                'budget_used' => $used_budget_data[$setting_id],
                'results' => $chat,
                'cost_per_result' => ($used_budget_data[$setting_id] && $chat) ? $used_budget_data[$setting_id]/$chat : 0,
                'date' => $request->chat_entry_date,
                'created_at' => date('Y-m-d')
              ];
              DailyAdResult::updateOrCreate($whereClause, $user_chat_data);
            }
          }
        }else {
          if(isset($request->schedule_chat)) {
            $date = $request->schedule_date;
            $action = 'schedule';
            $campaign_id = $request->campaign_next;
            $u_c_data  = ['budget_alloted'=>$request->schedule_total_budget_alloted,
            'budget_used'=>$request->schedule_total_budget_used,
            'user_id' => $request->user_id,
            'results'=>$request->schedule_total_chat_received,
            'cost_per_result_alloted'=>$request->schedule_total_cost_per_chat_alloted,
            'cost_per_result'=>$request->schedule_total_cost_per_chat
          ];
          }else {
            $date = $request->date;
            $action = 'current';
            $campaign_id = $request->campaign_current;
            $u_c_data  = ['budget_alloted'=>$request->total_budget_alloted,
            'budget_used'=>$request->total_budget_used,
            'user_id' => $request->user_id,
            'results'=>$request->total_chat_received,
            'cost_per_result_alloted'=>$request->total_cost_per_chat_alloted,
            'cost_per_result'=>$request->total_cost_per_chat
          ];
          }
          $u_c_where = ['date'=>$date,'promotion_schedule_setting_main_id'=>$request->main_id,'duration'=>$action,'campaign_id'=>$campaign_id];
          
          DailyAdResult::updateOrCreate($u_c_where,$u_c_data);
            
        }
        if($post_data['main_id']) {
          return Redirect()->route('performance.marketing.save.add.chat', [$request->action_status, $post_data['main_id']])->with("success","Chat updated Successfully.");
        }else {
          return Redirect()->route('performance.marketing.save.add.chat')->with("success","Chat updated Successfully.");
        }
      }
    }

    public function calculate_week_Days($today, $action) {

        $oneweekfromnow = strtotime("+1 month", strtotime($today));
        $onemontbackfromnow = strtotime("-1 month", strtotime($today));
        $oneweekfromnow = date('Y-m-d', $oneweekfromnow);
        $onemontbackfromnow = date('Y-m-d', $onemontbackfromnow);
        $today = new DateTime($today);
        $today_for_next = $today;
        $oneweekfromnow = new DateTime($oneweekfromnow);
        $onemontbackfromnow = new DateTime($onemontbackfromnow);
        $days = [];
        $pre_days = [];
        for($date = $today; $date < $oneweekfromnow; $date->modify('+1 day')) {
            $dates = [
                'display_date' => $date->format('D-d-F'),
                'hiddn_date'   => $date->format('Y-m-d')
            ];
            array_push($days, $dates);
        }
        // dd($today_for_next);
        // For previous dates 
        for($pre_date = $onemontbackfromnow; $pre_date < new DateTime(date('Y-m-d')); $pre_date->modify('+1 day')) {
            $dates = [
                'display_date' => $pre_date->format('D-d-F'),
                'hiddn_date'   => $pre_date->format('Y-m-d'),
            ];
            array_push($pre_days, $dates);
        }
        $marge_date_array = array_merge($pre_days, $days);
        if($action == 1) {
            
            return $days;
        }else {
            return $marge_date_array;
        }
    }

    public function CreateCampaign(Request $request) {
      $this->validate($request, [
        'campaign' => 'required',
        'user'     => 'required'
      ]);
      $template_ids = $request->setting_id;
      $exist = PaidAdsCampaign::where('campaign_name', trim($request->campaign))->count();
      if(!$template_ids) {
        return response()->json([
          'status' => false,
          'no_template' => true
        ]);
      }
      if($exist > 0) {
        return response()->json([
          'status' => false,
          'exist' => true,
          'campaign' => $request->campaign
        ]);
      }else {
        $templates = PromotionScheduleSettingModel::whereIn('id', $template_ids)->get();
        
        $campaign = new PaidAdsCampaign();
        $campaign->campaign_name = $request->campaign;
        $campaign->main_setting_id = $request->main_id;
        $campaign->user_id = $request->user;
        $campaign->created_at = date('Y-m-d H:i:s');
        if($campaign->save()) {
          foreach($templates as $template) {
            $campaignTemplate = new PromotionSchedulePaidAdsCampaignTemplateModel();
            $campaignTemplate->campaign_id = $campaign->id;
            $campaignTemplate->main_setting_id = $template->main_setting_id;
            $campaignTemplate->ad_set_name = $template->ad_set_name;
            $campaignTemplate->promotion_product_type_id = $template->promotion_product_type_id;
            $campaignTemplate->sub_category_id = $template->sub_category_id;
            $campaignTemplate->sub_category = $template->sub_category;
            $campaignTemplate->range = $template->range;
            $campaignTemplate->budget = $template->budget;
            $campaignTemplate->creative_type_id = $template->creative_type_id;
            $campaignTemplate->category = $template->category;
            $campaignTemplate->category_id = $template->category_id;
            $campaignTemplate->created_by = session('user_id');
            $campaignTemplate->created_at = date('Y-m-d H:i s');
            $campaignTemplate->save();
        }
        $mainSetting = PromotionScheduleSettingMainModel::find($request->main_setting_id);
        $mainSetting->user_id = $request->user;
        $mainSetting->update();
      }
    
        return response()->json([
          'status' => true
        ]);
      }
    }


    public function ActiveSinglePaidAd($main_setting, $setting, $duration, $post,$campaign) {
      $end = date('Y-m-d', strtotime('+15 days'));
      $current_flag = false;
      $paid_post = PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                            ->where('campaign_id', $campaign)
                                            ->where('setting_id', $setting)
                                            ->where('post_duration', $duration)
                                            ->update(['is_active_paid_ads' => 1,'date' => date('Y-m-d'),'last_date' => $end]);
      // CHECKING if recent active coming ads                                     
      if($duration == 2) {
        // checking for current ads are available or not and the coming ads are all active
        $next_ads = PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                              ->where('campaign_id', $campaign)
                                              ->where('post_duration', $duration)
                                              ->where('is_active_paid_ads', 0)
                                              ->count();
                                              
      $current_ads = PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                                ->where('post_duration', 1)
                                                ->where('campaign_id', '!=', $campaign)
                                                ->count();
        
        if($next_ads <= 0) {
          if($current_ads <= 0) {
            $current_flag = true;
            PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                      ->where('campaign_id', $campaign)
                                      ->where('post_duration', 2)
                                      ->update(['post_duration' => 1]);
            PaidAdsCampaign::where('main_setting_id', $main_setting)
                            ->where('status', 2)
                            ->update(['status' => 1,'start_date' => date('Y-m-d')]);
          }else {
            PromotionProductPaidPostModel::where('id', $post)->update(['is_active_paid_ads' => 0,'post_duration' => 2]);
            return response()->json([
              'status' => false,
              'errorm' => 'Please first stop current campaign'
            ]);
          }
          
        }
      }  
      if($paid_post) {
        return response()->json([
          'status' => true,
          'current_flag' => $current_flag
        ]);
      }else {
        return response()->json([
          'status' => false
        ]);
      }
    }

    public function stopSinglePaidAd($post, $main_setting, $template, $duration, $campaign) {
      $exist = true;
      $chat = false;
      $paid_post = PromotionProductPaidPostModel::where('id', $post)
                                                ->where('main_setting_id', $main_setting)
                                                ->where('post_duration', $duration)
                                                ->update(['posting' => 0,'last_date' => date('Y-m-d')]);
      // checking current active ads
      $ads_exist = PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                                ->where('campaign_id', $campaign)
                                                ->where('post_duration', $duration)
                                                ->where('posting', 1)
                                                ->count();
      if($ads_exist > 0 && $paid_post) {
        $exist = true;
      }else {
        $exist = false;
        PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                      ->where('campaign_id', $campaign)
                                      ->where('post_duration', 1)
                                      ->where('is_active_paid_ads', 0)
                                      ->delete(); //Delete In-Active Ads
        // Move All current Ads to history
        PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                      ->where('post_duration', $duration)
                                      ->update(['post_duration' => 0]);

        PromotionProductPaidPostModel::where('main_setting_id', $main_setting)
                                      ->where('post_duration', 2)
                                      ->update(['post_duration' => 1]);
        
        PaidAdsCampaign::where('id', $campaign)->where('status', 1)->update(['status' => 0]);

        PaidAdsCampaign::where('main_setting_id', $main_setting)->where('status', 2)->update(['status' => 1,'start_date' => date('Y-m-d')]);
      }
      return response()->json([
        'status' => true,
        'exist'  => $exist,
        'chat'  => false
      ]);
    }

    function saveRemark(Request $request) {
      // dd($request->all());
      if($request->action == 'post') {
        $post = PromotionProductPaidPostModel::find($request->post);
      }else {
        $post = PromotionSchedulePaidAdsCampaignTemplateModel::find($request->post);
      }
      $post->remark = $request->rmark;
    if($post->update()) {
      return response()->json([
        'status' => true
      ]);
    }
  }

  public function changePaidAdsSettingStatus(PromotionSchedulePaidAdsCampaignTemplateModel $setting, $status) {
    // dd($setting);
    if($setting) {
      $setting->is_active = $status;
      $setting->update();
      return response()->json([
        'status' => true
      ]);
    }
  }

  public function smartLook() {
    $smart_looks = SmartLookModel::with('user')->orderBy('id', 'DESC')->paginate(20);
    $old_input = [];
    // foreach($smart_looks as $s) {
    //   echo $s->is_emergency. "<br>";
    // }
    // dd($smart_looks);
    return view(SELF::VIEW_DIR. '.marketting.smart_look')->with(compact('smart_looks','old_input'));
  }

  public function smartLookForm() {
    $user_group = OmsUserGroupModel::all();
    $dutyLists = []; 
    return view(SELF::VIEW_DIR.'.marketting.smart_look_form')->with(compact('user_group','dutyLists'));
  }
  
  public function saveSmartLookCustomDuty(Request $request) {
    $this->validate($request, [
      'smart_look_title' => 'required',
      'link' => 'required',
   ]);
   if($request->assign_to_developer) {
    $this->validate($request, [
      'user' => 'required|numeric',
      // 'duty' => 'required|numeric',
      'title' => 'required',
      // 'quantity' => 'required',
      'date_from' => 'required|date',
      'date_to' => 'required|date',
      'date_event' => 'required|date',
   ]);
   }
   if($request->user_group) {
     $ug = $request->user_group;
   }else {
    if($request->action == 'web') {
      $ug = 18;
    }else if($request->action == 'app') {
      $ug = 19;
    }else {
      $g = OmsUserModel::select('user_group_id')->find(session('user_id'));
      $ug = $g->user_group_id;
    }
   }
   $smart_look = new SmartLookModel();
   $smart_look->title = $request->smart_look_title;
   $smart_look->description = $request->smart_look_description;
   $smart_look->link = $request->link;
   $smart_look->user_group_id = $ug;
   $smart_look->user_id = session('user_id');
   $smart_look->create_at = date('Y-m-d');
   if($request->date_event) {
    $smart_look->event_date = $request->date_event;
   }
   $smart_look->is_emergency = $request->is_emergency ? 1 : 0;
   if($request->assign_to_developer) {
      $smart_look->assigned_to = $request->user;
      $smart_look->progress = 0;
   }
   if($smart_look->save() && $request->assign_to_developer) {
    $customDuty = ($request->id) ? EmployeeCustomeDutiesModel::find($request->id) : new EmployeeCustomeDutiesModel();
     $customDuty->user_id = $request->user;
     $customDuty->user_group_id = $request->user_group;
     $customDuty->duty_list_id = $request->duty ? $request->duty : 0;
     $customDuty->title = $request->title;
    //  $customDuty->quantity = $request->quantity;
     $customDuty->description = $request->description;
     $customDuty->start_date = $request->date_from;
     $customDuty->end_date = $request->date_to;
     $customDuty->event_date = $request->date_event;
     $customDuty->is_close = $request->is_close;
     $customDuty->is_regular = $request->irregular;
     $customDuty->smart_look_id = $smart_look->id;
     $customDuty->assigned_by = session('user_id');
     $customDuty->is_emergency = $request->is_emergency ? 1 : 0;
     
     if($customDuty->save()) {
        if($request->hasFile('file')) {
           $files = $request->file;
           // EmployeeCustomDutyFileModel::where('custom_duty_id',$customDuty->id)->delete();
           $i = 0;
            foreach($files as $file) {
                 // dd($file->originalName());
                 $filee = new EmployeeCustomDutyFileModel();
                 $filePath = Storage::putFile('public/uploads/custom_duties_file', $file);
                 $filee->custom_duty_id = $customDuty->id;
                 $filee->file = $filePath;
                 $filee->extension = $file->extension();
                 $filee->save();
                 $i++;
            }
        }
        if(!$request->id) {
           $enttity = [
              'id' => $customDuty->id,
              'title' => $customDuty->title,
              'user' => $customDuty->user_id,
              'date' => $customDuty->created_at,
          ];
        }
        
     }
   }
     if($request->user_type) {
       if($request->action == 'web') {
        return redirect()->route('developerweb.smart.look', [$request->user_type, $request->action])->with('message', 'Smart look created successfully.');
       }else {
        return redirect()->route('employee-performance.app.developer.smart.look', [$request->user_type, $request->action])->with('message', 'Smart look created successfully.');
       }
      
     }else {
      return redirect()->route('employee-performance.smart.look.form')->with('message', 'Duty assigned successfully.');
     }
  }

  public function productListing($action = null) {
    $whereClause = [];
    $active_tab = '';
    if(!$action || $action == 'pending' || $action == '') {
      array_push($whereClause, ['oms_inventory_product.product_list', 0]);
    }
    if($action && $action == 'image') {
      array_push($whereClause, ['oms_inventory_product.product_list', 1]);
      array_push($whereClause, ['oms_inventory_product.product_image', 0]);
    }
    if($action && $action == 'enable') {
      array_push($whereClause, ['oms_inventory_product.product_list', 1]);
      array_push($whereClause, ['oms_inventory_product.product_image', 1]);
      array_push($whereClause, ['oms_inventory_product.enable_product', 0]);
    }
    if(!$action) {
      $active_tab = 'current';
    }
    $new_arrivals = OmsInventoryProductModel::select('oms_inventory_product.*','pop.model','pop.order_id','po.order_status_id','po.order_id','po.total','ipg.id as group_id','ipg.name as group_name','pp.id as photo_shoot_id','pp.photo_shoot','listing_checked','upload_image_checked')
              ->leftJoin('oms_purchase_order_product as pop', 'pop.model', '=', 'oms_inventory_product.sku')
              ->leftJoin('oms_purchase_order as po', 'po.order_id', '=', 'pop.order_id')
              ->leftJoin('oms_inventory_product_group as ipg', 'ipg.id', '=', 'oms_inventory_product.group_id')
              ->leftJoin('oms_product_photography as pp', 'pp.product_group_id', '=', 'ipg.id')
              ->where('po.order_status_id', '>=', 2)
              ->where('oms_inventory_product.listing', 0)
              ->where($whereClause)
              ->orderBy('oms_inventory_product.update_at','DESC')
              ->OrderBy('oms_inventory_product.group_id')
              ->get();
    $new_arrivals_data = [];
    foreach($new_arrivals as $val) {
      $new_arrivals_data[$val->confirm_data][] = [];
      $ba_exist = ProductsModel::select('sku','price')->where('sku', 'REGEXP', $val->group_name)->first();
      // $df_exist = DFProductsModel::select('sku','price')->where('sku', 'REGEXP', $val->group_name)->first();
    }
  }
}
