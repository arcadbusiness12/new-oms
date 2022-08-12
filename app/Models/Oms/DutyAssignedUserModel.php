<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class DutyAssignedUserModel extends Model
{
    public $timestamps = false;
    protected $table = 'duty_assigned_users';
    protected $primaryKey = "id";
    protected $fillable = ['user_id', 'activity_id', 'point', 'quantity', 'duration', 'per_quantity_point', 'monthly_tasks','daily_compulsory'];

    public function customDuty() {
        return $this->belongsTo(DutyListsModel::class, 'activity_id');
    }

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    
}
