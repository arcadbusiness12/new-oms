<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveModel extends Model
{
    public $timestamps = false;
    protected $table = 'employee_leaves';

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
}
