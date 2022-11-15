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
    public function attributeCategories() {
        return $this->belongsToMany(GroupCategoryModel::class, 'attribute_categories' ,'attribute_id', 'category_id');
    }

}
