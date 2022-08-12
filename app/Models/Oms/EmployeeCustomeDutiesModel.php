<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeCustomeDutiesModel extends Model
{
    public $timestamps = false;
    protected $table = 'employee_custom_duties';
    protected $fillable = ['user_id','user_group_id', 'duty_list_id', 'title', 'description', 'start_date', 'end_date', 'progress','is_close','is_auto','is_view','is_regular'];

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    public function movedBy() {
        return $this->belongsTo(OmsUserModel::class, 'changed_by');
    }

    public function duty_list() {
        return $this->belongsTo(DutyListsModel::class, 'duty_list_id');
    }

     public function sub_duty_list() {
        return $this->belongsTo(OmsSubDutyListModel::class, 'sub_duty_list_id');
    }

    public function attachmentFiles() {
        return $this->hasMany(EmployeeCustomDutyFileModel::class, 'custom_duty_id')->where('is_attachment', 0)->orderBy('id','DESC');
    }

    public function files() {
        return $this->hasMany(EmployeeCustomDutyFileModel::class, 'custom_duty_id')->orderBy('id','DESC');
    }

    public function comments() {
        return $this->hasMany(EmployeeCustomDutyCommentModel::class, 'employee_custom_duty_id')->orderBy('id','DESC');
    }

    public function customActivity() {
        return $this->belongsTo(DutyListsModel::class, 'duty_list_id');
    }

    public function statusHistories() {
        return $this->hasMany(EmployeeDutyStatusHistoryModel::class, 'duty_id')->orderBy('id','DESC');
    }
}
