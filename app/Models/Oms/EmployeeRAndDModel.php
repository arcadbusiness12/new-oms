<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class EmployeeRAndDModel extends Model
{
    public $timestamps = false;
    protected $table = 'r_and_d';


    public function customDutiy() {
        return $this->hasOne(EmployeeCustomeDutiesModel::class, 'smart_look_id');
    }

    public function assignedUser() {
        return $this->belongsTo(OmsUserModel::class, 'assigned_to');
    }

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
}
