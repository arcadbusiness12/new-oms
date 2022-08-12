<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class PromotionScheduleSettingModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_promotion_schedule_setting';
    
    protected $fillable = ['main_setting_id','user_id', 'posting_type', 'schedule_time','setting_name', 'range', 'budget', 'promotion_product_type_id', 'ad_set_name', 'creative_type_id', 'category_id', 'category', 'sub_category_id', 'sub_category', 'created_by','created_at', 'is_active'];


    public function type() {
        return $this->belongsTo(PromotionTypeModel::class, 'promotion_product_type_id');
    }

    public function mainSetting() {
        return $this->belongsTo(PromotionScheduleSettingMainModel::class, 'main_setting_id');
    }
    
    public function subCategory() {
        return $this->belongsTo(GroupSubCategoryModel::class, 'sub_category_id');
    }

    public function creativeType() {
        return $this->belongsTo(AdsTypeModel::class, 'creative_type_id');
    }
    public function adResultHistories() {
        return $this->hasMany(DailyAdResult::class, 'setting_id');
    }
}
