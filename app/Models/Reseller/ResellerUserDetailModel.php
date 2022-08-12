<?php

namespace App\Models\Reseller;

use Illuminate\Database\Eloquent\Model;

class ResellerUserDetailModel extends Model
{
    public $timestamps = false;
    protected $table = 'user_details';
    protected $fillable = ['user_id', 'alternate_number', 'emirates_id_no', 'address', 'working_from', 'working_to', 'brand_logo', 'brand_link'];
    
    public function user() {
        return $this->belongsTo(OmsUserModel::class, 'user_id');
    }
}
