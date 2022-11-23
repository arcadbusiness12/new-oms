<?php

namespace App\Models\Oms\Localisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class zoneToAreaModel extends Model
{
    use HasFactory;
    protected $table = 'zone_areas';
    protected $fillable = ['zone_id','city_id'];
}
