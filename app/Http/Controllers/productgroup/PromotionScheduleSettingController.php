<?php

namespace App\Http\Controllers\productgroup;

use App\Http\Controllers\Controller;
use App\Models\Oms\AdsTypeModel;
use App\Models\Oms\BudgetType;
use App\Models\Oms\DutyListsModel;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\PromotionProductPaidPostModel;
use App\Models\Oms\PromotionPromoCategoryVideosPostModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionScheduleSettingPostPageModel;
use App\Models\Oms\PromotionSchedulePaidAdsCampaignTemplateModel;
use App\Models\Oms\PromotionScheduleSettingModel;
use App\Models\Oms\PromotionProductPaidPostSocialModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\storeModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as Input;
use Session;
use DB;
use Carbon\Carbon;

class PromotionScheduleSettingController extends Controller
{
    const VIEW_DIR = 'employeePeerformance';
    const PER_PAGE = 20;

    public function paidAdsSettingTemplateForm($setting = null) {
        $store = Input::get('store');
        $settings = '';
        if($setting != 'null') {
            $settings = PromotionScheduleSettingMainModel::with(['settingSchedules' => function($q) {
                $q->where('is_deleted', 0);
            }, 'settingSchedules.type'])->find($setting);
            foreach($settings->settingSchedules as $k => $schedule) {
                $sub_cates = GroupCategoryModel::with('subCategories')->whereIn('id', explode(",", $schedule->category_id))->first();
                $schedule->cate = $sub_cates;
            }
            $settings->social_ids = explode(',', $settings->social_ids);
            $settings->pages = explode(',', $settings->pages);
        }
        $staff = OmsUserModel::join("oms_user_group AS oug","oug.id","=","oms_user.user_group_id")->where('status', 1)
                            ->where(function($query){
                                $query->where('oms_user.user_access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%')->orWhere('oug.access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%');
                            });
        // dd($staff);
        $types_for_setting = PromotionTypeModel::where('status', 1)->orderBy('name', 'ASC')->get();
        $socials = SocialModel::where('status', 1)->get();
        $categories = GroupCategoryModel::all();

        $users = OmsUserModel::where('status', 1)->whereIn('user_group_id',[12])->get();
        $budget_types = BudgetType::all();    
        $ads_types = AdsTypeModel::where('status', 1)->where('type', 0)->get();
        $creative_types = AdsTypeModel::where('status', 1)->where('type', 1)->get();
        $page = ($setting != 'null') ? '.promotionSetting.paidAds.editPaidSettingForm' : '.promotionSetting.paidAds.createPaidSettingForm';
        // dd($page);
        return view('productGroup'.$page.'')->with(compact('settings','types_for_setting','store','socials','categories','users','budget_types','ads_types','creative_types','staff'));
    }
    
    public function getSubCategoriesForPaidSetting($cate) {
        $subCates = GroupSubCategoryModel::where('group_main_category_id', $cate)->get();
        if(count($subCates) > 0) {
            return response()->json([
                'status' => true,
                'cates' => $subCates
            ]);
        }else {
            return response()->json([
                'status' => false,
                'cates' => 'No sub category available..'
            ]);
        }
    }

