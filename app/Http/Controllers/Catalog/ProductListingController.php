<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
use App\Models\Oms\storeModel;
use Illuminate\Http\Request;
use Symfony\Component\Console\Input\Input;

class ProductListingController extends Controller
{
    const PER_PAGE = 20;
    const VIEW_DIR = 'Catalog';
    public function ProductListing(Request $request) {
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
        }, 'productImages'])->where($whereCluase)->paginate(self::VIEW_DIR)->appends($request->all());
    //    dd($productLists->toArray()); 
        return view(self::VIEW_DIR. '.productLists')->with(compact('productLists'));
    }

    public function EditProductListing($product) {
        $productLists = OmsInventoryProductModel::with(['productGroups', 'productDescriptions', 'productImages', 'productSpecials', 'seoUrls'])->where('product_id', $product)->first();
    //    dd($productLists->toArray()); 
        $stores = storeModel::where('status', 1)->get();
        return view(self::VIEW_DIR. '.editProductListing')->with(compact('productLists', 'stores'));
    }
}
