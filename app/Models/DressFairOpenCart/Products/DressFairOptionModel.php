<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class DressFairOptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'option';
  protected $primaryKey = 'option_id';

  const FIELD_OPTION_ID = 'option_id';
  const FIELD_TYPE = 'type';
  const FIELD_SORT_ORDER = 'sort_order';

  public function description()
  {
    return $this->hasOne(__NAMESPACE__ . "\DressFairOptionDescriptionModel", DressFairOptionDescriptionModel::FIELD_OPTION_ID, self::FIELD_OPTION_ID);
  }

}
