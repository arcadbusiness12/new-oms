<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsSubDutyListModel extends Model
{
    public $timestamps = false;
    protected $table = 'sub_duty_lists';
    protected $primaryKey = 'id';
    protected $fillable = ['name','duty_list_id','parent_id'];
    public function duty_list() {
        return $this->belongsTo(DutyListsModel::class, 'duty_list_id');
    }

    public function customDuties() {
        return $this->hasMany(EmployeeCustomeDutiesModel::class, 'sub_duty_list_id');
    }
}
