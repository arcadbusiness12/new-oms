<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;

class OmsLedgerDetail extends Model
{
    // protected $table = 'oms_activity_log';
    // protected $primaryKey = "id";
    // const UPDATED_AT = null;

    // public function activity()
    // {
    //     return $this->hasOne('App\Models\Oms\OmsActivityModel','id','activity_id');
    // }
    // public function user()
    // {
    //     return $this->hasOne('App\Models\Oms\OmsUserModel','user_id','created_by');
    // }
    // public static function newLog($ref_id,$ativity_id){
    //     $inertion = new self;
    //     $inertion->activity_id = $ativity_id;
    //     $inertion->ref_id = $ref_id;
    //     $inertion->created_by = session('user_id');
    //     if($inertion->save()) return true; else return false;
    // }
    
}