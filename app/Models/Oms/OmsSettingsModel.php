<?php
namespace App\Models\Oms;
use Illuminate\Database\Eloquent\Model;

class OmsSettingsModel extends Model
{
    protected $table = 'oms_setting';
    protected $primaryKey = "setting_id";
    protected $fillable = ['code', 'key', 'value', 'serialize'];
    const FIELD_CODE = 'code';
    const FIELD_KEY = 'key';
    const FIELD_VALUE = 'value';
    const FIELD_SERIALIZE = 'serialize';
    
    public static function getByCode($code = ''){
        $query = self::select('key','value');
        $data = $query->where('code',$code)->get();
        if($data){
            $return = array();
            foreach ($data->toArray() as $value) {
                $return[$value['key']] = $value['value'];
            }
            return $return;
        }else{
            return false;
        }
    }
    public static function get($code = '',$key = ''){
        $query = self::select('key','value');
        $data = $query->where('code',$code)->where('key',$key)->first();
        if($data){
            if($data->serialize){
                return json_decode($data->value, true);
            }else{
                return $data->value;
            }
        }else{
            return false;
        }
    }
    public static function getByKey($key = ''){
        $query = self::select('value','serialize');
        $data = $query->where('key',$key)->first();
        if($data){
            if($data->serialize){
                return json_decode($data->value, true);
            }else{
                return $data->value;
            }
        }else{
            return false;
        }
    }
}