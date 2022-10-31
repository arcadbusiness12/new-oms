<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    // protected $table = 'oms_store';
    // protected $fillable = ['name','status'];

    public function cities() {
        return $this->hasMany(City::class, 'country_id');
    }
}
