<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\PromotionProductPaidPostSocialModel;

class PromotionProductPaidPostModel extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'oms_promotion_product_paid_posts';
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

    public function postSocials() {
        return $this->hasMany(PromotionProductPaidPostSocialModel::class, 'paid_post_id');
    }
}
