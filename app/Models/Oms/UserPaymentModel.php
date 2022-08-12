<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;

class UserPaymentModel extends Model
{
    public $timestamps = false;
    
    protected $table = 'user_salary_payments';
    protected $fillable = ['user_id','amount','payment','month','payment_date','created_at'];

    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }

    public function userGroup() {
        return $this->belongsTo('App\Models\Oms\OmsUserGroupModel', 'user_group_id');
    }
}
