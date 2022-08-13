<?php

namespace App\Models\OpenCart\Products;

use App\Models\OpenCart\AbstractOpenCartModel;

class OptionModel extends AbstractOpenCartModel
{

  protected $table = 'option';
  protected $primaryKey = 'option_id';

  const FIELD_OPTION_ID = 'option_id';
  const FIELD_TYPE = 'type';
  const FIELD_SORT_ORDER = 'sort_order';

  public function description()
  {
    return $this->hasOne(__NAMESPACE__ . "\OptionDescriptionModel", OptionDescriptionModel::FIELD_OPTION_ID, self::FIELD_OPTION_ID);
  }

}
