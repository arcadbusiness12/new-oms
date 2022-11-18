<?php

namespace App\Http\Controllers\omsSetting;

use App\Http\Controllers\Controller;
use App\Models\Oms\CountryModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\Oms\storeModel;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    const VIEW_DIR = 'oms_setting';
    const PER_PAGE = 20;
    public function __construct()
    {
        
    }

    public function shippingMethods() {
        $methods = ShippingMethodModel::with(['country', 'store'])->get();
        // dd($methods);
        $stores = storeModel::where('status',1)->get();
        $countries = CountryModel::where('status',1)->get();
        $freeShippingAmount = OmsSettingsModel::select(['setting_id','value'])->where('code', 'free_shipping_amount')->first();
        // dd($freeShippingAmount);
        return view(self::VIEW_DIR. '.shippingMethods')->with(compact('methods', 'countries', 'stores', 'freeShippingAmount'));
    }

    public function addShippingMethods(Request $request) {
        $this->validate($request, [
            'store' => 'required',
            'name' => 'required',
            'country' => 'required'
        ]);
        $exist = ShippingMethodModel::where('name', $request->name)->where('store_id', $request->store)->exists();
        if($exist) {
            $store = storeModel::find($request->store);
            return redirect()->back()->with('error', 'The shipping method name "'. $request->name. '" already exist with same '. $store->name. ' store');
        }
        ShippingMethodModel::updateOrCreate(
            ['id' => $request->shipping_method_id],
            [
                'name' => $request->name,
                'amount'  => $request->amount,
                'country_id' => $request->country,
                'store_id' => $request->store
            ]
            );

        return redirect()->back()->with('success', 'Shipping Method added succussfully.');
    }

    public function getCountries() {
        $countries = CountryModel::where('status',1)->get();
        return response()->json([
            'countries' => $countries 
        ]);
    }

    public function AddFreeShippingSetting(Request $request) {
        $this->validate($request, [
            'free_shipping_amount' => 'required'
        ]);
        OmsSettingsModel::updateOrCreate(
            ['setting_id' => $request->freeShipping_id],
            [
                'code' => 'free_shipping_amount',
                'key' => 'free_shipping_on',
                'value' => $request->free_shipping_amount,
                'serialize' => 0
            ]
            );
            return redirect()->back()->with('success', 'Free shipping amount set.');
            
    }
}
