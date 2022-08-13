<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class DressFairProductOptionModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product_option';
  protected $primaryKey = 'product_option_id';

  const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_OPTION_ID = 'option_id';
  const FIELD_VALUE = 'value';
  const FIELD_REQUIRED = 'required';

  public function options()
  {
    return $this->hasOne(__NAMESPACE__ . "\DressFairOptionModel", DressFairOptionModel::FIELD_OPTION_ID, self::FIELD_OPTION_ID)->with('description');
  }

  public function options_values_all()
  {
    return $this->hasMany(__NAMESPACE__ . "\DressFairProductOptionValueModel", DressFairProductOptionValueModel::FIELD_PRODUCT_OPTION_ID, self::FIELD_PRODUCT_OPTION_ID)
                    ->with('option_value');
  }

}
