<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // public $timestamps = false;
    // protected $table = 'oms_store';
    // protected $fillable = ['name','status'];

    public function addresses() {

        return $this->hasMany(CustomerAddress::class, 'customer_id');
    }
    public function defaultAddress() {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }
    protected function mobile(): Attribute
    {
        return Attribute::make(
        //     get: fn ($value) => ucfirst($value),
            set: fn ($value) => str_replace("+","",$value),
        );
    }
}
