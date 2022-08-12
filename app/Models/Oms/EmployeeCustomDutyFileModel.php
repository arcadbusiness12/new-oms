<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeCustomDutyFileModel extends Model
{
    public $timestamps = false;

    protected $table = 'custom_duty_files';

    public function comments() {
        return $this->hasMany(EmployeeCustomDutyCommentModel::class, 'file_id')->orderBy('id', 'DESC');
    }

    public function files() {
        return $this->hasMany(EmployeeCustomDutyFileModel::class, 'custom_duty_id')->orderBy('id', 'DESC');
    }
}
