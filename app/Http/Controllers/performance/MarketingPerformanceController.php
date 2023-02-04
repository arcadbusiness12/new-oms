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
use Carbon\Carbon;
use DateTime;

class MarketingPerformanceController extends Controller
{
    const PER_PAGE = 20;
    const VIEW_DIR = 'employeePeerformance';
    
    public function saveChat($action, $id = null) {
      // dd($action);
        $post_type = 2;
        $today = date('Y-m-d');
        $all_lists = PromotionScheduleSettingMainModel::with('user')->where('is_deleted',0)->where('posting_type',2)->get();
        
        if( $id == "" && $all_lists->count() > 0 ){
        $id = $all_lists[0]->id;
        }
        $next_date = strtotime("+7 days", strtotime($today));
        $next_seven_day = date('Y-m-d',$next_date);
        $templates = PromotionScheduleSettingMainModel::with(['chatResults'=>function($query) use ($today){
        $query->orderBy('date','DESC')->first();
        },'schedulechatResults' => function($q) {
        $q->orderBy('date','DESC')->first();
        }])->find($id);
        $store = $templates->store_id;
        if( $templates ){
        $template_socials = explode(',', $templates->social_ids);
        }else{
        $template_socials = "";
        }
        $product_pro_posts = [];
        $campaign_current = PaidAdsCampaign::with(['chatResults' => function($q) {
          $q->orderBy('date','DESC')->first();
          },'chatResults.user'])->where('main_setting_id', $id)->where('status', 1)->first();
          if($campaign_current) {
            $product_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with(['type','subCategory','creativeType','adResultHistories'])->where('campaign_id', $campaign_current->id)->where('is_deleted', 0)->orderBy('category')->get();
          }
    
        $product_next_pro_posts = [];
        $campaign_next = PaidAdsCampaign::with(['schedulechatResults' => function($q) {
          $q->orderBy('date','DESC')->first();
          }])->where('main_setting_id', $id)->where('status', 2)->first();
          if($campaign_next) {
            $product_next_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with(['type','subCategory','creativeType','adResultHistories'])->where('campaign_id', $campaign_next->id)->where('is_deleted', 0)->orderBy('category')->get();
          }
          
        $socials = SocialModel::where('status', 1)->get();
        $pro_posts = PromotionProductPaidPostModel::with(['group.products.ProductsSizes','chatHistories'])->where('store_id', $store)->where('main_setting_id',$id)->where('posting_type', $post_type)->where('post_duration', 1)->groupBy('group_id')->get();
         
        $next_post_data = PromotionProductPaidPostModel::with('group','chatHistories')->where('store_id', $store)->where('main_setting_id',$id)->where('posting_type', $post_type)->where('post_duration', 2)->get();
          // dd($next_post_data->toArray());
        foreach($pro_posts as $post) {
            $post['stock'] = 1; 
            $out_products = [];
            foreach($post->group->products as $product) {
              if($product->status == 1){
                foreach($product->ProductsSizes as $size) {
                  if($size->available_quantity == 0) {
                    $post['stock'] = 0;
                  }
                }
              }
            }
            
          } 
          $days = $this->calculate_week_Days(date('Y-m-d'), 2);
          
          $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
          $ad_types = AdsTypeModel::with(['paidAdsSettings'=>function($query){
          $query->where('is_deleted',0);
          }])->whereHas('paidAdsSettings')->get();

          $users = OmsUserModel::whereIn('user_group_id', [12,11,8])->where('status', 1)->get();

          $current_campaign_history = PaidAdsCampaign::where('main_setting_id', $id)->where('status', 0)->get();
        //   dd($product_pro_posts);
          return view(self::VIEW_DIR.'.marketting.paidAdsDetails',compact('product_pro_posts', 'product_next_pro_posts', 'pro_posts', 'days', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day','all_lists','next_post_data','ad_types','campaign_current','campaign_next','users','current_campaign_history','action'));
    }

    public function getOutStockPaidAdsDetails($group) {
      dd($group);
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
}
