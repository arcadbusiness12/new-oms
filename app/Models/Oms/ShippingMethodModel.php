<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodModel extends Model
{
    use HasFactory;
    protected $table = 'shipping_methods';
    protected $fillable = ['store_id','country_id', 'name', 'amount'];
    public function country() {
        return $this->belongsTo(CountryModel::class, 'country_id');
    }

    public function store() {
        return $this->belongsTo(storeModel::class, 'store_id');
    }
}
