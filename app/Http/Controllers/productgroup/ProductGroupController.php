<?php

namespace App\Http\Controllers\productgroup;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsOptions;
use App\Models\Oms\OmsProductSizeChartValueModel;
use App\Models\Oms\OmsSizeChartOptionModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\OpenCart\Products\ProductsModel;
use App\Models\DressFairOpenCart\Products\ProductsModel as DFProductsModel;
use App\Models\Oms\PurchaseManagement\OmsPurchaseOrdersModel;
use Illuminate\Http\Request;
use App\Models\Oms\PromotionTypeModel;
use App\Models\Oms\storeModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\GroupSubCategoryModel;
use DB;
use App\Models\Oms\PromotionProductPostModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductDescriptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductSpecialModel;
use App\Models\Oms\PromotionScheduleSettingMainModel;
use App\Models\Oms\PromotionProductPaidPostModel;
use App\Models\Oms\PromotionSchedulePaidAdsCampaignTemplateModel;
use App\Models\Oms\PaidAdsCampaign;
use App\Models\Oms\PromotionScheduleSettingModel;
use Carbon\Carbon;
use Session;

class ProductGroupController extends Controller
{
    const VIEW_DIR = 'productGroup';
    const PER_PAGE = 20;
   
    public function addMainCategoryToGroup($cate, ProductGroupModel $group){
        $cate_name = GroupCategoryModel::with('subCategories')->find($cate);
        // dd($cate_name->subCategories->toArray());
        $group->category_id = $cate;
        $group->category_name = $cate_name->name;    
        if($group->update()) {
            return response()->json([
                'status' => true,
                'sub_cates' => $cate_name->subCategories->toArray(),
                'message' => 'Category assigned to group '.$group->name.' successfully'
            ]);
        }
    }
    
    public function addSubCategoryToGroup($cate, ProductGroupModel $group){
        $group->sub_category_id = $cate;
        if($group->update()) {
            return response()->json([
                'status' => true,
                'message' => 'Category assigned to group '.$group->name.' successfully'
            ]);
        }
    }

    public function changeGroupType($type, ProductGroupModel $group){
        $group->product_type_id = $type;
        if($group->update()) {
            return response()->json([
                'status' => true,
                'message' => 'Type added to group '.$group->name.' successfully'
            ]);
        }
    }

    public function groupChangeProductStatus(Request $request) {
        $update = OmsInventoryProductModel::where(['group_id'=>$request->group_id])->update(['status'=>$request->status]);
        if($update) {
            $msg = "Products updated successfully in inventory";
        }else {
            $msg = "Opps! Somethings went wrong.";
        }

     return response()->json(['status'=>true,'msgs'=>$msg]);
    }

    public function getProductSizeChart(Request $request) {
          $group_array = explode("_", $request->group_name);
        //   dd($group_array);
            $group = ProductGroupModel::with('products.ProductsSizes')->find($group_array[1]);
            // dd($group->products[0]->ProductsSizes);
            $option_ids = $group->products[0]->ProductsSizes->pluck('option_value_id')->toArray();
            // dd($option_ids);
            $optionid = 0;
            if($group->category_id) {
                $optionid = $group->products[0]->ProductsSizes[0]['option_id'];
                // if($group->category_name == 'Clothings') {
                //     $optionid = 11;           
                // }
                // if($group->category_name == 'Shoes') {
                //     $optionid = 14;           
                // }
                // if($group->category_name == 'Rings') {
                //     $optionid = 15;           
                // }
                // if($group->category_name == 'Bags') {
                //     $optionid = 19;           
                // }
                $sizeOptions = OmsOptions::with(['omsOptionsDetails' => function($q) use($option_ids) {
                    $q->whereIn('id', $option_ids)->orderBy('sort', 'asc');
                }])->find($optionid);
                // dd($sizeOptions);
                $topOptions = OmsSizeChartOptionModel::where('category_id', $group->category_id)->get();
                
                $groupid = $group_array[1];
                $groupname = $group_array[0];
                $sizeChartValues = OmsProductSizeChartValueModel::where('group_id', $groupid)->get();
                // dd($sizeChartValues);
                return view(self::VIEW_DIR. '.size_chart', compact('sizeOptions', 'topOptions','groupid', 'sizeChartValues', 'groupname'));
               
            }else {
                return response()->json([
                    'status' => 'notconnect',
                    'mesge'  => 'Category is not connect, first connect cstegory'
                ]);
            }
        }

