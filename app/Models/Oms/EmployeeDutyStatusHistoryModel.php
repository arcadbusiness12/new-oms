<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeDutyStatusHistoryModel extends Model
{
    public $timestamps = false;
    protected $table = 'duty_change_status_histories';
    protected $fillable = ['duty_id','user_id', 'created_at'];

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    public function duty() {
        return $this->belongsTo(EmployeeCustomeDutiesModel::class, 'duty_id');
    }
}
