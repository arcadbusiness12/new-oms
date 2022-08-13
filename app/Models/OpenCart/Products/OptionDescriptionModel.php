<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

class OptionDescriptionModel extends AbstractOpenCartModel
{

	protected $table = 'option_description';
	protected $primaryKey = 'option_value_id';

	const FIELD_OPTION_VALUE_ID = 'option_value_id';
	const FIELD_OPTION_ID = 'option_id';
	const FIELD_LANGUAGE_ID = 'language_id';
	const FIELD_NAME = 'name';
	public function optionValueDescriptions(){
		return $this->hasMany('App\Models\OpenCart\Products\OptionValueDescriptionModel','option_id','option_id');
	}

}
