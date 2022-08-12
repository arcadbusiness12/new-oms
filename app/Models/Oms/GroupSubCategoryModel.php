<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class GroupSubCategoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'group_sub_categories';
    protected $fillable = ['group_main_category_id', 'name'];

    public function mainCategory() {
        return $this->belongsTo(GroupCategoryModel::class, 'group_main_category_id');
    }

    public function settings() {
        return $this->hasMany(PromotionScheduleSettingModel::class, 'sub_category_id');
    }
}
