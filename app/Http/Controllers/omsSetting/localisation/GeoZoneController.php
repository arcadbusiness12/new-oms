<?php

namespace App\Http\Controllers\omsSetting\localisation;

use App\Http\Controllers\Controller;
use App\Models\DressFairOpenCart\ExchangeOrders\CountryModel;
use App\Models\Oms\City;
use App\Models\Oms\CityArea;
use App\Models\Oms\Country;
use App\Models\Oms\Localisation\GeoZoneModel;
use App\Models\Oms\Localisation\GeoZoneToZoneModel;
use App\Models\Oms\Localisation\zoneToAreaModel;
use Illuminate\Http\Request;

class GeoZoneController extends Controller
{
    const VIEW_DIR = 'oms_setting.localisation';
    const PER_PAGE = 20;
    
    public function __construct()
    {
        
    }

    public function geoZones() {
        $geoZones = GeoZoneModel::all();
        return view(self::VIEW_DIR. '.geoZones')->with(compact('geoZones'));
    }

    public function addGeoZones() {
        $countries = Country::where('status', 1)->get();
        return view(self::VIEW_DIR. '.addGeoZone')->with(compact('countries'));
    }

    public function getZones($country) {
        // dd($country);
        $cities = City::where('country_id', $country)->where('status', 1)->get();
        return response()->json([
            'cities' => $cities
        ]);
    }  
    
    public function getAreas($city) {
        $areas = CityArea::where('city_id', $city)->get();
        return response()->json([
            'areas' => $areas
        ]);
    }

    public function saveGeoZone(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required | unique:geo_zones',
            'geo_zone_description' => 'required',
            // 'country.*' => 'required'
        ]);
        
        $countries = $request->country;
        $zones = $request->zone;
        $areas = $request->area;
        $geo_zone = new GeoZoneModel();
        $geo_zone->name = $request->name;
        $geo_zone->description = $request->geo_zone_description;
        if($geo_zone->save()) {
            if($countries) {
                foreach($countries as $k => $country) {
                    if(@$zones[$k] == 0) {
                        $exist = GeoZoneToZoneModel::where('geo_zone_id', $geo_zone->id)->where('country_id', $country)->where('city_id', 0)->first();
                        if($exist) {
                            continue;
                        }
                    }
                    $gztZoze = new GeoZoneToZoneModel();
                    $gztZoze->geo_zone_id = $geo_zone->id;
                    $gztZoze->country_id = $country;
                    $gztZoze->city_id = $zones[$k];
                    $gztZoze->save();
                    if(isset($areas[$k])) {
                        foreach($areas[$k] as $area) {
                            $zoneArea = new zoneToAreaModel();
                            $zoneArea->zone_id = $gztZoze->id;
                            $zoneArea->area_id = $area;
                            $zoneArea->save();
                        }
                    }
                }
            }
        }

        return redirect()->route('geo.zones')->with('success', 'Geo zone values added successfully.');
    
    }

    public function editGeoZone($id)  {
        $geoZone = GeoZoneModel::with(['zones', 'zones.zoneAreas'])->find($id);
        $countries = Country::where('status', 1)->get();
        $cities  = City::where('status', 1)->get();
        $CityArea = CityArea::all();
        // dd($geoZone);
        return view(self::VIEW_DIR. '.editGeoZone')->with(compact('geoZone','countries','cities','CityArea'));
    }

    public function updateGeoZone(Request $request) {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required | unique:geo_zones,name'.$request->geo_zone_id,
            'geo_zone_description' => 'required'
        ]);
        
        $countries = $request->country;
        $zones = $request->zone;
        $areas = $request->area;
        $zone_id = $request->zone_id;
        $geo_zone = GeoZoneModel::find($request->geo_zone_id);
        $geo_zone->name = $request->name;
        $geo_zone->description = $request->geo_zone_description;
        if($geo_zone->update()) {
            if($countries) {
                foreach($countries as $k => $country) {
                    if($zones[$k] == 0) {
                        $exist = GeoZoneToZoneModel::where('geo_zone_id', $geo_zone->id)->where('country_id', $country)->where('city_id', 0)->first();
                        if($exist) {
                            continue;
                        }
                    }
                    $gztZoze = GeoZoneToZoneModel::updateOrCreate(
                        ['id' => @$zone_id[$k]],
                        [
                            'geo_zone_id' => $geo_zone->id,
                            'country_id'  => $country,
                            'city_id'     => $zones[$k]
                        ]
                    );
                    if(@$zone_id[$k]) {
                        zoneToAreaModel::where('zone_id', $zone_id[$k])->delete();
                    }
                        
                        if(isset($areas[$k])) {
                            foreach($areas[$k] as $area) {
                                $zoneArea = new zoneToAreaModel();
                                $zoneArea->zone_id = $gztZoze->id;
                                $zoneArea->area_id = $area;
                                $zoneArea->save();
                            }
                        }
                }
            }
        }
        

        return redirect()->route('geo.zones')->with('success', 'Geo zone values updated successfully.');
    }
}
