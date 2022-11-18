<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public $timestamps = false;
    protected $table = 'customer_address';
    // protected $fillable = ['name','status'];
    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function city() {

        return $this->belongsTo(City::class, 'city_id');
    }
    public function area() {

        return $this->belongsTo(CityArea::class, 'area_id');
    }
}
