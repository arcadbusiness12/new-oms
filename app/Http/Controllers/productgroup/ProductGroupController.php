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
                        $history[] = PromotionProductPostModel::select('*')->where('group_id', $groupProduct->id)->where('store_id', $st->id)->where($whereClause)->orderBy('id','DESC')->first();
                    }
                    $ba_campaigns = PromotionProductPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 1)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
                    $df_campaigns = PromotionProductPostModel::with('campaign')->where('group_id', $groupProduct->id)->where('store_id', 2)->groupBy('campaign_id')->orderBy('date', 'DESC')->get();
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
                // dd($groupProducts);
                return view(SELF::VIEW_DIR. '.'.$page)->with(compact('groupProducts', 'types_for_organic','types_for_setting', 'socials', 'stores', 'categories','old_input','main_categories'));
                
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
             
            return view(SELF::VIEW_DIR. '.paidAds.promotionPaidAdTemplates')->with(compact('ba_paid_promotion_main_setting','df_paid_promotion_main_setting'));
        }
}
