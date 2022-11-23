<?php

namespace App\Models\Oms\Localisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoZoneModel extends Model
{
    use HasFactory;
    protected $table = 'geo_zones';
    
    public function zones() {
        return $this->hasMany(GeoZoneToZoneModel::class, 'geo_zone_id');
    }
}
