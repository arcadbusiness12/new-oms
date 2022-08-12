<?php

namespace App\Models\Oms\InventoryManagement;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryOptionValueModel extends Model
{
    protected $table = 'oms_inventory_option_value';
    protected $primaryKey = "option_value_id";

    const FIELD_OPTION_VALUE_ID = 'option_value_id';
    const FIELD_BA_OPTION_ID = 'ba_option_id';
    const FIELD_BA_OPTION_VALUE_ID = 'ba_option_value_id';
    const FIELD_DF_OPTION_ID = 'df_option_id';
    const FIELD_DF_OPTION_VALUE_ID = 'df_option_value_id';

    public static function convertDFOptionValueID($option_id, $option_value_id){
       $id = self::select('ba_option_value_id')->where('df_option_id', $option_id)->where('df_option_value_id', $option_value_id)->first();

       if($id){
        return $id->ba_option_value_id;
    }else{
        return 0;
    }
}
public static function convertINVTODFOptionValueID($option_id, $option_value_id){
   $id = self::select('df_option_value_id')->where('ba_option_id', $option_id)->where('ba_option_value_id', $option_value_id)->first();

   if($id){
    return $id->df_option_value_id;
}else{
    return 0;
}
}
public static function getINVTODFOptionValueID($option_value_id){
    $id = self::select('df_option_value_id')->where('ba_option_value_id', $option_value_id)->first();

    if($id){
        return $id->df_option_value_id;
    }else{
        return 0;
    }
}
public static function OmsOptionsFromBa($option_id, $option_value_id){
    $data = self::select('oms_options_id','oms_option_details_id')->where('ba_option_id', $option_id)->where('ba_option_value_id', $option_value_id)->first();

    if(!empty($data)){
        return $data;
    }else{
        return 0;
    }
}
public static function BaOptionsFromOms($option_id, $option_value_id){
    $data = self::select('ba_option_id','ba_option_value_id')->where('oms_options_id', $option_id)->where('oms_option_details_id', $option_value_id)->first();

    if($data){
        return $data;
    }else{
        return 0;
    }
}
public static function OmsOptionsFromDf($option_id, $option_value_id){
    $data = self::select('oms_options_id','oms_option_details_id')->where('df_option_id', $option_id)->where('df_option_value_id', $option_value_id)->first();

    if($data){
        return $data;
    }else{
        return 0;
    }
}


}
