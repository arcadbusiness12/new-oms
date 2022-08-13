<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

class OptionValueModel extends AbstractOpenCartModel
{

  protected $table = 'option_value';
  protected $primaryKey = 'option_value_id';

  const FIELD_OPTION_VALUE_ID = 'option_value_id';
  const FIELD_OPTION_ID = 'option_id';
  const FIELD_IMAGE = 'image';
  const FIELD_SORT_ORDER = 'sort_order';

  public function option_value_description()
  {
    return $this->hasMany(__NAMESPACE__ . "\OptionValueDescriptionModel", OptionDescriptionModel::FIELD_OPTION_VALUE_ID, self::FIELD_OPTION_VALUE_ID);
  }

}