        public function updateProductSizeChart(Request $request){
            $cmss = [];
            OmsProductSizeChartValueModel::where('group_id', $request->groupid)->delete();
            foreach($request->cm as $k => $cms) {
               foreach($cms as $key => $cm) {
                   if($cm[0]) {
                    $inseData = [
                        'option_id' => $k,
                        'size_chart_option_id' => $key,
                        'group_id' => $request->groupid,
                        'group_name' => $request->groupname,
                        'value' => $cm[0],
                        'cm_inch' => 'cm'
                    ];
                    // CM entry
                    OmsProductSizeChartValueModel::create($inseData);
                   }
                   
               }
               
            }
            foreach($request->inch as $k => $inches) {
               foreach($inches as $key => $inch) {
                   if($inch[0]) {
                    $inseData = [
                        'option_id' => $k,
                        'size_chart_option_id' => $key,
                        'group_id' => $request->groupid,
                        'group_name' => $request->groupname,
                        'value' => $inch[0],
                        'cm_inch' => 'inch'
                    ];
                    // Inches Entry
                    OmsProductSizeChartValueModel::create($inseData);
                   }
                   
               }
               
            }
    
            $products = OmsInventoryProductModel::where('group_id', $request->groupid)->pluck('sku')->toArray();
            // ProductsModel::whereIn('sku', $products)->update(['oms_size_chart' => 1]);
            // DFProductsModel::WhereIn('sku', $products)->update(['oms_size_chart' => 1]);
            
            return response()->json([
                'status' => true
            ]);
          }

