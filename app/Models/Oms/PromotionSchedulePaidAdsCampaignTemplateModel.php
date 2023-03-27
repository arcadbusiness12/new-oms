<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionSchedulePaidAdsCampaignTemplateModel extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'paid_ads_campaigns_templates';
    
    protected $fillable = ['campaign_id','main_setting_id','user_id','setting_name', 'range', 'budget', 'promotion_product_type_id', 'ad_set_name', 'creative_type_id', 'category_id', 'category', 'sub_category_id', 'sub_category', 'created_by','created_at', 'is_active','is_deleted','remark'];


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

    public function productPostes() {
        return $this->hasMany(PromotionProductPaidPostModel::class, 'setting_id');
     }
}
