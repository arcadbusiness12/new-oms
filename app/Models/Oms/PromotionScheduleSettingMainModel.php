<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class PromotionScheduleSettingMainModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_promotion_schedule_setting_main';
    
    protected $fillable = ['posting_type', 'social_ids', 'title'];

    public function settingSchedules() {
        return $this->hasMany(PromotionScheduleSettingModel::class, 'main_setting_id');
    }
    public function productPostes() {
      return $this->hasMany(PromotionProductPostModel::class, 'main_setting_id');
   }
    
   public function budgetType() {
      return $this->belongsTo(BudgetType::class, 'budget_type_id');
   }

   public function adsType() {
      return $this->belongsTo(AdsTypeModel::class, 'ads_type_id');
   }

   public function user() {
      return $this->belongsTo(OmsUserModel::class, 'user_id');
   }

   public function activity() {
      return $this->belongsTo(DutyListsModel::class, 'duty_activity_id');
   }
   public function chatResults() {
    return $this->hasOne(DailyAdResult::class, 'promotion_schedule_setting_main_id')->where('duration', 'current');
  }
  public function schedulechatResults() {
   return $this->hasOne(DailyAdResult::class, 'promotion_schedule_setting_main_id')->where('duration', 'schedule');
 }
 public function paidAdsCampaigns() {
    return $this->hasMany(PaidAdsCampaign::class, 'main_setting_id');
 }
 public function postPages(){
  return $this->hasMany(PromotionScheduleSettingPostPageModel::class, 'main_setting_id');
 }
}
