<?php

namespace App\Models\Oms\InventoryManagement;

use Illuminate\Database\Eloquent\Model;

class OmsDetails extends Model
{
	protected $table = 'oms_options_details';
	protected $fillable = ['options', 'value'];
	public function omsOptions()
	{
		return $this->belongsTo('App\Models\Oms\OmsOptions', 'options', 'id');
	}
	public static function colorId($color_title){
		$data = self::where('value',$color_title)->first();
		if( !empty($data) ){
			return $data->id;
		}else{
			return 0;
		}
	} 
}
