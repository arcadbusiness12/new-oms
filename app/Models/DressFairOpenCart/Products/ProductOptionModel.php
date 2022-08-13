<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class ProductOptionModel extends AbstractDressFairOpenCartModel
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
    return $this->hasOne(__NAMESPACE__ . "\OptionModel", OptionModel::FIELD_OPTION_ID, self::FIELD_OPTION_ID)->with('description');
  }

  public function options_values_all()
  {
    return $this->hasMany(__NAMESPACE__ . "\ProductOptionValueModel", ProductOptionValueModel::FIELD_PRODUCT_OPTION_ID, self::FIELD_PRODUCT_OPTION_ID)
                    ->with('option_value');
  }

}
