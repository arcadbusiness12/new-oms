<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class OptionDescriptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'option_description';
  protected $primaryKey = 'option_value_id';

  const FIELD_OPTION_VALUE_ID = 'option_value_id';
  const FIELD_OPTION_ID = 'option_id';
  const FIELD_LANGUAGE_ID = 'language_id';
  const FIELD_NAME = 'name';

  public function optionValueDescriptions(){
		return $this->hasMany('App\Models\DressFairOpenCart\Products\OptionValueDescriptionModel','option_id','option_id');
	}

}
