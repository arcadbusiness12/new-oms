<?php

namespace App\Models\Oms\InventoryManagement\Attribute;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroupAttribute extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'oms_inventory_product_group_attributes';
    protected $fillable = ['group_id','attribute_id','attribute_preset_id','text','created_at','updated_at'];
}
