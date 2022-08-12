<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class PromotionTypeModel extends Model
{
    protected $table = 'oms_promotion_product_types';

    public function scheduleSettings() {
        return $this->hasMany(PromotionScheduleSettingModel::class, 'promotion_product_type_id');
    }

    public function productGroups() {
      return $this->hasMany(ProductGroupModel::class, 'product_type_id');
    }
}
