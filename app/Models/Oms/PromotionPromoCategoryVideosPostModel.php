<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class PromotionPromoCategoryVideosPostModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_promotion_promo_category_videos_posts';

    public function product_post() {
        return $this->belongsTo(PromotionProductPostModel::class, 'product_post_id');
    }
}
