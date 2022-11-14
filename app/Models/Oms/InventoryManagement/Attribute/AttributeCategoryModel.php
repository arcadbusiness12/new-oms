<?php

namespace App\Models\Oms\InventoryManagement\Attribute;

use App\Models\Oms\GroupCategoryModel;
use Illuminate\Database\Eloquent\Model;

class AttributeCategoryModel extends Model
{
    public $timestamps = false;
    protected $table = "attribute_categories";
    public function category() {
        return $this->belongsTo(GroupCategoryModel::class, 'category_id');
    }
}
