<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Oms\InventoryManagement\OmsInventoryProductModel;
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
        }, 'productImages', 'productSpecials', 'seoUrls'])->where($whereCluase)->paginate()->appends($request->all());
        
        return view(self::VIEW_DIR. '.productLists')->with(compact('productLists'));
    }
}
