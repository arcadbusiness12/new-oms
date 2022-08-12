<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class AdsTypeModel extends Model
{
    protected $table = 'ads_types';

    public function paidAdsSettings() {
      return  $this->hasMany(PromotionScheduleSettingMainModel::class, 'ads_type_id');
    }
}
