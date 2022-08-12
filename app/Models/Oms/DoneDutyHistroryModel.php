<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class DoneDutyHistroryModel extends Model
{
    public $timestamps = false;
    protected $table = 'done_duty_histories';
    protected $primaryKey = "id";
    protected $fillable = ['user_id', 'duty_id', 'duty_name', 'done_date', 'created_at'];

    public function duty() {
        return $this->belongsTo(DutyModel::class, 'duty_id');
    }
}
