<?php

namespace App\Models\Oms;

use App\Models\Oms\InventoryManagement\Attribute\AttributeModel;
use App\Models\Oms\InventoryManagement\Attribute\AttributePresetModel;
use Illuminate\Database\Eloquent\Model;

class GroupCategoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'group_main_categories';
    protected $fillable = ['group', 'name', 'code', 'sub_category'];

    public function subCategories() {
        return $this->hasMany(GroupSubCategoryModel::class, 'group_main_category_id');
    }

    public function groups() {
        return $this->hasMany(ProductGroupModel::class, 'category_id');
    }

    public function attributes() {
        return $this->hasMany(AttributeModel::class, 'attribute_categories', 'category_id','attribute_id');
    }

    public function presets() {
        return $this->belongsToMany(AttributePresetModel::class, 'attribute_preset_categories', 'category_id' ,'attribute_preset_id');
    }
}
