<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

class DressFairProductOptionValueModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product_option_value';
  protected $primaryKey = 'product_option_value_id';

  const FIELD_PRODUCT_OPTION_VALUE_ID = 'product_option_value_id';
  const FIELD_PRODUCT_OPTION_ID = 'product_option_id';
  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_OPTION_ID = 'option_id';
  const FIELD_OPTION_VALUE_ID = 'option_value_id';
  const FIELD_QUANTITY = 'quantity';
  const FIELD_SUBTRACT = 'subtract';
  const FIELD_PRICE = 'price';
  const FIELD_PRICE_PREFIX = 'price_prefix';
  const FIELD_POINTS = 'points';
  const FIELD_POINTS_PREFIX = 'points_prefix';
  const FIELD_WEIGHT = 'weight';
  const FIELD_WEIGHT_PREFIX = 'weight_prefix';

  public function option_value()
  {
    return $this->hasMany(__NAMESPACE__ . "\DressFairOptionValueModel", DressFairOptionValueModel::FIELD_OPTION_VALUE_ID, self::FIELD_OPTION_VALUE_ID)
                    ->with('option_value_description');
  }

}
