<?php

namespace App\Models\Oms;

use App\Models\Oms\Localisation\GeoZoneModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodModel extends Model
{
    use HasFactory;
    protected $table = 'shipping_methods';
    protected $fillable = ['store_id','geo_zone_id','country_id', 'name', 'amount', 'shipping_type', 'additional_amount'];
    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function geoZone() {
        return $this->belongsTo(GeoZoneModel::class, 'geo_zone_id');
    }

    public function store() {
        return $this->belongsTo(storeModel::class, 'store_id');
    }

    public function shippingWeightAmounts() {
        return $this->hasMany(ShippingMethodWeightAmount::class, 'shipping_method_id');
    }
}