          public function productGroup(Request $request, $page) {
                $old_input = [];
                $whereClause = [];
                $productStatusWhereClause = [];
                // dd($request->all()); 
                if($request->type) {
                    $whereClause[] = array('product_type_id', $request->type);
                }
                if($request->g_name) {
                    $name = $request->g_name;
                    $whereClause[] = array('name', 'LIKE', $name.'%');
                }
                if($request->cate) {
                    $cate = $request->cate;
                    $whereClause[] = array('category_id', $cate);
                }
                if($request->sub_cates) {
                    $sub_cate = $request->sub_cates;
                    $whereClause[] = array('sub_category_id', $sub_cate);
                }
                
                if($request->product_status == 'no') {
                    $productStatusWhereClause[] = array('status', 0);
                }else{
                    $productStatusWhereClause[] = array('status', 1);
                }

                $groupProducts = ProductGroupModel::with(['products' => function($q) use($productStatusWhereClause) {
                    $q->where($productStatusWhereClause);
                },'producType','category.subCategories'])->where($whereClause)
                ->whereHas('products', function ($query) use($productStatusWhereClause) {
                  $query->where($productStatusWhereClause);
                })->orderByRaw('SUBSTRING_INDEX(name,"-",1),CAST(SUBSTRING_INDEX(name,"-",-1) AS SIGNED INTEGER)')->paginate(20);
                $types_for_organic = PromotionTypeModel::where('status', 1)->where('product_status', 1)->get();
                $types_for_setting = PromotionTypeModel::where('status', 1)->get();
                $stores = storeModel::where('status', 1)->get();
                $socials = SocialModel::where('status', 1)->get();
                $main_categories = GroupCategoryModel::all();
                $categories = ProductGroupModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT id) AS group_ids'))->groupBY(DB::raw("substr(name,1,1)"))->get();
                foreach($groupProducts as $groupProduct) {
                    $st_history = [];
                    $whereClause = [];
                    if($page == 'paid_ads') {
                        $whereClause[] = array('posting_type', 2);
                    }elseif($page == 'organic_posts') {
                        $whereClause[] = array('posting_type', 1);
                    }else {
                        $whereClause = [];
                    }
                    $stores = storeModel::where('status', 1)->get();
                    $hs = [];
                    $history = [];
                    foreach($stores as $st) {
                        $history[] = PromotionProductPaidPostModel::select('*')->where('group_id', $groupProduct->id)->where('store_id', $st->id)->where($whereClause)->orderBy('id','DESC')->first();
                    }
                    $ba_campaigns = PromotionProductPaidPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 1)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
                    $df_campaigns = PromotionProductPaidPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 2)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
                    $history = array_filter($history);
                    if(count($history)) {
                        $history = $this->checkCampaignsHistory($history, $ba_campaigns, $df_campaigns);
                        array_push($st_history, $history);
                     
                    }
                    $groupProduct['histories'] = $st_history;
                    $groupProduct->purchase_history = DB::table('oms_purchase_order AS opo')
                                                  ->select('opo.order_id',DB::raw('GROUP_CONCAT(opop.model) AS all_model'),'opos.name AS current_status')
                                                  ->join("oms_purchase_order_product AS opop","opo.order_id","=","opop.order_id")
                                                  ->leftjoin("oms_purchase_order_status AS opos","opos.order_status_id","=","opo.order_status_id")
                                                  ->leftjoin("oms_purchase_shipped_order AS opso","opso.order_id","=","opo.order_id")
                                                  ->whereIn("opop.model",function($query) use ($groupProduct){
                                                    $query->select('sku')->from("oms_inventory_product")->where("group_id",$groupProduct->id);
                                                  })
                                                  ->where(function($query){
                                                    $query->whereNotIn("opso.status",[5,3])->orWhereNull("opso.status");
                                                  })
                                                  
                                                  ->whereNotIn('opo.order_status_id',[6,7])
                                                  ->groupBy("opo.order_id")
                                                  ->get();
                }
                foreach($categories as $g) {
                    if(strpos($g->name, '-') !== false) {
                        $ar = explode('-', $g->name);
                        $name = $ar[0];
                    }else {
                        $name = $g->name;
                    }
                    $g['name'] = $name;
                }
                $old_input = $request->all();
                if($page == 'productList') {
                    $page = 'paidAds.'.$page;
                }
                dd($groupProducts->toArray);
                return view(SELF::VIEW_DIR. '.'.$page)->with(compact('groupProducts', 'types_for_organic','types_for_setting', 'socials', 'stores', 'categories','old_input','main_categories'));
                
          }

          public function producpaidAdProductListtGroup(Request $request) {
            $old_input = [];
            $whereClause = [];
            $productStatusWhereClause = [];
            // dd($request->all()); 
            if($request->type) {
                $whereClause[] = array('product_type_id', $request->type);
            }
            if($request->g_name) {
                $name = $request->g_name;
                $whereClause[] = array('name', 'LIKE', $name.'%');
            }
            if($request->cate) {
                $cate = $request->cate;
                $whereClause[] = array('category_id', $cate);
            }
            if($request->sub_cates) {
                $sub_cate = $request->sub_cates;
                $whereClause[] = array('sub_category_id', $sub_cate);
            }
            
            if($request->product_status == 'no') {
                $productStatusWhereClause[] = array('status', 0);
            }else{
                $productStatusWhereClause[] = array('status', 1);
            }

            $groupProducts = ProductGroupModel::with(['products' => function($q) use($productStatusWhereClause) {
                $q->where($productStatusWhereClause);
            },'producType','category.subCategories'])->where($whereClause)
            ->whereHas('products', function ($query) use($productStatusWhereClause) {
              $query->where($productStatusWhereClause);
            })->orderByRaw('SUBSTRING_INDEX(name,"-",1),CAST(SUBSTRING_INDEX(name,"-",-1) AS SIGNED INTEGER)')->paginate(20);
            $types_for_organic = PromotionTypeModel::where('status', 1)->where('product_status', 1)->get();
            $types_for_setting = PromotionTypeModel::where('status', 1)->get();
            $stores = storeModel::where('status', 1)->get();
            $socials = SocialModel::where('status', 1)->get();
            $main_categories = GroupCategoryModel::all();
            $categories = ProductGroupModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT id) AS group_ids'))->groupBY(DB::raw("substr(name,1,1)"))->get();
            foreach($groupProducts as $groupProduct) {
                $st_history = [];
                $whereClause = [];
                $whereClause[] = array('posting_type', 2);
                $stores = storeModel::where('status', 1)->get();
                $hs = [];
                $history = [];
                foreach($stores as $st) {
                    $history[] = PromotionProductPaidPostModel::select('*')->where('group_id', $groupProduct->id)->where('store_id', $st->id)->where($whereClause)->orderBy('id','DESC')->first();
                }
                $ba_campaigns = PromotionProductPaidPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 1)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
                $df_campaigns = PromotionProductPaidPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 2)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
                $history = array_filter($history);
                if(count($history)) {
                    $history = $this->checkCampaignsHistory($history, $ba_campaigns, $df_campaigns);
                    array_push($st_history, $history);
                 
                }
                $groupProduct['histories'] = $st_history;
                $groupProduct->purchase_history = DB::table('oms_purchase_order AS opo')
                                              ->select('opo.order_id',DB::raw('GROUP_CONCAT(opop.model) AS all_model'),'opos.name AS current_status')
                                              ->join("oms_purchase_order_product AS opop","opo.order_id","=","opop.order_id")
                                              ->leftjoin("oms_purchase_order_status AS opos","opos.order_status_id","=","opo.order_status_id")
                                              ->leftjoin("oms_purchase_shipped_order AS opso","opso.order_id","=","opo.order_id")
                                              ->whereIn("opop.model",function($query) use ($groupProduct){
                                                $query->select('sku')->from("oms_inventory_product")->where("group_id",$groupProduct->id);
                                              })
                                              ->where(function($query){
                                                $query->whereNotIn("opso.status",[5,3])->orWhereNull("opso.status");
                                              })
                                              
                                              ->whereNotIn('opo.order_status_id',[6,7])
                                              ->groupBy("opo.order_id")
                                              ->get();
                $sizeChartValues = OmsProductSizeChartValueModel::where('group_id', $groupProduct->id)->get();
                if(count($sizeChartValues) > 0) {
                    $groupProduct['size_chart'] = 1;
                }else {
                    $groupProduct['size_chart'] = 0;
                }
            }
            foreach($categories as $g) {
                if(strpos($g->name, '-') !== false) {
                    $ar = explode('-', $g->name);
                    $name = $ar[0];
                }else {
                    $name = $g->name;
                }
                $g['name'] = $name;
            }
            $old_input = $request->all();
            // foreach($groupProducts as $p) {
            //     foreach($p->histories as $htries) {
            //         dd($htries);
            //     }
            // }
            // dd($groupProducts->toArray());
            return view(SELF::VIEW_DIR. '.paidAds.productList')->with(compact('groupProducts', 'types_for_organic','types_for_setting', 'socials', 'stores', 'categories','old_input','main_categories'));
          }

          private function checkCampaignsHistory($histories, $ba_campaigns, $df_campaigns) {
            $histories = collect($histories); // convert array into laravel collection
            foreach($histories as $k => $h) {
                if($h->store_id == 1) {
                    $h['campaigns'] = $ba_campaigns;
                    // dd($h1);
                }
                if($h->store_id == 2) {
                    $h['campaigns'] = $df_campaigns;
                }
            }
            return $histories;
        }

        public function sitePrice(Request $request) {
            $action = null;
            $update = false;
            if($request->isMethod('post')) {
                $update = $this->updateSitePrices($request);
                    return \response()->json([
                        'status' => $update
                    ]);
            }
            $whereClause = [];
            if($request->action && $request->action == 'deals') {
                $whereClause[] = array('ba_deals', 1);
            }
            $group_data = ProductGroupModel::with(["products"=>function($query) use($whereClause){
                // $query->select('group_id','sku')->where('status',1);
                $query->where($whereClause)->select('group_id','sku');
            }])->select('id','name')->where("name",$request->group_name)->first();
            if( $group_data->count() > 0 ){
                $products_data = $group_data->products->toArray();
                $products_skus  = array_column($products_data,'sku');
            }else{
                echo "No Sku found in this group.";
                return;
            }
            
            $stores = storeModel::get();
                
            foreach($stores as $store) {
                $products = OmsInventoryProductModel::with(['productDescriptions' => function($dq) use($store) {
                    $dq->where('store_id', $store->id);
                }, 'productSpecials' => function($ds) use($store) {
                    $ds->where('store_id', $store->id);
                }, 'ProductsSizes'])->whereHas('productDescriptions', function($q) use($store) {
                    $q->where('store_id', $store->id);
                })->whereIn('sku', $products_skus)->get();
                $store['products'] = $products;
            } 
            $action = $request->action;
            return view(self::VIEW_DIR.'.update_site_prices',compact('stores','action'));
        }
        
        public function sitePromotionPrice(Request $request) {
            // dd($request->all());
            if($request->isMethod('post')) {
               $up = $this->updateSitePromotionPricesQuery($request);
               if(!$up) {
                   return \response()->json([
                       'status' => 'updateError',
                       'meassage' => 'Please select atleast one product.'
                   ]);
               }
               if($up) {
                return \response()->json([
                    'status' => 'updated',
                    'meassage' => 'Offer prices updated successfully.'
                ]);
            }
            }
            $group_data = ProductGroupModel::with(["products"=>function($query){
                $query->select('group_id','sku')->where('status',1);
              }])->select('id','name')->where("name",$request->promotion_group_name)->first();
              if( $group_data->count() > 0 ){
                $products_data = $group_data->products->toArray();
                $products_skus  = array_column($products_data,'sku');
              }else{
                echo "No Sku found in this group.";
                return;
              }
            $stores = storeModel::get();
                
            foreach($stores as $store) {
                $products = OmsInventoryProductModel::with(['productDescriptions' => function($dq) use($store) {
                    $dq->where('store_id', $store->id);
                }, 'productSpecials' => function($ds) use($store) {
                    $ds->where('store_id', $store->id)->orderBy('date_end','DESC');
                }, 'ProductsSizes'])->whereHas('productDescriptions', function($q) use($store) {
                    $q->where('store_id', $store->id);
                })->whereIn('sku', $products_skus)->get();
                $store['products'] = $products;
            } 
            // dd($stores->toArray());
            return view(self::VIEW_DIR.'.updateSitePricesPromotion',compact('stores'));
        }

        private function updateSitePrices($request) {
            // dd($request->all());
            $stores = $request->store;
            $dataPrices = $request->data_price;
            $spPrice = $request->sp_price;
            $offerPrice = $request->offer_price;
            foreach($stores as $store) {
                
                foreach($dataPrices[$store] as $product => $price) {
                    OmsInventoryProductDescriptionModel::where('product_id', $product)->where('store_id', $store)->update(['price'=>$price]);
                }
                
                foreach($spPrice[$store] as $product => $price) {
                    if($price !="" && $price !=null) {
                        // dd($product);
                        OmsInventoryProductSpecialModel::updateOrCreate(
                            ['product_id' => $product,'store_id' => $store,'date_start'=>'0000-00-00','date_end'=>'0000-00-00'],
                            ['price' => $price, 'store_id' => $store]
                        );
                    }
                    
                }
            }
            return true;

        }

        private function updateSitePromotionPricesQuery($request) {
            $update_products = $request->update_flag;
            $product_ids = $request->product_id;
            $offer_prices = $request->offer_price;
            $offer_start_date = $request->offer_start_date;
            $offer_end_date = $request->offer_end_date;
            if(isset($update_products) && count($update_products) > 0) {
                foreach($update_products as $store => $special_ids) {
                    foreach($special_ids as $special_id => $ac) {
                        OmsInventoryProductSpecialModel::updateOrCreate(
                            ['id' => $special_id,'store_id' => $store],
                            ['date_start' => $offer_start_date[$special_id], 'date_end' => $offer_end_date[$special_id], 'price' => $offer_prices[$special_id], 'store_id' => $store, 'product_id' => $product_ids[$special_id]]
                        );
                    }
                }
                return true;
            }else {
                return false;
            }
        }

        public function promotionPaidAdsTemplateSettings($type) {
            $ba_paid_promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('posting_type', $type)->where('store_id', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->paginate(12);
            $df_paid_promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('posting_type', $type)->where('store_id', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
             
            return view(SELF::VIEW_DIR. '.promotionSetting.paidAds.promotionPaidAdTemplates')->with(compact('ba_paid_promotion_main_setting','df_paid_promotion_main_setting'));
        }

        public function getSettingTemplate($store, $group, $type, $select_cate) {
            $product_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with('type')->get();
            $socials = SocialModel::where('status', 1)->get();
            $pro_posts = []; //real pro_posts is commented
            // $pro_posts = PromotionProductPostModel::with('group')->where('store_id', $store)->get();
            $days = []; //real days is commented
            $setting_templates = PromotionScheduleSettingMainModel::where('store_id', $store)->where('is_deleted', 0)->get();
            foreach($setting_templates as $temp) {
                if($temp->end_date) {
                    $dt      = Carbon::parse($temp->end_date);
                    $future  = Carbon::parse(date('Y-m-d'));
                   $temp['remain_days'] = $dt->diffInDays($future);
                }else{
                    $temp['remain_days'] = null;
                }
                
            }
            return  view(SELF::VIEW_DIR. '.paidAds.settingTemplatePopup')->with(compact('product_pro_posts', 'days', 'store','group','type', 'socials','pro_posts','setting_templates','select_cate'));
        }

        public function getTemplateSchedules($id, $type, $cate, $store, $selected_cate) {
            $action = 'current';
            $whereClause = [];
            $templates = PromotionScheduleSettingMainModel::find($id);
            $template_socials = explode(',', $templates->social_ids);
            $category = ProductGroupModel::find($cate);
            // dd($type);
            $orWhereClause = [];
            $whereClause1 = [];
            $catWhereClause = [];
            $history_blocks = null;
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
                // dd($category->category_name);
                $catWhereClause[] = array('category', 'LIKE', '%'.$category->category_name.'%');
                
            }
            // $product_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with('type','subCategory')
            //                                                                     ->where('main_setting_id', $id)
            //                                                                     ->where($whereClause)
            //                                                                     ->where($catWhereClause)
            //                                                                     ->get();

            $socials = SocialModel::where('status', 1)->get();
            $pro_posts = PromotionProductPaidPostModel::with('group')->where('store_id', $store)->get();
            $product_pro_posts = [];
            $campaign_current = PaidAdsCampaign::with(['chatResults' => function($q) {
                $q->orderBy('date','DESC')->first();
              },'chatResults.user'])->where('main_setting_id', $id)->where('status', 1)->first();
              
            if($campaign_current) {
                $product_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with(['productPostes','type'])->where('main_setting_id', $id)
                                                            ->where('campaign_id', $campaign_current->id)
                                                            ->where('category', 'LIKE', '%'.$category->category_name.'%')
                                                            ->get();
            }
            $product_pro_posts_next = [];
            $campaign_next = PaidAdsCampaign::with(['schedulechatResults' => function($q) {
                $q->orderBy('date','DESC')->first();
              }])->where('main_setting_id', $id)->where('status', 2)->first();
            //   dd($campaign_next);
            if($campaign_next) {
                $product_pro_posts_next = PromotionSchedulePaidAdsCampaignTemplateModel::with(['productPostes','type'])->where('main_setting_id', $id)
                                                            ->where('campaign_id', $campaign_next->id)
                                                            ->where('category', 'LIKE', '%'.$category->category_name.'%')
                                                            ->get();
            }

              if(strpos($category->name, '-') !== false) {
                    $ar = explode('-', $category->name);
                    $name = $ar[0];
                }else {
                    $name = $category->name;
                }
                $category['name'] = $name;
                
                // $data = $this->getPaidWorkReports($id,$store,2, $action, $cate);

                $templates = $templates;
                $template_socials = $template_socials;
                $store = $store;
                $action = $action;
                $history_blocks = $history_blocks;
                $group_id = $cate;
                $post_type = 2;
            return  view(SELF::VIEW_DIR. '.paidAds.schedulesTemplate')->with(compact('product_pro_posts', 'product_pro_posts_next', 'socials', 'templates', 'template_socials', 'store', 'id', 'action', 'post_type', 'history_blocks','group_id','selected_cate','campaign_current','campaign_next','category'));
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
                    // dd($category->category_name);
                    $catWhereClause[] = array('category', 'LIKE', '%'.$category->category_name.'%');
                    
                }
                $product_pro_posts = PromotionSchedulePaidAdsCampaignTemplateModel::with('type','subCategory')
                                                                                    ->where('main_setting_id', $id)
                                                                                    ->where($whereClause)
                                                                                    ->where($catWhereClause)
                                                                                    ->where($subcWhereClause)
                                                                                    ->get();
                // dd($product_pro_posts);
            $socials = SocialModel::where('status', 1)->get();
            $pro_posts = PromotionProductPaidPostModel::with('group','chatHistories')->where('store_id', $store)
                                                        ->where('main_setting_id', $id)
                                                        ->where($whereClause1)
                                                        ->orWhere($orWhereClause)
                                                        ->get();
            
            // dd($pro_posts->toArray());
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
                dd($product_pro_posts);
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
                // dd($schedule_date);
                return  $schedule_date;
                // view(SELF::VIEW_DIR. '.template_schedules')->with(compact('product_pro_posts', 'pro_posts', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day', 'action', 'post_type', 'history_blocks'));
            }else {
                if($post_type == 1) {
                    $directory = '.ba_df_work_reports';
                }else {
                    $product_pro_posts = $this->transformPaidPosting($product_pro_posts, $pro_posts, $id);
                    dd($product_pro_posts);
                    $directory = '.paid_ads';
                }
                // dd($pro_posts);
                $ad_types = AdsTypeModel::with(['paidAdsSettings'=>function($query){
                    $query->where('is_deleted',0);
                  }])->whereHas('paidAdsSettings')->get();
                //   dd($ad_types->toArray());
                return  view(SELF::VIEW_DIR. $directory.'.ba_work_report')->with(compact('product_pro_posts', 'pro_posts', 'socials', 'templates', 'template_socials', 'store', 'id','next_seven_day', 'action', 'post_type', 'history_blocks','ad_types', 'campaign_current', 'campaign_next'));
            }
            // dd($directory);
            
        }
    
        public function getHistoryBlocks($id, $store, $post_type, $whereClause1) {
            // dd($whereClause1);
            $history_blocks = PromotionProductPaidPostModel::with('group')->where('store_id', $store)
                                                        ->where('main_setting_id', $id)
                                                        ->where($whereClause1)
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
                    // echo $product_pro_post->id ."=". $post->setting_id . "<br>";
                    
                    if($product_pro_post->id == $post->setting_id && $id == $post->main_setting_id && $product_pro_post->promotion_product_type_id == $post->product_type_id && $product_pro_post->range == $post->range) {
                        // dd($post->last_date);
                    $post_date = Carbon::parse(date('Y-m-d',strtotime($post->date)));
                    $post_last_date = Carbon::parse(date('Y-m-d',strtotime($post->last_date)));
                    
                        // it base on duration
                        if($post->post_duration == 1) {
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
                $product_pro_post['current_post'] = $current; 
                
                $product_pro_post['next_post'] = $next;
                $index = count($previous)-1;
                
                $product_pro_post['previous_post'] = $previous;
            }
            
            return $product_pro_posts;
        }



        public function promotionOrganicSettings(Request $request, $type) {
            // dd($request->all());
            $whereClause = 0;
            if($request->status) {
                $whereClause= $request->status;
            }
            // dd($whereClause);
            $types_for_organic = PromotionTypeModel::where('status', 1)->where('product_status', 1)->orderBy('name', 'ASC')->get();
            $types_for_setting = PromotionTypeModel::where('status', 1)->orderBy('name', 'ASC')->get();
            $stores = storeModel::where('status', 1)->get();
            $socials = SocialModel::where('status', 1)->get();
            $categories = ProductGroupModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT id) AS group_ids'))->groupBY(DB::raw("substr(name,1,2)"))->get();
            $promotion_orginaic_setting = PromotionScheduleSettingModel::where('is_deleted', 0)->get();
            $promotion_paid_ad_setting = PromotionScheduleSettingModel::where('is_deleted', 0)->get();
            $mainCategories = GroupCategoryModel::all();
            $ba_paid_promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('posting_type', $type)->where('store_id', 1)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
            $df_promotion_main_setting = PromotionScheduleSettingMainModel::with('adsType','user')->where('posting_type', $type)->where('store_id', 2)->where('is_deleted', 0)->orderBy('id', 'DESC')->get();
            // dd($ba_paid_promotion_main_setting);
           
            foreach($categories as $g) {
                if(strpos($g->name, '-') !== false) {
                    $ar = explode('-', $g->name);
                    $name = $ar[0];
                }else {
                    $name = $g->name;
                }
                $g['name'] = $name;
            }
            if($type == 1) {
                $page = '.promotion_organic_settings';
            }else {
                $page = '.paid_ads.promotion_paid_ads_settings'; 
            }
            $old_input = $whereClause;
            // dd($request->status);
            return view(SELF::VIEW_DIR. $page)->with(compact('types_for_organic','types_for_setting', 'socials', 'stores', 'promotion_orginaic_setting', 'promotion_paid_ad_setting','ba_paid_promotion_main_setting','df_promotion_main_setting','categories','mainCategories', 'old_input'));
        }

        
}
