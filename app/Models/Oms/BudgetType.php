<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class BudgetType extends Model
{
    protected $table = 'budget_types';

    public function paidAdsSettings() {
        $this->hasMany(PromotionScheduleSettingMainModel::class, 'budget_type_id');
    }
}
