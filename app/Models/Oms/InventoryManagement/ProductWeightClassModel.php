<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWeightClassModel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'weight_classes';
}
