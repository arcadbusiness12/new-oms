<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsProductSizeChartValueModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_product_size_chart_values';
    protected $fillable = ['option_id', 'group_name', 'size_chart_option_id', 'group_id', 'value', 'cm_inch'];
}
