<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeCustomDutyCommentModel extends Model
{
    public $timestamps = false;
    protected $table = 'custom_duty_comments';
    protected $fillable = ['employee_custom_duty_id', 'user_id', 'comment', 'created_at'];

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    public function file() {
        return $this->belongsTo(EmployeeCustomDutyFileModel::class, 'file_id')->where('is_attachment', 1);
    }

    public function custom_duty() {
        return $this->belongsTo(EmployeeCustomeDutiesModel::class, 'employee_custom_duty_id');
    }

    public function replies() {
        return $this->hasMany(CustomDutyCommentReplyModel::class, 'comment_id')->whereNull('parent_id')->orderBy('id', 'DESC');
    }
}
