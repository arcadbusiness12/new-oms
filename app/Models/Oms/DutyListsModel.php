<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class DutyListsModel extends Model
{
  public $timestamps = false;
    protected $table = 'duty_lists';
    protected $primaryKey = "id";
    protected $fillable = ['name', 'duty_id', 'is_custom', 'is_auto', 'points'];
  public function duty()
  {
    return $this->belongsTo(DutyModel::class,"duty_id", "id");
  }

  public function employeePerformances()
  {
    return $this->hasMany(EmployeePerformanceModel::class,"duty_list_id");
  }

  public function employeeCustomDuties()
  {
    return $this->hasMany(EmployeeCustomeDutiesModel::class,"duty_list_id");
  }
  
  public function assignedUsersDuties()
  {
    return $this->hasMany(DutyAssignedUserModel::class,"activity_id");
  }

  public function sub_duty_lists() {
    return $this->hasMany(OmsSubDutyListModel::class, 'duty_list_id');
  }
}
