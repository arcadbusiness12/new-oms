<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class GroupCategoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'group_main_categories';
    protected $fillable = ['group', 'name', 'sub_category'];

    public function subCategories() {
        return $this->hasMany(GroupSubCategoryModel::class, 'group_main_category_id');
    }

    public function groups() {
        return $this->hasMany(ProductGroupModel::class, 'category_id');
    }
}
