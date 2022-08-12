<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;

class OmsLedger extends Model
{
    // protected $table = 'oms_activity_log';
    // protected $primaryKey = "id";
    // const UPDATED_AT = null;

    public function ledgerDetails()
    {
        return $this->hasMany('App\Models\Oms\OmsLedgerDetail','ledger_id','id');
    }
    public function shippingProvider()
    {
        return $this->hasOne('App\Models\Oms\ShippingProvidersModel','shipping_provider_id','account_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\Oms\OmsUserModel','user_id','created_by');
    }
    public function transactionType()
    {
        return $this->hasOne('App\Models\Oms\OmsTransactionType','id','transaction_type_id');
    }
    // public function user()
    // {
    //     return $this->hasOne('App\Models\Oms\ShippingProvidersModel','user_id','created_by');
    // }
    // public static function newLog($ref_id,$ativity_id){
    //     $inertion = new self;
    //     $inertion->activity_id = $ativity_id;
    //     $inertion->ref_id = $ref_id;
    //     $inertion->created_by = session('user_id');
    //     if($inertion->save()) return true; else return false;
    // }
    
}