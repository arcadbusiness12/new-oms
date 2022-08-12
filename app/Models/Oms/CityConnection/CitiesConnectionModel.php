<?php

namespace App\Models\Oms\CityConnection;

use Illuminate\Database\Eloquent\Model;

class CitiesConnectionModel extends Model
{
    public $timestamps = false;
    protected $table = 'city_connections';
    protected $primaryKey = "id";
    protected $fillable = ['name', 'tcs', 'movex'];
}
