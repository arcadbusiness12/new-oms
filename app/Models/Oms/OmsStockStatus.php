<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsStockStatus extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'oms_stock_status';
}
