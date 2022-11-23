<?php

namespace App\Http\Controllers\omsSetting;

use App\Http\Controllers\Controller;
use App\Models\Models\Oms\ShippingMethodWeightAmount;
use App\Models\Oms\Country;
use App\Models\Oms\CountryModel;
use App\Models\Oms\Localisation\GeoZoneModel;
use App\Models\Oms\OmsSettingsModel;
use App\Models\Oms\ShippingMethodModel;
use App\Models\Oms\ShippingMethodWeightAmount as OmsShippingMethodWeightAmount;
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
        $methods = ShippingMethodModel::with(['geoZone', 'store'])->get();
        // dd($methods);
        $freeShippingAmount = OmsSettingsModel::select(['setting_id','value'])->where('code', 'free_shipping_amount')->first();
        // dd($freeShippingAmount);
        return view(self::VIEW_DIR. '.shippingMethods')->with(compact('methods','freeShippingAmount'));
    }

    public function addShippingMethods() {
        $stores = storeModel::with('shippingMethods')->where('status',1)->get();
        $countries = Country::where('status',1)->get();
        $geoZones = GeoZoneModel::all();
        return view(self::VIEW_DIR. '.addShippingMethod')->with(compact('countries', 'stores','geoZones'));
    }

    public function saveShippingMethods(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'store_id' => 'required',
            'name.*' => 'required',
            'geo_zone.*' => 'required'
        ]);
        $whereCluase = [];
        $names = $request->name;
        $geoZones = $request->geo_zone;
        $amount = $request->amount;
        $weights = $request->weight;
        $weigghtAmounts = $request->amount_weight;
        foreach($names as $k => $name) { 
            if($request->shipping_method_id) {
                $whereCluase[] = array('id', '!=', $request->shipping_method_id);
            }
            $exist = ShippingMethodModel::where('name', $name)->where('store_id', $request->store_id)->where($whereCluase)->exists();
            if($exist) {
                $store = storeModel::find($request->store_id);
                return response()->json([
                    'status' => false,
                    'mesge' => 'The shipping method name "'. $name. '" already exist with same '. $store->name. ' store'
                ]);
            }
            $shippingMethod = ShippingMethodModel::updateOrCreate(
                ['id' => $request->shipping_method_id],
                [
                    'name' => $name,
                    'amount'  => $amount[$k] ? $amount[$k] : 0,
                    'geo_zone_id' => $geoZones[$k],
                    'store_id' => $request->store_id,
                    'shipping_type' => $request->shipping_type,
                    'additional_amount' => $request->additional_amount[$k]
                ]
                );
            if($request->shipping_method_id) {
                OmsShippingMethodWeightAmount::where('shipping_method_id', $shippingMethod->id)->delete();
            }
            if(isset($weights[$k]) && $request->shipping_type == 2) {
                $this->validate($request, [
                    'amount_weight' => 'required',
                ]);
                foreach($weights[$k] as $key => $weight) {
                    $shippingWeightAmount = new OmsShippingMethodWeightAmount();
                    $shippingWeightAmount->shipping_method_id = $shippingMethod->id;
                    $shippingWeightAmount->weight = $weight;
                    $shippingWeightAmount->amount_weight = $weigghtAmounts[$k][$key];
                    $shippingWeightAmount->save();
                }
            }
        }
        
        return response()->json([
            'status' => true,
            'mesge' => 'Shipping method added successfully.'
        ]);
    }

    public function editShippingMethod($shippingMethod) {
        $shippingMethod = ShippingMethodModel::with('shippingWeightAmounts')->find($shippingMethod);
        $geoZones = GeoZoneModel::all();
        return view(self::VIEW_DIR. '.editShippingMethod')->with(compact('shippingMethod', 'geoZones'));
    }

    public function updateShippingMethods(Request $request) {
        dd($request->all());
    }

    public function getCountries() {
        $countries = Country::where('status',1)->get();
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

    public function destroyWeightAmount($id) {
        $weight = OmsShippingMethodWeightAmount::find($id);
        if($weight->delete()) {
            return response()->json([
                'status' => true
            ]);
        }
    }
}
