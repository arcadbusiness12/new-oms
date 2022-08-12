<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeePerformanceModel extends Model
{
    public $timestamps = false;
    protected $table = 'employee_performances';
    protected $primaryKey = "id";
    protected $fillable = ['duty_list_id','user_id','achieved','target','confirm', 'created_at', 'achieved_point', 'updated_by'];
    // protected $guarded = ['id'];

    public function performanceDetails() {
        return $this->hasMany(EmployeePerformanceDetailModel::class, 'employee_performance_id');
    }

    public function activity() {
        return $this->belongsTo(DutyListsModel::class, 'duty_list_id');
    }
    
    public function sale_person()
    {
        return $this->hasOne(__NAMESPACE__ . '\OmsUserModel',"user_id", "user_id");
    }
}
