<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionProductPaidPostSocialModel extends Model
{
    use HasFactory;
    protected $table = 'oms_promotion_product_paid_post_socials';

    public function paidAdsPost() {
        return $this->belongsTo(PromotionProductPaidPostModel::class, 'paid_post_id');
    }
}
