<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
class OmsActivityLogModel extends Model
{
    protected $table = 'oms_activity_log';
    protected $primaryKey = "id";
    const UPDATED_AT = null;

    public function activity()
    {
        return $this->hasOne('App\Models\Oms\OmsActivityModel','id','activity_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\Oms\OmsUserModel','user_id','created_by');
    }
    public function courier()
    {
        return $this->hasOne('App\Models\Oms\ShippingProvidersModel','shipping_provider_id','created_by_courier');
    }
    public static function newLog($ref_id,$ativity_id,$store = 1){
        $inertion = new self;
        $inertion->activity_id = $ativity_id;
        $inertion->ref_id = $ref_id;
        $inertion->store = $store;
        $inertion->created_by = session('user_id');
        if($inertion->save()) return true; else return false;
    }
    protected function createdAt(): Attribute
    {
        return new Attribute(
            get: fn ($value) => date('Y-m-d G:i:s',strtotime($value)),
            set: fn ($value) => $value,
        );
    }

}
