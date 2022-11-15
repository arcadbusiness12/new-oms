<?php

namespace App\Models\Oms\InventoryManagement\Attribute;
use Illuminate\Database\Eloquent\Model;

class AttributePresetModel extends Model
{
    public $timestamps = false;
    protected $table = "attribute_presets";
    public function presetCategories() {
        return $this->hasMany(AttributePresetCategoryModel::class, 'attribute_preset_id');
    }
}
