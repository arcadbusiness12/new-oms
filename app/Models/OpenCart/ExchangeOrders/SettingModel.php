<?php
/*
 * Exchange Order table model
 */
namespace App\Models\OpenCart\ExchangeOrders;
use App\Models\OpenCart\AbstractOpenCartModel;
/**
 * Description of ExchangeOrdersModel
 *
 * @author kamran
 */
class SettingModel extends AbstractOpenCartModel{
    protected $table = 'setting';
    protected $primaryKey = 'setting_id';

    const FIELD_STORE_ID = 'store_id';
    const FIELD_CODE = 'code';
    const FIELD_KEY = 'key';
    const FIELD_VALUE = 'value';

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