    public function savePaidAdsSetting(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'social' => 'required',
            'type.*' => 'required',
            'category.*' => 'required',
            'setting_name' => 'required',
            'ads_type' => 'required',
            'budget_type' => 'required',
            'range' => 'required',
            'estimate_cost' => 'required',
            'optimization_type' => 'required',
            'user' => 'required'
        ]);
        // $exist = PromotionScheduleSettingMainModel::where('social_ids', implode(',', $request->social))->where('store_id', $request->store)->where('posting_type', $request->postIng_type)->where('id', '!=' ,$request->main_setting_id)->first();
        // if(!$exist) {
            $main_setting = ($request->main_setting_id) ? PromotionScheduleSettingMainModel::find($request->main_setting_id) : new PromotionScheduleSettingMainModel();
            $socials = SocialModel::whereIn('id', $request->social)->get(['name'])->toArray();
            $socialTitles = $this->creatSocialName($socials);
            $main_setting->posting_type = $request->postIng_type;
            $main_setting->store_id = $request->store;
            $main_setting->social_ids = implode(',', $request->social);
            $main_setting->title = $socialTitles;
            $main_setting->user_id = $request->user;
            $main_setting->setting_name = $request->setting_name;
            $main_setting->range = $request->range;
            $main_setting->estimated_cost_per_ad_type = $request->estimate_cost;
            $main_setting->ads_type_id = $request->ads_type;
            $main_setting->budget_type_id = $request->budget_type;
            $main_setting->optimization_type = $request->optimization_type;
            $main_setting->designing_person = $request->designing;
            $main_setting->posting_person   = $request->posting;
            $main_setting->duty_activity_id   = $request->acitvity;
            $main_setting->campaign_budget = (isset($request->campaign_budget)) ? $request->campaign_budget : null;	
            $request['social_ids'] = $main_setting->social_ids;
            if($main_setting->save()) {
                $type = $request->type;
                $category = $request->category;
                $setting = $request->setting ? $request->setting : [];
                $is_active = $request->is_active;
                $creative_types = $request->creative_types;
                $ad_set_name = $request->ad_set_name;
                $sub_cate = $request->sub_category;
                $budget = $request->budget;
                
                for($i = 0 ; $i < count($type); $i++) {
                    $cate_name = GroupCategoryModel::select('name')->whereIn('id', explode(",", $category[$i]))->get();
                    $c_name = [];
                    foreach($cate_name as $name) {
                        array_push($c_name, $name->name);
                    } 
                    $sub_name = null;
                    if($sub_cate[$i]) {

                        $sub_name = GroupSubCategoryModel::select('name')->whereIn('id', explode(",", $sub_cate[$i]))->get()->pluck('name');
                        $sub_name = $sub_name->toArray();
                        
                    }else {
                        $sub_name = [];
                    }
                    $request_data = array(
                        'main_setting_id' => $main_setting->id,
                        'posting_type' => 2,
                        'ad_set_name' => $ad_set_name[$i],
                        'promotion_product_type_id' => $type[$i],
                        'sub_category_id' => $sub_cate[$i],
                        'sub_category' => implode(",",$sub_name),
                        'range' => $request->range,
                        'budget' => $budget[$i],
                        'creative_type_id' => $creative_types[$i],
                        'category' => implode(",", $c_name),
                        'category_id' => $category[$i],
                        'created_by' => session('user_id'),
                        'created_at' => date('Y-m-d H:i s'),
                        'is_active' => $is_active[$i],
                    );
                    $settings = PromotionScheduleSettingModel::updateOrCreate(
                        ['id' => @$setting[$i]],
                        $request_data
                    );
                    
                }
                $ba_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', $request->store)->where('posting_type', $request->postIng_type)->orderBy('id', 'DESC')->get();
                $this->setCreatedMenuList($ba_promotion_main_setting, $request->store, $request->postIng_type);
                if($settings) {
                    
                    $promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('store_id', $request->store)->where('posting_type', $request->postIng_type)->where('is_deleted', 0)->orderBy('id', 'DESC')->paginate(self::PER_PAGE);
                    
                    return view('productGroup.promotionSetting.paidAds.paid_setting_ajax_template')->with(compact('promotion_main_setting'));
                }else {
                     return response()->json([
                        'status' => false,
                        'meassge' => 'Setting saved successfully.'
                    ]);
                }
            }
        // }else {
        //     return response()->json([
        //         'status' => 'exist',
        //         'meassge' => 'The social templete has already been taken.'
        //     ]);
        // }
    }

    public function creatSocialName($socials) {
        $name = [];
        
        foreach($socials as $social) {
            array_push($name, $social['name']);
        }
        $social_name = implode('/', $name);
        return $social_name;
    }
    public function setCreatedMenuList($menu, $store, $post_type) {
        if($store == 1 && $post_type == 1) {
            Session::forget('ba_main_setting_list');
            Session::put('ba_main_setting_list', $menu);
        }elseif($store == 2 && $post_type == 1) {
            Session::forget('df_main_setting_list');
            Session::put('df_main_setting_list', $menu);
        }elseif($store == 1 && $post_type == 2) {
            Session::forget('df_paid_main_setting_list');
            Session::put('df_paid_main_setting_list', $menu);
        }else {
            Session::forget('df_paid_main_setting_list');
            Session::put('df_paid_main_setting_list', $menu);
        }  
       
        // Session::put('df_main_setting_list', json_encode($df_promotion_main_setting));  
    }

    public function createMainSettingCopy(PromotionScheduleSettingMainModel $setting) {
        $action = false;
        $newCopy = new PromotionScheduleSettingMainModel();
        $newCopy->store_id = $setting->store_id;
        $newCopy->posting_type = $setting->posting_type;
        $newCopy->social_ids = $setting->social_ids;
        $newCopy->title = $setting->title;
        $newCopy->user_id = $setting->user_id;
        $newCopy->setting_name = $setting->setting_name. '(Copy)';
        $newCopy->range = $setting->range;
        $newCopy->ads_type_id = $setting->ads_type_id;
        $newCopy->budget_type_id = $setting->budget_type_id;
        $newCopy->start_date = $setting->start_date;
        $newCopy->end_date = $setting->end_date ;
        if($newCopy->save()) {
            $action = true;
            $sub_settings = PromotionScheduleSettingModel::where('main_setting_id', $setting->id)->get();
            if(count($sub_settings) > 0) {
                foreach($sub_settings as $sub_setting) {
                    $new_sub_setting = new PromotionScheduleSettingModel();
                    $new_sub_setting->main_setting_id = $newCopy->id;
                    $new_sub_setting->posting_type = $sub_setting->posting_type;
                    $new_sub_setting->range = $sub_setting->range;
                    $new_sub_setting->budget = $sub_setting->budget;
                    $new_sub_setting->promotion_product_type_id = $sub_setting->promotion_product_type_id;
                    $new_sub_setting->category_id = $sub_setting->category_id;
                    $new_sub_setting->category = $sub_setting->category;
                    $new_sub_setting->sub_category_id = $sub_setting->sub_category_id;
                    $new_sub_setting->sub_category = $sub_setting->sub_category;
                    $new_sub_setting->created_by = $sub_setting->created_by;
                    $new_sub_setting->created_by = date('Y-m-d');
                    $new_sub_setting->save();
                }
            }
            $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
            
            Session::forget('df_paid_main_setting_list');
            Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
            return response()->json([
                'success' => true,
                'message' => 'Setting copied successfully.'
            ]);
        }else {
            return response()->json([
                'error' => true,
                'message' => 'Opps, Process failed try again.'
            ]);
        }
    }

    public function destroyMainSetting(PromotionScheduleSettingMainModel $setting) {
        $setting->is_deleted = 1;
        if($setting->update()) {
            $ba_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
            $df_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
            Session::put('ba_main_setting_list', json_encode($ba_promotion_main_setting));
            Session::put('df_main_setting_list', json_encode($df_promotion_main_setting));
            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully.'
            ]);
        }else {
            return response()->json([
                'error' => true,
                'message' => 'Opps, Process failed try again.'
            ]);
        }
    }

    public function getPaidAdsCompaignTemplate($setting) {
            $campaignName = Input::get('campaign_name');
            $settings = PromotionScheduleSettingMainModel::with(['settingSchedules' => function($q) {
                $q->where('is_deleted', 0);
            }, 'adsType','budgetType','settingSchedules.type','settingSchedules.creativeType'])->find($setting);
            
        $staff = OmsUserModel::join("oms_user_group AS oug","oug.id","=","oms_user.user_group_id")->where('status', 1)
                            ->where(function($query){
                                $query->where('oms_user.user_access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%')->orWhere('oug.access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%');
                            });
        
        $users = OmsUserModel::where('status', 1)->whereIn('user_group_id',[12])->get();
        // dd($settings);
        return \view(self::VIEW_DIR. '.paidSettingTemplate')->with(compact('settings','staff','users','campaignName'));
        
    }

    public function getnewFormissingDayschedulesGroupsForPaidAds($campaign,$row,$main_setting,$setting_id,$type, $category,$category_ids, $group_type, $socials,$store, $post_type, $range, $budget, $action, $date = null, $end = null, $sub_category = null, $form = null) {
        $cate = explode(",", $category);
        $group_name = '';
        $whereClause = [];
        $subCateWhere = [];
        $subCat_name = '';
        if($type != 'Promo Video' && $type != 'All' && $type != 'Season' && $type != 'Square Video') { // Season added just for emergency) {
            $whereClause[] = array('product_type_id',$group_type);
        }
        if(count($cate) == 1) {
            $subCateWhere[] = array('sub_category_id', $sub_category);
            $subCat = GroupSubCategoryModel::find($sub_category);
            if($subCat) {
                $subCat_name = $subCat->name;
            }
        }
        $groups = ProductGroupModel::with('producType','products')
                                    ->whereHas('products', function ($query) {
                                      $query->where('status', 1);
                                    })
                                    ->where($whereClause)
                                    ->whereIn('category_name', $cate)
                                    ->where($subCateWhere)->get();
        // dd($groups);
        $collection = $groups;
        
        $group_code = null;
        return view(SELF::VIEW_DIR. '.marketting.changeScheduleForm')->with(compact('collection', 'type', 'category', 'group_type', 'group_code','socials','date','end','range','main_setting','campaign','setting_id','group_name','cate','post_type','store','budget','sub_category','row','action','form','subCat_name'));
    }

    public function getnewschedulesGroups($main_setting,$setting_id,$type, $category, $group_type, $group_code,$group_id,$post_id, $socials,$date,$store, $post_type, $time = null, $action = null, $sub_category = null,$form = null) {
        $cate = explode(",", $category);
        $setting = PromotionProductPaidPostModel::find($post_id);
        $group_name = $setting->group_name;
        $last_date = $setting->last_date;
        $whereClause = [];
        $whereClauseSubCate = [];
        if($type != 'Promo Video' && $type != 'All' && $type != 'Season') { // Season added just for emergency
            $whereClause[] = array('product_type_id',$group_type);
        }
        if($sub_category) {
            $whereClauseSubCate[] = array('sub_category_id', $sub_category);
        }
        $groups = ProductGroupModel::with('producType','products')
                                    ->whereHas('products', function ($query) {
                                      $query->where('status', 1);
                                    })
                                    ->where($whereClause)
                                    ->where($whereClauseSubCate)
                                    ->where('category_name', $setting->group_name)->get();
        $collection = $groups;
        return view(SELF::VIEW_DIR. '.marketting.editChangeScheduleForm')->with(compact('collection', 'type', 'category', 'group_type', 'group_code', 'post_id','socials','date','last_date','time','main_setting','setting_id','group_name','cate','post_type','store','group_id','action','sub_category','form'));
    }

    public function searchGroupCodeForSchedule($search, $cate, $selected_cate, $group_type, $type, $sub_category = null) {
        // dd($selected_cate);
        $whereClause = [];
        $whereClauseSubCate = [];
        if($type != 'Promo Video' && $type != 'All' && $type != 'Season') { // Season added just for emergency
            $whereClause[] = array('product_type_id',$group_type);
        }
        if($sub_category) {
            $whereClauseSubCate[] = array('sub_category_id', $sub_category);
        }
        $cate = $selected_cate;
        // dd($cate);
        $groups = ProductGroupModel::with('producType','products')
                                    ->whereHas('products', function ($query) {
                                      $query->where('status', 1);
                                    })
                                    ->where($whereClause)
                                    ->where($whereClauseSubCate)
                                    ->where('category_name', $cate)
                                    ->where('name', 'LIKE', $search.'%')
                                    ->get();
        // dd($groups);
        return $groups;
        
    }

    public function getGroupListForSelectedCategory($group_type, $type, $cate, $sub_cate = null) {
        $whereClause = [];
        $whereClauseSubCate = [];
        // dd($type);
        if($type != 'Promo Video' && $type != 'All' && $type != 'Season' && $type != 'Square Video') { // Season added just for emergency
            $whereClause[] = array('product_type_id',$group_type);
        }
        if($sub_cate) {
            $whereClauseSubCate[] = array('sub_category_id', $sub_cate);
        }
        // dd($cate);
        $groups = ProductGroupModel::with('producType','products')
                                    ->whereHas('products', function ($query) {
                                      $query->where('status', 1);
                                    })
                                    ->where($whereClause)
                                    ->where('category_name', $cate)->where($whereClauseSubCate)->get();
                    
        if(count($groups) > 0) {
            return response()->json([
                'status' => true,
                'groups' => $groups
            ]);
        }else {
            return response()->json([
                'status' => false
            ]);
        }
        
    }

    public function getSelectedGroupId($group_code) {
        $group = ProductGroupModel::select('id')
                                    ->where('name', '=', $group_code)
                                    ->first();
        // dd($group);
        return response()->json([
            'status' => true,
            'id' =>$group ? $group->id : null
        ]);
    }

    public function scheduleGroupDetail($group, $posting_type = null) {
        $groupProducts = ProductGroupModel::with('producType','products')->find($group);
        $whereClause = [];
        $socials = SocialModel::where('status', 1)->get();
            $st_history = [];
                if($posting_type) {
                    $whereClause[] = array('posting_type', $posting_type);
                }
                // dd($whereClause);
                // $bahistory = PromotionProductPostModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT social_id) AS socials'))->where('group_id', $groupProducts->id)->groupBy('group_id', 'date')->orderBy('id','DESC')->take(2)->get();
                $bahistory = PromotionProductPaidPostModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT social_id) AS socials'))->where('group_id', $groupProducts->id)->where($whereClause)->where('store_id', 1)->groupBy('group_id', 'date')->orderBy('id','DESC')->first();
                if($bahistory) {

                    array_push($st_history, $bahistory);
                }
                $dfhistory = PromotionProductPaidPostModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT social_id) AS socials'))->where('group_id', $groupProducts->id)->where($whereClause)->where('store_id', 2)->groupBy('group_id', 'date')->orderBy('id','DESC')->first();
                // $history = PromotionProductPostModel::with('store')->select('*',DB::raw('GROUP_CONCAT(DISTINCT social_id) AS socials'))->groupBy('store_id')->orderBy('id','DESC')->get();
                if($dfhistory) {
                    array_push($st_history, $dfhistory);
                }
            $groupProducts->histories = $st_history;
            // dd($groupProducts->products);
            return view('productGroup.ba_df_work_reports.scheduleGroupDetails')->with(compact('groupProducts','socials'));

    }

    public function saveChangedSchedule(Request $request) {
        $mainSetting = PromotionScheduleSettingMainModel::find($request->main_setting);
        $main_setting_name = '';
        $product = OmsInventoryProductModel::with('ProductsSizes')->whereHas('ProductsSizes', function($q) {
            $q->where('available_quantity', '!=', 0);
        })->where('group_id', $request->schedule_group)->first();
        if($product) {
            if($request->action == 'next') {
                $exist = true;
                $exist_date = '';
                $whereClause = [];
                $group = ProductGroupModel::find($request->schedule_group);
                $whereClause[] = array('post_duration', 2);
                // Checking for same duration in next posts
                $check_days = PromotionProductPaidPostModel::where('group_id', $request->schedule_group)->where('group_code', $group->name)
                                                            ->whereIn('group_name', explode(",", $request->category))
                                                            ->where($whereClause)
                                                            ->where('posting_type', $request->post_type)
                                                            ->groupBy('date')
                                                            ->get();
                
                if(count($check_days) > 0) {
                    $exist = false;
                    $main_setting_name = $check_days[0]->main_setting->setting_name;
                }
                // checking for same user in current posts
                $exist_in_current = PromotionProductPaidPostModel::where('group_id', $request->schedule_group)->where('group_code', $group->name)
                                                                ->where('main_setting_id', $request->main_setting)
                                                                ->whereIn('group_name', explode(",", $request->category))
                                                                ->where('post_duration', 1)
                                                                ->where('posting_type', $request->post_type)
                                                                ->groupBy('date')
                                                                ->get();
                if(count($exist_in_current) > 0) {
                    $exist = false;
                    $main_setting_name = $exist_in_current[0]->main_setting->setting_name;
                }
                if($exist) {
                    $action = '';
                    $setting_data = PromotionSchedulePaidAdsCampaignTemplateModel::find($request->setting);
                    
                    $socials = explode(",",$request->socials);
                    if($request->post_id) {
                        $action = 'old';
                        $post = PromotionProductPaidPostModel::find($request->post_id);
                        $post->group_code = $group->name;
                        $post->group_id = $request->schedule_group;
                        $post->updated_by = session('user_id');
                        $post->updated_at = date('Y-m-d H:i:s');
                        $post->save();
                    }else {
                        if($request->action == 'next') {
                            $range = explode(' ', $request->time);
                            $today = date('Y-m-d');
                            $next_date = \strtotime("+".$range[0]." days", strtotime($today));
                            $next_date = date('Y-m-d', $next_date);
                            $next_start = date('Y-m-d', \strtotime($request->last_date . ' +1 day'));
                            if($request->post_type == 2) {
                                $next_start = date('Y-m-d');
                            }
                            $post_duration = 2;
                            $is_active_paid_ad = 0;
                            $campaign = $request->campaign;
                        }else {
                            $next_start = $request->date;
                            $next_date = $request->last_date;
                            $post_duration = 1;
                            $is_active_paid_ad = 1;
                            $campaign = null;
                        }
                        $action = 'new';
                        $post = new PromotionProductPaidPostModel();
                        $post->group_code = $group->name;
                        $post->group_id = $request->schedule_group;
                        $post->store_id = $request->store;
                        $post->social_id = $request->socials;
                        $post->group_name = $request->selected_category;
                        $post->main_setting_id = $request->main_setting;
                        $post->setting_id = $request->setting;
                        $post->product_type_id = $setting_data->promotion_product_type_id;
                        $post->time = $request->time;
                        $post->range = $request->time;
                        $post->budget = $request->budget;
                        $post->post_duration = $post_duration;
                        $post->is_active_paid_ads = $is_active_paid_ad;
                        $post->campaign_id = $campaign;
                                    // $post->date = $request->date;
                                    // $post->last_date = ($request->last_date) ? $request->last_date : $request->date;
                        $post->date = $next_start;
                        $post->last_date = $next_date;
                        $post->posting_type = $request->post_type;
                        $post->pages = $mainSetting->pages ? $mainSetting->pages : null;
                        $post->created_at = date('Y-m-d H:i:s');
                        if($post->save()) {
                            foreach($socials as $socl) {
                                $social = new PromotionProductPaidPostSocialModel();
                                $social->paid_post_id = $post->id;
                                $social->social_id = $socl;
                                $social->save();
                            }
                        }
                    }
                    return response()->json([
                        'status' => true,
                        'code' => $group->name,
                        'post_id' => $post->id,
                        'action' => $action,
                        'old_code' => $request->old_group_code,
                        'row' => $request->row,
                        'id' => $request->main_setting
                    ]);
                }else {
                    return response()->json([
                        'status' => false,
                        'exist_date' => ($exist_date && $exist_date != "") ? $exist_date->format('D-d-F') : '',
                        'code' => $group->name,
                        'main_setting' => $main_setting_name
                    ]);
                }
            }else {
                $new = $this->savePaidChangedSchedule($request);
                return $new;
            }
            
        }else {
            return response()->json([
                'status' => 'no_quantity',
                'mesge' => 'There are no quantity available'
            ]);
        }
    }

    public function savePaidChangedSchedule($request) {
        // dd($request->all());
        $this->validate($request, [
            'selected_category' => 'required'
        ]);
        $exist = true;
        $exist_date = '';
        $exist_date = '';
        $main_setting_name = '';
        $group = ProductGroupModel::find($request->schedule_group);
        $check_days = PromotionProductPaidPostModel::where('group_id', $request->schedule_group)
                        ->where('group_code', $group->name)
                        ->whereIn('group_name', explode(",", $request->category))
                        // ->where('date','<=', $request->date)
                        // ->where('last_date','>=', $request->date)
                        ->where('post_duration', 1)
                        ->where('posting_type', $request->post_type)
                        ->groupBy('date')
                        ->first();
        if($check_days) { 
                $exist_date = Carbon::parse(date('Y-m-d', strtotime($check_days->date)));
                $main_setting_name = $check_days->main_setting->setting_name;
                $exist = false;
        }
        $setting_data = PromotionSchedulePaidAdsCampaignTemplateModel::find($request->setting);
        // dd($request->campaign);
        if($exist) {
            $postt = PromotionProductPaidPostModel::find($request->post_id);
            $save_flag = false;
            $socials = explode(",",$request->socials);
            
            $post = new PromotionProductPaidPostModel();
            $post->group_code = $group->name;
            $post->group_id = $request->schedule_group;
            $post->store_id = $request->store;
            $post->social_id = $request->socials;
            $post->group_name = $request->selected_category;
            $post->main_setting_id = $request->main_setting;
            $post->setting_id = $request->setting;
            $post->range = $request->time;
            $post->product_type_id = $setting_data->promotion_product_type_id;
            // $post->time = $p_p_p->schedule_time;
            $post->date = $request->date;
            $post->last_date = $request->last_date;
            $post->posting_type = $request->post_type;
            $post->budget = ($request->budget) ? $request->budget : $setting_data->budget;
            $post->post_duration = 1;
            $post->campaign_id = ($request->campaign || $request->campaign == 0) ? $request->campaign : $postt->campaign_id;
            if($request->post_id) {
                $post->is_active_paid_ads = $postt->is_active_paid_ads;
            }
            $post->created_at = date('Y-m-d H:i:s');
            if($post->save()) {
                foreach($socials as $socl) {
                    $social = new PromotionProductPaidPostSocialModel();
                    $social->paid_post_id = $post->id;
                    $social->social_id = $socl;
                    $social->save();
                }
            }
            $save_flag = true;
          if($request->post_id) {
              $subst = 1;
                    $take = count($socials)-$subst;
                    
                    $postt->posting = 0;
                    $postt->update();
        }
        if($save_flag) {
            return response()->json([
                'status' => true,
                'code' => $group->name,
                'post_id' => $post->id,
                'old_code' => $request->old_group_code,
                'row' => $request->row,
                'id' => $request->main_setting
            ]);
        }
          
        }else {
            return response()->json([
                'status' => false,
                'exist_date' => $main_setting_name,
                'main_setting' => $main_setting_name,
                'code' => $group->name
            ]);
        }
        
    }

    public function destroySetting(PromotionScheduleSettingModel $setting) {
        $setting->is_deleted = 1;
        if($setting->update()) {
            // PromotionProductPostModel::where('setting_id', $setting->id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Setting deleted successfully.'
            ]);
        }else {
            return response()->json([
                'error' => true,
                'message' => 'Opps, Process fail try again.'
            ]);
        }
    }

    
}
