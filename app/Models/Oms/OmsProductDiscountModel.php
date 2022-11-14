<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsProductDiscountModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'oms_product_discounts';
}
