<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodWeightAmount extends Model
{
    use HasFactory;
    protected $table = 'shipping_method_weight_amounts';
    protected $fillable = ['shipping_method_id','weight','amount_weight'];
}
