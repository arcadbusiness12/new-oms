<?php

namespace App\Models\Oms\InventoryManagement\Attribute;

use App\Models\Oms\GroupCategoryModel;
use Illuminate\Database\Eloquent\Model;

class AttributePresetModel extends Model
{
    public $timestamps = false;
    protected $table = "attribute_presets";

    public function categories() {
        return $this->belongsToMany(GroupCategoryModel::class, 'attribute_preset_categories' ,'attribute_preset_id', 'category_id');
    }
}
