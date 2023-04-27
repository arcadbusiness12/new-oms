<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class PaidAdsCampaign extends Model
{
    public $timestamps = false;
    protected $table = 'paid_ads_campaigns';
    
    public function mainSetting() {
        return $this->belongsTo(PromotionScheduleSettingMainModel::class, 'main_setting_id');
    }

    public function paidAds() {
        return $this->hasMany(PromotionProductPostModel::class, 'campaign_id');
    }

    public function chatResults() {
        return $this->hasMany(DailyAdResult::class, 'campaign_id')->where('duration', 'current');
      }
    public function schedulechatResults() {
       return $this->hasOne(DailyAdResult::class, 'campaign_id')->where('duration', 'schedule');
     }
     public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
    public function users() {
        return $this->belongsToMany(OmsUserModel::class, 'campaign_users', 'campaign_id', 'user_id');
    }  
}
