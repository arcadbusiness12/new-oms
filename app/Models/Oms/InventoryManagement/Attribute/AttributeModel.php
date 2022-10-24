<?php

namespace App\Models\Oms\InventoryManagement\Attribute;

use App\Models\Oms\GroupCategoryModel;
use App\Models\Oms\ProductGroupModel;
use Illuminate\Database\Eloquent\Model;

class AttributeModel extends Model
{
    public $timestamps = false;
    protected $table = "attributes";

    public function category() {
        return $this->belongsTo(GroupCategoryModel::class, 'category_id');
    }

    public function presets() {
        return $this->hasMany(AttributePresetModel::class, 'attribute_id');
    }

    public function productGroups() {
        return $this->belongsToMany(ProductGroupModel::class, 'oms_inventory_product_group_attributes', 'attribute_id', 'group_id');
    } 
}
