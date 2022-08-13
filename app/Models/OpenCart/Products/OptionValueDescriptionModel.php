<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

class OptionValueDescriptionModel extends AbstractOpenCartModel
{

	protected $table = 'option_value_description';
	protected $primaryKey = 'option_value_id';
	protected $guarded = ['option_value_id', 'created_at', 'updated_at'];
	const FIELD_OPTION_VALUE_ID = 'option_value_id';
	const FIELD_LANGUAGE_ID = 'language_id';
	const FIELD_OPTION_ID = 'option_id';
	const FIELD_NAME = 'name';
	public function optionDescription(){
		return $this->belongsTo('App\Models\OpenCart\Products\OptionDescriptionModel','option_id','option_id');
	}

}
