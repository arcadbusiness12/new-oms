<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsPlaceExchangeModel extends Model
{
    protected $table = "oms_place_exchanges";
    public function exchangeProducts(){
        return $this->hasMany(OmsExchangeProductModel::class,'order_id','order_id');
    }
    public function omsExchange(){
        return $this->hasOne(OmsExchangeOrdersModel::class,"order_id","order_id");
    }
    public function omsStore(){
        return $this->BelongsTo(storeModel::class,'store');
    }
}
