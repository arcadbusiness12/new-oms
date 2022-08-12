<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class CustomDutyCommentReplyModel extends Model
{
    public $timestamps = false;
    protected $table = 'duty_comment_replies';

    protected $fillable = ['comment_id','user_id','reply_comment','created_at'];

    public function files() {
        return $this->hasMany(EmployeeCustomDutyFileModel::class, 'comment_reply_id');
    }

    public function comment() {
        return $this->belongsTo(EmployeeCustomDutyCommentModel::class, 'comment_id');
    }

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    public function parent() {
        return $this->belongsTo(CustomDutyCommentReplyModel::class, 'parent_id');
    }

    public function childs() {
        return $this->hasMany(CustomDutyCommentReplyModel::class, 'parent_id')->orderBy('id', 'DESC');
    }

}
