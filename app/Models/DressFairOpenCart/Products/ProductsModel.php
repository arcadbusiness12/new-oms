<?php

namespace App\Models\DressFairOpenCart\Products;

use App\Models\DressFairOpenCart\AbstractDressFairOpenCartModel;

/*
 * Opencart Product table Model
 */

/**
 * Description of ProductsModel
 *
 * @author kamran
 */
class ProductsModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product';
  protected $primaryKey = 'product_id';

  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_MODEL = 'model';
  const FIELD_SKU = 'sku';
  const FIELD_UPC = 'upc';
  const FIELD_JAN = 'jan';
  const FIELD_ISBN = 'isbn';
  const FIELD_MPN = 'mpn';
  const FIELD_LOCATION = 'location';
  const FIELD_QUANTITY = 'quantity';
  const FIELD_STOCK_STATUS_ID = 'stock_status_id';
  const FIELD_IMAGE = 'image';
  const FIELD_MANUFACTURER_ID = 'manufacturer_id';
  const FIELD_SHIPPING = 'shipping';
  const FIELD_PRICE = 'price';
  const FIELD_POS = 'pos';
  const FIELD_TAX_CLASS_ID = 'tax_class_id';
  const FIELD_DATE_AVAILABLE = 'date_available';
  const FIELD_WEIGHT = 'weight';
  const FIELD_WEIGHT_CLASS_ID = 'weight_class_id';
  const FIELD_LENGTH = 'length';
  const FIELD_WIDTH = 'width';
  const FIELD_HEIGHT = 'height';
  const FIELD_LENGTH_CLASS_ID = 'length_class_id';
  const FIELD_SUBTRACT = 'subtract';
  const FIELD_MINIMUM = 'minimum';
  const FIELD_SORT_ORDER = 'sort_order';
  const FIELD_STATUS = 'status';
  const FIELD_VIEWED = 'viewed';
  const FIELD_DATE_ADDED = 'date_added';
  const FIELD_DATE_MODIFIED = 'date_modified';

  public function product_description()
  {
    return $this->hasOne("App\\Models\\DressFairOpenCart\\Products\\ProductsDescriptionModel", self::FIELD_PRODUCT_ID, self::FIELD_PRODUCT_ID);
  }

  public function product_options()
  {
    return $this->hasMany(__NAMESPACE__ . "\ProductOptionModel", ProductOptionModel::FIELD_PRODUCT_ID, self::FIELD_PRODUCT_ID)
                    ->with(['options', 'options_values_all']);
  }
  public function product_special()
  {
    return $this->hasMany(__NAMESPACE__ . "\ProductSpecialModel", "product_id", self::FIELD_PRODUCT_ID);
  }
  public function promotion_product()
  {
    return $this->hasOne(__NAMESPACE__ . "\PromotionProductModel", "product_id", self::FIELD_PRODUCT_ID);
  }

}
