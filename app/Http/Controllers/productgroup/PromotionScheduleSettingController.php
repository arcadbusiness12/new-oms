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
use DateTime;
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



    public function saveChangedScheduleOr(Request $request) {
        // dd($request->all());
        $mainSetting = PromotionScheduleSettingMainModel::find($request->main_setting);
        if($request->organic_multiple) {
          $video_posts =  $this->saveMultiplePostForArganic($request);
         return $video_posts;
        }else {
            $main_setting_name = '';
            $product = OmsInventoryProductModel::with('ProductsSizes')->whereHas('ProductsSizes', function($q) {
                $q->where('available_quantity', '!=', 0);
            })->where('group_id', $request->schedule_group)->first();
            if($product) {
                if($request->post_type == 1 || ($request->post_type == 2 && $request->action == 'next')) {
                    // dd($request->action);
                    $exist = true;
                    $exist_date = '';
                    $whereClause = [];
                    $group = ProductGroupModel::find($request->schedule_group);
                    // dd($request->setting);
                    if($request->post_type == 1) {
                        array_push($whereClause, array('main_setting_id', $request->main_setting));
                        array_push($whereClause, array('setting_id', $request->setting));
                    }else {
                        $whereClause[] = array('post_duration', 2);
                    }
                    // Checking for same duration in next posts
                    $check_days = PromotionProductPostModel::where('group_id', $request->schedule_group)->where('group_code', $group->name)
                                    ->whereIn('group_name', explode(",", $request->category))
                                    ->where($whereClause)
                                    ->where('posting_type', $request->post_type)
                                    ->groupBy('date')
                                    ->get();
                    // dd($check_days);
                    if(count($check_days) > 0 && $request->post_type == 2) {
                        $exist = false;
                        $main_setting_name = $check_days[0]->main_setting->setting_name;
                    }else {
                        foreach($check_days as $k => $check_day) {
                            $d = date('Y-m-d',strtotime($check_day->date));
                            // dd(Carbon::parse(date('Y-m-d',strtotime($check_day->date)))->diffInDays(Carbon::parse(date('Y-m-d', strtotime($request->date)))));
                            if(Carbon::parse(date('Y-m-d',strtotime($check_day->date)))->diffInDays(Carbon::parse(date('Y-m-d', strtotime($request->date)))) <= 5) {
                                $exist = false;
                                $exist_date = Carbon::parse(date('Y-m-d', strtotime($check_day->date)));
                                break;
                            }else {
                                $exist = true;
                            }
                        }
                    }
                    // dd($exist);
                    // checking for same user in current posts
                    if($request->post_type == 2){
                        $exist_in_current = PromotionProductPostModel::where('group_id', $request->schedule_group)->where('group_code', $group->name)
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
                    }
                    
                    if($exist) {
                        $action = '';
                        $setting_data = PromotionScheduleSettingModel::find($request->setting);
                        $socials = explode(",",$request->socials);
                        if($request->post_id) {
                            $action = 'old';
                            $subst = 1;
                            $take = count($socials)-$subst;
                            $post = PromotionProductPostModel::find($request->post_id);
                            $post->group_code = $group->name;
                            $post->group_id = $request->schedule_group;
                            $post->updated_by = session('user_id');
                            $post->updated_at = date('Y-m-d H:i:s');
                            $whereClause = [];
                            $orderByClause = '';
                            if($request->post_type == 1) {
                                $whereClause[] = array('id', '<', $request->post_id);
                                $orderByClause = 'DESC';
                                
                            }else{
                                $whereClause[] = array('id', '>', $request->post_id);
                                $orderByClause = 'ASC';
                            }
                            // dd($orderByClause);
                            if($post->save()) {
                                $pre_post = PromotionProductPostModel::where($whereClause)->orderBy('id', $orderByClause)->take($take)->get();                
                                if(count($pre_post) > 0){
                                    foreach($pre_post as $p) {
                                        $p->group_code = $group->name;
                                        $p->group_id = $request->schedule_group;
                                        $p->update();
                                    }
                                }
                                
                            }
                        }else {
                            if($request->action == 'next') {
                                $range = explode(' ', $request->time);
                                if($request->post_type == 2) {
                                    $today = date('Y-m-d');
                                }else {
                                    $today = date('Y-m-d', strtotime($request->last_date));
                                }
                                $next_date = strtotime("+".$range[0]." days", strtotime($today));
                                
                                $next_date = date('Y-m-d', $next_date);
                                $next_start = date('Y-m-d', strtotime($request->last_date . ' +1 day'));
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
                            foreach($socials as $social) {
                                $post = new PromotionProductPostModel();
                                $post->group_code = $group->name;
                                $post->group_id = $request->schedule_group;
                                $post->store_id = $request->store;
                                $post->social_id = $social;
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
                                $post->save();
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
                // dd("Yes");
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
        
    }
    
    public function saveMultiplePostForArganic($request) {
        // dd($request->all());
        $mainSetting = PromotionScheduleSettingMainModel::find($request->main_setting);
        $exist = true;
        $group_codes = [];
        $exist_group = '';
        $whereClause = [];
        if($request->post_id) {
            $whereClause[] = array('product_post_id', '!=', $request->post_id);
        }
        // dd($whereClause);
        foreach($request->schedule_group as $schedule_group) { // checking for quantity and scheduled post is already posted in 20 days or not
            $product = OmsInventoryProductModel::with('ProductsSizes')->whereHas('ProductsSizes', function($q) {
                $q->where('available_quantity', '!=', 0);
            })->where('group_id', $schedule_group)->first();
            if($product) {
                $group = ProductGroupModel::find($schedule_group);
                // Checking for exist posts
                $check_exist = PromotionPromoCategoryVideosPostModel::where('group_id', $schedule_group)->where('group_code', $group->name)
                                                                    ->where('group_name', $group->category_name)
                                                                    ->where('main_setting_id', $request->main_setting)
                                                                    ->where('setting_id', $request->setting)
                                                                    ->where($whereClause)
                                                                    ->orderBy('post_date', 'DESC')
                                                                    ->first();
                // dd($check_exist);
                if($check_exist) {
                        // dd(Carbon::parse(date('Y-m-d',strtotime($check_day->date)))->diffInDays(Carbon::parse(date('Y-m-d', strtotime($request->date)))));
                        if(Carbon::parse(date('Y-m-d',strtotime($check_exist->post_date)))->diffInDays(Carbon::parse(date('Y-m-d', strtotime($request->date)))) <= 19) {
                            $exist = false;
                            $exist_date = Carbon::parse(date('Y-m-d', strtotime($check_exist->post_date)));
                            $exist_group = $check_exist->group_code;
                            break;
                        }else {
                            $exist = true;
                        }
                }
                array_push($group_codes, $group->name);
                // dd($exist);
            }else {
                $group = ProductGroupModel::find($schedule_group);
                return response()->json([
                    'status' => 'no_quantity',
                    'mesge' => 'No quantity available in '.$group->name
                ]);
                // break;
            }
        }
        // dd($request->category);
        if($exist) {
            $post_id = null;
            $action = '';
            $setting_data = PromotionScheduleSettingModel::find($request->setting);
            $socials = explode(",",$request->socials);
            if($request->post_id) {
                $post_id = $request->post_id;
                $action = 'old';
                $subst = 1;
                $take = count($socials)-$subst;
                $post = PromotionProductPostModel::find($request->post_id);
                $post->group_code = implode(',', $group_codes);
                $post->group_name = $request->category;
                $post->updated_by = session('user_id');
                $post->updated_at = date('Y-m-d H:i:s');
                $whereClause = [];
                $orderByClause = '';
                if($request->post_type == 1) {
                    $whereClause[] = array('id', '<', $request->post_id);
                    $orderByClause = 'DESC';
                    
                }else{
                    $whereClause[] = array('id', '>', $request->post_id);
                    $orderByClause = 'ASC';
                }
                // dd($orderByClause);
                if($post->save()) {
                    $pre_post = PromotionProductPostModel::where($whereClause)->orderBy('id', $orderByClause)->take($take)->get();                
                    if(count($pre_post) > 0){
                        foreach($pre_post as $p) {
                            $post->group_code = implode(',', $group_codes);
                            $post->group_name = $request->category;
                            $p->update();
                        }
                    }
                    
                }
            }else {
                    $next_start = $request->date;
                    $next_date = $request->last_date;
                    $post_duration = 1;
                $action = 'new';
                foreach($socials as $social) {
                    $post = new PromotionProductPostModel();
                    $post->group_code = implode(',', $group_codes);
                    $post->group_name = $request->category;
                    $post->store_id = $request->store;
                    $post->social_id = $social;
                    $post->group_name = $request->selected_category;
                    $post->main_setting_id = $request->main_setting;
                    $post->setting_id = $request->setting;
                    $post->product_type_id = $setting_data->promotion_product_type_id;
                    $post->time = $request->time;
                    $post->range = $request->time;
                    $post->budget = $request->budget;
                    $post->post_duration = 0;
                    // $post->date = $request->date;
                    // $post->last_date = ($request->last_date) ? $request->last_date : $request->date;
                    $post->date = $next_start;
                    $post->last_date = $next_date;
                    $post->posting_type = $request->post_type;
                    $post->pages = $mainSetting->pages ? $mainSetting->pages : null;
                    $post->created_at = date('Y-m-d H:i:s');
                    $post->save();
                    
                     $post_id = $post->id;   
                }
            }

            PromotionPromoCategoryVideosPostModel::where('product_post_id', $post->id)->delete(); // delete old entry
            foreach($request->schedule_group as $schedule_group) { // creating new posts for videos
                $group = ProductGroupModel::find($schedule_group);

                $post_vidoes = new PromotionPromoCategoryVideosPostModel();
                $post_vidoes->product_post_id = $post_id;
                $post_vidoes->main_setting_id = $request->main_setting;
                $post_vidoes->setting_id = $request->setting;
                $post_vidoes->group_code = $group->name;
                $post_vidoes->group_id = $schedule_group;
                $post_vidoes->group_name = $group->category_name;
                $post_vidoes->post_date = $request->date;
                $post_vidoes->created_at = date('Y-m-d');
                
                $post_vidoes->save();
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
            // dd("Yes");
            return response()->json([
                'status' => false,
                'exist_date' => ($exist_date && $exist_date != "") ? $exist_date->format('D-d-F') : '',
                'code' => $exist_group,
            ]);
        }
        

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

    public function getBaWorkReports($id, $store, $post_type) {
        
        $today = date('Y-m-d');
        // $counter = 1;
        $next_date = strtotime("+7 days", strtotime($today));
        $next_seven_day = date('Y-m-d',$next_date);
        // $this->createReportSchedules($id, $store, $post_type);
        // $this->createPaidReportSchedules($id, $store, $post_type, $socials);
        $templates = PromotionScheduleSettingMainModel::find($id);
        $template_socials = explode(',', $templates->social_ids);
        $product_pro_posts = PromotionScheduleSettingModel::with('type','subCategory')->where('main_setting_id', $id)->where('posting_type', $post_type)->where('is_deleted', 0)->get();
        // dd($product_pro_posts);
        $socials = SocialModel::where('status', 1)->get();
        $pro_posts = PromotionProductPostModel::with(['promo_cate_posts'=>function($qry) use($id){
          $qry->where('main_setting_id',$id);
        },'group'])->where('store_id', $store)->where('main_setting_id', $id)->where('posting_type', $post_type)->whereDate('created_at','>',date('Y-m-d',strtotime('-35 days')))->get();
        // dd($pro_posts->toArray());
        // $pro_posts = PromotionProductPostModel::select('oms_promotion_product_posts.*','ipg.*','ppcvp.*')
        //                                         ->join('oms_inventory_product_groups AS ipg', 'ipg.id', '=', 'oms_promotion_product_posts.group_id')
        //                                         ->join('oms_promotion_promo_category_videos_posts AS ppcvp', 'ppcvp.product_post_id', '=', 'oms_promotion_product_posts.id');
        // $pro_posts = $pro_posts->where('oms_promotion_product_posts.store_id', $store)->where('oms_promotion_product_posts.main_setting_id', $id)->where('oms_promotion_product_posts.posting_type', $post_type)->whereDate('oms_promotion_product_posts.created_at','>',date('Y-m-d',strtotime('-35 days')))->get();
        // dd($pro_posts->toArray());
        $days = $this->calculate_week_Days(date('Y-m-d'), 2); 
        // dd($templates);
        $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        
        Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
        if($post_type == 1) {
            $directory = '.ba_df_work_reports';
        }else {
            $product_pro_posts = $this->transformPaidPosting($product_pro_posts, $pro_posts, $id);
            // dd($product_pro_posts);
            $directory = '.paid_ads';
        }
        
        return  view('productGroup'. $directory.'.ba_work_report')->with(compact('product_pro_posts', 'pro_posts', 'days', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day','post_type'));
    }

    public function calculate_week_Days($today, $action) {
        // $oneweekfromnow = strtotime("+1 month", strtotime($today));
        // $oneweekfromnow = date('Y-m-d', $oneweekfromnow);
        // $today = new DateTime($today);
        // $oneweekfromnow = new DateTime($oneweekfromnow);
        // $days = [];
        // for($date = $today; $date < $oneweekfromnow; $date->modify('+1 day')) {
        //     $dates = [
        //         'display_date' => $date->format('D-d-F'),
        //         'hiddn_date'   => $da te->format('Y-m-d')
        //     ];
        //     array_push($days, $dates);
        // }
        // return $days;
        

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

    public function getOrganicschedulesGroupForNewDays($row,$main_setting,$setting_id,$type, $category,$category_ids, $group_type, $socials,$date,$store, $post_type, $time = null) {
        
        $cate = explode(",", $category);
        if(in_array('Rings', $cate) || in_array('Earrings', $cate)) {
            array_push($cate,'Jewlery');
        }
        $group_name = '';
        $whereClause = [];

        if($type != 'Promo Video' && $type != 'All' && $type != 'Category Video' && $group_type != 6 && $group_type != 9 && $type != 'Season' && $type != 'Square Video' ) { // Season added just for emergency) {
            $whereClause[] = array('product_type_id',$group_type);
        }
        $groups = ProductGroupModel::with('producType','products')
                                    ->whereHas('products', function ($query) {
                                      $query->where('status', 1);
                                    })
                                    ->where($whereClause)
                                    ->whereIn('category_name', $cate)->get();

        // dd("Ok");
        $collection = $groups;
        
        $group_code = null;
        $post_id = null;
        return view('productGroup.ba_df_work_reports.change_organic_schedule_form')->with(compact('collection', 'type', 'category', 'group_type', 'group_code', 'post_id','socials','date','time','main_setting','setting_id','group_name','cate','post_type','store','row'));
           
        
    }

    public function getBaWorkReportsHistory($id, $store, $post_type) {
        $today = date('Y-m-d');
        // $counter = 1;
        $next_date = strtotime("+7 days", strtotime($today));
        $next_seven_day = date('Y-m-d',$next_date);
        // $this->createReportSchedules($id, $store, $post_type);
        // $this->createPaidReportSchedules($id, $store, $post_type, $socials);
        $templates = PromotionScheduleSettingMainModel::find($id);
        $template_socials = explode(',', $templates->social_ids);
        $product_pro_posts = PromotionScheduleSettingModel::with('type','subCategory')->where('main_setting_id', $id)->where('posting_type', $post_type)->get();
        // dd($product_pro_posts);
        $socials = SocialModel::where('status', 1)->get();
        $pro_posts = PromotionProductPostModel::with(['promo_cate_posts'=>function($qry) use($id){
          $qry->where('main_setting_id',$id);
        },'group'])->where('store_id', $store)->where('main_setting_id', $id)->where('posting_type', $post_type)->whereDate('created_at','<=',date('Y-m-d'))->whereDate('created_at','>',date('Y-m-d',strtotime('-40 days')))->get();
        // dd($pro_posts->toArray());
        // $pro_posts = PromotionProductPostModel::select('oms_promotion_product_posts.*','ipg.*','ppcvp.*')
        //                                         ->join('oms_inventory_product_groups AS ipg', 'ipg.id', '=', 'oms_promotion_product_posts.group_id')
        //                                         ->join('oms_promotion_promo_category_videos_posts AS ppcvp', 'ppcvp.product_post_id', '=', 'oms_promotion_product_posts.id');
        // $pro_posts = $pro_posts->where('oms_promotion_product_posts.store_id', $store)->where('oms_promotion_product_posts.main_setting_id', $id)->where('oms_promotion_product_posts.posting_type', $post_type)->whereDate('oms_promotion_product_posts.created_at','>',date('Y-m-d',strtotime('-35 days')))->get();
        // dd($pro_posts->toArray());
        $days = $this->calculate_week_Days(date('Y-m-d'), 2); 
        // dd($templates);
        $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 1)->where('posting_type', 2)->orderBy('id', 'DESC')->get();
        
        Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
        $directory = '.ba_df_work_reports';
        // $ar = [];
        // foreach($pro_posts as $p) {
        //     if($p->id == 52567 || $p->id == 52568 || $p->id == 52571 || $p->id == 52572 || $p->id == 52467 || $p->id == 52468 || $p->id == 52610 || $p->id == 52611 || $p->id == 52612) {
        //         array_push($ar, $p);
        //     }
        // }
        // dd($ar); 
        return  view('productGroup'. $directory.'.ba_work_report_history')->with(compact('product_pro_posts', 'pro_posts', 'days', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day','post_type'));
    }

    public function getPaidWorkReports($id, $store, $post_type, $action, $cate = null) {
        // dd($product_pro_postts->toArray());
        $new_p_p = [];
        $today = date('Y-m-d');
        $history_blocks = null;
        $next_date = strtotime("+7 days", strtotime($today));
        $next_seven_day = date('Y-m-d',$next_date);
        $templates = PromotionScheduleSettingMainModel::find($id);
        $template_socials = explode(',', $templates->social_ids);
        $whereClause = [];
        $orWhereClause = [];
        $whereClause1 = [];
        $catWhereClause = [];
        $subcWhereClause = [];
        if($action == 'current') {
            $whereClause[] = array('is_deleted', 0);
            array_push($whereClause1, ['post_duration', 1]);
            array_push($orWhereClause, ['post_duration', 2]);
        }else {
            $whereClause1[] = array('last_date', '<', date('Y-m-d'));
            array_push($orWhereClause, ['post_duration', 0]);
            $history_blocks = $this->getHistoryBlocks($id,$store, $post_type,$whereClause1);
        }
            if($cate) {
                $category = ProductGroupModel::find($cate);
                $catWhereClause[] = array('category', 'LIKE', '%'.$category->category_name.'%');
            }
            $product_pro_posts = PromotionScheduleSettingModel::with('type','subCategory')
             ->where('main_setting_id', $id)
             ->where('posting_type', $post_type)
             ->where($whereClause)
             ->where($catWhereClause)
             ->where($subcWhereClause)
             ->get();
             
        $socials = SocialModel::where('status', 1)->get();
        $pro_posts = PromotionProductPostModel::with('group', 'chatHistories')->where('store_id', $store)
        ->where('main_setting_id', $id)
        ->where('posting_type', $post_type)
        ->where($whereClause1)
        ->orWhere($orWhereClause)
        ->get();
        $ba_paid_ads_promotion_main_setting = PromotionScheduleSettingMainModel::where('posting_type', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
        $campaign_current = PaidAdsCampaign::with(['chatResults' => function($q) {
            $q->orderBy('date','DESC')->first();
          },'chatResults.user'])->where('main_setting_id', $id)->where('status', 1)->first();
        $campaign_next = PaidAdsCampaign::with(['chatResults' => function($q) {
            $q->orderBy('date','DESC')->first();
          },'chatResults.user'])->where('main_setting_id', $id)->where('status', 2)->first(); 
          Session::put('df_paid_main_setting_list', json_encode($ba_paid_ads_promotion_main_setting));
        if($cate) {
            $product_pro_posts = $this->transformPaidPosting($product_pro_posts, $pro_posts, $id);
            $schedule_date = [
                'product_pro_posts' => $product_pro_posts,
                'pro_posts' => $pro_posts,
                'socials' => $socials,
                'templates' => $templates,
                'template_socials' => $template_socials,
                'store' => $store,
                'id' => $id,
                'next_seven_day' => $next_seven_day,
                'action' => $action,
                'post_type' => $post_type,
                'history_blocks' => $history_blocks,
            ];
            return  $schedule_date;
            // view(SELF::VIEW_DIR. '.template_schedules')->with(compact('product_pro_posts', 'pro_posts', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day', 'action', 'post_type', 'history_blocks'));
        }else {
            if($post_type == 1) {
                $directory = '.ba_df_work_reports';
            }else {
                $product_pro_posts = $this->transformPaidPosting($product_pro_posts, $pro_posts, $id);
                // dd($product_pro_posts);
                $directory = '.paidAds';
            }
            // dd($directory);
            $ad_types = AdsTypeModel::with(['paidAdsSettings'=>function($query){
                $query->where('is_deleted',0);
              }])->whereHas('paidAdsSettings')->get();
              // dd($ad_types->toArray());
            return  view('productGroup'. $directory.'.ba_work_report')->with(compact('product_pro_posts', 'pro_posts', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day', 'action', 'post_type', 'history_blocks','ad_types', 'campaign_current', 'campaign_next'));
        }
        
        
    }

    public function getHistoryBlocks($id, $store, $post_type, $whereClause1) {
        // dd($whereClause1);
        $history_blocks = PromotionProductPostModel::with('group')->where('store_id', $store)
                                                    ->where('main_setting_id', $id)
                                                    ->where('posting_type', $post_type)->where($whereClause1)
                                                    ->groupBy('date')->orderBy('date','ASC')
                                                    ->get();
        return $history_blocks;
    }
    public function transformPaidPosting($product_pro_posts, $pro_posts, $id) {
        // dd($pro_posts->toArray());
        foreach($product_pro_posts as $product_pro_post) {
            $previous = [];
            $current = [];
            $next = [];
            foreach($pro_posts as $post) {
                if($product_pro_post->id == $post->setting_id && $id == $post->main_setting_id && $product_pro_post->promotion_product_type_id == $post->product_type_id && $product_pro_post->range == $post->range) {
                    // dd($post->last_date);
                $post_date = Carbon::parse(date('Y-m-d',strtotime($post->date)));
                $post_last_date = Carbon::parse(date('Y-m-d',strtotime($post->last_date)));
                // dd($post_date);
                // dd(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d')))));
                // if($post_date->lte(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d'))))) && $post_last_date->gte(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d')))))) {
                //     dd('equal');
                // }else {
                //     dd('Not equal');
                // }

                // it base on date
                    // if($post->last_date) {
                    //     if($post_date->lte(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d'))))) && $post_last_date->gte(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d')))))) {
                    //         // dd($post);2021-08-21
                    //         array_push($current, $post);
                    //     }elseif($post_date->gt(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d'))))) && $post_last_date->gte(Carbon::parse(date('Y-m-d',strtotime(date('Y-m-d')))))) {
                    //         array_push($next, $post);
                    //     }else {
                    //         array_push($previous, $post);
                    //     }
                    //     // if($post->date <= date('Y-m-d') && $post->last_date >= date('Y-m-d')) {
                    //     //     dd($post);
                    //     //     array_push($current, $post);
                    //     // }elseif($post->date > date('Y-m-d') && $post->last_date >= date('Y-m-d')){
                    //     //     array_push($next, $post);
                    //     // }else {
                    //     //     array_push($previous, $post);
                    //     // }
                    // }else {
                    //     array_push($current, $post);
                    // }

                    // it base on duration
                    if($post->post_duration == 1) {
                        // dd($post);2021-08-21
                        array_push($current, $post);
                    }elseif($post->post_duration == 2) {
                        array_push($next, $post);
                    }else {
                        array_push($previous, $post);
                    }
                    
                }else {
                    continue;
                }
            }
            //  $tempArr_cu = array_unique(array_column($current, 'group_code'));
            // $product_pro_post_current = array_intersect_key($current, $tempArr_cu);
            // $product_pro_post['current_post'] = $product_pro_post_current; 
            $product_pro_post['current_post'] = $current; 
            
            // $tempArr_next = array_unique(array_column($next, 'group_code'));
            // $product_pro_post_next = array_intersect_key($current, $tempArr_next);
            // $product_pro_post['next_post'] = $product_pro_post_next;
            $product_pro_post['next_post'] = $next;
            $index = count($previous)-1;
            // dd($previous);
            // $product_pro_post['previous_post'] = (count($previous) > 0 ) ? $previous[count($previous)-1] : $previous;
            $product_pro_post['previous_post'] = $previous;
        }
        // dd($product_pro_posts);
        return $product_pro_posts;
    }

    public function settingTemplateForm($setting = null) {
        // die($setting);
        $store = Input::get('store');
        $settings = '';
        if($setting != 'null') {
            $settings = PromotionScheduleSettingMainModel::with(['settingSchedules' => function($q) {
                $q->where('is_deleted', 0)->orderBy('position', 'ASC');
            },'settingSchedules.type'])
            ->find($setting);
            foreach($settings->settingSchedules as $k => $s) {
                $sub_cates = GroupCategoryModel::with('subCategories')->whereIn('id', explode(",", $s->category_id))->first();
               
                $s->cate = $sub_cates;
            }
            $settings->social_ids = explode(',', $settings->social_ids);
            $settings->pages = explode(',', $settings->pages);
        }
        // dd($settings->toArray());
        // dd(session('access'));
        $staff = OmsUserModel::join("oms_user_group AS oug","oug.id","=","oms_user.user_group_id")->where('status', 1)
        ->where(function($query){
            $query->where('oms_user.user_access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%')->orWhere('oug.access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%');
        })
        // ->where('user_access','LIKE','%employee-performance\\\\/designer\\\\/save-daily-work%')
        ->get();

        $types_for_organic = PromotionTypeModel::where('status', 1)->where('product_status', 1)->orderBy('name', 'ASC')->get();
        $types_for_setting = PromotionTypeModel::where('status', 1)->orderBy('name', 'ASC')->get();
        $socials = SocialModel::where('status', 1)->get();
        $categories = GroupCategoryModel::all();
        // $categories = ProductGroupModel::groupBY(DB::raw("substr(name,1,1)"))->where('category_name', '!=', '')->get();
        // foreach($categories as $g) {
        //     if(strpos($g->name, '-') !== false) {
        //         $ar = explode('-', $g->name);
        //         $name = $ar[0];
        //     }else {
        //         $name = $g->name;
        //     }
        //     $g['name'] = $name;
        // }
        // dd($staff);
        // dd($categories);
        $users = OmsUserModel::where('status', 1)->whereIn('user_group_id',[12])->get();
        // echo "<pre>"; print_r($users->toArray()); die;
        $budget_types = BudgetType::all();    
        $ads_types = AdsTypeModel::where('status', 1)->where('type', 0)->get();
        $creative_types = AdsTypeModel::where('status', 1)->where('type', 1)->get();
        $activities = DutyListsModel::where('status', 1)->get();
        if(Input::get('type') == 1) {
            $page = ($setting != 'null') ? '.edit_setting_form' : '.create_setting_form';
        }else {
            $page = ($setting != 'null') ? '.paid_ads.edit_paid_setting_form' : '.paid_ads.create_paid_setting_form';
        }
        return view('productGroup'.''.$page.'')->with(compact('settings','types_for_organic','types_for_setting','store','socials','categories','users','budget_types','ads_types','creative_types','staff', 'activities'));
    }

    public function savsSetting(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'social' => 'required',
            'type.*' => 'required',
            'category.*' => 'required',
            // 'acitvity' => 'required'
        ]);
        if($request->postIng_type == 1) {
            // $this->validate($request, [
            //     'pages.*' => 'required'
            // ]);
            
            $exist = PromotionScheduleSettingMainModel::where('social_ids', implode(',', $request->social))->where('store_id', $request->store)->where('posting_type', $request->postIng_type)->where('id', '!=' ,$request->main_setting_id)->first();
        }else {
            $exist = null;
        }
// dd($request->budget);
        if(!$exist) {
            $main_setting = ($request->main_setting_id) ? PromotionScheduleSettingMainModel::find($request->main_setting_id) : new PromotionScheduleSettingMainModel();
            $socials = SocialModel::whereIn('id', $request->social)->get(['name'])->toArray();
            // $socials = $socials->toArray();
            $socialTitles = $this->creatSocialName($socials);
            $main_setting->posting_type = $request->postIng_type;
            $main_setting->store_id = $request->store;
            $main_setting->social_ids = implode(',', $request->social);
            $main_setting->title = $socialTitles;
            $main_setting->user_id = ($request->postIng_type == 2) ? $request->user : null;
            $main_setting->setting_name = ($request->postIng_type == 2) ? $request->setting_name : null;
            $main_setting->range = ($request->postIng_type == 2) ? $request->range : null;
            $main_setting->estimated_cost_per_ad_type = ($request->postIng_type == 2) ? $request->estimate_cost : 0;
            $main_setting->ads_type_id = ($request->postIng_type == 2) ? $request->ads_type : null;
            $main_setting->budget_type_id = ($request->postIng_type == 2) ? $request->budget_type : null;
            $main_setting->optimization_type = ($request->postIng_type == 2) ? $request->optimization_type : null;
            $main_setting->designing_person = ($request->postIng_type == 1) ? $request->designing : null;
            $main_setting->posting_person   = ($request->postIng_type == 1) ? $request->posting : null;
            $main_setting->duty_activity_id   = $request->acitvity;
            $main_setting->pages = $request->pages ? implode(',',$request->pages) : null;
            $main_setting->campaign_budget = (isset($request->campaign_budget) && $request->postIng_type == 2) ? $request->campaign_budget : null;	
            $request['social_ids'] = $main_setting->social_ids;
            // $this->validate($request, [
            //     'social_ids' => ($request->main_setting_id) ? 'unique:oms_promotion_schedule_setting_main,social_ids,'.$request->main_setting_id : 'unique:oms_promotion_schedule_setting_main',
            // ]);
            if($main_setting->save()) {
                $type = $request->type;
                $category = $request->category;
                $setting = $request->setting;
                $is_active = $request->is_active;
                $creative_types = $request->creative_types;
                if($request->postIng_type == 2) {
                    $ad_set_name = $request->ad_set_name;
                    $sub_cate = $request->sub_category;
                    // dd($type);
                    $budget = $request->budget;
                $ad_set_name = $request->ad_set_name;
                }else {
                 $time = $request->time;
                }
                // dd($setting);
                for($i = 0 ; $i < count($type); $i++) {
                    // dd($setting);
                    $cate_name = GroupCategoryModel::select('name')->whereIn('id', explode(",", $category[$i]))->get();
                    $c_name = [];
                    foreach($cate_name as $name) {
                        array_push($c_name, $name->name);
                    }   
                    $sub_name = null;
                    if($request->postIng_type == 2) {
                        if($sub_cate[$i]) {

                            $sub_name = GroupSubCategoryModel::select('name')->whereIn('id', explode(",", $sub_cate[$i]))->get()->pluck('name');
                            $sub_name = $sub_name->toArray();
                            
                        }else {
                            $sub_name = [];
                        }
                    }
                    $request_data = array(
                        'main_setting_id' => $main_setting->id,
                        'posting_type' => $request->postIng_type,
                        'schedule_time' => ($request->postIng_type == 1) ? $time[$i] : null,
                        'ad_set_name' => ($request->postIng_type == 2) ? $ad_set_name[$i] : null,
                        'promotion_product_type_id' => $type[$i],
                        'sub_category_id' => ($request->postIng_type == 2) ? $sub_cate[$i] : null,
                        'sub_category' => ($request->postIng_type == 2) ? implode(",",$sub_name) : null,
                        'range' => ($request->postIng_type == 2) ? $request->range : null,
                        'budget' => ($request->postIng_type == 2) ? $budget[$i] : null,
                        'creative_type_id' => ($request->postIng_type == 2) ? $creative_types[$i] : null,
                        'category' => implode(",", $c_name),
                        'category_id' => $category[$i],
                        'created_by' => session('user_id'),
                        'created_at' => date('Y-m-d H:i s'),
                        'is_active' => $is_active[$i],
                        'position' => $i,
                    );
                    // dd($request_data);
                    // if($request->postIng_type == 2) {
                    //     $settings = PromotionScheduleSettingModel::updateOrCreate(
                    //         ['id' => isset($setting[$i])],
                    //         $request_data
                    //     );
                    // }else {
                        $settings = PromotionScheduleSettingModel::updateOrCreate(
                            ['id' => @$setting[$i]],
                            $request_data
                        );
                    // }
                    
                    // $data[] = $request_data;
                }

                    $ba_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', $request->store)->where('posting_type', $request->postIng_type)->orderBy('id', 'DESC')->get();
                    // $df_promotion_main_setting = PromotionScheduleSettingMainModel::where('store_id', 2)->orderBy('id', 'DESC')->get();
                    $this->setCreatedMenuList($ba_promotion_main_setting, $request->store, $request->postIng_type);
                   
                // $setting_old = DB::table('oms_promotion_schedule_setting')->where('main_setting_id', $main_setting->id)->delete();
                //entry in setting posting pages table start
                if( $request->postIng_type == 1 ){
                  $request_pages   = $request->pages;
                  $page_posting_by = $request->page_posting_by;
                  // dd($request_pages);
                  if( is_array($request_pages) && count($request_pages) > 0 ){
                    foreach($request_pages as $key => $page){
                      //echo $page."<br>";
                      $check_page_data = PromotionScheduleSettingPostPageModel::where("main_setting_id",$main_setting->id)->where('page_name',$page)->first();
                      if($check_page_data){
                        continue;
                      }
                      if( $page != "" ){
                        $new_posting_pages = new PromotionScheduleSettingPostPageModel();
                        $new_posting_pages->main_setting_id  = $main_setting->id;
                        $new_posting_pages->page_name        = $page;
                        $new_posting_pages->designing_person =  0;
                        $new_posting_pages->posting_person   =  (@$page_posting_by[$key]) ? $page_posting_by[$key] : 0;
                        $new_posting_pages->save();
                      }
                    }
                  }
                }
                //entry in setting posting pages table end
                
                if($settings) {
                    
                $promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('store_id', $request->store)->where('posting_type', $request->postIng_type)->where('is_deleted', 0)->orderBy('id', 'DESC')->paginate(self::PER_PAGE);
                    // dd($promotion_main_setting);
                if($request->postIng_type == 1) {
                   $page = ".setting_ajax_template";
                }else {
                    $page = ".paid_setting_ajax_template";
                }
                return view('productGroup'. ''.$page.'')->with(compact('promotion_main_setting'));
                }else {
                    return response()->json([
                        'status' => false,
                        'meassge' => 'Setting saved successfully.'
                    ]);
                }
            }
        }else {
            return response()->json([
                'status' => 'exist',
                'meassge' => 'The social templete has already been taken.'
            ]);
        }
        
    }
    
}
