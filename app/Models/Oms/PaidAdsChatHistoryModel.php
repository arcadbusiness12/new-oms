<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\ProductGroupModel;
use App\Models\Oms\storeModel;
use App\Models\Oms\SocialModel;
class PaidAdsChatHistoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'paid_ads_chat_histories';
    protected $fillable = ['product_post_id','setting_id','group_code','chat','created_at'];

    public function productPost() {
      return $this->belongsTo(PromotionProductPostModel::class, 'product_post_id');
   }
        
}
