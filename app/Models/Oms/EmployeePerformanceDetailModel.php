<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeePerformanceDetailModel extends Model
{
    public $timestamps = false;
    protected $table = 'employee_performance_details';
    protected $primaryKey = "id";
    protected $fillable = ['employee_performance_id','product_group_id','product_group_name','type','confirm', 'created_at'];

    public function empPerformance() {
        return $this->belongsTo(EmployeePerformanceModel::class, 'employee_performance_id');
    }
}
