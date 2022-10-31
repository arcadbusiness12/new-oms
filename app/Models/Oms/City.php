<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    // public $timestamps = false;
    // protected $table = 'oms_store';
    // protected $fillable = ['name','status'];

    public function areas() {

        return $this->hasMany(CityArea::class, 'city_id');
      }
}
