<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    // public $timestamps = false;
    // protected $table = 'oms_store';
    // protected $fillable = ['name','status'];

    public function addresses() {

        return $this->hasMany(CustomerAddress::class, 'customer_id');
      }
}
