<?php

namespace App\Models\Oms\InventoryManagement;
use App\Models\Oms\InventoryManagement\OmsOptions;
use Illuminate\Database\Eloquent\Model;

class OmsInventoryOptionModel extends Model
{
	protected $table = 'oms_inventory_option';
	protected $primaryKey = "option_id";
	protected $guarded = ['option_id', 'created_at', 'updated_at'];

	const FIELD_OPTION_ID = 'option_id';
	const FIELD_BA_OPTION_ID = 'ba_option_id';
	const FIELD_DF_OPTION_ID = 'df_option_id';

	public static function convertDFOptionID($option_id){
		$id = self::select('ba_option_id')->where('df_option_id', $option_id)->first();

		if($id){
			return $id->ba_option_id;
		}else{
			return 0;
		}
	}
	public static function convertINVTODFoptionID($option_id){
		$id = self::select('df_option_id')->where('ba_option_id', $option_id)->first();

		if($id){
			return $id->df_option_id;
		}else{
			return 0;
		}
	}
	public static function baColorOptionId(){
		$data = self::select('ba_option_id')->where('oms_options_id',OmsOptions::colorOptionId())->first();
		if( !empty($data) ){
			return $data->ba_option_id;
		}else{
			return 0;
		}
	}
	public static function dfColorOptionId(){
		$data = self::select('df_option_id')->where('oms_options_id',OmsOptions::colorOptionId())->first();
		if( !empty($data) ){
			return $data->df_option_id;
		}else{
			return 0;
		}
	}   
}
