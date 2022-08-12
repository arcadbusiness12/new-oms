<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class OmsNotificationModel extends Model
{
    public $timestamps = false;
    protected $table = 'oms_notifications';

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

}
