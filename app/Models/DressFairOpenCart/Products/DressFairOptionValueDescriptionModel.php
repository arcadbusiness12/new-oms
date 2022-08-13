<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class DressFairOptionValueDescriptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'option_value_description';
  protected $primaryKey = 'option_value_id';

  const FIELD_OPTION_VALUE_ID = 'option_value_id';
  const FIELD_LANGUAGE_ID = 'language_id';
  const FIELD_OPTION_ID = 'option_id';
  const FIELD_NAME = 'name';

}
