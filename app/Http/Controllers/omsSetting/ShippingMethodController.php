<?php

namespace App\Http\Controllers\omsSetting;

use App\Http\Controllers\Controller;
use App\Models\Oms\CountryModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\Oms\storeModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        return view(self::VIEW_DIR. '.shippingMethods')->with(compact('methods', 'countries', 'stores'));
    }

    public function addShippingMethods(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'store' => 'required',
            'name' => 'required',Rule::unique('shipping_methods', 'store_id')->ignore($request->store),
            'country' => 'required'
        ]);
        
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
}
