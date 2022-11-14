<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductDescriptionModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductImageModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\InventoryManagement\OmsInventoryProductSpecialModel;
use App\Models\Oms\InventoryManagement\OmsSeoUrlModel;
use App\Models\Oms\InventoryManagement\ProductWeightClassModel;
use App\Models\Oms\OmsProductDiscountModel;
use App\Models\Oms\OmsProductRewardPointModel;
use App\Models\Oms\OmsStockStatus;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\PromotionTypeModel;
use App\Models\Oms\SocialModel;
use App\Models\Oms\storeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic;
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

    public function productListingDetails($product, $store) {
        // dd($store);
        $productList = OmsInventoryProductModel::with(['productGroups', 'productDescriptions' => function($q) use($store) {
            $q->where('store_id', $store);
        }, 'productDescriptions.store', 'productImages', 'productSpecials' => function($q) use($store) {
            $q->where('store_id', $store);
        }, 'seoUrls' => function($s) use($store) {
            $s->where('store_id', $store);
        },'productDiscounts' => function($pd) use($store) {
            $pd->where('store_id', $store);
        },'productRewardPoint'])->where('product_id', $product)->first();
        $store = storeModel::find($store);
        $weightClasses = ProductWeightClassModel::orderBy('title')->get();
        $stock_statuses = OmsStockStatus::where('language_id', 1)->get();
        return view(self::VIEW_DIR. '.productListDetails')->with(compact('productList', 'weightClasses', 'store', 'stock_statuses'));
    }

    public function ProductListing(Request $request) {
        $old_input = [];
        $whereClause = [];
        $productStatusWhereClause = [];
        // dd($request->all());    
        if($request->type) {
            $whereClause[] = array('product_type_id', $request->type);
        }
        // if($request->g_name) {
        //     $whereClause[] = array('name', 'LIKE', $request->g_name.'%');
        // }
        if($request->g_name) {
            $productStatusWhereClause[] = array('sku', 'LIKE', $request->g_name.'%');
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
        // dd($productStatusWhereClause);
        $groupProducts = ProductGroupModel::with(['products' => function($q) use($productStatusWhereClause) {
            $q->where($productStatusWhereClause);
        },'producType','category.subCategories','products.productDescriptions','products.productDescriptions.store'])->withCount('sizeChartValue')->where($whereClause)
        ->whereHas('products', function ($query) use($productStatusWhereClause) {
          $query->where($productStatusWhereClause);
        })->orderByRaw('SUBSTRING_INDEX(name,"-",1),CAST(SUBSTRING_INDEX(name,"-",-1) AS SIGNED INTEGER)')->paginate(20);
    //    dd($groupProducts->toArray()); 
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
        $description->name_ar = $request->product_name_ar;
        $description->product_description = $request->description;
        $description->product_description_ar = $request->description_ar;
        $description->meta_title = $request->meta_title;
        $description->meta_title_ar = $request->meta_title_ar;
        $description->meta_keywords = $request->meta_keyword;
        $description->meta_keywords_ar = $request->meta_keyword_ar;
        $description->meta_description = $request->meta_description;
        $description->meta_description_ar = $request->meta_description_ar;
        $description->product_tags = $request->product_tags;
        $description->product_tags_ar = $request->product_tags_ar;
        $description->price = $request->product_price;
        $description->store_id = $request->store;
        $description->product_id = $request->product_id;
        if($description->save()) {
            OmsSeoUrlModel::updateOrCreate(
                ['product_id' => $request->product_id, 'store_id' => $request->store],
                [
                    'seo_url' => $request->seourl,
                    'store_id' => $request->store,
                    'type' => 1
                ]
            );
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
    
    public function uploadCroppedImage(Request $request) {
        $folderPath = public_path('uploads/inventory_products/');
        $image_parts = explode(";base64,", $request->image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $imageName = md5(uniqid(rand(), true)) . '.png';
        $imageFullPath = $folderPath.$imageName;
        
        // $img = ImageManagerStatic::make($image_base64);
        // // $img->greyscale(5);
        // // $img->brightness(51);
        // $img->response('png');
        // // dd($image_base64);
        // $i = 'uploads/635ba7476c77a.png';
        // $bool = imagefilter($image_base64, IMG_FILTER_BRIGHTNESS, 51);
        if($request->id) {
            $folderPath = public_path('uploads/product_gallery/');
            $imageFullPath = $folderPath.$imageName; 
            $this->updateGalleryImage($image_base64, $imageFullPath, $imageName, $request->id);
        }else {
            file_put_contents($imageFullPath, $image_base64);
        
            $productImg = OmsInventoryProductModel::find($request->product);
            // remove old file from directory 
            File::delete($folderPath.$productImg->image);
            $productImg->image = $imageName;
            $productImg->update();
        }
        
        return response()->json([
            'status' => true
        ]);

    }

    public function uploadGalleryImages(Request $request) {
        // dd($request->all());
        if($request->product && count($request->files) > 0) {
            $images = [];
            foreach($request->file('files') as $k => $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = md5(uniqid(rand(), true)). '.' . $extension;
                $file->move(base_path('public/uploads/product_gallery/'), $fileName);
                
                $image = new OmsInventoryProductImageModel();
                $image->product_id = $request->product;
                $image->image = $fileName;
                $image->sort_order = $k;
                $image->save();
                $im = [
                    'id' => $image->id,
                    'url' => asset('uploads/product_gallery/'.$fileName)
                ];
                array_push($images, $im);
            }

            return response()->json([
                'status' => true,
                'images' => $images
            ]);
        }else {
            return response()->json([
                'status' => false,
                'error' => 'Somethings went wrong.'
            ]);
        }
    }
    
    private function updateGalleryImage($image, $path, $imageName, $id) {
        if($image) {
            file_put_contents($path, $image);
            $image = OmsInventoryProductImageModel::find($id);
            File::delete(public_path('uploads/product_gallery/').$image->image);
            $image->image = $imageName;
            if($image->update()) {
                return $image;
            }
        }
    }   

    public function removeGalleryImage(Request $request) {
        $image = OmsInventoryProductImageModel::find($request->image);
        if($image->delete()) {
            return response()->json([
                'status' => true
            ]);
        }else {
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function generateSeoUrl(Request $request) {
        $seo_url = \Str::slug($request->title);
        return response()->json([
            'status' => true,
            'url' => $seo_url
        ]);
    }

    public function saveSpecialPrice(Request $request) {
        $this->validate($request, [
            'store' => 'required|numeric',
            'product_id' => 'required',
            'sort_order.*' => 'required',
            'price.*' => 'required'
        ]);
        $sort_order = $request->sort_order;
        $price = $request->price;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if(count($sort_order)) {
            OmsInventoryProductSpecialModel::where('store_id', $request->store)->where('product_id', $request->product_id)->delete();
            for($i = 0; $i < count($sort_order); $i++) {
                $specialPrice = new OmsInventoryProductSpecialModel();
                $specialPrice->store_id = $request->store;
                $specialPrice->product_id = $request->product_id;
                $specialPrice->price = $price[$i];
                $specialPrice->date_start = $start_date[$i];
                $specialPrice->date_end = $end_date[$i];
                $specialPrice->sort_order = $sort_order[$i];
                $specialPrice->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Special price saved successfully.'
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Opps! Somothing went wrong, Try again.'
            ]);
        }
        

    }

    public function saveDiscountPrice(Request $request) {
        $this->validate($request, [
            'store' => 'required|numeric',
            'product_id' => 'required',
            'from_quantity.*' => 'required',
            'price.*' => 'required'
        ]);
        $sort_order = $request->sort_order;
        $price = $request->price;
        $from_quantity = $request->from_quantity;
        $to_quantity = $request->to_quantity;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if(count($sort_order)) {
            OmsProductDiscountModel::where('store_id', $request->store)->where('product_id', $request->product_id)->delete();
            for($i = 0; $i < count($sort_order); $i++) {
                $discountPrice = new OmsProductDiscountModel();
                $discountPrice->store_id = $request->store;
                $discountPrice->product_id = $request->product_id;
                $discountPrice->price = $price[$i];
                $discountPrice->from_quantity = $from_quantity[$i];
                $discountPrice->to_quantity = $to_quantity[$i];
                $discountPrice->date_start = $start_date[$i];
                $discountPrice->date_end = $end_date[$i];
                $discountPrice->sort_order = $sort_order[$i];
                $discountPrice->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Discount price saved successfully.'
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Opps! Somothing went wrong, Try again.'
            ]);
        }
        

    }

    public function saveRewardPoints(Request $request) {
       $this->validate($request, [
           'points' => 'required'
       ]);

        OmsProductRewardPointModel::updateOrCreate(
           ['product_id' => $request->product_id],
           ['points' => $request->points]
       );

       return response()->json([
           'status' => true,
           'message' => 'Reward points added sucessfully.'
       ]);

    }

    public function removeSpecialPrice(Request $request) {
        if($request->id) {
            $sPrice = OmsInventoryProductSpecialModel::find($request->id);
            if($sPrice->delete()) {
                return response()->json([
                    'status' => true
                ]);
            }else {
                return response()->json([
                    'status' => false
                ]);
            }
        }else {
            return response()->json([
                'status' => false
            ]);
        }
    }

    public function saveListingDataForm(Request $request) {
        $product = OmsInventoryProductModel::find($request->product_id);
        $product->model = $request->product_model;
        $product->stock_status_id = $request->stock_status;
        $product->minimum_quantity = $request->minimum_quantity;
        $product->date_available = $request->date_available;
        $product->location = $request->location;
        $product->weight = $request->weight;
        $product->weight_class_id = $request->weight_class;
        $product->sort_order = $request->sort_order;
        $product->status = $request->status;
        if($product->update()) {
            return response()->json([
                'status' => true
            ]);
        }else {
            return response()->json([
                'status' => false
            ]);
        }
    }
    // public function uploadCroppedImage1() {
    //     // dd("ok");
    //     $img = ImageManagerStatic::make('uploads/63593db93889e.png');
    //     // $img->greyscale(1.5);
    //     $img->contrast(65);
    //     dd($img->response('jpg'));
    //     return $img->response('jpg');
    // }
}
