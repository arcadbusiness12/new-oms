<?php

namespace App\Models\Oms\Localisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoZoneToZoneModel extends Model
{
    use HasFactory;
    protected $table = 'geo_zone_to_zones';
    protected $fillable = ['geo_zone_id', 'country_id', 'city_id'];

    public function geoZone() {
       return $this->belongsTo(GeoZoneModel::class, 'geo_zone_id');
    }

    public function zoneAreas() {
        return $this->hasMany(zoneToAreaModel::class, 'zone_id');
    }
}
