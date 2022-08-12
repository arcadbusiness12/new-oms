<?php

namespace App\Models\Oms;

use Illuminate\Database\Eloquent\Model;
use App\Models\Oms\OmsUserModel;
use App\Models\Oms\PhotographySettingsDetail;
use App\Models\Oms\PhotographySettingsSocialPosting;
use DB;
class PhotographySetting extends Model
{
    //public $timestamps = true;
    //protected $table = 'oms_product_photography';
    public function model(){
      return $this->hasOne(OmsUserModel::class,'user_id','model_id');
    }
    public function settingsDetail(){
      return $this->hasMany(PhotographySettingsDetail::class,'photography_settings_id');
    }
    public function SettingsSocialPosting(){
      return $this->hasMany(PhotographySettingsSocialPosting::class,'photography_settings_id');
    }
}
