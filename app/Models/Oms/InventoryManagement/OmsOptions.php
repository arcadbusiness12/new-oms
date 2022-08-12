<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Model;

class OmsOptions extends Model
{
	protected $table = 'oms_options';
	protected $fillable = ['option_name','color_name']; 
	public function omsOptionsDetails()
	{
		return $this->hasMany('App\Models\Oms\InventoryManagement\OmsDetails','options', 'id');
	} 
	public function omsInventoryOption()
	{
		return $this->hasOne('App\Models\Oms\InventoryManagement\omsInventoryOptionModel','oms_options_id', 'id');
	}
	public function omsInventoryOptionValue()
	{
		return $this->hasMany('App\Models\Oms\InventoryManagement\omsInventoryOptionValueModel','oms_options_id', 'id');
	}
	public function ProductsSizes()
	{
		return $this->hasMany('App\Models\Oms\InventoryManagement\OmsInventoryProductOptionModel', 'option_id');
	}
	public static function colorOptionId(){
		$data = self::whereRaw('LOWER(option_name) = "color"')->orWhereRaw('LOWER(option_name) = "colors"')->first();
		if( !empty($data) ){
			return $data->id;
		}else{
			echo "Color entry not set in oms_option table you should enter color or colors in oms_option table.";
		}
	}
	
}
