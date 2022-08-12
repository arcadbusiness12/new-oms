<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    // protected $table = 'oms_activity_log';
    // protected $primaryKey = "id";
    // const UPDATED_AT = null;
    public function getCommissionConditionsAttribute($value)
    {
        return json_decode($value);
    }
    public function getCommissionConditionsAmountAttribute($value)
    {
        return json_decode($value);
    }
    
}