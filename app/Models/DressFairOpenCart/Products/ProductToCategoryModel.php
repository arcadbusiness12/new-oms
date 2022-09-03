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
class ProductToCategoryModel extends AbstractDressFairOpenCartModel
{

  protected $table = 'product_to_category';
  protected $primaryKey = 'product_id';

  const FIELD_PRODUCT_ID = 'product_id';
  const FIELD_CATEGORY_ID = 'category_id';

}
