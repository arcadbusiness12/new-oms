<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class SmartLookModel extends Model
{
    public $timestamps = false;
    protected $table = 'smart_looks';
    protected $primaryKey = "id";
    protected $fillable = ['user_id','title','description','link','create_at','assigned_to'];
    protected $guarded = ['id'];

    public function customDutiy() {
        return $this->hasOne(EmployeeCustomeDutiesModel::class, 'smart_look_id');
    }

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'assigned_to');
    }

    public function create_user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
}
