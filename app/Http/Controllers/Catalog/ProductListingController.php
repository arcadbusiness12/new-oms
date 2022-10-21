<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductDescriptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\storeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class ProductListingController extends Controller
{
    const PER_PAGE = 20;
    const VIEW_DIR = 'Catalog';
    
    public function ProductListing_Old(Request $request) {
        $whereCluase = [];
        $whereCluaseRelation = [];
        if($request->product_name) {
            $whereCluaseRelation[] = array('mame', 'LIKE', '%'.$request->product_name.'%');   
        }
        if($request->price) {
            $whereCluaseRelation[] = array('price', $request->price);   
        }
        if($request->sku) {
            $whereCluase[] = array('sku', 'LIKE', '%'.$request->sku.'%');   
        }
        if($request->status) {
            $whereCluase[] = array('status', $request->status);   
        }
        $productLists = OmsInventoryProductModel::with(['productGroups', 'productDescriptions' => function($q) use($whereCluaseRelation) {
            $q->where($whereCluaseRelation);
        }, 'productImages'])->where($whereCluase)->paginate(self::PER_PAGE)->appends($request->all());
    //    dd($productLists->toArray()); 
        return view(self::VIEW_DIR. '.productLists')->with(compact('productLists'));
    }

    public function ProductListing(Request $request) {
        $old_input = [];
        $whereClause = [];
        $productStatusWhereClause = [];
        // dd(Input::all());    
        if($request->type) {
            $whereClause[] = array('product_type_id', $request->type);
        }
        if($request->g_name) {
            $whereClause[] = array('name', 'LIKE', $request->g_name.'%');
        }
        if($request->cate) {
            $whereClause[] = array('category_id', $request->cate);
        }
        if($request->sub_cates) {
            $whereClause[] = array('sub_category_id', $request->sub_cates);
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
    //    dd($productLists->toArray()); 
        $types_for_organic = PromotionTypeModel::where('status', 1)->where('product_status', 1)->get();
        $types_for_setting = PromotionTypeModel::where('status', 1)->get();
        $stores = storeModel::where('status', 1)->get();
        $socials = SocialModel::where('status', 1)->get();
        $main_categories = GroupCategoryModel::all();
        $categories = ProductGroupModel::select('*',DB::raw('GROUP_CONCAT(DISTINCT id) AS group_ids'))->groupBY(DB::raw("substr(name,1,1)"))->get();
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

        return view(self::VIEW_DIR. '.productLists')->with(compact('groupProducts', 'types_for_organic','types_for_setting', 'socials', 'stores', 'categories','old_input','main_categories'));
    }

    public function EditProductListing($product) {
        $productList = OmsInventoryProductModel::with(['productGroups', 'productDescriptions', 'productImages', 'productSpecials', 'seoUrls'])->where('product_id', $product)->first();
    //    dd($productLists->toArray()); 
        $stores = storeModel::with(['productDescriptions' => function($q) use($product) {
            $q->where('product_id', $product);
        }])->where('status', 1)->get();
        // dd($stores->toArray());
        return view(self::VIEW_DIR. '.editProductListing')->with(compact('productList', 'stores'));
    }

    public function saveListingDescription(Request $request) {
        $description = ($request->description_id) ? OmsInventoryProductDescriptionModel::find($request->description_id) : new OmsInventoryProductDescriptionModel();
        $description->name = $request->product_name;
        $description->product_description = $request->description;
        $description->meta_title = $request->meta_title;
        $description->meta_keywords = $request->meta_keyword;
        $description->meta_description = $request->meta_description;
        $description->product_tags = $request->product_tags;
        $description->price = $request->product_price;
        $description->store_id = $request->store;
        $description->product_id = $request->product_id;
        if($description->save()) {
            return response()->json([
                'status' => true,
                'mesge'  => 'Records saved successfully.'
            ]);
        }else {
            return response()->json([
                'status' => false,
                'mesge'  => 'Opps! Somethings went wrong.'
            ]);
        }
    }
}
