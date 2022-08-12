<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class DailyAdResult extends Model
{
    protected $table = 'daily_ad_results';
    protected $fillable = ['user_id','promotion_schedule_setting_main_id','setting_id','campaign_id','budget_alloted','budget_used','results','cost_per_result_alloted','cost_per_result','duration','date'];
    // protected $guarded = ['id'];
    // public function paidAdsSettings() {
    //   return  $this->hasMany(PromotionScheduleSettingMainModel::class, 'ads_type_id');
    // }
    public function mainSetting() {
        return $this->belongsTo(PromotionScheduleSettingMainModel::class, 'promotion_schedule_setting_main_id');
    } 
    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    } 
}
