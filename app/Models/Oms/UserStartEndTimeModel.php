<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class UserStartEndTimeModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_user_start_end_times';

    protected $fillable = ['user_id','start_time','end_time','date'];

    public function user() 
    {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
}
