<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\storeModel;
use App\Models\Oms\SocialModel;
use FacebookAds\Object\Campaign;

class PromotionProductPostModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_promotion_product_posts';
    protected $fillable = ['product_id','store_id','main_setting_id','social_id','group_id','group_code','group_name','date','time','created_at','created_by','posting_type'];

   public function group() {
      return $this->belongsTo(ProductGroupModel::class, 'group_id');
   }

   public function store() {
      return $this->belongsTo(storeModel::class, 'store_id');
   }
   public function social_media() {
    return $this->belongsTo(SocialModel::class, 'social_id');
  }

  public function main_setting() {
   return $this->belongsTo(PromotionScheduleSettingMainModel::class, 'main_setting_id');
 }

 public function chatHistories() {
   return $this->hasMany(PaidAdsChatHistoryModel::class, 'product_post_id');
 }

 public function promo_cate_posts() {
   return $this->hasMany(PromotionPromoCategoryVideosPostModel::class, 'product_post_id');
 }

 public function campaign() {
   return $this->belongsTo(PaidAdsCampaign::class, 'campaign_id');
 }

 public function setting() {
  return $this->belongsTo(PromotionScheduleSettingModel::class, 'setting_id');
}

public function chats() {
  return $this->hasMany(DailyAdResult::class, 'post_id')->whereIn('duration', ['current','schedule']);
}
         
}
